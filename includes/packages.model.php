<?php
// packages.model.php
// Requires $pdo (PDO instance) from your existing DB bootstrap.

function packages_all(PDO $pdo, bool $includeInactive = false): array {
  $sql = "SELECT * FROM packages ";
  if (!$includeInactive) $sql .= "WHERE is_active = 1 ";
  $sql .= "ORDER BY sort_order ASC, id DESC";
  return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function packages_get(PDO $pdo, int $id): ?array {
  $stmt = $pdo->prepare("SELECT * FROM packages WHERE id = :id LIMIT 1");
  $stmt->execute([':id' => $id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  return $row ?: null;
}

function packages_create(PDO $pdo, array $data): int {
  $stmt = $pdo->prepare("
    INSERT INTO packages (title, subtitle, price, currency, duration_label, highlights, badge, sort_order, is_active)
    VALUES (:title, :subtitle, :price, :currency, :duration_label, :highlights, :badge, :sort_order, :is_active)
  ");
  $stmt->execute([
    ':title'          => trim((string)($data['title'] ?? '')),
    ':subtitle'       => trim((string)($data['subtitle'] ?? '')) ?: null,
    ':price'          => ($data['price'] === '' || $data['price'] === null) ? null : (float)$data['price'],
    ':currency'       => trim((string)($data['currency'] ?? 'AED')) ?: 'AED',
    ':duration_label' => trim((string)($data['duration_label'] ?? '')) ?: null,
    ':highlights'     => trim((string)($data['highlights'] ?? '')) ?: null,
    ':badge'          => trim((string)($data['badge'] ?? '')) ?: null,
    ':sort_order'     => (int)($data['sort_order'] ?? 0),
    ':is_active'      => !empty($data['is_active']) ? 1 : 0,
  ]);
  return (int)$pdo->lastInsertId();
}

function packages_update(PDO $pdo, int $id, array $data): void {
  $stmt = $pdo->prepare("
    UPDATE packages SET
      title = :title,
      subtitle = :subtitle,
      price = :price,
      currency = :currency,
      duration_label = :duration_label,
      highlights = :highlights,
      badge = :badge,
      sort_order = :sort_order,
      is_active = :is_active
    WHERE id = :id
    LIMIT 1
  ");
  $stmt->execute([
    ':id'             => $id,
    ':title'          => trim((string)($data['title'] ?? '')),
    ':subtitle'       => trim((string)($data['subtitle'] ?? '')) ?: null,
    ':price'          => ($data['price'] === '' || $data['price'] === null) ? null : (float)$data['price'],
    ':currency'       => trim((string)($data['currency'] ?? 'AED')) ?: 'AED',
    ':duration_label' => trim((string)($data['duration_label'] ?? '')) ?: null,
    ':highlights'     => trim((string)($data['highlights'] ?? '')) ?: null,
    ':badge'          => trim((string)($data['badge'] ?? '')) ?: null,
    ':sort_order'     => (int)($data['sort_order'] ?? 0),
    ':is_active'      => !empty($data['is_active']) ? 1 : 0,
  ]);
}

function packages_delete(PDO $pdo, int $id): void {
  $stmt = $pdo->prepare("DELETE FROM packages WHERE id = :id LIMIT 1");
  $stmt->execute([':id' => $id]);
}
