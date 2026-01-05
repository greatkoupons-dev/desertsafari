<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/lead.php';
require_once __DIR__ . '/includes/whatsapp.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = rtrim($path, '/');
if ($path === '') $path = '/';

if ($path === '/lead-submit' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  handle_lead_submit();
  exit;
}

if ($path === '/wa-track' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  wa_track_handler();
}

if ($path === '/admin') {
  redirect(url('admin/'));
}


// Preload packages for pages that need it (e.g., Home -> Our Packages).
// Pages can use $packages directly.
$packages = [];
if (function_exists('all')) {
  try {
    $packages = all("SELECT * FROM packages WHERE is_active=1 ORDER BY sort_order ASC, id DESC");
  } catch (Throwable $e) {
    // Fail-closed: do not break frontend if DB/table is not ready.
    $packages = [];
  }
}


$routes = [
  '/' => ['file' => __DIR__ . '/pages/home.php'],
  '/blog' => ['file' => __DIR__ . '/pages/blog_list.php'],
];

if (preg_match('~^/blog/([a-z0-9\-]+)$~', $path, $m)) {
  $params = ['slug' => $m[1]];
  require __DIR__ . '/pages/blog_post.php';
  exit;
}

if (isset($routes[$path])) {
  require $routes[$path]['file'];
  exit;
}

http_response_code(404);
$seo_title = "404 • Not found";
$seo_description = "Page not found.";
$site_name = setting('site_name','DesertSafariGo');
$footer_bg_url = setting('footer_bg_url', url('assets/img/placeholder.jpg'));
$phone = setting('contact_phone','+971 50 000 0000');
$email = setting('contact_email','hello@desertsafarigo.com');
$whatsapp = setting('contact_whatsapp','+971500000000');

require __DIR__ . '/partials/head.php';
?>
<section class="section">
  <div class="container">
    <h2>Page not found</h2>
    <p class="lead">The page you requested does not exist.</p>
    <a class="btn primary" href="<?= e(url('/')) ?>">Go home</a>
  </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
