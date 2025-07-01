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

$id = $input['id'];
$feedback = $input['feedback'];

if(!isset($input['id']) || !isset($feedback['userName']) || !isset($feedback['rating']) || !isset($feedback['feedbackText'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing fields']);
    exit;
}

$name = strip_tags(trim($feedback['userName']));
$rating = (int)$feedback['rating'];
$feedbackText = strip_tags(trim($feedback['feedbackText']));
if($rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Rating must be between 1 and 5']);
    exit;
}

if(empty($name) || empty($feedbackText)) {
    http_response_code(400);
    echo json_encode(['error' => 'Name and feedback cannot be empty']);
    exit;
}

$stmt = $conn->prepare("UPDATE feedback SET user=?, rating=?, description=? WHERE id=?");
$stmt->bind_param("sisi", $name, $rating, $feedbackText, $id);
$stmt->execute();

$results = $stmt->affected_rows;
if($results > 0) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Feedback updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update feedback or no changes made']);
}

$stmt->close();
$conn->close();