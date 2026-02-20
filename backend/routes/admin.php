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

// GET /api/admin/stats
if ($method === 'GET' && strpos($uri, '/api/admin/stats') !== false) {
    $payload = requireAdminAuth();

    $totalOrders = (int)$db->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
    $totalRevenue = (float)($db->query("SELECT COALESCE(SUM(total_amount),0) as s FROM orders WHERE payment_status='paid'")->fetch_assoc()['s'] ?? 0);
    $totalProducts = (int)$db->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];

    echo json_encode([
        'totalOrders' => $totalOrders,
        'totalRevenue' => $totalRevenue,
        'totalProducts' => $totalProducts
    ]);
    $db->close();
    exit;
}

// GET /api/admin/orders
if ($method === 'GET' && strpos($uri, '/api/admin/orders') !== false) {
    $payload = requireAdminAuth();

    $result = $db->query("
        SELECT o.id, o.customer_name, o.customer_email, o.customer_address,
               o.total_amount, o.payment_status, o.created_at,
               GROUP_CONCAT(CONCAT(p.name, ' x', oi.quantity) SEPARATOR ', ') as items
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $row['id'] = (int)$row['id'];
        $row['total_amount'] = (float)$row['total_amount'];
        $orders[] = $row;
    }

    echo json_encode($orders);
    $db->close();
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Route not found']);
$db->close();