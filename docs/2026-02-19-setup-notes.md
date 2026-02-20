# Attendance Checker - Setup Notes (2026-02-19)

## Summary
Today we set up the project to use **Laravel 12** with **Inertia + React** for the UI, and configured an **admin-only** login flow (no public user registration).

## Key changes made

### 1) Installed Breeze (Inertia + React)
We installed Laravel Breeze scaffolding using the Inertia + React preset.

What this provides:
- Login, logout, forgot/reset password pages (React)
- Inertia page structure under `resources/js/Pages`
- Auth routes under `routes/auth.php`

### 2) Admin-only access (registration disabled)
We removed the public registration routes so users cannot self-register.

File changed:
- `routes/auth.php`
  - Removed the `register` GET/POST routes.

Result:
- Only the seeded admin user can log in.

### 3) Admin user provisioning via Seeder
We added an **idempotent seeder** that creates the admin user **only if it does not already exist**.

Files added/changed:
- `database/seeders/AdminUserSeeder.php` (added)
- `database/seeders/DatabaseSeeder.php` (changed to call `AdminUserSeeder`)

How it works:
- Reads `ADMIN_EMAIL` and `ADMIN_PASSWORD` from environment variables
- If either is missing/empty, it does nothing
- Creates the user via `User::firstOrCreate(['email' => ...], [...])`
- Password is stored hashed because the `User` model casts `password` as `hashed`

Required `.env` entries (you add these locally):
```env
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=ChangeMe123!
```

Create the admin user:
```bash
php artisan config:clear
php artisan db:seed
```

### 4) Admin Login page text customization
We customized the existing Breeze login page to reflect **Admin Login**.

File changed:
- `resources/js/Pages/Auth/Login.jsx`
  - Title changed to `Admin Login`
  - Added a small header + subtitle
  - Button label changed to `Sign in`

### 5) Removed Register link from Welcome page
Since registration is disabled, we removed the `Register` link from the landing page.

File changed:
- `resources/js/Pages/Welcome.jsx`

## Notes about migrations (auth/system tables)
Even though your ERD focuses on domain tables (employees, attendance, payroll), Laravel still needs infrastructure tables for auth and platform features.

Existing default migrations include (important):
- `users`, `password_reset_tokens` (authentication)
- `sessions` (if using DB sessions)
- `cache` (if using DB cache)
- `jobs` (if using DB queues)

These should generally be kept for a deployable system.

## Common DB errors encountered and what they mean

### "relation \"sessions\" does not exist"
Cause:
- `SESSION_DRIVER=database` but the `sessions` table does not exist in the connected database.

Fix:
- Run migrations on the same database Laravel is using:
  ```bash
  php artisan migrate
  ```
  or switch sessions to file:
  ```env
  SESSION_DRIVER=file
  ```

### "relation \"cache\" does not exist"
Cause:
- Cache is set to `database` but `cache` table does not exist.

Fix:
- Ensure cache migration ran:
  ```bash
  php artisan migrate
  ```
  or switch cache to file:
  ```env
  CACHE_STORE=file
  ```

## How to run the project (dev)
1) Start Laravel:
```bash
php artisan serve
```

2) Start Vite:
```bash
npm run dev
```

3) Visit:
- `/login` for admin login
- `/dashboard` (requires auth)

## Next planned work (not done today)
- Create migrations/models for ERD domain tables:
  - employees, departments, work_schedules, holidays, schedule_overrides
  - attendance_logs, attendance_records
  - payroll_periods, payrolls, payroll_items
