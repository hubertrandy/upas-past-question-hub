<?php
require __DIR__ . '/config/db.php';
require_role('admin');
$flash = take_flash();
$hist = $pdo->query('SELECT p.*, u.full_name FROM past_questions p JOIN users u ON u.id = p.uploaded_by ORDER BY p.uploaded_at DESC LIMIT 100')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin | UPSA Past Question Hub</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="admin">
<aside class="sidebar">
  <div class="logo-wrap"><img class="logo" src="assets/UPSALOGO.jpeg" alt="UPSA"></div>
  <nav>
    <a class="active" href="admin_dashboard.php">Upload &amp; manage</a>
    <a href="#history">Upload history</a>
    <a href="logout.php">Log out</a>
  </nav>
</aside>
<div class="admin-main">
  <header class="topbar light">
    <strong>Admin dashboard</strong>
    <span class="who"><?= e($_SESSION['user']['name']) ?></span>
  </header>
  <main class="container">
    <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>" role="alert"><?= e($flash['msg']) ?></div><?php endif; ?>

    <section class="panel">
      <h2>Upload a past question</h2>
      <form method="post" action="upload_process.php" enctype="multipart/form-data" class="upload-form" id="uploadForm">
        <?= csrf_field() ?>
        <div class="field"><label for="course_code">Course code</label><input id="course_code" name="course_code" placeholder="ITEC 201" required></div>
        <div class="field"><label for="course_title">Course title</label><input id="course_title" name="course_title" placeholder="Database Management" required></div>
        <div class="field"><label for="department">Department</label>
          <select id="department" name="department" required><option value="">Select department</option>
          <?php foreach (DEPARTMENTS as $d): ?><option><?= e($d) ?></option><?php endforeach; ?></select></div>
        <div class="field"><label for="semester">Semester</label>
          <select id="semester" name="semester" required><option value="">Select semester</option>
          <?php foreach (SEMESTERS as $s): ?><option><?= e($s) ?></option><?php endforeach; ?></select></div>
        <div class="field"><label for="academic_year">Academic year</label><input id="academic_year" name="academic_year" placeholder="2024/2025" pattern="\d{4}/\d{4}" required></div>
        <div class="field"><label for="paper_type">Paper type</label>
          <select id="paper_type" name="paper_type" required><option value="">Select paper type</option>
          <?php foreach (PAPER_TYPES as $t): ?><option><?= e($t) ?></option><?php endforeach; ?></select></div>

        <div class="dropzone" id="dropzone" tabindex="0">
          <input type="file" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required>
          <p><strong>Drag a file here</strong> or <u>choose a file</u></p>
          <small id="fileName">PDF, JPG, JPEG or PNG, up to 10 MB</small>
        </div>
        <button class="btn btn-primary" type="submit">Upload past question</button>
      </form>
    </section>

    <section class="panel" id="history">
      <h2>Upload history</h2>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Uploaded</th><th>Course</th><th>File</th><th>Year / semester</th><th>Status</th><th>Uploaded by</th></tr></thead>
          <tbody>
          <?php foreach ($hist as $h): ?>
            <tr>
              <td><?= e(date('d M Y, H:i', strtotime($h['uploaded_at']))) ?></td>
              <td><span class="badge badge-code"><?= e($h['course_code']) ?></span> <?= e($h['course_title']) ?></td>
              <td><?= e($h['file_name']) ?></td>
              <td><?= e($h['academic_year']) ?> / <?= e($h['semester']) ?></td>
              <td><span class="badge badge-ok">Published</span></td>
              <td><?= e($h['full_name']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$hist): ?><tr><td colspan="6" class="empty-cell">Nothing uploaded yet. Use the form above to add the first past question.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</div>
<script src="assets/app.js"></script>
</body>
</html>
