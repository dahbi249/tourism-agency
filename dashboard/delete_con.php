<?php
session_start();
require("../includes/connect_db.php");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $conID = (int)$_GET['id'];

    // Optional: delete associated answers too
    $stmt = mysqli_prepare($conn, "DELETE FROM answers WHERE conversation_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $conID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Delete the conversation itself
    $stmt = mysqli_prepare($conn, "DELETE FROM conversations WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $conID);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION["success"] = "Conversation deleted.";
    } else {
        $_SESSION["error"] = "Failed to delete.";
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION["error"] = "Invalid ID.";
}

header("Location: manage_conversations.php");
exit;
?>
