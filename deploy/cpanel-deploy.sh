#!/bin/bash
# Run by cPanel's "Deploy HEAD Commit" through .cpanel.yml. See CPANEL.md for the flow.
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ACCOUNT_HOME="$(dirname "$(dirname "$APP_DIR")")"
export HOME="${HOME:-$ACCOUNT_HOME}"

PHP="${PHP_BIN:-/usr/local/bin/php}"
COMPOSER="${COMPOSER_PHAR:-$ACCOUNT_HOME/composer.phar}"

cd "$APP_DIR"
mkdir -p storage/logs
exec > >(tee -a storage/logs/deploy.log) 2>&1

echo "=== deploy $(git rev-parse --short HEAD) at $(date '+%Y-%m-%d %H:%M:%S')"

fail() { echo "DEPLOY FAILED: $*"; exit 1; }

[ -x "$PHP" ] || fail "no PHP at $PHP"
"$PHP" -r 'exit(PHP_VERSION_ID >= 80300 ? 0 : 1);' || fail "$PHP is older than 8.3"
[ -f "$COMPOSER" ] || fail "no composer.phar at $COMPOSER"
[ -f .env ] || fail ".env is missing"
[ -f public/build/manifest.json ] || fail "public/build is missing; is cPanel on the production branch?"

"$PHP" "$COMPOSER" install --no-dev --optimize-autoloader --no-interaction --no-progress

"$PHP" artisan down --retry=15
trap '"$PHP" artisan up' EXIT

"$PHP" artisan migrate --force
"$PHP" artisan config:cache
"$PHP" artisan route:cache
"$PHP" artisan view:cache
"$PHP" artisan queue:restart

"$PHP" artisan up
trap - EXIT

# cPanel refuses the next deploy while tracked files differ from HEAD.
if [ -n "$(git status --porcelain --untracked-files=no)" ]; then
    echo "WARNING: tracked files changed on the server; the next deploy will be refused:"
    git status --short --untracked-files=no
fi

"$PHP" artisan deploy:check

echo "=== done"
