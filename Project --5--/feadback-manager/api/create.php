<?php

require_once '../db/dbconfig.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if(!isset($input['userName']) || !isset($input['rating']) || !isset($input['feedbackText'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing feilds']);
    exit;
}

$name = strip_tags(trim($input['userName']));
$rating = strip_tags($input['rating']);
$feedback = strip_tags(trim($input['feedbackText']));

if($rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Rating must be between 1 and 5']);
    exit;
}

if(empty($name) || empty($feedback)) {
    http_response_code(400);
    echo json_encode(['error' => 'Name and feedback cannot be empty']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO feedback (user, rating, description) VALUES (?,?,?)");
$stmt->bind_param("sis", $name, $rating, $feedback);
$stmt->execute();

if($stmt->affected_rows > 0) {
    http_response_code(201);
    echo json_encode(['success' => true,
    'id' => $stmt->insert_id,
        'message' => 'Feedback submitted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to submit feedback']);
}

$stmt->close();
$conn->close();