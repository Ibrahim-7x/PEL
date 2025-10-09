@extends('layouts.app')

@section('title', 'Agent')

@section('meta')
    @vite('resources/css/agent.css')
    <meta name="last-feedback-id" content="{{ (isset($ici) && $ici && isset($feedbacks) && $feedbacks->count() > 0) ? $feedbacks->last()->id : 0 }}">
@endsection

@section('content')
    <div class="container mt-4">
        <div id="chatArea">
            {{-- Flash messages, errors, ticket search form, chat box --}}
            {{-- Complaint search form --}}
            <div class="form-card p-4 shadow rounded">

                <!-- Error Alert for No Ticket Found -->
                <div id="noTicketError" class="alert alert-danger d-none" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>No Ticket Found:</strong> <span id="errorMessage"></span>
                </div>

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
                                        placeholder="000000-000000" value="{{ $ici->complaint_number ?? '' }}">
                                    <button type="button" id="searchComplaintBtn" class="btn btn-primary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Fetch customer/job details from COMS</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Job #</label>
                                <input type="text" name="job_number" id="job_number" class="form-control" readonly value="{{ $ici->job_number ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">COMS Complaint Date</label>
                                <input type="date" name="coms_complaint_date" id="coms_complaint_date" class="form-control"
                                    readonly value="{{ $ici->coms_complaint_date ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Job Type</label>
                                <input type="text" name="job_type" id="job_type" class="form-control" readonly value="{{ $ici->job_type ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Customer Name</label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control" readonly value="{{ $ici->customer_name ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact No</label>
                                <input type="text" name="contact_no" id="contact_no" class="form-control" readonly value="{{ $ici->contact_no ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Technician Name</label>
                                <input type="text" name="technician_name" id="technician_name" class="form-control" readonly value="{{ $ici->technician_name ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date of Purchase</label>
                                <input type="date" name="purchase_date" id="purchase_date" class="form-control" readonly value="{{ $ici->purchase_date ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Product</label>
                                <input type="text" name="product" id="product" class="form-control" readonly value="{{ $ici->product ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Job Status</label>
                                <input type="text" name="job_status" id="job_status" class="form-control" readonly value="{{ $ici->job_status ?? '' }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Problem</label>
                                <textarea id="problem" name="problem" rows="2" class="form-control" readonly>{{ $ici->problem ?? '' }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Work Done</label>
                                <textarea id="workdone" name="workdone" rows="2" class="form-control" readonly>{{ $ici->workdone ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ticket Status Information -->
                <div id="ticketStatusInfo" class="alert alert-info" style="display: none;">
                    <div id="ticketStatusContent"></div>
                </div>

                <!-- Initial Customer Information -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0 text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-person-vcard"></i> Initial Customer Information
                    </h5>
                    <span class="badge rounded-pill bg-light text-secondary">View Only</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ticket No</label>
                        <input type="text" name="ticket_no" id="ticket_no" class="form-control" readonly value="{{ $ici->ticket_no ?? '' }}" placeholder="Auto-generated when complaint is searched">
                        <small class="text-muted">Auto-generated when complaint is searched</small>
                    </div>

                    <div class="col-md-6">
                        <label for="service_center" class="form-label fw-semibold">Service Center</label>
                        <input name="service_center" id="service_center" class="form-control" readonly value="{{ $ici->service_center ?? '' }}" placeholder="Populated when complaint is searched">
                    </div>
                    <div class="col-md-6">
                        <label for="complaint_escalation_date" class="form-label fw-semibold">Complaint Escalation Date</label>
                        <input type="date" class="form-control" name="complaint_escalation_date"
                            id="complaint_escalation_date" readonly value="{{ $ici ? \Carbon\Carbon::parse($ici->complaint_escalation_date)->format('Y-m-d') : '' }}" placeholder="Auto-filled">
                    </div>
                    <div class="col-md-3">
                        <label for="case_status" class="form-label fw-semibold">Case Status</label>
                        <input name="case_status" id="case_status" class="form-control" readonly value="{{ $ici->case_status ?? '' }}" placeholder="Auto-filled">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Aging</label>
                        <input type="number" name="aging" id="aging" class="form-control" readonly value="" placeholder="Auto-calculated">
                    </div>
                    <div class="col-md-6">
                        <label for="complaint_category" class="form-label fw-semibold">Complaint Category</label>
                        <input name="complaint_category" id="complaint_category" class="form-control" readonly value="{{ $ici->complaint_category ?? '' }}" placeholder="Auto-filled">
                    </div>
                    <div class="col-md-6">
                        <label for="agent_name" class="form-label fw-semibold">Agent Name</label>
                        <input type="text" class="form-control" name="agent_name" id="agent_name" readonly value="{{ $ici->agent_name ?? '' }}" placeholder="Auto-filled">
                    </div>
                    <div class="col-md-6">
                        <label for="reason_of_escalation" class="form-label fw-semibold">Reason of Escalation</label>
                        <input name="reason_of_escalation" id="reason_of_escalation" class="form-control" readonly value="{{ $ici->reason_of_escalation ?? '' }}" placeholder="Auto-filled">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Escalation Level</label>
                        <input name="escalation_level" id="escalation_level" class="form-control" readonly value="{{ $ici->escalation_level ?? '' }}" placeholder="Auto-filled">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Voice of Customer</label>
                        <textarea name="voice_of_customer" id="voice_of_customer" rows="3" class="form-control" readonly placeholder="Auto-filled">{{ $ici->voice_of_customer ?? '' }}</textarea>
                    </div>
                </div>

            </form>

            {{-- Chat section --}}
            @php
                $shouldShowChat = $ici || $submitted_ticket_no;
            @endphp
            <div id="chatSection" style="{{ $shouldShowChat ? '' : 'display: none;' }}">
                {{-- Config for chat.js (used when chatArea is updated via AJAX) --}}
                <div id="chatConfig" data-feedback-list-url="{{ $ici ? route('agent.feedback.list', $ici->ticket_no) : '' }}"
                    data-current-user="{{ auth()->user()->name }}" data-agent-index-url="{{ route('agent.index') }}"
                    data-last-feedback-id="{{ $feedbacks->last()->id ?? 0 }}"></div>

                {{-- Chat header --}}
                <div class="d-flex align-items-center mb-3 mt-4">
                    <h4 class="mb-0">
                        Ticket <span class="badge rounded-pill text-bg-primary" id="ticketBadge">{{ $ici->ticket_no ?? $submitted_ticket_no ?? '' }}</span>
                    </h4>
                </div>

                {{-- Chat box --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body chat-scroll" id="chatScrollArea" style="height: 420px; overflow-y: auto;">
                        @if($ici)
                            @forelse($feedbacks as $feedback)
                                @php
                                    $isMe = isset(auth()->user()->name) && $feedback->name === auth()->user()->name;
                                    $messageClass = $isMe ? 'justify-content-end' : 'justify-content-start';
                                    $bgColor = $isMe ? '#d1e7dd' : '#e2e3e5';
                                    $senderName = $isMe ? 'You' : $feedback->name;
                                @endphp

                                <div class="d-flex {{ $messageClass }} mb-3">
                                    <div class="p-2 rounded-3" style="max-width: 70%; background-color: {{ $bgColor }};">
                                        <div class="fw-semibold mb-1">{{ $senderName }} <span
                                                class="text-muted">({{ $feedback->role }})</span></div>
                                        <div>{{ $feedback->message }}</div>
                                        <div class="mt-1"><small
                                                class="text-muted">{{ $feedback->created_at->format('d M Y, h:i A') }}</small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted my-5">No feedback yet. Be the first to send a message!</p>
                            @endforelse
                        @endif
                    </div>

                    {{-- Chat input --}}
                    <div class="card-footer card-footer-grey">
                        @php
                            $chatTicketNo = $ici ? $ici->ticket_no : $submitted_ticket_no;
                        @endphp
                        <form id="chatForm" action="{{ $chatTicketNo ? route('agent.feedback.store', $chatTicketNo) : '#' }}" method="POST"
                            class="chat-input-form">
                            <div class="input-group">
                                <input type="text" name="message" id="chatMessage"
                                        class="form-control @error('message') is-invalid @enderror"
                                        placeholder="Type your message…" autocomplete="off" required {{ empty($chatTicketNo) ? 'disabled' : '' }}">
                                    <button type="submit" class="btn btn-primary" {{ empty($chatTicketNo) ? 'disabled' : '' }}>
                                    <i class="bi bi-send-fill me-1"></i> Send
                                </button>
                            </div>
                            @error('message')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </form>
                    </div>
                </div>
                {{-- Happy Call Form (shown when no Happy Call exists) --}}
                <div id="happyCallForm" class="card mt-4 d-none">
                    <div class="card-header card-header-primary d-flex align-items-center gap-2">
                        <i class="bi bi-emoji-smile text-primary"></i>
                        <span class="fw-semibold text-primary">Happy Call Status</span>
                    </div>
                    <div class="card-body">
                        <form id="happyCallFormElement" method="POST" action="">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Case Resolved Date</label>
                                    <input type="date" name="resolved_date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Happy Call Date</label>
                                    <input type="date" name="happy_call_date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Customer Satisfied</label>
                                    <select name="customer_satisfied" class="form-select" required>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Delay Reason</label>
                                    <select name="delay_reason" class="form-select" required>
                                        <option value="">-- Select Reason --</option>
                                        @foreach ($delayReason as $reason)
                                            <option value="{{ $reason->reason }}">{{ $reason->reason }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Voice of Customer</label>
                                    <textarea name="voice_of_customer" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary" id="happyCallSubmitBtn">Submit Happy Call</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Happy Call Status Banner (shown when Happy Call already exists) --}}
                <div class="alert alert-info mt-3 d-none" id="happyCallBanner" style="background-color: #d1ecf1 !important; border: 1px solid #bee5eb !important; color: #0c5460 !important; padding: 1rem !important; font-weight: bold; margin-top: 1rem;">
                    <strong>✅ Happy Call Status:</strong> Happy Call is already submitted against this complaint number.
                </div>
            </div>

            <script>
                // Function to calculate and update aging
                function updateAging(complaintDate) {
                    if (complaintDate) {
                        try {
                            // Handle different date formats
                            let complaintDateObj;
                            if (complaintDate.includes(' ')) {
                                // Handle Laravel date format like "2025-10-07 00:00:00"
                                complaintDateObj = new Date(complaintDate.replace(' ', 'T'));
                            } else {
                                // Handle date format like "2025-10-07"
                                complaintDateObj = new Date(complaintDate);
                            }

                            const today = new Date();
                            const diffTime = Math.abs(today - complaintDateObj);
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                            document.getElementById('aging').value = diffDays;
                            console.log('Aging calculated:', diffDays, 'days from', complaintDate, 'Date object:', complaintDateObj);
                        } catch (error) {
                            console.error('Error calculating aging:', error, 'Input date:', complaintDate);
                            document.getElementById('aging').value = '';
                        }
                    } else {
                        document.getElementById('aging').value = '';
                        console.log('No complaint date provided for aging calculation');
                    }
                }

                // Initialize aging on page load if we have existing data
                document.addEventListener('DOMContentLoaded', function() {
                    @if($ici && $ici->complaint_escalation_date)
                        updateAging('{{ $ici->complaint_escalation_date }}');
                        console.log('Initial aging calculation for existing ICI data');
                    @endif

                    // Check for existing Happy Call on page load
                    @if($ici && $ici->ticket_no)
                        // If we have a ticket on page load, check Happy Call status after a short delay
                        setTimeout(() => {
                            checkHappyCallStatus('{{ $ici->ticket_no }}');
                        }, 500);
                    @endif
                });

                // Function to check Happy Call status for a ticket
                function checkHappyCallStatus(ticketNo) {
                    console.log('🔍 Checking Happy Call status for ticket:', ticketNo);

                    // Create a minimal request to check if Happy Call exists
                    fetch('/agent/' + ticketNo + '/happy-call', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('📡 Happy Call check response status:', response.status);
                        if (response.status === 409) {
                            // 409 means Happy Call already exists - show banner, hide form
                            console.log('✅ Happy Call already exists for ticket:', ticketNo);
                            showHappyCallBanner();
                            hideHappyCallForm();
                            return { exists: true };
                        } else if (response.ok) {
                            // 200 means no Happy Call exists - show form, hide banner
                            console.log('❌ No Happy Call found for ticket:', ticketNo);
                            showHappyCallForm(ticketNo);
                            hideHappyCallBanner();
                            return { exists: false };
                        } else {
                            throw new Error('HTTP ' + response.status);
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error checking Happy Call status:', error);
                        // On error, show form as fallback
                        showHappyCallForm(ticketNo);
                        hideHappyCallBanner();
                    });
                }

                // Function to show Happy Call form
                function showHappyCallForm(ticketNo) {
                    const form = document.getElementById("happyCallForm");
                    const formElement = document.getElementById("happyCallFormElement");
                    if (form && formElement) {
                        form.style.display = "block";
                        form.classList.remove("d-none");
                        // Update form action
                        formElement.action = "/agent/" + ticketNo + "/happy-call";
                        console.log('📝 Happy Call form is now visible for ticket:', ticketNo);
                    }
                }

                // Function to hide Happy Call form
                function hideHappyCallForm() {
                    const form = document.getElementById("happyCallForm");
                    if (form) {
                        form.style.display = "none";
                        form.classList.add("d-none");
                        console.log('🙈 Happy Call form is now hidden');
                    }
                }

                // Function to show Happy Call banner
                function showHappyCallBanner() {
                    const banner = document.getElementById("happyCallBanner");
                    if (banner) {
                        banner.classList.remove("d-none");
                        banner.style.display = "block";
                        console.log('🎯 Happy Call banner is now visible');
                    }
                }

                // Function to hide Happy Call banner
                function hideHappyCallBanner() {
                    const banner = document.getElementById("happyCallBanner");
                    if (banner) {
                        banner.classList.add("d-none");
                        banner.style.display = "none";
                        console.log('🙈 Happy Call banner is now hidden');
                    }
                }

                // Handle Happy Call form submission
                const happyCallFormElement = document.getElementById("happyCallFormElement");
                if (happyCallFormElement) {
                    happyCallFormElement.addEventListener("submit", function(e) {
                        e.preventDefault();

                        const form = this;
                        const submitBtn = document.getElementById("happyCallSubmitBtn");
                        const formData = new FormData(form);

                        // Show loading state
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';

                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Reset button state
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = 'Submit Happy Call';

                            if (data.success) {
                                // Hide form and show banner
                                hideHappyCallForm();
                                showHappyCallBanner();
                                console.log('🎉 Happy Call submitted successfully');
                            } else {
                                alert('Error: ' + (data.error || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            // Reset button state
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = 'Submit Happy Call';
                            console.error('Error submitting Happy Call:', error);
                            alert('Failed to submit Happy Call. Please try again.');
                        });
                    });
                }

                // Function to show error message
                function showErrorMessage(message) {
                    const errorDiv = document.getElementById("noTicketError");
                    const errorMessageSpan = document.getElementById("errorMessage");
                    if (errorDiv && errorMessageSpan) {
                        errorMessageSpan.textContent = message;
                        errorDiv.classList.remove("d-none");
                        // Scroll to the error message
                        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }

                // Function to hide error message
                function hideErrorMessage() {
                    const errorDiv = document.getElementById("noTicketError");
                    if (errorDiv) {
                        errorDiv.classList.add("d-none");
                    }
                }

                // Handle complaint number search
                document.getElementById("searchComplaintBtn").addEventListener("click", function() {
                    // Reset Happy Call form and banner visibility for new search
                    hideHappyCallForm();
                    hideHappyCallBanner();
                    const complaintNumber = document.getElementById("complaint_number").value.trim();

                    if (!complaintNumber) {
                        showErrorMessage("Please enter a complaint number");
                        return;
                    }

                    // Hide any previous error messages
                    hideErrorMessage();

                    // Show loading state
                    this.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                    this.disabled = true;

                    // First, fetch COMS data
                    fetch("{{ route('fetch.coms') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            complaint_number: complaintNumber
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Reset button state
                        document.getElementById("searchComplaintBtn").innerHTML = '<i class="bi bi-search"></i>';
                        document.getElementById("searchComplaintBtn").disabled = false;

                        if (data.error) {
                            showErrorMessage('COMS Error: ' + data.error);
                            return;
                        }

                        // Populate COMS fields with fetched data
                        if (data.JobNo) document.getElementById('job_number').value = data.JobNo;
                        if (data.JobDate) {
                            document.getElementById('coms_complaint_date').value = data.JobDate;
                            // Update aging calculation when COMS complaint date is set
                            updateAging(data.JobDate);
                            console.log('COMS Date set:', data.JobDate, 'Aging updated');
                        }
                        if (data.JobType) document.getElementById('job_type').value = data.JobType;
                        if (data.CustomerName) document.getElementById('customer_name').value = data.CustomerName;
                        if (data.ContactNo) document.getElementById('contact_no').value = data.ContactNo;
                        if (data.TechnicianName) document.getElementById('technician_name').value = data.TechnicianName;
                        if (data.PurchaseDate) document.getElementById('purchase_date').value = data.PurchaseDate;
                        if (data.Product) document.getElementById('product').value = data.Product;
                        if (data.JobStatus) document.getElementById('job_status').value = data.JobStatus;
                        if (data.Problem) document.getElementById('problem').value = data.Problem;
                        if (data.WorkDone) document.getElementById('workdone').value = data.WorkDone;

                        // Now fetch ticket information (read-only, no updates)
                        return fetch("{{ route('fetch.ticket.info') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({
                                complaint_number: complaintNumber
                            })
                        });
                    })
                    .then(response => response.json())
                    .then(ticketData => {
                        if (ticketData.success) {
                            // Set the ticket number
                            const ticketInput = document.getElementById('ticket_no');
                            ticketInput.value = ticketData.ticket_no;

                            // If ticket exists, populate form with existing data (read-only)
                            if (ticketData.exists && ticketData.ticket_data) {
                                // Show status message for existing ticket (read-only)
                                const statusInfo = document.getElementById('ticketStatusInfo');
                                const statusContent = document.getElementById('ticketStatusContent');
                                if (statusInfo && statusContent) {
                                    statusInfo.className = 'alert alert-info';
                                    statusContent.innerHTML = `
                                        <strong>📋 Ticket Found</strong><br>
                                        <small>Ticket <strong>${ticketData.ticket_no}</strong> found for this complaint number.</small><br>
                                        <small>Ready for chat and tracking.</small>
                                    `;
                                    statusInfo.style.display = 'block';
                                }

                                // Populate Initial Customer Information fields
                                if (ticketData.ticket_data.service_center) {
                                    document.getElementById('service_center').value = ticketData.ticket_data.service_center;
                                }
                                if (ticketData.ticket_data.case_status) {
                                    document.getElementById('case_status').value = ticketData.ticket_data.case_status;
                                }
                                if (ticketData.ticket_data.complaint_category) {
                                    document.getElementById('complaint_category').value = ticketData.ticket_data.complaint_category;
                                }
                                if (ticketData.ticket_data.agent_name) {
                                    document.getElementById('agent_name').value = ticketData.ticket_data.agent_name;
                                }
                                if (ticketData.ticket_data.reason_of_escalation) {
                                    document.getElementById('reason_of_escalation').value = ticketData.ticket_data.reason_of_escalation;
                                }
                                if (ticketData.ticket_data.voice_of_customer) {
                                    document.getElementById('voice_of_customer').value = ticketData.ticket_data.voice_of_customer;
                                }

                                // Display current escalation level (read-only) - ACTUAL DATABASE VALUE
                                if (ticketData.ticket_data.escalation_level) {
                                    const escalationInput = document.getElementById('escalation_level');
                                    if (escalationInput) {
                                        escalationInput.value = ticketData.ticket_data.escalation_level;
                                        escalationInput.style.borderColor = '#17a2b8';
                                        escalationInput.title = `Current escalation level: ${ticketData.ticket_data.escalation_level}`;
                                    }
                                }

                                // Update aging based on ICI complaint escalation date
                                if (ticketData.ticket_data.complaint_escalation_date) {
                                    document.getElementById('complaint_escalation_date').value = ticketData.ticket_data.complaint_escalation_date;
                                    updateAging(ticketData.ticket_data.complaint_escalation_date);
                                }
                            }

                            // Show chat section and update it
                            const chatSection = document.getElementById("chatSection");
                            if (chatSection) {
                                chatSection.style.display = "block";
                            }

                            // Update ticket badge
                            const ticketBadge = document.getElementById("ticketBadge");
                            if (ticketBadge) {
                                ticketBadge.textContent = ticketData.ticket_no;
                            }

                            // Update chat form action
                            const chatForm = document.getElementById("chatForm");
                            if (chatForm) {
                                chatForm.action = "{{ url('/home-agent/ticket') }}/" + ticketData.ticket_no + "/feedback";
                            }

                            // Enable chat input
                            const chatMessage = document.getElementById("chatMessage");
                            if (chatMessage) {
                                chatMessage.disabled = false;
                            }

                            const chatFormButton = document.getElementById("chatForm").querySelector("button");
                            if (chatFormButton) {
                                chatFormButton.disabled = false;
                            }

                            // Handle Happy Call form/banner for existing tickets
                            if (ticketData.exists && ticketData.ticket_data) {
                                // Check if happy call exists for this ticket
                                const ticketHasHappyCall = ticketData.ticket_data.has_happy_call;

                                if (ticketHasHappyCall) {
                                    console.log('✅ Happy Call exists for ticket:', ticketData.ticket_no);
                                    showHappyCallBanner();
                                    hideHappyCallForm();
                                } else {
                                    console.log('❌ No Happy Call for ticket:', ticketData.ticket_no);
                                    showHappyCallForm(ticketData.ticket_no);
                                    hideHappyCallBanner();
                                }

                                // Always check Happy Call status for existing tickets to ensure accuracy
                                setTimeout(() => {
                                    checkHappyCallStatus(ticketData.ticket_no);
                                }, 100);
                            }

                            // Clear existing chat messages
                            const chatScrollArea = document.getElementById("chatScrollArea");
                            if (chatScrollArea) {
                                chatScrollArea.innerHTML = '<p class="text-center text-muted my-5">Loading chat...</p>';
                            }

                            // Load feedbacks
                            fetch("{{ url('/home-agent/ticket') }}/" + ticketData.ticket_no + "/feedbacks")
                                .then(response => response.json())
                                .then(feedbacks => {
                                    const chatBox = document.getElementById("chatScrollArea");
                                    if (chatBox) {
                                        chatBox.innerHTML = '';

                                        if (feedbacks.length === 0) {
                                            chatBox.innerHTML = '<p class="text-center text-muted my-5">No feedback yet. Be the first to send a message!</p>';
                                        } else {
                                            feedbacks.forEach(fb => {
                                                const isMe = fb.name === "{{ auth()->user()->name }}";
                                                const messageClass = isMe ? 'justify-content-end' : 'justify-content-start';
                                                const bgColor = isMe ? '#d1e7dd' : '#e2e3e5';
                                                const senderName = isMe ? 'You' : fb.name;

                                                const bubble = document.createElement('div');
                                                bubble.className = `d-flex ${messageClass} mb-3`;
                                                bubble.innerHTML = `
                                                    <div class="p-2 rounded-3" style="max-width: 70%; background-color: ${bgColor};">
                                                        <div class="fw-semibold mb-1">${senderName} <span class="text-muted">(${fb.role})</span></div>
                                                        <div>${fb.message}</div>
                                                        <div class="mt-1"><small class="text-muted">${fb.time}</small></div>
                                                    </div>
                                                `;
                                                chatBox.appendChild(bubble);
                                            });
                                            chatBox.scrollTop = chatBox.scrollHeight;
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Error loading feedbacks:', error);
                                    if (chatScrollArea) {
                                        chatScrollArea.innerHTML = '<p class="text-center text-muted my-5">Error loading chat messages.</p>';
                                    }
                                });
                        } else {
                            // Handle new ticket creation
                            if (ticketData.is_new_ticket && ticketData.ticket_no) {
                                // Set the new ticket number
                                const ticketInput = document.getElementById('ticket_no');
                                ticketInput.value = ticketData.ticket_no;

                                // Show success message for new ticket
                                console.log('New ticket generated:', ticketData.ticket_no);

                                // Show chat section for new ticket
                                const chatSection = document.getElementById("chatSection");
                                if (chatSection) {
                                    chatSection.style.display = "block";
                                }

                                // Update ticket badge
                                const ticketBadge = document.getElementById("ticketBadge");
                                if (ticketBadge) {
                                    ticketBadge.textContent = ticketData.ticket_no;
                                }

                                // Update chat form action
                                const chatForm = document.getElementById("chatForm");
                                if (chatForm) {
                                    chatForm.action = "{{ url('/home-agent/ticket') }}/" + ticketData.ticket_no + "/feedback";
                                }

                                // Enable chat input
                                const chatMessage = document.getElementById("chatMessage");
                                if (chatMessage) {
                                    chatMessage.disabled = false;
                                }

                                const chatFormButton = document.getElementById("chatForm").querySelector("button");
                                if (chatFormButton) {
                                    chatFormButton.disabled = false;
                                }

                                // For new tickets, show the Happy Call form
                                showHappyCallForm(ticketData.ticket_no);
                                hideHappyCallBanner();

                                // Set escalation level for new tickets (read-only display)
                                if (ticketData.ticket_data && ticketData.ticket_data.escalation_level) {
                                    const escalationInput = document.getElementById('escalation_level');
                                    if (escalationInput) {
                                        escalationInput.value = ticketData.ticket_data.escalation_level;
                                        escalationInput.style.borderColor = '#17a2b8';
                                        escalationInput.title = `Escalation level: ${ticketData.ticket_data.escalation_level}`;
                                    }
                                }

                                // Clear existing chat messages and show empty state
                                const chatScrollArea = document.getElementById("chatScrollArea");
                                if (chatScrollArea) {
                                    chatScrollArea.innerHTML = '<p class="text-center text-muted my-5">No feedback yet. Be the first to send a message!</p>';
                                }

                            } else {
                                // Handle errors or no ticket found
                                if (ticketData.message) {
                                    showErrorMessage(ticketData.message);
                                } else {
                                    showErrorMessage('Error: ' + (ticketData.error || 'Unknown error occurred'));
                                }

                                // Ensure chat section remains hidden for errors
                                const chatSection = document.getElementById("chatSection");
                                if (chatSection) {
                                    chatSection.style.display = "none";
                                }
                            }
                        }
                    })
                    .catch(error => {
                        // Reset button state
                        document.getElementById("searchComplaintBtn").innerHTML = '<i class="bi bi-search"></i>';
                        document.getElementById("searchComplaintBtn").disabled = false;

                        console.error('Error in complaint search process:', error);
                        showErrorMessage('Failed to process complaint. Please try again.');
                    });
                });




            </script>
        </div>
    </div>

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