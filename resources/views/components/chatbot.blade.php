<?php

    $chatbotUserName = Auth::user()->name ?? 'Mahasiswa';
?>

{{-- Chatbot Component for Student Exam Page --}}

{{-- Chatbot floating button --}}
<div id="chatbot-toggle-btn" class="chatbot-floating-btn" onclick="toggleChatbot()">
    <img src="{{ asset('assets/media/icons_chatbot/logo_chatbot.png') }}" alt="Chatbot" class="chatbot-btn-icon">
</div>

{{-- Chatbot overlay (klik di luar hanya menutup saat mode biasa) --}}
<div id="chatbot-overlay" class="chatbot-overlay chatbot-hidden" onclick="handleChatbotOverlayClick()"></div>

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
                <button class="chatbot-close-btn" onclick="closeChatbot()" title="Tutup bimbingan chatbot">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
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

/* Mode adaptif: layar digelapkan dan halaman di belakang terkunci */
.chatbot-overlay.chatbot-dim {
    background: rgba(0, 0, 0, 0.604);
    cursor: not-allowed;
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

.chatbot-floating-btn.chatbot-active {
    background: #9af802;/* hijau aktif */
    border-color: #9af802;
    box-shadow: 0 6px 25px #9af802;
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
    gap: 12px;
}

.chatbot-header-title {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 0;
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
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;

    /* kecilkan efek putih */
    background: rgba(255, 255, 255, 0.9);

    /* border lebih tipis biar tidak terlalu dominan */
    border: 1.5px solid rgba(255, 255, 255, 0.8);

    border-radius: 50%;

    /* padding diperkecil → logo otomatis terlihat lebih besar */
    padding: 4px;

    transition: all 0.3s ease;
}

/* .chatbot-header-icon-wrapper:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2), 
                0 3px 8px rgba(0, 0, 0, 0.15),
                inset 0 1px 3px rgba(255, 255, 255, 0.8);
} */

.chatbot-header-icon {
    width: 100%;
    height: 100%;
    object-fit: contain;
    position: relative;
    z-index: 1;
}

.chatbot-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    margin-left: auto;
}

.chatbot-close-btn {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.4);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;

    /* tambahan biar lebih kelihatan */
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.chatbot-close-btn:hover {
    background: #ff4d4f; /* merah */
    border-color: #ff4d4f;
    transform: scale(1.15);
    box-shadow: 0 4px 12px rgba(255, 77, 79, 0.5);
}

.chatbot-close-btn svg {
    width: 18px;
    height: 18px;
}

/* optional: animasi icon */
.chatbot-close-btn:hover svg {
    transform: rotate(90deg);
}

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
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
// Chatbot state
let chatbotOpen = false;
let chatbotAccessId = null;
const chatbotMessages = [];

// Adaptive chatbot state
let chatbotLowPerformance = false;
let chatbotPerformanceLabel = null;
let chatbotAdaptiveGuideSent = false;
let chatbotPerformanceInterval = null;
let chatbotAdaptiveActive = false; // true saat popup bimbingan adaptif mengunci layar
let chatbotPerformanceTotalDrag = null;
let chatbotPerformanceElapsed = null;
const chatbotAdaptiveSoundSrc = @json(asset('assets/media/audio/sound_notif_chatbot_adaptive.mp3'));

// Play sound ceting ketika chatbot adaptive muncul
function playChatbotCetingSound() {
    try {
        const audio = new Audio(chatbotAdaptiveSoundSrc);
        audio.preload = 'auto';
        audio.volume = 1;
        audio.play().catch((error) => {
            console.error('Chatbot sound error:', error);
        });
    } catch (error) {
        console.error('Chatbot sound error:', error);
    }
}
    
