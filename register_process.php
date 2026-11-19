<?php
require __DIR__ . '/config/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?tab=register'); exit; }
csrf_check();

$name  = trim($_POST['full_name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$pass  = $_POST['password'] ?? '';
$conf  = $_POST['confirm'] ?? '';

function fail($msg) { flash('error', $msg); header('Location: index.php?tab=register'); exit; }

if ($name === '' || mb_strlen($name) > 120) fail('Enter your full name.');
if (!preg_match(STUDENT_EMAIL_REGEX, $email)) fail('Email must be your student ID followed by @upsamail.edu.gh, for example 10293847@upsamail.edu.gh.');
if (strlen($pass) < 8) fail('Password must be at least 8 characters.');
if ($pass !== $conf) fail('Passwords do not match.');

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) fail('An account with this email already exists.');

$ins = $pdo->prepare('INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, "student")');
$ins->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);

flash('success', 'Account created. You can log in now.');
header('Location: index.php?tab=login');
