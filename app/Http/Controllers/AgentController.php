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
        try {
            // Optional: Pre-check before inserting
            if (InitialCustomerInformation::where('ticket_no', $request->ticket_no)->exists()) {
                return back()->withErrors(['ticket_no' => 'This ticket number already exists. Please use a different one.'])->withInput();
            }

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
        catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry error code
                return back()->withErrors(['ticket_no' => 'This ticket number already exists. Please use a different one.'])->withInput();
            }
            throw $e; // rethrow for other errors
        }
    }
}
