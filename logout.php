<?php
// ====================================================================
// FILE: logout.php
// ====================================================================
session_start();

// Destroy all session variable metrics cleanly
$_SESSION = array();
session_destroy();

// Restart an active session pipeline immediately to dispatch the tracking alert
session_start();
$_SESSION['msg'] = "You have been logged out successfully.";
$_SESSION['msg_type'] = "success";

header("Location: index.php");
exit();
?>
