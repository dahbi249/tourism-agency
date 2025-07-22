<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Check if user is logged in
    if (!isset($_SESSION['CustomerID'])) {
        // Store FULL URL
        header('Location: http://localhost/tourism%20agency/auth/login.php');
        exit();
    }
    $comment = mysqli_real_escape_string($conn, $_POST["comment"]);
    $rating = (int)$_POST['rate'];
    $stmt = mysqli_prepare($conn, "INSERT INTO review (CustomerID, EntityID, EntityType, Comment, Rate) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iissi", $_SESSION["CustomerID"], $EntityID, $EntityType, $comment, $rating);
    if (mysqli_stmt_execute($stmt)) {
        // Redirect to prevent form resubmission
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        // Add error handling
        die("Query failed: " . mysqli_error($conn));
    }
}
require("../includes/connect_db.php");

$stmt = mysqli_prepare($conn, "SELECT * FROM review JOIN customer ON review.CustomerID = customer.CustomerID WHERE EntityID = ? AND EntityType = ?");

mysqli_stmt_bind_param($stmt, "is", $EntityID, $EntityType);

if (mysqli_stmt_execute($stmt)) {
    $reviewsResult = mysqli_stmt_get_result($stmt);
} else {
    // Add error handling
    die("Query failed: " . mysqli_error($conn));
}
