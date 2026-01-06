<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();
$active = 'toast';

$editId = (int)($_GET['edit'] ?? 0);
$editing = $editId ? one("SELECT * FROM toast_items WHERE id=?", [$editId]) : null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $action = (string)($_POST['action'] ?? '');
  if ($action === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim((string)($_POST['title'] ?? ''));
    $subtitle = trim((string)($_POST['subtitle'] ?? ''));
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($title === '') redirect(url('admin/toast.php'));

    if ($id) {
      q("UPDATE toast_items SET title=?, subtitle=?, is_active=? WHERE id=?",
        [$title,$subtitle,$is_active,$id]);
    } else {
      q("INSERT INTO toast_items(title, subtitle, is_active, created_at) VALUES(?,?,?,?)",
        [$title,$subtitle,$is_active,date('Y-m-d H:i:s')]);
    }
  }

  if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) q("DELETE FROM toast_items WHERE id=?", [$id]);
  }

  redirect(url('admin/toast.php'));
}

$list = all("SELECT * FROM toast_items ORDER BY id DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Popups • DesertSafariGo</title>
  <link rel="stylesheet" href="<?= e(url('admin/assets/admin.css')) ?>">
</head>
<body>
  <?php require __DIR__ . '/partials/nav.php'; ?>
  <div style="padding:18px 0">
    <div class="container">
      <div class="grid">
        <div class="card">
          <div class="h1"><?= $editing ? 'Edit Popup Item' : 'Add Popup Item' ?></div>
          <form method="post">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

            <div style="margin-top:6px">
              <label class="small">Title *</label>
              <input class="input" name="title" required value="<?= e($editing['title'] ?? '') ?>" placeholder="Recently booked: Evening Safari">
            </div>

            <div style="margin-top:10px">
              <label class="small">Subtitle</label>
              <input class="input" name="subtitle" value="<?= e($editing['subtitle'] ?? '') ?>" placeholder="2 seats • Downtown pickup">
            </div>

            <div class="row" style="margin-top:10px">
              <label class="row" style="gap:8px">
                <input type="checkbox" name="is_active" <?= ((int)($editing['is_active'] ?? 1)===1)?'checked':'' ?>>
                <span class="small">Active</span>
              </label>
            </div>

            <div class="row" style="margin-top:14px">
              <button class="btn primary" type="submit">Save</button>
              <?php if($editing): ?><a class="btn" href="<?= e(url('admin/toast.php')) ?>">Cancel</a><?php endif; ?>
            </div>
          </form>
          <div class="small" style="margin-top:10px">These rotate every ~20–30 seconds on the homepage for social proof.</div>
        </div>

        <div class="card">
          <div class="h1">All Items</div>
          <div style="overflow:auto">
            <table class="table">
              <thead><tr><th>ID</th><th>Title</th><th>Active</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach($list as $p): ?>
                  <tr>
                    <td><?= (int)$p['id'] ?></td>
                    <td><?= e($p['title']) ?><div class="small"><?= e($p['subtitle']) ?></div></td>
                    <td><?= ((int)$p['is_active']===1)?'Yes':'No' ?></td>
                    <td class="row">
                      <a class="btn" href="<?= e(url('admin/toast.php?edit='.$p['id'])) ?>">Edit</a>
                      <form method="post" onsubmit="return confirm('Delete item #<?= (int)$p['id'] ?>?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="btn danger" type="submit">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if(!$list): ?><tr><td colspan="4" class="small">No items yet.</td></tr><?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
