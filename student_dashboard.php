<?php
require __DIR__ . '/config/db.php';
require_role('student');
$rows = $pdo->query('SELECT id, course_code, course_title, department, semester, academic_year, paper_type FROM past_questions ORDER BY academic_year DESC, course_code')->fetchAll();
$years = array_values(array_unique(array_column($rows, 'academic_year')));
rsort($years);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Past Questions | UPSA Past Question Hub</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="student_dashboard.php"><img class="logo" src="assets/UPSALOGO.jpeg" alt="UPSA, University of Professional Studies, Accra"><span class="brand-text">Past Question Hub</span></a>
  <div class="topbar-right">
    <span class="who"><?= e($_SESSION['user']['name']) ?></span>
    <a class="btn btn-light" href="logout.php">Log out</a>
  </div>
</header>

<main class="container">
  <div class="page-head">
    <h1>Past questions</h1>
    <p>Search by course code or title, then filter by department, semester, year or paper type.</p>
  </div>

  <div class="filters">
    <input type="search" id="search" placeholder="Search e.g. ITEC 201 or Database Management" aria-label="Search past questions">
    <select id="f_dept" aria-label="Department">
      <option value="">All departments</option>
      <?php foreach (DEPARTMENTS as $d): ?><option><?= e($d) ?></option><?php endforeach; ?>
    </select>
    <select id="f_sem" aria-label="Semester">
      <option value="">All semesters</option>
      <?php foreach (SEMESTERS as $s): ?><option><?= e($s) ?></option><?php endforeach; ?>
    </select>
    <select id="f_year" aria-label="Academic year">
      <option value="">All years</option>
      <?php foreach ($years as $y): ?><option><?= e($y) ?></option><?php endforeach; ?>
    </select>
    <select id="f_type" aria-label="Paper type">
      <option value="">All paper types</option>
      <?php foreach (PAPER_TYPES as $t): ?><option><?= e($t) ?></option><?php endforeach; ?>
    </select>
    <button type="button" class="btn btn-light" id="reset">Reset filters</button>
  </div>

  <p class="count" id="count" aria-live="polite"></p>
  <div class="grid" id="grid">
    <?php foreach ($rows as $r): ?>
    <article class="card pq"
      data-search="<?= e(strtolower($r['course_code'] . ' ' . $r['course_title'])) ?>"
      data-dept="<?= e($r['department']) ?>" data-sem="<?= e($r['semester']) ?>"
      data-year="<?= e($r['academic_year']) ?>" data-type="<?= e($r['paper_type']) ?>">
      <span class="badge badge-code"><?= e($r['course_code']) ?></span>
      <h3><?= e($r['course_title']) ?></h3>
      <dl>
        <dt>Department</dt><dd><?= e($r['department']) ?></dd>
        <dt>Paper type</dt><dd><?= e($r['paper_type']) ?></dd>
        <dt>Semester</dt><dd><?= e($r['semester']) ?></dd>
        <dt>Academic year</dt><dd><?= e($r['academic_year']) ?></dd>
      </dl>
      <div class="actions">
        <a class="btn btn-primary" href="download.php?id=<?= (int)$r['id'] ?>&mode=view" target="_blank" rel="noopener">View</a>
        <a class="btn btn-outline" href="download.php?id=<?= (int)$r['id'] ?>&mode=download">Download</a>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
  <p class="empty hidden" id="empty">No past questions match your search. Try a different course code or reset the filters.</p>
</main>
<script src="assets/app.js"></script>
</body>
</html>
