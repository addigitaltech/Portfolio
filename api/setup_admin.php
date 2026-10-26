<?php
// ONE-TIME: creates the first admin account, then deletes itself. Requires setup_key from config.php.
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/content_lib.php';
header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: DENY');
$msg = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
  $key = (string)($_POST['key'] ?? ''); $u = clean($_POST['username'] ?? '', 60); $p = (string)($_POST['password'] ?? '');
  if (strlen(cfg()['setup_key']) < 16 || !hash_equals(cfg()['setup_key'], $key)) $msg = 'Wrong setup key.';
  elseif ((int)db()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() > 0) $msg = 'An admin already exists. Delete this file.';
  elseif (!preg_match('/^[A-Za-z0-9_.-]{3,60}$/', $u)) $msg = 'Username: 3-60 letters, numbers, . _ -';
  elseif (strlen($p) < 12) $msg = 'Password must be at least 12 characters.';
  else {
    db()->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)')->execute([$u, password_hash($p, PASSWORD_DEFAULT)]);
    seed_all();
    $msg = 'Admin created. Now REMOVE the SETUP_KEY environment variable and delete api/setup_admin.php from the repo. Sign in at /admin/';
  }
}
?><!DOCTYPE html><meta charset="utf-8"><meta name="robots" content="noindex"><title>Create admin</title>
<body style="font-family:sans-serif;max-width:420px;margin:40px auto;padding:0 16px">
<h1>Create admin</h1><p><?= htmlspecialchars($msg, ENT_QUOTES) ?></p>
<form method="post" autocomplete="off">
<p><label>Setup key<br><input name="key" type="password" required style="width:100%"></label></p>
<p><label>Username<br><input name="username" required style="width:100%"></label></p>
<p><label>Password (12+ characters)<br><input name="password" type="password" required minlength="12" style="width:100%"></label></p>
<button>Create admin</button></form></body>
