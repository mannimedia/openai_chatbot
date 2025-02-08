class ChatBot {
    constructor() {
        this.chatbot = document.querySelector('.tx-openai-chatbot');
        if (!this.chatbot) return;

        this.threadId = null; // Speichert die Thread-ID für fortlaufende Konversationen
        this.initElements();
        this.initEventListeners();
        this.isProcessing = false;
    }

    initElements() {
        this.chatButton = this.chatbot.querySelector('.chat-button button');
        this.chatWindow = this.chatbot.querySelector('.chat-window');
        this.closeButton = this.chatbot.querySelector('.chat-close');
        this.sendButton = this.chatbot.querySelector('.send-message');
        this.textarea = this.chatbot.querySelector('textarea');
        this.messagesContainer = this.chatbot.querySelector('.chat-messages');
        this.ajaxUrl = this.chatbot.dataset.ajaxUrl;
    }

    initEventListeners() {
        this.chatButton?.addEventListener('click', () => this.toggleChat());
        this.closeButton?.addEventListener('click', () => this.toggleChat());
        this.sendButton?.addEventListener('click', () => this.handleSendMessage());

        this.textarea?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.handleSendMessage();
            }
        });

        this.textarea?.addEventListener('input', () => this.adjustTextareaHeight());
    }

    toggleChat() {
        if (this.chatWindow.style.display === 'none' || !this.chatWindow.style.display) {
            this.openChat();
        } else {
            this.closeChat();
        }
    }

    openChat() {
        this.chatWindow.style.display = 'flex';
        setTimeout(() => {
            this.chatWindow.classList.add('visible');
            this.textarea?.focus();
        }, 10);
    }

    closeChat() {
        this.chatWindow.classList.remove('visible');
        setTimeout(() => {
            this.chatWindow.style.display = 'none';
        }, 300);
    }

    adjustTextareaHeight() {
        this.textarea.style.height = 'auto';
        this.textarea.style.height = `${this.textarea.scrollHeight}px`;
    }

    addLoadingIndicator() {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'message assistant loading';
        loadingDiv.innerHTML = `
            <div class="message-content">
                <div class="loading-dots">
                    <span></span><span></span><span></span>
                </div>
                <div class="typing-indicator">Assistant schreibt...</div>
            </div>
        `;
        this.messagesContainer.appendChild(loadingDiv);
        this.scrollToBottom();
        return loadingDiv;
    }

    addMessage(type, content) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;

        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.textContent = content;

        messageDiv.appendChild(contentDiv);
        this.messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
    }

    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }

    async handleSendMessage() {
        if (this.isProcessing) return;

        const message = this.textarea.value.trim();
        if (!message) return;

        this.isProcessing = true;
        this.addMessage('user', message);
        this.textarea.value = '';
        this.textarea.style.height = 'auto';

        const loadingIndicator = this.addLoadingIndicator();

        try {
            const formData = new FormData();
            formData.append('tx_openaichatbot_chat[message]', message);
            if (this.threadId !== null) {
                formData.append('tx_openaichatbot_chat[threadId]', this.threadId);
            }

            const response = await fetch(this.ajaxUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.threadId = data.response.threadId;
                // Verwende die neue formatierte Nachrichtenausgabe
                this.addFormattedMessage('assistant', data.response.message);
            } else {
                this.addMessage('error', data.error || 'Es ist ein Fehler aufgetreten.');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.addMessage('error', 'Es ist ein Fehler aufgetreten.');
        } finally {
            loadingIndicator.remove();
            this.isProcessing = false;
        }
    }

// Neue Methode für formatierte Nachrichten
    addFormattedMessage(type, content) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;

        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';

        // Formatiere den Inhalt
        const formattedContent = this.formatMessageContent(content);
        contentDiv.innerHTML = formattedContent; // Verwende innerHTML statt textContent

        messageDiv.appendChild(contentDiv);
        this.messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
    }

    formatMessageContent(content) {
        if (!content) return '';

        // Ersetze Zeilenumbrüche mit <br>
        let formatted = content.replace(/\n\n/g, '<br><br>');

        // Formatiere fett gedruckte Texte
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

        // Formatiere Links - [Text](URL)
        formatted = formatted.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');

        // Formatiere nummerierte Listen
        formatted = formatted.replace(/(\d+\.\s)(.*?)(?=\n|$)/g, (match, number, text) => {
            return `${number}<strong>${text}</strong><br>`;
        });

        return formatted;
    }
}

// Initialisierung des Chatbots, wenn das DOM vollständig geladen ist
document.addEventListener('DOMContentLoaded', () => {
    new ChatBot();
});