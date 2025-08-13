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

    public function index()
    {
        $serviceCenters = ServiceCenter::orderBy('sc_name')->get();
        $complaintCategory = ComplaintCategory::orderBy('category_name')->get();
        $caseStatus = CaseStatus::orderBy('status')->get();
        $reasonofEscalation = EscalationReason::orderBy('reason')->get();
        return view('agent', compact('serviceCenters','complaintCategory', 'caseStatus', 'reasonofEscalation'));
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function store(Request $request)
    {
        // Validate inputs
        $validated = $request->validate([
            'ticket_no' => 'required|string|max:50',
            'service_center' => 'required|string',
            'complaint_escalation_date' => 'required|date',
            'case_status' => 'required|integer',
            'complaint_category' => 'required|integer',
            'agent_name' => 'required|string',
            'reason_of_escalation' => 'required|integer',
            'escalation_level' => 'required|string',
            'voice_of_customer' => 'required|string',
        ]);

        // Trim ticket_no
        $ticketNo = trim($validated['ticket_no']);

        // Save to database
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

        return redirect()->back()->with('success', 'Data saved!');

        return redirect()->back()->with('success', 'Customer information saved successfully.');
    }
}
