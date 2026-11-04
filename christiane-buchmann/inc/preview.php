<?php
/**
 * Non-indexable redesign preview under /preview/.
 * Live public templates are untouched.
 */

function cb_preview_url($path = '')
{
    $path = trim($path, '/');
    return cb_url('preview/' . ($path !== '' ? $path . '/' : ''));
}

/**
 * Ticket button — two shells (label + stub) with consistent circular cutouts.
 * Label sizes to content; arrow stub is square; text stub sizes to its text.
 * Hover: whole stub (shape + arrow) slides right.
 *
 * $label  visible text
 * $after  hover swap text (defaults to $label)
 * $mod    ink|light|chip|''
 * $attrs  extra HTML attributes for the root
 * $stub   'arrow' | 'arrow-left' | string (text stub, e.g. year) | '' (no stub)
 * $href   URL; empty renders a <span> (non-link chip)
 */
function cb_preview_ticket_geom()
{
    static $g = null;
    if ($g !== null) {
        return $g;
    }
    $h = 44.0;
    $r = 4.0;    // small cut — same radius for left scallops + join corners
    $R = 9.5;    // large right-edge bite on stub / solo
    $n = 4;      // left-edge perforations
    $lo = $r + 2.75;
    $hi = $h - $r - 2.75;
    $centers = array();
    for ($i = 0; $i < $n; $i++) {
        $centers[] = $lo + ($i * ($hi - $lo) / max(1, $n - 1));
    }
    $cy = $h / 2;

    // Left cap: only as wide as the scallop depth — no body overlap into mid.
    $lw = $r;
    // Sweep 1 (CW): arcs bulge into the body so scallops read as cutouts.
    $left_fill = "M{$lw} 0 H0";
    foreach ($centers as $c) {
        $left_fill .= ' V' . ($c - $r) . " A{$r} {$r} 0 0 1 0 " . ($c + $r);
    }
    $left_fill .= " V{$h} H{$lw} Z";
    $left_stroke = "M{$lw} 0 H0";
    foreach ($centers as $c) {
        $left_stroke .= ' V' . ($c - $r) . " A{$r} {$r} 0 0 1 0 " . ($c + $r);
    }
    $left_stroke .= " V{$h} H{$lw}";

    // Join cap: corner bites on the right edge (width = r), straight run between them.
    $jw = $r;
    $join_fill = "M0 0 A{$r} {$r} 0 0 0 {$jw} {$r}"
        . ' V' . ($h - $r)
        . " A{$r} {$r} 0 0 0 0 {$h} Z";
    $join_stroke = "M0 0 A{$r} {$r} 0 0 0 {$jw} {$r}"
        . ' V' . ($h - $r)
        . " A{$r} {$r} 0 0 0 0 {$h}";

    // Join-left cap: same corner bites mirrored onto the left edge (strip middles / end).
    $join_left_fill = "M{$jw} 0 A{$r} {$r} 0 0 1 0 {$r}"
        . ' V' . ($h - $r)
        . " A{$r} {$r} 0 0 1 {$jw} {$h} Z";
    $join_left_stroke = "M{$jw} 0 A{$r} {$r} 0 0 1 0 {$r}"
        . ' V' . ($h - $r)
        . " A{$r} {$r} 0 0 1 {$jw} {$h}";

    // Solo end: large right bite.
    $ew = $R;
    $end_fill = "M0 0 H{$ew}"
        . ' V' . ($cy - $R)
        . " A{$R} {$R} 0 0 0 {$ew} " . ($cy + $R)
        . " V{$h} H0 Z";
    $end_stroke = "M0 0 H{$ew}"
        . ' V' . ($cy - $R)
        . " A{$R} {$R} 0 0 0 {$ew} " . ($cy + $R)
        . " V{$h} H0";

    // Stub: join bites on LEFT only; sharp top-right + bottom-right; large right bite.
    $s = $h;
    $stub = "M{$r} 0"
        . " H{$s}"
        . ' V' . ($cy - $R)
        . " A{$R} {$R} 0 0 0 {$s} " . ($cy + $R)
        . " V{$h}"
        . " H{$r}"
        . " A{$r} {$r} 0 0 0 0 " . ($h - $r)
        . " V{$r}"
        . " A{$r} {$r} 0 0 0 {$r} 0 Z";

    $g = array(
        'h' => $h,
        'r' => $r,
        'R' => $R,
        'left_fill' => $left_fill,
        'left_stroke' => $left_stroke,
        'left_vb' => "0 0 {$lw} {$h}",
        'left_w' => $lw,
        'join_fill' => $join_fill,
        'join_stroke' => $join_stroke,
        'join_vb' => "0 0 {$jw} {$h}",
        'join_w' => $jw,
        'join_left_fill' => $join_left_fill,
        'join_left_stroke' => $join_left_stroke,
        'join_left_vb' => "0 0 {$jw} {$h}",
        'join_left_w' => $jw,
        'end_fill' => $end_fill,
        'end_stroke' => $end_stroke,
        'end_vb' => "0 0 {$ew} {$h}",
        'end_w' => $ew,
        'stub' => $stub,
    );
    return $g;
}

