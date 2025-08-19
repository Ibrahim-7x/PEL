@extends('layouts.app')

@section('title', 'Agent')

@section('content')

<!-- 📝 RU CASE Form Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold mb-4 text-center">RU CASE</h2>
        
        <!-- Customer Detail From COMS -->
        <h5 class="mb-3 text-warning">Customer Detail From COMS</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Complaint #</label>
                <input type="text" name="complaint_number" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Job #</label>
                <input type="text" name="job_number" class="form-control" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">COMS Complaint Date</label>
                <input type="date" name="coms_complaint_date" class="form-control" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Job Type</label>
                <input type="text" name="job_type" class="form-control" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Customer Name</label>
                <input type="text" name="customer_name" class="form-control" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Contact No</label>
                <input type="text" name="contact_no" class="form-control" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Technician Name</label>
                <input type="text" name="technician_name" class="form-control" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Date of Purchase</label>
                <input type="date" name="purchase_date" class="form-control" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Product</label>
                <input type="text" name="product" class="form-control" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Job Status</label>
                <input type="text" name="job_status" class="form-control" readonly>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Problem</label>
                <input name="problem" rows="2" class="form-control" readonly>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Work Done</label>
                <input name="workdone" rows="2" class="form-control" readonly>
            </div>
        </div>
        
        <hr class="my-4">
        <!-- Initial Customer Information -->

        {{-- Success Message --}}
        @if(session('success'))
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
        <form action="{{ route('agent.store') }}" method="POST" class="p-4 shadow rounded bg-white">
            @csrf
            <h5 class="mb-3 text-primary">Initial Customer Information</h5>
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
                        @foreach($serviceCenters as $center)
                        <option value="{{ $center->sc_name }}">{{ $center->sc_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="complaint_escalation_date" class="form-label fw-semibold">Complaint Escalation Date</label>
                    <input type="date" 
                    class="form-control" 
                    name="complaint_escalation_date" 
                    id="complaint_escalation_date" 
                    value="{{ now()->format('Y-m-d') }}" 
                    readonly>
                </div>
                <div class="col-md-3">
                    <label for="case_status" class="form-label fw-semibold">Case Status</label>
                    <select name="case_status" id="case_status" class="form-control" required>
                        <option value="">-- Select Case Status --</option>
                        @foreach($caseStatus as $status)
                        <option value="{{ $status->status }}">{{ $status->status }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="complaint_category" class="form-label fw-semibold">Complaint Category</label>
                    <select name="complaint_category" id="complaint_category" class="form-control" required>
                        <option value="">-- Select Complaint Category --</option>
                        @foreach($complaintCategory as $category)
                        <option value="{{ $category->category_name }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="agent_name" class="form-label fw-semibold">Agent Name</label>
                    @if(auth()->user()->role === 'Agent')
                    <input type="text" class="form-control" name="agent_name" id="agent_name" 
                    value="{{ auth()->user()->name }}" readonly>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <label for="reason_of_escalation" class="form-label fw-semibold">Reason of Escalation</label>
                    <select name="reason_of_escalation" id="reason_of_escalation" class="form-control" required>
                        <option value="">-- Select Reason of Escalation --</option>
                        @foreach($reasonofEscalation as $reason)
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
                @if(empty($ici))
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="mb-3">Open a Ticket Chat</h5>
                            {{-- Search form --}}
                            <form id="ticketSearchForm" method="GET" action="{{ route('agent.index') }}" class="row g-2">
                                <div class="col-auto">
                                    <input type="text" name="ticket_no" class="form-control" placeholder="Enter Ticket No" required>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-primary">Open</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- Chat header --}}
                    <div class="d-flex align-items-center mb-4">
                        <a href="javascript:void(0)" class="btn btn-light me-2" onclick="goBackToSearch()">← Back</a>
                        <h4 class="mb-0">Ticket #{{ $ici->ticket_no }}</h4>
                    </div>

                    {{-- Chat box --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body" id="chatScrollArea" style="height: 420px; overflow-y: auto; background-color: #f8f9fa;">
                            @forelse($feedbacks as $feedback)
                                @php
                                    $isMe = isset(auth()->user()->name) && $feedback->name === auth()->user()->name;
                                @endphp

                                @if($isMe)
                                    <div class="d-flex justify-content-end mb-3">
                                        <div class="p-2 rounded-3" style="max-width: 70%; background-color: #d1e7dd;">
                                            <div class="fw-semibold mb-1">You <span class="text-muted">({{ $feedback->role }})</span></div>
                                            <div>{{ $feedback->message }}</div>
                                            <div class="mt-1"><small class="text-muted">{{ $feedback->created_at->format('d M Y, h:i A') }}</small></div>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex justify-content-start mb-3">
                                        <div class="p-2 rounded-3" style="max-width: 70%; background-color: #e2e3e5;">
                                            <div class="fw-semibold mb-1">{{ $feedback->name }} <span class="text-muted">({{ $feedback->role }})</span></div>
                                            <div>{{ $feedback->message }}</div>
                                            <div class="mt-1"><small class="text-muted">{{ $feedback->created_at->format('d M Y, h:i A') }}</small></div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <p class="text-center text-muted my-5">No feedback yet. Be the first to send a message!</p>
                            @endforelse
                        </div>

                        {{-- Chat input --}}
                        <div class="card-footer bg-white">
                            <form id="chatForm" action="{{ route('agent.feedback.store', $ici->ticket_no) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <input
                                    type="text"
                                    name="message"
                                    id="chatMessage"
                                    class="form-control @error('message') is-invalid @enderror"
                                    placeholder="Type your message…"
                                    autocomplete="off"
                                    required
                                >
                                <button type="submit" class="btn btn-primary">Send</button>
                                @error('message')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </form>
                        </div>
                    </div>
                    <script>
                        function goBackToSearch() {
                            fetch("{{ route('agent.index') }}")
                                .then(response => response.text())
                                .then(html => {
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(html, 'text/html');
                                    const newContent = doc.querySelector('#chatArea');
                                    document.getElementById('chatArea').innerHTML = newContent.innerHTML;
                                });
                        }

                        // Intercept ticket search form submit
                        document.addEventListener('submit', function(e) {
                            if (e.target && e.target.id === 'ticketSearchForm') {
                                e.preventDefault(); // stop full reload

                                const form = e.target;
                                const formData = new FormData(form);
                                const url = form.action + '?' + new URLSearchParams(formData).toString();

                                fetch(url)
                                .then(response => response.text())
                                .then(html => {
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(html, 'text/html');
                                    const newContent = doc.querySelector('#chatArea');
                                    document.getElementById('chatArea').innerHTML = newContent.innerHTML;
                                });
                            }
                        });

                        // Auto-scroll on load
                        window.addEventListener('load', function () {
                            var box = document.getElementById('chatScrollArea');
                            if (box) { box.scrollTop = box.scrollHeight; }
                        });

                        document.addEventListener('submit', function(e) {
                            if (e.target && e.target.id === 'chatForm') {
                                e.preventDefault();

                                const form = e.target;
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
                            }
                        });
                        let lastFeedbackId = {{ $feedbacks->last()->id ?? 0 }};

                        function fetchNewFeedbacks() 
                        {
                            fetch("{{ route('agent.feedback.list', $ici->ticket_no) }}")
                            .then(response => response.json())
                            .then(data => {
                                const chatBox = document.getElementById('chatScrollArea');

                                data.forEach(fb => {
                                    if (fb.id > lastFeedbackId) {
                                        const isMe = fb.name === "{{ auth()->user()->name }}";

                                        const bubble = document.createElement('div');
                                        bubble.classList.add('d-flex', isMe ? 'justify-content-end' : 'justify-content-start', 'mb-3');

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
                        setInterval(fetchNewFeedbacks, 5000);
                    </script>
                @endif
            </div>
        </div>
    </div>
</section>

<footer class="text-center py-4 bg-dark text-white mt-5">
    <p class="mb-0">&copy; {{ date('Y') }} PEL Portal. All rights reserved.</p>
</footer>

@endsection
