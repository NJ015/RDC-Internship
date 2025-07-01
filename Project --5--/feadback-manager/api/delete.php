<?php

session_start();

require_once '../db/dbconfig.php';

header('Content-Type: application/json');

if($_SESSION['admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if(!isset($input['id'])){
    http_response_code(400);
    echo json_encode(['error' => 'Missing ID']);
    exit;
}

$id = (int)$input['id'];

$stmt = $conn->prepare("DELETE FROM feedback WHERE id=?");
$stmt->bind_param("i", $id);

$stmt->execute();

$results = $stmt->affected_rows;
if($results > 0) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Feedback deleted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete feedback or no such ID exists']);
}


$stmt->close();
$conn->close();
?>