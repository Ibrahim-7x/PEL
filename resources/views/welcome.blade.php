@extends('layouts.app')

@section('content')
<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h1 style="color: #2c3e50; font-size: 2rem; margin-bottom: 10px;">Welcome to PEL-Abacus</h1>
    <p style="color: #34495e; font-size: 1.1rem;">Your comprehensive solution for customer relationship management and case tracking</p>
</div>

<!-- Mention Notifications -->
<div id="mentionNotifications" class="container mt-3" style="display: none;">
    <div id="mentionToast" class="alert alert-primary alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-at me-2"></i>
            <span id="mentionMessage"></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
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
                const mention = data.mentions[0]; // Show the most recent mention
                showMentionNotification(mention);
            }
        })
        .catch(error => {
            console.error('Error checking mentions:', error);
        });
    }

    function showMentionNotification(mention) {
        const mentionToast = document.getElementById('mentionToast');
        const mentionMessage = document.getElementById('mentionMessage');
        const mentionNotifications = document.getElementById('mentionNotifications');

        mentionMessage.innerHTML = `<strong>${mention.mentioner_name}</strong> mentioned you in ticket <strong>${mention.ticket_no}</strong>: "${mention.message}"`;

        // Make the notification clickable to redirect to the ticket
        mentionToast.style.cursor = 'pointer';
        mentionToast.onclick = function() {
            // Mark as read first
            fetch(`{{ url('/mentions') }}/${mention.id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(() => {
                // Redirect to the ticket page
                const role = '{{ auth()->user()->role }}';
                const routeName = role === 'Management' ? 't_management.index' : 't_agent.index';
                window.location.href = `{{ url('/') }}/${role === 'Management' ? 't-management' : 't-agent'}?ticket_no=${mention.ticket_no}`;
            });
        };

        mentionNotifications.style.display = 'block';

        // Scroll to notification
        mentionNotifications.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Check for mentions every 30 seconds
    mentionCheckInterval = setInterval(checkMentions, 30000);

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