<?php
require_once '../db/dbconfig.php';

header('Content-Type: application/json');

$issue_id = $_GET['issue_id'] ?? null;

if (!is_numeric($issue_id)) {
    echo json_encode([]);
    exit;
}

// Get users NOT already assigned
$sql = "SELECT id, username FROM users 
        WHERE id NOT IN (
            SELECT user_id FROM issue_assignee WHERE issue_id = ?
        )
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $issue_id);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
