<?php
declare(strict_types=1);

function e(?string $s): string {
  return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_url(): string {
  static $base = null;
  if ($base !== null) return $base;

  $config = require __DIR__ . '/config.php';
  $cfgBase = $config['app']['base_url'] ?? null;
  if (is_string($cfgBase) && $cfgBase !== '') {
    $base = rtrim($cfgBase, '/');
    return $base;
  }

  $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') === '443');
  $scheme = $https ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $base = $scheme . '://' . $host;
  return $base;
}

function url(string $path = ''): string {
  $path = ltrim($path, '/');
  if ($path === '') return base_url() . '/';
  return base_url() . '/' . $path;
}

function redirect(string $to): void {
  header('Location: ' . $to);
  exit;
}

function slugify(string $text): string {
  $text = trim(mb_strtolower($text));
  $text = preg_replace('~[^\pL\pN]+~u', '-', $text);
  $text = trim($text, '-');
  return $text !== '' ? $text : 'post';
}
