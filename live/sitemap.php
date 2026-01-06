<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');
$base = rtrim(base_url(), '/');

$urls = [];
$urls[] = ['loc'=>$base.'/', 'lastmod'=>date('c')];
$urls[] = ['loc'=>$base.'/blog', 'lastmod'=>date('c')];

$posts = all("SELECT slug, updated_at, published_at FROM blog_posts WHERE status='published' ORDER BY published_at DESC LIMIT 500");
foreach($posts as $p){
  $lm = $p['updated_at'] ?: $p['published_at'] ?: date('Y-m-d H:i:s');
  $urls[] = ['loc'=>$base.'/blog/'.$p['slug'], 'lastmod'=>date('c', strtotime($lm))];
}
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach($urls as $u): ?>
  <url>
    <loc><?= e($u['loc']) ?></loc>
    <lastmod><?= e($u['lastmod']) ?></lastmod>
  </url>
<?php endforeach; ?>
</urlset>
