<?php
// Start session
session_start();

// Destroy session data
session_unset();
session_destroy();

// Redirect to the sign-in page or any other page as desired
header("Location: index.php");
exit();
?>
