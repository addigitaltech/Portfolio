<?php
// Serves cover images stored in the database.
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
$f = (string)($_GET['f'] ?? '');
if (!preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $f)) { http_response_code(404); exit; }
$st = db()->prepare('SELECT mime, data FROM images WHERE name=?');
$st->execute([$f]);
$r = $st->fetch();
if (!$r) { http_response_code(404); exit; }
$data = is_resource($r['data']) ? stream_get_contents($r['data']) : $r['data'];
header('Content-Type: ' . $r['mime']);
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=31536000, immutable');
echo $data;
