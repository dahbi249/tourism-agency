<?php
session_start();
unset($_SESSION['chat_history']);
header('Location: http://localhost/tourism%20agency/main/ai.php'); // Redirect back to chat