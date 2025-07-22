<?php 
session_start();
require("../includes/connect_db.php");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $answer_id = (int) $_GET['id'];

    // Step 1: Get the conversation_id for the answer before deleting
    $select_stmt = mysqli_prepare($conn, "SELECT conversation_id FROM answers WHERE id = ?");
    if ($select_stmt) {
        mysqli_stmt_bind_param($select_stmt, "i", $answer_id);
        mysqli_stmt_execute($select_stmt);
        mysqli_stmt_bind_result($select_stmt, $conversation_id);
        mysqli_stmt_fetch($select_stmt);
        mysqli_stmt_close($select_stmt);

        if (!empty($conversation_id)) {
            // Step 2: Delete the answer
            $delete_stmt = mysqli_prepare($conn, "DELETE FROM answers WHERE id = ?");
            if ($delete_stmt) {
                mysqli_stmt_bind_param($delete_stmt, "i", $answer_id);
                if (mysqli_stmt_execute($delete_stmt)) {
                    // Step 3: Redirect to conversation answers page
                    header("Location: http://localhost/tourism%20agency/dashboard/con_ans.php?id=" . urlencode($conversation_id));
                    exit;
                } else {
                    echo "Error deleting answer.";
                }
                mysqli_stmt_close($delete_stmt);
            } else {
                echo "Failed to prepare delete statement.";
            }
        } else {
            echo "Answer not found or conversation_id missing.";
        }
    } else {
        echo "Failed to prepare select statement.";
    }
} else {
    echo "Invalid ID.";
}
?>
