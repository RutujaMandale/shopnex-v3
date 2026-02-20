<?php
function requireAdminAuth() {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (empty($auth)) {
        http_response_code(401);
        die(json_encode(['error' => 'Unauthorized - no token']));
    }

    // Simple token check
    $token = str_replace('Bearer ', '', $auth);
    $expected = hash('sha256', (getenv('ADMIN_USERNAME') ?: 'admin') . (getenv('JWT_SECRET') ?: 'secret'));

    if ($token !== $expected) {
        http_response_code(401);
        die(json_encode(['error' => 'Unauthorized - invalid token']));
    }

    return true;
}

function generateJWT($payload) {
    $username = getenv('ADMIN_USERNAME') ?: 'admin';
    $secret = getenv('JWT_SECRET') ?: 'secret';
    // Return simple hash token
    return hash('sha256', $username . $secret);
}
?>