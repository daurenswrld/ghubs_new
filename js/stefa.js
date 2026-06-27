/**
 * STEFA AI Assistant - Gymnastics Hub
 * Handling chat UI, logic and automated responses
 */

const StefaAI = {
    isOpen: false,
    suggestions: [
        { text: '🏆 Найти турнир', action: 'search_tournament' },
        { text: '⛺ Найти сборы', action: 'search_camp' },
        { text: '🎓 Найти семинар', action: 'search_seminar' },
        { text: '➕ Добавить мероприятие', action: 'add_event' },
        { text: '❓ Вопрос о сайте', action: 'site_question' }
    ],

    init() {
        this.injectHTML();
        this.bindEvents();
        console.log('Stefa AI Initialized');
    },

    injectHTML() {
        const chatHTML = `
            <div class="ai-chat-window" id="stefaChat">
                <div class="ai-chat-window__header">
                    <div class="stefa-avatar">
                        <img src="${window.themeData?.templateUri || ''}/img/ai-btn.png" alt="Стефа">
                    </div>
                    <div class="stefa-info">
                        <span class="name">Стефа</span>
                        <span class="status">В сети</span>
                    </div>
                    <button class="close-chat" id="closeStefa">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="ai-chat-window__messages" id="stefaMessages" data-lenis-prevent>
                    <div class="message message--stefa">
                        Привет! Я Стефа 👋 Ваш персональный помощник в мире гимнастики. Чем я могу вам помочь сегодня?
                    </div>
                    <div class="quick-suggestions" id="stefaSuggestions" data-lenis-prevent>
                        ${this.suggestions.map(s => `<button class="suggest-btn" data-action="${s.action}">${s.text}</button>`).join('')}
                    </div>
                </div>
                <div class="ai-chat-window__input-area">
                    <div class="input-wrapper">
                        <input type="text" id="stefaInput" placeholder="Напишите сообщение...">
                        <button class="send-btn" id="stefaSend">
                             <img src="${window.themeData?.templateUri || ''}/img/arrow-up-right.svg" alt="Send">
                        </button>
                    </div>
                </div>
            </div>
        `;

        let container = document.querySelector('.ai-widget-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'ai-widget-container';
            container.innerHTML = `<a href="#" class="ai-widget" id="stefaTrigger"><img src="${window.themeData?.templateUri || ''}/img/ai-btn.png" alt="AI"></a>`;
            document.body.appendChild(container);
        } else {
            const trigger = container.querySelector('.ai-widget');
            if (trigger) trigger.id = 'stefaTrigger';
        }
        
        container.insertAdjacentHTML('afterbegin', chatHTML);
    },

    bindEvents() {
        const trigger = document.getElementById('stefaTrigger');
        const closeBtn = document.getElementById('closeStefa');
        const sendBtn = document.getElementById('stefaSend');
        const input = document.getElementById('stefaInput');
        const suggestions = document.getElementById('stefaSuggestions');

        if (trigger) {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleChat();
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.toggleChat(false));
        }

        if (sendBtn) {
            sendBtn.addEventListener('click', () => this.handleSendMessage());
        }

        if (input) {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.handleSendMessage();
            });
        }

        if (suggestions) {
            suggestions.addEventListener('click', (e) => {
                if (e.target.classList.contains('suggest-btn')) {
                    const action = e.target.dataset.action;
                    const text = e.target.innerText;
                    this.handleAction(action, text);
                }
            });
        }

        const msgContainer = document.getElementById('stefaMessages');
        if (msgContainer) {
            const stopScroll = (e) => e.stopPropagation();
            msgContainer.addEventListener('wheel', stopScroll, { passive: true });
            msgContainer.addEventListener('touchmove', stopScroll, { passive: true });
        }
    },

    toggleChat(force) {
        const chatWindow = document.getElementById('stefaChat');
        const trigger = document.getElementById('stefaTrigger');
        const tooltip = document.querySelector('.ai-tooltip');
        this.isOpen = force !== undefined ? force : !this.isOpen;
        
        if (this.isOpen) {
            chatWindow.classList.add('is-active');
            if (trigger) trigger.style.display = 'none';
            if (tooltip) tooltip.style.display = 'none';
        } else {
            chatWindow.classList.remove('is-active');
            if (trigger) trigger.style.display = 'flex';
            if (tooltip) tooltip.style.display = 'block';
        }
    },

    addMessage(text, type = 'user') {
        const container = document.getElementById('stefaMessages');
        if (!container) return;
        const msg = document.createElement('div');
        msg.className = `message message--${type}`;
        msg.innerHTML = text;
        container.appendChild(msg);
        
        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 100);
    },

    handleSendMessage() {
        const input = document.getElementById('stefaInput');
        const text = input.value.trim();
        if (!text) return;

        this.addMessage(text, 'user');
        input.value = '';

        this.showTypingIndicator();

        const formData = new FormData();
        formData.append('action', 'gh_ai_chat');
        formData.append('message', text);

        fetch(window.themeData?.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            this.removeTypingIndicator();
            if (data.success) {
                this.addMessage(data.data, 'stefa');
            } else {
                this.addMessage('Простите, у меня возникли технические трудности. 😔', 'stefa');
            }
        })
        .catch(() => {
            this.removeTypingIndicator();
            this.addMessage('Связь с сервером прервалась. 🌐', 'stefa');
        });
    },

    showTypingIndicator() {
        const container = document.getElementById('stefaMessages');
        if (!container) return;
        const typing = document.createElement('div');
        typing.className = 'message message--stefa typing-indicator';
        typing.id = 'stefaTyping';
        typing.innerHTML = '<span></span><span></span><span></span>';
        container.appendChild(typing);
        container.scrollTop = container.scrollHeight;
    },

    removeTypingIndicator() {
        const typing = document.getElementById('stefaTyping');
        if (typing) typing.remove();
    },

    handleAction(action, text) {
        this.addMessage(text, 'user');
        this.showTypingIndicator();
        
        let message = text;
        switch(action) {
            case 'search_tournament': message = 'Турниры'; break;
            case 'search_camp': message = 'Сборы'; break;
            case 'search_seminar': message = 'Семинары'; break;
            case 'add_event': message = 'Добавить мероприятие'; break;
            case 'site_question': message = 'О сайте'; break;
        }

        const formData = new FormData();
        formData.append('action', 'gh_ai_chat');
        formData.append('message', message);

        fetch(window.themeData?.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            this.removeTypingIndicator();
            if (data.success) {
                this.addMessage(data.data, 'stefa');
            }
        });
    },
};

document.addEventListener('DOMContentLoaded', () => StefaAI.init());

