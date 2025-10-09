window.lastFeedbackId = parseInt(document.querySelector('meta[name="last-feedback-id"]')?.content || '0', 10);
let isPolling = false;

function pollNewMessages() {
    if (isPolling) return;
    if (!window.feedbackListUrl) return;
    isPolling = true;

    fetch(`${window.feedbackListUrl}?last_id=${window.lastFeedbackId}`)
        .then(response => response.json())
        .then(data => {
            // Support both formats:
            // 1) Array of feedbacks: [ {id, name, role, message, time} ]
            // 2) Object with feedbacks property: { feedbacks: [ ... ] }
            const list = Array.isArray(data) ? data : (Array.isArray(data.feedbacks) ? data.feedbacks : []);

            if (list.length > 0) {
                // Only take new messages
                const newOnes = list.filter(fb => Number(fb.id) > Number(window.lastFeedbackId));

                if (newOnes.length > 0) {
                    appendNewMessages(newOnes);
                    // Advance lastFeedbackId to max seen
                    window.lastFeedbackId = Math.max(Number(window.lastFeedbackId), ...newOnes.map(fb => Number(fb.id)));
                    // Scroll to bottom if needed
                    scrollToBottom();
                }
            }
        })
        .catch(error => {})
        .finally(() => {
            isPolling = false;
            window.feedbackPollTimeoutId = setTimeout(pollNewMessages, 36000); // Poll every 5 seconds
        });
}

function appendNewMessages(feedbacks) {
    const chatArea = document.getElementById('chatScrollArea');
    
    feedbacks.forEach(feedback => {
        const isMe = feedback.name === currentUser;
        const messageHtml = createMessageElement(feedback, isMe);
        chatArea.insertAdjacentHTML('beforeend', messageHtml);
    });
}

function createMessageElement(feedback, isMe) {
    const bgColor = isMe ? '#d1e7dd' : '#e2e3e5';
    const alignment = isMe ? 'justify-content-end' : 'justify-content-start';
    // Backend may send either `time` (formatted string) or `created_at` (ISO/date)
    const displayTime = feedback.time
        ? feedback.time
        : (feedback.created_at ? new Date(feedback.created_at).toLocaleString() : '');

    return `
        <div class="d-flex ${alignment} mb-3">
            <div class="p-2 rounded-3" style="max-width: 70%; background-color: ${bgColor}">
                <div class="fw-semibold mb-1">
                    ${isMe ? 'You' : feedback.name}
                    <span class="text-muted">(${feedback.role})</span>
                </div>
                <div>${feedback.message}</div>
                <div class="mt-1">
                    <small class="text-muted">${displayTime}</small>
                </div>
            </div>
        </div>
    `;
}

function scrollToBottom() {
    const chatArea = document.getElementById('chatScrollArea');
    chatArea.scrollTop = chatArea.scrollHeight;
}

// Reconfigure chat variables from #chatConfig if present
function reconfigureChatFromDOM() {
    const cfg = document.getElementById('chatConfig');
    if (!cfg) {
        return false;
    }
    window.feedbackListUrl = cfg.dataset.feedbackListUrl || window.feedbackListUrl;
    window.currentUser = cfg.dataset.currentUser || window.currentUser;
    window.agentIndexUrl = cfg.dataset.agentIndexUrl || window.agentIndexUrl;
    const id = parseInt(cfg.dataset.lastFeedbackId || '0', 10);
    if (!Number.isNaN(id)) {
        window.lastFeedbackId = id;
    }
    return true;
}

// Start or restart polling interval safely
function startFeedbackPolling() {
    if (!window.feedbackListUrl) {
        return;
    }
    if (window.feedbackInterval) {
        clearInterval(window.feedbackInterval);
        window.feedbackInterval = null;
    }
    if (window.feedbackPollTimeoutId) {
        clearTimeout(window.feedbackPollTimeoutId);
        window.feedbackPollTimeoutId = null;
    }
    pollNewMessages();
    scrollToBottom();
}

