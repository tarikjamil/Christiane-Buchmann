"""On-page SEO audit of every URL in the sitemap (plus drafts), written to audits/<label>/seo.md.
    python3 tools/seo_audit.py 2026-11-03-seo https://christianebuchmann.com
"""
import collections
import html
import json
import os
import re
import sys
import urllib.request

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))


def fetch(url):
    req = urllib.request.Request(url, headers={"User-Agent": "Mozilla/5.0 (SEO audit)"})
    try:
        with urllib.request.urlopen(req, timeout=30) as r:
            return r.status, r.read().decode("utf-8", "replace")
    except urllib.error.HTTPError as e:
        return e.code, e.read().decode("utf-8", "replace")


def text_of(fragment):
    return html.unescape(re.sub(r"\s+", " ", re.sub(r"<[^>]+>", " ", fragment))).strip()


def audit(url):
    status, h = fetch(url)
    head = h[: h.find("</head>")]
    body = h[h.find("<body"):]
    body_text = re.sub(r"<(script|style)\b.*?</\1>", " ", body, flags=re.S)
    get = lambda p, s=head: (re.search(p, s, re.S | re.I).group(1).strip() if re.search(p, s, re.S | re.I) else "")
    r = {
        "url": url,
        "status": status,
        "lang": get(r'<html[^>]*\blang="([^"]*)"', h),
        "title": html.unescape(get(r"<title>(.*?)</title>")),
        "description": html.unescape(get(r'<meta name="description" content="([^"]*)"')),
        "canonical": get(r'<link rel="canonical" href="([^"]*)"'),
        "robots": get(r'<meta name="robots" content="([^"]*)"'),
        "og_image": get(r'<meta property="og:image" content="([^"]*)"'),
        "h1": [text_of(x) for x in re.findall(r"<h1\b[^>]*>(.*?)</h1>", body, re.S)],
        "h2_count": len(re.findall(r"<h2\b", body)),
        "img_total": len(re.findall(r"<img\b", body)),
        "img_no_alt": len([i for i in re.findall(r"<img\b[^>]*>", body) if not re.search(r'\salt="', i)]),
        "img_empty_alt": len(re.findall(r'<img\b[^>]*\salt=""', body)),
        "words": len(text_of(body_text).split()),
        "schema_types": [],
        "schema_ok": True,
        "links_no_text": 0,
    }
    for block in re.findall(r'<script type="application/ld\+json">(.*?)</script>', h, re.S):
        try:
            data = json.loads(block)
            r["schema_types"] += [g.get("@type") for g in data.get("@graph", [data])]
        except Exception:
            r["schema_ok"] = False
    for a in re.findall(r"<a\b([^>]*)>(.*?)</a>", body, re.S):
        if not text_of(a[1]) and "aria-label" not in a[0] and not re.search(r'alt="[^"]+"', a[1]):
            r["links_no_text"] += 1
    return r


def issues(r):
    out = []
    if r["status"] != 200:
        out.append("HTTP %s" % r["status"])
    if not r["lang"]:
        out.append("no lang attribute")
    if not r["title"]:
        out.append("no title")
    elif not 30 <= len(r["title"]) <= 65:
        out.append("title length %d (aim 30–65)" % len(r["title"]))
    if not r["description"]:
        out.append("no meta description")
    elif not 70 <= len(r["description"]) <= 160:
        out.append("description length %d (aim 70–160)" % len(r["description"]))
    if r["canonical"] != r["url"]:
        out.append("canonical mismatch (%s)" % r["canonical"])
    if len(r["h1"]) != 1:
        out.append("%d h1 tags" % len(r["h1"]))
    if r["img_no_alt"]:
        out.append("%d images without alt" % r["img_no_alt"])
    if r["links_no_text"]:
        out.append("%d links without text" % r["links_no_text"])
    if not r["schema_ok"]:
        out.append("invalid JSON-LD")
    if r["words"] < 150:
        out.append("thin content (%d words)" % r["words"])
    return out


def main():
    label = sys.argv[1]
    base = sys.argv[2].rstrip("/") if len(sys.argv) > 2 else "https://christianebuchmann.com"
    _, sitemap = fetch(base + "/sitemap.xml")
    urls = re.findall(r"<loc>([^<]+)</loc>", sitemap)
    urls = [u for u in urls if "/wp-content/" not in u]
    urls += [base + "/gdpr-privacy-notice/"]
    rows = [audit(u) for u in urls]
    titles = collections.Counter(r["title"] for r in rows)
    descs = collections.Counter(r["description"] for r in rows)

    out_dir = os.path.join(ROOT, "audits", label)
    os.makedirs(out_dir, exist_ok=True)
    json.dump(rows, open(os.path.join(out_dir, "seo.json"), "w"), indent=2, ensure_ascii=False)

    lines = ["# SEO audit – %s" % label, "", "%d URLs from %s/sitemap.xml" % (len(rows), base), "",
             "| Page | Title (len) | Desc (len) | H1 | Words | Imgs (empty alt) | Schema | Issues |", "|---|---|---|---|---|---|---|---|"]
    for r in rows:
        iss = issues(r)
        if titles[r["title"]] > 1:
            iss.append("duplicate title")
        if r["description"] and descs[r["description"]] > 1:
            iss.append("duplicate description")
        path = r["url"].replace(base, "") or "/"
        lines.append("| %s | %d | %d | %s | %d | %d (%d) | %s | %s |" % (
            path, len(r["title"]), len(r["description"]), "; ".join(r["h1"])[:40], r["words"],
            r["img_total"], r["img_empty_alt"], ", ".join(t for t in r["schema_types"] if t), "; ".join(iss) or "ok"))
    open(os.path.join(out_dir, "seo.md"), "w").write("\n".join(lines) + "\n")
    print("\n".join(lines))


if __name__ == "__main__":
    main()
