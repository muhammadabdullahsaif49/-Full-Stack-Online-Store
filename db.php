<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "registration";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// WAMP mein mysqli strict exception mode off karo
// Warna SQL errors page crash kar dete hain
mysqli_report(MYSQLI_REPORT_OFF);
?>