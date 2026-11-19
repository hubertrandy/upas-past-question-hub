<?php
require __DIR__ . '/config/db.php';
if (empty($_SESSION['user'])) { http_response_code(403); exit('Please log in.'); }

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT course_code, academic_year, file_name, file_path FROM past_questions WHERE id = ?');
$stmt->execute([$id]);
$q = $stmt->fetch();

$full = $q ? __DIR__ . '/uploads/' . basename($q['file_path']) : '';
if (!$q || !is_file($full)) { http_response_code(404); exit('File not found.'); }

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($full);
$ext  = pathinfo($full, PATHINFO_EXTENSION);
$nice = preg_replace('/[^A-Za-z0-9_-]/', '_', $q['course_code'] . '_' . $q['academic_year']) . '.' . $ext;
$disp = (($_GET['mode'] ?? '') === 'download') ? 'attachment' : 'inline';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($full));
header('Content-Disposition: ' . $disp . '; filename="' . $nice . '"');
header('X-Content-Type-Options: nosniff');
readfile($full);
