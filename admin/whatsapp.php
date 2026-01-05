<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/whatsapp.php';

require_admin();
$active = 'whatsapp';

wa_ensure_table();

$total = (int)(one("SELECT COUNT(*) AS c FROM whatsapp_clicks")['c'] ?? 0);
$rows = all("SELECT * FROM whatsapp_clicks ORDER BY id DESC LIMIT 200");

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>WhatsApp Clicks • DesertSafariGo</title>
  <link rel="stylesheet" href="<?= e(url('admin/assets/admin.css')) ?>">
</head>
<body>
  <?php require __DIR__ . '/partials/nav.php'; ?>
  <div style="padding:18px 0">
    <div class="container">
      <div class="card">
        <div class="row" style="justify-content:space-between">
          <div>
            <div class="h1" style="margin:0">WhatsApp Clicks</div>
            <div class="small">Latest 200 events. Stored fields: timestamp, page, IP hash, user agent.</div>
          </div>
          <div class="badge">Total: <?= $total ?></div>
        </div>

        <div style="overflow:auto; margin-top:12px">
          <table class="table">
            <thead>
              <tr><th>ID</th><th>Created</th><th>Page</th><th>IP hash</th><th>User agent</th></tr>
            </thead>
            <tbody>
              <?php foreach($rows as $r): ?>
                <tr>
                  <td><?= (int)$r['id'] ?></td>
                  <td><?= e($r['created_at']) ?></td>
                  <td><?= e($r['page']) ?></td>
                  <td><code><?= e(substr($r['ip_hash'], 0, 14)) ?>…</code></td>
                  <td><?= e($r['user_agent']) ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if(!$rows): ?><tr><td colspan="5" class="small">No WhatsApp clicks tracked yet.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div style="margin-top:14px" class="card">
        <div class="row">
          <a class="btn" href="<?= e(url('admin/')) ?>">Back to Dashboard</a>
          <a class="btn" href="<?= e(url('/')) ?>" target="_blank">Open Website</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
