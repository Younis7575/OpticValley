#!/usr/bin/env bash
# =============================================================================
# OpticValley — zero-downtime release activation (runs ON the server).
#
# Layout under $DEPLOY_PATH:
#   releases/<id>/        immutable release folders (built in CI)
#   shared/.env           production environment (never in git)
#   shared/storage/       logs, sessions, cache, private prescription uploads
#   shared/uploads/       product / banner images (public/uploads symlink)
#   current -> releases/<id>
#   backups/              database dumps taken before every migration
#
# The web root (e.g. ~/domains/opticvalley.co/public_html) is a symlink to
# current/public, created once by server-setup.sh.
#
# Usage: deploy.sh <deploy_path> <release_id>
# =============================================================================
set -Eeuo pipefail

DEPLOY_PATH="${1:?deploy path required}"
RELEASE_ID="${2:?release id required}"
RELEASE="$DEPLOY_PATH/releases/$RELEASE_ID"
SHARED="$DEPLOY_PATH/shared"
PHP="${PHP_BIN:-php}"
KEEP_RELEASES=5

log() { printf '\033[1;34m[deploy]\033[0m %s\n' "$*"; }
fail() { printf '\033[1;31m[deploy] ERROR:\033[0m %s\n' "$*" >&2; exit 1; }

[ -d "$RELEASE" ] || fail "Release folder $RELEASE not found"
[ -f "$SHARED/.env" ] || fail "$SHARED/.env missing — run deployment/server-setup.sh first"

PREVIOUS="$(readlink "$DEPLOY_PATH/current" 2>/dev/null || true)"

rollback() {
  if [ -n "$PREVIOUS" ] && [ -d "$PREVIOUS" ]; then
    log "Rolling back to $PREVIOUS"
    ln -sfn "$PREVIOUS" "$DEPLOY_PATH/current.tmp" && mv -Tf "$DEPLOY_PATH/current.tmp" "$DEPLOY_PATH/current"
    (cd "$PREVIOUS" && $PHP artisan up >/dev/null 2>&1 || true)
  fi
}
trap 'log "Deployment failed on line $LINENO"; rollback' ERR

log "PHP: $($PHP -r 'echo PHP_VERSION;')"
$PHP -r 'exit(version_compare(PHP_VERSION, "8.3.0", ">=") ? 0 : 1);' || fail "PHP 8.3+ required. Select PHP 8.3 in hPanel → Advanced → PHP Configuration."

log "Linking shared files"
mkdir -p "$SHARED/storage/app/private" "$SHARED/storage/app/public" "$SHARED/storage/framework/"{cache/data,sessions,views} "$SHARED/storage/logs" "$SHARED/uploads"
rm -rf "$RELEASE/storage" "$RELEASE/public/uploads"
ln -sfn "$SHARED/storage" "$RELEASE/storage"
ln -sfn "$SHARED/uploads" "$RELEASE/public/uploads"
ln -sfn "$SHARED/.env" "$RELEASE/.env"
chmod -R ug+rwX "$SHARED/storage" "$SHARED/uploads" "$RELEASE/bootstrap/cache"

cd "$RELEASE"

log "Backing up database before migrations"
mkdir -p "$DEPLOY_PATH/backups"
if $PHP artisan migrate:status --no-ansi >/dev/null 2>&1; then
  DB_DATABASE=$(grep -E '^DB_DATABASE=' "$SHARED/.env" | cut -d= -f2- | tr -d '"')
  DB_USERNAME=$(grep -E '^DB_USERNAME=' "$SHARED/.env" | cut -d= -f2- | tr -d '"')
  DB_PASSWORD=$(grep -E '^DB_PASSWORD=' "$SHARED/.env" | cut -d= -f2- | tr -d '"')
  DB_HOST=$(grep -E '^DB_HOST=' "$SHARED/.env" | cut -d= -f2- | tr -d '"')
  if command -v mysqldump >/dev/null 2>&1 && [ -n "$DB_DATABASE" ]; then
    MYSQL_PWD="$DB_PASSWORD" mysqldump --single-transaction --no-tablespaces -h "${DB_HOST:-127.0.0.1}" -u "$DB_USERNAME" "$DB_DATABASE" \
      | gzip > "$DEPLOY_PATH/backups/db-$(date +%Y%m%d-%H%M%S)-$RELEASE_ID.sql.gz"
    ls -1t "$DEPLOY_PATH/backups"/db-*.sql.gz 2>/dev/null | tail -n +11 | xargs -r rm -f
    log "Database backup saved"
  else
    log "mysqldump unavailable — skipping backup (enable it or rely on Hostinger daily backups)"
  fi
fi

log "Running migrations"
$PHP artisan migrate --force --no-interaction

# First deploy only: seed roles, settings, categories, admin user and starter catalogue.
if [ ! -f "$SHARED/.seeded" ]; then
  log "First deploy — seeding store data"
  $PHP artisan db:seed --force --no-interaction
  touch "$SHARED/.seeded"
fi

log "Optimising caches"
$PHP artisan optimize:clear >/dev/null
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan event:cache

log "Activating release $RELEASE_ID"
ln -sfn "$RELEASE" "$DEPLOY_PATH/current.tmp"
mv -Tf "$DEPLOY_PATH/current.tmp" "$DEPLOY_PATH/current"

# Clear PHP opcache if exposed via CLI (shared hosts reload on file change).
$PHP -r 'if (function_exists("opcache_reset")) opcache_reset();' || true

log "Pruning old releases (keeping $KEEP_RELEASES)"
ls -1dt "$DEPLOY_PATH/releases"/*/ | tail -n +$((KEEP_RELEASES + 1)) | xargs -r rm -rf

trap - ERR
log "Release $RELEASE_ID is live"
echo "$PREVIOUS" > "$DEPLOY_PATH/.previous_release"
