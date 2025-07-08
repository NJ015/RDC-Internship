<?php
require_once '../db/dbconfig.php';

header('Content-Type: application/json');

$issueId = $_POST['issue_id'] ?? null;
$userId = $_POST['user_id'] ?? null;

if (!is_numeric($issueId) || !is_numeric($userId)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM issue_assignee WHERE issue_id = ? AND user_id = ?");
$stmt->bind_param("ii", $issueId, $userId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No such assignment']);
}
