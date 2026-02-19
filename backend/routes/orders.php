<?php
$db = getDB();

if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);

    $name = trim($body['name'] ?? '');
    $email = trim($body['email'] ?? '');
    $address = trim($body['address'] ?? '');
    $cart = $body['cart'] ?? [];
    $paymentIntentId = $body['paymentIntentId'] ?? '';

    if (empty($name) || empty($email) || empty($cart)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    // Calculate total server-side
    $result = $db->query("SELECT id, price FROM products");
    $prices = [];
    while ($row = $result->fetch_assoc()) {
        $prices[(int)$row['id']] = (float)$row['price'];
    }

    $total = 0;
    foreach ($cart as $item) {
        $id = (int)($item['id'] ?? 0);
        $qty = (int)($item['qty'] ?? 0);
        if (isset($prices[$id])) $total += $prices[$id] * $qty;
    }

    // Save order
    $stmt = $db->prepare("INSERT INTO orders (customer_name, customer_email, customer_address, total_amount, payment_intent_id, payment_status) VALUES (?, ?, ?, ?, ?, 'paid')");
    $stmt->bind_param('sssds', $name, $email, $address, $total, $paymentIntentId);
    $stmt->execute();
    $orderId = $db->insert_id;

    // Save order items
    foreach ($cart as $item) {
        $id = (int)($item['id'] ?? 0);
        $qty = (int)($item['qty'] ?? 0);
        if (isset($prices[$id])) {
            $stmt2 = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param('iiid', $orderId, $id, $qty, $prices[$id]);
            $stmt2->execute();
        }
    }

    echo json_encode(['success' => true, 'orderId' => $orderId]);
}

$db->close();
?>
