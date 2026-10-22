<?php
// Optional for local use. On Render, set these as environment variables instead. Never commit config.php.
return [
  'database_url' => 'postgresql://USER:PASSWORD@HOST/DBNAME?sslmode=require',
  'app_secret'   => 'LONG_RANDOM_STRING_16+',
  'setup_key'    => 'LONG_RANDOM_STRING_16+',
  'notify_email' => '',
  'mail_from'    => 'no-reply@addigitaltech.com.ng',
];
