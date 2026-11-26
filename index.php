<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$c    = require __DIR__ . '/includes/content.php';
$site = config('site');
$news = get_news(4);
$csrf = csrf_token();

/** Responsive image with automatic fallback to the small size if the full-size file is missing. */
function img(array $i, string $alt, string $class = '', bool $eager = false): string
{
    return sprintf(
        '<img src="%s" data-fallback="%s" alt="%s" class="%s" loading="%s" decoding="async">',
        e($i['src']), e($i['thumb'] ?? ''), e($alt), e($class), $eager ? 'eager' : 'lazy'
    );
}

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type'    => 'NGO',
    'name'     => $site['name'],
    'url'      => $site['url'],
    'logo'     => $c['logo'],
    'email'    => $site['email'],
    'telephone'=> $site['phone'],
    'slogan'   => 'Our Community, Our Nature, Our Future',
    'address'  => ['@type' => 'PostalAddress', 'streetAddress' => $c['contact']['address'], 'addressCountry' => 'KE'],
    'sameAs'   => array_column($c['social'], 1),
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mt. Elgon-ICLIP | Community-led conservation on Mt. Elgon</title>
  <meta name="description" content="Mt. Elgon-ICLIP champions community-led conservation, water access, sustainable livelihoods and indigenous knowledge across the Mt. Elgon ecosystem.">
  <meta name="theme-color" content="#12291D">
  <meta name="csrf-token" content="<?= e($csrf) ?>">
  <link rel="canonical" href="<?= e($site['url']) ?>/">
  <link rel="icon" href="<?= e($c['logo']) ?>">

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?= e($site['name']) ?>">
  <meta property="og:title" content="Mt. Elgon-ICLIP | Our Community, Our Nature, Our Future">
  <meta property="og:description" content="Community-led conservation, water access and sustainable livelihoods on Mt. Elgon.">
  <meta property="og:image" content="<?= e($c['images']['hero']['src']) ?>">
  <meta name="twitter:card" content="summary_large_image">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wdth,wght@12..96,75..100,300..800&family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>

<header class="site-header" id="siteHeader">
  <div class="wrap bar">
    <a class="brand" href="/" aria-label="<?= e($site['name']) ?> home">
      <img src="<?= e($c['logo']) ?>" alt="" width="44" height="44">
      <span>Mt. Elgon-ICLIP</span>
    </a>
    <nav class="primary" id="primaryNav" aria-label="Main">
      <ul>
        <?php foreach ($c['nav'] as [$label, $href]): ?>
          <li><a href="<?= e($href) ?>"><?= e($label) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <a class="btn btn-solid nav-cta" href="#involve">Get involved</a>
    </nav>
    <button class="menu-btn" id="menuBtn" aria-expanded="false" aria-controls="primaryNav">
      <span class="sr">Menu</span><i></i><i></i>
    </button>
  </div>
</header>

<!-- Elevation gauge: page progress as a climb up the mountain, doubles as section navigation -->
<nav class="gauge" id="gauge" aria-label="Sections" data-tone="light">
  <ol>
    <li><a href="#top" data-target="top"><span>Home</span></a></li>
    <li><a href="#vision" data-target="vision"><span>Vision and mission</span></a></li>
    <li><a href="#projects" data-target="projects"><span>Projects</span></a></li>
    <li><a href="#pillars" data-target="pillars"><span>Strategic pillars</span></a></li>
    <li><a href="#news" data-target="news"><span>News</span></a></li>
    <li><a href="#involve" data-target="involve"><span>Get involved</span></a></li>
  </ol>
  <div class="gauge-line"><b class="gauge-marker" id="gaugeMarker"></b></div>
  <output class="gauge-value" id="gaugeValue" aria-hidden="true">2,000 m</output>
</nav>

