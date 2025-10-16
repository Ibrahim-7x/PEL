@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h1 style="color: #2c3e50; font-size: 2rem; margin-bottom: 10px;">Welcome to PEL-Abacus</h1>
    <p style="color: #34495e; font-size: 1.1rem;">Your comprehensive solution for customer relationship management and case tracking</p>
</div>

<!-- Mention Notifications -->
<div id="mentionNotifications" class="container mt-4" style="display: none;">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="bi bi-bell-fill me-2"></i>
            <h5 class="mb-0">Notifications</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="mentionsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-semibold">Mentioned By</th>
                            <th class="border-0 fw-semibold">Ticket & Time</th>
                            <th class="border-0 fw-semibold">Message</th>
                            <th class="border-0 fw-semibold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="mentionsTableBody">
                        <!-- Mention rows will be inserted here -->
                    </tbody>
                </table>
            </div>
            <div id="noMentionsMessage" class="text-center text-muted py-4">
                <i class="bi bi-bell-slash display-4 mb-3"></i>
                <p class="mb-0">No new Notifcations</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let mentionCheckInterval;

    function checkMentions() {
        fetch('{{ route("mentions.get") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.count > 0) {
                showMentionNotification(data.mentions);
            } else {
                // Hide notifications if no mentions
                const mentionNotifications = document.getElementById('mentionNotifications');
                if (mentionNotifications) {
                    mentionNotifications.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error checking mentions:', error);
        });
    }

    function showMentionNotification(mentions) {
        const mentionNotifications = document.getElementById('mentionNotifications');
        const mentionsTableBody = document.getElementById('mentionsTableBody');
        const noMentionsMessage = document.getElementById('noMentionsMessage');

        if (mentions.length === 0) {
            mentionsTableBody.innerHTML = '';
            noMentionsMessage.style.display = 'block';
            mentionNotifications.style.display = 'none';
            return;
        }

        // Clear existing rows
        mentionsTableBody.innerHTML = '';

        // Add mention rows
        mentions.forEach(mention => {
            const row = document.createElement('tr');
            row.className = 'mention-row';

            // Format the date and time
            const mentionDate = new Date(mention.created_at);
            const formattedDate = mentionDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            row.innerHTML = `
                <td class="align-middle">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle text-primary me-2"></i>
                        <strong>${mention.mentioner_name}</strong>
                    </div>
                </td>
                <td class="align-middle">
                    <div>
                        <span class="badge bg-info">${mention.ticket_no}</span>
                        <br>
                        <small class="text-muted">${formattedDate}</small>
                    </div>
                </td>
                <td class="align-middle">
                    <div class="text-truncate" style="max-width: 300px;" title="${mention.message}">
                        ${mention.message}
                    </div>
                </td>
                <td class="align-middle text-center">
                    <button class="btn btn-primary btn-sm open-mention-btn" data-mention-id="${mention.id}" data-ticket-no="${mention.ticket_no}">
                        <i class="bi bi-eye me-1"></i>Open
                    </button>
                </td>
            `;

            mentionsTableBody.appendChild(row);
        });

        // Add event listeners to open buttons
        document.querySelectorAll('.open-mention-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const mentionId = this.getAttribute('data-mention-id');
                const ticketNo = this.getAttribute('data-ticket-no');

                // Mark as read first
                fetch(`{{ url('/mentions') }}/${mentionId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(() => {
                    // Redirect to the ticket page
                    const role = '{{ auth()->user()->role }}';
                    window.location.href = `{{ url('/') }}/${role === 'Management' ? 't-management' : 't-agent'}?ticket_no=${ticketNo}`;
                });
            });
        });

        noMentionsMessage.style.display = 'none';
        mentionNotifications.style.display = 'block';

        // Scroll to notification
        mentionNotifications.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Check for mentions every 60 seconds
    mentionCheckInterval = setInterval(checkMentions, 60000);

    // Initial check
    checkMentions();

    // Clear interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (mentionCheckInterval) {
            clearInterval(mentionCheckInterval);
        }
    });
});
</script>
@endsection