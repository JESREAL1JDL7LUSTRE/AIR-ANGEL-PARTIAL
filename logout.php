<?php
session_start();
session_unset();
session_destroy();
header("Location: signin.php");  // Redirect to sign-in page
exit();
?>
