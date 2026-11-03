// Summarise the Lighthouse JSON reports in a folder into summary.json and summary.md.
//   node tools/lighthouse-summary.js audits/2026-11-03-before [audits/2026-11-10-after]
// With two folders it also writes a before/after comparison into the second folder.
const fs = require('fs');
const path = require('path');

const METRICS = {
  fcp: 'first-contentful-paint',
  lcp: 'largest-contentful-paint',
  tbt: 'total-blocking-time',
  cls: 'cumulative-layout-shift',
  si: 'speed-index',
};

function summarise(dir) {
  const rows = [];
  for (const file of fs.readdirSync(dir).filter((f) => f.endsWith('.report.json')).sort()) {
    const r = JSON.parse(fs.readFileSync(path.join(dir, file), 'utf8'));
    const [page, device] = file.replace('.report.json', '').split('.');
    const score = (k) => (r.categories[k] && r.categories[k].score !== null ? Math.round(r.categories[k].score * 100) : null);
    const audit = (id) => r.audits[id] || {};
    const row = {
      page,
      device,
      url: r.finalDisplayedUrl,
      status: (r.runtimeError && r.runtimeError.code) || 'ok',
      performance: score('performance'),
      accessibility: score('accessibility'),
      bestPractices: score('best-practices'),
      seo: score('seo'),
      totalBytes: audit('total-byte-weight').numericValue || null,
      requests: ((audit('network-requests').details || {}).items || []).length,
      failedSeoAudits: Object.values(r.audits)
        .filter((a) => (r.categories.seo.auditRefs || []).some((ref) => ref.id === a.id && ref.weight > 0) && a.score !== null && a.score < 1)
        .map((a) => a.title),
    };
    for (const [key, id] of Object.entries(METRICS)) row[key] = audit(id).numericValue ?? null;
    rows.push(row);
  }
  return rows;
}

const fmtMs = (v) => (v === null ? '–' : v >= 1000 ? (v / 1000).toFixed(1) + ' s' : Math.round(v) + ' ms');
const fmtKb = (v) => (v === null ? '–' : v > 1e6 ? (v / 1e6).toFixed(1) + ' MB' : Math.round(v / 1024) + ' KB');

function table(rows) {
  const head = '| Page | Device | Perf | A11y | Best pr. | SEO | LCP | FCP | TBT | CLS | Speed idx | Weight | Requests |\n|---|---|---|---|---|---|---|---|---|---|---|---|---|';
  const body = rows.map((r) => `| ${r.page} | ${r.device} | ${r.performance ?? '–'} | ${r.accessibility ?? '–'} | ${r.bestPractices ?? '–'} | ${r.seo ?? '–'} | ${fmtMs(r.lcp)} | ${fmtMs(r.fcp)} | ${fmtMs(r.tbt)} | ${r.cls === null ? '–' : r.cls.toFixed(3)} | ${fmtMs(r.si)} | ${fmtKb(r.totalBytes)} | ${r.requests} |`);
  return [head, ...body].join('\n');
}

function compare(before, after) {
  const key = (r) => r.page + '.' + r.device;
  const b = Object.fromEntries(before.map((r) => [key(r), r]));
  const diff = (x, y) => (x === null || y === null ? '–' : (y - x > 0 ? '+' : '') + (y - x));
  const head = '| Page | Device | Perf | A11y | Best pr. | SEO | LCP | Weight |\n|---|---|---|---|---|---|---|---|';
  const body = after.filter((r) => b[key(r)]).map((r) => {
    const o = b[key(r)];
    return `| ${r.page} | ${r.device} | ${o.performance ?? '–'} → ${r.performance ?? '–'} (${diff(o.performance, r.performance)}) | ${o.accessibility} → ${r.accessibility} | ${o.bestPractices} → ${r.bestPractices} | ${o.seo} → ${r.seo} | ${fmtMs(o.lcp)} → ${fmtMs(r.lcp)} | ${fmtKb(o.totalBytes)} → ${fmtKb(r.totalBytes)} |`;
  });
  return [head, ...body].join('\n');
}

const [dirA, dirB] = process.argv.slice(2);
const a = summarise(dirA);
fs.writeFileSync(path.join(dirA, 'summary.json'), JSON.stringify(a, null, 2));
let md = `# Lighthouse – ${path.basename(dirA)}\n\n${table(a)}\n\n## Failed SEO audits\n\n` +
  a.map((r) => `- ${r.page} (${r.device}): ${r.status !== 'ok' ? 'run error ' + r.status : r.failedSeoAudits.join('; ') || 'none'}`).join('\n') + '\n';
fs.writeFileSync(path.join(dirA, 'summary.md'), md);
console.log(table(a));

if (dirB) {
  const b = summarise(dirB);
  const out = `# Before / after\n\n${path.basename(dirA)} → ${path.basename(dirB)}\n\n${compare(a, b)}\n`;
  fs.writeFileSync(path.join(dirB, 'comparison.md'), out);
  console.log('\n' + compare(a, b));
}
