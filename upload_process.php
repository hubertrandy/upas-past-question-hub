<?php
require __DIR__ . '/config/db.php';
require_role('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: admin_dashboard.php'); exit; }
csrf_check();

function back($type, $msg) { flash($type, $msg); header('Location: admin_dashboard.php'); exit; }

$code  = strtoupper(trim($_POST['course_code'] ?? ''));
$title = trim($_POST['course_title'] ?? '');
$dept  = $_POST['department'] ?? '';
$sem   = $_POST['semester'] ?? '';
$year  = trim($_POST['academic_year'] ?? '');
$type  = $_POST['paper_type'] ?? '';

if (!preg_match('/^[A-Z]{2,6}\s?\d{3}$/', $code)) back('error', 'Course code must look like ITEC 201.');
if ($title === '' || mb_strlen($title) > 150) back('error', 'Enter a course title.');
if (!in_array($dept, DEPARTMENTS, true) || !in_array($sem, SEMESTERS, true) || !in_array($type, PAPER_TYPES, true)) back('error', 'Choose a valid department, semester and paper type.');
if (!preg_match('/^\d{4}\/\d{4}$/', $year)) back('error', 'Academic year must look like 2024/2025.');

$f = $_FILES['file'] ?? null;
if (!$f || $f['error'] !== UPLOAD_ERR_OK) back('error', 'Choose a file to upload.');
if ($f['size'] > 10 * 1024 * 1024) back('error', 'File is larger than 10 MB.');

$allowed = ['pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
$ext  = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
if (!isset($allowed[$ext]) || $allowed[$ext] !== $mime) back('error', 'Only PDF, JPG, JPEG and PNG files are allowed.');

$dir = __DIR__ . '/uploads/';
if (!is_dir($dir)) mkdir($dir, 0755, true);
$safe = bin2hex(random_bytes(16)) . '.' . $ext;
if (!move_uploaded_file($f['tmp_name'], $dir . $safe)) back('error', 'Could not save the file. Check folder permissions.');

$original = substr(preg_replace('/[^\w.\- ]/u', '_', basename($f['name'])), 0, 200);
$stmt = $pdo->prepare('INSERT INTO past_questions (course_code, course_title, department, semester, academic_year, paper_type, file_name, file_path, uploaded_by) VALUES (?,?,?,?,?,?,?,?,?)');
$stmt->execute([$code, $title, $dept, $sem, $year, $type, $original, $safe, $_SESSION['user']['id']]);

back('success', "$code uploaded and published to students.");
