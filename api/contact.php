<?php
// Public: receives project enquiries, stores them, optionally emails an alert.
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') json_out(['error' => 'Method not allowed.'], 405);

$d = body();
if (clean($d['website'] ?? '', 50) !== '') json_out(['ok' => true]);            // honeypot: pretend success
$elapsed = (int)($d['elapsed'] ?? 0);
if ($elapsed < 3000) json_out(['error' => 'Please take a moment to complete the form and try again.'], 422);

$name  = clean($d['name'] ?? '', 120);
$email = clean($d['email'] ?? '', 160);
$phone = clean($d['phone'] ?? '', 40);
$org   = clean($d['org'] ?? '', 120);
$type  = clean($d['type'] ?? '', 60);
$budget = clean($d['budget'] ?? '', 80);
$time  = clean($d['timeframe'] ?? '', 80);
$msg   = clean($d['message'] ?? '', 4000);
$method = clean($d['method'] ?? '', 20);

$errors = [];
if (mb_strlen($name) < 2) $errors['name'] = 'Enter your full name.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email address.';
if (strlen(preg_replace('/\D/', '', $phone) ?? '') < 7) $errors['phone'] = 'Enter a phone or WhatsApp number.';
if ($type === '') $errors['type'] = 'Choose a service.';
if (mb_strlen($msg) < 20) $errors['message'] = 'Describe your project in at least 20 characters.';
if ($errors) json_out(['error' => 'Please fix the highlighted fields.', 'fields' => $errors], 422);

if (!rate_limit('contact', 5, 3600)) json_out(['error' => 'Too many enquiries from this connection. Please try again later.'], 429);

db()->prepare('INSERT INTO enquiries (name,email,phone,organisation,project_type,budget,timeframe,message,contact_method) VALUES (?,?,?,?,?,?,?,?,?)')
  ->execute([$name, $email, $phone, $org ?: null, $type, $budget ?: null, $time ?: null, $msg, $method ?: null]);

$to = cfg()['notify_email'] ?? '';
if ($to !== '' && filter_var($to, FILTER_VALIDATE_EMAIL)) {
  $line = fn(string $s): string => str_replace(["\r", "\n"], ' ', $s);
  $text = "New project enquiry\n\nName: {$line($name)}\nEmail: {$line($email)}\nPhone: {$line($phone)}\nOrganisation: {$line($org)}\nType: {$line($type)}\nBudget: {$line($budget)}\nTimeframe: {$line($time)}\nPreferred contact: {$line($method)}\n\n$msg\n";
  $headers = 'From: ' . cfg()['mail_from'] . "\r\nReply-To: " . $line($email) . "\r\nContent-Type: text/plain; charset=utf-8";
  @mail($to, 'New project enquiry: ' . $line($type), $text, $headers);
}
json_out(['ok' => true]);
