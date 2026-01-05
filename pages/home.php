<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/site.php';
require_once __DIR__ . '/../includes/functions.php';

$site_name = setting('site_name','DesertSafariGo');
$site_tagline_small = setting('site_tagline_small','Dubai experiences • instant booking');
$seo_title = setting('seo_home_title','DesertSafariGo • Dubai Desert Safari & Dhow Cruise');
$seo_description = setting('seo_home_desc','Book premium Dubai experiences with fast pickup, transparent packages, and instant support.');

$hero_title = setting('hero_title','Dubai Desert Safari — Chic, Fast, Verified');
$hero_subtitle = setting('hero_subtitle','Instant booking. Pickup on time. Transparent packages. Real support. Designed to convert — especially on mobile.');
$hero_bg_url = setting('hero_bg_url', url('assets/img/placeholder.jpg'));

$primary_cta_text = setting('primary_cta_text','Book now');
$primary_cta_link = '#lead';

$phone = setting('contact_phone','+971 50 000 0000');
$email = setting('contact_email','hello@desertsafarigo.com');
$whatsapp = setting('contact_whatsapp','+971500000000');

$wa_icon_file = __DIR__ . '/../assets/img/whatsapp-icon.png';
$wa_icon_url = url('assets/img/whatsapp-icon.png') . '?v=' . (string)@filemtime($wa_icon_file);

$footer_bg_url = setting('footer_bg_url', url('assets/img/placeholder.jpg'));

$packages = all("SELECT * FROM packages WHERE is_active=1 ORDER BY sort_order ASC, id DESC LIMIT 12");
$highlights = all("SELECT * FROM highlights WHERE is_active=1 ORDER BY sort_order ASC, id DESC LIMIT 12");
$testimonials = all("SELECT * FROM testimonials WHERE is_active=1 ORDER BY sort_order ASC, id DESC LIMIT 12");
$why = all("SELECT * FROM whyus WHERE is_active=1 ORDER BY sort_order ASC, id DESC LIMIT 12");
$posts = all("SELECT id, title, slug, excerpt, cover_url, published_at FROM blog_posts WHERE status='published' ORDER BY published_at DESC, id DESC LIMIT 6");

$toastItems = all("SELECT title, subtitle FROM toast_items WHERE is_active=1 ORDER BY id DESC LIMIT 12");

require __DIR__ . '/../partials/head.php';
?>

