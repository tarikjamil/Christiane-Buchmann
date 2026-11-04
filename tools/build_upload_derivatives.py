#!/usr/bin/env python3
"""
Download WordPress upload stills used by projects and build responsive
JPEG + AVIF derivatives under christiane-buchmann/images/uploads/.

Usage:
    python3 tools/build_upload_derivatives.py

Widths: 500, 800, 1080, 1600 (only when source is wider).
"""
from __future__ import annotations

import json
import os
import subprocess
import sys
import urllib.error
import urllib.parse
import urllib.request

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
THEME = os.path.join(ROOT, "christiane-buchmann")
OUT_ROOT = os.path.join(THEME, "images", "uploads")
LIVE = "https://christianebuchmann.com"
WIDTHS = (500, 800, 1080, 1600)
AVIF_Q = int(os.environ.get("AVIF_QUALITY", "55"))
AVIF_SPEED = int(os.environ.get("AVIF_SPEED", "6"))


def projects() -> list:
    out = subprocess.check_output(
        ["php", "-r", f'echo json_encode(require "{THEME}/inc/projects.php");']
    )
    return json.loads(out)


def collect_upload_paths() -> list[str]:
    paths: list[str] = []
    for p in projects():
        for key in (p.get("image"),):
            if key and "wp-content/uploads" in key:
                paths.append(key)
        for g in p.get("gallery") or []:
            src = g.get("src") or ""
            if src and "wp-content/uploads" in src:
                paths.append(src)
    # Normalize leading slash
    normed = []
    seen = set()
    for path in paths:
        path = "/" + path.lstrip("/")
        if path not in seen:
            seen.add(path)
            normed.append(path)
    return normed


def live_url(path: str) -> str:
    parts = path.split("/")
    return LIVE + "/".join(urllib.parse.quote(part) for part in parts)


def local_rel(path: str) -> str:
    """/wp-content/uploads/2020/06/4.jpg -> images/uploads/2020/06/4.jpg"""
    prefix = "/wp-content/uploads/"
    if not path.startswith(prefix):
        raise ValueError(path)
    return "images/uploads/" + path[len(prefix) :]


def local_abs(rel: str) -> str:
    return os.path.join(THEME, rel)


def ensure_parent(path: str) -> None:
    os.makedirs(os.path.dirname(path), exist_ok=True)


def download(path: str, dest: str) -> None:
    if os.path.isfile(dest) and os.path.getsize(dest) > 0:
        return
    ensure_parent(dest)
    url = live_url(path)
    print(f"  download {url}")
    try:
        urllib.request.urlretrieve(url, dest)
    except urllib.error.HTTPError as e:
        # Retry with NFC/NFD variants for macOS unicode filenames
        import unicodedata

        for form in ("NFC", "NFD"):
            alt = unicodedata.normalize(form, path)
            if alt == path:
                continue
            try:
                urllib.request.urlretrieve(live_url(alt), dest)
                return
            except Exception:
                pass
        raise e


def sips_size(path: str) -> tuple[int, int]:
    info = subprocess.check_output(
        ["sips", "-g", "pixelWidth", "-g", "pixelHeight", path], text=True
    )
    w = int(info.split("pixelWidth:")[1].split()[0])
    h = int(info.split("pixelHeight:")[1].split()[0])
    return w, h


def make_jpeg_variant(src: str, dest: str, width: int) -> None:
    if os.path.isfile(dest) and os.path.getmtime(dest) >= os.path.getmtime(src):
        return
    ensure_parent(dest)
    # Resize longest side to width, force JPEG output
    subprocess.check_call(
        [
            "sips",
            "-s",
            "format",
            "jpeg",
            "-s",
            "formatOptions",
            "72",
            "-Z",
            str(width),
            src,
            "--out",
            dest,
        ],
        stdout=subprocess.DEVNULL,
    )


def make_avif(src: str, dest: str) -> None:
    if os.path.isfile(dest) and os.path.getmtime(dest) >= os.path.getmtime(src):
        return
    ensure_parent(dest)
    subprocess.check_call(
        [
            "avifenc",
            "-q",
            str(AVIF_Q),
            "-s",
            str(AVIF_SPEED),
            "-j",
            "all",
            src,
            dest,
        ],
        stdout=subprocess.DEVNULL,
    )


def process(path: str) -> None:
    rel = local_rel(path)
    original = local_abs(rel)
    print(f"• {path}")
    download(path, original)
    w, h = sips_size(original)
    base, _ext = os.path.splitext(rel)

    # Full-size AVIF sibling next to original (for cb_avif_path)
    full_avif = local_abs(base + ".avif")
    try:
        make_avif(original, full_avif)
    except subprocess.CalledProcessError as e:
        print(f"  avif full failed: {e}")

    for width in WIDTHS:
        if w <= width:
            continue
        jpeg = local_abs(f"{base}-p-{width}.jpeg")
        avif = local_abs(f"{base}-p-{width}.avif")
        make_jpeg_variant(original, jpeg, width)
        try:
            make_avif(jpeg, avif)
        except subprocess.CalledProcessError as e:
            print(f"  avif {width} failed: {e}")
        print(f"  + {width}w  jpeg={os.path.getsize(jpeg)} avif={os.path.getsize(avif) if os.path.isfile(avif) else 0}")


def main() -> int:
    paths = collect_upload_paths()
    print(f"{len(paths)} upload images → {OUT_ROOT}")
    os.makedirs(OUT_ROOT, exist_ok=True)
    failed = []
    for path in paths:
        try:
            process(path)
        except Exception as e:
            print(f"  FAILED {path}: {e}", file=sys.stderr)
            failed.append((path, str(e)))
    print(f"done. ok={len(paths) - len(failed)} failed={len(failed)}")
    for path, err in failed:
        print(f"  - {path}: {err}")
    return 1 if failed else 0


if __name__ == "__main__":
    raise SystemExit(main())
