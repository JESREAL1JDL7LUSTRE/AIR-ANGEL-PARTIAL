<?php
session_start();
session_unset();
session_destroy();
header("Location: noacc_dashboard.php");  // Redirect to sign-in page
exit();
?>