function cb_preview_ticket($href, $label, $after = '', $mod = '', $attrs = '', $stub = 'arrow')
{
    $after_text = $after !== '' ? $after : $label;
    $do_swap = ($mod !== 'chip');
    $is_back = ($stub === 'arrow-left');
    $is_arrow = ($stub === 'arrow' || $is_back);
    $has_stub = ($stub !== '' && $stub !== null);
    $is_text_stub = $has_stub && !$is_arrow;
    $class = trim(
        'pv-ticket'
        . ($mod ? ' pv-ticket--' . $mod : '')
        . ($is_back ? ' pv-ticket--back' : '')
        . ($is_text_stub ? ' pv-ticket--text-stub' : '')
        . (!$has_stub ? ' pv-ticket--solo' : '')
    );
    $tag = ($href !== '') ? 'a' : 'span';
    if ($tag === 'a') {
        $attrs = cb_external_link_attrs($href, $attrs);
    }
    $g = cb_preview_ticket_geom();
    $end_cap = $has_stub ? 'join' : 'end';

    $label_html = '';
    ob_start();
    ?>
  <span class="pv-ticket__label">
    <span class="pv-ticket__shell pv-ticket__shell--label" aria-hidden="true">
      <svg class="pv-ticket__cap pv-ticket__cap--left" viewBox="<?php echo esc_attr($g['left_vb']); ?>" width="<?php echo esc_attr($g['left_w']); ?>" height="100%" preserveAspectRatio="xMinYMid meet" focusable="false">
        <path class="pv-ticket__fill" d="<?php echo esc_attr($g['left_fill']); ?>"/>
        <path class="pv-ticket__shape" fill="none" d="<?php echo esc_attr($g['left_stroke']); ?>"/>
      </svg>
      <span class="pv-ticket__mid">
        <span class="pv-ticket__mid-fill"></span>
      </span>
      <svg class="pv-ticket__cap pv-ticket__cap--<?php echo esc_attr($end_cap); ?>" viewBox="<?php echo esc_attr($g[$end_cap . '_vb']); ?>" width="<?php echo esc_attr($g[$end_cap . '_w']); ?>" height="100%" preserveAspectRatio="xMaxYMid meet" focusable="false">
        <path class="pv-ticket__fill" d="<?php echo esc_attr($g[$end_cap . '_fill']); ?>"/>
        <path class="pv-ticket__shape" fill="none" d="<?php echo esc_attr($g[$end_cap . '_stroke']); ?>"/>
      </svg>
    </span>
    <?php if ($do_swap) : ?>
    <span class="pv-ticket__swap" data-after="<?php echo esc_attr(strtoupper($after_text)); ?>">
      <span class="pv-ticket__text"><?php echo esc_html(strtoupper($label)); ?></span>
    </span>
    <?php else : ?>
    <span class="pv-ticket__text"><?php echo esc_html(strtoupper($label)); ?></span>
    <?php endif; ?>
  </span>
    <?php
    $label_html = ob_get_clean();

    $stub_html = '';
    if ($has_stub) {
        ob_start();
        ?>
  <span class="pv-ticket__stub"<?php echo $is_arrow ? ' aria-hidden="true"' : ''; ?>>
    <svg class="pv-ticket__shell pv-ticket__shell--stub" viewBox="0 0 44 44" preserveAspectRatio="<?php echo $is_text_stub ? 'none' : 'xMidYMid meet'; ?>" aria-hidden="true" focusable="false">
      <path class="pv-ticket__fill" d="<?php echo esc_attr($g['stub']); ?>"/>
      <path class="pv-ticket__shape" fill="none" d="<?php echo esc_attr($g['stub']); ?>"/>
    </svg>
    <?php if ($is_arrow) : ?>
    <svg class="pv-ticket__arrow<?php echo $is_back ? ' pv-ticket__arrow--left' : ''; ?>" viewBox="0 0 16 16" width="15" height="15" aria-hidden="true">
      <path fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="square" stroke-linejoin="miter" d="M3 8 H12.25 M8.25 4 L12.75 8 L8.25 12"/>
    </svg>
    <?php else : ?>
    <span class="pv-ticket__stub-text"><?php echo esc_html(strtoupper((string) $stub)); ?></span>
    <?php endif; ?>
  </span>
        <?php
        $stub_html = ob_get_clean();
    }
    ?>
<<?php echo $tag; ?> class="<?php echo esc_attr($class); ?>"<?php
    if ($tag === 'a') {
        echo ' href="' . esc_url($href) . '"';
    }
    echo $attrs ? ' ' . $attrs : '';
?>>
<?php
    if (!$has_stub) {
        echo $label_html;
    } else {
        echo $is_back ? $stub_html . $label_html : $label_html . $stub_html;
    }
?>
</<?php echo $tag; ?>>
    <?php
}

