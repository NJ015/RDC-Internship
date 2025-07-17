<?php

require_once '../db/dbconfig.php';

header('Content-Type: application/json');

$query = "SELECT id, username FROM users";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'id' => $row['id'],
            'name' => $row['username']
        ];
    }
    echo json_encode(['success' => true, 'assignees' => $users]);
} else {
    echo json_encode(['success' => false, 'message' => 'No users found']);
}