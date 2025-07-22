<?php
$pageTitle = "AI chatbot page";
include __DIR__ . '/../includes/header.php';
?>
<h1 class=" text-center text-4xl my-8 font-bold">JAWLA Chatbot</h1>
<div class="chat-container max-w-2xl mx-auto  rounded-xl shadow-md p-6 border border-gray-200 my-5">
    <div id="chat-history" class="h-96 overflow-y-auto mb-6 p-4  rounded-lg border border-gray-200 space-y-4"></div>
    
    <div class="flex flex-col gap-4">
        <div class="flex gap-3">
            <input type="text" id="user-input" 
                   class="flex-1 px-5 h-14 bg-transparent border border-gray-300 rounded-lg outline-none focus:border-primary transition-colors"
                   placeholder="Ask about tourism...">
            <button onclick="sendMessage()" 
                    class="px-6 h-14 bg-primary text-white rounded-lg text-lg font-semibold hover:bg-primary-dark transition-colors">
                Send
            </button>
        </div>
        
        <button onclick="clearChat()" 
                class="w-full md:w-auto px-6 h-12 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">
            Clear Chat History
        </button>
    </div>
</div>
    <script>
// Load chat history on page load
window.addEventListener('DOMContentLoaded', async () => {
    const history = document.getElementById('chat-history');
    const response = await fetch('../other/get_history.php');
    const messages = await response.json();
    
    messages.forEach(msg => {
        const className = msg.role === 'user' 
            ? 'text-right mb-2' 
            : 'text-left mb-2';
        history.innerHTML += `
            <div class=" ${className}">
        ${msg.role === 'user' ? 'You' : 'Assistant'}:
        ${msg.content}
    </div>
    `;
    });
    history.scrollTop = history.scrollHeight;
    });

    // Updated sendMessage function
    async function sendMessage() {
    const input = document.getElementById('user-input');
    const history = document.getElementById('chat-history');
    const button = document.querySelector('button');
    const message = input.value.trim();

    if (!message) return;

    // Disable during processing
    button.disabled = true;
    input.value = '';

    try {
    // Get response from server
    const response = await fetch('../other/chatbot.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ message })
    });

    const data = await response.json();

    // Add both messages to display
    history.innerHTML += `
    <div class="text-right mb-2">You: ${message}</div>
    <div class="text-left mb-2">Assistant: ${data.reply}</div>
    `;

    // Scroll to bottom
    history.scrollTop = history.scrollHeight;
    } catch (error) {
    alert('Error sending message');
    input.value = message; // Restore message on error
    } finally {
    button.disabled = false;
    }
    }
    async function clearChat() {
    await fetch('../other/clear_chat.php');
    location.reload(); // Refresh to show empty chat
    }
    </script>

    <?php
    include __DIR__ . "/../includes/footer.php";
    ?>