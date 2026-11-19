<?php
require __DIR__ . '/config/db.php';
if (!empty($_SESSION['user'])) {
    header('Location: ' . ($_SESSION['user']['role'] === 'admin' ? 'admin_dashboard.php' : 'student_dashboard.php'));
    exit;
}
$flash = take_flash();
$tab = ($_GET['tab'] ?? 'login') === 'register' ? 'register' : 'login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>UPSA Past Question Hub</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
<main class="auth">
  <section class="auth-brand">
    <div class="upsa-mark">UPSA</div>
    <h1>Past Question Hub</h1>
    <p>Find, view and download past exam papers for your courses, all in one place.</p>
  </section>
  <section class="auth-panel">
    <div class="auth-card">
      <div class="tabs" role="tablist">
        <button type="button" class="tab <?= $tab==='login'?'active':'' ?>" data-tab="login" role="tab">Log in</button>
        <button type="button" class="tab <?= $tab==='register'?'active':'' ?>" data-tab="register" role="tab">Create account</button>
      </div>
      <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>" role="alert"><?= e($flash['msg']) ?></div><?php endif; ?>

      <form id="loginForm" class="form <?= $tab==='login'?'':'hidden' ?>" method="post" action="login_process.php" novalidate>
        <?= csrf_field() ?>
        <label for="l_email">Student email</label>
        <input type="email" id="l_email" name="email" placeholder="10293847@upsamail.edu.gh" required>
        <small class="err" data-for="l_email"></small>
        <label for="l_pass">Password</label>
        <input type="password" id="l_pass" name="password" required>
        <small class="err" data-for="l_pass"></small>
        <button class="btn btn-primary" type="submit">Log in</button>
      </form>

      <form id="registerForm" class="form <?= $tab==='register'?'':'hidden' ?>" method="post" action="register_process.php" novalidate>
        <?= csrf_field() ?>
        <label for="r_name">Full name</label>
        <input type="text" id="r_name" name="full_name" required>
        <small class="err" data-for="r_name"></small>
        <label for="r_email">Student email</label>
        <input type="email" id="r_email" name="email" placeholder="10293847@upsamail.edu.gh" required>
        <small class="hint">Use your student ID, for example 10293847@upsamail.edu.gh</small>
        <small class="err" data-for="r_email"></small>
        <label for="r_pass">Password</label>
        <input type="password" id="r_pass" name="password" minlength="8" required>
        <small class="err" data-for="r_pass"></small>
        <label for="r_conf">Confirm password</label>
        <input type="password" id="r_conf" name="confirm" required>
        <small class="err" data-for="r_conf"></small>
        <button class="btn btn-primary" type="submit">Create account</button>
      </form>
    </div>
  </section>
</main>
<script src="assets/app.js"></script>
</body>
</html>
