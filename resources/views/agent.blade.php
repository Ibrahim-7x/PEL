@extends('layouts.app')

@section('title', 'Agent')

@section('meta')
    @vite('resources/css/agent.css')
    <meta name="last-feedback-id" content="{{ (isset($ici) && $ici && isset($feedbacks) && $feedbacks->count() > 0) ? $feedbacks->last()->id : 0 }}">
@endsection

@section('content')

    <!-- 📝 RU CASE Form Section -->
    <section class="py-5 bg-light agent-page">
        <div class="container">
            {{-- Success Message --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h2 class="fw-bold mb-2 text-center d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-clipboard2-check text-primary"></i>
                RU CASE
            </h2>
            <p class="text-muted text-center mb-4">Record, track and collaborate on customer escalations</p>

            <!-- Customer Detail From COMS -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header card-header-warning d-flex align-items-center">
                    <i class="bi bi-database-gear text-warning me-2"></i>
                    <span class="fw-semibold text-warning">Customer Detail From COMS</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Complaint #</label>
                            <div class="input-group">
                                <input type="text" id="complaint_number" name="complaint_number" class="form-control"
                                    placeholder="000000-000000">
                                <button type="button" id="searchComplaintBtn" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <small class="text-muted">Fetch customer/job details from COMS</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Job #</label>
                            <input type="text" name="job_number" id="job_number" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">COMS Complaint Date</label>
                            <input type="date" name="coms_complaint_date" id="coms_complaint_date" class="form-control"
                                readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Job Type</label>
                            <input type="text" name="job_type" id="job_type" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact No</label>
                            <input type="text" name="contact_no" id="contact_no" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Technician Name</label>
                            <input type="text" name="technician_name" id="technician_name" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Purchase</label>
                            <input type="date" name="purchase_date" id="purchase_date" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Product</label>
                            <input type="text" name="product" id="product" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Job Status</label>
                            <input type="text" name="job_status" id="job_status" class="form-control" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Problem</label>
                            <textarea id="problem" name="problem" rows="2" class="form-control" readonly></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Work Done</label>
                            <textarea id="workdone" name="workdone" rows="2" class="form-control" readonly></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <!-- Initial Customer Information -->
            <form action="{{ route('agent.store') }}" method="POST" class="form-card p-4 shadow rounded">
                @csrf
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0 text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-person-vcard"></i> Initial Customer Information
                    </h5>
                    <span class="badge rounded-pill bg-light text-secondary">Step 1</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ticket No</label>
                        <input type="text" name="ticket_no" class="form-control" required
                            onblur="this.value = this.value.trim();">
                    </div>
                    <div class="col-md-6">
                        <label for="service_center" class="form-label fw-semibold">Service Center</label>
                        <select name="service_center" id="service_center" class="form-control" required>
                            <option value="">-- Select Service Center --</option>
                            @foreach ($serviceCenters as $center)
                                <option value="{{ $center->sc_name }}">{{ $center->sc_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="complaint_escalation_date" class="form-label fw-semibold">Complaint Escalation
                            Date</label>
                        <input type="date" class="form-control" name="complaint_escalation_date"
                            id="complaint_escalation_date" value="{{ now()->format('Y-m-d') }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="case_status" class="form-label fw-semibold">Case Status</label>
                        <select name="case_status" id="case_status" class="form-control" required>
                            <option value="">-- Select Case Status --</option>
                            @foreach ($caseStatus as $status)
                                <option value="{{ $status->status }}">{{ $status->status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="complaint_category" class="form-label fw-semibold">Complaint Category</label>
                        <select name="complaint_category" id="complaint_category" class="form-control" required>
                            <option value="">-- Select Complaint Category --</option>
                            @foreach ($complaintCategory as $category)
                                <option value="{{ $category->category_name }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="agent_name" class="form-label fw-semibold">Agent Name</label>
                        @if (auth()->user()->role === 'Agent')
                            <input type="text" class="form-control" name="agent_name" id="agent_name"
                                value="{{ auth()->user()->name }}" readonly>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label for="reason_of_escalation" class="form-label fw-semibold">Reason of Escalation</label>
                        <select name="reason_of_escalation" id="reason_of_escalation" class="form-control" required>
                            <option value="">-- Select Reason of Escalation --</option>
                            @foreach ($reasonofEscalation as $reason)
                                <option value="{{ $reason->reason }}">{{ $reason->reason }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Escalation Level</label>
                        <select name="escalation_level" class="form-select">
                            <option value="">-- Select Escalation Level --</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Voice of Customer</label>
                        <textarea name="voice_of_customer" rows="3" class="form-control"></textarea>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary px-5">Submit</button>
                </div>
            </form>

            {{-- Feedback --}}
            <div class="container mt-4">
                <div id="chatArea">
                    {{-- Flash messages, errors, ticket search form, chat box --}}
                    @if (empty($ici))
                        {{-- Show ticket search form --}}
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="mb-3">Open a Ticket Chat</h5>
                                <form id="ticketSearchForm" method="GET" action="{{ route('agent.index') }}"
                                    class="row g-2">
                                    <div class="col-auto">
                                        <input type="text" name="ticket_no" class="form-control"
                                            placeholder="Enter Ticket No" required>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-primary">Open</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        {{-- Config for chat.js (used when chatArea is updated via AJAX) --}}
                        <div id="chatConfig" data-feedback-list-url="{{ route('agent.feedback.list', $ici->ticket_no) }}"
                            data-current-user="{{ auth()->user()->name }}"
                            data-agent-index-url="{{ route('agent.index') }}"
                            data-last-feedback-id="{{ $feedbacks->last()->id ?? 0 }}"></div>

                        {{-- Chat header --}}
                        <div class="d-flex align-items-center mb-3">
                            <a href="javascript:void(0)" class="btn btn-outline-secondary btn-sm me-2"
                                onclick="goBackToSearch()">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                            <h4 class="mb-0">
                                Ticket <span class="badge rounded-pill text-bg-primary">{{ $ici->ticket_no }}</span>
                            </h4>
                        </div>

                        {{-- Chat box --}}
                        <div class="card border-0 shadow-sm">
                            <div class="card-body chat-scroll" id="chatScrollArea"
                                style="height: 420px; overflow-y: auto;">
                                @forelse($feedbacks as $feedback)
                                    @php
                                        $isMe = isset(auth()->user()->name) && $feedback->name === auth()->user()->name;
                                        $messageClass = $isMe ? 'justify-content-end' : 'justify-content-start';
                                        $bgColor = $isMe ? '#d1e7dd' : '#e2e3e5';
                                        $senderName = $isMe ? 'You' : $feedback->name;
                                    @endphp

                                    <div class="d-flex {{ $messageClass }} mb-3">
                                        <div class="p-2 rounded-3" style="max-width: 70%; background-color: {{ $bgColor }};">
                                            <div class="fw-semibold mb-1">{{ $senderName }} <span class="text-muted">({{ $feedback->role }})</span></div>
                                            <div>{{ $feedback->message }}</div>
                                            <div class="mt-1"><small class="text-muted">{{ $feedback->created_at->format('d M Y, h:i A') }}</small></div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-muted my-5">No feedback yet. Be the first to send a message!</p>
                                @endforelse
                            </div>

                            {{-- Chat input --}}
                            <div class="card-footer card-footer-grey">
                                <form id="chatForm" action="{{ route('agent.feedback.store', $ici->ticket_no) }}"
                                    method="POST" class="chat-input-form">
                                    @csrf
                                    <div class="input-group">
                                        <input type="text" name="message" id="chatMessage"
                                            class="form-control @error('message') is-invalid @enderror"
                                            placeholder="Type your message…" autocomplete="off" required>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-send-fill me-1"></i> Send
                                        </button>
                                    </div>
                                    @error('message')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </form>
                            </div>
                        </div>
                        @if (!$ici || !$ici->happyCallStatus)
                            {{-- Show form only if no happy call exists --}}
                            <div id="happyCallForm" class="card mt-4">
                                <div class="card-header card-header-primary d-flex align-items-center gap-2">
                                    <i class="bi bi-emoji-smile text-primary"></i>
                                    <span class="fw-semibold text-primary">Happy Call Status</span>
                                </div>
                                <div class="card-body p-0">
                                    <form method="POST" action="{{ route('agent.happy-call.save', $ici->ticket_no) }}">
                                        @csrf
                                        <table class="table table-grey mb-0">
                                            <tr>
                                                <td class="fw-semibold">Case Resolved Date</td>
                                                <td><input type="date" name="resolved_date" class="form-control" required></td>
                                                <td class="fw-semibold">Happy Call Date</td>
                                                <td><input type="date" name="happy_call_date" class="form-control" required></td>
                                                <td class="fw-semibold">Customer Satisfied</td>
                                                <td>
                                                    <select name="customer_satisfied" class="form-select" required>
                                                        <option value="Yes">Yes</option>
                                                        <option value="No">No</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Reasons of Delay</td>
                                                <td colspan="5">
                                                    <select name="delay_reason" class="form-select" required>
                                                        <option value="">-- Select Reason of Delay --</option>
                                                        @foreach ($delayReason as $reason)
                                                            <option value="{{ $reason->reason }}">{{ $reason->reason }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Voice of Customer</td>
                                                <td colspan="5">
                                                    <textarea name="voice_of_customer" class="form-control" rows="2"></textarea>
                                                </td>
                                            </tr>
                                        </table>
                                        <div class="p-3 text-end">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success mt-3">✅ Happy Call already submitted for this ticket.</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </section>


    <footer class="text-center py-4 bg-dark text-white mt-5">
        <p class="mb-0">&copy; {{ date('Y') }} PEL. All rights reserved.</p>
    </footer>

@endsection

@section('scripts')
    <script>
        // Initialize window variables for chat functionality
        window.agentIndexUrl = "{{ route('agent.index') }}";
        @if (isset($ici) && $ici)
            window.feedbackListUrl = "{{ route('agent.feedback.list', $ici->ticket_no) }}";
            window.currentUser = "{{ auth()->user()->name }}";
        @endif
    </script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/chat.js') }}"></script>
@endsection
