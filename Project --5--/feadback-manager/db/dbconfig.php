<?php

$conn = new mysqli('localhost', 'root', '', 'feedback_manager');

if ($conn->connect_error) {
    die('Connect Error: ' . $conn->connect_error);
}
