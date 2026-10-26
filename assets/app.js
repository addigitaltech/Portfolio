(function () {
'use strict';
var D = document, B = D.body, PAGE = B.dataset.page, Q = new URLSearchParams(location.search);
var IMG = /^(api\/image\.php\?f=[a-f0-9]{32}\.(jpg|png|webp)|assets\/[A-Za-z0-9._-]+)$/;
function h(t, a, k) {
  var e = D.createElement(t);
  for (var n in (a || {})) { var v = a[n]; if (v == null || v === false) continue; if (n === 'class') e.className = v; else if (n === 'text') e.textContent = v; else e.setAttribute(n, v === true ? '' : v); }
  (k || []).forEach(function (c) { if (c == null || c === false) return; e.appendChild(typeof c === 'string' ? D.createTextNode(c) : c); });
  return e;
}
function safe(u) { return typeof u === 'string' && /^(https?:\/\/|\/|#|mailto:|tel:)/i.test(u) ? u : '#'; }
function img(src, alt, w, hh, eager) { return src && IMG.test(src) ? h('img', { src: src, alt: alt || '', width: w, height: hh, loading: eager ? null : 'lazy', decoding: 'async' }) : null; }
function enc(s) { return encodeURIComponent(s); }
function arr(x) { return Array.isArray(x) ? x : []; }
function paras(t) { return String(t || '').split(/\n{2,}/).filter(function (x) { return x.trim(); }).map(function (x) { return h('p', { text: x.trim() }); }); }
function chips(l) { l = arr(l); return l.length ? h('ul', { class: 'chips' }, l.map(function (x) { return h('li', { text: x }); })) : null; }
function sec(cls, kids) { return h('section', { class: 's ' + (cls || '') }, [h('div', { class: 'w' }, kids)]); }
function head(title, text, eye) { return h('div', { class: 'sh' }, [eye ? h('p', { class: 'eyebrow', text: eye }) : null, h('h2', { text: title }), text ? h('p', { class: 'lead', text: text }) : null]); }
function btn(txt, href, cls, ext) { return h('a', { class: 'btn ' + (cls || 'p'), href: safe(href), target: ext ? '_blank' : null, rel: ext ? 'noopener noreferrer' : null, text: txt }); }
var S = {}, C = {};

function load() {
  var c = null; try { c = JSON.parse(sessionStorage.getItem('adc') || 'null'); } catch (e) {}
  if (c && Date.now() - c.t < 15000) return Promise.resolve(c.d);
  return fetch('api/content.php').then(function (r) { if (!r.ok) throw 0; return r.json(); }).then(function (d) { try { sessionStorage.setItem('adc', JSON.stringify({ t: Date.now(), d: d })); } catch (e) {} return d; });
}
function meta(sel, attr, val) { var m = D.querySelector(sel); if (!m) { m = D.createElement(sel.indexOf('link') === 0 ? 'link' : 'meta'); var mm = sel.match(/\[(\w+)="?([^\]"]+)"?\]/); if (mm) m.setAttribute(mm[1], mm[2]); D.head.appendChild(m); } m.setAttribute(attr, val); }
function seo(title, desc, image) {
  var full = title ? title + ' | ' + S.company_name : S.seo_title;
  D.title = full;
  meta('meta[name="description"]', 'content', desc || S.seo_description);
  meta('meta[property="og:title"]', 'content', full);
  meta('meta[property="og:description"]', 'content', desc || S.seo_description);
  meta('meta[property="og:type"]', 'content', 'website');
  meta('meta[property="og:url"]', 'content', location.origin + location.pathname + (Q.get('s') ? '?s=' + enc(Q.get('s')) : ''));
  meta('link[rel="canonical"]', 'href', location.origin + location.pathname + (Q.get('s') ? '?s=' + enc(Q.get('s')) : ''));
  var im = image || S.og_image; if (im && IMG.test(im)) meta('meta[property="og:image"]', 'content', location.origin + '/' + im);
}
function logoSrc() { return S.logo || (S.theme === 'dark' ? 'assets/logo-dark.png' : 'assets/logo.png'); }

function chrome() {
  D.documentElement.dataset.theme = S.theme === 'dark' ? 'dark' : 'light';
  if (/^#[0-9a-f]{6}$/i.test(S.brand_color)) D.documentElement.style.setProperty('--blue', S.brand_color);
  meta('link[rel="icon"]', 'href', S.favicon && IMG.test(S.favicon) ? S.favicon : 'assets/favicon.png');
  var nav = arr(C.nav).length ? C.nav : [{ label: 'Home', url: '/' }, { label: 'Services', url: '/services' }, { label: 'Our Work', url: '/work' }, { label: 'Blog', url: '/blog' }, { label: 'Contact', url: '/contact' }];
  var path = location.pathname.replace(/\/$/, '').replace(/\.html$/, '') || '/';
  var ul = h('ul', {}, nav.map(function (n) { var u = safe(n.url), cur = u.replace(/\.html$/, '') === path; return h('li', {}, [h('a', { href: u, 'aria-current': cur ? 'page' : null, text: n.label })]); }));
  var navEl = h('nav', { id: 'nav', 'aria-label': 'Main' }, [ul]);
  var mb = h('button', { class: 'menu', type: 'button', 'aria-expanded': 'false', 'aria-controls': 'nav', text: 'Menu' });
  mb.onclick = function () { var o = navEl.classList.toggle('open'); mb.setAttribute('aria-expanded', o); };
  var hd = D.getElementById('hdr'); hd.textContent = '';
  hd.appendChild(h('div', { class: 'w' }, [h('a', { class: 'logo', href: '/', 'aria-label': S.company_name + ' home' }, [img(logoSrc(), S.company_name + ' logo', 126, 42, true)]), navEl, btn(S.cta_primary_text, S.cta_primary_url, 'p cta'), mb]));
  var oldwa = D.querySelector('.wa'); if (oldwa) oldwa.remove();
  if (S.whatsapp) {
    var wa = h('a', { class: 'wa', href: 'https://wa.me/' + S.whatsapp.replace(/\D/g, '') + '?text=' + enc(S.whatsapp_message || ''), target: '_blank', rel: 'noopener noreferrer', 'aria-label': 'Chat with us on WhatsApp' });
    wa.innerHTML = '<svg viewBox="0 0 32 32" width="28" height="28" aria-hidden="true"><path fill="#fff" d="M16 3C8.8 3 3 8.7 3 15.8c0 2.5.7 4.8 2 6.8L3.2 29l6.6-1.7c1.9 1 4 1.6 6.2 1.6 7.2 0 13-5.7 13-12.8S23.200 3 16 3zm0 23.300c-2 0-3.900-.6-5.600-1.600l-.4-.2-3.900 1 1-3.800-.3-.4A10.300 10.300 0 0 1 5.600 15.800C5.600 10.200 10.300 5.600 16 5.600s10.400 4.600 10.400 10.200S21.700 26.300 16 26.300z"/><path fill="#fff" d="M21.800 18.700c-.3-.2-1.800-.9-2.100-1-.3-.1-.5-.2-.7.200l-.9 1.100c-.2.200-.3.200-.6.100-1.800-.9-3-1.600-4.100-3.500-.3-.5.300-.5.900-1.500.1-.2 0-.4 0-.5l-1-2.300c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.400-.3.300-1.100 1.100-1.100 2.600s1.100 3 1.300 3.200c.2.200 2.200 3.400 5.400 4.700 2 .8 2.800.9 3.800.7.600-.1 1.800-.7 2.100-1.500.3-.7.300-1.300.2-1.500-.1-.1-.3-.2-.6-.3z"/></svg><span>Chat on WhatsApp</span>';
    D.body.appendChild(wa);
  }
  var soc = [['social_linkedin', 'LinkedIn'], ['social_facebook', 'Facebook'], ['social_instagram', 'Instagram'], ['social_x', 'X'], ['social_github', 'GitHub'], ['social_youtube', 'YouTube']].filter(function (s) { return S[s[0]]; });
  var ft = D.getElementById('ftr'); ft.textContent = ''; ft.className = 'site';
  ft.appendChild(h('div', { class: 'w' }, [
    h('div', { class: 'fg' }, [
      h('div', {}, [img(logoSrc(), S.company_name, 120, 46), h('p', { text: S.description })]),
      h('div', {}, [h('h3', { text: 'Explore' }), h('ul', {}, nav.map(function (n) { return h('li', {}, [h('a', { href: safe(n.url), text: n.label })]); }))]),
      h('div', {}, [h('h3', { text: 'Contact' }), h('ul', {}, [
        S.contact_email ? h('li', {}, [h('a', { href: 'mailto:' + S.contact_email, text: S.contact_email })]) : null,
        S.whatsapp ? h('li', {}, [h('a', { href: 'https://wa.me/' + S.whatsapp.replace(/\D/g, ''), target: '_blank', rel: 'noopener noreferrer', text: 'WhatsApp' })]) : null,
        S.phone ? h('li', { text: S.phone }) : null
      ].concat(soc.map(function (s) { return h('li', {}, [h('a', { href: S[s[0]], target: '_blank', rel: 'noopener noreferrer', text: s[1] })]); })))])
    ]),
    h('div', { class: 'bt', text: String(S.footer_text).replace('{year}', new Date().getFullYear()) })
  ]));
}

function badge(st) { var k = { 'Live': 'live', 'Completed': 'completed', 'In development': 'dev', 'Internal demonstration': 'demo', 'Temporarily unavailable': 'off' }[st] || ''; return st ? h('span', { class: 'badge ' + k, text: st }) : null; }
function pcard(p) {
  var shot = img(p.screenshot, p.screenshot_alt || p.title + ' screenshot', 800, 500) || h('div', { class: 'ph', text: 'Screenshot coming soon' });
  var url = '/project?s=' + enc(p.slug), demo = p.demo_enabled && p.demo_url, first = p.preview_mode === 'live-demo-first' && demo;
  var view = btn('View case study', url, first ? 'o' : 'p'), live = demo ? btn('Live demo', p.demo_url, first ? 'p' : 'o', true) : null;
  return h('article', { class: 'card pc' }, [
    h('a', { class: 'shot', href: url, 'aria-label': p.title + ' case study' }, [shot]),
    h('div', { class: 'cb' }, [h('div', {}, [badge(p.status), p.category ? h('span', { class: 'badge', text: p.category }) : null]), h('h3', { text: p.title }), h('p', { text: p.summary }), chips(arr(p.technologies).slice(0, 4)),
      h('div', { class: 'row' }, first ? [live, view] : [view, live]), p.screenshot ? h('p', { class: 'cap', text: 'Screenshot preview of the project' }) : null])]);
}
function scard(s) { return h('a', { class: 'card', href: '/service?s=' + enc(s.slug) }, [h('h3', { text: s.title }), h('p', { text: s.summary })]); }
function steps(list) { return h('ol', { class: 'steps' }, list.map(function (x) { return h('li', {}, [h('h3', { text: x.title }), x.description ? h('p', { text: x.description }) : null]); })); }
function tcard(m) { return h('article', { class: 'card tm' }, [img(m.photo, m.photo_alt || m.name, 400, 400) || h('div', { class: 'ph', text: m.name.charAt(0) }), h('h3', { text: m.name }), h('p', { text: m.role }), m.qualifications ? h('p', { text: m.qualifications, style: 'margin-top:6px;font-size:.85rem' }) : null, m.bio ? h('p', { text: m.bio, style: 'margin-top:8px' }) : null, h('div', { style: 'margin-top:10px' }, [chips(m.skills)]),
  h('p', { style: 'margin-top:10px' }, [['linkedin', 'LinkedIn'], ['github', 'GitHub'], ['website', 'Website']].filter(function (l) { return m[l[0]]; }).map(function (l) { return h('a', { href: safe(m[l[0]]), target: '_blank', rel: 'noopener noreferrer', text: l[1], style: 'margin-right:12px' }); }))]); }
function faqs() { var f = arr(C.faqs); return f.length ? sec('soft', [head('Frequently asked questions')].concat(f.map(function (x) { return h('details', { class: 'faq' }, [h('summary', { text: x.question }), h('p', { text: x.answer })]); }))) : null; }
function cta() { return h('section', { class: 'band' }, [h('div', { class: 'w' }, [h('h2', { text: S.contact_cta_title }), h('p', { text: S.contact_cta_text }), btn(S.cta_primary_text, S.cta_primary_url, 'p')])]); }
function banner(title, text) { return h('section', { class: 'hero', style: 'padding:56px 0' }, [h('div', { class: 'w' }, [h('h1', { text: title, style: 'font-size:clamp(1.9rem,4.6vw,2.9rem)' }), text ? h('p', { class: 'lead', text: text }) : null])]); }
function empty(t) { return h('p', { class: 'empty', text: t }); }

var P = {};
function ccard(c) { var im = img(c.image, c.image_alt || c.title, 600, 420); return h('article', { class: 'card cc' }, [im ? h('div', { class: 'cimg' }, [im]) : null, h('h3', { text: c.title }), c.issuer ? h('p', { text: 'Issued by ' + c.issuer }) : null, c.reference ? h('p', { text: 'Reference: ' + c.reference }) : null, c.description ? h('p', { text: c.description }) : null]); }
P.home = function () {
  var svc = arr(C.services), prj = arr(C.projects), cred = arr(C.credentials), feat = prj.filter(function (p) { return p.featured; }); if (!feat.length) feat = prj.slice(0, 3);
  var hero = h('section', { class: 'hero' }, [h('div', { class: 'w g' }, [
    h('div', {}, [h('p', { class: 'eyebrow lt', text: 'Software development company' }), h('h1', { text: S.hero_title }), h('p', { class: 'lead', text: S.hero_text }), h('div', { class: 'row' }, [btn(S.cta_primary_text, S.cta_primary_url, 'p'), btn(S.cta_secondary_text, S.cta_secondary_url, 'w2')])]),
    img(S.hero_image, S.hero_image_alt, 700, 520, true) || h('div', { class: 'panel' }, [h('h3', { text: 'What we build' }), h('ul', {}, (svc.length ? svc.slice(0, 6).map(function (x) { return x.title; }) : ['Websites', 'Web applications', 'School systems']).map(function (t) { return h('li', { text: t }); }))])])]);
  var out = [hero];
  if (arr(S.technologies).length) out.push(h('section', { class: 'strip' }, [h('div', { class: 'w' }, [h('p', { class: 'strip-l', text: 'Technologies we work with' }), chips(S.technologies)])]));
  if (cred.length) out.push(sec('', [head('Registered and certified', 'Our official registrations and certificates.', 'Credentials'), h('div', { class: 'grid' }, cred.map(ccard))]));
  out.push(sec('', [h('div', { class: 'two' }, [h('div', {}, [h('p', { class: 'eyebrow', text: 'Who we are' }), h('h2', { text: S.intro_title }), h('p', { class: 'lead', text: S.intro_text })]), arr(S.industries).length ? h('div', { class: 'card' }, [h('h3', { text: 'Who we serve' }), chips(S.industries)]) : null])]));
  if (svc.length) out.push(sec('soft', [head('Our services', 'Everything from the first design to deployment and support.', 'What we do'), h('div', { class: 'grid' }, svc.map(scard)), h('p', { style: 'margin-top:24px' }, [btn('All services', '/services', 'o')])]));
  if (feat.length) out.push(sec('', [head('Selected projects', 'Previews are saved screenshots, so they are always available.', 'Our work'), h('div', { class: 'grid w3' }, feat.map(pcard)), h('p', { style: 'margin-top:24px' }, [btn('See all projects', '/work', 'o')])]));
  if (arr(C.process).length) out.push(sec('soft', [head('How we work', 'A clear process from idea to a supported live system.', 'Our process'), steps(C.process)]));
  if (arr(C.why).length) out.push(sec('', [head('Why choose us', '', 'Why us'), h('div', { class: 'grid' }, C.why.map(function (x) { return h('div', { class: 'card' }, [h('h3', { text: x.title }), h('p', { text: x.text })]); }))]));
  if (arr(C.team).length) out.push(sec('soft', [head('Our team', '', 'The people'), h('div', { class: 'grid' }, C.team.slice(0, 4).map(tcard)), h('p', { style: 'margin-top:24px' }, [btn('Meet the team', '/team', 'o')])]));
  if (arr(C.testimonials).length) out.push(sec('', [head('What clients say', '', 'Testimonials'), h('div', { class: 'grid' }, C.testimonials.map(function (t) { return h('blockquote', { class: 'card quote', style: 'margin:0' }, [h('p', { text: '\u201C' + t.quote + '\u201D', style: 'color:inherit;font-size:1.02rem' }), h('footer', { text: '\u2014 ' + t.author + (t.role ? ', ' + t.role : '') })]); }))]));
  var bl = h('section', { class: 's soft', hidden: true }), blg = h('div', { class: 'ng' });
  bl.appendChild(h('div', { class: 'w' }, [head('Latest from our blog', 'Technology updates and practical engineering notes.', 'Blog'), blg, h('p', { style: 'margin-top:24px' }, [btn('Read the blog', '/blog', 'o')])]));
  fetch('api/posts.php?limit=3').then(function (r) { if (!r.ok) throw 0; return r.json(); }).then(function (j) { if (!j.posts || !j.posts.length || !window.ADNews) return; j.posts.forEach(function (p) { blg.appendChild(ADNews.card(p, false)); }); bl.hidden = false; }).catch(function () {});
  out.push(bl, cta()); return out;
};
P.about = function () {
  var cred = arr(C.credentials), out = [banner('About ' + S.company_name, S.tagline), sec('', [h('div', { class: 'two' }, [h('div', {}, [h('h2', { text: 'Company overview' })].concat(paras(S.about_overview))), h('div', {}, [h('div', { class: 'card', style: 'margin-bottom:16px' }, [h('h3', { text: 'Mission' }), h('p', { text: S.mission })]), h('div', { class: 'card' }, [h('h3', { text: 'Vision' }), h('p', { text: S.vision })])])])])];
  if (arr(S.values).length) out.push(sec('soft', [head('Our values', '', 'What guides us'), h('div', { class: 'grid' }, S.values.map(function (v) { var p = v.split('|'); return h('div', { class: 'card' }, [h('h3', { text: p[0].trim() }), p[1] ? h('p', { text: p.slice(1).join('|').trim() }) : null]); }))]));
  out.push(sec('', [head('Our approach', '', 'How we build'), h('div', { style: 'max-width:760px' }, paras(S.approach))]));
  var fp = img(S.founder_photo, S.founder_name + ', ' + S.founder_role, 480, 600);
  out.push(sec('soft', [h('div', { class: 'two' }, [fp ? h('div', {}, [fp]) : null, h('div', {}, [h('p', { class: 'eyebrow', text: 'Founder' }), h('h2', { text: S.founder_name }), h('p', { class: 'mut', text: S.founder_role }), h('p', { text: S.founder_bio })])])]));
  if (cred.length) out.push(sec('', [head('Credentials and certificates', 'Our official registrations.', 'Credentials'), h('div', { class: 'grid' }, cred.map(ccard))]));
  if (arr(S.history).length) out.push(sec('', [head('Milestones'), h('ul', { class: 'tl' }, S.history.map(function (x) { var p = x.split('|'); return h('li', {}, [h('strong', { text: p.length > 1 ? p[0].trim() : '' }), h('p', { text: (p.length > 1 ? p.slice(1).join('|') : x).trim() })]); }))]));
  if (arr(C.team).length) out.push(sec('soft', [head('The team', '', 'The people'), h('div', { class: 'grid' }, C.team.map(tcard))]));
  out.push(cta()); return out;
};
P.services = function () {
  var svc = arr(C.services), out = [banner('Services', 'Software development services for businesses, schools and organisations.')];
  out.push(svc.length ? sec('', [h('div', { class: 'grid w3' }, svc.map(function (s) { return h('div', { class: 'card' }, [h('h3', { text: s.title }), h('p', { text: s.summary }), h('p', { style: 'margin-top:12px' }, [h('a', { href: '/service?s=' + enc(s.slug), text: 'Details' })])]); }))]) : sec('', [empty('Services will appear here once they are published.')]));
  var f = faqs(); if (f) out.push(f); out.push(cta()); return out;
};
P.service = function () {
  var s = arr(C.services).filter(function (x) { return x.slug === Q.get('s'); })[0];
  if (!s) return [banner('Service not found'), sec('', [empty('This service could not be found.'), h('p', { style: 'text-align:center' }, [btn('All services', '/services', 'o')])])];
  seo(s.title, s.summary, s.image);
  var out = [banner(s.title, s.summary), sec('', [h('div', { class: 'two' }, [h('div', {}, paras(s.description).concat(arr(s.features).length ? [h('h3', { text: 'What is included' }), h('ul', { class: 'tick' }, s.features.map(function (x) { return h('li', { text: x }); }))] : [], arr(s.benefits).length ? [h('h3', { text: 'Benefits' }), h('ul', { class: 'tick' }, s.benefits.map(function (x) { return h('li', { text: x }); }))] : [], [btn(s.cta_text || 'Start a Project', '/contact?service=' + enc(s.title), 'p')])), img(s.image, s.image_alt || s.title, 640, 480) ? h('div', {}, [img(s.image, s.image_alt || s.title, 640, 480)]) : null])]),
    sec('soft', [head('Other services'), h('div', { class: 'grid' }, arr(C.services).filter(function (x) { return x.slug !== s.slug; }).map(scard))])];
  return out;
};
P.work = function () {
  var prj = arr(C.projects), cats = []; prj.forEach(function (p) { if (p.category && cats.indexOf(p.category) < 0) cats.push(p.category); });
  var grid = h('div', { class: 'grid w3' }, prj.map(pcard)), fl = h('div', { class: 'filters', role: 'group', 'aria-label': 'Filter projects' }, ['All'].concat(cats).map(function (c, i) { var b = h('button', { type: 'button', 'aria-pressed': i === 0 ? 'true' : 'false', text: c }); b.onclick = function () { fl.querySelectorAll('button').forEach(function (x) { x.setAttribute('aria-pressed', x === b); }); grid.textContent = ''; prj.filter(function (p) { return i === 0 || p.category === c; }).forEach(function (p) { grid.appendChild(pcard(p)); }); }; return b; }));
  return [banner('Our work', 'Each project shows its real status. Previews are saved screenshots, so they stay available even when a live demo is asleep.'), sec('', prj.length ? [cats.length > 1 ? fl : null, grid] : [empty('Projects will appear here once they are published.')]), cta()];
};
P.project = function () {
  var p = arr(C.projects).filter(function (x) { return x.slug === Q.get('s'); })[0];
  if (!p) return [banner('Project not found'), sec('', [empty('This project could not be found.'), h('p', { style: 'text-align:center' }, [btn('All projects', '/work', 'o')])])];
  seo(p.title, p.summary, p.screenshot);
  var shots = [p.screenshot].concat(arr(p.gallery)).filter(function (x) { return x && IMG.test(x); }), alt = p.screenshot_alt || p.title + ' screenshot';
  var main = h('div', { class: 'main-shot' }, [shots.length ? img(shots[0], alt, 1200, 750, true) : h('div', { class: 'ph', style: 'aspect-ratio:16/10', text: 'Screenshot coming soon' })]), th = null;
  if (shots.length > 1) th = h('div', { class: 'thumbs' }, shots.map(function (s, i) { var b = h('button', { type: 'button', 'aria-label': 'Show screenshot ' + (i + 1), 'aria-current': i === 0 ? 'true' : 'false' }, [img(s, '', 192, 120)]); b.onclick = function () { main.textContent = ''; main.appendChild(img(s, alt + ' ' + (i + 1), 1200, 750, true)); th.querySelectorAll('button').forEach(function (x) { x.setAttribute('aria-current', x === b); }); }; return b; }));
  var demo = p.demo_enabled && p.demo_url, side = [h('div', {}, [badge(p.status), p.category ? h('span', { class: 'badge', text: p.category }) : null]), h('h1', { text: p.title, style: 'font-size:clamp(1.7rem,4vw,2.4rem);margin-top:10px' }), h('p', { class: 'lead', text: p.summary })];
  if (p.client) side.push(h('p', { text: 'Client or industry: ' + p.client }));
  side.push(h('div', { class: 'row' }, [demo ? btn('Live demo', p.demo_url, 'p', true) : null, p.repo_url ? btn('Source code', p.repo_url, 'o', true) : null, btn('Start a similar project', '/contact', 'o')]));
  if (demo) side.push(h('p', { class: 'notice', style: 'margin-top:14px', text: 'The live demo opens in a new tab. If the service has been idle it can take up to a minute to start. This page and its screenshots stay available meanwhile.' }));
  var det = [];
  if (p.case_study) det.push(h('h2', { text: 'Case study' })), paras(p.case_study).forEach(function (x) { det.push(x); });
  if (arr(p.features).length) det.push(h('h3', { text: 'Features implemented' }), h('ul', { class: 'tick' }, p.features.map(function (x) { return h('li', { text: x }); })));
  if (arr(p.technologies).length) det.push(h('h3', { text: 'Technologies' }), chips(p.technologies));
  return [sec('', [h('p', { class: 'crumb' }, [h('a', { href: '/work', text: '\u2190 All projects' })]), h('div', { class: 'two' }, [h('div', {}, [main, th, shots.length ? h('p', { class: 'cap', style: 'font-size:.8rem;color:var(--mut)', text: 'Screenshots are previews of the project.' }) : null]), h('div', {}, side)])]), det.length ? sec('soft', [h('div', { style: 'max-width:780px' }, det)]) : null, cta()];
};
P.team = function () { var t = arr(C.team); return [banner('Our team', 'The people behind ' + S.company_name + '.'), sec('', t.length ? [h('div', { class: 'grid' }, t.map(tcard))] : [empty('Team members will appear here once they are published.')]), cta()]; };
P.process = function () { var p = arr(C.process); return [banner('Our process', 'How we take a project from idea to a supported live system.'), sec('', p.length ? [steps(p)] : [empty('Our process will appear here once it is published.')]), cta()]; };
P.contact = function () {
  var svcs = arr(C.services).map(function (s) { return s.title; }).concat(['Other']), f = h('form', { class: 'f', novalidate: true });
  function row(id, label, el, full) { return h('div', { class: full ? 'full' : '' }, [h('label', { for: id, text: label }), el, h('span', { class: 'err', 'data-for': id })]); }
  var sel = h('select', { id: 'type', name: 'type', required: true }, [h('option', { value: '', text: 'Choose a service' })].concat(svcs.map(function (s) { return h('option', { text: s }); })));
  if (Q.get('service')) sel.value = Q.get('service');
  var cm = h('select', { id: 'method', name: 'method' }, ['Email', 'WhatsApp', 'Phone call'].map(function (s) { return h('option', { text: s }); }));
  var note = h('div', { class: 'msg', role: 'status', 'aria-live': 'polite' }), sb = h('button', { class: 'btn p', type: 'submit', text: 'Send enquiry' });
  [row('name', 'Full name *', h('input', { id: 'name', name: 'name', autocomplete: 'name', required: true })), row('email', 'Email address *', h('input', { id: 'email', name: 'email', type: 'email', autocomplete: 'email', required: true })),
   row('phone', 'Phone or WhatsApp *', h('input', { id: 'phone', name: 'phone', type: 'tel', autocomplete: 'tel', required: true })), row('org', 'Organisation', h('input', { id: 'org', name: 'org', autocomplete: 'organization' })),
   row('type', 'Service *', sel), row('method', 'Preferred contact method', cm), row('budget', 'Budget range (optional)', h('input', { id: 'budget', name: 'budget' })), row('timeframe', 'Preferred timeframe (optional)', h('input', { id: 'timeframe', name: 'timeframe' })),
   row('message', 'Project description *', h('textarea', { id: 'message', name: 'message', rows: '5', required: true }), true),
   h('input', { class: 'hp', name: 'website', tabindex: '-1', autocomplete: 'off', 'aria-hidden': 'true' }),
   h('p', { class: 'full mut', style: 'font-size:.85rem;margin:0', text: 'We use these details only to respond to your enquiry and do not share them.' }), h('div', { class: 'full' }, [sb]), h('div', { class: 'full' }, [note])].forEach(function (x) { f.appendChild(x); });
  var t0 = Date.now(), busy = false;
  f.addEventListener('submit', function (e) {
    e.preventDefault(); if (busy) return; var v = function (k) { return f.elements[k].value.trim(); }, bad = 0;
    var r = { name: v('name').length > 1, email: /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v('email')), phone: v('phone').replace(/\D/g, '').length >= 7, type: !!v('type'), message: v('message').length >= 20 };
    var m = { name: 'Enter your full name.', email: 'Enter a valid email address.', phone: 'Enter a phone or WhatsApp number.', type: 'Choose a service.', message: 'Describe your project in at least 20 characters.' };
    Object.keys(r).forEach(function (k) { f.querySelector('[data-for=' + k + ']').textContent = r[k] ? '' : m[k]; f.elements[k].setAttribute('aria-invalid', !r[k]); if (!r[k]) bad++; });
    if (bad) return; busy = true; sb.disabled = true; sb.textContent = 'Sending...'; note.className = 'msg';
    fetch('api/contact.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ name: v('name'), email: v('email'), phone: v('phone'), org: v('org'), type: v('type'), method: v('method'), budget: v('budget'), timeframe: v('timeframe'), message: v('message'), website: f.elements.website.value, elapsed: Date.now() - t0 }) })
      .then(function (x) { return x.json().then(function (j) { if (!x.ok) throw new Error(j.error || 'Request failed'); return j; }); })
      .then(function () { note.className = 'msg show ok'; note.textContent = 'Thank you. Your enquiry has been received and we will reply soon.'; f.reset(); })
      .catch(function (x) { note.className = 'msg show'; note.textContent = (x && x.message && x.message !== 'Failed to fetch' ? x.message : 'We could not send your enquiry right now.') + ' Please try again or contact us directly.'; })
      .then(function () { busy = false; sb.disabled = false; sb.textContent = 'Send enquiry'; });
  });
  var info = h('div', {}, [h('h2', { text: 'Contact us' }), h('p', { class: 'lead', text: 'Tell us about your project and we will reply with the next steps.' }),
    S.contact_email ? h('p', {}, [h('strong', { text: 'Email: ' }), h('a', { href: 'mailto:' + S.contact_email, text: S.contact_email })]) : null,
    S.whatsapp ? h('p', {}, [btn('Chat on WhatsApp', 'https://wa.me/' + S.whatsapp.replace(/\D/g, ''), 'o', true)]) : null,
    S.phone ? h('p', {}, [h('strong', { text: 'Phone: ' }), S.phone]) : null, S.address ? h('p', {}, [h('strong', { text: 'Address: ' }), S.address]) : null]);
  var out = [banner('Contact us'), sec('', [h('div', { class: 'two' }, [info, f])])]; var q = faqs(); if (q) out.push(q); return out;
};

function boot() {
  var done = function () {
    try {
      chrome();
      var app = D.getElementById('app');
      if (PAGE !== 'blog' && P[PAGE]) {
        var pre = (PAGE === 'service' || PAGE === 'project') ? null : { home: [null, S.seo_description], about: ['About us', S.mission], services: ['Services', 'Software development services'], work: ['Our work', 'Projects and case studies'], team: ['Our team', ''], process: ['Our process', ''], contact: ['Contact us', 'Start a project with ' + S.company_name] }[PAGE];
        if (pre) seo(pre[0], pre[1]);
        app.textContent = ''; P[PAGE]().forEach(function (n) { if (n) app.appendChild(n); });
      } else if (PAGE === 'blog') seo('Blog', 'Technology updates and engineering notes from ' + S.company_name);
      D.dispatchEvent(new Event('adready'));
    } catch (e) { console.error(e); }
  };
  load().then(function (d) { C = d; S = d.settings || {}; done(); }).catch(function () {
    S = { company_name: 'AD Digital Tech Agency', theme: 'light', footer_text: '\u00A9 {year} AD Digital Tech Agency', cta_primary_text: 'Contact us', cta_primary_url: '/contact', description: '', seo_title: 'AD Digital Tech Agency', seo_description: '' };
    done(); if (PAGE !== 'blog') { var a = D.getElementById('app'); a.textContent = ''; a.appendChild(empty('Content is temporarily unavailable. Please try again shortly.')); }
  });
}
window.ADApp = { ready: function (cb) { D.addEventListener('adready', cb, { once: true }); } };
boot();
})();
