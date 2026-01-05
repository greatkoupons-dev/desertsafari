<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/site.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/whatsapp.php';

require_admin();
$active = 'dashboard';

$leads_new = (int)(one("SELECT COUNT(*) AS c FROM leads WHERE status='new'")['c'] ?? 0);
$leads_total = (int)(one("SELECT COUNT(*) AS c FROM leads")['c'] ?? 0);
$posts = (int)(one("SELECT COUNT(*) AS c FROM blog_posts")['c'] ?? 0);
$packages = (int)(one("SELECT COUNT(*) AS c FROM packages")['c'] ?? 0);

wa_ensure_table();
$wa_total = (int)(one("SELECT COUNT(*) AS c FROM whatsapp_clicks")['c'] ?? 0);
$wa_24h = (int)(one("SELECT COUNT(*) AS c FROM whatsapp_clicks WHERE created_at >= (NOW() - INTERVAL 24 HOUR)")['c'] ?? 0);

$latest = all("SELECT * FROM leads ORDER BY id DESC LIMIT 10");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard • DesertSafariGo</title>
  <link rel="stylesheet" href="<?= e(url('admin/assets/admin.css')) ?>">
</head>
<body>
  <?php require __DIR__ . '/partials/nav.php'; ?>
  <div style="padding:18px 0">
    <div class="container">
      <div class="grid4">
        <div class="card"><div class="small">New leads</div><div class="h1"><?= $leads_new ?></div></div>
        <div class="card"><div class="small">Total leads</div><div class="h1"><?= $leads_total ?></div></div>
        <div class="card"><div class="small">Packages / Posts</div><div class="h1"><?= $packages ?> / <?= $posts ?></div></div>
        <div class="card">
          <div class="small">WhatsApp clicks</div>
          <div class="h1"><?= $wa_total ?></div>
          <div class="small" style="margin-top:6px">Last 24h: <?= $wa_24h ?></div>
        </div>
      </div>

      <div style="margin-top:14px" class="card">
        <div class="row" style="justify-content:space-between">
          <div>
            <div class="h1" style="margin:0">Latest Leads</div>
            <div class="small">Captured from homepage form and stored in your database.</div>
          </div>
          <a class="btn primary" href="<?= e(url('admin/leads.php')) ?>">Open Leads</a>
        </div>

        <div style="overflow:auto; margin-top:12px">
          <table class="table">
            <thead>
              <tr><th>ID</th><th>Name</th><th>Phone</th><th>Package</th><th>Date</th><th>Status</th><th>Created</th></tr>
            </thead>
            <tbody>
              <?php foreach($latest as $l): ?>
                <tr>
                  <td><?= (int)$l['id'] ?></td>
                  <td><?= e($l['full_name']) ?></td>
                  <td><?= e($l['phone']) ?></td>
                  <td><?= e($l['package_name']) ?></td>
                  <td><?= e($l['trip_date']) ?></td>
                  <td><?= e($l['status']) ?></td>
                  <td><?= e($l['created_at']) ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if(!$latest): ?><tr><td colspan="7" class="small">No leads yet.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div style="margin-top:14px" class="card">
        <div class="h1">Quick Links</div>
        <div class="row">
          <a class="btn" href="<?= e(url('/')) ?>" target="_blank">Open Website</a>
          <a class="btn" href="<?= e(url('admin/settings.php')) ?>">Site Settings</a>
          <a class="btn" href="<?= e(url('admin/blog.php')) ?>">Manage Blog</a>
          <a class="btn" href="<?= e(url('admin/whatsapp.php')) ?>">WhatsApp Clicks</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
