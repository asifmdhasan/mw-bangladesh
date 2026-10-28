# MW Bangladesh

An original Laravel 11 lifestyle and digital magazine demo, built with Blade and Bootstrap 5. Articles, excerpts and editorial copy are original sample content.

## Requirements

- PHP 8.2 or newer with PDO MySQL enabled
- Composer
- MySQL 8 (or a compatible MySQL/MariaDB server)
- No Node.js or npm is required to run the site. Bootstrap 5.3.3, jQuery (admin only), Bootstrap Icons and Montserrat are loaded from CDNs. The project does not use Vue, Tailwind or Alpine.

## Local setup

1. Create a MySQL database named `mans_world_bd` (or update the `DB_*` values in `.env`).
2. Install PHP packages: `composer install`.
3. If starting from a fresh clone, copy `.env.example` to `.env`, then run `php artisan key:generate`.
4. Create tables and original demo stories with `php artisan migrate --seed`.
5. Start the app with `php artisan serve` and visit the printed local URL.

The sample editor account is `editor@mansworldbd.test` / `password`. Change this password before exposing the app outside a local demo. Subscriber accounts are created at `/register` and sign in at `/login`; the editorial sign-in is at `/admin/login`.

## Production uploads

The story editor can submit a story with up to 12 gallery images (10 MB each) and featured images. For Nginx/PHP-FPM deployments, apply the settings in `deploy/nginx/mw-upload-limits.conf` to the site's Nginx server block and load `deploy/php/mw-upload-limits.ini` in the PHP-FPM configuration. Then reload Nginx and restart PHP-FPM. Both limits are needed: Nginx's request limit must allow the full multipart request, and PHP's `post_max_size` must be larger than that request. Without the Nginx setting, a large story can fail with `413 Request Entity Too Large` before Laravel handles the upload.

## Included

- Editorial homepage, latest-story listing, category pages, story detail pages and search
- Seven-story Spotlight slider; Latest, Style, Entertainment and Magazine grids with full-size See All cards
- Separate `web` subscriber and `admin` session guards backed by a role field
- Laravel Breeze Blade authentication scaffolding for subscribers
- Bootstrap/jQuery CMS with dashboard metrics, searchable/filterable stories, create/edit/delete and draft/publish controls
- Category management, admin/subscriber account management, newsletter reader list and dynamic analytics/social settings
- Multi-image article uploads and in-form category creation
- Newsletter signup with name, phone and email fields
