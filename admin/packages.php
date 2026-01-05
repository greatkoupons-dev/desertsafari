<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();
$active = 'packages';

$editId = (int)($_GET['edit'] ?? 0);
$editing = $editId ? one("SELECT * FROM packages WHERE id=?", [$editId]) : null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $action = (string)($_POST['action'] ?? '');

  if ($action === 'save') {
    $id         = (int)($_POST['id'] ?? 0);
    $name       = trim((string)($_POST['name'] ?? ''));
    $short_desc = trim((string)($_POST['short_desc'] ?? ''));
    $price_badge= trim((string)($_POST['price_badge'] ?? ''));
    $image_url  = trim((string)($_POST['image_url'] ?? ''));
    $is_active  = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    if ($name === '') {
      redirect(url('admin/packages.php'));
    }

    if ($id) {
      q(
        "UPDATE packages
         SET name=?, short_desc=?, price_badge=?, image_url=?, is_active=?, sort_order=?
         WHERE id=?",
        [$name, $short_desc, $price_badge, $image_url, $is_active, $sort_order, $id]
      );
    } else {
      q(
        "INSERT INTO packages (name, short_desc, price_badge, image_url, is_active, sort_order, created_at)
         VALUES (?,?,?,?,?,?,?)",
        [$name, $short_desc, $price_badge, $image_url, $is_active, $sort_order, date('Y-m-d H:i:s')]
      );
    }
  }

  if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
      q("DELETE FROM packages WHERE id=?", [$id]);
    }
  }

  redirect(url('admin/packages.php'));
}

$list = all("SELECT * FROM packages ORDER BY sort_order ASC, id DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Packages • DesertSafariGo</title>
  <link rel="stylesheet" href="<?= e(url('admin/assets/admin.css')) ?>">
</head>
<body>
  <?php require __DIR__ . '/partials/nav.php'; ?>

  <div style="padding:18px 0">
    <div class="container">
      <div class="grid">

        <div class="card">
          <div class="h1"><?= $editing ? 'Edit Package' : 'Add Package' ?></div>

          <form method="post">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

            <div class="row" style="gap:14px">
              <div style="flex:1">
                <label class="small">Name <span class="muted">*</span></label>
                <input class="input" name="name" required value="<?= e($editing['name'] ?? '') ?>">
              </div>

              <div style="width:160px">
                <label class="small">Sort order</label>
                <input class="input" name="sort_order" type="number" value="<?= e((string)($editing['sort_order'] ?? 0)) ?>">
              </div>
            </div>

            <div style="margin-top:10px">
              <label class="small">Short description</label>
              <textarea class="input" name="short_desc" rows="3"><?= e($editing['short_desc'] ?? '') ?></textarea>
            </div>

            <div class="row" style="margin-top:10px; gap:14px">
              <div style="flex:1">
                <label class="small">Price badge</label>
                <input class="input" name="price_badge" value="<?= e($editing['price_badge'] ?? '') ?>" placeholder="e.g. From AED 149">
              </div>
              <div style="flex:1">
                <label class="small">Image URL</label>
                <input class="input" name="image_url" value="<?= e($editing['image_url'] ?? '') ?>" placeholder="https://...">
              </div>
            </div>

            <div style="margin-top:10px">
              <label class="row" style="align-items:center; gap:10px; margin:0">
                <input type="checkbox" name="is_active" <?= ((int)($editing['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
                <span class="small">Active</span>
              </label>
            </div>

            <div class="row" style="margin-top:14px; gap:10px">
              <button class="btn primary" type="submit">Save</button>
              <?php if ($editing): ?>
                <a class="btn" href="<?= e(url('admin/packages.php')) ?>">Cancel</a>
              <?php endif; ?>
            </div>
          </form>
        </div>

        <div class="card">
          <div class="h1">All Packages</div>

          <div style="overflow:auto">
            <table class="table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Active</th>
                  <th>Sort</th>
                  <th style="width:220px">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($list as $p): ?>
                  <tr>
                    <td><?= (int)$p['id'] ?></td>
                    <td>
                      <div><strong><?= e($p['name']) ?></strong></div>
                      <?php if (!empty($p['price_badge'])): ?>
                        <div class="small muted"><?= e($p['price_badge']) ?></div>
                      <?php endif; ?>
                      <?php if (!empty($p['short_desc'])): ?>
                        <div class="small"><?= e($p['short_desc']) ?></div>
                      <?php endif; ?>
                    </td>
                    <td><?= ((int)$p['is_active'] === 1) ? 'Yes' : 'No' ?></td>
                    <td><?= (int)$p['sort_order'] ?></td>
                    <td>
                      <a class="btn" href="<?= e(url('admin/packages.php?edit='.(int)$p['id'])) ?>">Edit</a>

                      <form method="post" style="display:inline" onsubmit="return confirm('Delete this package?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="btn danger" type="submit">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if (!$list): ?>
                  <tr><td colspan="5" class="small">No packages yet.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="small" style="margin-top:10px">Tip: smaller sort order appears first.</div>
        </div>

      </div>
    </div>
  </div>
</body>
</html>
