"""Extract project data from the captured WordPress pages into reference/projects.json."""
import glob
import html as H
import json
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
REF = os.path.join(ROOT, "reference")

CATEGORY_BY_WP_ID = {12: "narrative", 3: "music-video", 4: "commercial", 5: "documentary"}


def first(pattern, text):
    m = re.search(pattern, text, re.S)
    return m.group(1).strip() if m else ""


def uploads_path(url):
    return url.split("christianebuchmann.com", 1)[-1] if url else ""


def clean_credits(s):
    s = re.sub(r"<br[^>]*>", "<br>", s)
    s = re.sub(r"</?p[^>]*>", "", s)
    s = re.sub(r"\s*<br>\s*", "<br>\n", s)
    return s.strip()


def main():
    meta = {p["slug"]: p for p in json.load(open(os.path.join(REF, "wp_projects_meta.json")))}
    projects = []
    for path in sorted(glob.glob(os.path.join(REF, "html", "all-projects__*.html"))):
        slug = os.path.basename(path)[len("all-projects__"):-5]
        if slug.endswith(".archive"):
            continue
        h = open(path).read()
        if "critical error" in h:
            h = open(path.replace(".html", ".archive.html")).read()
        main_html = h[h.find('<div id="hero"'):h.find('<div class="fullsection footersec"')]
        body = first(r'<div class="section notoppadding">\s*<div class="largecontainer">\s*<div class="w-richtext">(.*?)</div>\s*<p class="_24topmargin">', main_html)
        body_u = H.unescape(body).replace("\n", "")
        videos = []
        for vid in re.findall(r"vimeo\.com/(?:video/)?\s*(\d{6,})", body_u):
            if ("vimeo", vid) not in videos:
                videos.append(("vimeo", vid))
        for a, b in re.findall(r"youtube(?:-nocookie)?\.com\\?/(?:embed\\?/|watch\?v=)([\w-]{11})|youtu\.be\\?/([\w-]{11})", body_u):
            if ("youtube", a or b) not in videos:
                videos.append(("youtube", a or b))
        gallery = []
        for src in re.findall(r'(?:src|data-thumbnail)="(http://christianebuchmann\.com/wp-content/uploads/[^"]+)"', body):
            if src not in gallery:
                gallery.append(src)
        leftover = re.sub(r"<iframe.*?</iframe>|<pre.*?</pre>|<figcaption.*?</figcaption>", " ", body, flags=re.S)
        leftover = H.unescape(re.sub(r"<[^>]+>", " ", leftover))
        leftover = re.sub(r"\s+", " ", leftover).strip()
        m = meta.get(slug, {})
        cats = [CATEGORY_BY_WP_ID[c] for c in m.get("project_categories", []) if c in CATEGORY_BY_WP_ID]
        projects.append({
            "slug": slug,
            "category": cats[0] if cats else "",
            "date": m.get("date", "")[:10],
            "title": H.unescape(first(r"<h1[^>]*>(.*?)</h1>", main_html)),
            "subtitle": H.unescape(first(r'<p class="h5">(.*?)</p>', main_html)),
            "tagline": H.unescape(first(r'<p class="h5">.*?</p>\s*</div>\s*<div class="hide">\s*<p>(.*?)</p>', main_html)),
            "description": H.unescape(first(r'<p data-w-id="56662b6c[^>]*>(.*?)</p>', main_html)),
            "videos": [{"provider": p, "id": i} for p, i in videos],
            "gallery": [uploads_path(g) for g in gallery],
            "body_leftover_text": leftover,
            "details": H.unescape(first(r'<p class="_24topmargin">(.*?)</p>', main_html)),
            "credits": clean_credits(first(r'<div class="largecontainer _24topmargin">\s*<div class="w-richtext">(.*?)</div>\s*<div class="_24topmargin">', main_html)),
            "crew_united": first(r'<a href="([^"]*)" target="_blank" class="socialmedia_link', main_html),
            "image": uploads_path(first(r'<div class="fullsection"><img src="([^"]*)"', main_html)),
        })
    json.dump(projects, open(os.path.join(REF, "projects.json"), "w"), indent=2, ensure_ascii=False)
    for p in projects:
        print(p["slug"], p["category"], len(p["videos"]), len(p["gallery"]), "| leftover:", p["body_leftover_text"][:160])


if __name__ == "__main__":
    main()
