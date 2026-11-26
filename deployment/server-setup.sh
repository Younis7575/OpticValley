#!/usr/bin/env bash
# =============================================================================
# One-time server preparation for Hostinger shared hosting (run over SSH).
#
#   bash server-setup.sh ~/opticvalley ~/domains/opticvalley.co/public_html
#
# - Creates the release layout and shared folders
# - Creates shared/.env from the example (you then fill in DB + mail values)
# - BACKS UP the existing web root (e.g. old WordPress) to ~/backups and
#   replaces it with a symlink to the Laravel public folder
# =============================================================================
set -euo pipefail
DEPLOY_PATH="${1:?deploy path, e.g. ~/opticvalley}"
WEB_ROOT="${2:?web root, e.g. ~/domains/opticvalley.co/public_html}"

mkdir -p "$DEPLOY_PATH"/{releases,shared/uploads,shared/storage,backups}

if [ ! -f "$DEPLOY_PATH/shared/.env" ]; then
  cp "$(dirname "$0")/production.env.example" "$DEPLOY_PATH/shared/.env"
  KEY="base64:$(head -c 32 /dev/urandom | base64)"
  sed -i "s#^APP_KEY=.*#APP_KEY=$KEY#" "$DEPLOY_PATH/shared/.env"
  chmod 600 "$DEPLOY_PATH/shared/.env"
  echo "Created $DEPLOY_PATH/shared/.env — edit DB_* and MAIL_* before the first deploy."
fi

if [ -L "$WEB_ROOT" ]; then
  echo "Web root is already a symlink → $(readlink "$WEB_ROOT")"
else
  STAMP=$(date +%Y%m%d-%H%M%S)
  if [ -d "$WEB_ROOT" ] && [ "$(ls -A "$WEB_ROOT")" ]; then
    echo "Backing up existing site at $WEB_ROOT → $DEPLOY_PATH/backups/webroot-$STAMP.tar.gz"
    tar -czf "$DEPLOY_PATH/backups/webroot-$STAMP.tar.gz" -C "$(dirname "$WEB_ROOT")" "$(basename "$WEB_ROOT")"
    mv "$WEB_ROOT" "$DEPLOY_PATH/backups/webroot-$STAMP"
  else
    rm -rf "$WEB_ROOT"
  fi
  mkdir -p "$DEPLOY_PATH/current"  # placeholder until first deploy
  ln -sfn "$DEPLOY_PATH/current/public" "$WEB_ROOT"
  rmdir "$DEPLOY_PATH/current" 2>/dev/null || true
  echo "Web root now points to $DEPLOY_PATH/current/public"
fi
echo "Server is ready for the first GitHub Actions deploy."
