<?php
function getDB() {
    $host = getenv('DB_HOST') ?: 'localhost';
    $user = getenv('DB_USER') ?: 'shopnex_user';
    $pass = getenv('DB_PASS') ?: '';
    $name = getenv('DB_NAME') ?: 'shopnex_db';

    $conn = new mysqli($host, $user, $pass, $name);
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode(['error' => 'Database connection failed']));
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
?>
