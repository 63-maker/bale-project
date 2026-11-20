# Deploying College Complaint Portal to Vercel

This application is fully configured for deployment on **Vercel** using the community PHP 8.3 serverless runtime (`vercel-php@0.7.4`).

---

## 1. Architecture on Vercel

- **Runtime:** `vercel-php@0.7.4` (PHP 8.3)
- **Entrypoint:** `api/index.php` (routes traffic to Laravel's front-controller and manages the writable `/tmp` directory)
- **Static Assets:** Built with Vite (`npm run build`) and served directly by Vercel's global CDN from `public/build/`
- **Reverse Proxy:** Trusted proxy support configured in `bootstrap/app.php` for SSL / HTTPS
- **Stateful Persistence:** Supabase PostgreSQL recommended for production; SQLite fallback auto-copied to `/tmp` for zero-config preview deployments

---

## 2. Environment Variables Checklist

Add these in **Vercel Dashboard > Project Settings > Environment Variables**:

| Variable | Description | Example / Recommended Value |
| :--- | :--- | :--- |
| `APP_NAME` | Portal Title | `"College Complaint Portal"` |
| `APP_ENV` | Application Environment | `production` |
| `APP_KEY` | Application Encryption Key | Output of `php artisan key:generate --show` |
| `APP_DEBUG` | Debug Mode | `false` |
| `APP_URL` | Production URL | `https://your-project.vercel.app` |
| `SESSION_DRIVER` | Session Storage | `cookie` (or `database`) |
| `CACHE_STORE` | Cache Backend | `array` (or `database`) |
| `LOG_CHANNEL` | Logging Output | `stderr` |
| `DB_CONNECTION` | Database Driver | `pgsql` |
| `DB_HOST` | Supabase DB Host | `aws-0-xx.pooler.supabase.com` |
| `DB_PORT` | Supabase DB Port | `5432` (or `6543`) |
| `DB_DATABASE` | Database Name | `postgres` |
| `DB_USERNAME` | Database User | `postgres.your-ref` |
| `DB_PASSWORD` | Database Password | `your-db-password` |
| `DB_SSLMODE` | SSL Mode | `require` |

> **Note:** Alternatively, you can set the single `DB_URL` environment variable provided in your Supabase connection strings:
> `postgresql://postgres:[PASSWORD]@[HOST]:5432/postgres?sslmode=require`

---

## 3. Deployment Methods

### Method A: Connect with GitHub / GitLab (Recommended)

1. Push your repository to GitHub:
   ```bash
   git push origin master
   ```
2. Navigate to [vercel.com](https://vercel.com) and click **"Add New Project"**.
3. Import your GitHub repository.
4. Keep the Framework Preset as **Other** (Vercel automatically detects `vercel.json`).
5. Open **Environment Variables** and add `APP_KEY`, `APP_URL`, and your database credentials.
6. Click **Deploy**.

---

### Method B: Deploy using Vercel CLI

1. Ensure Vercel CLI is installed:
   ```bash
   npm i -g vercel
   ```
2. Build assets locally:
   ```bash
   npm run build
   ```
3. Deploy to production:
   ```bash
   vercel --prod
   ```
4. Follow the interactive prompts to link your project.

---

## 4. Running Migrations & Seeders (Supabase)

Because Vercel functions are stateless and ephemeral, database migrations should be run against your Supabase instance from your local environment or GitHub Actions:

```bash
# Set your production database URL temporarily or in your local .env
php artisan migrate --force

# Optionally seed essential reference data (Admin, Departments, Categories)
php artisan db:seed --force
```

Default credentials:
- **Admin:** `admin@college.edu` / `Admin@123`
- **Management:** `management@college.edu` / `Management@123`
- **Student:** `student@college.edu` / `Student@123` (or register as a new student)
