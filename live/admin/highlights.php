<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();
$active = 'highlights';

$editId = (int)($_GET['edit'] ?? 0);
$editing = $editId ? one("SELECT * FROM highlights WHERE id=?", [$editId]) : null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $action = (string)($_POST['action'] ?? '');
  if ($action === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim((string)($_POST['title'] ?? ''));
    $subtitle = trim((string)($_POST['subtitle'] ?? ''));
    $image_url = trim((string)($_POST['image_url'] ?? ''));
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    if ($title === '') redirect(url('admin/highlights.php'));

    if ($id) {
      q("UPDATE highlights SET title=?, subtitle=?, image_url=?, is_active=?, sort_order=? WHERE id=?",
        [$title,$subtitle,$image_url,$is_active,$sort_order,$id]);
    } else {
      q("INSERT INTO highlights(title, subtitle, image_url, is_active, sort_order, created_at) VALUES(?,?,?,?,?,?)",
        [$title,$subtitle,$image_url,$is_active,$sort_order,date('Y-m-d H:i:s')]);
    }
  }

  if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) q("DELETE FROM highlights WHERE id=?", [$id]);
  }

  redirect(url('admin/highlights.php'));
}

$list = all("SELECT * FROM highlights ORDER BY sort_order ASC, id DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Highlights • DesertSafariGo</title>
  <link rel="stylesheet" href="<?= e(url('admin/assets/admin.css')) ?>">
</head>
<body>
  <?php require __DIR__ . '/partials/nav.php'; ?>
  <div style="padding:18px 0">
    <div class="container">
      <div class="grid">
        <div class="card">
          <div class="h1"><?= $editing ? 'Edit Highlight' : 'Add Highlight' ?></div>
          <form method="post">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">
            <div class="row" style="gap:14px">
              <div style="flex:1">
                <label class="small">Title *</label>
                <input class="input" name="title" required value="<?= e($editing['title'] ?? '') ?>">
              </div>
              <div style="width:160px">
                <label class="small">Sort order</label>
                <input class="input" name="sort_order" type="number" value="<?= e((string)($editing['sort_order'] ?? 0)) ?>">
              </div>
            </div>

            <div style="margin-top:10px">
              <label class="small">Subtitle</label>
              <textarea class="input" name="subtitle"><?= e($editing['subtitle'] ?? '') ?></textarea>
            </div>

            <div style="margin-top:10px">
              <label class="small">Image URL</label>
              <input class="input" name="image_url" value="<?= e($editing['image_url'] ?? '') ?>" placeholder="https://...">
            </div>

            <div class="row" style="margin-top:10px">
              <label class="row" style="gap:8px">
                <input type="checkbox" name="is_active" <?= ((int)($editing['is_active'] ?? 1)===1)?'checked':'' ?>>
                <span class="small">Active</span>
              </label>
            </div>

            <div class="row" style="margin-top:14px">
              <button class="btn primary" type="submit">Save</button>
              <?php if($editing): ?><a class="btn" href="<?= e(url('admin/highlights.php')) ?>">Cancel</a><?php endif; ?>
            </div>
          </form>
        </div>

        <div class="card">
          <div class="h1">All Highlights</div>
          <div style="overflow:auto">
            <table class="table">
              <thead><tr><th>ID</th><th>Title</th><th>Active</th><th>Sort</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach($list as $p): ?>
                  <tr>
                    <td><?= (int)$p['id'] ?></td>
                    <td><?= e($p['title']) ?></td>
                    <td><?= ((int)$p['is_active']===1)?'Yes':'No' ?></td>
                    <td><?= (int)$p['sort_order'] ?></td>
                    <td class="row">
                      <a class="btn" href="<?= e(url('admin/highlights.php?edit='.$p['id'])) ?>">Edit</a>
                      <form method="post" onsubmit="return confirm('Delete highlight #<?= (int)$p['id'] ?>?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="btn danger" type="submit">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if(!$list): ?><tr><td colspan="5" class="small">No highlights yet.</td></tr><?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="small" style="margin-top:10px">These show in the homepage carousel.</div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
