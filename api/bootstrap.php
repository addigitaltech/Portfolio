<?php
declare(strict_types=1);

function cfg(): array {
  static $c = null;
  if ($c === null) {
    $f = __DIR__ . '/config.php';
    $file = is_file($f) ? (array)require $f : [];
    $env = fn(string $k, string $fk): string => (string)(getenv($k) ?: ($file[$fk] ?? ''));
    $c = [
      'database_url' => $env('DATABASE_URL', 'database_url'),
      'app_secret'   => $env('APP_SECRET', 'app_secret'),
      'setup_key'    => $env('SETUP_KEY', 'setup_key'),
      'notify_email' => $env('NOTIFY_EMAIL', 'notify_email'),
      'mail_from'    => $env('MAIL_FROM', 'mail_from') ?: 'no-reply@localhost',
    ];
  }
  return $c;
}

function db(): PDO {
  static $pdo = null;
  if ($pdo === null) {
    $u = parse_url(cfg()['database_url']);
    if (!$u || empty($u['host']) || empty($u['path'])) throw new RuntimeException('DATABASE_URL is missing or invalid');
    $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s;sslmode=require', $u['host'], $u['port'] ?? 5432, ltrim($u['path'], '/'));
    $pdo = new PDO($dsn, urldecode($u['user'] ?? ''), urldecode($u['pass'] ?? ''), [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);
  }
  return $pdo;
}

/** SQL fragment: timestamp column as ISO-8601 UTC text, for JSON output. */
function iso(string $col): string {
  return "to_char($col AT TIME ZONE 'UTC', 'YYYY-MM-DD\"T\"HH24:MI:SS\"Z\"')";
}

function json_out(array $data, int $code = 200, string $cache = 'no-store'): never {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: ' . $cache);
  header('X-Content-Type-Options: nosniff');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

set_exception_handler(function (Throwable $e): void {
  error_log('[addigitaltech] ' . $e->getMessage());
  json_out(['error' => 'Something went wrong. Please try again later.'], 500);
});

function body(): array {
  $raw = file_get_contents('php://input') ?: '';
  if (strlen($raw) > 60000) json_out(['error' => 'Request too large.'], 413);
  $d = json_decode($raw, true);
  return is_array($d) ? $d : [];
}

function clean(mixed $v, int $max): string {
  $s = is_string($v) ? trim($v) : '';
  $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $s) ?? '';
  return mb_substr($s, 0, $max);
}

function ip_hash(): string {
  if (strlen(cfg()['app_secret']) < 16) throw new RuntimeException('APP_SECRET must be set (16+ characters)');
  return hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . cfg()['app_secret']);
}

/** Returns false (and records nothing) when the limit is already reached. */
function rate_limit(string $bucket, int $max, int $windowSec, bool $record = true): bool {
  $pdo = db(); $ip = ip_hash();
  $st = $pdo->prepare("SELECT COUNT(*) FROM rate_limits WHERE bucket=? AND ip_hash=? AND created_at > (NOW() - (?::int * INTERVAL '1 second'))");
  $st->execute([$bucket, $ip, $windowSec]);
  if ((int)$st->fetchColumn() >= $max) return false;
  if ($record) {
    $pdo->prepare('INSERT INTO rate_limits (bucket, ip_hash) VALUES (?, ?)')->execute([$bucket, $ip]);
    if (random_int(1, 50) === 1) $pdo->exec("DELETE FROM rate_limits WHERE created_at < (NOW() - INTERVAL '2 days')");
  }
  return true;
}

function start_session(): void {
  if (session_status() === PHP_SESSION_ACTIVE) return;
  $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
  session_name('adt_admin');
  session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Strict']);
  session_start();
}

function base_url(): string {
  $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
  return ($https ? 'https://' : 'http://') . preg_replace('/[^A-Za-z0-9.\-:]/', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
}

function upsert_setting(string $k, string $v): void {
  db()->prepare('INSERT INTO settings (k, v) VALUES (?, ?) ON CONFLICT (k) DO UPDATE SET v = EXCLUDED.v')->execute([$k, $v]);
}
