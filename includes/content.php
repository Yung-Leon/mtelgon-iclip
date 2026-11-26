<?php
declare(strict_types=1);

/**
 * Editable page content. Copy comes from the existing mtelgon-iclip.org homepage.
 * Images point at the current WordPress uploads; swap 'src' for local files in /assets/img when ready.
 * 'thumb' is the small WordPress size, used automatically if a full-size file fails to load.
 */
$wp = 'https://mtelgon-iclip.org/wp-content/uploads/';

return [
    'logo' => $wp . '2026/04/Final-Mt-Elgon-ICLIP-Logo-2_page-0001-Photoroom-300x300.png',

    'nav' => [
        ['About us',       '/about-us/'],
        ['What we do',     '/projects-2/'],
        ['Resources',      '/resources/'],
        ['News & updates', '/news-updates/'],
    ],

    'strapline' => [
        'Community-led conservation',
        'Rooted in nature',
        'Sustainable livelihoods',
        'Climate resilience',
    ],

    'images' => [
        'hero'    => ['src' => $wp . '2026/04/Mount-Elgon.jpg',  'thumb' => $wp . '2026/04/Mount-Elgon-300x200.jpg'],
        'vision'  => ['src' => $wp . '2026/04/DSC_5095.jpg',     'thumb' => $wp . '2026/04/DSC_5095-300x200.jpg'],
        'mission' => ['src' => $wp . '2026/03/DSC_5098.jpg',     'thumb' => $wp . '2026/03/DSC_5098-300x200.jpg'],
    ],

    'vision'  => 'A healthy Mt. Elgon ecosystem driving socio-cultural, environmental and economic transformation.',
    'mission' => 'To champion integrated community-led natural resource management in the Mt. Elgon ecosystem through environmental conservation, water access for all, sustainable livelihoods, capacity building and indigenous knowledge protection.',

    'values' => [
        ['Community ownership',      'A community-driven approach through inclusive participation and mutual benefit.'],
        ['Accountability and integrity', 'Ethics and transparency as our way of working, within the organisation and beyond it.'],
        ['Sustainability',           'Innovative approaches that balance conservation with the socio-economic needs of surrounding communities.'],
    ],

    'projects' => [
        [
            'id'    => 'forest',
            'title' => 'Forest restoration and landscape rehabilitation',
            'short' => 'Trees, rivers and biodiversity',
            'text'  => 'We restore degraded forests through planting indigenous trees, assisted natural regeneration and community-led conservation.',
            'focus' => ['Indigenous tree planting', 'Forest restoration', 'Riparian rehabilitation', 'Biodiversity conservation'],
            'img'   => ['src' => $wp . '2026/07/DSC_0284.jpg', 'thumb' => $wp . '2026/07/DSC_0284-300x200.jpg'],
        ],
        [
            'id'    => 'heritage',
            'title' => 'Cultural heritage and indigenous knowledge',
            'short' => 'Traditions that protect the mountain',
            'text'  => 'Mt. Elgon is deeply rooted in the wisdom, traditions and cultural heritage of its people. We preserve the indigenous knowledge and cultural practices that promote environmental stewardship, while celebrating the identity and diversity of local communities.',
            'focus' => [],
            'img'   => ['src' => $wp . '2026/07/IMG_1891.jpg', 'thumb' => $wp . '2026/07/IMG_1891-300x169.jpg'],
        ],
        [
            'id'    => 'livelihoods',
            'title' => 'Sustainable livelihoods',
            'short' => 'Income that eases pressure on nature',
            'text'  => 'We support environmentally sustainable enterprises that improve household incomes while reducing pressure on natural resources.',
            'focus' => ['Community tree nurseries', 'Agroforestry', 'Bee keeping', 'Green enterprise development'],
            'img'   => ['src' => $wp . '2026/07/IMG_1979.jpg', 'thumb' => $wp . '2026/07/IMG_1979-300x206.jpg'],
        ],
        [
            'id'    => 'education',
            'title' => 'Environmental education and awareness',
            'short' => 'Schools, campaigns and training',
            'text'  => 'We inspire environmental stewardship through education, advocacy and public engagement.',
            'focus' => ['School environmental programmes', 'Conservation campaigns', 'Environmental roadshows', 'Community training'],
            'img'   => ['src' => $wp . '2026/07/FB_IMG_1779970822269.jpg', 'thumb' => $wp . '2026/07/FB_IMG_1779970822269-300x200.jpg'],
        ],
    ],

    'pillars_intro' => 'Our strategic objectives are anchored in the 17 Sustainable Development Goals and shaped into five pillars.',
    'pillars' => [
        ['Mazingira Dhabiti',  'Championing environmental conservation and restoration'],
        ['Masomo Maarifa',     'Capacity building, civic education and awareness programmes'],
        ['Ustawi Kwa Wote',    'Promoting inclusive livelihoods improvement'],
        ['Utamaduni Kiasili',  'Identifying and protecting community heritage, indigenous knowledge and natural resources'],
        ['Maji Kwa Wote',      'Advocating for the protection of water sources and access to water for all'],
    ],

    'involve' => [
        ['volunteer', 'Volunteer',      'Give your time and skills to restoration and community programmes.'],
        ['partner',   'Partner',        'Work with us on joint conservation and livelihood projects.'],
        ['donate',    'Donate',         'Fund forest restoration, water access and community enterprises.'],
        ['tree',      'Sponsor a tree', 'Back indigenous trees planted and cared for by local communities.'],
        ['csr',       'Corporate CSR',  'Connect your company’s sustainability goals to work on the mountain.'],
    ],

    'contact' => [
        'address'  => 'KWFT Centre, 3rd Floor, Kiambere, Masaba Road, 00100',
        'map'      => 'https://www.google.com/maps/search/?api=1&query=KWFT+Centre%2C+3rd+Floor%2C+Kiambere%2C+Masaba+Road%2C+00100',
    ],

    'social' => [
        ['Facebook',  'https://www.facebook.com/profile.php?id=61589376999416'],
        ['Instagram', 'https://www.instagram.com/mt.elgoniclip/'],
        ['LinkedIn',  'https://www.linkedin.com/in/mt-elgon-iclip-aa239b423'],
    ],

    // Shown if the database is unavailable or the news table is empty.
    'fallback_news' => [
        [
            'title'        => 'H.E Dr. William Samoei Ruto, President of the Republic of Kenya, launches the Mt. Elgon-ICLIP Strategic Plan (2026–2036)',
            'category'     => 'Strategic plan',
            'excerpt'      => 'The ten-year plan sets the direction for community-led conservation, water, livelihoods and indigenous knowledge on Mt. Elgon.',
            'image_url'    => $wp . '2026/11/Banner-2.png',
            'url'          => 'https://mtelgon-iclip.org/h-e-dr-william-samoei-ruto-president-of-the-republic-of-kenya-launches-the-mt-elgon-iclips-10-year-strategic-plan-2026-2036/',
            'published_at' => '2026-11-01',
        ],
        [
            'title'        => 'Investing in forests is investing in people and the future of the Mt. Elgon ecosystem',
            'category'     => 'Forests',
            'excerpt'      => 'Why restoring the mountain’s forests is also an investment in the households that depend on them.',
            'image_url'    => $wp . '2026/08/IMG-20260807-WA0017.jpg',
            'url'          => 'https://mtelgon-iclip.org/investing-in-forests-is-investing-in-people-and-future-of-mt-elgon-ecosystem/',
            'published_at' => '2026-08-07',
        ],
    ],
];
