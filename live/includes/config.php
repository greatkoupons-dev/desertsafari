<?php
declare(strict_types=1);

/**
 * Base configuration.
 * Database credentials are stored in includes/config.local.php created by the installer.
 */

$config = [
  'app' => [
    // Root-domain only: should be https://desertsafarigo.com (no trailing slash)
    'base_url' => null,
    'env' => 'production',
    'timezone' => 'Asia/Dubai',
  ],
  'db' => [
    'host' => null,
    'name' => null,
    'user' => null,
    'pass' => null,
    'charset' => 'utf8mb4',
  ],
];

$local = __DIR__ . '/config.local.php';
if (is_file($local)) {
  $localConfig = require $local;
  if (is_array($localConfig)) {
    $config = array_replace_recursive($config, $localConfig);
  }
}

return $config;
