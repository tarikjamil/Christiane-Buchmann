<?php
/**
 * Titles, meta descriptions, structured data (schema.org), robots.txt, sitemap.xml and llms.txt.
 */

function cb_page_meta($page)
{
    $site = cb_site();
    $suffix = ' – ' . $site['name'] . ', ' . $site['job_title'];
    $meta = array(
        'title'       => $site['full_name'] . ' – ' . $site['job_title'] . ' (Hamburg / Frankfurt)',
        'description' => $site['description'],
        'canonical'   => cb_url('/'),
        'image'       => cb_asset('images/about-me_retouched.jpg'),
        'robots'      => 'index, follow, max-image-preview:large',
        'og_type'     => 'website',
    );

    switch ($page['template']) {
        case 'about':
            $meta['title'] = 'About' . $suffix;
            $meta['description'] = 'Biography, awards and festival recognitions of Christiane Buchmann, BVK – German cinematographer trained at Hamburg Media School, EICTV Cuba and Berlinale Talents.';
            $meta['canonical'] = cb_url('about/');
            $meta['image'] = cb_asset('images/about.jpg');
            $meta['og_type'] = 'profile';
            break;
        case 'portfolio':
            $meta['title'] = 'Work: Narrative, Music Videos, Commercials, Documentaries' . $suffix;
            $meta['description'] = 'Selected work by cinematographer Christiane Buchmann: TV series and feature films (Tatort, SOKO Linz, Ein Fall für Zwei), music videos, commercials and documentaries.';
            $meta['canonical'] = cb_url('portfolio/');
            $meta['image'] = cb_asset('images/Feature-bearbeitet.jpg');
            break;
        case 'contact':
            $meta['title'] = 'Contact & Agency' . $suffix;
            $meta['description'] = 'Contact Christiane Buchmann, BVK, Director of Photography in Hamburg / Frankfurt. Represented by ' . $site['agency']['name'] . ' (' . $site['agency']['agent'] . ').';
            $meta['canonical'] = cb_url('contact/');
            break;
        case 'privacy':
            $meta['title'] = 'GDPR Privacy Notice – ' . $site['name'];
            $meta['description'] = 'Privacy notice for christianebuchmann.com.';
            $meta['canonical'] = cb_url('gdpr-privacy-notice/');
            break;
        case 'project':
            $p = $page['project'];
            $name = cb_project_name($p);
            $meta['title'] = $name . $suffix;
            $intro = $name . ' – cinematography by Christiane Buchmann, BVK.';
            $meta['description'] = cb_excerpt($intro . ' ' . ($p['details'] ? $p['details'] : $p['description']), 160);
            $meta['canonical'] = cb_project_url($p['slug']);
            $meta['image'] = $p['image'] ? cb_upload($p['image']) : $meta['image'];
            $meta['og_type'] = 'video.other';
            if (!empty($p['draft'])) {
                $meta['robots'] = 'noindex, nofollow';
            }
            break;
        case '404':
            $meta['title'] = 'Page not found – ' . $site['name'];
            $meta['robots'] = 'noindex, follow';
            break;
    }
    $meta['schema'] = cb_schema($page, $meta);
    return $meta;
}

function cb_project_name($p)
{
    if (!empty($p['name'])) {
        return $p['name'];
    }
    $title = cb_title_case($p['title']);
    if ($p['subtitle'] && !in_array(strtolower($p['subtitle']), array('trailer'), true)) {
        $title .= ' (' . cb_title_case($p['subtitle']) . ')';
    }
    return $title;
}

function cb_title_case($text)
{
    if (!function_exists('mb_strtoupper') || $text !== mb_strtoupper($text) || !preg_match('/[A-Z]{3}/u', $text)) {
        return $text;
    }
    $small = array('der', 'die', 'das', 'den', 'dem', 'des', 'für', 'an', 'in', 'im', 'von', 'mit', 'und', 'et', 'la', 'le', 'de', 'of', 'the', 'and');
    $words = explode(' ', mb_convert_case(mb_strtolower($text), MB_CASE_TITLE));
    foreach ($words as $i => $word) {
        if ($i > 0 && in_array(mb_strtolower($word), $small, true)) {
            $words[$i] = mb_strtolower($word);
        }
    }
    return implode(' ', $words);
}