/**
 * Ticket-strip chip shell.
 * $pos: start (perforated left + join right) | middle (join both) | end (join left + big bite) | solo (perforated + bite).
 */
function cb_preview_chip_shell($pos = 'solo')
{
    $g = cb_preview_ticket_geom();
    $left = ($pos === 'start' || $pos === 'solo') ? 'left' : 'join_left';
    $right = ($pos === 'end' || $pos === 'solo') ? 'end' : 'join';
    ?>
  <span class="pv-chip__shell" aria-hidden="true">
    <svg class="pv-chip__cap pv-chip__cap--left" viewBox="<?php echo esc_attr($g[$left . '_vb']); ?>" width="<?php echo esc_attr($g[$left . '_w']); ?>" height="100%" preserveAspectRatio="xMinYMid meet" focusable="false">
      <path class="pv-chip__fill" d="<?php echo esc_attr($g[$left . '_fill']); ?>"/>
      <path class="pv-chip__shape" fill="none" d="<?php echo esc_attr($g[$left . '_stroke']); ?>"/>
    </svg>
    <span class="pv-chip__mid"><span class="pv-chip__mid-fill"></span></span>
    <svg class="pv-chip__cap pv-chip__cap--<?php echo esc_attr($right); ?>" viewBox="<?php echo esc_attr($g[$right . '_vb']); ?>" width="<?php echo esc_attr($g[$right . '_w']); ?>" height="100%" preserveAspectRatio="xMaxYMid meet" focusable="false">
      <path class="pv-chip__fill" d="<?php echo esc_attr($g[$right . '_fill']); ?>"/>
      <path class="pv-chip__shape" fill="none" d="<?php echo esc_attr($g[$right . '_stroke']); ?>"/>
    </svg>
  </span>
    <?php
}

/** Work page category filter — strip-positioned ticket chip. */
function cb_preview_filter($key, $label, $active = false, $pos = 'solo')
{
    $class = 'pv-chip pv-filter pv-chip--' . $pos . ($active ? ' is-active' : '');
    ?>
<button type="button" class="<?php echo esc_attr($class); ?>" role="tab" aria-selected="<?php echo $active ? 'true' : 'false'; ?>" data-filter="<?php echo esc_attr($key); ?>"<?php echo $key !== 'all' ? ' id="' . esc_attr($key) . '"' : ''; ?>>
  <?php cb_preview_chip_shell($pos); ?>
  <span class="pv-chip__text"><?php echo esc_html(strtoupper($label)); ?></span>
</button>
    <?php
}

