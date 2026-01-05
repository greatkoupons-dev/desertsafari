<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/auth.admin.php';
require_once __DIR__ . '/../includes/packages.model.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
  packages_delete($pdo, $id);
}
header('Location: packages.php');
exit;