function openChatbotPopup(options = {}) {
    const { playAdaptiveSound = false, adaptive = false } = options;
    const container = document.getElementById('chatbot-container');
    const overlay = document.getElementById('chatbot-overlay');
    const toggleBtn = document.getElementById('chatbot-toggle-btn');
    const headerIconWrapper = document.querySelector('.chatbot-header-icon-wrapper');

    chatbotOpen = true;
    if (adaptive) {
        chatbotAdaptiveActive = true;
    }
    container.classList.remove('chatbot-hidden');
    container.classList.add('chatbot-visible');
    overlay.classList.remove('chatbot-hidden');
    overlay.classList.add('chatbot-visible');

    if (chatbotAdaptiveActive) {
        overlay.classList.add('chatbot-dim');
    } else {
        overlay.classList.remove('chatbot-dim');
    }

    // Tampilkan status aktif (hijau) pada logo/tombol ketika popup terbuka
    if (toggleBtn) {
        toggleBtn.classList.add('chatbot-active');
    }
    if (headerIconWrapper) {
        headerIconWrapper.classList.add('chatbot-active');
    }

    logChatbotOpen();

    if (playAdaptiveSound && !chatbotAdaptiveGuideSent) {
        playChatbotCetingSound();
    }

    if (chatbotMessages.length === 0) {
        sendWelcomeMessage();
    }

    setTimeout(() => {
        document.getElementById('chatbot-input').focus();
    }, 300);
}

async function triggerAdaptiveGuidePopup() {
    if (chatbotOpen) {
        // Jika chatbot sudah terbuka, aktifkan mode adaptif: layar digelapkan & terkunci
        chatbotAdaptiveActive = true;
        const overlay = document.getElementById('chatbot-overlay');
        overlay.classList.remove('chatbot-hidden');
        overlay.classList.add('chatbot-visible', 'chatbot-dim');

        if (chatbotAccessId) {
            await logChatbotClose();
        }

        await logChatbotOpen('adaptive');

        sendAdaptiveGuide();
        return;
    }

    openChatbotPopup({ playAdaptiveSound: true, adaptive: true });

    if (!chatbotAdaptiveGuideSent) {
        sendAdaptiveGuide();
    }
}

// Toggle chatbot visibility
function toggleChatbot() {
    const container = document.getElementById('chatbot-container');
    const overlay   = document.getElementById('chatbot-overlay');

    // Saat mode adaptif aktif, chatbot tidak boleh ditutup dengan klik tombol mengambang
    if (chatbotAdaptiveActive) {
        return;
    }

    chatbotOpen = !chatbotOpen;

    if (chatbotOpen) {
        openChatbotPopup();
    } else {
        closeChatbot();
    }
}

// Close chatbot
function closeChatbot() {
    const container = document.getElementById('chatbot-container');
    const overlay   = document.getElementById('chatbot-overlay');
    const toggleBtn = document.getElementById('chatbot-toggle-btn');
    const headerIconWrapper = document.querySelector('.chatbot-header-icon-wrapper');
    chatbotOpen = false;
    chatbotAdaptiveActive = false;

    container.classList.remove('chatbot-visible');
    container.classList.add('chatbot-hidden');
    overlay.classList.remove('chatbot-visible', 'chatbot-dim');
    overlay.classList.add('chatbot-hidden');

    // Hilangkan status aktif (hijau) pada logo/tombol ketika popup ditutup
    if (toggleBtn) {
        toggleBtn.classList.remove('chatbot-active');
    }
    if (headerIconWrapper) {
        headerIconWrapper.classList.remove('chatbot-active');
    }

    // Log close
    logChatbotClose();
}

// Handler klik overlay: hanya menutup chatbot saat mode biasa (non-adaptif)
function handleChatbotOverlayClick() {
    if (chatbotAdaptiveActive) {
        // Mode adaptif: klik di luar tidak melakukan apa-apa
        return;
    }
    closeChatbot();
}

