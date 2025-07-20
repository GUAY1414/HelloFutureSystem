<?php
session_start();
session_unset();             // remove all session variables
session_destroy();           // destroy session
setcookie(session_name(), '', time() - 3600);  // destroy session cookie
header("Location: index.php");
exit();