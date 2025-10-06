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
            {{-- Ticket search form --}}
            <form id="ticketSearchForm" class="form-card p-4 shadow rounded">
                @csrf
                <!-- Initial Customer Information -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0 text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-person-vcard"></i> Initial Customer Information
                    </h5>
                    <span class="badge rounded-pill bg-light text-secondary">Step 1</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ticket No</label>
                        <div class="input-group">
                            <input type="text" name="ticket_no" id="ticket_no" class="form-control" required value="{{ $ici->ticket_no ?? '' }}">
                            <button type="button" id="searchBtn" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="service_center" class="form-label fw-semibold">Service Center</label>
                        <input name="service_center" id="service_center" class="form-control" readonly value="{{ $ici->service_center ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label for="complaint_escalation_date" class="form-label">Complaint Escalation Date</label>
                        <input type="date" class="form-control" name="complaint_escalation_date"
                            id="complaint_escalation_date" readonly value="{{ $ici ? \Carbon\Carbon::parse($ici->complaint_escalation_date)->format('Y-m-d') : '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="case_status" class="form-label fw-semibold">Case Status</label>
                        <input name="case_status" id="case_status" class="form-control" readonly value="{{ $ici->case_status ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Aging</label>
                        <input type="number" name="aging" id="aging" class="form-control" readonly value="{{ $ici ? round(\Carbon\Carbon::parse($ici->complaint_escalation_date)->diffInDays(now())) : '' }}">
                    </div>
                    <div class="col-md-6">
                        <label for="complaint_category" class="form-label fw-semibold">Complaint Category</label>
                        <input name="complaint_category" id="complaint_category" class="form-control" readonly value="{{ $ici->complaint_category ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label for="agent_name" class="form-label">Agent Name</label>
                        <input type="text" class="form-control" name="agent_name" id="agent_name" readonly value="{{ $ici->agent_name ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label for="reason_of_escalation" class="form-label fw-semibold">Reason of Escalation</label>
                        <input name="reason_of_escalation" id="reason_of_escalation" class="form-control" readonly value="{{ $ici->reason_of_escalation ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Escalation Level</label>
                        <input name="escalation_level" id="escalation_level" class="form-control" readonly value="{{ $ici->escalation_level ?? '' }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Voice of Customer</label>
                        <textarea name="voice_of_customer" id="voice_of_customer" rows="3" class="form-control" readonly>{{ $ici->voice_of_customer ?? '' }}</textarea>
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
                            @csrf
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
                {{-- Show form only if no happy call exists --}}
                @php
                    $hasHappyCall = ($ici && $ici->relationLoaded('happyCallStatus') && !is_null($ici->happyCallStatus)) ||
                                  ($success_message && $submitted_ticket_no && (!$ici || $ici->ticket_no === $submitted_ticket_no));
                @endphp
                <div id="happyCallForm" class="card mt-4" style="{{ $hasHappyCall ? 'display: none;' : '' }}">
                    <div class="card-header card-header-primary d-flex align-items-center gap-2">
                        <i class="bi bi-emoji-smile text-primary"></i>
                        <span class="fw-semibold text-primary">Happy Call Status</span>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $happyCallTicketNo = $ici ? $ici->ticket_no : $submitted_ticket_no;
                        @endphp
                        <form method="POST" action="{{ $happyCallTicketNo ? route('agent.happy-call.save', $happyCallTicketNo) : '#' }}">
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
                @php
                    $hasHappyCall = ($ici && $ici->relationLoaded('happyCallStatus') && !is_null($ici->happyCallStatus)) ||
                                  ($success_message && $submitted_ticket_no && (!$ici || $ici->ticket_no === $submitted_ticket_no));
                @endphp
                <div class="alert alert-success mt-3" id="happyCallSuccess" style="{{ $hasHappyCall ? '' : 'display: none;' }}">
                    @if($success_message && $submitted_ticket_no)
                        ✅ {{ $success_message }}
                    @else
                        ✅ Happy Call already submitted for this ticket.
                    @endif
                </div>
            </div>

            <script>
                document.getElementById("searchBtn").addEventListener("click", function() {
                    let ticketNo = document.getElementById("ticket_no").value.trim();

                    if (!ticketNo) {
                        alert("Please enter a ticket number");
                        return;
                    }

                    // Populate the fields via AJAX
                    fetch("{{ route('agent.ticket.search') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                ticket_no: ticketNo
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                alert(data.error);
                            } else {
                                document.getElementById("service_center").value = data.service_center;
                                document.getElementById("complaint_escalation_date").value = data
                                    .complaint_escalation_date ?? '';
                                document.getElementById("case_status").value = data.case_status;
                                document.getElementById("aging").value = data.aging;
                                document.getElementById("complaint_category").value = data.complaint_category;
                                document.getElementById("agent_name").value = data.agent_name;
                                document.getElementById("reason_of_escalation").value = data.reason_of_escalation;
                                document.getElementById("escalation_level").value = data.escalation_level;
                                document.getElementById("voice_of_customer").value = data.voice_of_customer;

                                // Show chat section and update it
                                document.getElementById("chatSection").style.display = "block";
                                document.getElementById("ticketBadge").textContent = ticketNo;
                                document.getElementById("chatForm").action = "{{ url('/home-agent/ticket') }}/" + ticketNo + "/feedback";
                                document.getElementById("chatMessage").disabled = false;
                                document.getElementById("chatForm").querySelector("button").disabled = false;

                                // Handle happy call form
                                const happyCallForm = document.getElementById("happyCallForm");
                                const happyCallSuccess = document.getElementById("happyCallSuccess");
                                if (data.has_happy_call) {
                                    if (happyCallForm) happyCallForm.style.display = "none";
                                    if (happyCallSuccess) {
                                        happyCallSuccess.style.display = "block";
                                        happyCallSuccess.textContent = "✅ Happy Call already submitted for this ticket.";
                                    }
                                } else {
                                    if (happyCallForm) {
                                        happyCallForm.style.display = "block";
                                        // Update form action
                                        const form = happyCallForm.querySelector("form");
                                        if (form) {
                                            form.action = "{{ url('/agent') }}/" + ticketNo + "/happy-call";
                                        }
                                    }
                                    if (happyCallSuccess) happyCallSuccess.style.display = "none";
                                }

                                // Update ticket badge and form state
                                document.getElementById("ticketBadge").textContent = ticketNo;

                                // Update chat form action
                                document.getElementById("chatForm").action = "{{ url('/home-agent/ticket') }}/" + ticketNo + "/feedback";

                                // Clear existing chat messages
                                document.getElementById("chatScrollArea").innerHTML = '<p class="text-center text-muted my-5">Loading chat...</p>';

                                // Load feedbacks
                                fetch("{{ url('/home-agent/ticket') }}/" + ticketNo + "/feedbacks")
                                    .then(response => response.json())
                                    .then(feedbacks => {
                                        const chatBox = document.getElementById("chatScrollArea");
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
                                    })
                                    .catch(error => {
                                        console.error('Error loading feedbacks:', error);
                                        document.getElementById("chatScrollArea").innerHTML = '<p class="text-center text-muted my-5">Error loading chat messages.</p>';
                                    });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while searching for the ticket.');
                        });
                });

                // Handle chat form submission
                document.getElementById("chatForm").addEventListener("submit", function(e) {
                    e.preventDefault();

                    const form = this;
                    const formData = new FormData(form);

                    fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                // Append new message bubble (right side "You")
                                const chatBox = document.getElementById('chatScrollArea');
                                const bubble = document.createElement('div');
                                bubble.classList.add('d-flex', 'justify-content-end', 'mb-3');
                                bubble.innerHTML = `
                                <div class="p-2 rounded-3" style="max-width: 70%; background-color: #d1e7dd;">
                                    <div class="fw-semibold mb-1">You <span class="text-muted">(${data.role})</span></div>
                                    <div>${data.message}</div>
                                    <div class="mt-1"><small class="text-muted">${data.time}</small></div>
                                </div>
                            `;
                                chatBox.appendChild(bubble);
                                chatBox.scrollTop = chatBox.scrollHeight; // auto-scroll

                                // Reset input
                                document.getElementById('chatMessage').value = '';
                            } else if (data.error) {
                                alert(data.error);
                            }
                        })
                        .catch(err => console.error(err));
                });

                // Real-time feedback updates
                window.lastFeedbackId = {{ $feedbacks->last()->id ?? 0 }};
                function fetchNewFeedbacks() {
                    if (document.getElementById("chatSection").style.display !== "none") {
                        const ticketNo = document.getElementById("ticketBadge").textContent;
                        if (ticketNo) {
                            fetch("{{ url('/home-agent/ticket') }}/" + ticketNo + "/feedbacks")
                                .then(response => response.json())
                                .then(data => {
                                    const chatBox = document.getElementById('chatScrollArea');

                                    data.forEach(fb => {
                                        if (fb.id > window.lastFeedbackId) {
                                            const isMe = fb.name === "{{ auth()->user()->name }}";
                                            const messageClass = isMe ? 'justify-content-end' : 'justify-content-start';
                                            const bgColor = isMe ? '#d1e7dd' : '#e2e3e5';
                                            const senderName = isMe ? 'You' : fb.name;

                                            const bubble = document.createElement('div');
                                            bubble.classList.add('d-flex', messageClass, 'mb-3');
                                            bubble.innerHTML = `
                                                <div class="p-2 rounded-3" style="max-width: 70%; background-color: ${bgColor};">
                                                    <div class="fw-semibold mb-1">${senderName} <span class="text-muted">(${fb.role})</span></div>
                                                    <div>${fb.message}</div>
                                                    <div class="mt-1"><small class="text-muted">${fb.time}</small></div>
                                                </div>
                                            `;
                                            chatBox.appendChild(bubble);
                                            chatBox.scrollTop = chatBox.scrollHeight;
                                            window.lastFeedbackId = fb.id;
                                        }
                                    });
                                })
                                .catch(err => console.error(err));
                        }
                    }
                }
                setInterval(fetchNewFeedbacks, 5000);
            </script>
        </div>
    </div>
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