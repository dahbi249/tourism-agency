<?php
session_start();
header('Content-Type: application/json');

// Initialize chat history if not exists
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Sanitize input
$input = json_decode(file_get_contents('php://input'));
$userMessage = filter_var($input->message, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Add user message to history
$_SESSION['chat_history'][] = [
    'role' => 'user',
    'content' => $userMessage
];

// System prompt
$systemPrompt = 'You are a tourism expert assistant. Respond in 4-10 very short sentences. Always put each sentence on a new line.';
// Prepare API request
$data = [
    'model' => 'gpt-4o-mini-2024-07-18',
    'messages' => [
        [
            'role' => 'system',
            'content' => $systemPrompt
        ],
        [
            'role' => 'user',
            'content' => $userMessage
        ]
    ]
];
// API call
$ch = curl_init('https://api.aimlapi.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer 075efa599f054567b2a79f3e44a142d4'
    ],
    CURLOPT_POSTFIELDS => json_encode($data)
]);
$response = curl_exec($ch);
curl_close($ch);
// Process response
$result = json_decode($response, true);
$botResponse = nl2br(htmlspecialchars(trim($result['choices'][0]['message']['content']), ENT_QUOTES, 'UTF-8'));

// Add bot response to history
$_SESSION['chat_history'][] = [
    'role' => 'assistant',
    'content' => $botResponse
];

echo json_encode(['reply' => $botResponse]);