function cb_person_schema()
{
    $site = cb_site();
    $same_as = array($site['agency']['url']);
    foreach (array_merge($site['networks'], $site['social']) as $link) {
        $same_as[] = $link['url'];
    }
    return array(
        '@type'           => 'Person',
        '@id'             => cb_url('/#person'),
        'name'            => $site['name'],
        'honorificSuffix' => 'BVK',
        'jobTitle'        => $site['job_title'],
        'description'     => $site['description'],
        'url'             => cb_url('/'),
        'image'           => cb_asset('images/bio.jpg'),
        'email'           => 'mailto:' . $site['email'],
        'telephone'       => $site['phone'],
        'nationality'     => array('@type' => 'Country', 'name' => 'Germany'),
        'homeLocation'    => array('@type' => 'Place', 'name' => 'Hamburg, Germany'),
        'workLocation'    => array(
            array('@type' => 'Place', 'name' => 'Hamburg, Germany'),
            array('@type' => 'Place', 'name' => 'Frankfurt am Main, Germany'),
        ),
        'knowsLanguage'   => $site['languages'],
        'knowsAbout'      => array('Cinematography', 'Feature film', 'TV series', 'Documentary film', 'Music videos', 'Commercials'),
        'hasOccupation'   => array('@type' => 'Occupation', 'name' => 'Cinematographer', 'alternateName' => array('Director of Photography', 'DoP', 'Kamerafrau', 'Bildgestalterin')),
        'alumniOf'        => array(
            array('@type' => 'CollegeOrUniversity', 'name' => 'Hamburg Media School'),
            array('@type' => 'CollegeOrUniversity', 'name' => 'Fachhochschule Dortmund'),
            array('@type' => 'CollegeOrUniversity', 'name' => 'Escuela Internacional de Cine y Televisión (EICTV)'),
        ),
        'memberOf'        => array(
            array('@type' => 'Organization', 'name' => 'BVK – Berufsverband Kinematografie (German Society of Cinematographers)', 'url' => 'https://kinematografie.org/'),
            array('@type' => 'Organization', 'name' => 'Cinematographinnen – Women Cinematographers Network', 'url' => 'https://cinematographinnen.net/'),
        ),
        'contactPoint'    => array(
            array(
                '@type'       => 'ContactPoint',
                'contactType' => 'agency',
                'name'        => $site['agency']['name'] . ' – ' . $site['agency']['agent'],
                'telephone'   => $site['agency']['phone'],
                'email'       => $site['agency']['email'],
                'url'         => $site['agency']['url'],
            ),
            array(
                '@type'       => 'ContactPoint',
                'contactType' => 'direct',
                'telephone'   => $site['phone'],
                'email'       => $site['email'],
            ),
        ),
        'sameAs'          => $same_as,
    );
}

function cb_project_schema($p, $meta)
{
    $series = preg_match('/series/i', $p['details'] . ' ' . $p['subtitle']);
    $types = array(
        'narrative'   => $series ? 'TVSeries' : 'Movie',
        'documentary' => $series ? 'TVSeries' : 'Movie',
        'music-video' => 'MusicVideoObject',
        'commercial'  => 'CreativeWork',
    );
    $genres = array('narrative' => 'Drama', 'documentary' => 'Documentary', 'music-video' => 'Music video', 'commercial' => 'Commercial');
    $work = array(
        '@type'       => $types[$p['category']],
        '@id'         => $meta['canonical'] . '#work',
        'name'        => cb_project_name($p),
        'url'         => $meta['canonical'],
        'genre'       => $genres[$p['category']],
        'description' => $p['description'] ? $p['description'] : $p['details'],
        'image'       => $meta['image'],
        'contributor' => array(
            '@type'       => 'Role',
            'roleName'    => cb_credit($p, 'B- Camera') ? 'B-Camera Operator' : 'Director of Photography',
            'contributor' => array('@id' => cb_url('/#person')),
        ),
    );
    $year = cb_project_year($p);
    if ($year) {
        $work['dateCreated'] = $year;
    }
    $director = cb_credit($p, 'Director');
    if ($director) {
        $work['director'] = array();
        foreach (preg_split('/\s*(?:,|&amp;|&| and )\s*/', $director) as $name) {
            if ($name !== '') {
                $work['director'][] = array('@type' => 'Person', 'name' => $name);
            }
        }
    }
    if ($p['crew_united']) {
        $work['sameAs'] = $p['crew_united'];
    }
    if ($p['videos']) {
        $work['trailer'] = array(
            '@type'        => 'VideoObject',
            'name'         => cb_project_name($p) . ' – Trailer',
            'embedUrl'     => cb_video_embed_url($p['videos'][0]),
            'thumbnailUrl' => $meta['image'],
            'description'  => $work['description'],
        );
    }
    return $work;
}

