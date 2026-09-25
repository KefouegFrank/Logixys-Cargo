#!/bin/bash
# Builds public/build and commits it onto `production`, the branch cPanel checks out.
# The server's Node can't run Vite 8, so the build happens here. Run it after pushing main.
set -euo pipefail

cd "$(dirname "${BASH_SOURCE[0]}")/.."

fail() { echo "publish failed: $*" >&2; exit 1; }

[ "$(git rev-parse --abbrev-ref HEAD)" = main ] || fail "check out main first"
[ -z "$(git status --porcelain)" ] || fail "commit or stash your changes first"

git fetch --quiet origin
[ "$(git rev-parse HEAD)" = "$(git rev-parse origin/main)" ] || fail "push main first, or pull if it's behind"

# The admin theme imports Filament's CSS from vendor/, so it must match composer.lock.
composer install --no-interaction --no-progress
[ -z "$(git status --porcelain)" ] || fail "composer install changed tracked files; commit them, push, rerun"

npm ci --no-audit --no-fund
npm run build

# A throwaway index, so the build never touches your own staging area.
tmp="$(mktemp -d)"
trap 'rm -rf "$tmp"' EXIT
export GIT_INDEX_FILE="$tmp/index"
git read-tree HEAD
git add -f public/build
tree="$(git write-tree)"
unset GIT_INDEX_FILE

# Parent on the previous production tip so cPanel's pull is always a fast-forward.
parents=(-p HEAD)
if git rev-parse --verify --quiet origin/production >/dev/null; then
    parents=(-p origin/production "${parents[@]}")
fi

commit="$(git commit-tree "$tree" "${parents[@]}" -m "build $(git rev-parse --short HEAD)")"
git push origin "$commit:refs/heads/production"

echo "production is now $(git rev-parse --short "$commit"). In cPanel: Update from Remote, then Deploy HEAD Commit."
