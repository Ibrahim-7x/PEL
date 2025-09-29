<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\InitialCustomerInformation;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

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
    
    public function index(Request $request)
    {
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

        return view('management', compact(
            'ici',
            'feedbacks'
        ));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showTicket(string $ticket_no)
    {
        $ici = InitialCustomerInformation::where('ticket_no', $ticket_no)->firstOrFail();

        $feedbacks = Feedback::where('ici_id', $ici->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Pass the same variable names the Blade expects
        return view('Management', [
            'ici'        => $ici,
            'feedbacks'  => $feedbacks,
        ]);
    }

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
                'role'    => Auth::user()->role ?? 'Management',
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

            // Fallback for normal (non-AJAX) submits
            return redirect()
                ->route('management.ticket', $ticket_no)
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
}
