<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();
$active = 'leads';

$action = (string)($_POST['action'] ?? '');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  if ($action === 'status' && $id) {
    $status = (string)($_POST['status'] ?? 'new');
    q("UPDATE leads SET status=? WHERE id=?", [$status, $id]);
  }
  if ($action === 'delete' && $id) {
    q("DELETE FROM leads WHERE id=?", [$id]);
  }
  redirect(url('admin/leads.php'));
}

$page = max(1, (int)($_GET['page'] ?? 1));
$per = 25;
$offset = ($page - 1) * $per;
$total = (int)(one("SELECT COUNT(*) AS c FROM leads")['c'] ?? 0);
$leads = all("SELECT * FROM leads ORDER BY id DESC LIMIT {$per} OFFSET {$offset}");
$pages = (int)ceil($total / $per);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Leads • DesertSafariGo</title>
  <link rel="stylesheet" href="<?= e(url('admin/assets/admin.css')) ?>">
</head>
<body>
  <?php require __DIR__ . '/partials/nav.php'; ?>
  <div style="padding:18px 0">
    <div class="container">
      <div class="card">
        <div class="row" style="justify-content:space-between">
          <div>
            <div class="h1" style="margin:0">Leads</div>
            <div class="small">All form submissions captured from the homepage.</div>
          </div>
          <div class="badge">Total: <?= $total ?></div>
        </div>

        <div style="overflow:auto; margin-top:12px">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Package</th><th>Trip date</th><th>Persons</th><th>Pref</th><th>Message</th><th>Status</th><th>Created</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($leads as $l): ?>
              <tr>
                <td><?= (int)$l['id'] ?></td>
                <td><?= e($l['full_name']) ?></td>
                <td><?= e($l['phone']) ?></td>
                <td><?= e($l['email']) ?></td>
                <td><?= e($l['package_name']) ?></td>
                <td><?= e($l['trip_date']) ?></td>
                <td><?= e($l['persons']) ?></td>
                <td><?= e($l['contact_pref']) ?></td>
                <td><?= e($l['message']) ?></td>
                <td>
                  <form method="post" class="row" style="gap:6px">
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
                    <select class="input" name="status" style="padding:8px 10px; border-radius:12px">
                      <?php foreach(['new','contacted','booked','closed'] as $s): ?>
                        <option value="<?= e($s) ?>" <?= $l['status']===$s?'selected':'' ?>><?= e($s) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button class="btn" type="submit">Save</button>
                  </form>
                </td>
                <td><?= e($l['created_at']) ?></td>
                <td>
                  <form method="post" onsubmit="return confirm('Delete lead #<?= (int)$l['id'] ?>?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
                    <button class="btn danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(!$leads): ?><tr><td colspan="12" class="small">No leads yet.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if($pages > 1): ?>
        <div class="row" style="margin-top:12px">
          <?php for($i=1; $i<=$pages; $i++): ?>
            <a class="btn <?= $i===$page?'primary':'' ?>" href="<?= e(url('admin/leads.php?page='.$i)) ?>"><?= $i ?></a>
          <?php endfor; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
