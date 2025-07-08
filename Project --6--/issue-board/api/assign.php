<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../db/dbconfig.php';

header('Content-Type: application/json');

$issue_id = isset($_POST['issue_id']) ? $_POST['issue_id'] : null;
$user_ids = isset($_POST['user_ids']) ? (array)$_POST['user_ids'] : [];

if (!$issue_id || empty($user_ids)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input. from php']);
    exit;
}

$stmt = $conn->prepare("INSERT IGNORE INTO issue_assignee (issue_id, user_id) VALUES (?, ?)");

$errors = [];
foreach ($user_ids as $uid) {
    if (!is_numeric($uid)) $errors[] = $uid." isnt a nb";
    $stmt->bind_param('ii', $issue_id, $uid);
    if (!$stmt->execute()) {
        $errors[] = $stmt->error;
    }
}

if (count($errors) > 0) {
    echo json_encode(['success' => false, 'message' => 'MySQL error(s): ' . implode('; ', $errors)]);
} else {
    echo json_encode(['success' => true]);
}