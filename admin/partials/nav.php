<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin();

$user = admin_user();
$active = $active ?? '';

function nav_active(string $key, string $active): string {
  return $key === $active ? ' active' : '';
}
?>

<div class="admin-nav">
  <div class="container admin-nav-inner">
    <a class="brand" href="<?= e(url('admin/')) ?>">DesertSafariGo Admin</a>

    <div class="nav-links">
      <a class="nav-link<?= nav_active('dashboard', $active) ?>" href="<?= e(url('admin/')) ?>">Dashboard</a>
      <a class="nav-link<?= nav_active('leads', $active) ?>" href="<?= e(url('admin/leads.php')) ?>">Leads</a>
      <a class="nav-link<?= nav_active('packages', $active) ?>" href="<?= e(url('admin/packages.php')) ?>">Packages</a>
      <a class="nav-link<?= nav_active('blog', $active) ?>" href="<?= e(url('admin/blog.php')) ?>">Blog</a>
      <a class="nav-link<?= nav_active('settings', $active) ?>" href="<?= e(url('admin/settings.php')) ?>">Settings</a>
      <a class="nav-link<?= nav_active('whatsapp', $active) ?>" href="<?= e(url('admin/whatsapp.php')) ?>">WhatsApp</a>
    </div>

    <div class="nav-actions">
      <a class="nav-link" href="<?= e(url('admin/logout.php')) ?>">Logout</a>
    </div>
  </div>
</div>
