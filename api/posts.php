<?php
// Public, read-only: published news. ?slug=... for one story; ?limit&offset&category for lists.
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') json_out(['error' => 'Method not allowed.'], 405);
$pdo = db();
$slug = clean($_GET['slug'] ?? '', 140);
if ($slug !== '') {
  $st = $pdo->prepare('SELECT slug, title, category, summary, body, link_url, cover_image, is_featured, '.iso('published_at').' AS published_at FROM posts WHERE is_published=1 AND slug=?');
  $st->execute([$slug]);
  $p = $st->fetch();
  if (!$p) json_out(['error' => 'Not found.'], 404);
  json_out(['post' => $p]);
}
$limit = max(1, min(24, (int)($_GET['limit'] ?? 9)));
$offset = max(0, (int)($_GET['offset'] ?? 0));
$cat = clean($_GET['category'] ?? '', 40);
$where = 'is_published=1'; $args = [];
if ($cat !== '') { $where .= ' AND category=?'; $args[] = $cat; }
$c = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE $where"); $c->execute($args);
$st = $pdo->prepare('SELECT slug, title, category, summary, link_url, cover_image, is_featured, ' . iso('published_at') . " AS published_at FROM posts WHERE $where ORDER BY is_featured DESC, posts.published_at DESC, id DESC LIMIT $limit OFFSET $offset");
$st->execute($args);
$cats = $pdo->query('SELECT DISTINCT category FROM posts WHERE is_published=1 ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
json_out(['posts' => $st->fetchAll(), 'total' => (int)$c->fetchColumn(), 'categories' => $cats]);
