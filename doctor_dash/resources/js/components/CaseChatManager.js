/**
 * CaseChatManager Component
 * 
 * Manages case-specific chat functionality including message display,
 * sending, and real-time polling for new messages.
 * 
 * Requirements: 6.1, 6.3, 6.4, 7.1, 7.2, 7.3, 7.4, 7.5, 7.7, 9.1, 9.4, 9.5
 */
export default class CaseChatManager {
    /**
     * Initialize the case chat manager
     * 
     * @param {string} batchId - The batch ID of the case
     * @param {string} messagesUrl - URL to fetch messages
     * @param {string} sendUrl - URL to send messages
     */
    constructor(batchId, messagesUrl, sendUrl) {
        this.batchId = batchId;
        this.messagesUrl = messagesUrl;
        this.sendUrl = sendUrl;
        this.pollingInterval = null;
        this.lastMessageId = 0;
        this.init();
    }
    
    /**
     * Initialize the component by binding events and loading initial messages
     */
    init() {
        this.messageContainer = document.getElementById('case-chat-messages');
        this.messageForm = document.getElementById('case-chat-form');
        this.messageInput = document.getElementById('case-chat-input');
        
        console.log('CaseChatManager initialized:', {
            container: !!this.messageContainer,
            form: !!this.messageForm,
            input: !!this.messageInput,
            batchId: this.batchId,
            messagesUrl: this.messagesUrl,
            sendUrl: this.sendUrl
        });
        
        if (!this.messageContainer || !this.messageForm || !this.messageInput) {
            console.error('CaseChatManager: Required elements not found');
            return;
        }
        
        this.messageForm.addEventListener('submit', (e) => this.handleSend(e));

        this.messageInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.handleSend(e);
            }
        });
        
        this.loadMessages();
        this.startPolling();
    }
    
    /**
     * Load all messages from the server
     */
    async loadMessages() {
        try {
            const response = await fetch(this.messagesUrl, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.error) {
                this.showError('Failed to load messages: ' + data.error);
                return;
            }
            
            if (data.messages) {
                this.renderMessages(data.messages);
                if (data.messages.length > 0) {
                    this.lastMessageId = data.messages[data.messages.length - 1].id;
                }
            }
        } catch (error) {
            console.error('Failed to load messages:', error);
            this.showError('Unable to load messages. Please refresh the page.');
        }
    }
    
    /**
     * Handle message form submission
     * 
     * @param {Event} e - The form submit event
     */
    async handleSend(e) {
        e.preventDefault();
        
        console.log('handleSend called');
        
        const message = this.messageInput.value.trim();
        console.log('Message to send:', message);
        
        if (!message) {
            console.log('Empty message, returning');
            return;
        }
        
        // Disable input while sending
        const originalValue = this.messageInput.value;
        this.messageInput.disabled = true;
        
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                throw new Error('CSRF token not found');
            }
            
            console.log('Sending to:', this.sendUrl);
            console.log('Payload:', { message });
            
            const response = await fetch(this.sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content
                },
                body: JSON.stringify({ message })
            });
            
            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);
            
            if (!response.ok) {
                this.showError(data.error || `HTTP error! status: ${response.status}`);
                this.messageInput.value = originalValue;
            } else if (data.ok) {
                this.messageInput.value = '';
                await this.loadMessages();
            } else {
                this.showError('Failed to send message. Please try again.');
                this.messageInput.value = originalValue;
            }
        } catch (error) {
            console.error('Failed to send message:', error);
            this.showError('Unable to send message: ' + error.message);
            this.messageInput.value = originalValue;
        } finally {
            this.messageInput.disabled = false;
            this.messageInput.focus();
        }
    }
    
    /**
     * Render all messages in the message container
     * 
     * @param {Array} messages - Array of message objects
     */
    renderMessages(messages) {
        this.messageContainer.innerHTML = '';
        messages.forEach(msg => {
            const messageEl = this.createMessageElement(msg);
            this.messageContainer.appendChild(messageEl);
        });
        this.scrollToBottom();
    }
    
    /**
     * Create a DOM element for a single message
     * 
     * @param {Object} message - The message object
     * @returns {HTMLElement} The message element
     */
    createMessageElement(message) {
        const div = document.createElement('div');
        const currentUserId = document.querySelector('meta[name="user-id"]')?.content;
        const isSelf = message.is_self !== undefined ? message.is_self : (String(message.sender_id) === String(currentUserId));
        
        div.className = `flex ${isSelf ? 'justify-end' : 'justify-start'}`;
        div.innerHTML = `
            <div class="max-w-[75%] ${isSelf ? 'bg-[#FACC15] text-black' : 'bg-[#111111] border border-white/10 text-white'} rounded-2xl px-4 py-2.5 shadow-sm">
                ${!isSelf ? `<p class="text-xs font-bold text-[#FACC15] mb-1">${message.sender_name}</p>` : ''}
                <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">${this.linkify(message.body)}</p>
                <p class="text-[10px] ${isSelf ? 'text-black/60' : 'text-gray-500'} mt-1 font-medium">${message.created_at_label || message.created_at}</p>
            </div>
        `;
        return div;
    }
    
    /**
     * Convert URLs in text to clickable links
     * 
     * @param {string} text - The text to linkify
     * @returns {string} The text with URLs converted to anchor tags
     */
    linkify(text) {
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        return text.replace(urlRegex, '<a href="$1" target="_blank">$1</a>');
    }
    
    /**
     * Scroll the message container to the bottom
     */
    scrollToBottom() {
        if (this.messageContainer) {
            this.messageContainer.scrollTop = this.messageContainer.scrollHeight;
        }
    }
    
    /**
     * Start polling for new messages every 5 seconds
     */
    startPolling() {
        if (this.pollingInterval) return;
        this.pollingInterval = setInterval(() => this.pollNewMessages(), 5000);
    }
    
    /**
     * Stop polling for new messages
     */
    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }
    
    /**
     * Poll for new messages since the last message ID
     */
    async pollNewMessages() {
        try {
            const response = await fetch(`${this.messagesUrl}?since=${this.lastMessageId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    const messageEl = this.createMessageElement(msg);
                    this.messageContainer.appendChild(messageEl);
                });
                this.lastMessageId = data.messages[data.messages.length - 1].id;
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Polling failed:', error);
        }
    }
    
    /**
     * Show error message to user
     * 
     * @param {string} message - The error message to display
     */
    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-red-500/10 border border-red-500/20 rounded-xl p-4 mb-4 text-red-400 text-sm';
        errorDiv.textContent = message;
        
        if (this.messageContainer) {
            this.messageContainer.insertBefore(errorDiv, this.messageContainer.firstChild);
            setTimeout(() => errorDiv.remove(), 5000);
        }
    }
}