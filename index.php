<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/lead.php';
require_once __DIR__ . '/includes/whatsapp.php';
require_once __DIR__ . '/includes/db.php';

/**
 * Packages preload (for home page sections). Safe on fresh installs.
 */
$packages = [];
try {
  if (function_exists('all')) {
    $packages = all("SELECT * FROM packages WHERE is_active=1 ORDER BY sort_order ASC, id DESC");
  } else {
    $pdo = $GLOBALS['pdo'] ?? null;
    if ($pdo instanceof PDO) {
      $st = $pdo->query("SELECT * FROM packages WHERE is_active=1 ORDER BY sort_order ASC, id DESC");
      $packages = $st ? ($st->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];
    }
  }
} catch (Throwable $e) {
  $packages = [];
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = rtrim((string)$path, '/');
if ($path === '') $path = '/';

/**
 * POST endpoints
 */
if ($path === '/lead-submit' && (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST')) {
  handle_lead_submit();
  exit;
}

if ($path === '/wa-track' || $path === '/whatsapp-track') {
  // track whatsapp clicks (expects your includes/whatsapp.php to handle response)
  if (function_exists('wa_track_handler')) {
    wa_track_handler();
    exit;
  }
}

/**
 * Admin route pass-through
 */
if ($path === '/admin') {
  header('Location: ' . url('/admin/'));
  exit;
}
if (str_starts_with($path, '/admin/')) {
  require __DIR__ . '/admin/index.php';
  exit;
}

/**
 * Page router: maps / to pages/home.php (or pages/index.php), and /slug to pages/slug.php
 */
$routes = [
  '/' => __DIR__ . '/pages/home.php',
];

if (isset($routes[$path]) && is_file($routes[$path])) {
  require $routes[$path];
  exit;
}

$slug = ltrim($path, '/');
$slugFile = __DIR__ . '/pages/' . $slug . '.php';
if ($slug !== '' && is_file($slugFile)) {
  require $slugFile;
  exit;
}

/**
 * Fallback 404
 */
$phone = function_exists('setting') ? setting('contact_phone','+971 50 000 0000') : '+971 50 000 0000';
$email = function_exists('setting') ? setting('contact_email','hello@desertsafarigo.com') : 'hello@desertsafarigo.com';
$whatsapp = function_exists('setting') ? setting('contact_whatsapp','+971500000000') : '+971500000000';

require __DIR__ . '/partials/head.php';
?>
<section class="section">
  <div class="container">
    <h2>Page not found</h2>
    <p class="lead">The page you requested does not exist.</p>
    <a class="btn primary" href="<?= function_exists('url') ? e(url('/')) : '/' ?>">Go home</a>
  </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
