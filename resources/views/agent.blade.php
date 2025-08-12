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
        <form action="{{ route('agent') }}" method="POST" class="p-4 shadow rounded bg-white">
            @csrf
            <!-- Initial Customer Information -->
            <h5 class="mb-3 text-primary">Initial Customer Information</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ticket No</label>
                    <input type="text" name="ticket_no" class="form-control" required
                        onblur="this.value = this.value.trim();">
                </div>
                <div class="col-md-6">
                    <label for="service_center" class="form-label fw-semibold">Service Center</label>
                    <select name="service_center_id" id="service_center" class="form-control" required>
                        <option value="">-- Select Service Center --</option>
                        @foreach($serviceCenters as $center)
                            <option value="{{ $center->id }}">{{ $center->sc_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="complaint_date" class="form-label fw-semibold">Complaint Escalation Date</label>
                    <input type="date" 
                        class="form-control" 
                        name="complaint_date" 
                        id="complaint_date" 
                        value="{{ now()->format('Y-m-d') }}" 
                        readonly>
                </div>
                <div class="col-md-3">
                    <label for="case_status" class="form-label fw-semibold">Case Status</label>
                    <select name="case_status_id" id="case_status" class="form-control" required>
                        <option value="">-- Select Case Status --</option>
                        @foreach($caseStatus as $status)
                            <option value="{{ $status->id }}">{{ $status->status }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="complaint_category" class="form-label fw-semibold">Complaint Category</label>
                    <select name="complaint_category_id" id="complaint_category" class="form-control" required>
                        <option value="">-- Select Complaint Category --</option>
                        @foreach($complaintCategory as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">Agent Name</label>
                    @if(auth()->user()->role === 'Agent')
                        <input type="text" class="form-control" name="name" id="name" 
                            value="{{ auth()->user()->name }}" readonly>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <label for="reason_of_escalation" class="form-label fw-semibold">Reason of Escalation</label>
                    <select name="reason_of_escalation_id" id="reason_of_escalation" class="form-control" required>
                        <option value="">-- Select Reason of Escalation --</option>
                        @foreach($reasonofEscalation as $reason)
                            <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
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
                    <textarea name="voice_customer" rows="3" class="form-control"></textarea>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary px-5">Submit</button>
            </div>
        </form>
    </div>
</section>

<!-- ⚫ Footer -->
<footer class="text-center py-4 bg-dark text-white mt-5">
    <p class="mb-0">&copy; {{ date('Y') }} PEL Project Portal. All rights reserved.</p>
</footer>

@endsection
