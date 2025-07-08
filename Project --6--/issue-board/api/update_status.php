<?php

require_once '../db/dbconfig.php';


header('Content-Type: application/json');



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;

// Validate
if (!is_numeric($id) || !is_numeric($status)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Prepare and execute update
$stmt = $conn->prepare("UPDATE issues SET status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param('ii', $status, $id);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
    exit;
}

$stmt->close();

// Get updated issue
$stmt2 = $conn->prepare("SELECT issues.*, projects.name AS project_name
                         FROM issues
                         JOIN projects ON issues.project_id = projects.id
                         WHERE issues.id = ?");
$stmt2->bind_param('i', $id);
$stmt2->execute();

$result = $stmt2->get_result();
$updatedIssue = $result->fetch_assoc();

echo json_encode([
    'success' => true,
    'issue' => $updatedIssue
]);

$stmt2->close();
$conn->close();
