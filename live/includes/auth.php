<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function start_session(): void {
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
}

function admin_user(): ?array {
  start_session();
  $id = $_SESSION['admin_id'] ?? null;
  if (!$id) return null;
  return one("SELECT id, email, name, created_at FROM users WHERE id = ?", [$id]);
}

function require_admin(): void {
  if (!admin_user()) {
    redirect(url('admin/login.php'));
  }
}

function login_admin(string $email, string $password): bool {
  start_session();
  $user = one("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
  if (!$user) return false;
  if (!password_verify($password, $user['password_hash'])) return false;
  $_SESSION['admin_id'] = (int)$user['id'];
  return true;
}

function logout_admin(): void {
  start_session();
  $_SESSION = [];
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
      $params["path"], $params["domain"],
      $params["secure"], $params["httponly"]
    );
  }
  session_destroy();
}
