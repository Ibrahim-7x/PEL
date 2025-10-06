@extends('layouts.app')

@section('title', 'Management')

@push('styles')
    <link href="{{ asset('css/managemnet.css') }}" rel="stylesheet">
@endpush

@section('content')

    <div class="container mt-4">
        <div id="chatArea">
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
            <div id="chatSection" style="{{ empty($ici) ? 'display: none;' : '' }}">
                {{-- Chat header --}}
                <div class="d-flex align-items-center mb-3 mt-4">
                    <h4 class="mb-0">
                        Ticket <span class="badge rounded-pill text-bg-primary" id="ticketBadge">{{ $ici->ticket_no ?? '' }}</span>
                    </h4>
                </div>

                {{-- Chat box --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body chat-scroll" id="chatScrollArea" style="height: 420px; overflow-y: auto;">
                        @if($ici)
                            @forelse($feedbacks as $feedback)
                                @php
                                    $isMe = isset(auth()->user()->name) && $feedback->name === auth()->user()->name;
                                @endphp

                                @if ($isMe)
                                    <div class="d-flex justify-content-end mb-3">
                                        <div class="p-2 rounded-3" style="max-width: 70%; background-color: #d1e7dd;">
                                            <div class="fw-semibold mb-1">You <span
                                                    class="text-muted">({{ $feedback->role }})</span></div>
                                            <div>{{ $feedback->message }}</div>
                                            <div class="mt-1"><small
                                                    class="text-muted">{{ $feedback->created_at->format('d M Y, h:i A') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex justify-content-start mb-3">
                                        <div class="p-2 rounded-3" style="max-width: 70%; background-color: #e2e3e5;">
                                            <div class="fw-semibold mb-1">{{ $feedback->name }} <span
                                                    class="text-muted">({{ $feedback->role }})</span></div>
                                            <div>{{ $feedback->message }}</div>
                                            <div class="mt-1"><small
                                                    class="text-muted">{{ $feedback->created_at->format('d M Y, h:i A') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <p class="text-center text-muted my-5">No feedback yet. Be the first to send a message!
                                </p>
                            @endforelse
                        @endif
                    </div>

                    {{-- Chat input --}}
                    <div class="card-footer card-footer-grey">
                        <form id="chatForm" action="{{ $ici ? route('management.feedback.store', $ici->ticket_no) : '#' }}"
                            method="POST" class="chat-input-form">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="message" id="chatMessage"
                                    class="form-control @error('message') is-invalid @enderror"
                                    placeholder="Type your message…" autocomplete="off" required {{ empty($ici) ? 'disabled' : '' }}>
                                <button type="submit" class="btn btn-primary" {{ empty($ici) ? 'disabled' : '' }}>
                                    <i class="bi bi-send-fill me-1"></i> Send
                                </button>
                            </div>
                            @error('message')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </form>
                    </div>
                </div>
                <script>
                    let lastFeedbackId = {{ $feedbacks->last()->id ?? 0 }};

                    function fetchNewFeedbacks() {
                        if (document.getElementById("chatSection").style.display !== "none") {
                            const ticketNo = document.getElementById("ticketBadge").textContent;
                            if (ticketNo) {
                                fetch("{{ url('/home-management/ticket') }}/" + ticketNo + "/feedbacks")
                                    .then(response => response.json())
                                    .then(data => {
                                        const chatBox = document.getElementById('chatScrollArea');

                                        data.forEach(fb => {
                                            if (fb.id > lastFeedbackId) {
                                                const isMe = fb.name === "{{ auth()->user()->name }}";

                                                const bubble = document.createElement('div');
                                                bubble.classList.add('d-flex', isMe ? 'justify-content-end' :
                                                    'justify-content-start', 'mb-3');

                                                bubble.innerHTML = `
                                                    <div class="p-2 rounded-3" style="max-width: 70%; background-color: ${isMe ? '#d1e7dd' : '#e2e3e5'};">
                                                        <div class="fw-semibold mb-1">
                                                            ${isMe ? 'You' : fb.name} <span class="text-muted">(${fb.role})</span>
                                                        </div>
                                                        <div>${fb.message}</div>
                                                        <div class="mt-1"><small class="text-muted">${fb.time}</small></div>
                                                    </div>
                                                `;

                                                chatBox.appendChild(bubble);
                                                chatBox.scrollTop = chatBox.scrollHeight;
                                                lastFeedbackId = fb.id;
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

            <script>
                document.getElementById("searchBtn").addEventListener("click", function() {
                    let ticketNo = document.getElementById("ticket_no").value.trim();

                    if (!ticketNo) {
                        alert("Please enter a ticket number");
                        return;
                    }

                    // Populate the fields via AJAX
                    fetch("{{ route('management.ticket.search') }}", {
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
                                document.getElementById("chatForm").action = "{{ url('/home-management/ticket') }}/" + ticketNo + "/feedback";
                                document.getElementById("chatMessage").disabled = false;
                                document.getElementById("chatForm").querySelector("button").disabled = false;

                                // Clear existing chat messages
                                document.getElementById("chatScrollArea").innerHTML = '<p class="text-center text-muted my-5">Loading chat...</p>';

                                // Load feedbacks
                                fetch("{{ url('/home-management/ticket') }}/" + ticketNo + "/feedbacks")
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
            </script>
        </div>
    </div>

    <!-- ⚫ Footer -->
    <footer class="text-center py-4 bg-dark text-white mt-5">
        <p class="mb-0">&copy; {{ date('Y') }} PEL. All rights reserved.</p>
    </footer>
@endsection
