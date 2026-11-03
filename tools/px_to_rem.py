"""Convert px to rem (and media query breakpoints to em) in the theme CSS, based on a 16px root.
Hairlines (<= 1px) stay in px so borders and text outlines stay crisp.
    python3 tools/px_to_rem.py
"""
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
CSS_DIR = os.path.join(ROOT, "christiane-buchmann", "css")
FILES = ["normalize.css", "components.css", "christiane-buchmann.css", "theme.css"]
BASE = 16.0

PX = re.compile(r"(?<![\w.#-])(-?\d*\.?\d+)px\b")


def fmt(value, unit):
    s = ("%.4f" % value).rstrip("0").rstrip(".")
    return "0" if s in ("0", "-0") else s + unit


def to_rem(m):
    px = float(m.group(1))
    if abs(px) <= 1:
        return m.group(0)
    return fmt(px / BASE, "rem")


def to_em(m):
    return fmt(float(m.group(1)) / BASE, "em")


def convert(css):
    out, i = [], 0
    for m in re.finditer(r"url\([^)]*\)", css):
        out.append(convert_plain(css[i:m.start()]))
        out.append(m.group(0))
        i = m.end()
    out.append(convert_plain(css[i:]))
    return "".join(out)


def convert_plain(css):
    css = re.sub(r"@media[^{]+", lambda m: PX.sub(to_em, m.group(0)), css)
    parts = re.split(r"(@media[^{]+)", css)
    return "".join(p if p.startswith("@media") else PX.sub(to_rem, p) for p in parts)


def main():
    for name in FILES:
        path = os.path.join(CSS_DIR, name)
        before = open(path).read()
        after = convert(before)
        open(path, "w").write(after)
        print("%-26s px before: %3d  after: %3d" % (name, len(PX.findall(before)), len(PX.findall(after))))


if __name__ == "__main__":
    main()
