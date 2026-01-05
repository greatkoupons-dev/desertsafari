<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();
$active = 'testimonials';

$editId = (int)($_GET['edit'] ?? 0);
$editing = $editId ? one("SELECT * FROM testimonials WHERE id=?", [$editId]) : null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $action = (string)($_POST['action'] ?? '');
  if ($action === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim((string)($_POST['name'] ?? ''));
    $meta = trim((string)($_POST['meta'] ?? ''));
    $quote = trim((string)($_POST['quote'] ?? ''));
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    if ($name === '' || $quote === '') redirect(url('admin/testimonials.php'));

    if ($id) {
      q("UPDATE testimonials SET name=?, meta=?, quote=?, is_active=?, sort_order=? WHERE id=?",
        [$name,$meta,$quote,$is_active,$sort_order,$id]);
    } else {
      q("INSERT INTO testimonials(name, meta, quote, is_active, sort_order, created_at) VALUES(?,?,?,?,?,?)",
        [$name,$meta,$quote,$is_active,$sort_order,date('Y-m-d H:i:s')]);
    }
  }

  if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) q("DELETE FROM testimonials WHERE id=?", [$id]);
  }

  redirect(url('admin/testimonials.php'));
}

$list = all("SELECT * FROM testimonials ORDER BY sort_order ASC, id DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Testimonials • DesertSafariGo</title>
  <link rel="stylesheet" href="<?= e(url('admin/assets/admin.css')) ?>">
</head>
<body>
  <?php require __DIR__ . '/partials/nav.php'; ?>
  <div style="padding:18px 0">
    <div class="container">
      <div class="grid">
        <div class="card">
          <div class="h1"><?= $editing ? 'Edit Testimonial' : 'Add Testimonial' ?></div>
          <form method="post">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

            <div class="row" style="gap:14px">
              <div style="flex:1">
                <label class="small">Name *</label>
                <input class="input" name="name" required value="<?= e($editing['name'] ?? '') ?>">
              </div>
              <div style="width:160px">
                <label class="small">Sort order</label>
                <input class="input" name="sort_order" type="number" value="<?= e((string)($editing['sort_order'] ?? 0)) ?>">
              </div>
            </div>

            <div style="margin-top:10px">
              <label class="small">Meta (e.g. "Evening Safari")</label>
              <input class="input" name="meta" value="<?= e($editing['meta'] ?? '') ?>">
            </div>

            <div style="margin-top:10px">
              <label class="small">Quote *</label>
              <textarea class="input" name="quote" required><?= e($editing['quote'] ?? '') ?></textarea>
            </div>

            <div class="row" style="margin-top:10px">
              <label class="row" style="gap:8px">
                <input type="checkbox" name="is_active" <?= ((int)($editing['is_active'] ?? 1)===1)?'checked':'' ?>>
                <span class="small">Active</span>
              </label>
            </div>

            <div class="row" style="margin-top:14px">
              <button class="btn primary" type="submit">Save</button>
              <?php if($editing): ?><a class="btn" href="<?= e(url('admin/testimonials.php')) ?>">Cancel</a><?php endif; ?>
            </div>
          </form>
        </div>

        <div class="card">
          <div class="h1">All Testimonials</div>
          <div style="overflow:auto">
            <table class="table">
              <thead><tr><th>ID</th><th>Name</th><th>Active</th><th>Sort</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach($list as $p): ?>
                  <tr>
                    <td><?= (int)$p['id'] ?></td>
                    <td><?= e($p['name']) ?></td>
                    <td><?= ((int)$p['is_active']===1)?'Yes':'No' ?></td>
                    <td><?= (int)$p['sort_order'] ?></td>
                    <td class="row">
                      <a class="btn" href="<?= e(url('admin/testimonials.php?edit='.$p['id'])) ?>">Edit</a>
                      <form method="post" onsubmit="return confirm('Delete testimonial #<?= (int)$p['id'] ?>?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="btn danger" type="submit">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if(!$list): ?><tr><td colspan="5" class="small">No testimonials yet.</td></tr><?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="small" style="margin-top:10px">Keep quotes short and credible for best conversion.</div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
