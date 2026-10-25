<?php
// RSS feed of published news.
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
$base = base_url();
$rows = db()->query('SELECT slug, title, summary, category, ' . iso('published_at') . ' AS published_at FROM posts WHERE is_published=1 ORDER BY posts.published_at DESC LIMIT 30')->fetchAll();
$e = fn(string $s): string => htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
header('Content-Type: application/rss+xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel><title>AD Digital Tech: Technology News</title><link>' . $e($base . '/news.html') . '</link><description>Technology news and engineering updates</description>';
foreach ($rows as $r) {
  echo '<item><title>' . $e($r['title']) . '</title><link>' . $e($base . '/news.html?s=' . rawurlencode($r['slug'])) . '</link><guid>' . $e($base . '/news.html?s=' . rawurlencode($r['slug'])) . '</guid><category>' . $e($r['category']) . '</category><description>' . $e($r['summary']) . '</description><pubDate>' . gmdate('r', strtotime((string)$r['published_at'])) . '</pubDate></item>';
}
echo '</channel></rss>';
