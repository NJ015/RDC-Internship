<?php
// Database connection
$host = 'localhost'; // Database host
$username = 'root'; // Database username
$password = ''; // Database password
$database = 'livenotes'; // Database name

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // echo "Connected successfully";
}

// Get the action from the request

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_desc';
    $orderBy = "id DESC";
    if ($sort == 'created_asc') $orderBy = "id ASC";
    if ($sort == 'updated_desc') $orderBy = "updated_at DESC";
    if ($sort == 'updated_asc') $orderBy = "updated_at ASC";
    if ($sort == 'title_asc') $orderBy = "title ASC";
    if ($sort == 'title_desc') $orderBy = "title DESC";

    $orderBy = "ORDER BY pinned DESC, $orderBy";

    if ($search !== '') {
        $sql = "SELECT * FROM notes WHERE title LIKE '%$search%' OR content LIKE '%$search%' $orderBy";
    } else {
        $sql = "SELECT * FROM notes $orderBy";
    }

    $result = $conn->query($sql);
    $notes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }
    }
    header('Content-Type: application/json');
    echo json_encode($notes);
    exit;
} elseif ($method == 'POST' && isset($_POST['id']) && isset($_POST['title']) && isset($_POST['content'])) {
    $id = intval($_POST['id']);
    $title = $_POST['title'];
    $content = $_POST['content'];
    $sql = "UPDATE notes SET title = ?, content = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $content, $id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit;
} elseif ($method == 'POST' && isset($_POST['pin_id'])) {
    $pin_id = intval($_POST['pin_id']);
    $status = intval($_POST['status']);
    $sql = "UPDATE notes SET pinned = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $status, $pin_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
        exit;
    }
} elseif ($method == 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM notes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} elseif ($method == 'POST') {
    if (!isset($_POST['title']) || !isset($_POST['content'])) {
        echo json_encode(['success' => false, 'error' => 'Missing data']);
        exit;
    }
    $sql = "INSERT INTO notes (title, content) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $_POST['title'], $_POST['content']);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo "Invalid action";
}
