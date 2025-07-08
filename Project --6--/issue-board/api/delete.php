<?php

require_once '../db/dbconfig.php';

header('Content-Type: application/json');


if (!isset($_POST['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$id = intval($_POST['id']);

$stmt = $conn->prepare("DELETE FROM issues WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Issue deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting issue']);
}