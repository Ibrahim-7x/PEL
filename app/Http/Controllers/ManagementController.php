<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ServiceCenter;
use App\Models\ComplaintCategory;
use App\Models\CaseStatus;
use App\Models\EscalationReason;

class ManagementController extends Controller
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
        $escalationDate = '2025-08-10';
        $aging = Carbon::parse($escalationDate)->diffInDays(Carbon::now());
        return view('Management', compact('serviceCenters','complaintCategory', 'caseStatus', 'reasonofEscalation','aging'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

}
