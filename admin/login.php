<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

start_session();
if (admin_user()) {
  redirect(url('admin/'));
}

$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $email = trim((string)($_POST['email'] ?? ''));
  $password = (string)($_POST['password'] ?? '');
  if (login_admin($email, $password)) {
    redirect(url('admin/'));
  } else {
    $error = 'Invalid credentials.';
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Login • DesertSafariGo</title>
  <link rel="stylesheet" href="<?= e(url('admin/assets/admin.css')) ?>">
</head>
<body>
  <div class="container" style="padding:40px 0">
    <div class="card" style="max-width:520px; margin:0 auto">
      <div class="h1">Admin Login</div>
      <div class="small">Login to manage content for <strong>desertsafarigo.com</strong>.</div>
      <?php if($error): ?><div class="card" style="margin-top:12px; border-color: rgba(180,40,50,.25); background: rgba(180,40,50,.06)"><?= e($error) ?></div><?php endif; ?>
      <form method="post" style="margin-top:12px">
        <div class="grid">
          <div>
            <label class="small">Email</label>
            <input class="input" name="email" type="email" required>
          </div>
          <div>
            <label class="small">Password</label>
            <input class="input" name="password" type="password" required>
          </div>
        </div>
        <div style="margin-top:14px; display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap">
          <button class="btn primary" type="submit">Login</button>
          <div class="small">If not installed, go to <a class="btn" href="<?= e(url('install/')) ?>">Installer</a></div>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