<main id="main">

  <!-- HERO -->
  <section class="hero" id="top" data-tone="light">
    <canvas id="contours" aria-hidden="true"></canvas>
    <div class="wrap hero-grid">
      <h1 class="hero-title">
        <span class="step"><span>Our Community,</span></span>
        <span class="step"><span>Our Nature,</span></span>
        <span class="step"><span>Our Future.</span></span>
      </h1>

      <div class="hero-copy">
        <p class="lede">A community-led organisation protecting the Mt. Elgon ecosystem: restoring forests, safeguarding indigenous knowledge and building livelihoods that let people and nature thrive together.</p>
        <div class="actions">
          <a class="btn btn-solid" href="#projects">See our work</a>
          <a class="btn btn-line" href="#involve">Work with us</a>
        </div>
      </div>

      <figure class="hero-media">
        <?= img($c['images']['hero'], 'Mt. Elgon rising above the landscape', '', true) ?>
        <figcaption>Mt. Elgon, on the Kenya–Uganda border, rises to 4,321 m.</figcaption>
      </figure>
    </div>

    <ul class="wrap strap" aria-label="What we stand for">
      <?php foreach ($c['strapline'] as $s): ?><li><?= e($s) ?></li><?php endforeach; ?>
    </ul>
  </section>

  <!-- VISION & MISSION -->
  <section class="sec vm" id="vision" data-tone="light">
    <div class="wrap vm-grid">
      <article class="vm-block vm-vision">
        <h2>Our vision</h2>
        <p class="statement"><?= e($c['vision']) ?></p>
        <?= img($c['images']['vision'], 'Landscape on the slopes of Mt. Elgon') ?>
      </article>
      <article class="vm-block vm-mission">
        <h2>Our mission</h2>
        <p class="statement small"><?= e($c['mission']) ?></p>
        <?= img($c['images']['mission'], 'Community members working on Mt. Elgon') ?>
      </article>
    </div>

    <div class="wrap values">
      <h3 class="sr">Our values</h3>
      <?php foreach ($c['values'] as [$t, $d]): ?>
        <div class="value">
          <h4><?= e($t) ?></h4>
          <p><?= e($d) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- PROJECTS -->
  <section class="sec projects" id="projects" data-tone="light">
    <div class="wrap">
      <header class="sec-head">
        <h2>Four ways we restore the mountain</h2>
        <p>We deliver community-led conservation that restores landscapes, protects natural resources and strengthens sustainable livelihoods.</p>
      </header>

      <div class="tabs" data-tabs>
        <div class="tablist" role="tablist" aria-label="Project areas" aria-orientation="vertical">
          <?php foreach ($c['projects'] as $i => $p): ?>
            <button role="tab" id="tab-<?= e($p['id']) ?>" aria-controls="panel-<?= e($p['id']) ?>"
                    aria-selected="<?= $i === 0 ? 'true' : 'false' ?>" tabindex="<?= $i === 0 ? '0' : '-1' ?>">
              <strong><?= e($p['title']) ?></strong>
              <span><?= e($p['short']) ?></span>
            </button>
          <?php endforeach; ?>
        </div>

        <?php foreach ($c['projects'] as $i => $p): ?>
          <div class="panel" role="tabpanel" id="panel-<?= e($p['id']) ?>" aria-labelledby="tab-<?= e($p['id']) ?>" <?= $i === 0 ? '' : 'hidden' ?>>
            <?= img($p['img'], $p['title']) ?>
            <div class="panel-body">
              <h3><?= e($p['title']) ?></h3>
              <p><?= e($p['text']) ?></p>
              <?php if ($p['focus']): ?>
                <ul class="focus">
                  <?php foreach ($p['focus'] as $f): ?><li><?= e($f) ?></li><?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- PILLARS -->
  <section class="sec pillars" id="pillars" data-tone="dark">
    <div class="wrap">
      <header class="sec-head">
        <h2>Five pillars for one mountain</h2>
        <p><?= e($c['pillars_intro']) ?></p>
      </header>
      <ul class="pillar-list">
        <?php foreach ($c['pillars'] as $i => [$sw, $en]): ?>
          <li style="--h: <?= 38 + $i * 12 ?>%">
            <h3><?= e($sw) ?></h3>
            <p><?= e($en) ?></p>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- NEWS -->
  <section class="sec news" id="news" data-tone="light">
    <div class="wrap">
      <header class="sec-head">
        <h2>News and updates</h2>
        <p>Stories and milestones from the community and the mountain.</p>
      </header>
      <div class="news-grid">
        <?php foreach ($news as $i => $n): ?>
          <article class="story <?= $i === 0 ? 'story-lead' : '' ?>">
            <a href="<?= e($n['url']) ?>">
              <div class="story-img">
                <?php if (!empty($n['image_url'])): ?>
                  <img src="<?= e($n['image_url']) ?>" alt="" loading="lazy" decoding="async">
                <?php endif; ?>
              </div>
              <div class="story-body">
                <p class="meta"><?= e($n['category']) ?> · <time datetime="<?= e($n['published_at']) ?>"><?= e(format_date($n['published_at'])) ?></time></p>
                <h3><?= e($n['title']) ?></h3>
                <?php if (!empty($n['excerpt']) && $i === 0): ?><p class="excerpt"><?= e($n['excerpt']) ?></p><?php endif; ?>
              </div>
            </a>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="more"><a class="link" href="/news-updates/">All news and updates</a></p>
    </div>
  </section>

  <!-- GET INVOLVED -->
  <section class="sec involve" id="involve" data-tone="dark">
    <div class="wrap involve-grid">
      <div class="involve-intro">
        <h2>Help keep Mt. Elgon standing</h2>
        <p>Choose how you want to take part. We reply to every message by email.</p>
        <fieldset class="ways" form="inquiryForm">
          <legend class="sr">How would you like to get involved?</legend>
          <?php foreach ($c['involve'] as $i => [$val, $label, $desc]): ?>
            <label class="way">
              <input type="radio" name="interest" value="<?= e($val) ?>" form="inquiryForm" <?= $i === 0 ? 'checked' : '' ?>>
              <span class="way-text"><strong><?= e($label) ?></strong><small><?= e($desc) ?></small></span>
            </label>
          <?php endforeach; ?>
        </fieldset>
      </div>

      <form class="form" id="inquiryForm" action="api/inquiry.php" method="post" novalidate>
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <div class="hp" aria-hidden="true"><label>Leave this empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

        <div class="field">
          <label for="f-name">Your name</label>
          <input id="f-name" name="name" type="text" autocomplete="name" required maxlength="120">
          <small class="err" data-err="name"></small>
        </div>
        <div class="field">
          <label for="f-email">Email address</label>
          <input id="f-email" name="email" type="email" autocomplete="email" required maxlength="190">
          <small class="err" data-err="email"></small>
        </div>
        <div class="field">
          <label for="f-org">Organisation <span class="opt">(optional)</span></label>
          <input id="f-org" name="organisation" type="text" autocomplete="organization" maxlength="160">
        </div>
        <div class="field">
          <label for="f-msg">Message</label>
          <textarea id="f-msg" name="message" rows="5" required minlength="10" maxlength="2000"></textarea>
          <small class="err" data-err="message"></small>
        </div>
        <button class="btn btn-lichen" type="submit">Send message</button>
        <p class="status" role="status" aria-live="polite" id="inquiryStatus"></p>
      </form>
    </div>
  </section>
