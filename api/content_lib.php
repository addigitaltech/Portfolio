<?php
// Content model: schema for settings and collections, validation, defaults, seeding.
declare(strict_types=1);

function F(string $k, string $l, string $t = 'text', array $o = []): array { return ['k' => $k, 'l' => $l, 't' => $t] + $o; }

function content_schema(): array {
  $status = ['Live', 'Completed', 'In development', 'Internal demonstration', 'Temporarily unavailable'];
  $cats = ['Education', 'School systems', 'Business', 'Platform', 'Demonstration'];
  return [
    'settings' => [
      'Company' => [
        F('company_name', 'Company name', 'text', ['max' => 80, 'def' => 'AD Digital Tech Agency']),
        F('tagline', 'Tagline', 'text', ['def' => 'Engineering Digital Solutions. Building What Matters.']),
        F('description', 'Short company description (footer and search results)', 'textarea', ['max' => 300, 'def' => 'A software development company building websites, web applications and digital systems for businesses, schools and organisations.']),
        F('logo', 'Logo (PNG with transparent background works best)', 'image'),
        F('favicon', 'Favicon (square image)', 'image'),
        F('brand_color', 'Brand accent colour (hex, for example #1e6bff)', 'color', ['def' => '#1e6bff']),
        F('theme', 'Website theme', 'select', ['options' => ['light', 'dark'], 'def' => 'light']),
      ],
      'Homepage' => [
        F('hero_title', 'Hero headline', 'text', ['max' => 140, 'def' => 'Engineering Digital Solutions. Building What Matters.']),
        F('hero_text', 'Hero text', 'textarea', ['max' => 400, 'def' => 'We design, build, and deploy modern web applications and software systems that help businesses and institutions operate smarter, solve complex problems, and grow with confidence.']),
        F('hero_image', 'Hero image (optional)', 'image'),
        F('hero_image_alt', 'Hero image description (for accessibility)', 'text'),
        F('cta_primary_text', 'Main button text', 'text', ['def' => 'Start a Project']),
        F('cta_primary_url', 'Main button link', 'link', ['def' => '/contact']),
        F('cta_secondary_text', 'Second button text', 'text', ['def' => 'Explore Our Work']),
        F('cta_secondary_url', 'Second button link', 'link', ['def' => '/work']),
        F('intro_title', 'Company introduction heading', 'text', ['def' => 'A software development company']),
        F('intro_text', 'Company introduction text', 'textarea', ['def' => 'AD Digital Tech Agency builds full-stack websites, web applications and digital systems. We work with schools, businesses and organisations that need dependable software, from the first design to deployment and support.']),
        F('industries', 'Industries we serve (one per line)', 'lines', ['def' => ['Schools and education', 'Businesses and corporate organisations', 'Startups', 'NGOs and non-profit organisations']]),
        F('contact_cta_title', 'Closing call-to-action heading', 'text', ['def' => 'Have a project in mind?']),
        F('contact_cta_text', 'Closing call-to-action text', 'textarea', ['def' => 'Tell us what you need and we will respond with the next steps.']),
      ],
      'About' => [
        F('about_overview', 'Company overview', 'textarea', ['max' => 3000, 'def' => 'AD Digital Tech Agency is a full-stack software development company. We build business and school websites, result portals, custom web applications and payment-enabled systems, and we support them after launch.']),
        F('mission', 'Mission', 'textarea', ['def' => 'To build reliable software that helps schools, businesses and organisations work more efficiently.']),
        F('vision', 'Vision', 'textarea', ['def' => 'To be a trusted software development partner for institutions and businesses.']),
        F('values', 'Company values (one per line, optional "Title | description")', 'lines', ['def' => ['Honesty | We describe our work and its status accurately.', 'Reliability | We build systems that are dependable in daily use.', 'Practicality | We solve real operational problems.', 'Quality | We care about the details.']]),
        F('approach', 'Our approach to software development', 'textarea', ['max' => 3000, 'def' => 'We start by understanding the problem, plan the system and its data, design the interface, build the frontend and backend together, test, deploy and then support the result.']),
        F('history', 'Company history or milestones (one per line, "Year | milestone")', 'lines'),
        F('founder_name', 'Founder name', 'text', ['def' => 'Anjorin Daniel Olorunfemi']),
        F('founder_role', 'Founder title', 'text', ['def' => 'Founder and Lead Developer']),
        F('founder_bio', 'Founder biography', 'textarea', ['def' => 'Anjorin Daniel Olorunfemi is a software developer and full-stack engineer with about five years of software development experience and a Diploma in Computer Science. He leads the engineering work at AD Digital Tech Agency.']),
        F('founder_photo', 'Founder photograph', 'image', ['def' => 'assets/founder.jpg']),
      ],
      'Contact and social' => [
        F('contact_email', 'Business email', 'text', ['def' => 'info@addigitaltech.com.ng']),
        F('whatsapp', 'WhatsApp number (digits with country code, e.g. 234...)', 'text', ['max' => 20]),
        F('phone', 'Phone number (optional)', 'text', ['max' => 30]),
        F('address', 'Office address (optional)', 'textarea', ['max' => 300]),
        F('social_linkedin', 'LinkedIn link', 'url'),
        F('social_facebook', 'Facebook link', 'url'),
        F('social_instagram', 'Instagram link', 'url'),
        F('social_x', 'X (Twitter) link', 'url'),
        F('social_github', 'GitHub link', 'url'),
        F('social_youtube', 'YouTube link', 'url'),
      ],
      'Footer and search engines' => [
        F('footer_text', 'Footer text (use {year} for the current year)', 'text', ['def' => '© {year} AD Digital Tech Agency. All rights reserved.']),
        F('seo_title', 'Homepage title for search engines', 'text', ['def' => 'AD Digital Tech Agency | Software Development Company']),
        F('seo_description', 'Homepage description for search engines', 'textarea', ['max' => 300, 'def' => 'AD Digital Tech Agency is a software development company building business websites, school websites, result portals and custom web applications.']),
        F('og_image', 'Social sharing image', 'image'),
      ],
    ],
    'collections' => [
      'services' => ['label' => 'Services', 'one' => 'service', 'title' => 'title', 'slug' => true, 'fields' => [
        F('title', 'Service name', 'text', ['req' => 1, 'max' => 120]),
        F('summary', 'Short description (shown on cards)', 'textarea', ['max' => 300, 'req' => 1]),
        F('description', 'Full description', 'textarea', ['max' => 4000]),
        F('features', 'Features (one per line)', 'lines'),
        F('benefits', 'Benefits to clients (one per line)', 'lines'),
        F('image', 'Service image', 'image'),
        F('image_alt', 'Image description (for accessibility)', 'text'),
        F('cta_text', 'Button text', 'text'),
      ]],
      'projects' => ['label' => 'Projects', 'one' => 'project', 'title' => 'title', 'slug' => true, 'fields' => [
        F('title', 'Project name', 'text', ['req' => 1, 'max' => 140]),
        F('summary', 'Short description', 'textarea', ['max' => 300, 'req' => 1]),
        F('category', 'Category', 'select', ['options' => $cats]),
        F('status', 'Project status', 'select', ['options' => $status, 'req' => 1]),
        F('client', 'Client or industry (only if approved for publication)', 'text'),
        F('technologies', 'Technologies (one per line)', 'lines'),
        F('features', 'Features implemented (one per line)', 'lines'),
        F('case_study', 'Case study (problem, solution, results; blank line between paragraphs)', 'textarea', ['max' => 8000]),
        F('screenshot', 'Primary screenshot (used on the card)', 'image'),
        F('screenshot_alt', 'Screenshot description (for accessibility)', 'text'),
        F('gallery', 'More screenshots', 'images'),
        F('preview_mode', 'Presentation', 'select', ['options' => ['screenshot-first', 'live-demo-first']]),
        F('demo_enabled', 'Show the Live Demo button', 'bool'),
        F('demo_url', 'Live demo link', 'url'),
        F('repo_url', 'Source code link (only if safe to publish)', 'url'),
        F('featured', 'Feature on the homepage', 'bool'),
      ]],
      'team' => ['label' => 'Team', 'one' => 'team member', 'title' => 'name', 'slug' => false, 'fields' => [
        F('name', 'Name', 'text', ['req' => 1, 'max' => 100]),
        F('role', 'Role', 'text', ['req' => 1]),
        F('photo', 'Photograph', 'image'),
        F('photo_alt', 'Photo description (for accessibility)', 'text'),
        F('bio', 'Short biography', 'textarea', ['max' => 800]),
        F('skills', 'Skills (one per line)', 'lines'),
        F('linkedin', 'LinkedIn link', 'url'),
        F('github', 'GitHub link', 'url'),
        F('website', 'Website link', 'url'),
      ]],
      'process' => ['label' => 'Process stages', 'one' => 'stage', 'title' => 'title', 'slug' => false, 'fields' => [
        F('title', 'Stage name', 'text', ['req' => 1]),
        F('description', 'Description', 'textarea', ['max' => 800]),
      ]],
      'why' => ['label' => 'Why choose us', 'one' => 'reason', 'title' => 'title', 'slug' => false, 'fields' => [
        F('title', 'Heading', 'text', ['req' => 1]),
        F('text', 'Text', 'textarea', ['max' => 500]),
      ]],
      'testimonials' => ['label' => 'Testimonials', 'one' => 'testimonial', 'title' => 'author', 'slug' => false, 'fields' => [
        F('quote', 'Quote (only genuine, approved testimonials)', 'textarea', ['req' => 1, 'max' => 800]),
        F('author', 'Name', 'text', ['req' => 1]),
        F('role', 'Role or organisation', 'text'),
      ]],
      'faqs' => ['label' => 'FAQs', 'one' => 'question', 'title' => 'question', 'slug' => false, 'fields' => [
        F('question', 'Question', 'text', ['req' => 1, 'max' => 200]),
        F('answer', 'Answer', 'textarea', ['req' => 1, 'max' => 1500]),
      ]],
      'nav' => ['label' => 'Navigation', 'one' => 'menu link', 'title' => 'label', 'slug' => false, 'fields' => [
        F('label', 'Link text', 'text', ['req' => 1, 'max' => 40]),
        F('url', 'Link address (e.g. /about or https://...)', 'link', ['req' => 1]),
      ]],
    ],
  ];
}

