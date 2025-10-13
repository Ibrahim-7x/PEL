<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHappyCallStatusRequest;
use App\Models\ServiceCenter;
use App\Models\ComplaintCategory;
use App\Models\CaseStatus;
use App\Models\EscalationReason;
use App\Models\InitialCustomerInformation;
use App\Models\Feedback;
use App\Models\HappyCallStatus;
use App\Models\DelayReason;
use App\Models\InitialCustomerInformationAuditLog;

class AgentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Main page - can show form + feedback list if ticket_no is passed
    public function index(Request $request)
    {
        $serviceCenters = ServiceCenter::orderBy('sc_name')->get();
        $complaintCategory = ComplaintCategory::orderBy('category_name')->get();
        //$caseStatus = CaseStatus::orderBy('status')->get();
        $reasonofEscalation = EscalationReason::orderBy('reason')->get();
        $delayReason = DelayReason::orderBy('reason')->get();

        $ici = null;
        $feedbacks = collect();

        // Honor ticket_no on any request. Page reload behavior is controlled by front-end history.
        if ($request->filled('ticket_no')) {
            $ici = InitialCustomerInformation::where('ticket_no', $request->ticket_no)->first();
            if ($ici) {
                $feedbacks = Feedback::where('ici_id', $ici->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return view('agent', compact(
            'serviceCenters',
            'complaintCategory',
            //'caseStatus',
            'reasonofEscalation',
            'ici',
            'feedbacks',
            'delayReason'
        ));
    }

    // Chat page for agents
    public function tIndex(Request $request)
    {
        $serviceCenters = ServiceCenter::orderBy('sc_name')->get();
        $complaintCategory = ComplaintCategory::orderBy('category_name')->get();
        $caseStatus = CaseStatus::orderBy('status')->get();
        $reasonofEscalation = EscalationReason::orderBy('reason')->get();
        $delayReason = DelayReason::orderBy('reason')->get();

        $ici = null;
        $feedbacks = collect();

        // Honor ticket_no on any request or from session after form submission
        $ticketNo = $request->ticket_no ?: session('ticket_no');
        if ($ticketNo) {
            $ici = InitialCustomerInformation::with('happyCallStatus')->where('ticket_no', $ticketNo)->first();
            if ($ici) {
                $feedbacks = Feedback::where('ici_id', $ici->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return view('t_agent', compact(
            'serviceCenters',
            'complaintCategory',
            'caseStatus',
            'reasonofEscalation',
            'ici',
            'feedbacks',
            'delayReason'
        ))->with([
            'success_message' => session('success'),
            'submitted_ticket_no' => session('ticket_no')
        ]);
    }

    // Save or update ICI record based on complaint number
    public function store(Request $request)
    {

        $complaintNumber = $request->complaint_number_hidden ?: $request->complaint_number;

        // Check if this is an update (complaint number exists) or create (new complaint)
        $existingTicket = InitialCustomerInformation::where('complaint_number', $complaintNumber)->first();

        $iciData = [
            'ticket_no' => $request->ticket_no,
            'complaint_number' => $complaintNumber,
            'service_center' => $request->service_center,
            'complaint_escalation_date' => $request->complaint_escalation_date,
            'case_status' => $request->case_status,
            'complaint_category' => $request->complaint_category,
            'agent_name' => $request->agent_name,
            'reason_of_escalation' => $request->reason_of_escalation,
            'escalation_level' => $request->escalation_level,
            'voice_of_customer' => $request->voice_of_customer,
            'u_id' => auth()->id(),
        ];

        if ($existingTicket) {
            // Update existing ticket
            $request->validate([
                'ticket_no' => 'required',
                'complaint_number_hidden' => 'required',
                'service_center' => 'required',
                'complaint_escalation_date' => 'required|date',
                'case_status' => 'required',
                'complaint_category' => 'required',
                'agent_name' => 'required',
                'reason_of_escalation' => 'required',
                'escalation_level' => 'required',
                'voice_of_customer' => 'required'
            ]);

            // Check if escalation level should be auto-updated
            $currentEscalation = $existingTicket->escalation_level;
            $requestedEscalation = $request->escalation_level;
            $nextEscalation = $this->getNextEscalationLevel($currentEscalation);

            // Auto-update escalation level if user selected current level (not manually changed)
            if ($requestedEscalation === $currentEscalation && $currentEscalation !== 'High') {
                $iciData['escalation_level'] = $nextEscalation;
                $escalationWasUpdated = true;
                $oldEscalationLevel = $currentEscalation;
            } else {
                $escalationWasUpdated = false;
                $oldEscalationLevel = $currentEscalation;
            }

            $existingTicket->update($iciData);

            // Get old values for logging (before update)
            $oldValues = $existingTicket->getOriginal();

            // Get new values for logging
            $newValues = $existingTicket->fresh()->only([
                'service_center', 'case_status', 'complaint_category',
                'agent_name', 'reason_of_escalation', 'escalation_level',
                'voice_of_customer', 'complaint_escalation_date'
            ]);

            // Determine changed fields
            $changedFields = [];
            foreach ($oldValues as $field => $oldValue) {
                if (isset($newValues[$field]) && $oldValue != $newValues[$field]) {
                    $changedFields[] = $field;
                }
            }

            // Create log notes based on what changed
            $logNotes = 'Ticket updated';
            if ($escalationWasUpdated) {
                $logNotes .= ' - escalation level auto-updated from ' . $oldEscalationLevel . ' to ' . $nextEscalation;
            }

            // Log the update activity
            $this->logActivity(
                $existingTicket->complaint_number,
                $existingTicket->ticket_no,
                'UPDATED',
                $existingTicket->escalation_level,
                $oldValues,
                $newValues,
                $changedFields,
                $logNotes
            );

            $successMessage = $escalationWasUpdated ?
                'Record updated successfully. Escalation level updated to ' . $nextEscalation . '.' :
                'Record updated successfully.';

            return redirect()->back()->with('success', $successMessage);
        } else {
            // Create new ticket
            $request->validate([
                'ticket_no' => 'required|unique:initial_customer_information,ticket_no',
                'complaint_number_hidden' => 'required|unique:initial_customer_information,complaint_number',
                'service_center' => 'required',
                'complaint_escalation_date' => 'required|date',
                'case_status' => 'required',
                'complaint_category' => 'required',
                'agent_name' => 'required',
                'reason_of_escalation' => 'required',
                'escalation_level' => 'required',
                'voice_of_customer' => 'required'
            ]);


            $ici = InitialCustomerInformation::create($iciData);


            // Log the creation activity
            $this->logActivity(
                $ici->complaint_number,
                $ici->ticket_no,
                'CREATED',
                $ici->escalation_level,
                null, // No old values for creation
                $ici->only([
                    'service_center', 'case_status', 'complaint_category',
                    'agent_name', 'reason_of_escalation', 'escalation_level',
                    'voice_of_customer', 'complaint_escalation_date'
                ]),
                null, // No changed fields for creation
                'New ticket created for complaint number'
            );

            return redirect()->back()->with('success', 'Record saved successfully.');
        }
    }

    // Show a specific ticket's details + feedback
    public function showTicket(string $ticket_no)
    {
        $ici = InitialCustomerInformation::with('happyCallStatus')->where('ticket_no', $ticket_no)->firstOrFail();

        $feedbacks = Feedback::where('ici_id', $ici->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('agent', [
            'ici'                 => $ici,
            'feedbacks'           => $feedbacks,
            'serviceCenters'      => ServiceCenter::orderBy('sc_name')->get(),
            'complaintCategory'   => ComplaintCategory::orderBy('category_name')->get(),
            'caseStatus'          => CaseStatus::orderBy('status')->get(),
            'reasonofEscalation'  => EscalationReason::orderBy('reason')->get(),
            'delayReason'         => DelayReason::orderBy('reason')->get(),
        ]);
    }

    // Show a specific ticket's details + feedback for tracking
    public function showTicketT(string $ticket_no)
    {
        $ici = InitialCustomerInformation::with('happyCallStatus')->where('ticket_no', $ticket_no)->firstOrFail();

        $feedbacks = Feedback::where('ici_id', $ici->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('t_agent', [
            'ici'                 => $ici,
            'feedbacks'           => $feedbacks,
            'serviceCenters'      => ServiceCenter::orderBy('sc_name')->get(),
            'complaintCategory'   => ComplaintCategory::orderBy('category_name')->get(),
            'caseStatus'          => CaseStatus::orderBy('status')->get(),
            'reasonofEscalation'  => EscalationReason::orderBy('reason')->get(),
            'delayReason'         => DelayReason::orderBy('reason')->get(),
        ]);
    }

    // Store feedback for a specific ticket
    public function storeFeedback(Request $request, string $ticket_no)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:2000',
            ]);

            $ici = InitialCustomerInformation::where('ticket_no', $ticket_no)->firstOrFail();

            $feedback = Feedback::create([
                'ici_id'  => $ici->id,
                'name'    => Auth::user()->name,
                'role'    => Auth::user()->role ?? 'Agent',
                'message' => $request->message,
            ]);

            // If AJAX, return JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'id'      => $feedback->id,
                    'message' => $feedback->message,
                    'role'    => $feedback->role,
                    'time'    => $feedback->created_at->format('d M Y, h:i A'),
                ]);
            }

            // Fallback for normal (non-AJAX) submits: reset to Agent home (search view)
            return redirect()
                ->route('agent.index')
                ->with('success', 'Feedback added!');
        } catch (\Throwable $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error'   => $e->getMessage(),
                ], 500);
            }
            throw $e;
        }
    }

    public function searchTicket(Request $request)
    {
        try {
            $ticketNo = $request->ticket_no;

            $record = \DB::table('initial_customer_information')
                        ->where('ticket_no', $ticketNo)
                        ->first();

            if (!$record) {
                return response()->json(['error' => 'Ticket not found'], 404);
            }

            // Calculate aging = today - escalation_date
            $today = now();
            $complaintDate = \Carbon\Carbon::parse($record->complaint_escalation_date);
            $aging = null;
            if (!empty($record->complaint_escalation_date)) {
                $aging = round(
                    \Carbon\Carbon::parse($record->complaint_escalation_date)->diffInDays(now())
                );
            }

            // Check if happy call exists
            $hasHappyCall = \DB::table('happy_call_status')
                ->where('ici_id', $record->id)
                ->exists();

            return response()->json([
                'service_center'        => $record->service_center,
                'complaint_escalation_date' => $record->complaint_escalation_date
                ? \Carbon\Carbon::parse($record->complaint_escalation_date)->format('Y-m-d')
                : '',
                'case_status'           => $record->case_status,
                'aging'                 => $aging,
                'complaint_category'    => $record->complaint_category,
                'agent_name'                  => $record->agent_name,
                'reason_of_escalation'  => $record->reason_of_escalation,
                'escalation_level'      => $record->escalation_level,
                'voice_of_customer'        => $record->voice_of_customer,
                'has_happy_call'        => $hasHappyCall,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchComsData(Request $request)
    {
        $complaintNo = $request->input('complaint_number');

        if (!$complaintNo) {
            return response()->json(['error' => 'Complaint number is required'], 400);
        }


        try {
            // Call external COMS API - try with query parameter as shown in Postman
            $response = Http::timeout(10)->post(
                'https://pelcareapi.pel.com.pk/GetComplaintDetailsEU?complaintno=' . urlencode($complaintNo)
            );

            if ($response->successful()) {
                try {
                    $data = $response->json();
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Invalid response from COMS API'], 502);
                }

                if ($data['Success'] === true && isset($data['ComplaintDetails'][0])) {
                    return response()->json($data['ComplaintDetails'][0]);
                } else {
                    return response()->json(['error' => 'Complaint not found or invalid response'], 404);
                }
            } else {
                // Try to parse the error response
                $errorData = null;
                try {
                    $errorData = $response->json();
                } catch (\Exception $e) {
                    // If we can't parse JSON, use the raw body
                }

                // Check if this is an invalid complaint number (ComplaintDetails: -1)
                if ($errorData && isset($errorData['ComplaintDetails']) && $errorData['ComplaintDetails'] === -1) {
                    return response()->json(['error' => 'Complaint number is invalid'], 404);
                }

                // Log the actual API response for debugging
                Log::error('COMS API error response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'complaint_no' => $complaintNo
                ]);

                return response()->json([
                    'error' => 'COMS API returned error: ' . $response->status(),
                    'details' => $response->body()
                ], 502);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to connect to COMS API. Please try again later.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a unique ticket number in the format YY-NNNN with yearly reset
     *
     * @return string
     */
    public function generateTicketNumber()
    {
        try {
            // Get current year in YY format
            $currentYear = date('y');

            // Find the highest ticket number for the current year
            $latestTicket = InitialCustomerInformation::where('ticket_no', 'like', $currentYear . '-%')
                ->orderBy('ticket_no', 'desc')
                ->first();

            if ($latestTicket) {
                // Extract the sequential number from the latest ticket
                $latestNumber = intval(substr($latestTicket->ticket_no, -4));
                $nextNumber = $latestNumber + 1;
            } else {
                // No tickets exist for this year, start from 0001
                $nextNumber = 1;
            }

            // Format: YY-NNNN (e.g., 25-0001)
            $ticketNumber = $currentYear . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Verify uniqueness (double-check in case of race conditions)
            while (InitialCustomerInformation::where('ticket_no', $ticketNumber)->exists()) {
                $nextNumber++;
                $ticketNumber = $currentYear . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            return $ticketNumber;

        } catch (\Exception $e) {
            Log::error('Error generating ticket number', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            // Fallback: generate a simple timestamp-based number
            return date('y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Check if complaint number exists and return ticket information
     * This method is READ-ONLY and does NOT update database
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkComplaintTicket(Request $request)
    {
        try {
            $complaintNumber = $request->input('complaint_number');

            if (!$complaintNumber) {
                return response()->json(['error' => 'Complaint number is required'], 400);
            }

            // Check if ticket exists for this complaint number
            $existingTicket = InitialCustomerInformation::where('complaint_number', $complaintNumber)->first();

            if ($existingTicket) {
                // READ-ONLY: Just return current data, don't update escalation level
                $currentEscalation = $existingTicket->escalation_level;
                $nextEscalation = $this->getNextEscalationLevel($currentEscalation);

                // Check if happy call exists for this ticket
                $hasHappyCall = \DB::table('happy_call_status')
                    ->where('ici_id', $existingTicket->id)
                    ->exists();

                return response()->json([
                    'success' => true,
                    'exists' => true,
                    'ticket_no' => $existingTicket->ticket_no,
                    'current_escalation' => $currentEscalation,
                    'next_escalation' => $nextEscalation,
                    'display_escalation' => $nextEscalation, // Show next level in form
                    'escalation_updated' => false, // No update happens here anymore
                    'ticket_data' => [
                        'service_center' => $existingTicket->service_center,
                        'case_status' => $existingTicket->case_status,
                        'complaint_category' => $existingTicket->complaint_category,
                        'agent_name' => $existingTicket->agent_name,
                        'reason_of_escalation' => $existingTicket->reason_of_escalation,
                        'escalation_level' => $currentEscalation, // Current database value (unchanged)
                        'voice_of_customer' => $existingTicket->voice_of_customer,
                        'complaint_escalation_date' => $existingTicket->complaint_escalation_date
                            ? \Carbon\Carbon::parse($existingTicket->complaint_escalation_date)->format('Y-m-d')
                            : '',
                        'has_happy_call' => $hasHappyCall,
                    ]
                ]);
            } else {
                // No ticket exists for this complaint number, generate a new ticket number
                $newTicketNumber = $this->generateTicketNumber();

                return response()->json([
                    'success' => true,
                    'exists' => false,
                    'ticket_no' => $newTicketNumber,
                    'message' => 'New ticket generated for this complaint number.',
                    'complaint_number' => $complaintNumber,
                    'is_new_ticket' => true,
                    'ticket_data' => [
                        'escalation_level' => 'Low' // Default escalation level for new tickets
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in checkComplaintTicket', [
                'error' => $e->getMessage(),
                'complaint_number' => $request->input('complaint_number'),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unable to check complaint ticket'
            ], 500);
        }
    }

    /**
     * Fetch ticket information without updating escalation levels (for Case Tracking)
     * This method is read-only and doesn't modify database
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchTicketInfo(Request $request)
    {
        try {
            $complaintNumber = $request->input('complaint_number');

            if (!$complaintNumber) {
                return response()->json(['error' => 'Complaint number is required'], 400);
            }

            // Check if ticket exists for this complaint number
            $existingTicket = InitialCustomerInformation::where('complaint_number', $complaintNumber)->first();

            if ($existingTicket) {
                // Check if happy call exists for this ticket
                $hasHappyCall = \DB::table('happy_call_status')
                    ->where('ici_id', $existingTicket->id)
                    ->exists();

                Log::info('Happy Call existence check in fetchTicketInfo', [
                    'ticket_no' => $existingTicket->ticket_no,
                    'ici_id' => $existingTicket->id,
                    'has_happy_call' => $hasHappyCall,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'success' => true,
                    'exists' => true,
                    'ticket_no' => $existingTicket->ticket_no,
                    'current_escalation' => $existingTicket->escalation_level,
                    'ticket_data' => [
                        'service_center' => $existingTicket->service_center,
                        'case_status' => $existingTicket->case_status,
                        'complaint_category' => $existingTicket->complaint_category,
                        'agent_name' => $existingTicket->agent_name,
                        'reason_of_escalation' => $existingTicket->reason_of_escalation,
                        'escalation_level' => $existingTicket->escalation_level, // Actual database value
                        'voice_of_customer' => $existingTicket->voice_of_customer,
                        'complaint_escalation_date' => $existingTicket->complaint_escalation_date
                            ? \Carbon\Carbon::parse($existingTicket->complaint_escalation_date)->format('Y-m-d')
                            : '',
                        'has_happy_call' => $hasHappyCall,
                    ]
                ]);
            } else {
                // No ticket exists for this complaint number
                return response()->json([
                    'success' => false,
                    'exists' => false,
                    'message' => 'No ticket found against the following complaint number.',
                    'complaint_number' => $complaintNumber
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in fetchTicketInfo', [
                'error' => $e->getMessage(),
                'complaint_number' => $request->input('complaint_number'),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unable to fetch ticket information'
            ], 500);
        }
    }

    /**
     * Get the next escalation level in sequence
     *
     * @param string $currentLevel
     * @return string
     */
    private function getNextEscalationLevel($currentLevel)
    {
        switch ($currentLevel) {
            case 'Low':
                return 'Medium';
            case 'Medium':
                return 'High';
            case 'High':
            default:
                return 'High'; // Stay at High for subsequent searches
        }
    }

    /**
     * Log activity for Initial Customer Information changes
     *
     * @param string $complaintNumber
     * @param string $ticketNo
     * @param string $action
     * @param string $escalationLevel
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param array|null $changedFields
     * @param string|null $notes
     * @return void
     */
    private function logActivity($complaintNumber, $ticketNo, $action, $escalationLevel, $oldValues = null, $newValues = null, $changedFields = null, $notes = null)
    {
        try {
            InitialCustomerInformationAuditLog::create([
                'complaint_number' => $complaintNumber,
                'ticket_no' => $ticketNo,
                'action' => $action,
                'escalation_level' => $escalationLevel,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'changed_fields' => $changedFields,
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->role,
                'user_id' => auth()->id(),
                'notes' => $notes,
                'action_timestamp' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to log activity', [
                'error' => $e->getMessage(),
                'complaint_number' => $complaintNumber,
                'ticket_no' => $ticketNo,
                'action' => $action,
                'user_id' => auth()->id(),
            ]);
        }
    }

    /**
     * API endpoint to generate a new ticket number
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTicketNumber(Request $request)
    {
        try {
            $ticketNumber = $this->generateTicketNumber();

            return response()->json([
                'success' => true,
                'ticket_number' => $ticketNumber
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getTicketNumber endpoint', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unable to generate ticket number'
            ], 500);
        }
    }

    public function getFeedbacks(Request $request, string $ticket_no)
    {
        $ici = InitialCustomerInformation::where('ticket_no', $ticket_no)->firstOrFail();

        $feedbacks = Feedback::where('ici_id', $ici->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($fb) {
                return [
                    'id'      => $fb->id,
                    'name'    => $fb->name,
                    'role'    => $fb->role,
                    'message' => $fb->message,
                    'time'    => $fb->created_at->format('d M Y, h:i A'),
                ];
            });

        return response()->json($feedbacks);
    }

    /**
     * Save Happy Call Status with improved validation and error handling
     * Also handles checking if Happy Call exists (when no form data provided)
     *
     * @param Request $request
     * @param string $ticket_no
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function saveHappyCallStatus(Request $request, string $ticket_no)
    {
        // Debug logging
        Log::info('🚀 Happy Call save attempt started', [
            'ticket_no' => $ticket_no,
            'request_method' => $request->method(),
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        try {
            // Find ticket with optimized query (select only needed columns)
            $ticket = InitialCustomerInformation::select('id', 'ticket_no')
                ->where('ticket_no', $ticket_no)
                ->first();

            // Check if this is a status check (no form data) vs actual form submission
            $hasFormData = $request->filled(['resolved_date', 'happy_call_date', 'customer_satisfied', 'delay_reason']);

            // If no form data, this is just a status check
            if (!$hasFormData) {
                return $this->handleHappyCallStatusCheck($ticket, $ticket_no);
            }

            // If we have form data, validate and create
            return $this->handleHappyCallCreation($request, $ticket, $ticket_no);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors specifically
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed: ' . implode(', ', $e->errors()['resolved_date'] ?? $e->errors()['happy_call_date'] ?? ['Invalid dates'])
                ], 422);
            }
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Unexpected error in saveHappyCallStatus', [
                'ticket_no' => $ticket_no,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            $error = 'An unexpected error occurred. Please contact support if the problem persists.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $error], 500);
            }

            return back()->withErrors(['error' => $error]);
        }
    }

    /**
     * Handle happy call status check (no form data)
     */
    private function handleHappyCallStatusCheck($ticket, $ticket_no)
    {
        // Check if ticket exists
        if (!$ticket) {
            Log::warning('Attempted to check Happy Call for non-existent ticket', [
                'ticket_no' => $ticket_no,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            return response()->json(['success' => false, 'error' => 'Ticket not found.'], 404);
        }

        // Check for existing Happy Call with optimized query
        $existingHappyCall = HappyCallStatus::select('id')
            ->where('ici_id', $ticket->id)
            ->exists();

        Log::info('Happy Call existence check in handleHappyCallStatusCheck', [
            'ticket_no' => $ticket_no,
            'ici_id' => $ticket->id,
            'existing_happy_call' => $existingHappyCall,
            'user_id' => auth()->id(),
        ]);

        if ($existingHappyCall) {
            Log::info('Happy Call already exists for this ticket', [
                'ticket_no' => $ticket_no,
                'user_id' => auth()->id(),
            ]);

            return response()->json(['success' => false, 'error' => 'Happy Call already exists for this ticket.'], 409);
        }

        // No Happy Call exists, ready to create
        return response()->json(['success' => true, 'message' => 'No Happy Call exists, ready to create']);
    }

    /**
     * Handle happy call creation (with form data)
     */
    private function handleHappyCallCreation($request, $ticket, $ticket_no)
    {
        // Validate the request data
        $request->validate([
            'resolved_date' => [
                'required',
                'date',
                'before_or_equal:happy_call_date',
            ],
            'happy_call_date' => [
                'required',
                'date',
                'after_or_equal:resolved_date',
            ],
            'customer_satisfied' => [
                'required',
                Rule::in(['Yes', 'No']),
            ],
            'delay_reason' => [
                'required',
                'string',
                'max:1000',
                'regex:/^[^<>{}]*$/', // Prevent XSS by blocking HTML tags
            ],
            'voice_of_customer' => [
                'nullable',
                'string',
                'max:2000',
                'regex:/^[^<>{}]*$/', // Prevent XSS by blocking HTML tags
            ],
        ]);

        // Custom validation for date logic
        $resolvedDate = $request->input('resolved_date');
        $happyCallDate = $request->input('happy_call_date');

        if ($resolvedDate && $happyCallDate) {
            try {
                $resolved = Carbon::parse($resolvedDate);
                $happyCall = Carbon::parse($happyCallDate);
                $today = Carbon::today();

                // Business rule: Resolution date cannot be in the future
                if ($resolved->isAfter($today)) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Resolution date cannot be in the future.'
                        ], 422);
                    }
                    return back()->withErrors(['resolved_date' => 'Resolution date cannot be in the future.']);
                }

                // Business rule: Happy call should not be more than 30 days after resolution
                if ($happyCall->diffInDays($resolved) > 30) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Happy call date should not be more than 30 days after resolution date.'
                        ], 422);
                    }
                    return back()->withErrors(['happy_call_date' => 'Happy call date should not be more than 30 days after resolution date.']);
                }

                // Business rule: Resolution should not be more than 90 days ago
                if ($resolved->diffInDays($today) > 90) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Resolution date should not be more than 90 days ago.'
                        ], 422);
                    }
                    return back()->withErrors(['resolved_date' => 'Resolution date should not be more than 90 days ago.']);
                }
            } catch (\Exception $e) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Invalid date format provided.'
                    ], 422);
                }
                return back()->withErrors(['resolved_date' => 'Invalid date format.']);
            }
        }

        // Use database transaction for data consistency (only for actual creation)
        $result = DB::transaction(function () use ($request, $ticket_no, $ticket) {

            Log::info('🔄 Starting Happy Call creation in database transaction', [
                'ticket_no' => $ticket_no,
                'ici_id' => $ticket->id,
                'user_id' => auth()->id(),
            ]);

            // Create Happy Call status with validated and sanitized data
            $happyCallStatus = HappyCallStatus::create([
                'ici_id'             => $ticket->id,
                'resolved_date'      => $request->input('resolved_date'),
                'happy_call_date'    => $request->input('happy_call_date'),
                'customer_satisfied' => $request->input('customer_satisfied'),
                'delay_reason'       => $request->input('delay_reason'),
                'voice_of_customer'  => $request->input('voice_of_customer'),
            ]);

            // Log successful creation
            Log::info('✅ Happy Call status created successfully', [
                'ticket_no' => $ticket_no,
                'happy_call_id' => $happyCallStatus->id,
                'ici_id' => $ticket->id,
                'user_id' => auth()->id(),
            ]);

            return $happyCallStatus;
        });

        // Handle the response based on request type
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Happy Call submitted successfully!',
                'happy_call_id' => $result->id
            ]);
        }

        return back()->with([
            'success' => 'Happy Call status saved successfully!',
            'ticket_no' => $ticket_no
        ]);
    }
}
