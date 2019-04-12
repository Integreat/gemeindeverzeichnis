<?php
global $conn;
$conn = new mysqli($cfg['host'], $cfg['user'], $cfg['password'], $cfg['database']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>