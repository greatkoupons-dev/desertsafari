<?php
declare(strict_types=1);
<li><a href="/admin/packages.php">Packages</a></li>
// NOTE: this file lives in /admin/partials, so project includes are two levels up.
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

$user = admin_user();
$active = $active ?? '';

?>
