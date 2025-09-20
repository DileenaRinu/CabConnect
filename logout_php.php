<?php
session_start();
session_destroy();
header("Location: unified_login.php");
exit;
?>