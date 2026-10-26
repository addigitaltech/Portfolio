# AD Digital Tech Agency: corporate website (Render + Neon)

PHP 8.3 + Apache in Docker, PostgreSQL on Neon. One Render Web Service.

## Environment variables (set in Render, never commit)
`DATABASE_URL`, `APP_SECRET` (16+ chars), `SETUP_KEY` (16+ chars, used once), optional `NOTIFY_EMAIL`, `MAIL_FROM`.

## Database
Run `sql/db_setup.sql` on Neon. It only adds tables and columns and never deletes data.

## First run
0. The first visit to the website loads starter content (services, projects, 10 blog posts) automatically.
1. Open `/api/setup_admin.php`, enter the setup key, create the admin (12+ character password). Starter content is added to empty sections.
2. Remove `SETUP_KEY` from Render, delete `api/setup_admin.php` from the repo, redeploy.
3. Sign in at `/admin/` and edit: Site settings, Services, Projects, Team, Process, Why choose us, Credentials and certificates (CAC, SMEDAN), Testimonials, FAQs, Navigation, Blog posts, and view Enquiries.
4. Already created the admin earlier? Open any section in `/admin/` and click "Load starter content".

## How content works
Everything public is stored in the database (`settings`, `entries`, `posts`, `images`) and loaded from `api/content.php`. Uploaded images are optimised (WebP, max 1800px wide) and stored in the database, so they survive redeploys. Project cards show saved screenshots; the live demo only opens in a new tab and is never embedded.

## Notes
Render has no mail server: enquiries are stored and shown in the admin. Pages are rendered in the browser, so search engines that do not run JavaScript see less. Free Render services sleep when idle.
