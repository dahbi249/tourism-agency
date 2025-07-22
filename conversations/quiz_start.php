<?php
include __DIR__ . '/../includes/header.php';require("../includes/connect_db.php");

$conversation_id = $_GET['id'] ?? 1;

// Get 10 random questions
$stmt = mysqli_prepare($conn, "SELECT * FROM answers WHERE conversation_id = ? ORDER BY RAND() LIMIT 10");
mysqli_stmt_bind_param($stmt, "i", $conversation_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$_SESSION['quiz'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
$_SESSION['current_q'] = 0;
$_SESSION['score'] = 0;

header("Location: quiz.php");
exit;
