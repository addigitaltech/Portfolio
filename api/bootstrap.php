<?php
declare(strict_types=1);

function cfg(): array { static $c = null; return $c ??= require __DIR__ . '/config.php'; }

function db(): PDO {
  static $pdo = null;
  if ($pdo === null) {
    $c = cfg()['db'];
    $pdo = new PDO("mysql:host={$c['host']};dbname={$c['name']};charset=utf8mb4", $c['user'], $c['pass'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);
  }
  return $pdo;
}

function json_out(array $data, int $code = 200): never {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store');
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
  $st = $pdo->prepare('SELECT COUNT(*) FROM rate_limits WHERE bucket=? AND ip_hash=? AND created_at > (NOW() - (?::int * INTERVAL '1 second'))');
  $st->execute([$bucket, $ip, $windowSec]);
  if ((int)$st->fetchColumn() >= $max) return false;
  if ($record) {
    $pdo->prepare('INSERT INTO rate_limits (bucket, ip_hash) VALUES (?, ?)')->execute([$bucket, $ip]);
    if (random_int(1, 50) === 1) $pdo->exec('DELETE FROM rate_limits WHERE created_at < (NOW() - INTERVAL '2 days')');
  }
  return true;
}

function start_session(): void {
  if (session_status() === PHP_SESSION_ACTIVE) return;
  $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
  session_name('adt_admin');
  session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Strict']);
  session_start();
}