// Only start the interval if feedbackListUrl is set, else try to read it from DOM
if (!window.feedbackListUrl) {
    reconfigureChatFromDOM();
}
if (window.feedbackListUrl) {
    startFeedbackPolling();
}
function attachChatFormListener() {
    const chatForm = document.getElementById('chatForm');
    if (chatForm && !window.chatFormListenerAttached) {

        chatForm.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (window.isSubmittingChat) {
                return;
            }

            window.isSubmittingChat = true;

            const form = this;
            const submitBtn = form.querySelector('button');
            const chatInput = document.getElementById('chatMessage');

            const formData = new FormData(form);

            // Disable input and button to prevent multiple submissions
            submitBtn.disabled = true;
            chatInput.disabled = true;

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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

                        // Update lastFeedbackId to prevent duplicate from polling
                        if (data.id) {
                            window.lastFeedbackId = data.id;
                        }

                        // Reset input
                        document.getElementById('chatMessage').value = '';
                    } else if (data.error) {
                        alert(data.error);
                    }
                })
                .catch(err => console.error(err))
                .finally(() => {
                    window.isSubmittingChat = false;
                    submitBtn.disabled = false;
                    chatInput.disabled = false;
                });
        });

        window.chatFormListenerAttached = true;
    }
}

// Attach on load
attachChatFormListener();
// Get lastFeedbackId from meta tag, default to 0 if not present
const lastFeedbackIdMeta = document.querySelector('meta[name="last-feedback-id"]');


// Auto-scroll on load
function goBackToSearch() {
    if (!window.agentIndexUrl) {
        return;
    }
    
    fetch(window.agentIndexUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(response => response.text())
        .then(html => {
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('#chatArea');
            document.getElementById('chatArea').innerHTML = newContent.innerHTML;

            // Re-attach chat form listener to the new form
            window.chatFormListenerAttached = false;
            attachChatFormListener();

            // Clear timers when going back to search
            if (window.feedbackInterval) {
                clearInterval(window.feedbackInterval);
                window.feedbackInterval = null;
            }
            if (window.feedbackPollTimeoutId) {
                clearTimeout(window.feedbackPollTimeoutId);
                window.feedbackPollTimeoutId = null;
            }

            // Reset URL so reload doesn't reopen the previous ticket
            try { history.replaceState(null, '', '/home-agent'); } catch (e) {}
        });
}
// Track if event listener is already attached
if (!window.ticketSearchFormListenerAttached) {
    
    document.addEventListener('submit', function (e) {
        if (e.target && e.target.id === 'ticketSearchForm') {
            e.preventDefault(); // stop full reload
            

            const form = e.target;
            const formData = new FormData(form);
            const rawTicketNo = (formData.get('ticket_no') || '').trim();
            if (!rawTicketNo) {
                alert('Please enter Ticket No');
                return;
            }
            const url = `/t-agent/ticket/${encodeURIComponent(rawTicketNo)}`;

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.text())
                .then(html => {

                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.querySelector('#chatArea');
                    document.getElementById('chatArea').innerHTML = newContent.innerHTML;

                    // Reconfigure and restart polling for the newly loaded ticket
                    reconfigureChatFromDOM();
                    startFeedbackPolling();

                    // Re-attach chat form listener to the new form
                    window.chatFormListenerAttached = false;
                    attachChatFormListener();

                    // Ensure URL is reset so page reload doesn't auto-open last ticket
                    if (window.location.pathname !== '/home-agent') {
                        try { history.replaceState(null, '', '/home-agent'); } catch (e) {}
                    } else if (window.location.search) {
                        try { history.replaceState(null, '', '/home-agent'); } catch (e) {}
                    }
                });
        }
    });
    
    window.ticketSearchFormListenerAttached = true;
}
window.addEventListener('load', function () {
    var box = document.getElementById('chatScrollArea');
    if (box) { box.scrollTop = box.scrollHeight; }
});