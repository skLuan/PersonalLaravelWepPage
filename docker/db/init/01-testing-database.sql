-- Runs once, the first time the `db` volume is created.
-- phpunit.xml sets DB_DATABASE=testing, so the test suite needs its own database
-- (it is wiped by RefreshDatabase and must never be the dev database).
CREATE DATABASE IF NOT EXISTS `testing` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON `testing`.* TO 'luane'@'%';
FLUSH PRIVILEGES;
