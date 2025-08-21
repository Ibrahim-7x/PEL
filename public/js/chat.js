// Counter for fetchNewFeedbacks calls
window.fetchNewFeedbacksCount = window.fetchNewFeedbacksCount || 0;

let lastFeedbackId = parseInt(document.querySelector('meta[name="last-feedback-id"]')?.content || '0', 10);
let isPolling = false;

function fetchNewFeedbacks() {
    window.fetchNewFeedbacksCount++;
    
    if (!window.feedbackListUrl) {
        console.error('feedbackListUrl is not set');
        return;
    }
    
    console.log('Fetching new feedbacks from:', window.feedbackListUrl, 'Call count:', window.fetchNewFeedbacksCount);
    
    fetch(window.feedbackListUrl)
        .then(response => response.json())
        .then(data => {
            console.log('Received feedback data:', data);
            
            const chatBox = document.getElementById('chatScrollArea');

            data.forEach(fb => {
                if (fb.id > lastFeedbackId) {
                    const isMe = fb.name === window.currentUser;

                    const bubble = document.createElement('div');
                    bubble.classList.add('d-flex', isMe ? 'justify-content-end' :
                        'justify-content-start', 'mb-3');

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
        .catch(err => console.error('Error fetching feedbacks:', err));
}

function pollNewMessages() {
    if (isPolling) return;
    if (!window.feedbackListUrl) return;
    isPolling = true;

    fetch(`${window.feedbackListUrl}?last_id=${lastFeedbackId}`)
        .then(response => response.json())
        .then(data => {
            // Support both formats:
            // 1) Array of feedbacks: [ {id, name, role, message, time} ]
            // 2) Object with feedbacks property: { feedbacks: [ ... ] }
            const list = Array.isArray(data) ? data : (Array.isArray(data.feedbacks) ? data.feedbacks : []);

            if (list.length > 0) {
                // Only take new messages
                const newOnes = list.filter(fb => Number(fb.id) > Number(lastFeedbackId));

                if (newOnes.length > 0) {
                    appendNewMessages(newOnes);
                    // Advance lastFeedbackId to max seen
                    lastFeedbackId = Math.max(Number(lastFeedbackId), ...newOnes.map(fb => Number(fb.id)));
                    // Scroll to bottom if needed
                    scrollToBottom();
                }
            }
        })
        .catch(error => console.error('Error polling messages:', error))
        .finally(() => {
            isPolling = false;
            window.feedbackPollTimeoutId = setTimeout(pollNewMessages, 5000); // Poll every 5 seconds
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
        console.log('chatConfig not found; skipping reconfigure');
        return false;
    }
    window.feedbackListUrl = cfg.dataset.feedbackListUrl || window.feedbackListUrl;
    window.currentUser = cfg.dataset.currentUser || window.currentUser;
    window.agentIndexUrl = cfg.dataset.agentIndexUrl || window.agentIndexUrl;
    const id = parseInt(cfg.dataset.lastFeedbackId || '0', 10);
    if (!Number.isNaN(id)) {
        lastFeedbackId = id;
    }
    console.log('Reconfigured chat from DOM:', {
        feedbackListUrl: window.feedbackListUrl,
        currentUser: window.currentUser,
        agentIndexUrl: window.agentIndexUrl,
        lastFeedbackId
    });
    return true;
}

// Start or restart polling interval safely
function startFeedbackPolling() {
    if (!window.feedbackListUrl) {
        console.warn('Cannot start polling: feedbackListUrl is not set');
        return;
    }
    if (window.feedbackInterval) {
        console.log('Clearing existing feedback interval');
        clearInterval(window.feedbackInterval);
        window.feedbackInterval = null;
    }
    if (window.feedbackPollTimeoutId) {
        console.log('Clearing existing feedback poll timeout');
        clearTimeout(window.feedbackPollTimeoutId);
        window.feedbackPollTimeoutId = null;
    }
    console.log('Starting feedback polling');
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
// Track if event listener is already attached
if (!window.chatFormListenerAttached) {
    console.log('Attaching chat form listener');
    
    document.addEventListener('submit', function (e) {
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
    
    window.chatFormListenerAttached = true;
}
// Get lastFeedbackId from meta tag, default to 0 if not present
const lastFeedbackIdMeta = document.querySelector('meta[name="last-feedback-id"]');


// Auto-scroll on load
function goBackToSearch() {
    if (!window.agentIndexUrl) {
        console.error('agentIndexUrl is not set');
        return;
    }
    
    console.log('Going back to search, agentIndexUrl:', window.agentIndexUrl);
    
    fetch(window.agentIndexUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(response => response.text())
        .then(html => {
            console.log('Received go back to search response');
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('#chatArea');
            document.getElementById('chatArea').innerHTML = newContent.innerHTML;
            
            // Clear timers when going back to search
            if (window.feedbackInterval) {
                console.log('Clearing feedback interval in goBackToSearch');
                clearInterval(window.feedbackInterval);
                window.feedbackInterval = null;
            }
            if (window.feedbackPollTimeoutId) {
                console.log('Clearing feedback poll timeout in goBackToSearch');
                clearTimeout(window.feedbackPollTimeoutId);
                window.feedbackPollTimeoutId = null;
            }

            // Reset URL so reload doesn't reopen the previous ticket
            try { history.replaceState(null, '', '/home-agent'); } catch (e) {}
        });
}
// Track if event listener is already attached
if (!window.ticketSearchFormListenerAttached) {
    console.log('Attaching ticket search form listener');
    
    document.addEventListener('submit', function (e) {
        if (e.target && e.target.id === 'ticketSearchForm') {
            e.preventDefault(); // stop full reload
            
            console.log('Ticket search form submitted');

            const form = e.target;
            const formData = new FormData(form);
            const rawTicketNo = (formData.get('ticket_no') || '').trim();
            if (!rawTicketNo) {
                alert('Please enter Ticket No');
                return;
            }
            const url = `/home-agent/ticket/${encodeURIComponent(rawTicketNo)}`;

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.text())
                .then(html => {
                    console.log('Received ticket search response');

                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.querySelector('#chatArea');
                    document.getElementById('chatArea').innerHTML = newContent.innerHTML;

                    // Reconfigure and restart polling for the newly loaded ticket
                    reconfigureChatFromDOM();
                    startFeedbackPolling();

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