function cb_schema($page, $meta)
{
    $site = cb_site();
    $graph = array(
        array(
            '@type'      => 'WebSite',
            '@id'        => cb_url('/#website'),
            'url'        => cb_url('/'),
            'name'       => $site['full_name'],
            'inLanguage' => 'en',
            'publisher'  => array('@id' => cb_url('/#person')),
        ),
        cb_person_schema(),
    );
    $webpage = array(
        '@type'      => 'WebPage',
        '@id'        => $meta['canonical'] . '#webpage',
        'url'        => $meta['canonical'],
        'name'       => $meta['title'],
        'description'=> $meta['description'],
        'isPartOf'   => array('@id' => cb_url('/#website')),
        'about'      => array('@id' => cb_url('/#person')),
        'inLanguage' => 'en',
    );
    if ($page['template'] === 'about') {
        $webpage['@type'] = 'ProfilePage';
        $webpage['mainEntity'] = array('@id' => cb_url('/#person'));
    } elseif ($page['template'] === 'contact') {
        $webpage['@type'] = 'ContactPage';
    } elseif ($page['template'] === 'portfolio') {
        $webpage['@type'] = 'CollectionPage';
        $items = array();
        foreach (cb_projects() as $i => $p) {
            $items[] = array('@type' => 'ListItem', 'position' => $i + 1, 'url' => cb_project_url($p['slug']), 'name' => cb_project_name($p));
        }
        $webpage['mainEntity'] = array('@type' => 'ItemList', 'name' => 'Filmography of Christiane Buchmann', 'itemListElement' => $items);
    } elseif ($page['template'] === 'project') {
        $graph[] = cb_project_schema($page['project'], $meta);
        $webpage['mainEntity'] = array('@id' => $meta['canonical'] . '#work');
        $webpage['breadcrumb'] = array(
            '@type' => 'BreadcrumbList',
            'itemListElement' => array(
                array('@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => cb_url('/')),
                array('@type' => 'ListItem', 'position' => 2, 'name' => 'Work', 'item' => cb_url('portfolio/')),
                array('@type' => 'ListItem', 'position' => 3, 'name' => cb_project_name($page['project'])),
            ),
        );
    }
    if ($page['template'] !== '404') {
        $graph[] = $webpage;
    }
    return array('@context' => 'https://schema.org', '@graph' => $graph);
}

function cb_render_robots()
{
    header('Content-Type: text/plain; charset=utf-8');
    echo "User-agent: *\n";
    echo "Disallow: /wp-admin/\n";
    echo "Allow: /wp-admin/admin-ajax.php\n\n";
    echo 'Sitemap: ' . cb_url('sitemap.xml') . "\n";
}

function cb_render_sitemap()
{
    header('Content-Type: application/xml; charset=utf-8');
    $theme_updated = gmdate('Y-m-d', filemtime(cb_theme_dir() . '/inc/projects.php'));
    $urls = array('', 'about/', 'portfolio/', 'contact/');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
    foreach ($urls as $u) {
        echo '  <url><loc>' . esc_url(cb_url($u)) . '</loc><lastmod>' . $theme_updated . "</lastmod></url>\n";
    }
    foreach (cb_projects() as $p) {
        echo '  <url><loc>' . esc_url(cb_project_url($p['slug'])) . '</loc><lastmod>' . $theme_updated . '</lastmod>';
        if ($p['image']) {
            echo '<image:image><image:loc>' . esc_url(cb_upload($p['image'])) . '</image:loc></image:image>';
        }
        echo "</url>\n";
    }
    echo "</urlset>\n";
}

function cb_render_llms()
{
    header('Content-Type: text/plain; charset=utf-8');
    $site = cb_site();
    $a = $site['agency'];
    echo '# ' . $site['full_name'] . "\n\n";
    echo '> ' . $site['description'] . "\n\n";
    echo "- Role: Director of Photography (cinematographer), member of the BVK (German Society of Cinematographers)\n";
    echo '- Based in: ' . implode(' and ', $site['locations']) . ", Germany\n";
    echo "- Languages: German, English, French, Spanish\n";
    echo '- Agency: ' . $a['name'] . ', agent ' . $a['agent'] . ', ' . $a['phone_label'] . ', ' . $a['email'] . ' (' . $a['url'] . ")\n";
    echo '- Direct contact: ' . $site['email'] . ', ' . $site['phone_label'] . "\n\n";
    echo "## Pages\n\n";
    echo '- [About](' . cb_url('about/') . "): biography, education, awards and festival recognitions\n";
    echo '- [Work](' . cb_url('portfolio/') . "): all projects by category\n";
    echo '- [Contact](' . cb_url('contact/') . "): agency and direct contact\n\n";
    foreach ($site['categories'] as $key => $cat) {
        $list = cb_projects_in($key);
        if (!$list) {
            continue;
        }
        echo '## ' . $cat['label'] . "\n\n";
        foreach ($list as $p) {
            $line = '- [' . cb_project_name($p) . '](' . cb_project_url($p['slug']) . ')';
            if ($p['details']) {
                $line .= ': ' . $p['details'];
            }
            $director = cb_credit($p, 'Director');
            if ($director) {
                $line .= ' | Director: ' . html_entity_decode($director, ENT_QUOTES, 'UTF-8');
            }
            echo $line . "\n";
        }
        echo "\n";
    }
    echo "## Profiles\n\n";
    foreach ($site['networks'] as $n) {
        echo '- [' . $n['label'] . '](' . $n['url'] . ")\n";
    }
}
