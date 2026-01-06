<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Start a session with safer cookie settings (prevents cookie/path issues on HTTPS).
 */
function start_session(): void {
  if (session_status() === PHP_SESSION_ACTIVE) return;

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
    // Fallback for older PHP
    session_set_cookie_params(0, '/; samesite=Lax', '', $isHttps, true);
  }

  session_start();
}

/**
 * Returns admin user row if logged in; null otherwise.
 */
function admin_user(): ?array {
  start_session();
  $id = $_SESSION['admin_id'] ?? null;
  if (!$id) return null;

  // If your users table has role/is_admin, you can enforce it here.
  // For now, mirrors your original logic.
  return one("SELECT id, email, name, created_at FROM users WHERE id = ? LIMIT 1", [(int)$id]);
}

/**
 * Loop-proof admin guard:
 * - If already on login.php, DO NOT redirect to login.php again.
 * - Adds ?return= to send user back after login.
 */
function require_admin(): void {
  $uri = (string)($_SERVER['REQUEST_URI'] ?? '');

  // If we are already on the login page, do not redirect (prevents redirect loop)
  if (strpos($uri, '/admin/login.php') !== false) {
    return;
  }

  if (!admin_user()) {
    $return = urlencode($uri !== '' ? $uri : '/admin/');
    // Use your helper url() if it exists, but keep it absolute to avoid path weirdness
    redirect('/admin/login.php?return=' . $return);
  }
}

/**
 * Authenticate admin using users table.
 * IMPORTANT: Only allow admin users if you have such a flag/role.
 */
function login_admin(string $email, string $password): bool {
  start_session();

  $email = trim($email);
  if ($email === '' || $password === '') return false;

  $user = one("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
  if (!$user) return false;

  if (empty($user['password_hash']) || !password_verify($password, (string)$user['password_hash'])) {
    return false;
  }

  // Optional but recommended: if you have an is_admin column, enforce it:
  // if (empty($user['is_admin'])) return false;

  // Regenerate session id after login to prevent fixation
  if (!headers_sent()) {
    @session_regenerate_id(true);
  }

  $_SESSION['admin_id'] = (int)$user['id'];
  return true;
}

/**
 * Logout admin cleanly.
 */
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
