<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
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
        $caseStatus = CaseStatus::orderBy('status')->get();
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
            'caseStatus',
            'reasonofEscalation',
            'ici',
            'feedbacks',
            'delayReason'
        ));
    }

    // Save new ICI record
    public function store(Request $request)
    {
        $request->validate([
            'ticket_no' => 'required|unique:initial_customer_information,ticket_no',
            'service_center' => 'required',
            'complaint_escalation_date' => 'required|date',
            'case_status' => 'required',
            'complaint_category' => 'required',
            'agent_name' => 'required',
            'reason_of_escalation' => 'required',
            'escalation_level' => 'required',
            'voice_of_customer' => 'required'
        ]);

        InitialCustomerInformation::create([
            'ticket_no' => $request->ticket_no,
            'service_center' => $request->service_center,
            'complaint_escalation_date' => $request->complaint_escalation_date,
            'case_status' => $request->case_status,
            'complaint_category' => $request->complaint_category,
            'agent_name' => $request->agent_name,
            'reason_of_escalation' => $request->reason_of_escalation,
            'escalation_level' => $request->escalation_level,
            'voice_of_customer' => $request->voice_of_customer,
            'u_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Record saved successfully.');
    }

    // Show a specific ticket's details + feedback
    public function showTicket(string $ticket_no)
    {
        $ici = InitialCustomerInformation::where('ticket_no', $ticket_no)->firstOrFail();

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
                $data = $response->json();
    
                if ($data['Success'] === true && isset($data['ComplaintDetails'][0])) {
                    return response()->json($data['ComplaintDetails'][0]);
                } else {
                    return response()->json(['error' => 'Complaint not found or invalid response'], 404);
                }
            } else {
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
     *
     * @param StoreHappyCallStatusRequest $request
     * @param string $ticket_no
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveHappyCallStatus(StoreHappyCallStatusRequest $request, string $ticket_no)
    {
        try {
            // Use database transaction for data consistency
            return DB::transaction(function () use ($request, $ticket_no) {
                // Find ticket with optimized query (select only needed columns)
                $ticket = InitialCustomerInformation::select('id', 'ticket_no')
                    ->where('ticket_no', $ticket_no)
                    ->lockForUpdate() // Prevent race conditions
                    ->first();

                // Check if ticket exists
                if (!$ticket) {
                    Log::warning('Attempted to save Happy Call for non-existent ticket', [
                        'ticket_no' => $ticket_no,
                        'user_id' => auth()->id(),
                        'ip' => request()->ip(),
                    ]);
                    return back()->withErrors(['error' => 'Ticket not found.']);
                }

                // Check for existing Happy Call with optimized query
                $existingHappyCall = HappyCallStatus::select('id')
                    ->where('ici_id', $ticket->id)
                    ->exists();

                if ($existingHappyCall) {
                    Log::info('Duplicate Happy Call attempt blocked', [
                        'ticket_no' => $ticket_no,
                        'user_id' => auth()->id(),
                    ]);
                    return back()->withErrors(['error' => 'Happy Call already exists for this ticket.']);
                }

                // Create Happy Call status with validated and sanitized data
                $happyCallStatus = HappyCallStatus::create([
                    'ici_id'             => $ticket->id,
                    'resolved_date'      => $request->validated()['resolved_date'],
                    'happy_call_date'    => $request->validated()['happy_call_date'],
                    'customer_satisfied' => $request->validated()['customer_satisfied'],
                    'delay_reason'       => $request->validated()['delay_reason'],
                    'voice_of_customer'  => $request->validated()['voice_of_customer'],
                ]);

                // Log successful creation
                Log::info('Happy Call status created successfully', [
                    'ticket_no' => $ticket_no,
                    'happy_call_id' => $happyCallStatus->id,
                    'user_id' => auth()->id(),
                ]);

                return back()->with('success', 'Happy Call status saved successfully!');
            });

        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database-specific errors
            Log::error('Database error while saving Happy Call status', [
                'ticket_no' => $ticket_no,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->withErrors([
                'error' => 'A database error occurred. Please try again later.'
            ]);
        } catch (\Exception $e) {
            // Handle general exceptions
            Log::error('Unexpected error while saving Happy Call status', [
                'ticket_no' => $ticket_no,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return back()->withErrors([
                'error' => 'An unexpected error occurred. Please contact support if the problem persists.'
            ]);
        }
    }
}
