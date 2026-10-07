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

## Included

- Editorial homepage, latest-story listing, category pages, story detail pages and search
- Seven-story Spotlight slider; Latest, Style, Entertainment and Magazine grids with full-size See All cards
- Separate `web` subscriber and `admin` session guards backed by a role field
- Laravel Breeze Blade authentication scaffolding for subscribers
- Bootstrap/jQuery CMS with dashboard metrics, searchable/filterable stories, create/edit/delete and draft/publish controls
- Category management, admin/subscriber account management, newsletter reader list and dynamic analytics/social settings
- Multi-image article uploads and in-form category creation
- Newsletter signup with name, phone and email fields
