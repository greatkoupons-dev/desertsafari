<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/auth.admin.php';
require_once __DIR__ . '/../includes/packages.model.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$item = $id ? packages_get($pdo, $id) : null;

if ($id && !$item) {
  http_response_code(404);
  exit('Package not found');
}

if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) {
    $errors[] = 'Invalid session token. Refresh and try again.';
  }

  $data = [
    'title'          => $_POST['title'] ?? '',
    'subtitle'       => $_POST['subtitle'] ?? '',
    'price'          => $_POST['price'] ?? null,
    'currency'       => $_POST['currency'] ?? 'AED',
    'duration_label' => $_POST['duration_label'] ?? '',
    'highlights'     => $_POST['highlights'] ?? '',
    'badge'          => $_POST['badge'] ?? '',
    'sort_order'     => $_POST['sort_order'] ?? 0,
    'is_active'      => isset($_POST['is_active']) ? 1 : 0,
  ];

  if (trim($data['title']) === '') $errors[] = 'Title is required.';

  if (!$errors) {
    if ($id) packages_update($pdo, $id, $data);
    else $id = packages_create($pdo, $data);

    header('Location: packages.php');
    exit;
  }
}

// Prefill form
$val = function($key, $default='') use ($item) {
  return $item[$key] ?? $default;
};
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= $id ? 'Edit Package' : 'Add Package' ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:system-ui,Arial;margin:20px;max-width:900px}
    label{display:block;margin-top:12px;font-weight:600}
    input,textarea{width:100%;padding:10px;border:1px solid #ddd;border-radius:10px}
    textarea{min-height:130px}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .btn{display:inline-block;padding:10px 12px;border:1px solid #ddd;border-radius:10px;text-decoration:none;color:#111}
    .btn.primary{border-color:#111}
    .err{background:#fff2f2;border:1px solid #ffd0d0;padding:10px;border-radius:10px;margin:12px 0}
    .muted{opacity:.7;font-size:13px}
  </style>
</head>
<body>

<h2 style="margin:0 0 6px 0"><?= $id ? 'Edit Package' : 'Add Package' ?></h2>
<div class="muted">Highlights: put one bullet per line.</div>

<?php if ($errors): ?>
  <div class="err">
    <strong>Please fix:</strong>
    <ul>
      <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

  <label>Title *</label>
  <input name="title" value="<?= htmlspecialchars($_POST['title'] ?? $val('title')) ?>">

  <label>Subtitle</label>
  <input name="subtitle" value="<?= htmlspecialchars($_POST['subtitle'] ?? $val('subtitle')) ?>">

  <div class="row">
    <div>
      <label>Price</label>
      <input name="price" value="<?= htmlspecialchars($_POST['price'] ?? ($val('price') ?? '')) ?>" placeholder="e.g. 199.00">
    </div>
    <div>
      <label>Currency</label>
      <input name="currency" value="<?= htmlspecialchars($_POST['currency'] ?? $val('currency','AED')) ?>">
    </div>
  </div>

  <div class="row">
    <div>
      <label>Duration Label</label>
      <input name="duration_label" value="<?= htmlspecialchars($_POST['duration_label'] ?? $val('duration_label')) ?>" placeholder="e.g. 6 Hours / Evening">
    </div>
    <div>
      <label>Badge</label>
      <input name="badge" value="<?= htmlspecialchars($_POST['badge'] ?? $val('badge')) ?>" placeholder="e.g. Best Seller">
    </div>
  </div>

  <label>Highlights (one per line)</label>
  <textarea name="highlights"><?= htmlspecialchars($_POST['highlights'] ?? $val('highlights','')) ?></textarea>

  <div class="row">
    <div>
      <label>Sort Order</label>
      <input name="sort_order" value="<?= htmlspecialchars($_POST['sort_order'] ?? $val('sort_order',0)) ?>">
    </div>
    <div style="display:flex;align-items:end;gap:10px">
      <label style="display:flex;gap:10px;align-items:center;font-weight:600;margin-top:30px">
        <input type="checkbox" name="is_active" value="1" <?= !empty($_POST) ? (!empty($_POST['is_active'])?'checked':'') : (((int)$val('is_active',1)===1)?'checked':'') ?>>
        Active
      </label>
    </div>
  </div>

  <div style="margin-top:16px;display:flex;gap:10px">
    <button class="btn primary" type="submit">Save</button>
    <a class="btn" href="packages.php">Cancel</a>
  </div>
</form>

</body>
</html>
