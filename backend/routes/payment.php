<?php
require_once __DIR__ . '/../app/db.php';

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$cart = $body['cart'] ?? [];
$name = trim($body['name'] ?? '');
$email = trim($body['email'] ?? '');
$address = trim($body['address'] ?? '');

if (empty($cart) || empty($name) || empty($email) || empty($address)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Server-side price calculation
$db = getDB();
$PRODUCT_PRICES = [];
$result = $db->query("SELECT id, price FROM products");
while ($row = $result->fetch_assoc()) {
    $PRODUCT_PRICES[(int)$row['id']] = (float)$row['price'];
}

$totalCents = 0;
foreach ($cart as $item) {
    $id = (int)($item['id'] ?? 0);
    $qty = (int)($item['qty'] ?? 0);
    if (!isset($PRODUCT_PRICES[$id]) || $qty < 1) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid cart item']);
        exit;
    }
    $totalCents += round($PRODUCT_PRICES[$id] * 100) * $qty;
}
$db->close();

// Stripe API call using PHP curl (no SDK needed)
$stripeKey = getenv('STRIPE_SECRET_KEY');

$ch = curl_init('https://api.stripe.com/v1/payment_intents');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $stripeKey . ':');
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'amount' => $totalCents,
    'currency' => 'usd',
    'description' => 'ShopNex order for ' . $name,
    'receipt_email' => $email,
    'metadata[name]' => $name,
    'metadata[address]' => $address,
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    error_log('Stripe error: ' . $response);
    http_response_code(500);
    echo json_encode(['error' => 'Payment initialization failed']);
    exit;
}

$intent = json_decode($response, true);
echo json_encode(['clientSecret' => $intent['client_secret']]);
?>
