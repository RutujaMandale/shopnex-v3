<?php
function requireAdminAuth() {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\s+(.+)/', $auth, $matches)) {
        http_response_code(401);
        die(json_encode(['error' => 'Unauthorized']));
    }

    $token = $matches[1];
    $secret = getenv('JWT_SECRET') ?: 'default_secret';

    // Simple JWT decode (header.payload.signature)
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        http_response_code(401);
        die(json_encode(['error' => 'Invalid token']));
    }

    $payload = json_decode(base64_decode(str_replace(['-','_'], ['+','/'], $parts[1])), true);

    // Verify signature
    $expectedSig = hash_hmac('sha256', $parts[0].'.'.$parts[1], $secret, true);
    $expectedSig = rtrim(strtr(base64_encode($expectedSig), '+/', '-_'), '=');

    if (!hash_equals($expectedSig, $parts[2])) {
        http_response_code(401);
        die(json_encode(['error' => 'Invalid token signature']));
    }

    if (isset($payload['exp']) && $payload['exp'] < time()) {
        http_response_code(401);
        die(json_encode(['error' => 'Token expired']));
    }

    return $payload;
}

function generateJWT($payload) {
    $secret = getenv('JWT_SECRET') ?: 'default_secret';
    $header = base64_encode(json_encode(['alg'=>'HS256','typ'=>'JWT']));
    $header = rtrim(strtr($header, '+/', '-_'), '=');
    $payload = base64_encode(json_encode($payload));
    $payload = rtrim(strtr($payload, '+/', '-_'), '=');
    $sig = hash_hmac('sha256', $header.'.'.$payload, $secret, true);
    $sig = rtrim(strtr(base64_encode($sig), '+/', '-_'), '=');
    return $header.'.'.$payload.'.'.$sig;
}
?>
