-- Mt. Elgon-ICLIP — database schema (MySQL 5.7+ / MariaDB 10.3+)
-- Usage:  mysql -u root -p < database/schema.sql
-- Then create a least-privilege user (see bottom of file).

CREATE DATABASE IF NOT EXISTS mtelgon_iclip
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mtelgon_iclip;

CREATE TABLE IF NOT EXISTS news (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title         VARCHAR(255)  NOT NULL,
  category      VARCHAR(80)   NOT NULL DEFAULT 'News',
  excerpt       VARCHAR(500)  NOT NULL DEFAULT '',
  image_url     VARCHAR(500)  NOT NULL DEFAULT '',
  url           VARCHAR(500)  NOT NULL DEFAULT '',
  is_published  TINYINT(1)    NOT NULL DEFAULT 1,
  published_at  DATE          NOT NULL,
  created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_news_published (is_published, published_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS inquiries (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(120)  NOT NULL,
  email       VARCHAR(190)  NOT NULL,
  organisation VARCHAR(160) NOT NULL DEFAULT '',
  interest    ENUM('volunteer','partner','donate','tree','csr','other') NOT NULL DEFAULT 'other',
  message     TEXT          NOT NULL,
  ip_hash     CHAR(64)      NOT NULL,
  status      ENUM('new','read','replied') NOT NULL DEFAULT 'new',
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_inquiries_ip_time (ip_hash, created_at),
  KEY idx_inquiries_status (status, created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS subscribers (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email       VARCHAR(190)  NOT NULL,
  ip_hash     CHAR(64)      NOT NULL,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_subscribers_email (email)
) ENGINE=InnoDB;

-- Seed: the two stories currently on the website.
INSERT INTO news (title, category, excerpt, image_url, url, published_at) VALUES
('H.E Dr. William Samoei Ruto, President of the Republic of Kenya, launches the Mt. Elgon-ICLIP Strategic Plan (2026–2036)',
 'Strategic plan',
 'The ten-year plan sets the direction for community-led conservation, water, livelihoods and indigenous knowledge on Mt. Elgon.',
 'https://mtelgon-iclip.org/wp-content/uploads/2026/11/Banner-2.png',
 'https://mtelgon-iclip.org/h-e-dr-william-samoei-ruto-president-of-the-republic-of-kenya-launches-the-mt-elgon-iclips-10-year-strategic-plan-2026-2036/',
 '2026-11-01'),
('Investing in forests is investing in people and the future of the Mt. Elgon ecosystem',
 'Forests',
 'Why restoring the mountain’s forests is also an investment in the households that depend on them.',
 'https://mtelgon-iclip.org/wp-content/uploads/2026/08/IMG-20260807-WA0017.jpg',
 'https://mtelgon-iclip.org/investing-in-forests-is-investing-in-people-and-future-of-mt-elgon-ecosystem/',
 '2026-08-07');

-- Least-privilege application user (change the password!):
-- CREATE USER 'mtelgon_user'@'localhost' IDENTIFIED BY 'a-long-random-password';
-- GRANT SELECT ON mtelgon_iclip.news TO 'mtelgon_user'@'localhost';
-- GRANT INSERT, SELECT ON mtelgon_iclip.inquiries TO 'mtelgon_user'@'localhost';
-- GRANT INSERT ON mtelgon_iclip.subscribers TO 'mtelgon_user'@'localhost';
-- FLUSH PRIVILEGES;