function img_ok(string $p): bool {
  return $p === '' || preg_match('#^(api/image\.php\?f=[a-f0-9]{32}\.(jpg|png|webp)|assets/[A-Za-z0-9._-]+)$#', $p) === 1;
}

function clean_field(array $f, mixed $v): mixed {
  $t = $f['t'];
  $max = $f['max'] ?? ($t === 'textarea' ? 4000 : 300);
  switch ($t) {
    case 'bool': return !empty($v) ? 1 : 0;
    case 'lines':
      $a = is_array($v) ? $v : (preg_split('/\R/u', (string)$v) ?: []);
      $out = [];
      foreach ($a as $x) { $x = clean($x, 300); if ($x !== '') $out[] = $x; if (count($out) >= 40) break; }
      return $out;
    case 'image':
      $p = clean($v, 255);
      if (!img_ok($p)) throw new InvalidArgumentException($f['l'] . ': invalid image.');
      return $p;
    case 'images':
      $out = [];
      foreach ((array)$v as $x) { $p = clean($x, 255); if ($p !== '' && img_ok($p)) $out[] = $p; if (count($out) >= 12) break; }
      return $out;
    case 'url':
      $u = clean($v, 255);
      if ($u !== '' && !preg_match('#^https?://#i', $u)) throw new InvalidArgumentException($f['l'] . ' must start with http:// or https://');
      return $u;
    case 'link':
      $u = clean($v, 255);
      if ($u !== '' && !preg_match('#^(https?://|/|\#|mailto:)#i', $u)) throw new InvalidArgumentException($f['l'] . ' must start with /, #, http:// or https://');
      return $u;
    case 'color':
      $c = clean($v, 7);
      if ($c !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', $c)) throw new InvalidArgumentException($f['l'] . ' must look like #1e6bff');
      return $c;
    case 'select':
      $s = clean($v, 60);
      if ($s !== '' && !in_array($s, $f['options'], true)) throw new InvalidArgumentException($f['l'] . ': invalid choice.');
      return $s;
    default: return clean($v, $max);
  }
}

function validate_fields(array $fields, array $in): array {
  $out = [];
  foreach ($fields as $f) {
    $k = $f['k'];
    $v = $in[$k] ?? (in_array($f['t'], ['lines', 'images'], true) ? [] : '');
    $out[$k] = clean_field($f, $v);
    if (!empty($f['req']) && ($out[$k] === '' || $out[$k] === [])) throw new InvalidArgumentException($f['l'] . ' is required.');
  }
  return $out;
}

function slugify(string $s): string {
  $s = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $s) ?? '', '-'));
  return $s !== '' ? mb_substr($s, 0, 120) : 'item';
}

