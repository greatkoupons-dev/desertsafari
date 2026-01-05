<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();
$active = 'blog';

$editId = (int)($_GET['edit'] ?? 0);
$editing = $editId ? one("SELECT * FROM blog_posts WHERE id=?", [$editId]) : null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $action = (string)($_POST['action'] ?? '');
  if ($action === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim((string)($_POST['title'] ?? ''));
    $slug = trim((string)($_POST['slug'] ?? ''));
    $excerpt = trim((string)($_POST['excerpt'] ?? ''));
    $cover_url = trim((string)($_POST['cover_url'] ?? ''));
    $content_html = (string)($_POST['content_html'] ?? '');
    $seo_title = trim((string)($_POST['seo_title'] ?? ''));
    $seo_description = trim((string)($_POST['seo_description'] ?? ''));
    $status = (string)($_POST['status'] ?? 'draft');

    if ($title === '') redirect(url('admin/blog.php'));

    if ($slug === '') $slug = slugify($title);

    // Ensure unique slug
    $existing = one("SELECT id FROM blog_posts WHERE slug=? AND id<>? LIMIT 1", [$slug, $id ?: 0]);
    if ($existing) {
      $slug = $slug . '-' . substr((string)time(), -4);
    }

    $now = date('Y-m-d H:i:s');
    $published_at = $editing['published_at'] ?? null;
    if ($status === 'published' && !$published_at) $published_at = $now;

    if ($id) {
      q("UPDATE blog_posts SET title=?, slug=?, excerpt=?, cover_url=?, content_html=?, seo_title=?, seo_description=?, status=?, published_at=?, updated_at=? WHERE id=?",
        [$title,$slug,$excerpt,$cover_url,$content_html,$seo_title,$seo_description,$status,$published_at,$now,$id]);
    } else {
      q("INSERT INTO blog_posts(title, slug, excerpt, cover_url, content_html, seo_title, seo_description, status, published_at, created_at, updated_at)
         VALUES(?,?,?,?,?,?,?,?,?,?,?)",
        [$title,$slug,$excerpt,$cover_url,$content_html,$seo_title,$seo_description,$status,$published_at,$now,$now]);
    }
  }

  if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) q("DELETE FROM blog_posts WHERE id=?", [$id]);
  }

  redirect(url('admin/blog.php'));
}

$list = all("SELECT id, title, slug, status, published_at, updated_at FROM blog_posts ORDER BY COALESCE(published_at, created_at) DESC, id DESC LIMIT 200");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Blog • DesertSafariGo</title>
  <link rel="stylesheet" href="<?= e(url('admin/assets/admin.css')) ?>">
</head>
<body>
  <?php require __DIR__ . '/partials/nav.php'; ?>
  <div style="padding:18px 0">
    <div class="container">
      <div class="grid">
        <div class="card">
          <div class="h1"><?= $editing ? 'Edit Post' : 'Add Post' ?></div>
          <form method="post">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

            <div style="margin-top:6px">
              <label class="small">Title *</label>
              <input class="input" name="title" required value="<?= e($editing['title'] ?? '') ?>">
            </div>

            <div class="row" style="margin-top:10px; gap:14px">
              <div style="flex:1">
                <label class="small">Slug (URL)</label>
                <input class="input" name="slug" value="<?= e($editing['slug'] ?? '') ?>" placeholder="auto-generated if empty">
                <div class="small">URL will be: /blog/<strong>slug</strong></div>
              </div>
              <div style="width:220px">
                <label class="small">Status</label>
                <select class="input" name="status">
                  <?php foreach(['draft','published'] as $s): ?>
                    <option value="<?= e($s) ?>" <?= (($editing['status'] ?? 'draft')===$s)?'selected':'' ?>><?= e($s) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="small">Publishing sets publish date automatically.</div>
              </div>
            </div>

            <div style="margin-top:10px">
              <label class="small">Excerpt (shown on cards) *</label>
              <textarea class="input" name="excerpt" required><?= e($editing['excerpt'] ?? '') ?></textarea>
            </div>

            <div style="margin-top:10px">
              <label class="small">Cover Image URL</label>
              <input class="input" name="cover_url" value="<?= e($editing['cover_url'] ?? '') ?>" placeholder="https://...">
            </div>

            <div style="margin-top:10px">
              <label class="small">Content (HTML allowed)</label>
              <textarea class="input" name="content_html" style="min-height:220px"><?= e($editing['content_html'] ?? '<p>Your content here...</p>') ?></textarea>
              <div class="small">You can paste HTML. Basic tags like &lt;p&gt;, &lt;h2&gt;, &lt;ul&gt; work well.</div>
            </div>

            <div class="grid" style="margin-top:10px">
              <div>
                <label class="small">SEO Title</label>
                <input class="input" name="seo_title" value="<?= e($editing['seo_title'] ?? '') ?>">
              </div>
              <div>
                <label class="small">SEO Description</label>
                <input class="input" name="seo_description" value="<?= e($editing['seo_description'] ?? '') ?>">
              </div>
            </div>

            <div class="row" style="margin-top:14px">
              <button class="btn primary" type="submit">Save</button>
              <?php if($editing): ?><a class="btn" href="<?= e(url('admin/blog.php')) ?>">Cancel</a><?php endif; ?>
              <?php if($editing && ($editing['status'] ?? '') === 'published'): ?>
                <a class="btn" target="_blank" href="<?= e(url('blog/'.$editing['slug'])) ?>">View</a>
              <?php endif; ?>
            </div>
          </form>
        </div>

        <div class="card">
          <div class="row" style="justify-content:space-between">
            <div class="h1" style="margin:0">All Posts</div>
            <a class="btn" href="<?= e(url('blog')) ?>" target="_blank">Open Blog</a>
          </div>
          <div style="overflow:auto; margin-top:10px">
            <table class="table">
              <thead><tr><th>ID</th><th>Title</th><th>Status</th><th>Published</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach($list as $p): ?>
                  <tr>
                    <td><?= (int)$p['id'] ?></td>
                    <td>
                      <div style="font-weight:800"><?= e($p['title']) ?></div>
                      <div class="small">/blog/<?= e($p['slug']) ?></div>
                    </td>
                    <td><?= e($p['status']) ?></td>
                    <td><?= e($p['published_at'] ?? '') ?></td>
                    <td class="row">
                      <a class="btn" href="<?= e(url('admin/blog.php?edit='.$p['id'])) ?>">Edit</a>
                      <form method="post" onsubmit="return confirm('Delete post #<?= (int)$p['id'] ?>?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="btn danger" type="submit">Delete</button>
                      </form>
                      <?php if(($p['status'] ?? '') === 'published'): ?>
                        <a class="btn" target="_blank" href="<?= e(url('blog/'.$p['slug'])) ?>">View</a>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if(!$list): ?><tr><td colspan="5" class="small">No posts yet.</td></tr><?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="small" style="margin-top:10px">SEO tip: keep excerpt 140–170 characters; use cover images for higher CTR.</div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