// Check student performance periodically (synced with exam timer)
async function checkPerformance() {
    // Hanya cek jika timer sudah berjalan (mahasiswa sudah mulai drag pertama)
    if (!window.timerStarted || window.timerElapsed === 0) return;

    const idSoal  = document.getElementById('id-soal')?.value;
    const idLevel = document.getElementById('id-level')?.value;

    if (!idSoal || !idLevel) return;

    try {
        const response = await fetch('/chatbot/check-performance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept'      : 'application/json',
            },
            body: JSON.stringify({
                id_soal:      idSoal,
                id_level:     idLevel,
                elapsed_time: window.timerElapsed,  // waktu pengerjaan real-time
            }),
        });

        const data = await response.json();

        if (data.success && data.status === 'low_performance') {
            chatbotLowPerformance   = true;
            chatbotPerformanceLabel = data.label;
            chatbotPerformanceTotalDrag = Number.isFinite(data.total_drag) ? Number(data.total_drag) : null;
            chatbotPerformanceElapsed = Number.isFinite(data.total_waktu) ? Number(data.total_waktu) : window.timerElapsed;

            // Langsung tampilkan pop up chatbot adaptive dan bunyikan suara ceting
            if (!chatbotAdaptiveGuideSent) {
                triggerAdaptiveGuidePopup();
            }
        } else {
            chatbotLowPerformance   = false;
            chatbotPerformanceLabel = data.label || null;
            chatbotPerformanceTotalDrag = null;
            chatbotPerformanceElapsed = null;
        }
    } catch (error) {
        console.error('Performance check error:', error);
    }
}

// Send adaptive guide automatically
async function sendAdaptiveGuide() {
    if (chatbotAdaptiveGuideSent) return;
    chatbotAdaptiveGuideSent = true;

    const idSoal  = document.getElementById('id-soal')?.value;
    const idLevel = document.getElementById('id-level')?.value;

    if (!idSoal || !idLevel || !chatbotPerformanceLabel) return;

    // Tampilkan pesan adaptif dari sistem sesuai kondisi performance
    let labelText = '';
    if (chatbotPerformanceLabel === 'Struggling') {
        labelText = '😟 Sepertinya kamu mengalami kesulitan pada soal ini (banyak mencoba, waktu lama). Jangan khawatir, saya akan membantu menjelaskan konsepnya!';
    } else if (chatbotPerformanceLabel === 'Gaming the System') {
        labelText = '⚡ Sepertinya kamu sedang menebak-nebak (banyak mencoba, tapi terlalu cepat). Mari kita pahami konsep soal ini dengan lebih teliti!';
    } else {
        labelText = '💡 Saya akan memberikan bimbingan untuk membantu kamu!';
    }

    addBotMessage(`${labelText} Tenang, saya akan memberikan bimbingan bantuan penjelasan untuk membantumu! 💡\n\nMohon tunggu sebentar...`);

    showTypingIndicator();

    const adaptiveAccessId = await waitForAdaptiveAccessId();

    try {
        const response = await fetch('/chatbot/adaptive-guide', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept'      : 'application/json',
            },
            body: JSON.stringify({
                id_soal:  idSoal,
                id_level: idLevel,
                label:    chatbotPerformanceLabel,
                elapsed_time: Number.isFinite(chatbotPerformanceElapsed) ? chatbotPerformanceElapsed : window.timerElapsed,
                total_drag: Number.isFinite(chatbotPerformanceTotalDrag) ? chatbotPerformanceTotalDrag : null,
                access_id: adaptiveAccessId,
            }),
        });

        const data = await response.json();
        hideTypingIndicator();

        if (data.success) {
            addBotMessage(data.respons);
            addBotMessage('Kamu bisa bertanya lebih lanjut jika masih ada yang belum dipahami! 😊');
        } else {
            addBotMessage('Maaf, saya belum bisa memberikan bimbingan saat ini. Silakan coba lagi.');
        }
    } catch (error) {
        hideTypingIndicator();
        addBotMessage('Maaf, terjadi kesalahan. Silakan ketik pertanyaanmu secara manual.');
        console.error('Adaptive guide error:', error);
    }
}

async function waitForAdaptiveAccessId(maxWaitMs = 1500) {
    const intervalMs = 100;
    let waitedMs = 0;

    while (!chatbotAccessId && waitedMs < maxWaitMs) {
        await new Promise((resolve) => setTimeout(resolve, intervalMs));
        waitedMs += intervalMs;
    }

    return chatbotAccessId;
}

