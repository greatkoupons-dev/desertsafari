<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= e($seo_title ?? 'DesertSafariGo • Dubai Desert Safari & Dhow Cruise') ?></title>
  <meta name="description" content="<?= e($seo_description ?? 'Book premium Dubai experiences with fast pickup, transparent packages, and instant support.') ?>">
  <meta name="app-base" content="<?= e(base_url()) ?>">
  <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>?v=<?= (string)@filemtime(__DIR__.'/../assets/css/style.css') ?>">
</head>
<body>
<header class="header">
  <div class="container">
    <div class="wrap">
      <div class="brand">
        <div class="logo"></div>
        <div>
          <div style="font-weight:900; letter-spacing:-.2px"><?= e($site_name ?? 'DesertSafariGo') ?></div>
          <div style="font-size:12px; color:rgba(16,19,22,.58)"><?= e($site_tagline_small ?? 'Dubai experiences • instant booking') ?></div>
        </div>
      </div>

      <nav class="nav">
        <a href="<?= e(url('#packages')) ?>">Packages</a>
        <a href="<?= e(url('#why')) ?>">Why us</a>
        <a href="<?= e(url('#reviews')) ?>">Testimonials</a>
        <a href="<?= e(url('blog')) ?>">Blog</a>
        <a class="btn primary" href="<?= e($primary_cta_link ?? url('#lead')) ?>"><?= e($primary_cta_text ?? 'Book now') ?></a>
      </nav>

      <button class="btn menuBtn" type="button" data-drawer-open aria-label="Menu">
        <span class="menuIcon" aria-hidden="true"><i></i><i></i><i></i></span>
      </button>
    </div>

    <!-- Mobile drawer (slides from right) -->
    <div class="drawerOverlay" data-drawer-overlay aria-hidden="true"></div>
    <aside class="drawer" data-drawer aria-hidden="true" aria-label="Site menu">
      <div class="drawerHead">
        <div class="drawerBrand">
          <div class="logo"></div>
          <div>
            <div style="font-weight:900; letter-spacing:-.2px"><?= e($site_name ?? 'DesertSafariGo') ?></div>
            <div style="font-size:12px; color:rgba(16,19,22,.58)"><?= e($site_tagline_small ?? 'Dubai experiences • instant booking') ?></div>
          </div>
        </div>
        <button class="drawerClose" type="button" data-drawer-close aria-label="Close menu">×</button>
      </div>
      <nav class="drawerNav">
        <a href="<?= e(url('#packages')) ?>" data-drawer-link>Packages</a>
        <a href="<?= e(url('#why')) ?>" data-drawer-link>Why us</a>
        <a href="<?= e(url('#reviews')) ?>" data-drawer-link>Testimonials</a>
        <a href="<?= e(url('blog')) ?>" data-drawer-link>Blog</a>
        <a class="btn primary" href="<?= e($primary_cta_link ?? url('#lead')) ?>" data-drawer-link><?= e($primary_cta_text ?? 'Book now') ?></a>
      </nav>
    </aside>
  </div>
</header>
