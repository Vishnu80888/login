<?php
require_once 'config.php';
$headers = getallheaders();
$token = null;
if (!empty($headers['Authorization'])) {
    if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $m)) $token = $m[1];
}
if (!$token) {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $token = $input['token'] ?? null;
}
if ($token) {
    $redis->del("sess:$token");
}
jsonResponse(['success'=>true,'message'=>'Logged out']);
