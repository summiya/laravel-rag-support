<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Support Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .chat-container {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #eee;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #fafafa;
        }
        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .user-message {
            background-color: #007bff;
            color: white;
            text-align: right;
        }
        .ai-message {
            background-color: #e9ecef;
            color: #333;
        }
        .input-group {
            display: flex;
            gap: 10px;
        }
        textarea {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }
        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        .loading {
            display: none;
            color: #666;
            font-style: italic;
        }
        .error {
            color: #dc3545;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>AI Support Chat</h1>
    <div class="chat-container">
        <div id="chat-messages" class="chat-messages">
            <!-- Chat messages will appear here -->
        </div>
        <div class="input-group">
            <textarea id="message-input" placeholder="Type your message here..." rows="3"></textarea>
            <button id="send-button" onclick="sendMessage()">Send</button>
        </div>
        <div id="loading" class="loading">AI is thinking...</div>
        <div id="error" class="error"></div>
    </div>

    <script>
        // Function to add a message to the chat
        function addMessage(content, type) {
            const messagesDiv = document.getElementById('chat-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}-message`;
            messageDiv.textContent = content;
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight; // Scroll to bottom
        }

        // Function to send message via AJAX
        async function sendMessage() {
            const messageInput = document.getElementById('message-input');
            const sendButton = document.getElementById('send-button');
            const loadingDiv = document.getElementById('loading');
            const errorDiv = document.getElementById('error');

            const message = messageInput.value.trim();
            if (!message) return;

            // Clear previous error
            errorDiv.textContent = '';

            // Add user message to chat
            addMessage(message, 'user');

            // Clear input and disable button
            messageInput.value = '';
            sendButton.disabled = true;
            loadingDiv.style.display = 'block';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/support/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();

                if (response.ok) {
                    // Add AI response to chat
                    addMessage(data.answer, 'ai');
                } else {
                    // Show error
                    errorDiv.textContent = data.error || 'An error occurred.';
                }
            } catch (error) {
                errorDiv.textContent = 'Network error. Please try again.';
            } finally {
                // Re-enable button and hide loading
                sendButton.disabled = false;
                loadingDiv.style.display = 'none';
            }
        }

        // Allow sending message with Enter key
        document.getElementById('message-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    </script>
</body>
</html>