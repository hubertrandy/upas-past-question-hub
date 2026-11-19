<?php
require __DIR__ . '/config/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
csrf_check();

$email = strtolower(trim($_POST['email'] ?? ''));
$pass  = $_POST['password'] ?? '';

function fail($msg) { flash('error', $msg); header('Location: index.php?tab=login'); exit; }

// Students must use the ID format; staff/admin accounts use an @upsa.edu.gh address.
if (!preg_match(STUDENT_EMAIL_REGEX, $email) && !preg_match(STAFF_EMAIL_REGEX, $email)) {
    fail('Email must be your student ID followed by @upsamail.edu.gh, for example 10293847@upsamail.edu.gh.');
}

$stmt = $pdo->prepare('SELECT id, full_name, email, password, role FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($pass, $user['password'])) fail('Incorrect email or password.');

session_regenerate_id(true);
$_SESSION['user'] = ['id' => (int)$user['id'], 'name' => $user['full_name'], 'email' => $user['email'], 'role' => $user['role']];
header('Location: ' . ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'student_dashboard.php'));