// Start performance monitoring — synced dengan waktu pengerjaan soal
function startPerformanceMonitor() {
    // Monitor setiap 5 detik apakah timer sudah berjalan
    const waitForTimer = setInterval(() => {
        if (window.timerStarted && window.timerElapsed > 0) {
            clearInterval(waitForTimer);
            // Timer sudah mulai (drag pertama sudah terjadi), mulai cek performa
            // Cek pertama kali langsung
            checkPerformance();
            // Lalu cek berkala setiap 10 detik (sinkron dengan waktu pengerjaan)
            chatbotPerformanceInterval = setInterval(checkPerformance, 10000);
        }
    }, 5000);
}

// Send welcome message
function sendWelcomeMessage() {
    const userName = @json($chatbotUserName);
    addBotMessage(`Hai <strong>${userName}</strong>! 👋<br><br>
    Saya adalah PseudoLearn Chatbot AI. Saya siap membantu kamu memahami materi atau menjawab pertanyaan seputar soal yang sedang kamu kerjakan.<br><br>
    Silakan ketik pertanyaanmu!`);
}

// Add bot message
function addBotMessage(text) {
    const messagesContainer = document.getElementById('chatbot-messages');
    const messageDiv        = document.createElement('div');
    messageDiv.className    = 'chatbot-message chatbot-message-bot';
    let html;
    if (typeof marked !== 'undefined' && typeof marked.parse === 'function') {
        html = marked.parse(text);
    } else {
        html = text.replace(/\n/g, '<br>');
    }
    messageDiv.innerHTML    = `
        <div class="chatbot-bubble chatbot-bubble-bot">${html}</div>
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

function getChatbotContextIds() {
    return {
        idSoal: '{{ $id_soal ?? "" }}' || (document.getElementById('id-soal') ? document.getElementById('id-soal').value : '') || null,
        idLevel: '{{ $id_level ?? "" }}' || (document.getElementById('id-level') ? document.getElementById('id-level').value : '') || null,
    };
}

// Log chatbot open
async function logChatbotOpen(typeOverride = null) {
    if (chatbotAccessId) return chatbotAccessId;

    const context = getChatbotContextIds();

    try {
        const response = await fetch('/chatbot/open', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept'      : 'application/json',
            },
            body: JSON.stringify({
                type: typeOverride || (chatbotLowPerformance ? 'adaptive' : 'biasa'),
                id_soal: context.idSoal,
                id_level: context.idLevel,
            }),
        });
        const data = await response.json();
        if (data.success) {
            chatbotAccessId = data.access_id;
        }
        return chatbotAccessId;
    } catch (error) {
        console.error('Log open error:', error);
        return null;
    }
}

// Log chatbot close
async function logChatbotClose() {
    if (!chatbotAccessId) return;
    try {
        await fetch('/chatbot/close', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept'      : 'application/json',
            },
            body: JSON.stringify({
                access_id: chatbotAccessId,
            }),
        });
        chatbotAccessId = null;
    } catch (error) {
        console.error('Log close error:', error);
    }
}

// Send message to backend
async function sendChatbotMessage() {
    const input   = document.getElementById('chatbot-input');
    const message = input.value.trim();

    if (!message) return;

    addUserMessage(message);
    input.value    = '';
    input.disabled = true;
    document.querySelector('.chatbot-send-btn').disabled = true;

    showTypingIndicator();

    try {
        if (!chatbotAccessId && chatbotOpen) {
            await logChatbotOpen();
        }

        const context = getChatbotContextIds();

        const response = await fetch('/chatbot/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept'      : 'application/json',
            },
            body: JSON.stringify({
                message   : message,
                access_id : chatbotAccessId || null,
                id_soal   : context.idSoal,
                id_level  : context.idLevel,
            }),
        });

        const data = await response.json();
        hideTypingIndicator();

        if (data.success) {
            addBotMessage(data.respons);
        } else {
            addBotMessage('Maaf, terjadi kesalahan. Silakan coba lagi.');
        }

    } catch (error) {
        hideTypingIndicator();
        addBotMessage('Maaf, tidak dapat terhubung ke server. Periksa koneksi internet kamu.');
        console.error('Chatbot error:', error);
    } finally {
        input.disabled = false;
        document.querySelector('.chatbot-send-btn').disabled = false;
        input.focus();
    }
}

// Mulai monitoring performa saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    startPerformanceMonitor();
});
</script>
