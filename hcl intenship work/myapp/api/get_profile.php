<?php
require_once 'config.php';

// get token from header
$headers = getallheaders();
$token = null;
if (!empty($headers['Authorization'])) {
    if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $m)) $token = $m[1];
}
if (!$token) jsonResponse(['success'=>false,'message'=>'Unauthorized'],401);

// fetch session
$s = $redis->get("sess:$token");
if (!$s) jsonResponse(['success'=>false,'message'=>'Session expired'],401);

$session = json_decode($s, true);
$user_id = $session['user_id'];

// fetch user data
$stmt = $pdo->prepare('SELECT id,email,name,age,dob,contact FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$user_id]);
$u = $stmt->fetch();
if (!$u) jsonResponse(['success'=>false,'message'=>'User not found'],404);

jsonResponse(['success'=>true,'user'=>$u]);
