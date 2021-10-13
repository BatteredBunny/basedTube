<?php
session_start();

unset($_SESSION['user-id']);
echo "<p>Logged out</p>";
header('Location: /');
exit();
?>