<section class="hero" id="top">
  <div class="heroBg" style="background-image:url('<?= e($hero_bg_url) ?>')"></div>
  <div class="container">
    <div class="grid">
      <div>
        <div class="badge"><?= e(setting('hero_badge','Dubai • Premium Bookings')) ?></div>
        <h1 class="hTitle"><?= e($hero_title) ?></h1>
        <p class="hSub"><?= e($hero_subtitle) ?></p>
        <div class="hActions">
          <a class="btn primary" href="#lead"><?= e(setting('hero_cta_primary','Get instant quote')) ?></a>
          <a class="btn ghost" href="#packages"><?= e(setting('hero_cta_secondary','View packages')) ?></a>
        </div>

        <div style="margin-top:20px; display:flex; gap:10px; flex-wrap:wrap">
          <span class="badge"><?= e(setting('usp_1','Free pickup')) ?></span>
          <span class="badge"><?= e(setting('usp_2','No hidden charges')) ?></span>
          <span class="badge"><?= e(setting('usp_3','Best time slots')) ?></span>
          <span class="badge"><?= e(setting('usp_4','WhatsApp support')) ?></span>
        </div>
      </div>

      <div class="glass formCard" id="lead">
        <h3><?= e(setting('form_title','Quick Booking Form')) ?></h3>
        <form id="leadForm" method="post" action="<?= e(url('lead-submit')) ?>">
          <div class="formRow">
            <input class="input" name="full_name" placeholder="Full name *" required>
            <input class="input" name="phone" placeholder="Phone / WhatsApp *" required>
          </div>
          <div class="formRow" style="margin-top:10px">
            <input class="input" name="email" placeholder="Email (optional)">
            <select class="input" name="package_name">
              <option value=""><?= e(setting('form_package_placeholder','Select package')) ?></option>
              <?php foreach($packages as $p): ?>
                <option value="<?= e($p['name']) ?>"><?= e($p['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="formRow" style="margin-top:10px">
            <input class="input" type="date" name="trip_date" placeholder="Trip date">
            <input class="input" name="persons" placeholder="Persons">
          </div>
          <div class="formRow" style="margin-top:10px">
            <select class="input" name="contact_pref">
              <option value="phone">Call me</option>
              <option value="whatsapp">WhatsApp me</option>
              <option value="email">Email me</option>
            </select>
            <input class="input" name="message" placeholder="Message (optional)">
          </div>
          <div class="formFoot">
            <button class="btn primary" type="submit"><?= e(setting('form_submit','Submit')) ?></button>
            <div class="note"><?= e(setting('form_note','Takes 10 seconds • Response within minutes')) ?></div>
          </div>
        </form>

        <script>
          (function(){
            const form = document.getElementById('leadForm');
            if(!form) return;
            form.addEventListener('submit', async (e)=>{
              e.preventDefault();
              const btn = form.querySelector('button[type="submit"]');
              const old = btn.textContent;
              btn.textContent = 'Sending...';
              btn.disabled = true;
              try{
                const res = await fetch(form.action, {method:'POST', body: new FormData(form)});
                const data = await res.json();
                alert(data.message || (data.ok ? 'Submitted' : 'Error'));
                if(data.ok) form.reset();
              }catch(err){
                alert('Please try again.');
              }finally{
                btn.textContent = old;
                btn.disabled = false;
              }
            });
          })();
        </script>

        <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap; align-items:center">
          <span class="badge"><?= e(setting('trust_1','Secure & private')) ?></span>
          <span class="badge"><?= e(setting('trust_2','Real human support')) ?></span>
          <span class="badge"><?= e(setting('trust_3','Best value options')) ?></span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section" id="highlights">
  <div class="container">
    <h2><?= e(setting('highlights_title','Latest in Dubai — Experiences people love')) ?></h2>
    <p class="lead"><?= e(setting('highlights_sub','Swipe to explore trending experiences, best time slots, and premium add-ons.')) ?></p>

    <div class="carousel" data-carousel>
      <?php if(!$highlights): ?>
        <?php for($i=0;$i<6;$i++): ?>
          <div class="card slide">
            <div class="img" style="background-image:url('<?= e(url('assets/img/placeholder.jpg')) ?>')"></div>
            <h3>Premium desert vibes</h3><p>Golden-hour dunes, cinematic views, and smooth pickup.</p>
          </div>
        <?php endfor; ?>
      <?php else: ?>
        <?php foreach($highlights as $h): ?>
          <div class="card slide">
            <div class="img" style="background-image:url('<?= e($h['image_url'] ?: url('assets/img/placeholder.jpg')) ?>')"></div>
            <h3><?= e($h['title']) ?></h3>
            <p><?= e($h['subtitle']) ?></p>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section" id="packages">
  <div class="container">
    <h2><?= e(setting('packages_title','Our Packages')) ?></h2>
    <p class="lead"><?= e(setting('packages_sub','Clear pricing, premium inclusions, and fast confirmation.')) ?></p>

    <div class="cards">
      <?php if(!$packages): ?>
        <?php for($i=0;$i<6;$i++): ?>
          <div class="card">
            <div class="img" style="background-image:url('<?= e(url('assets/img/placeholder.jpg')) ?>')"></div>
            <h3>Evening Desert Safari</h3>
            <p>Pickup • Dune bashing • BBQ dinner • Live shows</p>
            <div style="margin-top:10px; display:flex; align-items:center; justify-content:space-between">
              <span class="badge">From AED 149</span>
              <div style="display:flex; gap:10px; align-items:center">
                <a class="waRipple js-wa" href="<?= e('https://wa.me/'.preg_replace('/\D+/', '', $whatsapp).'?text='.urlencode('Hi! I want to book: Evening Desert Safari. Please share details.')) ?>" target="_blank" aria-label="WhatsApp" data-wa-source="package">
                  <img src="<?= e($wa_icon_url) ?>" alt="" aria-hidden="true">
                </a>
                <a class="btn primary" href="#lead">Book now</a>
              </div>
            </div>
          </div>
        <?php endfor; ?>
      <?php else: ?>
        <?php foreach($packages as $p): ?>
          <div class="card">
            <div class="img" style="background-image:url('<?= e($p['image_url'] ?: url('assets/img/placeholder.jpg')) ?>')"></div>
            <h3><?= e($p['name']) ?></h3>
            <p><?= e($p['short_desc']) ?></p>
            <div style="margin-top:10px; display:flex; align-items:center; justify-content:space-between">
              <span class="badge"><?= e($p['price_badge'] ?: 'Best value') ?></span>
              <div style="display:flex; gap:10px; align-items:center">
                <a class="waRipple js-wa" href="<?= e('https://wa.me/'.preg_replace('/\D+/', '', $whatsapp).'?text='.urlencode('Hi! I want to book: '.$p['name'].'. Please share details and pickup options.')) ?>" target="_blank" aria-label="WhatsApp" data-wa-source="package">
                  <img src="<?= e($wa_icon_url) ?>" alt="" aria-hidden="true">
                </a>
                <a class="btn primary" href="#lead" onclick="document.querySelector('select[name=package_name]').value='<?= e($p['name']) ?>'">Book now</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section" id="why">
  <div class="container">
    <div class="parallax" style="background-image:url('<?= e(setting('why_bg_url', url('assets/img/placeholder.jpg'))) ?>')">
      <div class="inner">
        <h2><?= e(setting('why_title','Why book with us?')) ?></h2>
        <p class="lead" style="max-width:70ch"><?= e(setting('why_sub','Because Dubai is premium — your booking experience should be too. We optimize everything for comfort, trust, and speed.')) ?></p>
        <div class="cards" style="margin-top:14px">
          <?php if(!$why): ?>
            <div class="card"><div class="img" style="background-image:url('<?= e(url('assets/img/placeholder.jpg')) ?>')"></div><h3>On-time pickup</h3><p>Transparent timings and proactive confirmation.</p></div>
            <div class="card"><div class="img" style="background-image:url('<?= e(url('assets/img/placeholder.jpg')) ?>')"></div><h3>Premium selections</h3><p>Packages curated for value and vibe.</p></div>
            <div class="card"><div class="img" style="background-image:url('<?= e(url('assets/img/placeholder.jpg')) ?>')"></div><h3>Fast support</h3><p>WhatsApp-first, human responses.</p></div>
          <?php else: ?>
            <?php foreach($why as $w): ?>
              <div class="card">
                <div class="img" style="background-image:url('<?= e($w['image_url'] ?: url('assets/img/placeholder.jpg')) ?>')"></div>
                <h3><?= e($w['title']) ?></h3>
                <p><?= e($w['subtitle']) ?></p>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div style="margin-top:18px; display:flex; gap:10px; flex-wrap:wrap">
          <span class="badge" style="background:rgba(255,255,255,.18); color:#fff; border:1px solid rgba(255,255,255,.22)"><?= e(setting('why_badge_1','Licensed partners')) ?></span>
          <span class="badge" style="background:rgba(255,255,255,.18); color:#fff; border:1px solid rgba(255,255,255,.22)"><?= e(setting('why_badge_2','Tourist-friendly')) ?></span>
          <span class="badge" style="background:rgba(255,255,255,.18); color:#fff; border:1px solid rgba(255,255,255,.22)"><?= e(setting('why_badge_3','Instant confirmation')) ?></span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section" id="reviews">
  <div class="container">
    <h2><?= e(setting('testi_title','What customers say')) ?></h2>
    <p class="lead"><?= e(setting('testi_sub','Real feedback — because trust matters.')) ?></p>

    <div class="testi">
      <?php if(!$testimonials): ?>
        <?php for($i=0;$i<6;$i++): ?>
          <div class="card">
            <div class="quote">“Pickup was on time, the vibe was premium, and everything felt smooth. This is how Dubai should be booked.”</div>
            <div class="person"><div class="avatar"></div><div><div style="font-weight:900">Ayesha</div><div class="postMeta">Evening Safari</div></div></div>
          </div>
        <?php endfor; ?>
      <?php else: ?>
        <?php foreach($testimonials as $t): ?>
          <div class="card">
            <div class="quote">“<?= e($t['quote']) ?>”</div>
            <div class="person"><div class="avatar"></div><div><div style="font-weight:900"><?= e($t['name']) ?></div><div class="postMeta"><?= e($t['meta']) ?></div></div></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section" id="blog">
  <div class="container">
    <h2><?= e(setting('blog_title','Dubai Travel Blog')) ?></h2>
    <p class="lead"><?= e(setting('blog_sub','SEO-ready guides and booking tips.')) ?></p>

    <div class="blogGrid">
      <?php if(!$posts): ?>
        <?php for($i=0;$i<6;$i++): ?>
          <div class="card">
            <div class="img" style="background-image:url('<?= e(url('assets/img/placeholder.jpg')) ?>')"></div>
            <div class="postMeta">Guide • <?= date('M d, Y') ?></div>
            <div class="postTitle">Best time for desert safari in Dubai</div>
            <div class="postExcerpt">Weather, timing, and what to expect — written to help you book with confidence.</div>
            <div style="margin-top:10px"><a class="btn" href="<?= e(url('blog')) ?>">Read</a></div>
          </div>
        <?php endfor; ?>
      <?php else: ?>
        <?php foreach($posts as $p): ?>
          <div class="card">
            <div class="img" style="background-image:url('<?= e($p['cover_url'] ?: url('assets/img/placeholder.jpg')) ?>')"></div>
            <div class="postMeta">Guide • <?= e(date('M d, Y', strtotime($p['published_at'] ?: 'now'))) ?></div>
            <div class="postTitle"><?= e($p['title']) ?></div>
            <div class="postExcerpt"><?= e($p['excerpt']) ?></div>
            <div style="margin-top:10px"><a class="btn" href="<?= e(url('blog/'.$p['slug'])) ?>">Read</a></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div style="margin-top:14px">
      <a class="btn primary" href="<?= e(url('blog')) ?>">View all posts</a>
    </div>
  </div>
</section>

<div class="fab">
  <div class="glass weatherCard" data-weather>
    <div><div class="t">Dubai • Today</div><div class="v">--°C</div></div>
    <div class="badge">Live</div>
  </div>
</div>

<a class="waFloating js-wa" href="<?= e(setting('chat_link','https://wa.me/'.preg_replace('/\D+/', '', $whatsapp))) ?>" target="_blank" rel="noopener" aria-label="Book Now on WhatsApp" data-wa-source="floating">
  <img src="<?= e($wa_icon_url) ?>" alt="WhatsApp">
</a>

<div class="toast" data-toast>
  <div class="glass inner">
    <div class="title" data-toast-title>Recently booked</div>
    <div class="sub" data-toast-sub>—</div>
  </div>
</div>
<script type="application/json" data-toast-items><?= json_encode(array_map(fn($x)=>['title'=>$x['title'],'subtitle'=>$x['subtitle']], $toastItems)) ?></script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
