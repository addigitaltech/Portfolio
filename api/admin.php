<?php
// Admin API (session + CSRF protected). Actions: login, logout, me, posts, post_save, post_delete, enquiries, enquiry_status.
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
start_session();

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

function need_admin(): void {
  if (empty($_SESSION['admin_id'])) json_out(['error' => 'Please sign in.'], 401);
}
function need_csrf(): void {
  $t = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
  if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $t)) json_out(['error' => 'Session expired. Reload and sign in again.'], 403);
}
function slugify(string $s): string {
  $s = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $s) ?? '', '-'));
  return $s !== '' ? mb_substr($s, 0, 120) : 'post';
}

if ($action === 'login' && $method === 'POST') {
  if (!rate_limit('login', 8, 900, false)) json_out(['error' => 'Too many attempts. Try again in 15 minutes.'], 429);
  $d = body();
  $u = clean($d['username'] ?? '', 60); $p = is_string($d['password'] ?? null) ? $d['password'] : '';
  $st = db()->prepare('SELECT id, password_hash FROM admin_users WHERE username=?'); $st->execute([$u]);
  $row = $st->fetch();
  $hash = $row['password_hash'] ?? '$2y$10$abcdefghijklmnopqrstuuABCDEFGHIJKLMNOPQRSTUVWXYZ012';
  $ok = password_verify($p, $hash) && $row;
  if (!$ok) { rate_limit('login', 8, 900); json_out(['error' => 'Incorrect username or password.'], 401); }
  session_regenerate_id(true);
  $_SESSION['admin_id'] = (int)$row['id'];
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
  json_out(['ok' => true, 'csrf' => $_SESSION['csrf']]);
}

if ($action === 'me' && $method === 'GET') {
  if (empty($_SESSION['admin_id'])) json_out(['signedIn' => false]);
  json_out(['signedIn' => true, 'csrf' => $_SESSION['csrf']]);
}

if ($action === 'logout' && $method === 'POST') {
  need_csrf();
  $_SESSION = []; session_destroy();
  json_out(['ok' => true]);
}

need_admin();

if ($action === 'posts' && $method === 'GET') {
  json_out(['posts' => db()->query('SELECT id, slug, title, category, summary, body, link_url, cover_image, is_featured, is_published, ' . iso('published_at') . ' AS published_at FROM posts ORDER BY id DESC LIMIT 200')->fetchAll()]);
}

if ($action === 'post_save' && $method === 'POST') {
  need_csrf();
  $d = body();
  $id = (int)($d['id'] ?? 0);
  $title = clean($d['title'] ?? '', 160); $cat = clean($d['category'] ?? 'Update', 40) ?: 'Update';
  $sum = clean($d['summary'] ?? '', 300); $text = clean($d['body'] ?? '', 20000);
  $link = clean($d['link_url'] ?? '', 255); $pub = !empty($d['is_published']) ? 1 : 0;
  $cover = clean($d['cover_image'] ?? '', 255); $feat = !empty($d['is_featured']) ? 1 : 0;
  if ($cover !== '' && !preg_match('#^api/image\.php\?f=[a-f0-9]{32}\.(jpg|png|webp)$#', $cover)) json_out(['error' => 'Invalid cover image.'], 422);
  if ($title === '' || $sum === '' || $text === '') json_out(['error' => 'Title, summary and body are required.'], 422);
  if ($link !== '' && !preg_match('#^https?://#i', $link)) json_out(['error' => 'The link must start with http:// or https://'], 422);
  if ($id > 0) {
    db()->prepare('UPDATE posts SET title=?, category=?, summary=?, body=?, link_url=?, cover_image=?, is_featured=?, is_published=?, updated_at=NOW(), published_at = CASE WHEN ?::int=1 THEN COALESCE(published_at, NOW()) ELSE NULL END WHERE id=?')
      ->execute([$title, $cat, $sum, $text, $link ?: null, $cover ?: null, $feat, $pub, $pub, $id]);
  } else {
    $slug = slugify($title);
    $chk = db()->prepare('SELECT COUNT(*) FROM posts WHERE slug=?'); $chk->execute([$slug]);
    if ((int)$chk->fetchColumn() > 0) $slug .= '-' . substr(bin2hex(random_bytes(3)), 0, 5);
    $ins = db()->prepare('INSERT INTO posts (slug,title,category,summary,body,link_url,cover_image,is_featured,is_published,published_at) VALUES (?,?,?,?,?,?,?,?,?, CASE WHEN ?::int=1 THEN NOW() ELSE NULL END) RETURNING id');
    $ins->execute([$slug, $title, $cat, $sum, $text, $link ?: null, $cover ?: null, $feat, $pub, $pub]);
    $newId = (int)$ins->fetchColumn();
  }
  if ($feat === 1) {
    $cur = $id > 0 ? $id : ($newId ?? 0);
    db()->prepare('UPDATE posts SET is_featured=0 WHERE id<>?')->execute([$cur]);
  }
  json_out(['ok' => true]);
}

if ($action === 'upload' && $method === 'POST') {
  need_csrf();
  $f = $_FILES['image'] ?? null;
  if (!$f || ($f['error'] ?? 1) !== UPLOAD_ERR_OK) json_out(['error' => 'Upload failed.'], 422);
  if ($f['size'] > 2500000) json_out(['error' => 'Image must be under 2.5 MB.'], 422);
  $info = @getimagesize($f['tmp_name']);
  $map = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
  if (!$info || !isset($map[$info['mime']])) json_out(['error' => 'Use a JPG, PNG or WebP image.'], 422);
  $name = bin2hex(random_bytes(16)) . '.' . $map[$info['mime']];
  $fh = fopen($f['tmp_name'], 'rb');
  $st = db()->prepare('INSERT INTO images (name, mime, data) VALUES (?, ?, ?)');
  $st->bindValue(1, $name); $st->bindValue(2, $info['mime']); $st->bindParam(3, $fh, PDO::PARAM_LOB);
  $st->execute();
  json_out(['ok' => true, 'path' => 'api/image.php?f=' . $name]);
}

if ($action === 'post_delete' && $method === 'POST') {
  need_csrf();
  $id = (int)(body()['id'] ?? 0);
  db()->prepare('DELETE FROM posts WHERE id=?')->execute([$id]);
  json_out(['ok' => true]);
}

if ($action === 'enquiries' && $method === 'GET') {
  json_out(['enquiries' => db()->query('SELECT id, name, email, phone, organisation, project_type, budget, timeframe, message, status, ' . iso('created_at') . ' AS created_at FROM enquiries ORDER BY id DESC LIMIT 200')->fetchAll()]);
}

if ($action === 'enquiry_status' && $method === 'POST') {
  need_csrf();
  $d = body(); $status = $d['status'] ?? '';
  if (!in_array($status, ['new', 'contacted', 'closed'], true)) json_out(['error' => 'Invalid status.'], 422);
  db()->prepare('UPDATE enquiries SET status=? WHERE id=?')->execute([$status, (int)($d['id'] ?? 0)]);
  json_out(['ok' => true]);
}

json_out(['error' => 'Not found.'], 404);