/** Work page search — underline field with loop icon. */
function cb_preview_search($placeholder = 'Search titles…')
{
    ?>
<label class="pv-search">
  <span class="pv-search__label">Search</span>
  <input class="pv-search__input" type="search" placeholder="<?php echo esc_attr($placeholder); ?>" data-pv-search autocomplete="off">
  <svg class="pv-search__icon" viewBox="0 0 16 16" width="16" height="16" aria-hidden="true" focusable="false">
    <circle cx="6.75" cy="6.75" r="4.25" fill="none" stroke="currentColor" stroke-width="1"/>
    <path d="M10.1 10.1 14 14" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
  </svg>
</label>
    <?php
}

/** A24-style text-swap hover link markup. */
function cb_preview_hover_link($href, $label, $class = '', $active = false)
{
    $class = trim('pv-hover ' . $class . ($active ? ' is-active' : ''));
    $extra = cb_external_link_attrs($href);
    ?>
<a class="<?php echo esc_attr($class); ?>" href="<?php echo esc_url($href); ?>" data-hover<?php echo $extra ? ' ' . $extra : ''; ?>>
  <span class="pv-hover__inner" data-text="<?php echo esc_attr($label); ?>">
    <span class="pv-hover__text"><?php echo esc_html($label); ?></span>
  </span>
</a>
    <?php
}

function cb_preview_project_url($slug)
{
    return cb_preview_url('all-projects/' . $slug);
}

/** Display title for preview (no parenthetical subtitle). */
function cb_preview_project_title($project)
{
    return trim(preg_replace('/\s*\([^)]*\)\s*$/u', '', $project['title']));
}

/**
 * Split a title into lines for ruled typography (rules sit between lines).
 * Short titles stay on one line; longer ones break near the middle for balance
 * (e.g. "La cigale" / "et la fourmi", not "… et la" / "fourmi").
 */
function cb_preview_title_lines($title)
{
    $title = trim(preg_replace('/\s*\([^)]*\)\s*$/u', '', preg_replace('/\s+/u', ' ', $title)));
    if ($title === '') {
        return array('');
    }
    $words = preg_split('/\s+/u', $title);
    $n = count($words);
    // One line when short enough to sit as a single title beat
    // ("Die Kälte der Erde" ~19 stays one line; longer titles still split)
    if ($n <= 2 || mb_strlen($title) <= 20) {
        return array($title);
    }

    $best = null;
    $best_score = PHP_INT_MAX;
    for ($i = 1; $i < $n; $i++) {
        $line1 = implode(' ', array_slice($words, 0, $i));
        $line2 = implode(' ', array_slice($words, $i));
        $l1 = mb_strlen($line1);
        $l2 = mb_strlen($line2);
        $score = abs($l1 - $l2);
        // Prefer a break near the middle of the word count
        $score += abs($i - ($n / 2)) * 1.5;
        // Avoid a lone first/last word on titles with 4+ words
        if ($n >= 4) {
            if ($i === 1) {
                $score += 10;
            }
            if ($i === $n - 1) {
                $score += 14;
            }
        }
        if ($score < $best_score) {
            $best_score = $score;
            $best = array($line1, $line2);
        }
    }
    return $best ? $best : array($title);
}

/** Subtitle/tagline for preview, skipping text already in the title. */
function cb_preview_project_sub($project)
{
    $title = mb_strtolower(cb_preview_project_title($project));
    $parts = array();
    foreach (array($project['subtitle'], $project['tagline']) as $bit) {
        $bit = trim((string) $bit);
        if ($bit === '' || in_array(mb_strtolower($bit), array('trailer'), true)) {
            continue;
        }
        if (mb_strpos($title, mb_strtolower($bit)) !== false) {
            continue;
        }
        $parts[] = $bit;
    }
    return implode(' · ', $parts);
}

