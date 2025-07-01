<?php 

require_once '../db/dbconfig.php';

header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT id, user, rating, description FROM feedback ORDER BY id DESC");
$stmt->execute();

$result = $stmt->get_result();
$feedbacks = [];
while ($row = $result->fetch_assoc()) {
    $feedbacks[] = [
        'id' => $row['id'],
        'userName' => $row['user'],
        'rating' => (int)$row['rating'],
        'feedbackText' => $row['description']
    ];
}

echo json_encode($feedbacks);

$stmt->close();
$conn->close();