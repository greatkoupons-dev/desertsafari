<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if (function_exists('require_admin')) {
  require_admin();
} elseif (function_exists('require_login')) {
  require_login();
}

/**
 * DB helper wrappers (works whether your project exposes all()/one()/execq() or only a PDO instance).
 */
function dsg_pdo(): ?PDO {
  if (function_exists('pdo')) {
    $p = pdo();
    if ($p instanceof PDO) return $p;
  }
  $p = $GLOBALS['pdo'] ?? null;
  return ($p instanceof PDO) ? $p : null;
}
function dsg_one(string $sql, array $params = []): ?array {
  if (function_exists('one')) {
    $r = one($sql, $params);
    return $r ?: null;
  }
  $pdo = dsg_pdo();
  if (!$pdo) return null;
  $st = $pdo->prepare($sql);
  $st->execute($params);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  return $row ?: null;
}
function dsg_all(string $sql, array $params = []): array {
  if (function_exists('all')) {
    $r = all($sql, $params);
    return is_array($r) ? $r : [];
  }
  $pdo = dsg_pdo();
  if (!$pdo) return [];
  $st = $pdo->prepare($sql);
  $st->execute($params);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);
  return is_array($rows) ? $rows : [];
}
function dsg_exec(string $sql, array $params = []): int {
  if (function_exists('execq')) {
    return (int) execq($sql, $params);
  }
  $pdo = dsg_pdo();
  if (!$pdo) return 0;
  $st = $pdo->prepare($sql);
  $st->execute($params);
  return (int) $st->rowCount();
}
function dsg_e(string $s): string {
  if (function_exists('e')) return (string) e($s);
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$active = 'packages';

$editId = (int)($_GET['edit'] ?? 0);
$editing = $editId ? dsg_one("SELECT * FROM packages WHERE id=?", [$editId]) : null;

$flash = null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $action = (string)($_POST['action'] ?? '');

  if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
      dsg_exec("DELETE FROM packages WHERE id=?", [$id]);
      $flash = ['type'=>'success','msg'=>'Package deleted.'];
    }
    header('Location: packages.php');
    exit;
  }

  if ($action === 'save') {
    $id = (int)($_POST['id'] ?? 0);

    $name = trim((string)($_POST['name'] ?? ''));
    $short_desc = trim((string)($_POST['short_desc'] ?? ''));
    $price_badge = trim((string)($_POST['price_badge'] ?? ''));
    $image_url = trim((string)($_POST['image_url'] ?? ''));
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($name === '') {
      $flash = ['type'=>'error','msg'=>'Package name is required.'];
    } else {
      if ($id > 0) {
        dsg_exec(
          "UPDATE packages
             SET name=?, short_desc=?, price_badge=?, image_url=?, sort_order=?, is_active=?
           WHERE id=?",
          [$name, $short_desc, $price_badge, $image_url, $sort_order, $is_active, $id]
        );
        $flash = ['type'=>'success','msg'=>'Package updated.'];
      } else {
        dsg_exec(
          "INSERT INTO packages (name, short_desc, price_badge, image_url, sort_order, is_active, created_at)
           VALUES (?,?,?,?,?,?, NOW())",
          [$name, $short_desc, $price_badge, $image_url, $sort_order, $is_active]
        );
        $flash = ['type'=>'success','msg'=>'Package added.'];
      }
      header('Location: packages.php');
      exit;
    }
  }
}

