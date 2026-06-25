# Railway Deployment Guide

Deployment guide for MyPengaduan (Laravel) on Railway, using the NIXPACKS builder with three
separate services (web, worker, scheduler), Cloudinary for file storage, and Firebase for push
notifications.

## Architecture Overview

Railway runs this app as three services from the same GitHub repository:

| Service   | Purpose                                  | Start command (from config)            |
|-----------|-------------------------------------------|-----------------------------------------|
| Web       | Serves the Laravel app over HTTP          | `Procfile` `web` entry                  |
| Worker    | Processes queued jobs (notifications etc) | `railway.worker.json` `startCommand`    |
| Scheduler | Runs Laravel's scheduled/cron tasks       | `railway.scheduler.json` `startCommand` |

All three services deploy from the same codebase and must share the same environment variables
(database, app key, Firebase, Cloudinary) so that queued jobs and scheduled tasks see the same
state as the web service.

## Prerequisites

- GitHub repository connected to Railway
- Railway account ([railway.app](https://railway.app))
- A Cloudinary account (free tier is fine) for photo/file storage
- A Firebase service account (for push notifications via FCM)
- A Postgres (or other) database — Railway can provision one, or use an external database

## Build & Deploy Configuration Files

These files live at the repo root and drive Railway's build/deploy behavior:

- **`Procfile`** — defines the `web` and `worker` process types:
  ```
  web: rm -f bootstrap/cache/*.php && php artisan storage:link && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=$PORT
  worker: php artisan queue:work --verbose --tries=3 --timeout=90 --sleep=3 --daemon
  ```
  The web command clears stale bootstrap cache, (re)creates the `public/storage` symlink, rebuilds
  config/route/view caches, then serves the app.

- **`railway.json`** — config for the **web** service:
  ```json
  {
    "build": {
      "builder": "NIXPACKS",
      "buildCommand": "npm install && npm run build && composer install --optimize-autoloader --no-dev --no-scripts"
    },
    "deploy": {
      "preDeployCommand": ["php artisan migrate --force"],
      "restartPolicyType": "ON_FAILURE",
      "restartPolicyMaxRetries": 10
    }
  }
  ```
  Note `--no-scripts` on the composer install — this skips `package:discover` during build (which
  would otherwise fail trying to autoload dev-only packages removed by `--no-dev`). Migrations run
  as a `preDeployCommand` rather than inline in the start command.

- **`railway.worker.json`** — config for the **worker** service:
  ```json
  {
    "build": { "builder": "NIXPACKS" },
    "deploy": {
      "startCommand": "php artisan queue:work --verbose --tries=3 --timeout=90 --sleep=3",
      "restartPolicyType": "ON_FAILURE",
      "restartPolicyMaxRetries": 10
    }
  }
  ```

- **`railway.scheduler.json`** — config for the **scheduler** service:
  ```json
  {
    "build": { "builder": "NIXPACKS" },
    "deploy": {
      "startCommand": "php artisan schedule:work",
      "restartPolicyType": "ON_FAILURE",
      "restartPolicyMaxRetries": 10
    }
  }
  ```

## Environment Variables

Start from `.env.example` at the repo root and adjust the following for production. Variables
not listed here can generally keep their `.env.example` defaults.

### Application
```env
APP_NAME="MyPengaduan"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app
```
`APP_URL` **must** be set correctly — it's used to build storage/file URLs and the storage route.

### Database
```env
DB_CONNECTION=pgsql
DB_HOST=...
DB_PORT=5432
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```
If using a Railway-provisioned database, reference Railway's variable references
(e.g. `${{Postgres.PGHOST}}`) instead of hardcoding values.

### Queue / Session / Cache
```env
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
```
`QUEUE_CONNECTION` must be `database` (not `sync`) for the worker service to have anything to
process.

### File Storage (Cloudinary)
```env
FILESYSTEM_DISK=cloudinary
CLOUDINARY_URL=cloudinary://<api_key>:<api_secret>@<cloud_name>
```
`CLOUDINARY_URL` is the single connection string from your Cloudinary dashboard
(Dashboard → Account Details → API Environment variable). This is **not currently listed** in
`.env.example` — add it there if you want new environments to be reminded to set it.

### Firebase (Push Notifications)
```env
FIREBASE_CREDENTIALS_BASE64=<base64-encoded service account JSON>
```
Railway doesn't support uploading files via its dashboard, so the Firebase service account JSON
is provided as a base64 string instead of a file path. To generate it:
```bash
base64 -w 0 path/to/firebase-service-account.json
```
`app/Services/FirebaseService.php` checks `FIREBASE_CREDENTIALS_BASE64` first; if unset, it falls
back to `FIREBASE_CREDENTIALS` (a file path under `storage/app/`) for local development.

### Variables that must match across all three services

Web, worker, and scheduler all need **identical** copies of: database credentials, `APP_KEY`,
`APP_URL`, `QUEUE_CONNECTION`, Cloudinary, and Firebase variables. The simplest way to keep them
in sync in Railway is to use **shared variables** at the project level rather than per-service
variables.

## Setting Up the Three Railway Services

### 1. Web Service
1. Railway dashboard → New Project → Deploy from GitHub repo.
2. Railway auto-detects the Laravel app and uses `railway.json` for build/deploy config.
3. Add a database (Railway Postgres, or connect an external one).
4. Set all environment variables listed above.
5. Deploy. The build runs `npm install && npm run build && composer install ...`; the
   `preDeployCommand` runs migrations; then the `Procfile` `web` command starts the server.

### 2. Worker Service
1. From the same Railway project → **+ New Service** → Deploy from the same GitHub repo.
2. In service settings, point it at `railway.worker.json` (or set its **Start Command** manually
   to match `railway.worker.json`'s `startCommand`).
3. Copy **all** environment variables from the Web service (or use shared/project-level variables).
4. Disable health checks for this service — it has no HTTP endpoint.
5. Restart policy: `ON_FAILURE` (already set in `railway.worker.json`).
6. Deploy and confirm logs show `Processing:` / `Processed:` lines as jobs run.

### 3. Scheduler Service
1. Same project → **+ New Service** → Deploy from the same GitHub repo.
2. Point it at `railway.scheduler.json` (start command: `php artisan schedule:work`).
3. Copy the same environment variables as Web/Worker.
4. Disable health checks (no HTTP endpoint).
5. This replaces a system cron entry — `schedule:work` runs continuously and triggers any
   `Schedule::` definitions in the app (e.g. the daily unverified-user cleanup command) at the
   right times.

## Photo / File Upload Handling (Cloudinary)

The app stores complaint/announcement photos and attachments via Cloudinary rather than local
disk, since Railway's filesystem is ephemeral across deploys.

- **Config**: `config/filesystems.php` defines a `cloudinary` disk that reads the single
  `CLOUDINARY_URL` env var. `FILESYSTEM_DISK=cloudinary` makes it the default disk.
- **Upload trait**: `app/Traits/HandlesCloudinaryUpload.php` provides `uploadToCloudinary()`,
  `uploadVideoToCloudinary()`, and `deleteFromCloudinary()` helpers. Controllers that handle
  uploads (e.g. `ComplaintController`) use this trait; it compresses/resizes images via
  Intervention Image before uploading, and falls back to local `Storage::disk('public')` if
  Cloudinary isn't configured (`isCloudinaryEnabled()` checks that `CLOUDINARY_URL` is set).
- **Stored value**: when Cloudinary is used, the full secure URL (not a relative path) is stored
  in the `photo` / `file_path` columns. Models' `*_url` accessors (e.g.
  `Complaint::getPhotoUrlAttribute()`) detect this with `filter_var($value, FILTER_VALIDATE_URL)`
  and return it as-is; otherwise they build a `storage/...` URL for local-disk fallback.
- **Local dev**: you can still run with `FILESYSTEM_DISK=local` or `public` and skip Cloudinary
  entirely — the fallback path in the controllers/trait handles that case.

See `CLOUDINARY_SETUP.md` (repo root) for the original account setup and credential steps if you
need to create a new Cloudinary account from scratch.

## Troubleshooting

### Queue / worker not processing jobs
- Confirm `QUEUE_CONNECTION=database` (not `sync`) on **both** web and worker services.
- Confirm the worker service is actually running `php artisan queue:work` — check its Railway logs
  for `Processing:` / `Processed:` lines.
- Check the `jobs` table for a backlog, and `failed_jobs` for failures:
  ```sql
  SELECT COUNT(*) FROM jobs;
  SELECT * FROM failed_jobs ORDER BY failed_at DESC LIMIT 10;
  ```
- Verify the worker has the same database credentials as the web service — a misconfigured DB
  connection on the worker will silently leave jobs unprocessed.
- Use the Railway CLI to inspect/retry: `railway run php artisan queue:failed` and
  `railway run php artisan queue:retry all`.

### Scheduled tasks not running
- Confirm the scheduler service is deployed and running `php artisan schedule:work` (check logs).
- Confirm it has the same environment variables as web/worker — scheduled commands run in the same
  app context and need DB/Firebase/Cloudinary access if the task uses them.

### Photos/files not uploading or not displaying
- Confirm `FILESYSTEM_DISK=cloudinary` and `CLOUDINARY_URL` are set correctly on the service
  handling the upload (web, since uploads happen on HTTP requests).
- Clear cached config after changing env vars: `php artisan config:clear` (or redeploy, since
  the web `Procfile` command rebuilds the config cache on every boot).
- Check the Cloudinary dashboard's Media Library to confirm the file actually arrived.
- If `photo`/`file_path` in the database is a relative path instead of a full URL, the upload fell
  back to local disk — check `isCloudinaryEnabled()` conditions (is `CLOUDINARY_URL` actually set
  and non-empty on that service?).
- If using local-disk fallback and files 404, confirm `public/storage` is a symlink to
  `storage/app/public` (`php artisan storage:link --force`) and that `APP_URL` is set correctly,
  since the `public` disk's URL is built from `APP_URL`.

### Build fails with class-not-found errors after `composer install --no-dev`
- This typically means a post-install script tried to autoload a dev-only package. Keep
  `--no-scripts` on the build's `composer install` step (already set in `railway.json`) so
  `package:discover` doesn't run until deploy time, after caches are rebuilt by the `Procfile`
  web command.

### Firebase notifications not sending
- Check worker logs for `Firebase initialized successfully` vs `Firebase not configured` /
  `Firebase initialization failed` (logged in `FirebaseService::__construct`).
- Confirm `FIREBASE_CREDENTIALS_BASE64` is set and is valid base64 of the full service account
  JSON (re-run `base64 -w 0 firebase-credentials.json` if unsure).
- Confirm device tokens exist and are active: `SELECT COUNT(*) FROM user_devices WHERE is_active = 1;`

## Useful Railway CLI Commands

```bash
npm i -g @railway/cli
railway login
railway link
railway logs --service <service-name> --follow
railway run php artisan queue:failed
railway run php artisan queue:retry all
railway run php artisan tinker
railway shell
```

## References

- [Railway Docs](https://docs.railway.app/)
- [Railway — Multiple Services](https://docs.railway.app/develop/services)
- [Laravel Queues](https://laravel.com/docs/queues)
- [Laravel Task Scheduling](https://laravel.com/docs/scheduling)
- [Cloudinary Laravel Package](https://github.com/cloudinary-labs/cloudinary-laravel)
