-- AD Digital Tech: PostgreSQL schema (Neon). Safe to run more than once; never drops data.
CREATE TABLE IF NOT EXISTS admin_users (id SERIAL PRIMARY KEY, username VARCHAR(60) NOT NULL UNIQUE, password_hash VARCHAR(255) NOT NULL, created_at TIMESTAMPTZ NOT NULL DEFAULT NOW());
CREATE TABLE IF NOT EXISTS posts (id SERIAL PRIMARY KEY, slug VARCHAR(140) NOT NULL UNIQUE, title VARCHAR(160) NOT NULL, category VARCHAR(40) NOT NULL DEFAULT 'Update', summary VARCHAR(300) NOT NULL, body TEXT NOT NULL, link_url VARCHAR(255) NULL, cover_image VARCHAR(255) NULL, is_featured SMALLINT NOT NULL DEFAULT 0, is_published SMALLINT NOT NULL DEFAULT 0, published_at TIMESTAMPTZ NULL, created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(), updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW());
CREATE INDEX IF NOT EXISTS idx_posts_pub ON posts (is_published, published_at);
CREATE TABLE IF NOT EXISTS enquiries (id SERIAL PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(160) NOT NULL, phone VARCHAR(40) NOT NULL, organisation VARCHAR(120) NULL, project_type VARCHAR(60) NOT NULL, budget VARCHAR(80) NULL, timeframe VARCHAR(80) NULL, message TEXT NOT NULL, status VARCHAR(12) NOT NULL DEFAULT 'new' CHECK (status IN ('new','contacted','closed')), created_at TIMESTAMPTZ NOT NULL DEFAULT NOW());
ALTER TABLE enquiries ADD COLUMN IF NOT EXISTS contact_method VARCHAR(20) NULL;
CREATE INDEX IF NOT EXISTS idx_enq_status ON enquiries (status, created_at);
CREATE TABLE IF NOT EXISTS rate_limits (id SERIAL PRIMARY KEY, bucket VARCHAR(40) NOT NULL, ip_hash CHAR(64) NOT NULL, created_at TIMESTAMPTZ NOT NULL DEFAULT NOW());
CREATE INDEX IF NOT EXISTS idx_rl ON rate_limits (bucket, ip_hash, created_at);
CREATE TABLE IF NOT EXISTS images (name VARCHAR(40) PRIMARY KEY, mime VARCHAR(20) NOT NULL, data BYTEA NOT NULL, created_at TIMESTAMPTZ NOT NULL DEFAULT NOW());
CREATE TABLE IF NOT EXISTS settings (k VARCHAR(60) PRIMARY KEY, v TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS entries (id SERIAL PRIMARY KEY, collection VARCHAR(30) NOT NULL, slug VARCHAR(140) NULL, data TEXT NOT NULL, sort_order INT NOT NULL DEFAULT 0, is_published SMALLINT NOT NULL DEFAULT 1, created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(), updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW());
CREATE INDEX IF NOT EXISTS idx_entries_c ON entries (collection, sort_order);
CREATE UNIQUE INDEX IF NOT EXISTS idx_entries_slug ON entries (collection, slug);
