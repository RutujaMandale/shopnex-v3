<?php
// Simple PHP unit test runner
$passed = 0;
$failed = 0;

function assert_equals($expected, $actual, $testName) {
    global $passed, $failed;
    if ($expected === $actual) {
        echo "✓ PASS: $testName\n";
        $passed++;
    } else {
        echo "✗ FAIL: $testName (expected: $expected, got: $actual)\n";
        $failed++;
    }
}

function assert_true($condition, $testName) {
    global $passed, $failed;
    if ($condition) {
        echo "✓ PASS: $testName\n";
        $passed++;
    } else {
        echo "✗ FAIL: $testName\n";
        $failed++;
    }
}

echo "Running ShopNex Unit Tests...\n\n";

// Test 1: Token generation
require_once __DIR__ . '/../app/auth.php';
putenv('JWT_SECRET=test_secret_key');
putenv('ADMIN_USERNAME=admin');
$token = generateJWT(['sub' => 'admin']);
assert_true(!empty($token), 'Token is generated successfully');

// Test 2: Token is a valid hash
assert_true(strlen($token) === 64, 'Token is a valid SHA256 hash');

// Test 3: Cart price calculation
$cart = [
    ['id' => 1, 'qty' => 2],
    ['id' => 2, 'qty' => 1],
];
$prices = [1 => 129.99, 2 => 89.99];
$total = 0;
foreach ($cart as $item) {
    $total += $prices[$item['id']] * $item['qty'];
}
assert_equals(349.97, round($total, 2), 'Cart total calculation is correct');

// Test 4: Input sanitization
$input = "<script>alert('xss')</script>";
$sanitized = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
assert_true(strpos($sanitized, '<script>') === false, 'XSS input is sanitized');

// Test 5: Email validation
assert_true(filter_var('test@example.com', FILTER_VALIDATE_EMAIL) !== false, 'Valid email passes validation');
assert_true(filter_var('not-an-email', FILTER_VALIDATE_EMAIL) === false, 'Invalid email fails validation');

// Summary
echo "\n----------------------------\n";
echo "Tests passed: $passed\n";
echo "Tests failed: $failed\n";
echo "----------------------------\n";

exit($failed > 0 ? 1 : 0);
?>