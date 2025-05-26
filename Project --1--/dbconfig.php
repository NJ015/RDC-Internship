<?php
$host = 'localhost';
$db   = 'rdc_project_1';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;dbname=$db";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Error reporting
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch as associative arrays by default
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Force use native prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "success";
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
