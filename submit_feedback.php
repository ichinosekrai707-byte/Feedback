<?php
// ====================================================================
// FILE: submit_feedback.php
// ====================================================================
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input text bounds to guard against white space tricks
    $name = trim($_POST['name']);
    $message = trim($_POST['message']);
    
    // Assign structural foreign keys if an authenticated session context is present
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Default username to Anonymous if field evaluation fails or is empty
    if (empty($name)) {
        $name = "Anonymous";
    }

    // Test Case 4 Validation: Check for a missing/blank message payload
    if (empty($message)) {
        $_SESSION['msg'] = "The feedback message field cannot be blank.";
        $_SESSION['msg_type'] = "warning";
        header("Location: index.php");
        exit();
    }

    // Test Case 5 Validation: Enforce structural character bounds constraints (Minimum 10 characters)
    if (strlen($message) < 10) {
        $_SESSION['msg'] = "Your feedback text is too short! It must contain at least 10 characters.";
        $_SESSION['msg_type'] = "warning";
        header("Location: index.php");
        exit();
    }

    // Test Case 6 Validation: Data persistence pipeline using safe parameters
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, name, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $name, $message);

    if ($stmt->execute()) {
        $_SESSION['msg'] = "Thank you! Your feedback has been saved successfully.";
        $_SESSION['msg_type'] = "success";
    } else {
        // Fallback catch configuration routing paths if system errors trigger
        $_SESSION['msg'] = "A critical system database processing error occurred. Please try again.";
        $_SESSION['msg_type'] = "error";
    }

    $stmt->close();
}

$conn->close();
header("Location: index.php");
exit();
?>
