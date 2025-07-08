<?php

require_once '../db/dbconfig.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$id = intval($_POST['id']);
$title = trim($_POST['title']);
$description = trim($_POST['description']);
$project_id = intval($_POST['project_id']);
$priority = intval($_POST['priority']);
$status = intval($_POST['status']);

if ($id <= 0 || empty($title) || empty($description) || $project_id <= 0 || $priority < 0 || $priority > 3 || $status < 0 || $status > 3) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Update the issue
$query = "UPDATE issues SET title = ?, description = ?, project_id = ?, priority = ?, status = ?, updated_at = NOW() WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssiiii", $title, $description, $project_id, $priority, $status, $id);

if ($stmt->execute()) {
    // Fetch updated data
    $q2 = "SELECT issues.*, projects.name AS project_name FROM issues
           JOIN projects ON issues.project_id = projects.id
           WHERE issues.id = ?";
    $stmt2 = $conn->prepare($q2);
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $result = $stmt2->get_result();

    if ($issue = $result->fetch_assoc()) {
        // Add extra fields if needed
        $priority_names = ['Low', 'Medium', 'High', 'Critical'];
        $issue['priority_name'] = $priority_names[$issue['priority']] ?? 'Unknown';
        $issue['assignees'] = []; // You can fill this later if needed
        echo json_encode(['success' => true, 'issue' => $issue]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Issue updated, but fetch failed']);
    }

    $stmt2->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update issue']);
}

$stmt->close();
$conn->close();
