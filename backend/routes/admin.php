<?php
$db = getDB();

// POST /api/admin/login
if ($method === 'POST' && strpos($uri, '/api/admin/login') !== false) {
    $body = json_decode(file_get_contents('php://input'), true);
    $username = trim($body['username'] ?? '');
    $password = $body['password'] ?? '';

    $adminUser = getenv('ADMIN_USERNAME') ?: 'admin';
    $adminPass = getenv('ADMIN_PASSWORD') ?: 'Admin@2026';

    if ($username === $adminUser && $password === $adminPass) {
        $token = generateJWT([
            'sub' => $username,
            'exp' => time() + (4 * 3600),
            'iat' => time()
        ]);
        echo json_encode(['token' => $token]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
    exit;
}

// GET /api/admin/orders - protected
if ($method === 'GET' && strpos($uri, '/api/admin/orders') !== false) {
    requireAdminAuth();

    $orders = $db->query("
        SELECT o.*, GROUP_CONCAT(CONCAT(p.name, ' x', oi.quantity) SEPARATOR ', ') as items
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ")->fetch_all(MYSQLI_ASSOC);

    foreach ($orders as &$o) {
        $o['id'] = (int)$o['id'];
        $o['total_amount'] = (float)$o['total_amount'];
    }

    echo json_encode($orders);
    exit;
}

// GET /api/admin/stats - protected
if ($method === 'GET' && strpos($uri, '/api/admin/stats') !== false) {
    requireAdminAuth();

    $totalOrders = (int)$db->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
    $totalRevenue = (float)($db->query("SELECT SUM(total_amount) as s FROM orders WHERE payment_status='paid'")->fetch_assoc()['s'] ?? 0);
    $totalProducts = (int)$db->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];

    echo json_encode([
        'totalOrders' => $totalOrders,
        'totalRevenue' => $totalRevenue,
        'totalProducts' => $totalProducts
    ]);
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Route not found']);
$db->close();
?>