$list = dsg_all("SELECT * FROM packages ORDER BY sort_order ASC, id DESC");

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin — Packages</title>
  <link rel="stylesheet" href="assets/admin.css?v=1">
  <style>
    .wrap{max-width:1100px;margin:0 auto;padding:18px}
    .card{background:#fff;border:1px solid rgba(0,0,0,.08);border-radius:14px;box-shadow:0 4px 18px rgba(0,0,0,.06)}
    .card h2{margin:0 0 12px 0;font-size:18px}
    .grid{display:grid;grid-template-columns: 1.2fr .8fr;gap:16px}
    @media (max-width: 980px){.grid{grid-template-columns:1fr}}
    .row{display:flex;gap:12px;align-items:center}
    .input, textarea{width:100%;border:1px solid rgba(0,0,0,.12);border-radius:10px;padding:10px 12px;font:inherit}
    textarea{min-height:96px;resize:vertical}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid rgba(0,0,0,.15);border-radius:10px;padding:10px 14px;background:#f7f7f7;cursor:pointer;text-decoration:none;color:#111}
    .btn.primary{background:#111;color:#fff;border-color:#111}
    .btn.danger{background:#b42318;color:#fff;border-color:#b42318}
    .btn.ghost{background:transparent}
    table{width:100%;border-collapse:collapse}
    th,td{padding:10px 8px;border-bottom:1px solid rgba(0,0,0,.08);text-align:left;vertical-align:top}
    th{font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#555}
    .badge{display:inline-block;padding:3px 8px;border-radius:999px;background:#f0f0f0;font-size:12px}
    .note{font-size:12px;color:#666}
    .flash{padding:10px 12px;border-radius:10px;margin:0 0 12px 0}
    .flash.success{background:#ecfdf3;border:1px solid #abefc6;color:#067647}
    .flash.error{background:#fef3f2;border:1px solid #fecdca;color:#b42318}
  </style>
</head>
<body>
    <?php require __DIR__ . '/partials/nav.php'; ?>
  <div class="wrap">
    <?php if ($flash): ?>
      <div class="flash <?= dsg_e($flash['type']) ?>"><?= dsg_e($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="grid">
      <div class="card" style="padding:16px">
        <h2><?= $editing ? 'Edit Package' : 'Add Package' ?></h2>

        <form method="post" action="packages.php">
          <input type="hidden" name="action" value="save">
          <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

          <div class="row">
            <div style="flex:1">
              <label class="note">Name</label>
              <input class="input" name="name" value="<?= dsg_e((string)($editing['name'] ?? '')) ?>" placeholder="e.g. Evening Desert Safari" required>
            </div>
            <div style="width:160px">
              <label class="note">Sort order</label>
              <input class="input" type="number" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>" placeholder="0">
            </div>
          </div>

          <div style="margin-top:10px">
            <label class="note">Short description</label>
            <textarea class="input" name="short_desc" placeholder="1–2 lines that sell the package"><?= dsg_e((string)($editing['short_desc'] ?? '')) ?></textarea>
          </div>

          <div class="row" style="margin-top:10px">
            <div style="flex:1">
              <label class="note">Price badge</label>
              <input class="input" name="price_badge" value="<?= dsg_e((string)($editing['price_badge'] ?? '')) ?>" placeholder="e.g. From AED 149">
            </div>
            <div style="flex:1">
              <label class="note">Image URL</label>
              <input class="input" name="image_url" value="<?= dsg_e((string)($editing['image_url'] ?? '')) ?>" placeholder="https://...">
            </div>
          </div>

          <div class="row" style="margin-top:10px">
            <label class="row" style="gap:8px">
              <input type="checkbox" name="is_active" <?= ((int)($editing['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
              <span class="note">Active</span>
            </label>
          </div>

          <div class="row" style="margin-top:12px">
            <button class="btn primary" type="submit"><?= $editing ? 'Update' : 'Add' ?></button>
            <?php if ($editing): ?>
              <a class="btn ghost" href="packages.php">Cancel</a>
            <?php endif; ?>
          </div>

          <div class="note" style="margin-top:10px">Tip: smaller sort order appears first.</div>
        </form>
      </div>

      <div class="card" style="padding:16px">
        <h2>All Packages</h2>

        <div style="overflow:auto">
          <table>
            <thead>
              <tr>
                <th style="width:56px">ID</th>
                <th>Name</th>
                <th style="width:120px">Status</th>
                <th style="width:90px">Sort</th>
                <th style="width:180px">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($list as $p): ?>
                <tr>
                  <td><?= (int)$p['id'] ?></td>
                  <td>
                    <div style="font-weight:600"><?= dsg_e((string)($p['name'] ?? '')) ?></div>
                    <?php if (!empty($p['price_badge'])): ?>
                      <div class="badge"><?= dsg_e((string)$p['price_badge']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($p['short_desc'])): ?>
                      <div class="note" style="margin-top:4px"><?= dsg_e((string)$p['short_desc']) ?></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ((int)$p['is_active'] === 1): ?>
                      <span class="badge">Active</span>
                    <?php else: ?>
                      <span class="badge">Hidden</span>
                    <?php endif; ?>
                  </td>
                  <td><?= (int)($p['sort_order'] ?? 0) ?></td>
                  <td>
                    <a class="btn" href="packages.php?edit=<?= (int)$p['id'] ?>">Edit</a>

                    <form method="post" action="packages.php" style="display:inline" onsubmit="return confirm('Delete this package?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                      <button class="btn danger" type="submit">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$list): ?>
                <tr><td colspan="5" class="note">No packages yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</body>
</html>
