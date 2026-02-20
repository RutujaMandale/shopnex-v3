<?php
function getDB() {
    $host = getenv('DB_HOST') ?: 'localhost';
    $user = getenv('DB_USER') ?: 'shopnex_user';
    $pass = getenv('DB_PASS') ?: '';
    $name = getenv('DB_NAME') ?: 'shopnex_db';

    // Connect without database first to create it if missing
    $conn = new mysqli($host, $user, $pass);
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
    }
    $conn->set_charset('utf8mb4');
    
    // Create database if it doesn't exist
    $conn->query("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4");
    
    // Select the database
    $conn->select_db($name);
    
    return $conn;
}
?>