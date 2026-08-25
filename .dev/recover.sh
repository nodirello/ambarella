#!/usr/bin/env bash
# AMBARELLA dev tooling — recovery script for fresh checkouts / sandbox resets.
# Heavy, git-ignored dependencies are not part of the repo; this restores them.
set -euo pipefail
cd "$(dirname "$0")/.."

if [ ! -f vendor/autoload.php ]; then
  echo "→ vendor/ tiklanmoqda (git tarixidagi asl ZIP'dan)…"
  git show d989cbb:ambarella.uz.zip > /tmp/ambarella-legacy.zip
  mkdir -p /tmp/ambarella-extract
  unzip -q -o /tmp/ambarella-legacy.zip "vendor/*" -d /tmp/ambarella-extract
  cp -r /tmp/ambarella-extract/vendor ./vendor
fi

if [ ! -d .dev/node_modules/@php-wasm ]; then
  echo "→ .dev/node_modules o'rnatilmoqda…"
  (cd .dev && npm install --no-audit --no-fund)
fi

echo "✓ Dev muhit tayyor. Ishga tushirish: node .dev/server.mjs 8080"
