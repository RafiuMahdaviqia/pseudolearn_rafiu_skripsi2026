<?php

    $chatbotUserName = Auth::user()->name ?? 'Mahasiswa';
?>

{{-- Chatbot Component for Student Exam Page --}}

{{-- Chatbot floating button --}}
<div id="chatbot-toggle-btn" class="chatbot-floating-btn" onclick="toggleChatbot()">
    <img src="{{ asset('assets/media/icons_chatbot/logo_chatbot.png') }}" alt="Chatbot" class="chatbot-btn-icon">
</div>

{{-- Chatbot overlay for closing when clicking outside --}}
<div id="chatbot-overlay" class="chatbot-overlay chatbot-hidden" onclick="closeChatbot()"></div>

{{-- Chatbot popup container --}}
<div id="chatbot-container" class="chatbot-container chatbot-hidden">
    <link href="https://fonts.googleapis.com/css2?family=Lemon&display=swap" rel="stylesheet">
    {{-- Header --}}
    <div class="chatbot-header">
        <div class="chatbot-header-content">
            <div class="chatbot-header-title">
                <span class="chatbot-title-text chatbot-title-italic">PseudoLearn</span>
                <span class="chatbot-title-text">Chatbot AI</span>
            </div>
            <div class="chatbot-header-actions">
                <div class="chatbot-header-icon-wrapper">
                    <img src="{{ asset('assets/media/icons_chatbot/logo_chatbot.png') }}" alt="Chatbot" class="chatbot-header-icon">
                </div>
                {{-- <button class="chatbot-close-btn" onclick="closeChatbot()" title="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button> --}}
            </div>
        </div>
    </div>

    {{-- Chat messages area --}}
    <div class="chatbot-messages" id="chatbot-messages">
        {{-- Messages will be appended here --}}
    </div>

    {{-- Input area --}}
    <div class="chatbot-input-area">
        <div class="chatbot-input-wrapper">
            <input type="text" id="chatbot-input" class="chatbot-input" placeholder="Ketik pesan..." onkeypress="handleChatbotKeypress(event)">
            <button class="chatbot-send-btn" onclick="sendChatbotMessage()">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<style>
/* Chatbot Overlay - transparent, only for click detection */
.chatbot-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: transparent;
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.chatbot-overlay.chatbot-visible {
    opacity: 1;
    visibility: visible;
}

.chatbot-overlay.chatbot-hidden {
    opacity: 0;
    visibility: hidden;
}

/* Chatbot Floating Button */
.chatbot-floating-btn {
    position: fixed;
    bottom: 25px;
    right: 25px;
    width: 65px;
    height: 65px;
    border-radius: 50%;
    background: #ffffff;
    border: 3px solid #0a3a71;
    box-shadow: 0 4px 15px rgba(10, 58, 113, 0.3);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.chatbot-floating-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(10, 58, 113, 0.4);
    border-color: #1565c0;
}

.chatbot-btn-icon {
    width: 50px;
    height: 50px;
    object-fit: contain;
}

/* Chatbot Container */
.chatbot-container {
    position: fixed;
    bottom: 100px;
    right: 25px;
    width: 340px;
    height: 420px;
    background: #ffffff;
    border-radius: 20px;
    border: 3px solid #0a3a71;
    box-shadow: 0 10px 40px rgba(10, 58, 113, 0.25), 0 0 0 1px rgba(10, 58, 113, 0.1);
    z-index: 9998;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
}

.chatbot-hidden {
    opacity: 0;
    transform: translateY(20px);
    visibility: hidden;
    pointer-events: none;
}

.chatbot-visible {
    opacity: 1;
    transform: translateY(0);
    visibility: visible;
    pointer-events: auto;
}

/* Header */
.chatbot-header {
    background: linear-gradient(135deg, #0a3a71 0%, #0d47a1 100%);
    padding: 12px 15px;
    border-radius: 17px 17px 0 0;
}

.chatbot-header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chatbot-header-title {
    display: flex;
    flex-direction: column;
}

.chatbot-title-text {
    color: #ffffff;
    font-weight: 550;
    font-size: 20px;
    font-family: 'Lemon', cursive;
    line-height: 1.2;
}

.chatbot-title-italic {
    font-style: italic;
}

.chatbot-header-icon-wrapper {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border: 3px solid rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15), 
                0 2px 6px rgba(0, 0, 0, 0.1),
                inset 0 1px 3px rgba(255, 255, 255, 0.8);
    padding: 8px;
    transition: all 0.3s ease;
}

.chatbot-header-icon-wrapper:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2), 
                0 3px 8px rgba(0, 0, 0, 0.15),
                inset 0 1px 3px rgba(255, 255, 255, 0.8);
}

