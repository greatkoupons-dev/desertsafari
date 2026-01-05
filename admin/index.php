<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (function_exists('require_admin')) {
  require_admin();
} elseif (function_exists('require_login')) {
  require_login();
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/admin/', PHP_URL_PATH);
$path = rtrim((string)$path, '/');

if ($path === '' || $path === '/admin') {
  header('Location: /admin/dashboard.php');
  exit;
}

$sub = str_replace('/admin', '', $path);
if ($sub === '' || $sub === '/') {
  header('Location: /admin/dashboard.php');
  exit;
}

$target = __DIR__ . $sub . '.php';
if (is_file($target)) {
  require $target;
  exit;
}

// fallback to dashboard
header('Location: /admin/dashboard.php');
exit;
