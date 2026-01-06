<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function start_session(): void {
  if (session_status() === PHP_SESSION_ACTIVE) return;

  // Detect HTTPS reliably (Hostinger + Cloudflare safe)
  $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
          || ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443)
          || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

  // Must be set BEFORE session_start()
  if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
      'lifetime' => 0,
      'path' => '/',
      'secure' => $isHttps,
      'httponly' => true,
      'samesite' => 'Lax',
    ]);
  } else {
    session_set_cookie_params(0, '/; samesite=Lax', '', $isHttps, true);
  }

  session_start();
}

function admin_user(): ?array {
  start_session();
  $id = $_SESSION['admin_id'] ?? null;
  if (!$id) return null;
  return one("SELECT id, email, name, created_at FROM users WHERE id = ? LIMIT 1", [(int)$id]);
}

function require_admin(): void {
  $uri = (string)($_SERVER['REQUEST_URI'] ?? '');

  // Prevent infinite redirects if someone accidentally includes require_admin on login page
  if (strpos($uri, '/admin/login.php') !== false) {
    return;
  }

  if (!admin_user()) {
    $return = urlencode($uri !== '' ? $uri : '/admin/index.php');
    // Use absolute path to avoid path weirdness
    redirect('/admin/login.php?return=' . $return);
  }
}

function login_admin(string $email, string $password): bool {
  start_session();

  $email = trim($email);
  if ($email === '' || $password === '') return false;

  $user = one("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
  if (!$user) return false;

  $hash = (string)($user['password_hash'] ?? '');
  if ($hash === '' || !password_verify($password, $hash)) return false;

  // (Optional) enforce admin flag if you have one:
  // if (empty($user['is_admin'])) return false;

  if (!headers_sent()) {
    @session_regenerate_id(true);
  }

  $_SESSION['admin_id'] = (int)$user['id'];
  return true;
}

function logout_admin(): void {
  start_session();

  $_SESSION = [];

  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
      session_name(),
      '',
      time() - 42000,
      $params["path"] ?? '/',
      $params["domain"] ?? '',
      (bool)($params["secure"] ?? false),
      (bool)($params["httponly"] ?? true)
    );
  }

  @session_destroy();
}
