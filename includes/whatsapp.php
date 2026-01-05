<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * WhatsApp click tracking
 * Stores: timestamp (created_at), page, IP hash, user agent
 */

function wa_client_ip(): string {
  // Support common reverse proxy/CDN headers (Cloudflare, etc.)
  $candidates = [
    $_SERVER['HTTP_CF_CONNECTING_IP'] ?? null,
    $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
    $_SERVER['HTTP_CLIENT_IP'] ?? null,
    $_SERVER['REMOTE_ADDR'] ?? null,
  ];
  foreach ($candidates as $v) {
    if (!is_string($v) || $v === '') continue;
    if (strpos($v, ',') !== false) {
      $v = trim(explode(',', $v)[0]);
    }
    return trim($v);
  }
  return '';
}

function wa_ip_hash(string $ip): string {
  // Fixed, code-local salt to avoid config changes; change if you ever need to invalidate historic hashes.
  $salt = 'dsg_wa_v1_salt';
  return hash('sha256', $salt . '|' . $ip);
}

function wa_ensure_table(): void {
  // Safe to call repeatedly
  q(
    "CREATE TABLE IF NOT EXISTS whatsapp_clicks (
      id INT AUTO_INCREMENT PRIMARY KEY,
      page VARCHAR(255) NOT NULL,
      ip_hash CHAR(64) NOT NULL,
      user_agent VARCHAR(255) NOT NULL,
      created_at DATETIME NOT NULL,
      INDEX idx_created_at (created_at),
      INDEX idx_page (page)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
  );
}

function wa_track_handler(): void {
  wa_ensure_table();

  $raw = file_get_contents('php://input') ?: '';
  $data = [];
  if ($raw !== '') {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) $data = $decoded;
  }

  $page = isset($data['page']) && is_string($data['page']) ? trim($data['page']) : '';
  if ($page === '') $page = ($_SERVER['REQUEST_URI'] ?? '/');
  if (strlen($page) > 255) $page = substr($page, 0, 255);

  $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
  if (!is_string($ua)) $ua = '';
  $ua = trim($ua);
  if ($ua === '') $ua = 'unknown';
  if (strlen($ua) > 255) $ua = substr($ua, 0, 255);

  $ip = wa_client_ip();
  $ipHash = wa_ip_hash($ip);

  // Timestamp stored server-side
  q(
    "INSERT INTO whatsapp_clicks (page, ip_hash, user_agent, created_at) VALUES (?, ?, ?, NOW())",
    [$page, $ipHash, $ua]
  );

  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok' => true]);
  exit;
}
