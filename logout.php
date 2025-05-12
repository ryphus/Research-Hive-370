<?php
// ekhane database er shathe connect korse
include 'db_connect.php';
?>

<?php
session_start();
session_unset();
session_destroy();
header("Location: home.php");
exit();
?>