.chatbot-header-icon {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.chatbot-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* .chatbot-close-btn {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    color: white;
} */

/* .chatbot-close-btn:hover {
    background: rgba(255, 255, 255, 0);
    border-color: rgba(255, 255, 255, 0);
    transform: scale(1.1);
}

.chatbot-close-btn svg {
    width: 16px;
    height: 16px;
} */

/* Messages Area */
.chatbot-messages {
    flex: 1;
    padding: 12px;
    overflow-y: auto;
    background: #f8f9fa;
}

/* Message Bubble - Bot */
.chatbot-message {
    margin-bottom: 15px;
    animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chatbot-message-bot {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.chatbot-message-user {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.chatbot-bubble {
    max-width: 85%;
    padding: 10px 14px;
    border-radius: 15px;
    font-size: 0.85rem;
    line-height: 1.4;
}

.chatbot-bubble-bot {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 15px 15px 15px 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.chatbot-bubble-user {
    background: linear-gradient(135deg, #0a3a71 0%, #1565c0 100%);
    color: #ffffff;
    border-radius: 15px 15px 5px 15px;
}

/* Material Card in Chat */
.chatbot-material-card {
    background: #ffffff;
    border-left: 4px solid #0a3a71;
    padding: 12px 15px;
    margin: 5px 0;
    border-radius: 0 10px 10px 0;
    box-shadow: 0 2px 8px rgba(10, 58, 113, 0.12);
    max-width: 90%;
}

.chatbot-material-title {
    color: #0a3a71;
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 8px;
    font-family: 'Poppins', sans-serif;
}

.chatbot-material-content {
    color: #333333;
    font-size: 0.85rem;
    line-height: 1.6;
}

/* User greeting with icon */
.chatbot-user-greeting {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
}

.chatbot-user-greeting-icon {
    width: 24px;
    height: 24px;
}

.chatbot-user-greeting-text {
    background: #e3f2fd;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    color: #1565c0;
}

/* Input Area */
.chatbot-input-area {
    padding: 12px;
    background: #ffffff;
    border-top: 2px solid rgba(10, 58, 113, 0.15);
}

.chatbot-input-wrapper {
    display: flex;
    align-items: center;
    background: #ffffff;
    border-radius: 25px;
    padding: 5px 10px 5px 15px;
    border: 2px solid #0a3a71;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.chatbot-input-wrapper:focus-within {
    border-color: #1565c0;
    box-shadow: 0 0 0 3px rgba(21, 101, 192, 0.15);
}

.chatbot-input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    font-size: 0.9rem;
    font-family: 'Poppins', sans-serif;
    color: #333333;
    padding: 8px 0;
}

.chatbot-input::placeholder {
    color: #9e9e9e;
}

.chatbot-send-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0a3a71 0%, #1565c0 100%);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    flex-shrink: 0;
}

.chatbot-send-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 10px rgba(10, 58, 113, 0.3);
}

.chatbot-send-btn svg {
    width: 18px;
    height: 18px;
    color: white;
}

/* Scrollbar styling */
.chatbot-messages::-webkit-scrollbar {
    width: 6px;
}

.chatbot-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.chatbot-messages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.chatbot-messages::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* Typing indicator */
.chatbot-typing {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 10px 15px;
    background: #ffffff;
    border-radius: 15px;
    border: 1px solid #e0e0e0;
    width: fit-content;
}

.chatbot-typing-dot {
    width: 8px;
    height: 8px;
    background: #0a3a71;
    border-radius: 50%;
    animation: typingBounce 1.4s infinite ease-in-out both;
}

.chatbot-typing-dot:nth-child(1) {
    animation-delay: -0.32s;
}

.chatbot-typing-dot:nth-child(2) {
    animation-delay: -0.16s;
}

@keyframes typingBounce {
    0%, 80%, 100% {
        transform: scale(0.6);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Responsive */
@media (max-width: 480px) {
    .chatbot-container {
        width: calc(100% - 20px);
        right: 10px;
        bottom: 100px;
        height: 60vh;
    }
    
    .chatbot-floating-btn {
        right: 15px;
        bottom: 15px;
        width: 60px;
        height: 60px;
    }
    
    .chatbot-btn-icon {
        width: 35px;
        height: 35px;
    }
}
</style>

<script>
// Chatbot state
let chatbotOpen = false;
let chatbotAccessId = null;
const chatbotMessages = [];

// Toggle chatbot visibility
function toggleChatbot() {
    const container = document.getElementById('chatbot-container');
    const overlay   = document.getElementById('chatbot-overlay');
    chatbotOpen     = !chatbotOpen;

    if (chatbotOpen) {
        container.classList.remove('chatbot-hidden');
        container.classList.add('chatbot-visible');
        overlay.classList.remove('chatbot-hidden');
        overlay.classList.add('chatbot-visible');
        
        // Send welcome message if first time
        if (chatbotMessages.length === 0) {
            sendWelcomeMessage();
        }

        setTimeout(() => {
            document.getElementById('chatbot-input').focus();
        }, 300);
    } else {
        container.classList.remove('chatbot-visible');
        container.classList.add('chatbot-hidden');
        overlay.classList.remove('chatbot-visible');
        overlay.classList.add('chatbot-hidden');

        // Log close
        logChatbotClose();
    }
}

// Close chatbot
function closeChatbot() {
    const container = document.getElementById('chatbot-container');
    const overlay   = document.getElementById('chatbot-overlay');
    chatbotOpen     = false;

    container.classList.remove('chatbot-visible');
    container.classList.add('chatbot-hidden');
    overlay.classList.remove('chatbot-visible');
    overlay.classList.add('chatbot-hidden');

    // Log close
    logChatbotClose();
}

// Send welcome message
function sendWelcomeMessage() {
    const userName = @json($chatbotUserName);
    addBotMessage(`Hai ${userName}! 👋\n\nSaya adalah PseudoLearn Chatbot AI. Saya siap membantu kamu memahami materi atau menjawab pertanyaan seputar soal yang sedang kamu kerjakan.\n\nSilakan ketik pertanyaanmu!`);
}

// Add bot message
function addBotMessage(text) {
    const messagesContainer = document.getElementById('chatbot-messages');
    const messageDiv        = document.createElement('div');
    messageDiv.className    = 'chatbot-message chatbot-message-bot';
    messageDiv.innerHTML    = `
        <div class="chatbot-bubble chatbot-bubble-bot">${text.replace(/\n/g, '<br>')}</div>
    `;
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    chatbotMessages.push({ role: 'bot', text });
}

// Add user message
function addUserMessage(text) {
    const messagesContainer = document.getElementById('chatbot-messages');
    const messageDiv        = document.createElement('div');
    messageDiv.className    = 'chatbot-message chatbot-message-user';
    messageDiv.innerHTML    = `
        <div class="chatbot-bubble chatbot-bubble-user">${text}</div>
    `;
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    chatbotMessages.push({ role: 'user', text });
}

// Show typing indicator
function showTypingIndicator() {
    const messagesContainer = document.getElementById('chatbot-messages');
    const typingDiv         = document.createElement('div');
    typingDiv.id            = 'chatbot-typing-indicator';
    typingDiv.className     = 'chatbot-message chatbot-message-bot';
    typingDiv.innerHTML     = `
        <div class="chatbot-typing">
            <div class="chatbot-typing-dot"></div>
            <div class="chatbot-typing-dot"></div>
            <div class="chatbot-typing-dot"></div>
        </div>
    `;
    messagesContainer.appendChild(typingDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Hide typing indicator
function hideTypingIndicator() {
    const typingIndicator = document.getElementById('chatbot-typing-indicator');
    if (typingIndicator) typingIndicator.remove();
}

// Handle Enter key
function handleChatbotKeypress(event) {
    if (event.key === 'Enter') sendChatbotMessage();
}

// Send message to backend
async function sendChatbotMessage() {
    const input   = document.getElementById('chatbot-input');
    const message = input.value.trim();

    if (!message) return;

    addUserMessage(message);
    input.value = '';
    input.disabled = true;
    document.querySelector('.chatbot-send-btn').disabled = true;

    showTypingIndicator();
    
    // Send to backend (TODO: implement actual API call)
    // For now, simulate response
    setTimeout(() => {
        hideTypingIndicator();
        processChatbotResponse(message);
    }, 1000);
}

// Process chatbot response (mock)
// TODO: Connect to actual chatbot API
function processChatbotResponse(userMessage) {
    const lowerMessage = userMessage.toLowerCase();
    
    // Simple keyword-based responses for demo
    if (lowerMessage.includes('tipe data')) {
        addBotMessage(
            'Tipe Data adalah atribut yang menentukan jenis nilai yang bisa disimpan oleh suatu variabel, seperti teks (string), angka (integer, float), atau nilai benar/salah (boolean). serta bagaimana komputer harus menginterpretasi, menyimpan, dan melakukan operasi pada data tersebut',
            true,
            'Tipe Data'
        );
    } else if (lowerMessage.includes('algoritma')) {
        addBotMessage(
            'Algoritma adalah langkah-langkah sistematis dan terstruktur untuk menyelesaikan suatu masalah atau mencapai tujuan tertentu. Dalam pemrograman, algoritma menjadi dasar untuk membuat program yang efisien.',
            true,
            'Algoritma'
        );
    } else if (lowerMessage.includes('variabel')) {
        addBotMessage(
            'Variabel adalah tempat penyimpanan data di dalam program yang memiliki nama dan dapat menyimpan nilai yang bisa berubah selama eksekusi program.',
            true,
            'Variabel'
        );
    } else if (lowerMessage.includes('bantuan') || lowerMessage.includes('help')) {
        addBotMessage('Saya bisa membantu kamu dengan:\n\n• Menjelaskan materi tipe data\n• Menjelaskan konsep algoritma\n• Menjawab pertanyaan seputar variabel\n• Memberikan tips mengerjakan soal\n\nSilakan tanyakan apa yang ingin kamu ketahui!');
    } else {
        addBotMessage('Terima kasih atas pertanyaanmu! Saya sedang memproses jawabannya. Untuk saat ini, coba tanyakan tentang "tipe data", "algoritma", atau "variabel" untuk mendapatkan penjelasan.');
    }
}
</script>
