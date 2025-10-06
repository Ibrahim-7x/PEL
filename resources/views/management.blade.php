@extends('layouts.app')

@section('title', 'Management')

@push('styles')
    <link href="{{ asset('css/managemnet.css') }}" rel="stylesheet">
@endpush

@section('content')

    <!-- 📝 RU CASE Form Section -->
    <section class="py-5 bg-light agent-page">
        <div class="container">
            <h2 class="fw-bold mb-2 text-center d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-clipboard2-check text-primary"></i>
                RU CASE
            </h2>
            <p class="text-muted text-center mb-4">Record, track and collaborate on customer escalations</p>

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
                            <input type="text" name="ticket_no" id="ticket_no" class="form-control" required>
                            <button type="button" id="searchBtn" class="btn btn-primary">
                                <i class="bi bi-search"></i> <!-- Bootstrap icon (optional) -->
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="service_center" class="form-label fw-semibold">Service Center</label>
                        <input name="service_center" id="service_center" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="complaint_escalation_date" class="form-label">Complaint Escalation Date</label>
                        <input type="date" class="form-control" name="complaint_escalation_date"
                            id="complaint_escalation_date" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="case_status" class="form-label fw-semibold">Case Status</label>
                        <input name="case_status" id="case_status" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Aging</label>
                        <input type="number" name="aging" id="aging" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="complaint_category" class="form-label fw-semibold">Complaint Category</label>
                        <input name="complaint_category" id="complaint_category" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="agent_name" class="form-label">Agent Name</label>
                        <input type="text" class="form-control" name="agent_name" id="agent_name" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="reason_of_escalation" class="form-label fw-semibold">Reason of Escalation</label>
                        <input name="reason_of_escalation" id="reason_of_escalation" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Escalation Level</label>
                        <input name="escalation_level" id="escalation_level" class="form-control" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Voice of Customer</label>
                        <textarea name="voice_of_customer" id="voice_of_customer" rows="3" class="form-control" readonly></textarea>
                    </div>
                </div>
            </form>

            <script>
                document.getElementById("searchBtn").addEventListener("click", function() {
                    let ticketNo = document.getElementById("ticket_no").value;

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
                            }
                        });
                });
            </script>

            <hr class="my-4">

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
                            <input type="date" name="coms_complaint_date" id="coms_complaint_date"
                                class="form-control" readonly>
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
                            <input type="text" name="technician_name" id="technician_name" class="form-control"
                                readonly>
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
        </div>
        <script>
            function goBackToSearch() {
                fetch("{{ route('management.index') }}")
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
            window.addEventListener('load', function() {
                var box = document.getElementById('chatScrollArea');
                if (box) {
                    box.scrollTop = box.scrollHeight;
                }
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
            {{-- lastFeedbackId initialization moved into the chat section to avoid undefined variable when $feedbacks is not set --}}
        </script>
    </section>

    <!-- ⚫ Footer -->
    <footer class="text-center py-4 bg-dark text-white mt-5">
        <p class="mb-0">&copy; {{ date('Y') }} PEL. All rights reserved.</p>
    </footer>

@endsection

@section('scripts')
    <script src="{{ asset('js/script.js') }}"></script>
@endsection