/** Map credit line role → A24-style uppercase label. */
function cb_preview_credit_label($role)
{
    $role = trim($role);
    $map = array(
        'script'              => 'Written by',
        'screenplay'          => 'Written by',
        'writer'              => 'Written by',
        'written by'          => 'Written by',
        'director'            => 'Directed by',
        'directed by'         => 'Directed by',
        'dop'                 => 'Cinematography',
        'dop/ cinematography' => 'Cinematography',
        'dop / cinematography'=> 'Cinematography',
        'cinematography'      => 'Cinematography',
        'camera'              => 'Cinematography',
        'editing'             => 'Edited by',
        'editor'              => 'Edited by',
        'cut'                 => 'Edited by',
        'music'               => 'Music',
        'composer'            => 'Music',
        'production'          => 'Production',
        'production company'  => 'Production',
        'producer'            => 'Produced by',
        'produced by'         => 'Produced by',
        'post production'     => 'Post production',
        'commissioning editor'=> 'Commissioning editor',
        'starring'            => 'Starring',
        'cast'                => 'Starring',
        'costume design'      => 'Costume design',
        'production design'   => 'Production design',
    );
    $key = strtolower(preg_replace('/\s+/', ' ', $role));
    return isset($map[$key]) ? $map[$key] : $role;
}

/** Parse project credits into [label, names] for lined A24 blocks. */
function cb_preview_credits($project)
{
    $out = array();
    foreach ((array) $project['credits'] as $line) {
        $parts = explode(':', $line, 2);
        if (count($parts) < 2) {
            continue;
        }
        $out[] = array(
            'label' => cb_preview_credit_label($parts[0]),
            'names' => trim($parts[1]),
        );
    }
    return $out;
}

/**
 * Split a credit value into display lines.
 * Returns list of [text, is_note] — notes are "based on…" style lines (no trailing comma).
 */
function cb_preview_credit_name_lines($names)
{
    $names = trim($names);
    $lines = array();
    if (preg_match('/^(.*?)(,\s*)?(based on\b.*)$/iu', $names, $m)) {
        $people = trim($m[1], " \t,");
        if ($people !== '') {
            foreach (preg_split('/\s*,\s*/', $people) as $person) {
                if ($person !== '') {
                    $lines[] = array($person, false);
                }
            }
        }
        $lines[] = array(trim($m[3]), true);
        return $lines;
    }
    foreach (preg_split('/\s*,\s*/', $names) as $person) {
        if ($person !== '') {
            $lines[] = array($person, false);
        }
    }
    return $lines;
}

/**
 * Turn freeform project details into A24 left/right meta rows.
 * Parses pipe- (or " / ") separated strings like:
 * "Germany 2018 | Mysterydrama | 96 min | Shooting format: … | NDR, FFHSH."
 */
