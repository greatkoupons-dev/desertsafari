<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/site.php';
require_once __DIR__ . '/../includes/functions.php';

$slug = (string)($params['slug'] ?? '');
$post = one("SELECT * FROM blog_posts WHERE slug = ? AND status='published' LIMIT 1", [$slug]);
if (!$post) {
  http_response_code(404);
  $seo_title = "Not found";
  $seo_description = "Post not found.";
  require __DIR__ . '/../partials/head.php';
  echo '<section class="section"><div class="container"><h2>Post not found</h2><p class="lead">Please go back to the blog.</p><a class="btn primary" href="'.e(url('blog')).'">Back to blog</a></div></section>';
  require __DIR__ . '/../partials/footer.php';
  exit;
}

$site_name = setting('site_name','DesertSafariGo');
$seo_title = $post['seo_title'] ?: ($post['title'] . ' • ' . $site_name);
$seo_description = $post['seo_description'] ?: $post['excerpt'];

$footer_bg_url = setting('footer_bg_url', url('assets/img/placeholder.jpg'));
$phone = setting('contact_phone','+971 50 000 0000');
$email = setting('contact_email','hello@desertsafarigo.com');
$whatsapp = setting('contact_whatsapp','+971500000000');

require __DIR__ . '/../partials/head.php';
?>

<section class="section">
  <div class="container">
    <div class="badge">Guide</div>
    <h1 class="hTitle" style="font-size:36px; margin-top:10px"><?= e($post['title']) ?></h1>
    <div class="postMeta"><?= e(date('M d, Y', strtotime($post['published_at'] ?: 'now'))) ?></div>

    <div class="card" style="margin-top:14px">
      <div class="img" style="height:280px; background-image:url('<?= e($post['cover_url'] ?: url('assets/img/placeholder.jpg')) ?>')"></div>
      <div style="margin-top:14px; line-height:1.8; color:rgba(16,19,22,.82); font-size:16px">
        <?= $post['content_html'] ?>
      </div>
    </div>

    <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap">
      <a class="btn" href="<?= e(url('blog')) ?>">Back to blog</a>
      <a class="btn primary" href="<?= e(url('/#lead')) ?>">Book now</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
