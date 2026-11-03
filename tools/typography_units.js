// Rewrite line-height as a percentage and letter-spacing in em, keeping the rendered values identical.
// Percentages are whole numbers because Chrome floors fractional line-height percentages.
// Both are relative to the element's own font size, so the font size of the elements each rule applies to
// is read from the running site (local preview) at the width matching the rule's media query.
//   NODE_PATH=/tmp/cbshot/node_modules node tools/typography_units.js http://localhost:8080
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = process.argv[2] || 'http://localhost:8080';
const CSS_DIR = path.join(__dirname, '..', 'christiane-buchmann', 'css');
const FILES = ['components.css', 'christiane-buchmann.css', 'theme.css'];
const PAGES = ['/', '/about/', '/portfolio/', '/contact/', '/gdpr-privacy-notice/', '/all-projects/soko-linz/', '/all-projects/kuss-mich/', '/all-projects/nope/'];
const WIDTH_FOR_MEDIA = (media) => {
  if (!media) return 1440;
  const m = media.match(/max-width:\s*([\d.]+)em/);
  if (!m) return 1440;
  return { '61.9375': 900, '47.9375': 600, '29.9375': 375 }[m[1]] || 1440;
};

// Split a flat CSS file into rule blocks, remembering the enclosing @media.
function parse(css) {
  const rules = [];
  let i = 0;
  let media = null;
  let depth = 0;
  let mediaEnd = -1;
  const re = /([^{}]+)\{([^{}]*)\}|(@media[^{]+)\{|\}/g;
  let m;
  while ((m = re.exec(css))) {
    if (m[3]) { media = m[3].trim(); depth = 1; continue; }
    if (m[0] === '}') { media = null; continue; }
    rules.push({ media, selector: m[1].trim(), body: m[2], start: m.index + m[1].length + 1, end: m.index + m[0].length - 1 });
  }
  return rules;
}

const toPx = (v) => {
  const m = String(v).trim().match(/^(-?[\d.]+)(rem|px)$/);
  if (!m) return null;
  return m[2] === 'rem' ? parseFloat(m[1]) * 16 : parseFloat(m[1]);
};
const round = (n, d) => String(Math.round(n * 10 ** d) / 10 ** d);

(async () => {
  const files = FILES.map((f) => ({ name: f, css: fs.readFileSync(path.join(CSS_DIR, f), 'utf8') }));
  const targets = [];
  for (const file of files) {
    for (const rule of parse(file.css)) {
      const lh = rule.body.match(/line-height:\s*([^;]+);/);
      const ls = rule.body.match(/letter-spacing:\s*([^;]+);/);
      if ((lh && toPx(lh[1]) !== null) || (ls && toPx(ls[1]) !== null)) targets.push({ file, rule, lh: lh && lh[1], ls: ls && ls[1] });
    }
  }

  const browser = await chromium.launch();
  const sizes = {};
  for (const width of [1440, 900, 600, 375]) {
    const page = await browser.newPage({ viewport: { width, height: 900 } });
    await page.route('**/*.{mp4,webm}', (r) => r.abort());
    for (const p of PAGES) {
      await page.goto(BASE + p, { waitUntil: 'networkidle' });
      // Only count elements where this rule's value is the one that ends up applied.
      const found = await page.evaluate((specs) => specs.map(({ sel, lhPx, lsPx }) => {
        const clean = sel.split(',').map((s) => s.replace(/::?[a-z-]+(\([^)]*\))?/g, '').trim()).filter(Boolean).join(',');
        let els = [];
        try { els = [...document.querySelectorAll(clean || 'html')]; } catch (e) { return null; }
        const counts = {};
        els.forEach((el) => {
          const cs = getComputedStyle(el);
          const wins = (lhPx !== null && Math.abs(parseFloat(cs.lineHeight) - lhPx) < 0.01) || (lsPx !== null && Math.abs(parseFloat(cs.letterSpacing) - lsPx) < 0.01);
          if (wins) counts[cs.fontSize] = (counts[cs.fontSize] || 0) + 1;
        });
        return Object.keys(counts).length ? counts : null;
      }), targets.map((t) => ({ sel: t.rule.selector, lhPx: t.lh ? toPx(t.lh) : null, lsPx: t.ls ? toPx(t.ls) : null })));
      found.forEach((counts, i) => {
        if (!counts) return;
        const key = width + '|' + i;
        sizes[key] = sizes[key] || {};
        for (const [fs, n] of Object.entries(counts)) sizes[key][fs] = (sizes[key][fs] || 0) + n;
      });
    }
    await page.close();
  }
  await browser.close();

  const report = [];
  targets.forEach((t, i) => {
    const width = WIDTH_FOR_MEDIA(t.rule.media);
    const counts = sizes[width + '|' + i];
    let fontPx;
    let source;
    if (counts && Object.keys(counts).length > 1) {
      t.newBody = t.rule.body;
      report.push(`${t.file.name} | ${t.rule.media || ''} ${t.rule.selector} | KEPT: applies to several font sizes ${JSON.stringify(counts)}`);
      return;
    }
    if (counts) {
      fontPx = parseFloat(Object.keys(counts)[0]);
      source = 'measured';
    } else {
      const own = t.rule.body.match(/font-size:\s*([^;]+);/);
      fontPx = (own && toPx(own[1])) || 16;
      source = own ? 'own font-size' : 'unused, assumed 16px';
    }
    let body = t.rule.body;
    if (t.lh && toPx(t.lh) !== null) body = body.replace(/line-height:\s*[^;]+;/, 'line-height: ' + round((toPx(t.lh) / fontPx) * 100, 0) + '%;');
    if (t.ls && toPx(t.ls) !== null) body = body.replace(/letter-spacing:\s*[^;]+;/, 'letter-spacing: ' + round(toPx(t.ls) / fontPx, 4) + 'em;');
    t.newBody = body;
    report.push(`${t.file.name} | ${t.rule.media || ''} ${t.rule.selector} | font ${fontPx}px ${source} | ${t.lh || ''} ${t.ls || ''}`);
  });

  for (const file of files) {
    const edits = targets.filter((t) => t.file === file).sort((a, b) => b.rule.start - a.rule.start);
    let css = file.css;
    for (const t of edits) css = css.slice(0, t.rule.start) + t.newBody + css.slice(t.rule.end);
    fs.writeFileSync(path.join(CSS_DIR, file.name), css);
  }
  console.log(report.join('\n'));
  console.log('\nconverted rules:', targets.length);
})();
