<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success'=>false,'message'=>'Invalid method'],405);
}

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
    jsonResponse(['success'=>false,'message'=>'Invalid credentials'],400);
}

$stmt = $pdo->prepare('SELECT id,password,name FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();
if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(['success'=>false,'message'=>'Invalid email or password'],401);
}

// create session token and store in Redis
$token = generateToken(64);
$redisKey = "sess:$token";
$sessionData = json_encode([
    'user_id' => $user['id'],
    'email' => $email,
    'name' => $user['name'],
    'created_at' => time()
]);

// TTL in seconds (e.g., 1800 = 30 minutes)
$ttl = 60 * 30;
$redis->set($redisKey, $sessionData);
$redis->expire($redisKey, $ttl);

// return token and minimal user info
jsonResponse([
    'success'=>true,
    'token' => $token,
    'user' => ['id'=>$user['id'], 'name'=>$user['name'], 'email'=>$email]
]);
