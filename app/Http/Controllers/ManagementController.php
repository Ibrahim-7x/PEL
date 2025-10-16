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
use App\Models\Mention;
use App\Models\User;

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
    

    // Chat page for management
    public function tIndex(Request $request)
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

        return view('t_management', compact(
            'ici',
            'feedbacks'
        ));
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

            // Process mentions in the feedback message
            $this->processMentions($feedback, $request->message);

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

        \Log::info('ManagementController fetchComsData called', ['user_role' => auth()->user()->role ?? 'none', 'complaint_no' => $complaintNo]);

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

    public function searchTicket(Request $request)
    {
        try {
            $complaintNumber = $request->complaint_number;

            $record = \DB::table('initial_customer_information')
                        ->where('complaint_number', $complaintNumber)
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
                'success' => true,
                'exists' => true,
                'ticket_no' => $record->ticket_no,
                'ticket_data' => [
                    'service_center' => $record->service_center,
                    'complaint_escalation_date' => $record->complaint_escalation_date
                    ? \Carbon\Carbon::parse($record->complaint_escalation_date)->format('Y-m-d')
                    : '',
                    'case_status' => $record->case_status,
                    'aging' => $aging,
                    'complaint_category' => $record->complaint_category,
                    'agent_name' => $record->agent_name,
                    'reason_of_escalation' => $record->reason_of_escalation,
                    'escalation_level' => $record->escalation_level,
                    'voice_of_customer' => $record->voice_of_customer,
                ]
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
}
