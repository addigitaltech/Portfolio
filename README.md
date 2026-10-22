# AD Digital Tech website (Render + Neon)

PHP 8.3 + Apache in Docker, PostgreSQL on Neon. Deployed as one Render Web Service.

## Environment variables (set in Render, never commit)
- `DATABASE_URL`: Neon connection string (`postgresql://...?sslmode=require`)
- `APP_SECRET`: random string, 16+ characters
- `SETUP_KEY`: random string, 16+ characters, used once by `api/setup_admin.php`
- `NOTIFY_EMAIL`, `MAIL_FROM`: optional (see note on email below)

## First run
1. Run `sql/db_setup.sql` on the Neon database.
2. Open `/api/setup_admin.php`, enter the setup key, create the admin (12+ character password).
3. Remove `SETUP_KEY` from Render, delete `api/setup_admin.php` from the repo, redeploy.
4. Sign in at `/admin/`. Publish a post to show the news section.
5. Set `CONFIG.whatsapp` in `index.html` to show the WhatsApp button.

## Notes
- Enquiries are stored in the database and shown in `/admin/`. Render has no mail server, so email alerts do not send. Use an SMTP service if you need them.
- Cover images are stored in the database (they survive redeploys).
- News: `news.html`, RSS at `api/feed.php`.
- Render free web services sleep when idle (about a minute to wake). Neon pauses compute when idle.
