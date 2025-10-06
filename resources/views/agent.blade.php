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
        </div>
    </section>


    <footer class="text-center py-4 bg-dark text-white mt-5">
        <p class="mb-0">&copy; {{ date('Y') }} PEL. All rights reserved.</p>
    </footer>

@endsection
