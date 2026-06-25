# data-desa

Sistem manajemen data desa berbasis Laravel dengan role-based access control.

## Setup commands
- Install deps: `composer install && npm install`
- Copy env: `cp .env.example .env` (jika belum ada)
- Generate key: `php artisan key:generate`
- Run migrations: `php artisan migrate`
- Start dev: `composer dev` (artisan serve + queue + pail + vite concurrent)
- Build assets: `npm run build`

## Code style
- PHP 8.3+ strict types
- Laravel conventions (Eloquent, Blade templates)
- PSR-12 coding standard
- Frontend: Vite + vanilla JS/Alpine (no React/Vue)
- Database: SQLite (dev), PostgreSQL-compatible (production via Supabase)

## Project structure
- **Routes:** `routes/web.php` - auth, dashboard, CRUD routes
- **Controllers:** CRUD untuk Desa, PerangkatWilayah, User, ActivityLog, Backup
- **Models:** Kecamatan, Wilayah, Desa, JabatanPerangkat, PerangkatWilayah, ActivityLog
- **Middleware:** `role` middleware untuk super_admin/operator access
- **Features:** 
  - CRUD desa & perangkat wilayah
  - Export Excel/PDF (dompdf, maatwebsite/excel)
  - Activity logging
  - Database backup/restore
  - Profile settings + password change

## Testing instructions
- Run tests: `composer test` (atau `php artisan test`)
- Config clear sebelum test (auto via composer script)
- Tambah tests untuk feature baru di `tests/Feature/`
- Check PHPUnit config di `phpunit.xml`

## Database
- Local: SQLite (`database/database.sqlite`)
- Production: PostgreSQL (Supabase-ready)
- Migrations di `database/migrations/`
- Seeders di `database/seeders/`
- Session/cache/queue: database driver

## Roles & Permissions
- **super_admin:** full access (users, activity logs, backups, delete operations)
- **operator:** CRUD desa/perangkat (no user management, no backups, no delete)
- **viewer:** read-only access (implemented via super_admin/operator checks)

## Deployment notes
- Assets: `npm run build` sebelum deploy
- Database: migrate fresh di production (`php artisan migrate --force`)
- Storage: foto perangkat di `storage/app/public` (symlink via `php artisan storage:link`)
- Env vars: update `APP_URL`, `DB_*`, `APP_KEY` untuk production
- Queue: setup supervisor/systemd untuk `php artisan queue:work`

## PR instructions
- Run `composer test` dan `npm run build` sebelum commit
- Check migrations bisa rollback (`php artisan migrate:rollback`)
- Test manually di browser untuk UI changes
- Commit format: `[feature/fix]: description` (lowercase, concise)
- Branch dari `main`, PR ke `main`
