<?php
session_start();
header('Content-Type: application/json');

$history = isset($_SESSION['chat_history']) ? $_SESSION['chat_history'] : [];
echo json_encode($history);