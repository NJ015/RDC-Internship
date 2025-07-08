<?php

$username = 'root';
$password = '';
$database = 'issue_board';
$host = 'localhost';

$conn = new mysqli($host, $username, $password, $database);

if (!$conn) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

