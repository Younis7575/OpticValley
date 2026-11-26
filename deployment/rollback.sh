#!/usr/bin/env bash
# Instantly switch back to the previous release. Usage: rollback.sh <deploy_path> [release_id]
set -euo pipefail
DEPLOY_PATH="${1:?deploy path required}"
TARGET="${2:-}"
if [ -n "$TARGET" ]; then TARGET="$DEPLOY_PATH/releases/$TARGET"; else
  CURRENT="$(readlink "$DEPLOY_PATH/current")"
  TARGET="$(ls -1dt "$DEPLOY_PATH/releases"/*/ | sed 's#/$##' | grep -v "^$CURRENT$" | head -1)"
fi
[ -d "$TARGET" ] || { echo "No release to roll back to"; exit 1; }
ln -sfn "$TARGET" "$DEPLOY_PATH/current.tmp" && mv -Tf "$DEPLOY_PATH/current.tmp" "$DEPLOY_PATH/current"
cd "$DEPLOY_PATH/current" && php artisan config:cache && php artisan route:cache && php artisan view:cache
echo "Rolled back to $TARGET"
echo "Note: database migrations are not reverted automatically. Restore from $DEPLOY_PATH/backups if a migration must be undone."
