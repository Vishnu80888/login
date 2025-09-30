<?php
// config.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$DB_HOST = '127.0.0.1';
$DB_NAME = 'myapp';
$DB_USER = 'mydbuser';
$DB_PASS = 'mydbpass';
$DB_DSN  = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// Create PDO
try {
    $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success'=>false, 'message'=>'Database connection failed']);
    exit;
}

// Redis connection (phpredis extension)
// Alternative: use Predis via composer if phpredis not available.
$redis = new Redis();
try {
    $redis->connect('127.0.0.1', 6379); // adjust host/port
    // If Redis has auth: $redis->auth('password');
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false, 'message'=>'Redis connection failed']);
    exit;
}

// Helpers
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function generateToken($length = 40) {
    return bin2hex(random_bytes($length/2));
}
