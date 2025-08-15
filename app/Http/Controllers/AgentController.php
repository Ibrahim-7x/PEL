<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\ServiceCenter;
use App\Models\ComplaintCategory;
use App\Models\CaseStatus;
use App\Models\EscalationReason;
use App\Models\InitialCustomerInformation;
use App\Models\Feedback;

class AgentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $serviceCenters = ServiceCenter::orderBy('sc_name')->get();
        $complaintCategory = ComplaintCategory::orderBy('category_name')->get();
        $caseStatus = CaseStatus::orderBy('status')->get();
        $reasonofEscalation = EscalationReason::orderBy('reason')->get();

        $ici = null;
        $feedbacks = collect();

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
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


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

    public function showTicket(string $ticket_no)
    {
        $ici = InitialCustomerInformation::where('ticket_no', $ticket_no)->firstOrFail();

        $feedbacks = Feedback::where('ici_id', $ici->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Pass the same variable names the Blade expects
        return view('agent', [
            'ici'        => $ici,
            'feedbacks'  => $feedbacks,
            // if your view needs the lists below, fetch them here as well:
            'serviceCenters' => ServiceCenter::orderBy('sc_name')->get(),
            'complaintCategory' => ComplaintCategory::orderBy('category_name')->get(),
            'caseStatus' => CaseStatus::orderBy('status')->get(),
            'reasonofEscalation' => EscalationReason::orderBy('reason')->get(),
        ]);
    }

    public function storeFeedback(Request $request, string $ticket_no)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $ici = InitialCustomerInformation::where('ticket_no', $ticket_no)->firstOrFail();

        Feedback::create([
            'ici_id'  => $ici->id,
            'name'    => Auth::user()->name,
            'role'    => Auth::user()->role ?? 'Agent',
            'message' => $request->message,
        ]);

        return redirect()
            ->route('agent.ticket', $ticket_no)
            ->with('success', 'Feedback added!');
    }

}