function unique_slug(string $c, string $title): string {
  $slug = slugify($title);
  $st = db()->prepare('SELECT COUNT(*) FROM entries WHERE collection=? AND slug=?');
  $st->execute([$c, $slug]);
  if ((int)$st->fetchColumn() > 0) $slug .= '-' . substr(bin2hex(random_bytes(3)), 0, 5);
  return $slug;
}

function insert_entry(string $c, array $data, int $pub, ?int $sort = null): int {
  $def = content_schema()['collections'][$c];
  $slug = !empty($def['slug']) ? unique_slug($c, (string)($data[$def['title']] ?? 'item')) : null;
  if ($sort === null) {
    $m = db()->prepare('SELECT COALESCE(MAX(sort_order), 0) FROM entries WHERE collection=?');
    $m->execute([$c]);
    $sort = (int)$m->fetchColumn() + 10;
  }
  db()->prepare('INSERT INTO entries (collection, slug, data, sort_order, is_published) VALUES (?,?,?,?,?)')
    ->execute([$c, $slug, json_encode($data, JSON_UNESCAPED_UNICODE), $sort, $pub]);
  return (int)db()->lastInsertId('entries_id_seq');
}

function settings_all(): array {
  $def = []; $types = [];
  foreach (content_schema()['settings'] as $fields) foreach ($fields as $f) {
    $def[$f['k']] = $f['def'] ?? ($f['t'] === 'lines' ? [] : '');
    $types[$f['k']] = $f['t'];
  }
  foreach (db()->query('SELECT k, v FROM settings')->fetchAll() as $r) {
    if (!isset($types[$r['k']])) continue;
    $v = $r['v'];
    if ($types[$r['k']] === 'lines') { $a = json_decode((string)$v, true); if (!is_array($a) || !$a) continue; $v = $a; }
    elseif ($v === '' || $v === null) continue;
    $def[$r['k']] = $v;
  }
  return $def;
}

/** Fills empty collections with the starter content (idempotent). Returns the collections filled. */
function seed_if_empty(): array {
  $seed = require __DIR__ . '/seed_data.php';
  $filled = [];
  foreach ($seed as $c => $items) {
    $n = db()->prepare('SELECT COUNT(*) FROM entries WHERE collection=?');
    $n->execute([$c]);
    if ((int)$n->fetchColumn() > 0) continue;
    $def = content_schema()['collections'][$c];
    foreach ($items as $i => $item) insert_entry($c, validate_fields($def['fields'], $item), 1, ($i + 1) * 10);
    $filled[] = $c;
  }
  return $filled;
}
