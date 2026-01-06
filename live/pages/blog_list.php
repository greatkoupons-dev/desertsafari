<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/site.php';
require_once __DIR__ . '/../includes/functions.php';

$site_name = setting('site_name','DesertSafariGo');
$seo_title = setting('seo_blog_title','Dubai Desert Safari Blog • DesertSafariGo');
$seo_description = setting('seo_blog_desc','Dubai travel guides, safari tips, and booking information.');

$page = max(1, (int)($_GET['page'] ?? 1));
$per = 9;
$offset = ($page - 1) * $per;

$totalRow = one("SELECT COUNT(*) AS c FROM blog_posts WHERE status='published'");
$total = (int)($totalRow['c'] ?? 0);

$posts = all("SELECT id, title, slug, excerpt, cover_url, published_at FROM blog_posts WHERE status='published' ORDER BY published_at DESC, id DESC LIMIT {$per} OFFSET {$offset}");

$footer_bg_url = setting('footer_bg_url', url('assets/img/placeholder.jpg'));
$phone = setting('contact_phone','+971 50 000 0000');
$email = setting('contact_email','hello@desertsafarigo.com');
$whatsapp = setting('contact_whatsapp','+971500000000');

require __DIR__ . '/../partials/head.php';
?>

<section class="section">
  <div class="container">
    <h2>Blog</h2>
    <p class="lead">Guides built for SEO and trust — created to help customers book confidently.</p>

    <div class="blogGrid">
      <?php foreach($posts as $p): ?>
        <div class="card">
          <div class="img" style="background-image:url('<?= e($p['cover_url'] ?: url('assets/img/placeholder.jpg')) ?>')"></div>
          <div class="postMeta"><?= e(date('M d, Y', strtotime($p['published_at'] ?: 'now'))) ?></div>
          <div class="postTitle"><?= e($p['title']) ?></div>
          <div class="postExcerpt"><?= e($p['excerpt']) ?></div>
          <div style="margin-top:10px"><a class="btn primary" href="<?= e(url('blog/'.$p['slug'])) ?>">Read</a></div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php
      $pages = (int)ceil($total / $per);
      if ($pages > 1):
    ?>
    <div style="margin-top:18px; display:flex; gap:10px; flex-wrap:wrap">
      <?php for($i=1; $i<=$pages; $i++): ?>
        <a class="btn <?= $i===$page ? 'primary':'' ?>" href="<?= e(url('blog?page='.$i)) ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
