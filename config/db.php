<?php
// Database connection, session bootstrap and shared helpers.
const DB_HOST = 'localhost';
const DB_NAME = 'upsa_hub';
const DB_USER = 'root';
const DB_PASS = '';

const DEPARTMENTS = ['Information Technology', 'Accounting', 'Banking and Finance', 'Marketing', 'Management', 'Law', 'Public Administration', 'Procurement and Supply Chain'];
const SEMESTERS   = ['Semester 1', 'Semester 2'];
const PAPER_TYPES = ['End of Semester', 'Mid-Semester', 'Quiz', 'Resit'];
const STUDENT_EMAIL_REGEX = '/^[0-9]+@upsamail\.edu\.gh$/';
const STAFF_EMAIL_REGEX   = '/^[A-Za-z0-9._-]+@upsa\.edu\.gh$/';

session_start();

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed.');
}

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403);
        exit('Invalid request token.');
    }
}

function flash($type, $msg) { $_SESSION['flash'] = ['type' => $type, 'msg' => $msg]; }
function take_flash() { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }

function require_role($role) {
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {
        header('Location: index.php');
        exit;
    }
}