function cb_preview_meta_rows($project)
{
    $site = cb_site();
    $cat = isset($site['categories'][$project['category']])
        ? $site['categories'][$project['category']]['label']
        : $project['category'];
    $details = trim((string) $project['details']);
    $year = cb_project_year($project);

    $origin = '';
    $runtime = '';
    $genre = '';
    $format = '';
    $lenses = '';
    $production = '';
    $extras = array();

    $parts = array();
    if ($details !== '') {
        if (strpos($details, '|') !== false) {
            $parts = preg_split('/\s*\|\s*/', $details);
        } else {
            $parts = preg_split('/\s+\/\s+/', $details);
        }
        $parts = array_values(array_filter(array_map('trim', $parts), function ($p) {
            return $p !== '';
        }));
    }

    $n = count($parts);
    foreach ($parts as $i => $part) {
        $part = trim($part, " \t.");
        if ($part === '') {
            continue;
        }
        if (preg_match('/^shooting\s*format\s*:?\s*(.+)$/iu', $part, $m)) {
            $format = trim($m[1]);
            continue;
        }
        if (preg_match('/^lenses?\s*:?\s*(.+)$/iu', $part, $m)) {
            $lenses = trim($m[1]);
            continue;
        }
        if (preg_match('/^(\d+\s*[x×]\s*)?\d+(?:[.,]\d+)?\s*min\.?$/iu', $part)) {
            $runtime = preg_replace('/\s+/', ' ', $part);
            continue;
        }
        if (
            $origin === ''
            && (
                preg_match('/^(Germany|Austria|Cuba|Tanzania|Tansznia|Spain|France|Italy)\b/iu', $part)
                || ($i === 0 && preg_match('/\b(19|20)\d{2}\b/', $part))
            )
        ) {
            $origin = $part;
            continue;
        }
        // Production / partners (often the last segment)
        if (
            $i === $n - 1
            && !preg_match('/min/i', $part)
            && preg_match('/,|NDR|ARD|ZDF|ORF|FFHSH|Nordmedia|Arte|WDR|Degeto|3SAT|Filmstiftung|SWR|Radio Bremen/i', $part)
        ) {
            $production = $part;
            continue;
        }
        if ($genre === '') {
            $genre = $part;
            continue;
        }
        $extras[] = $part;
    }

    // Runtime fallback from free text (e.g. "3x 1.30min" still inside a longer part)
    if ($runtime === '' && preg_match('/\b(\d+\s*[x×]\s*)?\d+(?:[.,]\d+)?\s*min\b/iu', $details, $m)) {
        $runtime = trim($m[0]);
    }

    $rows = array();
    if ($year) {
        $rows[] = array('Year', $year);
    }
    $rows[] = array('Category', $cat);
    if ($runtime !== '') {
        $rows[] = array('Runtime', $runtime);
    }
    if ($origin !== '') {
        $rows[] = array('Origin', $origin);
    }
    if ($genre !== '' && strcasecmp($genre, $cat) !== 0) {
        $label = preg_match('/\b(TV|series|feature|Imagefilm|Commercial|Music|Webfilm|Documentary)\b/i', $genre)
            ? 'Type'
            : 'Genre';
        $rows[] = array($label, $genre);
    }
    if ($format !== '') {
        $rows[] = array('Format', $format);
    }
    if ($lenses !== '') {
        $rows[] = array('Lenses', $lenses);
    }
    if ($production !== '') {
        $rows[] = array('Production', $production);
    }
    foreach ($extras as $extra) {
        $rows[] = array('Detail', $extra);
    }

    return $rows;
}

function cb_preview_dispatch($subpath)
{
    $subpath = trim($subpath, '/');
    header('X-Robots-Tag: noindex, nofollow', true);

    if ($subpath === '') {
        return cb_preview_render('home');
    }

    $pages = array(
        'about'     => 'about',
        'portfolio' => 'portfolio',
        'contact'   => 'contact',
    );
    if (isset($pages[$subpath])) {
        return cb_preview_render($pages[$subpath]);
    }

    if (preg_match('#^all-projects/([a-z0-9-]+)$#', $subpath, $m)) {
        $project = cb_project($m[1]);
        if ($project && empty($project['draft'])) {
            return cb_preview_render('project', array('project' => $project));
        }
    }

    return cb_preview_render('home', array('status' => 404));
}

function cb_preview_canonical($template, $vars = array())
{
    if ($template === 'home') {
        return cb_preview_url('');
    }
    if ($template === 'project' && !empty($vars['project']['slug'])) {
        return cb_preview_url('all-projects/' . $vars['project']['slug']);
    }
    return cb_preview_url($template);
}

function cb_preview_render($template, $vars = array())
{
    $page = array_merge(array(
        'template'   => $template,
        'status'     => 200,
        'is_preview' => true,
    ), $vars);

    $meta = cb_page_meta($page);
    $meta['robots'] = 'noindex, nofollow';
    $meta['canonical'] = cb_preview_canonical($template, $vars);
    if ($page['status'] === 404) {
        $meta['title'] = 'Page not found – ' . cb_site('name');
        $meta['description'] = 'This redesign preview page could not be found.';
    }

    $page = array_merge($page, $meta);

    if (function_exists('status_header')) {
        status_header($page['status']);
    } else {
        http_response_code($page['status']);
    }
    header('Content-Type: text/html; charset=utf-8');
    include cb_theme_dir() . '/templates/preview/layout.php';
}
