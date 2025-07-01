<?php

session_start();

require_once '../db/dbconfig.php';

// header('Content-Type: application/json');

// $input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = $_POST;

    if (!isset($input['username']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing credentials']);
        exit;
    }

    $username = strip_tags(trim($input['username']));
    $password = strip_tags(trim($input['password']));

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password cannot be empty']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id,password FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    
    $result = $stmt->get_result();
    if($row=$result->fetch_assoc()) {
        if($row['password'] === $password){
            $_SESSION['admin'] = true;
            http_response_code(200);
            // echo json_encode(['message' => 'Login successful']);
            header('Location: ../index.php'); // Redirect to main page
        exit;
        } else {
            http_response_code(401);
            // echo json_encode(['error' => 'Invalid username or password']);
            header('Location: ../index.php'); // Redirect to main page
        exit;
        }
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid username or password']);
    }

    $stmt->close();
    $conn->close();
}
