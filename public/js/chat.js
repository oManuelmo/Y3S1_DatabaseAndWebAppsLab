document.addEventListener('DOMContentLoaded', function () {
    if (window.chatData && window.chatData.chatid) {
        const chatid = window.chatData.chatid;
        const authUserId = window.chatData.authUserId;
        const isChatClosed = window.chatData.isChatClosed === 'true';

        console.log('Chat ID:', chatid);
        console.log('Auth User ID:', authUserId);
        console.log('Is Chat Closed:', isChatClosed);

        const chatMessages = document.getElementById('chatMessages');
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.querySelector('#messageForm button[type="submit"]');
        const closeChatButton = document.querySelector('#closeChatForm button[type="submit"]');

        if (isChatClosed) {
            console.log('Chat is already closed on load. Disabling input and buttons.');
            disableChatElements();
        }

        const pusher = new Pusher('eedefc1ec5f312a69b3a', {
            cluster: 'eu',
            encrypted: true,
        });

        const channel = pusher.subscribe('chat.' + chatid);
        console.log('Subscribed to chat.' + chatid);

        channel.bind('MessageSent', function (event) {
            console.log('MessageSent event received:', event);
            const messageElement = document.createElement('div');
            messageElement.classList.add('message');

            const senderName = event.message.senderid === 0 ? 'System' : (event.message.sender ? event.message.sender.firstname : 'Admin');
            const messageContent = event.message.messagetext;
            const timestamp = new Date(event.message.createdat).toLocaleString('en-GB');

            const isSent = event.message.senderid === parseInt(authUserId);
            messageElement.classList.add(isSent ? 'sent' : 'received');

            if (event.message.senderid === 0) {
                messageElement.classList.add('system');
            }

            messageElement.innerHTML = ` 
                <strong>${senderName}</strong>
                <p>${messageContent}</p>
                <small>${timestamp}</small>
            `;

            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });

        channel.bind('ChatClosed', function () {
            console.log('ChatClosed event received for chat ID: ' + chatid);
            disableChatElements();
        });

        const messageForm = document.getElementById('messageForm');
        if (messageForm) {
            messageForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const message = messageInput.value;
                if (!message.trim()) return;

                sendButton.disabled = true; 
                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ message })
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(`Error sending message: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then((data) => {
                        console.log('Message sent successfully:', data);
                        messageInput.value = ''; 
                    })
                    .catch((error) => console.error('Error sending message:', error))
                    .finally(() => {
                        sendButton.disabled = false; 
                    });
            });
        }

        const closeChatForm = document.getElementById('closeChatForm');
        if (closeChatForm) {
            closeChatForm.addEventListener('submit', function (e) {
                e.preventDefault();

                closeChatButton.disabled = true; 
                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(`Error closing chat: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then((data) => {
                        console.log('Chat closed successfully:', data);

                        disableChatElements();
                    })
                    .catch((error) => console.error('Error closing chat:', error))
                    .finally(() => {
                        closeChatButton.disabled = false; 
                    });
            });
        }

        function disableChatElements() {
            if (messageInput) messageInput.disabled = true;
            if (sendButton) sendButton.disabled = true;
            if (closeChatButton) closeChatButton.disabled = true;

            console.log('Chat elements disabled:');
            console.log('Message Input Disabled:', messageInput.disabled);
            console.log('Send Button Disabled:', sendButton.disabled);
            console.log('Close Chat Button Disabled:', closeChatButton.disabled);
        }
    }
});
