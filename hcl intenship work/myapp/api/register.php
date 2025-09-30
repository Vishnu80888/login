<?php
require_once 'config.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success'=>false,'message'=>'Invalid method'],405);
}

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$name = trim($data['name'] ?? '');
$age = isset($data['age']) ? (int)$data['age'] : null;
$dob = $data['dob'] ?? null;
$contact = trim($data['contact'] ?? '');

// basic server-side validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8 || empty($name)) {
    jsonResponse(['success'=>false,'message'=>'Invalid input'],400);
}

// check email exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    jsonResponse(['success'=>false,'message'=>'Email already registered'],409);
}

// hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// insert
$stmt = $pdo->prepare('INSERT INTO users (email,password,name,age,dob,contact) VALUES (?,?,?,?,?,?)');
try {
    $stmt->execute([$email, $hash, $name, $age, $dob, $contact]);
    jsonResponse(['success'=>true,'message'=>'Registered successfully']);
} catch (Exception $e) {
    jsonResponse(['success'=>false,'message'=>'Registration error'],500);
}
