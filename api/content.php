<?php
// Public, read-only: all published website content in one response.
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/content_lib.php';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') json_out(['error' => 'Method not allowed.'], 405);
$out = ['settings' => settings_all()];
foreach (array_keys(content_schema()['collections']) as $c) $out[$c] = [];
$st = db()->query('SELECT collection, slug, data FROM entries WHERE is_published=1 ORDER BY collection, sort_order, id');
foreach ($st->fetchAll() as $r) {
  $d = json_decode((string)$r['data'], true);
  if (!is_array($d) || !isset($out[$r['collection']])) continue;
  if ($r['slug'] !== null) $d['slug'] = $r['slug'];
  $out[$r['collection']][] = $d;
}
json_out($out, 200, 'public, max-age=30');
