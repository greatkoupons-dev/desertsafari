<?php
declare(strict_types=1);

// NOTE: this file lives in /admin/partials, so project includes are two levels up.
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
<a href="/admin/packages.php">Packages</a>
$user = admin_user();
$active = $active ?? '';

?>

