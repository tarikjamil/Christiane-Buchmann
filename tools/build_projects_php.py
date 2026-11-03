"""One-off: turn reference/projects.json into the theme's inc/projects.php data file."""
import html as H
import json
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
REF = os.path.join(ROOT, "reference")
OUT = os.path.join(ROOT, "christiane-buchmann", "inc", "projects.php")

ORDER = [
    "falsches-spiel-false-game", "ausgetanzt-danced-out", "die-kalte-der-erde", "schatten", "soko-linz",
    "die-pfefferkorner", "junas-phantastische-reise", "la-cigale", "dedowtschina", "exmun", "efecto-domino",
    "the-only-witness", "annalog", "another-world", "kuss-mich",
    "telekom", "belton-free", "volksbanken-raifeisenbanken", "vr-innovationaward-lasertemp", "wood-chip-press", "tchibo",
    "neonstaub", "sinema-leo", "la-sagrada-de-cuba",
]

OVERRIDES = {
    "die-kalte-der-erde": {"videos": [("vimeo", "1096845777")]},
    "soko-linz": {"videos": [("vimeo", "1121831409")]},
    "another-world": {"crew_united": "https://www.crew-united.com/en/Lovespells-Another-World__243297.html"},
    "efecto-domino": {"notes": ["Selected at 57 International Film Festivals", "Winner of 14 Awards"]},
}


def text(s):
    s = H.unescape(s or "").replace("\xa0", " ")
    return re.sub(r"\s+", " ", s).strip()


def credit_lines(s):
    s = re.sub(r"<!--.*?-->", "", s or "", flags=re.S)
    s = re.sub(r"<br[^>]*>", "\n", s)
    s = re.sub(r"<[^>]+>", "", s)
    return [t for t in (text(l) for l in s.split("\n")) if t]


def gallery(slug, srcs):
    raw = open(os.path.join(REF, "html", "all-projects__%s.html" % slug)).read()
    items = []
    for src in srcs:
        caption = ""
        m = re.search(re.escape(src) + r'[^>]*>(?:</a>)?<figcaption[^>]*>(.*?)</figcaption>', raw, re.S)
        if m:
            caption = text(re.sub(r"<[^>]+>", "", m.group(1)))
        items.append({"src": src, "caption": caption})
    return items


def php(v, indent=1):
    pad = "    " * indent
    if isinstance(v, dict):
        if not v:
            return "array()"
        inner = ",\n".join("%s%s => %s" % (pad, php(k), php(x, indent + 1)) for k, x in v.items())
        return "array(\n%s,\n%s)" % (inner, "    " * (indent - 1))
    if isinstance(v, (list, tuple)):
        if not v:
            return "array()"
        if all(isinstance(x, str) for x in v) and sum(len(x) for x in v) < 60:
            return "array(" + ", ".join(php(x) for x in v) + ")"
        inner = ",\n".join("%s%s" % (pad, php(x, indent + 1)) for x in v)
        return "array(\n%s,\n%s)" % (inner, "    " * (indent - 1))
    if isinstance(v, bool):
        return "true" if v else "false"
    return "'" + str(v).replace("\\", "\\\\").replace("'", "\\'") + "'"


def main():
    src = {p["slug"]: p for p in json.load(open(os.path.join(REF, "projects.json")))}
    out = []
    for slug in ORDER:
        p = src[slug]
        o = OVERRIDES.get(slug, {})
        videos = o.get("videos", []) + [(v["provider"], v["id"]) for v in p["videos"]]
        out.append({
            "slug": slug,
            "category": p["category"],
            "title": text(p["title"]),
            "subtitle": text(p["subtitle"]),
            "tagline": text(p["tagline"]),
            "description": text(p["description"]),
            "videos": [{"provider": a, "id": b} for a, b in dict.fromkeys(videos)],
            "gallery": gallery(slug, p["gallery"]) if p["gallery"] else [],
            "notes": o.get("notes", []),
            "details": text(p["details"]),
            "credits": credit_lines(p["credits"]),
            "crew_united": o.get("crew_united", p["crew_united"]),
            "image": p["image"],
        })
    body = "<?php\n/**\n * Every project on the site. Order here is the order on the Work page.\n * category: narrative | music-video | commercial | documentary\n * videos: provider is vimeo or youtube. Images are paths under /wp-content/uploads.\n * Set 'draft' => true to keep a project off the Work page and out of the sitemap.\n */\n\nreturn " + php(out) + ";\n"
    open(OUT, "w").write(body)
    print("wrote", OUT, len(out), "projects")


if __name__ == "__main__":
    main()
