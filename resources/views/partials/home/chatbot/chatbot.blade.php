<!-- Icon chatbot ở góc phải -->
<div id="chatbot-icon" onclick="toggleChatbot()">
    <img src="/images/chatbot-icon.png" alt="Chatbot Icon" />
</div>

<!-- Giao diện hộp chat -->
<div id="chatbot-box">
    <div id="chatbot-header">
        <span>Trợ lý AI</span>
        <button onclick="toggleChatbot()">X</button>
    </div>
    <div id="chatbot-messages"></div>
    <div id="chatbot-input">
        <input type="text" id="chatbot-user-message" placeholder="Nhập tin nhắn..." />
        <button onclick="sendMessage()">Gửi</button>
    </div>
</div>