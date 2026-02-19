<?php
$db = getDB();

// GET /api/products - list all or filter by category
if ($method === 'GET') {
    $category = $_GET['category'] ?? 'all';

    if ($category !== 'all') {
        $stmt = $db->prepare("SELECT * FROM products WHERE category = ?");
        $stmt->bind_param('s', $category);
        $stmt->execute();
        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $products = $db->query("SELECT * FROM products ORDER BY id")->fetch_all(MYSQLI_ASSOC);
    }

    // Cast types
    foreach ($products as &$p) {
        $p['id'] = (int)$p['id'];
        $p['price'] = (float)$p['price'];
        $p['stock'] = (int)$p['stock'];
        $p['reviews'] = (int)$p['reviews'];
    }

    echo json_encode($products);
}

// GET /api/products/{id}
elseif ($method === 'GET' && preg_match('/\/api\/products\/(\d+)/', $uri, $m)) {
    $id = (int)$m[1];
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    } else {
        $product['id'] = (int)$product['id'];
        $product['price'] = (float)$product['price'];
        echo json_encode($product);
    }
}

$db->close();
?>
