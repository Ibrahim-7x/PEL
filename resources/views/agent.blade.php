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
                                <input type="text" id="complaint_number" class="form-control"
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
                <input type="hidden" name="complaint_number_hidden" id="complaint_number_hidden" value="">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0 text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-person-vcard"></i> Initial Customer Information
                    </h5>
                    <span class="badge rounded-pill bg-light text-secondary">Step 1</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Ticket No
                            <span class="text-muted">(Auto-generated)</span>
                        </label>
                        <input type="text" name="ticket_no" class="form-control" required readonly
                            placeholder="00-0000"
                            onblur="this.value = this.value.trim();">
                        <small class="text-muted">Ticket number will be generated automatically</small>
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
                                <option value="{{ $status->status }}" {{ $status->status == 'In-Progress' ? 'selected' : '' }}>{{ $status->status }}</option>
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
                            <option value="Low" selected>Low</option>
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchComplaintBtn = document.getElementById('searchComplaintBtn');
    const complaintNumberInput = document.getElementById('complaint_number');
    const ticketNoInput = document.querySelector('input[name="ticket_no"]');

    if (searchComplaintBtn && complaintNumberInput) {
        searchComplaintBtn.addEventListener('click', function() {
            const complaintNumber = complaintNumberInput.value.trim();

            if (!complaintNumber) {
                alert('Please enter a complaint number');
                return;
            }

            // Show loading state
            searchComplaintBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
            searchComplaintBtn.disabled = true;

            // Make AJAX request to fetch COMS data
            fetch('{{ route("fetch.coms") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    complaint_number: complaintNumber
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Reset button state
                searchComplaintBtn.innerHTML = '<i class="bi bi-search"></i>';
                searchComplaintBtn.disabled = false;

                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }

                // Generate and populate ticket number first
                fetch('{{ route("generate.ticket.number") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(ticketData => {
                    if (ticketData.success) {
                        // Set the generated ticket number
                        const ticketInput = document.querySelector('input[name="ticket_no"]');
                        ticketInput.value = ticketData.ticket_number;

                        // Show success message for ticket generation
                        ticketInput.style.borderColor = '#28a745';
                        ticketInput.title = 'Auto-generated ticket number';

                        // Set focus to service center field
                        setTimeout(() => {
                            document.getElementById('service_center').focus();
                        }, 100);
                    } else {
                        console.error('Failed to generate ticket number:', ticketData.error);
                        // Keep the field empty for manual entry as fallback
                        const ticketInput = document.querySelector('input[name="ticket_no"]');
                        ticketInput.readOnly = false;
                        ticketInput.placeholder = 'Enter ticket number manually';
                        ticketInput.style.borderColor = '#ffc107';
                    }
                })
                .catch(error => {
                    console.error('Error generating ticket number:', error);
                    // Fallback: allow manual entry
                    const ticketInput = document.querySelector('input[name="ticket_no"]');
                    ticketInput.readOnly = false;
                    ticketInput.placeholder = 'Enter ticket number manually';
                    ticketInput.style.borderColor = '#ffc107';
                });

                // Store complaint number for form submission
                window.complaintNumber = complaintNumber;
                console.log('Complaint number stored in window:', complaintNumber);

                // Immediately set the hidden field value
                const complaintNumberHidden = document.getElementById('complaint_number_hidden');
                if (complaintNumberHidden) {
                    complaintNumberHidden.value = complaintNumber;
                    console.log('Hidden field set immediately:', complaintNumberHidden.value);
                }

                // Populate form fields with the fetched data
                if (data.JobNo) document.getElementById('job_number').value = data.JobNo;
                if (data.JobDate) document.getElementById('coms_complaint_date').value = data.JobDate;
                if (data.JobType) document.getElementById('job_type').value = data.JobType;
                if (data.CustomerName) document.getElementById('customer_name').value = data.CustomerName;
                if (data.ContactNo) document.getElementById('contact_no').value = data.ContactNo;
                if (data.TechnicianName) document.getElementById('technician_name').value = data.TechnicianName;
                if (data.PurchaseDate) document.getElementById('purchase_date').value = data.PurchaseDate;
                if (data.Product) document.getElementById('product').value = data.Product;
                if (data.JobStatus) document.getElementById('job_status').value = data.JobStatus;
                if (data.Problem) document.getElementById('problem').value = data.Problem;
                if (data.WorkDone) document.getElementById('workdone').value = data.WorkDone;

            })
            .catch(error => {
                // Reset button state
                searchComplaintBtn.innerHTML = '<i class="bi bi-search"></i>';
                searchComplaintBtn.disabled = false;

                console.error('Error fetching COMS data:', error);
                alert('Failed to fetch COMS data. Please check the complaint number and try again.');
            });
        });
    }

    // Verify and log form submission data
    const form = document.querySelector('form[action*="agent.store"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const complaintNumberHidden = document.getElementById('complaint_number_hidden');
            console.log('Form submission - Complaint number data:', {
                hiddenFieldValue: complaintNumberHidden ? complaintNumberHidden.value : 'No hidden field',
                windowComplaintNumber: window.complaintNumber,
                formAction: form.action
            });

            // Final verification before submission
            if (complaintNumberHidden && window.complaintNumber) {
                complaintNumberHidden.value = window.complaintNumber;
                console.log('Final hidden field value set to:', complaintNumberHidden.value);
            }
        });
    }
});
</script>
@endsection