</main>

<footer class="site-footer" data-tone="dark">
  <div class="wrap foot-grid">
    <div class="foot-brand">
      <img src="<?= e($c['logo']) ?>" alt="" width="56" height="56">
      <p class="foot-tag">Our Community. Our Nature. Our Future.</p>
    </div>

    <address class="foot-col">
      <h3>Contact</h3>
      <a href="<?= e($c['contact']['map']) ?>" rel="noopener"><?= e($c['contact']['address']) ?></a>
      <a href="mailto:<?= e($site['email']) ?>"><?= e($site['email']) ?></a>
      <a href="tel:<?= e($site['phone']) ?>"><?= e($site['phone_ui']) ?></a>
    </address>

    <div class="foot-col">
      <h3>Follow</h3>
      <?php foreach ($c['social'] as [$label, $url]): ?>
        <a href="<?= e($url) ?>" rel="noopener" target="_blank"><?= e($label) ?></a>
      <?php endforeach; ?>
    </div>

    <form class="foot-news" id="newsletterForm" action="api/newsletter.php" method="post" novalidate>
      <h3><label for="n-email">Get our updates</label></h3>
      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
      <div class="hp" aria-hidden="true"><label>Leave this empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
      <div class="inline">
        <input id="n-email" name="email" type="email" placeholder="Email address" autocomplete="email" required>
        <button class="btn btn-lichen" type="submit">Subscribe</button>
      </div>
      <p class="status" role="status" aria-live="polite" id="newsletterStatus"></p>
    </form>
  </div>

  <div class="wrap legal">
    <p>© <span id="year"><?= date('Y') ?></span> Mt. Elgon-ICLIP. All rights reserved.</p>
    <p><a href="/privacy-policy/">Privacy policy</a></p>
  </div>
</footer>

<script src="assets/js/main.js" defer></script>
</body>
</html>
