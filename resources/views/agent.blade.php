@extends('layouts.app')

@section('title', 'Regristration Form')

@section('meta')
    @vite('resources/css/agent.css')
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
                                    placeholder="000000-000000" value="{{ $ici->coms->complaint_number ?? '' }}">
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

            <!-- Complaint History Accordion -->
            <div class="accordion mt-4" id="complaintHistoryAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="historyHeading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#historyCollapse" aria-expanded="false" aria-controls="historyCollapse">
                            <i class="bi bi-clock-history me-2"></i>
                            Complaint History
                        </button>
                    </h2>
                    <div id="historyCollapse" class="accordion-collapse collapse" aria-labelledby="historyHeading" data-bs-parent="#complaintHistoryAccordion">
                        <div class="accordion-body">
                            <div id="historyContent">
                                <p class="text-muted">No history available. Search for a complaint to see history.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Ticket Status Information -->
            <div id="ticketStatusInfo" class="alert alert-info" style="display: none;">
                <div id="ticketStatusContent"></div>
            </div>

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
                        <input type="text" name="ticket_number" class="form-control" required readonly
                            placeholder="00-0000"
                            onblur="this.value = this.value.trim();">
                        <small class="text-muted">Ticket number will be generated automatically</small>
                    </div>
                    <div class="col-md-6">
                        <label for="service_center" class="form-label fw-semibold">Service Center</label>
                        <select name="service_center" id="service_center" class="form-control" required>
                            <option value="">-- Select Service Center --</option>
                            @foreach ($serviceCenters as $center)
                                <option value="{{ $center->sc }}">{{ $center->sc }}</option>
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
                        <label class="form-label fw-semibold">Case Status</label>
                        <select name="case_status" id="case_status" class="form-select" required>
                            <option value="">-- Select Case Status --</option>
                            <option value="In Progress" selected>In Progress</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="Hold PNA">Hold PNA</option>
                            <option value="Sales Return">Sales Return</option>
                            <option value="Pending from Customer">Pending from Customer</option>
                            <option value="NHC">NHC</option>
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
                    <button type="submit" class="btn btn-primary px-5" id="submitBtn">Submit</button>
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
    const ticketNoInput = document.querySelector('input[name="ticket_number"]');

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
                // Handle response
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

                // Check if complaint number exists and handle accordingly
                fetch('{{ route("check.complaint.ticket") }}', {
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
                .then(response => response.json())
                .then(ticketData => {
                    if (ticketData.success) {
                        // Set the ticket number (existing or new)
                        const ticketInput = document.querySelector('input[name="ticket_number"]');
                        ticketInput.value = ticketData.ticket_number;
                        ticketInput.style.borderColor = '#28a745';
                        ticketInput.title = ticketData.exists ? 'Existing ticket number' : (ticketData.is_new_ticket ? 'Auto-generated ticket number' : 'Ticket number');

                        // Update submit button text based on scenario
                        const submitBtn = document.getElementById('submitBtn');
                        if (ticketData.exists) {
                            submitBtn.textContent = 'Update Ticket';
                            submitBtn.className = 'btn btn-warning px-5';
                        } else if (ticketData.is_new_ticket) {
                            submitBtn.textContent = 'Create Ticket';
                            submitBtn.className = 'btn btn-primary px-5';
                        }

                        // Store next escalation for later use
                        let nextEscalation = ticketData.next_escalation;

                        // Set the escalation level (only for existing tickets) - show current level
                        if (ticketData.exists && ticketData.ticket_data) {
                            const escalationSelect = document.querySelector('select[name="escalation_level"]');
                            if (escalationSelect) {
                                // Show the current escalation level from database
                                escalationSelect.value = ticketData.ticket_data.escalation_level;

                                // Add visual indicator for escalation level progression
                                escalationSelect.style.borderColor = '#17a2b8';
                                escalationSelect.title = 'Current: ' + ticketData.current_escalation + ' → Will update to: ' + ticketData.next_escalation + ' (if case status changes to In Progress)';
                            }
                        }

                        // Show status information
                        const statusInfo = document.getElementById('ticketStatusInfo');
                        const statusContent = document.getElementById('ticketStatusContent');

                        if (ticketData.exists) {
                            // Existing ticket - show information with escalation progression
                            statusInfo.className = 'alert alert-info';
                            statusContent.innerHTML = `
                                <strong>📋 Existing Ticket Found</strong><br>
                                <small>Ticket <strong>${ticketData.ticket_number}</strong> found for this complaint number.</small><br>
                                <small>Current: <strong>${ticketData.current_escalation}</strong> → Will update to: <strong>${ticketData.next_escalation}</strong></small><br>
                            `;
                            statusInfo.style.display = 'block';

                            // Populate form fields with existing data (only for existing tickets)
                            if (ticketData.ticket_data) {
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
                                    document.querySelector('textarea[name="voice_of_customer"]').value = ticketData.ticket_data.voice_of_customer;
                                }

                                // Set escalation level to current database value (not next level)
                                const escalationSelect = document.querySelector('select[name="escalation_level"]');
                                if (escalationSelect && ticketData.ticket_data.escalation_level) {
                                    escalationSelect.value = ticketData.ticket_data.escalation_level;
                                    escalationSelect.style.borderColor = '';
                                    escalationSelect.title = `Current escalation level: ${ticketData.ticket_data.escalation_level}`;
                                }
                            }

                            // Disable non-editable fields for existing tickets
                            document.getElementById('service_center').disabled = true;
                            document.getElementById('complaint_category').disabled = true;
                            document.getElementById('reason_of_escalation').disabled = true;
                            // agent_name is already readonly for agents, but ensure it's disabled if not
                            const agentNameInput = document.getElementById('agent_name');
                            if (agentNameInput) {
                                agentNameInput.disabled = true;
                            }

                        } else if (ticketData.is_new_ticket) {
                            // New ticket - show creation information
                            statusInfo.className = 'alert alert-success';
                            statusContent.innerHTML = `
                                <strong>✅ New Ticket Generated</strong><br>
                                <small>Ticket <strong>${ticketData.ticket_number}</strong> generated successfully for this complaint.</small><br>
                                <small>Ready for form submission.</small>
                            `;
                            statusInfo.style.display = 'block';

                        } else {
                            // Handle actual errors
                            alert('Error: ' + (ticketData.error || ticketData.message));
                            return;
                        }

                        // Add event listener for case status change to update escalation level
                        const caseStatusSelect = document.getElementById('case_status');
                        const escalationSelect = document.querySelector('select[name="escalation_level"]');
                        if (caseStatusSelect && escalationSelect) {
                            caseStatusSelect.addEventListener('change', function() {
                                if (this.value === 'In Progress' && nextEscalation) {
                                    escalationSelect.value = nextEscalation;
                                    escalationSelect.style.borderColor = '#ffc107';
                                    escalationSelect.title = `Updated to: ${nextEscalation} (due to In Progress status)`;
                                } else {
                                    // For all other case statuses, reset escalation level to current database value
                                    if (ticketData.exists && ticketData.ticket_data && ticketData.ticket_data.escalation_level) {
                                        escalationSelect.value = ticketData.ticket_data.escalation_level;
                                        escalationSelect.style.borderColor = '';
                                        escalationSelect.title = `Current escalation level: ${ticketData.ticket_data.escalation_level}`;
                                    }
                                }
                            });
                        }

                        // Store original escalation level for comparison
                        window.originalEscalationLevel = ticketData.exists && ticketData.ticket_data ? ticketData.ticket_data.escalation_level : 'Low';

                        // Set focus to service center field (or case_status for existing)
                        setTimeout(() => {
                            if (ticketData.exists) {
                                document.getElementById('case_status').focus();
                            } else {
                                document.getElementById('service_center').focus();
                            }
                        }, 100);
                    } else {
                        alert('Error: ' + ticketData.error);
                    }
                })
                .catch(error => {
                    alert('Failed to process complaint ticket. Please try again.');
                });

                // Store complaint number for form submission
                window.complaintNumber = complaintNumber;

                // Immediately set the hidden field value (for both new and existing tickets)
                const complaintNumberHidden = document.getElementById('complaint_number_hidden');
                if (complaintNumberHidden) {
                    complaintNumberHidden.value = complaintNumber;
                }

                // Populate form fields with the fetched data
                if (data.JobNo) document.getElementById('job_number').value = data.JobNo;
                if (data.COMSComplaintDate || data.ComplaintDate) {
                    // Extract date part from datetime string (format: 2025-01-13T09:10:07 or 2025-01-13 00:00:00)
                    const complaintDateRaw = data.COMSComplaintDate || data.ComplaintDate;
                    let dateStr = complaintDateRaw;
                    if (complaintDateRaw.includes('T')) {
                        dateStr = complaintDateRaw.split('T')[0];
                    } else if (complaintDateRaw.includes(' ')) {
                        dateStr = complaintDateRaw.split(' ')[0];
                    }
                    document.getElementById('coms_complaint_date').value = dateStr;
                }
                if (data.JobType) document.getElementById('job_type').value = data.JobType;
                if (data.CustomerName) document.getElementById('customer_name').value = data.CustomerName;
                if (data.ContactNo) document.getElementById('contact_no').value = data.ContactNo;
                if (data.TCN_NAME || data.TechnicianName) {
                    const techName = data.TCN_NAME || data.TechnicianName;
                    document.getElementById('technician_name').value = techName;
                }
                if (data.DateofPurchase || data.PurchaseDate) {
                    // Extract date part from datetime string
                    const purchaseDateRaw = data.DateofPurchase || data.PurchaseDate;
                    let purchaseDateStr = purchaseDateRaw;
                    if (purchaseDateRaw.includes('T')) {
                        purchaseDateStr = purchaseDateRaw.split('T')[0];
                    } else if (purchaseDateRaw.includes(' ')) {
                        purchaseDateStr = purchaseDateRaw.split(' ')[0];
                    }
                    document.getElementById('purchase_date').value = purchaseDateStr;
                }
                if (data.Product) document.getElementById('product').value = data.Product;
                if (data.JobStatus) document.getElementById('job_status').value = data.JobStatus;
                if (data.Problem) document.getElementById('problem').value = data.Problem;
                if (data.WorkDone) document.getElementById('workdone').value = data.WorkDone;

                // Fields populated successfully

                // Fetch and display complaint history for the contact number
                if (data.ContactNo) {
                    fetchComplaintHistory(data.ContactNo);
                }

            })
            .catch(error => {
                // Handle fetch error
                // Reset button state
                searchComplaintBtn.innerHTML = '<i class="bi bi-search"></i>';
                searchComplaintBtn.disabled = false;

                alert('Failed to fetch COMS data. Please check the complaint number and try again.');
            });
        });
    }

    // Handle form submission and reset for next use
    const form = document.querySelector('form[action*="agent.store"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const complaintNumberHidden = document.getElementById('complaint_number_hidden');

            // Final verification before submission
            if (complaintNumberHidden && window.complaintNumber) {
                complaintNumberHidden.value = window.complaintNumber;
            }

            // Re-enable disabled fields for form submission (so they get included in POST data)
            const disabledFields = ['service_center', 'complaint_category', 'reason_of_escalation', 'agent_name'];
            disabledFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field && field.disabled) {
                    field.disabled = false;
                }
            });

            // Show loading state on submit button
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
                submitBtn.disabled = true;
            }
        });
    }

    // Function to fetch and display complaint history
    function fetchComplaintHistory(contactNo) {
        // Fetch complaint history

        fetch('{{ route("complaint.history") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                contact_no: contactNo
            })
        })
        .then(response => response.json())
        .then(data => {
            const historyContent = document.getElementById('historyContent');

            if (data.error) {
                historyContent.innerHTML = '<p class="text-danger">Error loading history: ' + data.error + '</p>';
                return;
            }

            if (!data.history || data.history.length === 0) {
                historyContent.innerHTML = '<p class="text-muted">No previous complaints found for this contact number.</p>';
                return;
            }

            // Build history HTML
            let historyHtml = '<div class="table-responsive"><table class="table table-striped table-sm">';
            historyHtml += '<thead><tr>';
            historyHtml += '<th>Complaint #</th>';
            historyHtml += '<th>Job #</th>';
            historyHtml += '<th>Date</th>';
            historyHtml += '<th>Product</th>';
            historyHtml += '<th>Status</th>';
            historyHtml += '<th>Ticket #</th>';
            historyHtml += '<th>Case Status</th>';
            historyHtml += '</tr></thead><tbody>';

            data.history.forEach(item => {
                historyHtml += '<tr>';
                historyHtml += '<td>' + (item.complaint_number || 'N/A') + '</td>';
                historyHtml += '<td>' + (item.job || 'N/A') + '</td>';
                historyHtml += '<td>' + (item.coms_complaint_date || 'N/A') + '</td>';
                historyHtml += '<td>' + (item.product || 'N/A') + '</td>';
                historyHtml += '<td>' + (item.job_status || 'N/A') + '</td>';
                historyHtml += '<td>' + (item.ticket_number || 'N/A') + '</td>';
                historyHtml += '<td>' + (item.case_status || 'N/A') + '</td>';
                historyHtml += '</tr>';
            });

            historyHtml += '</tbody></table></div>';
            historyHtml += '<small class="text-muted">Showing ' + data.history.length + ' complaint(s) for this contact number.</small>';

            historyContent.innerHTML = historyHtml;

            // Auto-expand the accordion if there's history
            const historyCollapse = document.getElementById('historyCollapse');
            if (historyCollapse && data.history.length > 0) {
                const bsCollapse = new bootstrap.Collapse(historyCollapse, {
                    show: true
                });
            }
        })
        .catch(error => {
            // Handle history fetch error
            const historyContent = document.getElementById('historyContent');
            historyContent.innerHTML = '<p class="text-danger">Failed to load complaint history.</p>';
        });
    }

    // Reset form after successful page reload (for new complaint entry)
    @if(session('success'))
        // Reset form for next complaint entry
        setTimeout(() => {
            // Clear COMS form fields
            document.getElementById('complaint_number').value = '';
            document.getElementById('job_number').value = '';
            document.getElementById('coms_complaint_date').value = '';
            document.getElementById('job_type').value = '';
            document.getElementById('customer_name').value = '';
            document.getElementById('contact_no').value = '';
            document.getElementById('technician_name').value = '';
            document.getElementById('purchase_date').value = '';
            document.getElementById('product').value = '';
            document.getElementById('job_status').value = '';
            document.getElementById('problem').value = '';
            document.getElementById('workdone').value = '';

            // Clear ticket number and reset to readonly
            const ticketInput = document.querySelector('input[name="ticket_number"]');
            if (ticketInput) {
                ticketInput.value = '';
                ticketInput.readOnly = true;
                ticketInput.placeholder = 'Will be auto-generated when complaint is searched';
                ticketInput.style.borderColor = '';
            }

            // Reset escalation level to Low
            document.querySelector('select[name="escalation_level"]').value = 'Low';

            // Reset submit button
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.textContent = 'Submit';
                submitBtn.className = 'btn btn-primary px-5';
                submitBtn.disabled = false;
            }

            // Hide status info
            const statusInfo = document.getElementById('ticketStatusInfo');
            if (statusInfo) {
                statusInfo.style.display = 'none';
            }

            // Clear stored complaint number
            window.complaintNumber = null;

            // Reset history accordion
            const historyContent = document.getElementById('historyContent');
            if (historyContent) {
                historyContent.innerHTML = '<p class="text-muted">No history available. Search for a complaint to see history.</p>';
            }
            const historyCollapse = document.getElementById('historyCollapse');
            if (historyCollapse) {
                const bsCollapse = bootstrap.Collapse.getInstance(historyCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            }

        }, 1000);
    @endif
});
</script>
@endsection

