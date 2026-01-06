<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function setting(string $key, ?string $default = null): ?string {
  $row = one("SELECT value FROM settings WHERE `key` = ? LIMIT 1", [$key]);
  return $row ? (string)$row['value'] : $default;
}

function setting_int(string $key, int $default = 0): int {
  $v = setting($key, (string)$default);
  return (int)$v;
}

function media_url_by_id(?int $id): ?string {
  if (!$id) return null;
  $row = one("SELECT url FROM media WHERE id = ? LIMIT 1", [$id]);
  return $row ? (string)$row['url'] : null;
}

function post_webhook(string $url, array $payload): void {
  if ($url === '') return;
  if (!function_exists('curl_init')) return;
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 3,
  ]);
  curl_exec($ch);
  curl_close($ch);
}
