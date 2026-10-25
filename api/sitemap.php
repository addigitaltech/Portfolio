<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
$base = base_url();
$urls = ['/', '/about', '/services', '/work', '/team', '/process', '/contact', '/news.html'];
foreach (db()->query("SELECT collection, slug FROM entries WHERE is_published=1 AND slug IS NOT NULL AND collection IN ('services','projects')")->fetchAll() as $r)
  $urls[] = ($r['collection'] === 'services' ? '/service?s=' : '/project?s=') . rawurlencode($r['slug']);
foreach (db()->query('SELECT slug FROM posts WHERE is_published=1')->fetchAll() as $r) $urls[] = '/news.html?s=' . rawurlencode($r['slug']);
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
foreach ($urls as $u) echo '<url><loc>' . htmlspecialchars($base . $u, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc></url>';
echo '</urlset>';
