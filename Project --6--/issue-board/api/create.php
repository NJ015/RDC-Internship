<?php

require_once '../db/dbconfig.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}


$title = trim($_POST['title']);
$description = trim($_POST['description']);
$project_id = intval($_POST['project_id']);
$priority = intval($_POST['priority']);
$status = intval($_POST['status']);

if (empty($title) || empty($description) || $project_id <= 0 || $status < 0 || $priority < 0 || $priority > 3 || $status > 3) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$query = "INSERT INTO issues (title, description, project_id, priority, status) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssiii", $title, $description, $project_id, $priority, $status);

if ($stmt->execute()) {
    $new_id = $conn->insert_id;

    $q2 = "SELECT projects.name AS project_name
    FROM projects
           WHERE projects.id = ?";
    $stmt2 = $conn->prepare($q2);
    $stmt2->bind_param("i", $project_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $proj_name = $result2->fetch_assoc()['project_name'];

    $issue = [
        'id' => $new_id,
        'title' => $title,
        'description' => $description,
        'project_id' => $project_id,
        'priority' => $priority,
        'status' => $status,
        'project_name' => $proj_name,
        'assignees' => []
    ];

    echo json_encode(['success' => true, 'issue' => $issue]);

} else {
    echo json_encode(['success' => false, 'message' => 'Error creating issue']);
}


$stmt->close();
$stmt2->close();
$conn->close();
