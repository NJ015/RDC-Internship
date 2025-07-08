<?php

require_once '../db/dbconfig.php';

header('Content-Type: application/json');

$query = "SELECT id, name FROM projects";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $projects = [];
    while ($row = $result->fetch_assoc()) {
        $projects[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }
    echo json_encode(['success' => true, 'projects' => $projects]);
} else {
    echo json_encode(['success' => false, 'message' => 'No projects found']);
}