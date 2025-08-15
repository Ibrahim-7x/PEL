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

        {{-- Flash: success --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Flash: validation errors --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>There were some problems with your submission:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- If $ici is not set, show a placeholder/help --}}
        @if(empty($ici))
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Open a Ticket Chat</h5>
                    <p class="text-muted mb-3">
                        Select or open a ticket to view and send chat-style feedback.
                    </p>
                    {{-- Optional: quick search form to open a ticket by ticket_no --}}
                    <form method="GET" action="{{ route('agent.index') }}" class="row g-2">
                        <div class="col-auto">
                            <input type="text" name="ticket_no" class="form-control" placeholder="Enter Ticket No">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary">Open</button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            {{-- Chat header --}}
            <h4 class="mb-4">
                Ticket #{{ $ici->ticket_no }}
                <small class="text-muted ms-2">Agent: {{ $ici->agent_name ?? '—' }}</small>
            </h4>

            {{-- Chat box --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body" id="chatScrollArea" style="height: 420px; overflow-y: auto; background-color: #f8f9fa;">
                    @forelse($feedbacks as $feedback)
                        @php
                            $isMe = isset(auth()->user()->name) && $feedback->name === auth()->user()->name;
                        @endphp

                        @if($isMe)
                            {{-- Right bubble (current user) --}}
                            <div class="d-flex justify-content-end mb-3">
                                <div class="p-2 rounded-3" style="max-width: 70%; background-color: #d1e7dd;">
                                    <div class="fw-semibold mb-1">You <span class="text-muted">({{ $feedback->role }})</span></div>
                                    <div>{{ $feedback->message }}</div>
                                    <div class="mt-1"><small class="text-muted">{{ $feedback->created_at->format('d M Y, h:i A') }}</small></div>
                                </div>
                            </div>
                        @else
                            {{-- Left bubble (others) --}}
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
                    <form action="{{ route('agent.feedback.store', $ici->ticket_no) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input
                            type="text"
                            name="message"
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

            {{-- Auto-scroll to bottom on page load --}}
            <script>
                window.addEventListener('load', function () {
                    var box = document.getElementById('chatScrollArea');
                    if (box) { box.scrollTop = box.scrollHeight; }
                });
            </script>
        @endif
    </div>
    </div>
</section>

<!-- ⚫ Footer -->
<footer class="text-center py-4 bg-dark text-white mt-5">
    <p class="mb-0">&copy; {{ date('Y') }} PEL Portal. All rights reserved.</p>
</footer>

@endsection
