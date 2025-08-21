<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ServiceCenter;
use App\Models\ComplaintCategory;
use App\Models\CaseStatus;
use App\Models\EscalationReason;
use App\Models\InitialCustomerInformation;
use App\Models\Feedback;
use App\Models\HappyCallStatus;

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
            'feedbacks'
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

    public function saveHappyCallStatus(Request $request, string $ticket_no)
    {
        $ticket = InitialCustomerInformation::where('ticket_no', $ticket_no)->first();

        if (!$ticket) {
            return back()->withErrors(['error' => 'Ticket not found.']);
        }

        // Prevent duplicate Happy Call for same ticket
        if (HappyCallStatus::where('ici_id', $ticket->id)->exists()) {
            return back()->withErrors(['error' => 'Happy Call already exists for this ticket.']);
        }

        $request->validate([
            'resolved_date'      => 'required|date',
            'happy_call_date'    => 'required|date',
            'customer_satisfied' => 'required|in:Yes,No',
            'delay_reason'       => 'nullable|string|max:1000',
            'voice_of_customer'  => 'nullable|string|max:2000',
        ]);

        HappyCallStatus::create([
            'ici_id'             => $ticket->id, // foreign key
            'resolved_date'      => $request->resolved_date,
            'happy_call_date'    => $request->happy_call_date,
            'customer_satisfied' => $request->customer_satisfied,
            'delay_reason'       => $request->delay_reason,
            'voice_of_customer'  => $request->voice_of_customer,
        ]);

        return back()->with('success', 'Happy Call status saved successfully!');
    }


}
