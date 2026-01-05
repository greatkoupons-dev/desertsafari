<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function db(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $config = require __DIR__ . '/config.php';
  $db = $config['db'] ?? [];
  $host = $db['host'] ?? '';
  $name = $db['name'] ?? '';
  $user = $db['user'] ?? '';
  $pass = $db['pass'] ?? '';
  $charset = $db['charset'] ?? 'utf8mb4';

  if (!$host || !$name || !$user) {
    throw new RuntimeException('Database is not configured. Run the installer at /install/');
  }

  $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";
  $opts = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];
  $pdo = new PDO($dsn, $user, $pass, $opts);
  return $pdo;
}

function q(string $sql, array $params = []): PDOStatement {
  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  return $stmt;
}

function one(string $sql, array $params = []): ?array {
  $row = q($sql, $params)->fetch();
  return $row ?: null;
}

function all(string $sql, array $params = []): array {
  return q($sql, $params)->fetchAll();
}
