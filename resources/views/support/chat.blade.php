<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Support Chat</title>
    <style>
        body {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 24px;
            background-color: #f2f6fb;
            color: #1f2937;
        }

        h1 {
            margin-bottom: 16px;
            font-size: 2rem;
            letter-spacing: -0.03em;
        }

        .chat-container {
            border-radius: 24px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 24px 64px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(148, 163, 184, 0.16);
            padding: 24px;
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            margin-bottom: 18px;
        }

        .chat-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #111827;
        }

        .chat-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .chat-messages {
            min-height: 360px;
            max-height: 540px;
            overflow-y: auto;
            padding: 16px;
            background-color: #eef4ff;
            border-radius: 18px;
            border: 1px solid rgba(59, 130, 246, 0.12);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .message {
            max-width: 78%;
            padding: 14px 16px;
            border-radius: 18px;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .user-message {
            margin-left: auto;
            background-color: #2563eb;
            color: white;
            border-bottom-right-radius: 6px;
        }

        .ai-message {
            background-color: white;
            color: #111827;
            border-bottom-left-radius: 6px;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .placeholder {
            color: #475569;
            font-style: italic;
            text-align: center;
            padding: 32px 16px;
        }

        .input-panel {
            margin-top: 18px;
            display: grid;
            gap: 14px;
        }

        textarea {
            width: 100%;
            min-height: 120px;
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            padding: 14px 16px;
            font-size: 1rem;
            line-height: 1.6;
            resize: vertical;
            background-color: white;
            color: #111827;
        }

        .controls {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        button {
            border: none;
            border-radius: 14px;
            padding: 12px 18px;
            font-weight: 700;
            cursor: pointer;
        }

        button[type='submit'] {
            background-color: #2563eb;
            color: white;
        }

        button#clear-button {
            background-color: #e2e8f0;
            color: #0f172a;
        }

        button:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .loading {
            color: #475569;
            font-size: 0.95rem;
            font-style: italic;
        }

        .error {
            color: #b91c1c;
            font-size: 0.95rem;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <h1>AI Support Chat</h1>

    <div class="chat-container">
        <div class="chat-header">
            <div class="chat-title">Support conversation</div>
            <div class="chat-actions">
                <button id="clear-button" type="button">Clear chat</button>
            </div>
        </div>

        <div id="chat-messages" class="chat-messages">
            @if ($messages->isEmpty())
                <div class="placeholder">Start the conversation by asking a question. Your previous messages will appear here.</div>
            @else
                @foreach ($messages as $message)
                    <div class="message {{ $message->role === 'user' ? 'user-message' : 'ai-message' }}">
                        {!! nl2br(e($message->content)) !!}
                    </div>
                @endforeach
            @endif
        </div>

        <div class="input-panel">
            <textarea id="message-input" placeholder="Type your message here..." rows="4"></textarea>
            <div class="controls">
                <button id="send-button" type="button">Send message</button>
                <span id="loading" class="loading">AI is thinking...</span>
            </div>
            <div id="error" class="error"></div>
        </div>
    </div>

    <script>
        const messagesContainer = document.getElementById('chat-messages');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');
        const clearButton = document.getElementById('clear-button');
        const loadingText = document.getElementById('loading');
        const errorText = document.getElementById('error');

        function scrollMessagesToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function setPlaceholder() {
            messagesContainer.innerHTML = '<div class="placeholder">Your conversation is empty. Send a message to get started.</div>';
        }

        function addMessage(content, type) {
            const placeholder = messagesContainer.querySelector('.placeholder');

            if (placeholder) {
                placeholder.remove();
            }

            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}-message`;
            messageDiv.textContent = content;
            messagesContainer.appendChild(messageDiv);
            scrollMessagesToBottom();
        }

        async function sendMessage() {
            const message = messageInput.value.trim();

            if (!message) {
                return;
            }

            errorText.textContent = '';
            sendButton.disabled = true;
            loadingText.style.display = 'inline';

            addMessage(message, 'user');
            messageInput.value = '';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/support/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ message }),
                });

                const data = await response.json();

                if (response.ok) {
                    addMessage(data.answer, 'ai');
                } else {
                    errorText.textContent = data.error || 'Unable to get a response from the AI service.';
                }
            } catch (error) {
                errorText.textContent = 'Network error. Please try again.';
            } finally {
                sendButton.disabled = false;
                loadingText.style.display = 'none';
            }
        }

        async function clearChat() {
            errorText.textContent = '';
            sendButton.disabled = true;
            clearButton.disabled = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/support/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({}),
                });

                if (response.ok) {
                    setPlaceholder();
                } else {
                    const data = await response.json();
                    errorText.textContent = data.error || 'Unable to clear the conversation.';
                }
            } catch (error) {
                errorText.textContent = 'Network error while clearing the chat.';
            } finally {
                sendButton.disabled = false;
                clearButton.disabled = false;
            }
        }

        sendButton.addEventListener('click', sendMessage);
        clearButton.addEventListener('click', clearChat);

        messageInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        });

        scrollMessagesToBottom();
    </script>
</body>
</html>
