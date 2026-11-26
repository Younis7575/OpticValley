# Production deployment (Hostinger shared hosting)

Every push to `main` runs tests, builds assets, and — when the repository secrets below exist — deploys to the server with zero downtime and an automatic health check / rollback.

```
git push → GitHub Actions → composer + npm build → tests → bundle
        → scp to server → deploy.sh (backup DB → migrate → cache → switch symlink)
        → health check (/up and /) → rollback automatically on failure
```

## 1. One-time server preparation

1. **hPanel → Advanced → PHP Configuration**: select **PHP 8.3** (or newer).
2. **hPanel → Databases → MySQL Databases**: create a database and user; note the name, user and password.
3. **hPanel → Advanced → SSH Access**: enable SSH. Note the host IP, port (usually `65002`) and username.
4. Create a deploy key on your computer (no passphrase):
   ```bash
   ssh-keygen -t ed25519 -C "github-deploy-opticvalley" -f ~/.ssh/opticvalley_deploy -N ""
   ```
   Add the **public** key (`~/.ssh/opticvalley_deploy.pub`) in hPanel → SSH Access → SSH keys.
5. Copy the `deployment/` folder to the server and run the setup script (this **backs up the current website** in `public_html` — e.g. the old WordPress — to `~/opticvalley/backups/` and points `public_html` to Laravel):
   ```bash
   scp -P 65002 -r deployment USER@HOST:~/deployment
   ssh -p 65002 USER@HOST
   bash ~/deployment/server-setup.sh ~/opticvalley ~/domains/opticvalley.co/public_html
   nano ~/opticvalley/shared/.env      # fill DB_* (and MAIL_* when ready)
   ```
   Take a WordPress database export from hPanel → Databases → phpMyAdmin first if you want to keep it.

## 2. GitHub repository secrets

GitHub → repository → Settings → Secrets and variables → Actions → **New repository secret**:

| Secret | Example |
|---|---|
| `SERVER_HOST` | `153.92.x.x` (from hPanel SSH Access) |
| `SERVER_USER` | `u123456789` |
| `SERVER_PORT` | `65002` |
| `SERVER_SSH_KEY` | contents of `~/.ssh/opticvalley_deploy` (private key) |
| `DEPLOY_PATH` | `/home/u123456789/opticvalley` (absolute path) |

Optional repository **variable** `APP_URL` (default `https://opticvalley.co`) for the health check.

Then re-run the latest workflow (Actions → CI / Deploy → Re-run) or push to `main`.

## 3. First deploy

The first deploy migrates the database and seeds roles, settings, categories, lens options, pages, menus, the starter catalogue and the admin user. If `ADMIN_PASSWORD` is empty in `shared/.env`, a password is generated and saved to `shared/storage/app/private/admin-credentials.txt` on the server — sign in at `/admin`, change it under *My profile*, then delete that file.

Set `SEED_DEMO_CATALOG=false` in `shared/.env` **before the first deploy** to start with an empty catalogue.

## 4. Operations

- **Rollback**: `ssh … "bash ~/opticvalley/bin/rollback.sh ~/opticvalley"` (switches to the previous release instantly).
- **Database backups**: taken automatically before each migration into `~/opticvalley/backups/` (last 10 kept). Hostinger also keeps daily backups.
- **Uploads & storage** live in `~/opticvalley/shared/` and are never touched by deploys.
- **Logs**: `~/opticvalley/shared/storage/logs/laravel-*.log`.
- **SSL**: hPanel → Security → SSL → install the free certificate for `opticvalley.co` and `www`.
- **E-mail**: create `no-reply@opticvalley.co` in hPanel → Emails, then set `MAIL_MAILER=smtp`, `MAIL_USERNAME`, `MAIL_PASSWORD` in `shared/.env` and redeploy.
- **Scheduler**: not required currently. If added later, create an hPanel cron: `php ~/opticvalley/current/artisan schedule:run`.

## Environment variables

See `deployment/production.env.example`. Never commit `.env` files.
