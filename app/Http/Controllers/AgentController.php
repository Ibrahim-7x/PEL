<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\ServiceCenter;
use App\Models\ComplaintCategory;
use App\Models\EscalationReason;
use App\Models\InitialCustomerInformation;
use App\Models\Feedback;
use App\Models\HappyCallStatus;
use App\Models\DelayReason;
use App\Models\InitialCustomerInformationLog;
use App\Models\Mention;
use App\Models\User;
use App\Models\Coms;

class AgentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    // Main page - can show form + feedback list if ticket_number is passed
    public function index(Request $request)
    {
        $data = $this->getCommonViewData($request->ticket_number);
        return view('agent', $data);
    }
    
    // Chat page for agents
    public function tIndex(Request $request)
    {
        $data = $this->getCommonViewData($request->ticket_number ?: session('ticket_number'));
        return view('t_agent', $data)->with([
            'success_message' => session('success'),
            'submitted_ticket_number' => session('ticket_number')
        ]);
    }
    
    private function getCommonViewData($ticketNo = null)
    {
        $data = [
            'serviceCenters' => ServiceCenter::orderBy('sc')->get(),
            'complaintCategory' => ComplaintCategory::orderBy('category_name')->get(),
            'reasonofEscalation' => EscalationReason::orderBy('reason')->get(),
            'delayReason' => DelayReason::orderBy('reason')->get(),
            'ici' => null,
            'feedbacks' => collect()
        ];

        if ($ticketNo) {
            $data['ici'] = InitialCustomerInformation::with('happyCallStatus')
                ->where('ticket_number', $ticketNo)
                ->first();
                
            if ($data['ici']) {
                $data['feedbacks'] = Feedback::where('ici_id', $data['ici']->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return $data;
    }
    
    public function store(Request $request)
    {
        $complaintNumber = $request->complaint_number_hidden ?: $request->complaint_number;

        // Fetch and store COMS data only when submitting the form
        $comsRecord = $this->fetchAndStoreComsData($complaintNumber);
        $existingTicket = InitialCustomerInformation::where('complaint_id', $comsRecord->id)->first();

        $iciData = [
            'user_id' => auth()->id(),
            'ticket_number' => $request->ticket_number,
            'complaint_id' => $comsRecord->id,
            'service_center' => $request->service_center,
            'complaint_escalation_date' => now(),
            'case_status' => $request->case_status,
            'complaint_category' => $request->complaint_category,
            'agent_name' => $request->agent_name,
            'reason_of_escalation' => $request->reason_of_escalation,
            'escalation_level' => $request->escalation_level,
            'voice_of_customer' => $request->voice_of_customer,
        ];

        if ($existingTicket) {
            $request->validate([
                'ticket_number' => 'required',
                'complaint_number_hidden' => 'required',
                'case_status' => 'required',
                'escalation_level' => 'required',
                'voice_of_customer' => 'required'
            ]);

            $updateData = [
                'case_status' => $request->case_status,
                'voice_of_customer' => $request->voice_of_customer,
                'complaint_escalation_date' => now(),
            ];

            $currentEscalation = $existingTicket->escalation_level;
            $nextEscalation = $this->getNextEscalationLevel($currentEscalation);

            if ($request->case_status === 'In Progress') {
                $userChangedEscalation = $request->filled('escalation_level') && $request->escalation_level !== $currentEscalation;
                if (!$userChangedEscalation) {
                    $updateData['escalation_level'] = $nextEscalation;
                    $escalationWasUpdated = true;
                } else {
                    $updateData['escalation_level'] = $request->escalation_level;
                    $escalationWasUpdated = true;
                }
            } else {
                if ($request->filled('escalation_level')) {
                    $updateData['escalation_level'] = $request->escalation_level;
                    $escalationWasUpdated = $request->escalation_level !== $currentEscalation;
                } else {
                    $escalationWasUpdated = false;
                }
            }

            $existingTicket->update($updateData);

            $oldValues = $existingTicket->getOriginal();
            $newValues = $existingTicket->fresh()->only(['case_status', 'escalation_level', 'voice_of_customer']);
            $changedFields = array_keys(array_diff_assoc($newValues, array_intersect_key($oldValues, $newValues)));

            $logNotes = 'Ticket updated';
            if ($escalationWasUpdated) {
                $logNotes .= ' - escalation level updated';
            }

            $this->logActivity(
                $existingTicket->coms->complaint_number ?? $complaintNumber,
                $existingTicket->ticket_number,
                'UPDATED',
                $existingTicket->escalation_level,
                $oldValues,
                $newValues,
                $changedFields,
                $logNotes
            );

            $successMessage = $escalationWasUpdated ? 'Record updated successfully. Escalation level updated.' : 'Record updated successfully.';
            return redirect()->back()->with('success', $successMessage);
        } else {
            $request->validate([
                'ticket_number' => 'required|unique:initial_customer_information,ticket_number',
                'complaint_number_hidden' => 'required',
                'service_center' => 'required',
                'case_status' => 'required',
                'complaint_category' => 'required',
                'agent_name' => 'required',
                'reason_of_escalation' => 'required',
                'escalation_level' => 'required',
                'voice_of_customer' => 'required'
            ]);

            $ici = InitialCustomerInformation::create($iciData);

            $this->logActivity(
                $ici->coms->complaint_number ?? $complaintNumber,
                $ici->ticket_number,
                'CREATED',
                $ici->escalation_level,
                null,
                $ici->only(['service_center', 'case_status', 'complaint_category', 'agent_name', 'reason_of_escalation', 'escalation_level', 'voice_of_customer', 'complaint_escalation_date']),
                null,
                'New ticket created for complaint number'
            );

            return redirect()->back()->with('success', 'Record saved successfully.');
        }
    }

    // Show a specific ticket's details + feedback
    private function showTicketCommon($ticket_number, $view)
    {
        $data = $this->getCommonViewData($ticket_number);
        return view($view, $data);
    }

    public function showTicket(string $ticket_number)
    {
        return $this->showTicketCommon($ticket_number, 'agent');
    }

    public function showTicketT(string $ticket_number)
    {
        return $this->showTicketCommon($ticket_number, 't_agent');
    }

    // Store feedback for a specific ticket
    public function storeFeedback(Request $request, string $ticket_number)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:2000',
            ]);

            $ici = InitialCustomerInformation::where('ticket_number', $ticket_number)->firstOrFail();

            $feedback = Feedback::create([
                'ici_id'  => $ici->id,
                'name'    => Auth::user()->name,
                'role'    => Auth::user()->role ?? 'Agent',
                'message' => $request->message,
            ]);

            // Process mentions in the feedback message
            $this->processMentions($feedback, $request->message);

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
            $ticketNo = $request->ticket_number;

            $record = InitialCustomerInformation::where('ticket_number', $ticketNo)->first();

            if (!$record) {
                return response()->json(['error' => 'Ticket not found'], 404);
            }

            // Calculate aging = today - escalation_date
            $aging = $record->complaint_escalation_date
                ? round(now()->diffInDays(\Carbon\Carbon::parse($record->complaint_escalation_date)))
                : null;

            // Check if happy call exists
            $hasHappyCall = HappyCallStatus::where('ici_id', $record->id)->exists();

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
            // First check if data already exists in database (for previously submitted complaints)
            $existingRecord = Coms::where('complaint_number', $complaintNo)->first();

            if ($existingRecord) {
                \Log::info('COMS data found in database', [
                    'complaint_number' => $complaintNo,
                    'record_id' => $existingRecord->id
                ]);

                // Return data in API format expected by frontend
                return response()->json([
                    'ComplaintNo' => $existingRecord->complaint_number,
                    'JobNo' => $existingRecord->job,
                    'COMSComplaintDate' => $existingRecord->coms_complaint_date,
                    'JobType' => $existingRecord->job_type,
                    'CustomerName' => $existingRecord->customer_name,
                    'ContactNo' => $existingRecord->contact_number,
                    'TCN_NAME' => $existingRecord->technician_name,
                    'DateofPurchase' => $existingRecord->date_of_purchase,
                    'Product' => $existingRecord->product,
                    'JobStatus' => $existingRecord->job_status,
                    'Problem' => $existingRecord->problem,
                    'WorkDone' => $existingRecord->work_done,
                ]);
            }

            // Data not in database, fetch from API and store
            $response = Http::timeout(10)->withoutVerifying()->post(
                'https://pelcareapi.pel.com.pk/GetComplaintDetailsEU?complaintno=' . urlencode($complaintNo)
            );

            if ($response->successful()) {
                $data = $response->json();

                if ($data['Success'] === true && isset($data['ComplaintDetails'][0])) {
                    $complaintData = $data['ComplaintDetails'][0];

                    // Store the API data in database
                    $comsRecord = Coms::create([
                        'complaint_number' => $complaintData['ComplaintNo'] ?? $complaintNo,
                        'job' => $complaintData['JobNo'] ?? '',
                        'coms_complaint_date' => isset($complaintData['COMSComplaintDate']) ? \Carbon\Carbon::parse($complaintData['COMSComplaintDate'])->format('Y-m-d') : (isset($complaintData['ComplaintDate']) ? \Carbon\Carbon::parse($complaintData['ComplaintDate'])->format('Y-m-d') : null),
                        'job_type' => $complaintData['JobType'] ?? '',
                        'customer_name' => $complaintData['CustomerName'] ?? '',
                        'contact_number' => $complaintData['ContactNo'] ?? '',
                        'technician_name' => $complaintData['TCN_NAME'] ?? $complaintData['TechnicianName'] ?? '',
                        'date_of_purchase' => isset($complaintData['DateofPurchase']) ? \Carbon\Carbon::parse($complaintData['DateofPurchase'])->format('Y-m-d') : (isset($complaintData['PurchaseDate']) ? \Carbon\Carbon::parse($complaintData['PurchaseDate'])->format('Y-m-d') : null),
                        'product' => $complaintData['Product'] ?? '',
                        'job_status' => $complaintData['JobStatus'] ?? '',
                        'problem' => $complaintData['Problem'] ?? '',
                        'work_done' => $complaintData['WorkDone'] ?? '',
                    ]);
    
                    \Log::info('COMS data stored in database on fetch', [
                        'complaint_number' => $complaintNo,
                        'record_id' => $comsRecord->id
                    ]);
    
                    // Return the API response data mapped to expected format
                    $mappedData = [
                        'ComplaintNo' => $complaintData['ComplaintNo'] ?? '',
                        'JobNo' => $complaintData['JobNo'] ?? '',
                        'COMSComplaintDate' => $complaintData['COMSComplaintDate'] ?? $complaintData['ComplaintDate'] ?? '',
                        'JobType' => $complaintData['JobType'] ?? '',
                        'CustomerName' => $complaintData['CustomerName'] ?? '',
                        'ContactNo' => $complaintData['ContactNo'] ?? '',
                        'TCN_NAME' => $complaintData['TCN_NAME'] ?? $complaintData['TechnicianName'] ?? '',
                        'DateofPurchase' => $complaintData['DateofPurchase'] ?? $complaintData['PurchaseDate'] ?? '',
                        'Product' => $complaintData['Product'] ?? '',
                        'JobStatus' => $complaintData['JobStatus'] ?? '',
                        'Problem' => $complaintData['Problem'] ?? '',
                        'WorkDone' => $complaintData['WorkDone'] ?? '',
                    ];
    
                    return response()->json($mappedData);
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
                    'complaint_no' => $complaintNo,
                    'request_url' => $response->effectiveUri(),
                    'request_method' => 'POST'
                ]);

                return response()->json([
                    'error' => 'COMS API returned error: ' . $response->status(),
                    'details' => $response->body()
                ], 502);
            }
        } catch (\Exception $e) {
            Log::error('COMS API connection error', [
                'error' => $e->getMessage(),
                'complaint_no' => $complaintNo,
                'trace' => $e->getTraceAsString()
            ]);

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
            $latestTicket = InitialCustomerInformation::where('ticket_number', 'like', $currentYear . '-%')
                ->orderBy('ticket_number', 'desc')
                ->first();

            if ($latestTicket) {
                // Extract the sequential number from the latest ticket
                $latestNumber = intval(substr($latestTicket->ticket_number, -4));
                $nextNumber = $latestNumber + 1;
            } else {
                // No tickets exist for this year, start from 0001
                $nextNumber = 1;
            }

            // Format: YY-NNNN (e.g., 25-0001)
            $ticketNumber = $currentYear . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Verify uniqueness (double-check in case of race conditions)
            while (InitialCustomerInformation::where('ticket_number', $ticketNumber)->exists()) {
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
            $comsRecord = Coms::where('complaint_number', $complaintNumber)->first();
            $existingTicket = $comsRecord ? InitialCustomerInformation::where('complaint_id', $comsRecord->id)->first() : null;

            if ($existingTicket) {
                // Update escalation level automatically on each check
                $currentEscalation = $existingTicket->escalation_level;
                $nextEscalation = $this->getNextEscalationLevel($currentEscalation);

                // Check if happy call exists for this ticket
                $hasHappyCall = HappyCallStatus::where('ici_id', $existingTicket->id)->exists();

                return response()->json([
                    'success' => true,
                    'exists' => true,
                    'ticket_number' => $existingTicket->ticket_number,
                    'current_escalation' => $currentEscalation,
                    'next_escalation' => $nextEscalation,
                    'display_escalation' => $nextEscalation, // Show next level in form
                    'escalation_updated' => true, // Escalation was updated
                    'ticket_data' => [
                        'service_center' => $existingTicket->service_center,
                        'case_status' => $existingTicket->case_status,
                        'complaint_category' => $existingTicket->complaint_category,
                        'agent_name' => $existingTicket->agent_name,
                        'reason_of_escalation' => $existingTicket->reason_of_escalation,
                        'escalation_level' => $nextEscalation, // Updated value
                        'voice_of_customer' => $existingTicket->voice_of_customer,
                        'complaint_escalation_date' => $existingTicket->complaint_escalation_date
                            ? Carbon::parse($existingTicket->complaint_escalation_date)->format('Y-m-d')
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
                    'ticket_number' => $newTicketNumber,
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
            $comsRecord = Coms::where('complaint_number', $complaintNumber)->first();
            $existingTicket = $comsRecord ? InitialCustomerInformation::where('complaint_id', $comsRecord->id)->first() : null;

            if ($existingTicket) {
                // Check if happy call exists for this ticket
                $hasHappyCall = HappyCallStatus::where('ici_id', $existingTicket->id)->exists();

                return response()->json([
                    'success' => true,
                    'exists' => true,
                    'ticket_number' => $existingTicket->ticket_number,
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
                            ? Carbon::parse($existingTicket->complaint_escalation_date)->format('Y-m-d')
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
     * Fetch COMS data from API and store in database
     *
     * @param string $complaintNumber
     * @return Coms
     */
    private function fetchAndStoreComsData($complaintNumber)
    {
        // First check if data already exists in database
        $existingRecord = Coms::where('complaint_number', $complaintNumber)->first();

        if ($existingRecord) {
            return $existingRecord;
        }

        // Data not in database, fetch from API and store
        $response = Http::timeout(10)->withoutVerifying()->post(
            'https://pelcareapi.pel.com.pk/GetComplaintDetailsEU?complaintno=' . urlencode($complaintNumber)
        );

        if ($response->successful()) {
            $data = $response->json();

            if ($data['Success'] === true && isset($data['ComplaintDetails'][0])) {
                $complaintData = $data['ComplaintDetails'][0];

                // Store the API data in database
                $comsRecord = Coms::create([
                    'complaint_number' => $complaintData['ComplaintNo'] ?? $complaintNumber,
                    'job' => $complaintData['JobNo'] ?? '',
                    'coms_complaint_date' => isset($complaintData['COMSComplaintDate']) ? \Carbon\Carbon::parse($complaintData['COMSComplaintDate'])->format('Y-m-d') : (isset($complaintData['ComplaintDate']) ? \Carbon\Carbon::parse($complaintData['ComplaintDate'])->format('Y-m-d') : null),
                    'job_type' => $complaintData['JobType'] ?? '',
                    'customer_name' => $complaintData['CustomerName'] ?? '',
                    'contact_number' => $complaintData['ContactNo'] ?? '',
                    'technician_name' => $complaintData['TCN_NAME'] ?? $complaintData['TechnicianName'] ?? '',
                    'date_of_purchase' => isset($complaintData['DateofPurchase']) ? \Carbon\Carbon::parse($complaintData['DateofPurchase'])->format('Y-m-d') : (isset($complaintData['PurchaseDate']) ? \Carbon\Carbon::parse($complaintData['PurchaseDate'])->format('Y-m-d') : null),
                    'product' => $complaintData['Product'] ?? '',
                    'job_status' => $complaintData['JobStatus'] ?? '',
                    'problem' => $complaintData['Problem'] ?? '',
                    'work_done' => $complaintData['WorkDone'] ?? '',
                ]);

                \Log::info('COMS data stored in database on form submit', [
                    'complaint_number' => $complaintNumber,
                    'record_id' => $comsRecord->id
                ]);

                return $comsRecord;
            }
        }

        // If API fails, create empty record to prevent errors
        return Coms::firstOrCreate(
            ['complaint_number' => $complaintNumber],
            [
                'job' => '',
                'coms_complaint_date' => null,
                'job_type' => '',
                'customer_name' => '',
                'contact_number' => '',
                'technician_name' => '',
                'date_of_purchase' => null,
                'product' => '',
                'job_status' => '',
                'problem' => '',
                'work_done' => '',
            ]
        );
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
     * @param string $caseStatus
     * @param string $voiceOfCustomer
     * @return void
     */
    private function logActivity($complaintNumber, $ticketNo, $action, $escalationLevel, $caseStatus, $voiceOfCuttomer)
    {
        try {
            InitialCustomerInformationLog::create([
                'complaint_number' => $complaintNumber,
                'ticket_number' => $ticketNo,
                'action' => $action,
                'escalation_level' => $escalationLevel,
                'case_status' => $caseStatus,
                'voice_of_customer' => $voiceOfCustomer,
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log activity', [
                'error' => $e->getMessage(),
                'complaint_number' => $complaintNumber,
                'ticket_number' => $ticketNo,
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

    /**
     * Get unread mentions for the current user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMentions(Request $request)
    {
        try {
            // Get mentions that are either unread OR were created within the last 24 hours
            $mentions = Mention::with(['feedback.ici', 'mentionerUser'])
                ->where('mentioned_user_id', Auth::id())
                ->where(function ($query) {
                    $query->where('is_read', false)
                          ->orWhere('created_at', '>=', now()->subDay());
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($mention) {
                    return [
                        'id' => $mention->id,
                        'feedback_id' => $mention->feedback_id,
                        'ticket_number' => $mention->feedback->ici->ticket_number,
                        'mentioner_name' => $mention->mentionerUser->name,
                        'message' => Str::limit($mention->feedback->message, 100),
                        'created_at' => $mention->created_at,
                        'is_read' => $mention->is_read,
                    ];
                });

            return response()->json([
                'success' => true,
                'mentions' => $mentions,
                'count' => $mentions->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching mentions', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unable to fetch mentions'
            ], 500);
        }
    }

    /**
     * Mark mention as read
     *
     * @param Request $request
     * @param int $mentionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markMentionAsRead(Request $request, $mentionId)
    {
        try {
            $mention = Mention::where('id', $mentionId)
                ->where('mentioned_user_id', Auth::id())
                ->firstOrFail();

            $mention->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Mention marked as read'
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking mention as read', [
                'error' => $e->getMessage(),
                'mention_id' => $mentionId,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unable to mark mention as read'
            ], 500);
        }
    }

    /**
     * Search usernames for autocomplete
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchUsernames(Request $request)
    {
        try {
            $query = $request->input('query', '');
            $limit = $request->input('limit', 10);

            if (strlen($query) < 1) {
                return response()->json([
                    'success' => true,
                    'usernames' => []
                ]);
            }

            $usernames = User::where('username', 'like', $query . '%')
                ->where('id', '!=', Auth::id()) // Exclude current user
                ->select('username', 'name')
                ->limit($limit)
                ->get()
                ->map(function ($user) {
                    return [
                        'username' => $user->username,
                        'name' => $user->name,
                        'display' => $user->username . ' (' . $user->name . ')'
                    ];
                });

            return response()->json([
                'success' => true,
                'usernames' => $usernames
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching usernames', [
                'error' => $e->getMessage(),
                'query' => $request->input('query'),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unable to search usernames'
            ], 500);
        }
    }

    public function getFeedbacks(Request $request, string $ticket_number)
    {
        $ici = InitialCustomerInformation::where('ticket_number', $ticket_number)->firstOrFail();

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
     * @param string $ticket_number
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function saveHappyCallStatus(Request $request, string $ticket_number)
    {
        try {
            // Find ticket
            $ticket = InitialCustomerInformation::where('ticket_number', $ticket_number)->first();

            // Check if this is a status check (no form data) vs actual form submission
            $hasFormData = $request->filled(['resolved_date', 'happy_call_date', 'customer_satisfied', 'delay_reason']);

            // If no form data, this is just a status check
            if (!$hasFormData) {
                return $this->handleHappyCallStatusCheck($ticket, $ticket_number);
            }

            // If we have form data, validate and create
            return $this->handleHappyCallCreation($request, $ticket, $ticket_number);

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
                'ticket_number' => $ticket_number,
                'error' => $e->getMessage(),
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
    private function handleHappyCallStatusCheck($ticket, $ticket_number)
    {
        // Check if ticket exists
        if (!$ticket) {
            return response()->json(['success' => false, 'error' => 'Ticket not found.'], 404);
        }

        // Check for existing Happy Call
        $existingHappyCall = HappyCallStatus::where('ici_id', $ticket->id)->exists();

        if ($existingHappyCall) {
            return response()->json(['success' => false, 'error' => 'Happy Call already exists for this ticket.'], 409);
        }

        // No Happy Call exists, ready to create
        return response()->json(['success' => true, 'message' => 'No Happy Call exists, ready to create']);
    }

    /**
     * Process mentions in feedback message and create mention records
     *
     * @param Feedback $feedback
     * @param string $message
     * @return void
     */
    private function processMentions(Feedback $feedback, string $message)
    {
        // Find all @username patterns in the message
        preg_match_all('/@([a-zA-Z0-9_]+)/', $message, $matches);

        if (!empty($matches[1])) {
            $usernames = array_unique($matches[1]);

            foreach ($usernames as $username) {
                // Find user by username
                $mentionedUser = User::where('username', $username)->first();

                if ($mentionedUser && $mentionedUser->id !== Auth::id()) {
                    // Create mention record
                    Mention::create([
                        'feedback_id' => $feedback->id,
                        'mentioned_user_id' => $mentionedUser->id,
                        'mentioner_user_id' => Auth::id(),
                        'username_mentioned' => $username,
                        'is_read' => false,
                    ]);
                }
            }
        }
    }

    private function handleHappyCallCreation($request, $ticket, $ticket_number)
    {
        $request->validate([
            'resolved_date' => 'required|date|before_or_equal:happy_call_date|before_or_equal:today',
            'happy_call_date' => 'required|date|after_or_equal:resolved_date',
            'customer_satisfied' => 'required|in:Yes,No',
            'delay_reason' => 'required|string|max:1000',
            'voice_of_customer' => 'nullable|string|max:2000'
        ]);

        $resolved = Carbon::parse($request->resolved_date);
        $happyCall = Carbon::parse($request->happy_call_date);

        if ($happyCall->diffInDays($resolved) > 30) {
            return response()->json(['success' => false, 'error' => 'Happy call date should not be more than 30 days after resolution date.'], 422);
        }

        $result = DB::transaction(function () use ($request, $ticket) {
            return HappyCallStatus::create([
                'ici_id' => $ticket->id,
                'resolved_date' => $request->resolved_date,
                'happy_call_date' => $request->happy_call_date,
                'customer_satisfied' => $request->customer_satisfied,
                'delay_reason' => $request->delay_reason,
                'voice_of_customer' => $request->voice_of_customer,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Happy Call submitted successfully!',
            'happy_call_id' => $result->id
        ]);
    }

    /**
     * Get complaint history by contact number
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComplaintHistory(Request $request)
    {
        try {
            $contactNo = $request->input('contact_no');

            if (!$contactNo) {
                return response()->json(['error' => 'Contact number is required'], 400);
            }

            // Find all COMS records with the same contact number
            $comsRecords = Coms::where('contact_number', $contactNo)->get();

            if ($comsRecords->isEmpty()) {
                return response()->json(['history' => []]);
            }

            $history = [];

            foreach ($comsRecords as $coms) {
                // Get associated ICI record if exists
                $ici = InitialCustomerInformation::where('complaint_id', $coms->id)->first();

                $history[] = [
                    'complaint_number' => $coms->complaint_number,
                    'job' => $coms->job,
                    'coms_complaint_date' => $coms->coms_complaint_date,
                    'job_type' => $coms->job_type,
                    'customer_name' => $coms->customer_name,
                    'technician_name' => $coms->technician_name,
                    'date_of_purchase' => $coms->date_of_purchase,
                    'product' => $coms->product,
                    'job_status' => $coms->job_status,
                    'problem' => $coms->problem,
                    'work_done' => $coms->work_done,
                    'ticket_number' => $ici ? $ici->ticket_number : null,
                    'case_status' => $ici ? $ici->case_status : null,
                    'escalation_level' => $ici ? $ici->escalation_level : null,
                    'complaint_escalation_date' => $ici ? $ici->complaint_escalation_date : null,
                ];
            }

            return response()->json(['history' => $history]);

        } catch (\Exception $e) {
            Log::error('Error fetching complaint history', [
                'error' => $e->getMessage(),
                'contact_no' => $request->input('contact_no'),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'error' => 'Unable to fetch complaint history'
            ], 500);
        }
    }


}
