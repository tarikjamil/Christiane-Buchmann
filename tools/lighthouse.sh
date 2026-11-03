#!/usr/bin/env bash
# Run Lighthouse (mobile + desktop) on the main pages and save reports under audits/<label>/.
#   tools/lighthouse.sh 2026-11-03-before http://christianebuchmann.com
#   tools/lighthouse.sh 2026-11-10-after https://christianebuchmann.com
set -euo pipefail

LABEL="${1:?label, e.g. 2026-11-03-before}"
BASE="${2:-http://christianebuchmann.com}"
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="$ROOT/audits/$LABEL"
export CHROME_PATH="${CHROME_PATH:-/Applications/Google Chrome.app/Contents/MacOS/Google Chrome}"

PAGES=(
  "home|/"
  "about|/about/"
  "portfolio|/portfolio/"
  "contact|/contact/"
  "project-soko-linz|/all-projects/soko-linz/"
  "project-neonstaub|/all-projects/neonstaub/"
  "project-junas|/all-projects/junas-phantastische-reise/"
)

mkdir -p "$OUT"
for spec in "${PAGES[@]}"; do
  name="${spec%%|*}"
  path="${spec#*|}"
  for device in mobile desktop; do
    extra=()
    [ "$device" = desktop ] && extra=(--preset=desktop)
    echo "→ $name ($device)"
    npx --yes lighthouse@13 "$BASE$path" \
      --quiet --chrome-flags="--headless=new --no-sandbox" \
      --output=json --output=html \
      --output-path="$OUT/$name.$device" \
      ${extra[@]+"${extra[@]}"} || echo "  failed: $name $device"
  done
done

node "$ROOT/tools/lighthouse-summary.js" "$OUT"
