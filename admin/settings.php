<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();
$active = 'settings';

function set_setting(string $key, string $value): void {
  q("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)", [$key, $value]);
}
function get_setting(string $key, string $default=''): string {
  $row = one("SELECT value FROM settings WHERE `key`=? LIMIT 1", [$key]);
  return $row ? (string)$row['value'] : $default;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  foreach($_POST as $k=>$v){
    if ($k === 'save') continue;
    if (!is_string($v)) continue;
    set_setting($k, trim($v));
  }
  // checkboxes
  redirect(url('admin/settings.php?ok=1'));
}

$ok = isset($_GET['ok']);

$fields = [
  'site_name' => ['Site Name','DesertSafariGo'],
  'site_tagline_small' => ['Small tagline','Dubai experiences • instant booking'],

  'contact_phone' => ['Phone','+971 50 000 0000'],
  'contact_email' => ['Email','hello@desertsafarigo.com'],
  'contact_whatsapp' => ['WhatsApp','+971500000000'],

  'hero_badge' => ['Hero badge','Dubai • Premium Bookings'],
  'hero_title' => ['Hero title','Dubai Desert Safari — Chic, Fast, Verified'],
  'hero_subtitle' => ['Hero subtitle','Instant booking. Pickup on time. Transparent packages. Real support. Designed to convert — especially on mobile.'],
  'hero_bg_url' => ['Hero background image URL',''],
  'hero_cta_primary' => ['Hero primary CTA','Get instant quote'],
  'hero_cta_secondary' => ['Hero secondary CTA','View packages'],

  'usp_1' => ['USP 1','Free pickup'],
  'usp_2' => ['USP 2','No hidden charges'],
  'usp_3' => ['USP 3','Best time slots'],
  'usp_4' => ['USP 4','WhatsApp support'],

  'form_title' => ['Form title','Quick Booking Form'],
  'form_package_placeholder' => ['Form package placeholder','Select package'],
  'form_submit' => ['Form submit button','Submit'],
  'form_note' => ['Form note','Takes 10 seconds • Response within minutes'],
  'trust_1' => ['Trust badge 1','Secure & private'],
  'trust_2' => ['Trust badge 2','Real human support'],
  'trust_3' => ['Trust badge 3','Best value options'],

  'highlights_title' => ['Highlights title','Latest in Dubai — Experiences people love'],
  'highlights_sub' => ['Highlights subtitle','Swipe to explore trending experiences, best time slots, and premium add-ons.'],

  'packages_title' => ['Packages title','Our Packages'],
  'packages_sub' => ['Packages subtitle','Clear pricing, premium inclusions, and fast confirmation.'],

  'why_title' => ['Why us title','Why book with us?'],
  'why_sub' => ['Why us subtitle','Because Dubai is premium — your booking experience should be too. We optimize everything for comfort, trust, and speed.'],
  'why_bg_url' => ['Why us background image URL',''],
  'why_badge_1' => ['Why badge 1','Licensed partners'],
  'why_badge_2' => ['Why badge 2','Tourist-friendly'],
  'why_badge_3' => ['Why badge 3','Instant confirmation'],

  'testi_title' => ['Testimonials title','What customers say'],
  'testi_sub' => ['Testimonials subtitle','Real feedback — because trust matters.'],

  'blog_title' => ['Blog section title','Dubai Travel Blog'],
  'blog_sub' => ['Blog section subtitle','SEO-ready guides and booking tips.'],
  'seo_home_title' => ['SEO home title','DesertSafariGo • Dubai Desert Safari & Dhow Cruise'],
  'seo_home_desc' => ['SEO home description','Book premium Dubai experiences with fast pickup, transparent packages, and instant support.'],
  'seo_blog_title' => ['SEO blog title','Dubai Desert Safari Blog • DesertSafariGo'],
  'seo_blog_desc' => ['SEO blog description','Dubai travel guides, safari tips, and booking information.'],

  'footer_bg_url' => ['Footer background image URL',''],

  'chat_link' => ['Chat link (WhatsApp or other)',''],
  'primary_cta_text' => ['Primary CTA text','Book now'],
  'lead_webhook_url' => ['Lead webhook URL (optional)',''],
];

$values = [];
foreach($fields as $k=>$meta){
  $values[$k] = get_setting($k, (string)$meta[1]);
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Settings • DesertSafariGo</title>
  <link rel="stylesheet" href="<?= e(url('admin/assets/admin.css')) ?>">
</head>
<body>
  <?php require __DIR__ . '/partials/nav.php'; ?>
  <div style="padding:18px 0">
    <div class="container">
      <?php if($ok): ?>
        <div class="card" style="border-color: rgba(47,111,103,.28); background: rgba(47,111,103,.06); margin-bottom:14px">
          <strong>Saved.</strong> Settings are live on the homepage immediately.
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="row" style="justify-content:space-between">
          <div>
            <div class="h1" style="margin:0">Site Settings</div>
            <div class="small">Edit homepage text, CTA labels, contact info, backgrounds, and webhook integration.</div>
          </div>
          <a class="btn" href="<?= e(url('/')) ?>" target="_blank">Open Website</a>
        </div>

        <form method="post" style="margin-top:12px">
          <div class="grid">
            <?php foreach($fields as $k=>$meta): ?>
              <div>
                <label class="small"><?= e($meta[0]) ?></label>
                <?php
                  $isLong = in_array($k, ['hero_subtitle','why_sub','form_note','highlights_sub','packages_sub','testi_sub','blog_sub','seo_home_desc','seo_blog_desc'], true);
                  if ($isLong):
                ?>
                  <textarea class="input" name="<?= e($k) ?>"><?= e($values[$k] ?? '') ?></textarea>
                <?php else: ?>
                  <input class="input" name="<?= e($k) ?>" value="<?= e($values[$k] ?? '') ?>">
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="row" style="margin-top:14px">
            <button class="btn primary" name="save" value="1" type="submit">Save Settings</button>
            <div class="small">If you add image URLs, use full https links. The design is optimized for high-quality photos.</div>
          </div>
        </form>
      </div>

      <div class="card" style="margin-top:14px">
        <div class="h1">Lead Webhook (optional)</div>
        <div class="small">
          If you paste a webhook URL (e.g., Zapier / Make / n8n), every new lead is POSTed as JSON immediately.
        </div>
      </div>
    </div>
  </div>
</body>
</html>
