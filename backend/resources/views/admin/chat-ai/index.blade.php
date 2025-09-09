@extends('admin.layouts.app')

@section('title', 'AI Assistant')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-robot text-warning me-2"></i>
                        AI Assistant Sekolah
                    </h1>
                    <p class="text-muted mb-0">Tanyakan tentang data sekolah dengan mudah</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-success">
                        <i class="fas fa-circle me-1"></i>
                        Online
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Interface -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <!-- Chat Messages Area -->
                    <div id="chatMessages" class="chat-messages">
                        <div class="message ai-message">
                            <div class="message-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="message-content">
                                <div class="message-header">
                                    <strong>AI Assistant</strong>
                                    <small class="text-muted">{{ now()->format('H:i') }}</small>
                                </div>
                                <div class="message-text">
                                    <p>Halo! Saya AI Assistant untuk sistem manajemen sekolah SMPN 14 Surabaya.</p>
                                    <p><strong>Saya bisa membantu dengan:</strong></p>
                                    <ul class="mb-0">
                                        <li>Data kehadiran pegawai dan siswa</li>
                                        <li>Data izin dan cuti</li>
                                        <li>Statistik dan laporan</li>
                                        <li>Informasi umum sekolah</li>
                                    </ul>
                                    <p class="mt-2 mb-0">Silakan tanyakan apa yang ingin Anda ketahui!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chat Input Area -->
                    <div class="chat-input">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="messageInput" 
                                   placeholder="Tanyakan tentang data sekolah..." 
                                   autocomplete="off">
                            <button class="btn btn-primary" type="button" id="sendBtn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <div class="chat-suggestions mt-2">
                            <small class="text-muted">Contoh pertanyaan:</small>
                            <div class="suggestion-tags">
                                <span class="badge bg-light text-dark suggestion-tag" data-question="Berapa pegawai yang hadir hari ini?">Kehadiran hari ini</span>
                                <span class="badge bg-light text-dark suggestion-tag" data-question="Data pegawai yang absen pada tanggal 3 september 2025">Data absen 3 Sep 2025</span>
                                <span class="badge bg-light text-dark suggestion-tag" data-question="Berapa total siswa di sekolah?">Total siswa</span>
                                <span class="badge bg-light text-dark suggestion-tag" data-question="Berapa pengajuan izin yang menunggu?">Izin menunggu</span>
                                <span class="badge bg-light text-dark suggestion-tag" data-question="Siapa yang terlambat hari ini?">Keterlambatan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-messages {
    height: 500px;
    overflow-y: auto;
    padding: 1rem;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.message {
    display: flex;
    margin-bottom: 1rem;
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.ai-message .message-avatar {
    background-color: #ffc107;
    color: white;
}

.user-message .message-avatar {
    background-color: #007bff;
    color: white;
}

.message-content {
    flex: 1;
    background: white;
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.user-message .message-content {
    background-color: #007bff;
    color: white;
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.message-header strong {
    font-size: 0.875rem;
}

.message-header small {
    font-size: 0.75rem;
}

.user-message .message-header small {
    color: rgba(255,255,255,0.8);
}

.message-text {
    line-height: 1.5;
}

.message-text ul {
    margin: 0.5rem 0;
    padding-left: 1.5rem;
}

.message-text li {
    margin-bottom: 0.25rem;
}

.chat-input {
    padding: 1rem;
    background: white;
}

.suggestion-tags {
    margin-top: 0.5rem;
}

.suggestion-tag {
    cursor: pointer;
    margin-right: 0.5rem;
    margin-bottom: 0.25rem;
    transition: all 0.2s ease;
}

.suggestion-tag:hover {
    background-color: #007bff !important;
    color: white !important;
}

.typing-indicator {
    display: none;
    align-items: center;
    padding: 0.75rem 1rem;
    color: #6c757d;
    font-style: italic;
}

.typing-indicator.show {
    display: flex;
}

.typing-dots {
    display: inline-block;
    margin-left: 0.5rem;
}

.typing-dots span {
    display: inline-block;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background-color: #6c757d;
    margin: 0 1px;
    animation: typing 1.4s infinite ease-in-out;
}

.typing-dots span:nth-child(1) { animation-delay: -0.32s; }
.typing-dots span:nth-child(2) { animation-delay: -0.16s; }

@keyframes typing {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}

/* Scrollbar styling */
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const chatMessages = document.getElementById('chatMessages');
    const suggestionTags = document.querySelectorAll('.suggestion-tag');
    
    // Send message function
    function sendMessage() {
        const message = messageInput.value.trim();
        
        if (message) {
            // Add user message
            addMessage(message, 'user');
            
            // Show typing indicator
            showTypingIndicator();
            
            // Send to server
            fetch('{{ route("admin.chat-ai.ask") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ question: message })
            })
            .then(response => response.json())
            .then(data => {
                hideTypingIndicator();
                
                if (data.success) {
                    addMessage(data.answer, 'ai');
                } else {
                    addMessage(data.answer || 'Maaf, terjadi kesalahan. Silakan coba lagi.', 'ai');
                }
            })
            .catch(error => {
                hideTypingIndicator();
                addMessage('Maaf, terjadi kesalahan koneksi. Silakan coba lagi.', 'ai');
            });
            
            messageInput.value = '';
        }
    }
    
    // Add message to chat
    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        messageDiv.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-${sender === 'user' ? 'user' : 'robot'}"></i>
            </div>
            <div class="message-content">
                <div class="message-header">
                    <strong>${sender === 'user' ? 'Anda' : 'AI Assistant'}</strong>
                    <small class="text-muted">${time}</small>
                </div>
                <div class="message-text">${formatMessage(text)}</div>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Format message text (convert markdown-like formatting)
    function formatMessage(text) {
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
    }
    
    // Show typing indicator
    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'typing-indicator show';
        typingDiv.id = 'typingIndicator';
        typingDiv.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
                <div class="message-header">
                    <strong>AI Assistant</strong>
                </div>
                <div class="message-text">
                    Sedang mengetik
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
        
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Hide typing indicator
    function hideTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }
    
    // Event listeners
    sendBtn.addEventListener('click', sendMessage);
    
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    // Suggestion tags
    suggestionTags.forEach(tag => {
        tag.addEventListener('click', function() {
            const question = this.getAttribute('data-question');
            messageInput.value = question;
            messageInput.focus();
        });
    });
    
    // Auto focus on input
    messageInput.focus();
});
</script>
@endpush
