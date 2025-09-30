<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success'=>false,'message'=>'Invalid method'],405);

// auth
$headers = getallheaders();
$token = null;
if (!empty($headers['Authorization']) && preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $m)) $token = $m[1];
if (!$token) jsonResponse(['success'=>false,'message'=>'Unauthorized'],401);

$s = $redis->get("sess:$token");
if (!$s) jsonResponse(['success'=>false,'message'=>'Session expired'],401);
$session = json_decode($s, true);
$user_id = $session['user_id'];

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$name = trim($data['name'] ?? '');
$age = isset($data['age']) ? (int)$data['age'] : null;
$dob = $data['dob'] ?? null;
$contact = trim($data['contact'] ?? '');

$stmt = $pdo->prepare('UPDATE users SET name = ?, age = ?, dob = ?, contact = ? WHERE id = ?');
try {
    $stmt->execute([$name, $age, $dob, $contact, $user_id]);
    // optionally update session data
    $session['name'] = $name;
    $redis->set("sess:$token", json_encode($session));
    $redis->expire("sess:$token", 60*30); // refresh ttl
    jsonResponse(['success'=>true,'message'=>'Profile updated']);
} catch (Exception $e) {
    jsonResponse(['success'=>false,'message'=>'Update failed'],500);
}
