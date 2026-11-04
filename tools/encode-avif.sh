#!/usr/bin/env bash
# High-quality AVIF siblings for theme images (keeps JPEG/PNG as fallback).
# Usage: ./tools/encode-avif.sh
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
IMG="$ROOT/christiane-buchmann/images"
Q="${AVIF_QUALITY:-90}"
SPEED="${AVIF_SPEED:-4}"

if ! command -v avifenc >/dev/null 2>&1; then
  echo "avifenc not found (brew install libavif)" >&2
  exit 1
fi

find "$IMG" -type f \( -iname '*.jpg' -o -iname '*.jpeg' -o -iname '*.png' \) | while read -r f; do
  base="$(basename "$f")"
  case "$base" in
    favicon.jpg|webclip.jpg) echo "skip $f"; continue ;;
  esac
  out="${f%.*}.avif"
  if [[ -f "$out" && "$out" -nt "$f" ]]; then
    echo "fresh $out"
    continue
  fi
  echo "encode q=$Q $f"
  avifenc -q "$Q" -s "$SPEED" -j all --yuv 444 "$f" "$out"
done
echo "done"
