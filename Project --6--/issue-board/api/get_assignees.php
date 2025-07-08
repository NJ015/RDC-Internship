<?php
require_once '../db/dbconfig.php';

header('Content-Type: application/json');

$issueId = $_GET['id'] ?? null;

if (!$issueId || !is_numeric($issueId)) {
    echo json_encode(['success' => false, 'message' => 'Invalid issue ID']);
    exit;
}

$sql = "SELECT users.id, users.username
        FROM issue_assignee
        JOIN users ON issue_assignee.user_id = users.id
        WHERE issue_assignee.issue_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $issueId);
$stmt->execute();
$result = $stmt->get_result();

$assignees = [];
while ($row = $result->fetch_assoc()) {
    $assignees[] = $row;
}

echo json_encode(['success' => true, 'assignees' => $assignees]);
