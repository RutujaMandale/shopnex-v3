<?php
// Main API Router
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Health check
if ($uri === '/health') {
    echo json_encode(['status' => 'ok', 'timestamp' => date('c')]);
    exit;
}

// Route requests
if (strpos($uri, '/api/products') === 0) {
    require __DIR__ . '/../routes/products.php';
} elseif (strpos($uri, '/api/payment') === 0) {
    require __DIR__ . '/../routes/payment.php';
} elseif (strpos($uri, '/api/orders') === 0) {
    require __DIR__ . '/../routes/orders.php';
} elseif (strpos($uri, '/api/admin') === 0) {
    require __DIR__ . '/../routes/admin.php';
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}
?>
