<?php
require_once 'includes/config.php';
$seo = [
    'title'       => 'Welcome',
    'description' => htmlspecialchars(get_setting('site_name','CAC Achievers House')) . ' — ' . htmlspecialchars(get_setting('site_tagline','Where Faith Meets Destiny')) . '. Join us for life-changing worship, Bible study, and vibrant church community.',
    'url'         => 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/cac/index.php',
];
include 'includes/header.php';

// Fetch latest 3 sermons
$sermons_result = $conn->query("SELECT * FROM sermons ORDER BY sermon_date DESC LIMIT 3");

// Fetch upcoming events
$events_result = $conn->query("SELECT * FROM events WHERE status='upcoming' OR status='planning' ORDER BY start_date ASC LIMIT 3");

// Fetch active testimonials
$testimonials_result = $conn->query("SELECT * FROM testimonials WHERE is_active=1 ORDER BY sort_order ASC LIMIT 10");
$testimonials_data = [];
if ($testimonials_result && $testimonials_result->num_rows > 0) {
    while ($t = $testimonials_result->fetch_assoc()) {
        $testimonials_data[] = $t;
    }
}
?>
<body>
<?php include 'includes/site-header.php'; ?>

<!-- ============================================================
     HERO — Premium Cinematic
     ============================================================ -->
<section class="hero" id="home" aria-label="Welcome to CAC Achievers House">
    <div class="hero-bg-overlay"></div>
    <div class="hero-glow"></div>
    <div class="hero-grain"></div>

    <div class="hero-content">
        <div class="hero-center">

            <div class="hero-label">
                <span class="hero-label-dot"></span>
                Welcome to Church
            </div>

            <h1>
                Welcome to<br>
                <span class="hero-church-name">CAC Achievers House</span><br>
                <span class="hero-location">Derby</span>
            </h1>

            <p>A place where Christ is exalted and lives are transformed. We are delighted to have you worship with us in an atmosphere of prayer, praise, and God's presence.</p>

            <div class="hero-actions">
                <a href="#services" class="hero-btn-primary">
                    Join Us Sunday <i class="bx bx-right-arrow-alt"></i>
                </a>
                <a href="sermons.php" class="hero-btn-ghost">
                    <i class="bx bx-play-circle"></i> Watch Sermons
                </a>
            </div>

        </div>
    </div>

    <div class="hero-scroll" aria-hidden="true">
        <div class="hero-scroll-line"></div>
        <span>Scroll</span>
    </div>
</section>

<!-- ============================================================
     SERVICE TIMES
     ============================================================ -->
<section class="service-times" id="services" aria-labelledby="service-heading">
    <div class="service-container">

        <span class="section-badge">
            <i class="bx bx-time"></i> OUR SCHEDULE
        </span>
        <h2 class="section-title" id="service-heading">Service Times</h2>
        <p class="section-subtitle">
            Join us for worship, fellowship, and spiritual growth throughout the week
        </p>

        <div class="service-grid">

            <div class="service-card primary reveal">
                <div class="icon-box-1">
                    <i class="bx bx-sun"></i>
                </div>
                <h3>Sunday Worship</h3>
                <span class="time">10:00 AM – 12:00 PM</span>
                <p>Powerful worship, inspiring messages, and fellowship with believers. The highlight of the week.</p>
            </div>

            <div class="service-card light reveal reveal-delay-1">
                <div class="icon-box-1">
                    <i class="bx bx-heart"></i>
                </div>
                <h3>Monday Achievers Pray</h3>
                <span class="time accent">Monday · 8:30 PM – 9:30 PM</span>
                <p>Corporate prayer and intercession for personal and communal breakthrough with the body of Christ.</p>
            </div>

            <div class="service-card dark reveal reveal-delay-2">
                <div class="icon-box-1">
                    <i class="bx bx-book-open"></i>
                </div>
                <h3>Wednesday Bible Study</h3>
                <span class="time accent">Wednesday · 7:00 PM – 8:10 PM</span>
                <p>Dive deeper into God's Word with interactive study sessions and transformative discussions.</p>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     ABOUT PREVIEW — Editorial split layout
     ============================================================ -->
<section class="about-strip" aria-labelledby="about-heading">
    <div class="about-strip-grid container">

        <div class="about-strip-image reveal">
            <img src="assets/images/join-us.jpg"
                 alt="CAC Achievers House congregation in worship"
                 loading="lazy">
        </div>

        <div class="about-strip-body reveal reveal-delay-1">
            <span class="section-badge"><i class="bx bx-info-circle"></i> WHO WE ARE</span>
            <h2 class="section-title" id="about-heading">
                A Community of<br>Faith and <span>Purpose</span>
            </h2>
            <p class="section-subtitle">
                Experience a purpose-driven church where faith, love, and a passion for Christ thrives. Together, we grow in God's Word, encourage one another, and build lasting relationships and impact generations.
            </p>

            <div class="value-list">
                <div class="value-item">
                    <div class="value-icon"><i class="bx bx-bible"></i></div>
                    <div>
                        <h4>Word-Centred Teaching</h4>
                        <p>Every message rooted in Scripture, relevant to real life</p>
                    </div>
                </div>
                <div class="value-item">
                    <div class="value-icon"><i class="bx bx-group"></i></div>
                    <div>
                        <h4>Vibrant Community</h4>
                        <p>A family that prays, grows, and serves together</p>
                    </div>
                </div>
                <div class="value-item">
                    <div class="value-icon"><i class="bx bx-world"></i></div>
                    <div>
                        <h4>Kingdom Impact</h4>
                        <p>Reaching our community and the nations with the Gospel</p>
                    </div>
                </div>
            </div>

            <a href="about-us.php" class="btn-primary">
                Our Story <i class="bx bx-right-arrow-alt"></i>
            </a>
        </div>

    </div>
</section>

<!-- ============================================================
     SERMONS PREVIEW — Dark navy section
     ============================================================ -->
<section class="sermons-section" id="sermons" aria-labelledby="sermons-heading">
    <div class="container" style="text-align:center;position:relative;z-index:1;">

        <span class="section-badge">
            <i class="bx bx-headphone"></i> RECENT SERMONS
        </span>
        <h2 class="section-title" id="sermons-heading">
            Listen. Learn. <span style="color:var(--accent-gold);font-style:italic;">Grow.</span>
        </h2>
        <p class="section-subtitle" style="color:rgba(255,255,255,0.6);margin-bottom:3rem;">
            Every sermon is a word in season. Catch up on our latest messages anytime, anywhere.
        </p>

        <div class="sermons-grid" style="max-width:var(--max-w);margin:0 auto 3rem;">
            <?php if ($sermons_result && $sermons_result->num_rows > 0):
                while ($s = $sermons_result->fetch_assoc()): ?>
            <div class="sermon-card reveal">
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:0.4rem;">
                    <div class="sermon-play">
                        <i class="bx bx-play"></i>
                    </div>
                    <span class="sermon-series"><?= htmlspecialchars($s['series'] ?? 'Sunday Message') ?></span>
                </div>
                <h3 class="sermon-title"><?= htmlspecialchars($s['title']) ?></h3>
                <div class="sermon-meta">
                    <span><i class="bx bx-user"></i> <?= htmlspecialchars($s['speaker']) ?></span>
                    <span><i class="bx bx-calendar"></i> <?= date('M j, Y', strtotime($s['sermon_date'])) ?></span>
                </div>
                <?php if ($s['scripture']): ?>
                <div class="sermon-scripture"><?= htmlspecialchars($s['scripture']) ?></div>
                <?php endif; ?>
            </div>
            <?php endwhile; else: ?>
            <!-- Placeholder sermon cards when DB is empty -->
            <div class="sermon-card reveal">
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:0.4rem;">
                    <div class="sermon-play"><i class="bx bx-play"></i></div>
                    <span class="sermon-series">Faith Series</span>
                </div>
                <h3 class="sermon-title">Walking in the Fullness of God's Promise</h3>
                <div class="sermon-meta">
                    <span><i class="bx bx-user"></i> Senior Pastor</span>
                    <span><i class="bx bx-calendar"></i> Jun 1, 2026</span>
                </div>
                <div class="sermon-scripture">Jeremiah 29:11</div>
            </div>
            <div class="sermon-card reveal reveal-delay-1">
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:0.4rem;">
                    <div class="sermon-play"><i class="bx bx-play"></i></div>
                    <span class="sermon-series">Destiny Series</span>
                </div>
                <h3 class="sermon-title">Purpose Is Not An Accident — It's a Divine Assignment</h3>
                <div class="sermon-meta">
                    <span><i class="bx bx-user"></i> Senior Pastor</span>
                    <span><i class="bx bx-calendar"></i> May 25, 2026</span>
                </div>
                <div class="sermon-scripture">Romans 8:28</div>
            </div>
            <div class="sermon-card reveal reveal-delay-2">
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:0.4rem;">
                    <div class="sermon-play"><i class="bx bx-play"></i></div>
                    <span class="sermon-series">Prayer Series</span>
                </div>
                <h3 class="sermon-title">Praying With Authority — The Power of a Believer's Voice</h3>
                <div class="sermon-meta">
                    <span><i class="bx bx-user"></i> Senior Pastor</span>
                    <span><i class="bx bx-calendar"></i> May 18, 2026</span>
                </div>
                <div class="sermon-scripture">Matthew 21:22</div>
            </div>
            <?php endif; ?>
        </div>

        <a href="sermons.php" class="btn-gold">
            View All Sermons <i class="bx bx-right-arrow-alt"></i>
        </a>

    </div>
</section>

<!-- ============================================================
     MINISTRIES
     ============================================================ -->
<section class="ministries" id="ministries" aria-labelledby="ministries-heading">
    <div class="ministries-container">

        <div class="ministries-header reveal">
            <div class="header-left">
                <span class="section-badge">
                    <i class="bx bx-crown"></i> WHAT WE OFFER
                </span>
                <h2 class="section-title" id="ministries-heading">
                    Our <span>Ministries</span>
                </h2>
            </div>
            <p class="header-right">
                We offer diverse ministries designed to nurture spiritual growth, build community,
                and empower believers of all ages to fulfil their God-given purpose.
            </p>
        </div>

        <div class="ministries-grid">

            <div class="ministry-card reveal">
                <div class="image-box">
                    <img src="https://images.unsplash.com/photo-1515169067865-5387ec356754?w=600&q=80"
                         alt="Youth Ministry" loading="lazy">
                    <span class="age-badge">Ages 13–25</span>
                </div>
                <h3>Youth Ministry</h3>
                <p>Empowering young people to discover their identity in Christ through dynamic worship and relevant teachings.</p>
            </div>

            <div class="ministry-card primary reveal reveal-delay-1">
                <div class="icon-box" style="background:rgba(255,255,255,0.08);font-size:3.5rem;color:rgba(255,255,255,0.8);">
                    <i class="bx bx-child"></i>
                </div>
                <span class="age-badge dark">Ages 3–12</span>
                <h3>Children's Church</h3>
                <p>Building strong foundations of faith in children through engaging Bible stories and a loving environment.</p>
            </div>

            <div class="ministry-card reveal reveal-delay-2">
                <div class="image-box">
                    <img src="assets/images/women-fellowship.jpg"
                         alt="Women's Fellowship" loading="lazy">
                    <span class="age-badge">All Ages</span>
                </div>
                <h3>Women's Fellowship</h3>
                <p>A nurturing community where women grow spiritually, build lasting friendships, and encourage one another.</p>
            </div>

        </div>

        <div style="text-align:center;margin-top:3rem;" class="reveal">
            <a href="ministries.php" class="btn-primary">
                Explore All Ministries <i class="bx bx-right-arrow-alt"></i>
            </a>
        </div>

    </div>
</section>

<!-- ============================================================
     EVENTS
     ============================================================ -->
<section class="events" id="events" aria-labelledby="events-heading">
    <div class="events-container">

        <span class="section-badge">
            <i class="bx bx-calendar-event"></i> WHAT'S HAPPENING
        </span>
        <h2 class="section-title" id="events-heading">Upcoming Events</h2>
        <p class="section-subtitle">
            Join us for these special gatherings and experience God's presence
        </p>

        <?php
        // Re-fetch into array so we can use freely
        $events_list = [];
        if ($events_result && $events_result->num_rows > 0) {
            $events_result->data_seek(0);
            while ($ev = $events_result->fetch_assoc()) {
                $events_list[] = $ev;
            }
        }
        $featured_event = $events_list[0] ?? null;
        ?>

        <?php if (empty($events_list)): ?>
        <!-- ── EMPTY STATE ── -->
        <div class="events-empty reveal">
            <div class="events-empty-icon">
                <i class="bx bx-calendar-x"></i>
            </div>
            <h3>No Upcoming Events</h3>
            <p>We're planning something amazing. Check back soon or follow us on social media for announcements.</p>
            <a href="<?= htmlspecialchars(get_setting('facebook_url','#')) ?>" class="btn-navy" style="margin-top:1rem;">
                <i class="bx bxl-facebook"></i> Follow Us for Updates
            </a>
        </div>

        <?php else: ?>
        <div class="events-grid">

            <!-- Featured Event -->
            <div class="event-featured reveal">
                <?php
                $feat_img = '';
                if (!empty($featured_event['image']) && file_exists(__DIR__ . '/assets/events/' . $featured_event['image'])) {
                    $feat_img = 'assets/events/' . htmlspecialchars($featured_event['image']);
                } elseif (!empty($featured_event['image'])) {
                    $feat_img = htmlspecialchars($featured_event['image']);
                }
                ?>
                <?php if ($feat_img): ?>
                <img src="<?= $feat_img ?>" alt="<?= htmlspecialchars($featured_event['title']) ?>" loading="lazy">
                <?php else: ?>
                <div class="event-featured-placeholder">
                    <i class="bx bx-calendar-star"></i>
                </div>
                <?php endif; ?>

                <div class="featured-overlay">
                    <div class="featured-badges">
                        <span class="event-status-badge <?= htmlspecialchars($featured_event['status']) ?>">
                            <?= ucfirst($featured_event['status']) ?>
                        </span>
                        <?php if ($featured_event['event_type']): ?>
                        <span class="event-type-badge"><?= htmlspecialchars($featured_event['event_type']) ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="event-date">
                        <i class="bx bx-calendar"></i>
                        <?= date('l, M j, Y', strtotime($featured_event['start_date'])) ?>
                        <?php if ($featured_event['start_time']): ?>
                            &nbsp;·&nbsp; <?= date('g:i A', strtotime($featured_event['start_time'])) ?>
                        <?php endif; ?>
                    </span>
                    <h3><?= htmlspecialchars($featured_event['title']) ?></h3>
                    <?php if ($featured_event['description']): ?>
                    <p><?= htmlspecialchars(substr($featured_event['description'], 0, 130)) ?>...</p>
                    <?php endif; ?>
                    <?php if ($featured_event['venue_name']): ?>
                    <div class="event-venue">
                        <i class="bx bx-map-pin"></i>
                        <?= htmlspecialchars($featured_event['venue_name']) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Event List -->
            <div class="event-list">
                <?php foreach (array_slice($events_list, 1, 3) as $i => $ev):
                    $style = $i % 2 === 0 ? 'primary' : 'light';
                    $delay = $i > 0 ? ' reveal-delay-' . $i : '';
                ?>
                <div class="event-card <?= $style ?> reveal<?= $delay ?>">
                    <?php
                    $card_img = '';
                    if (!empty($ev['image'])) {
                        $card_img = file_exists(__DIR__ . '/assets/events/' . $ev['image'])
                            ? 'assets/events/' . htmlspecialchars($ev['image'])
                            : htmlspecialchars($ev['image']);
                    }
                    ?>
                    <?php if ($card_img): ?>
                    <div class="event-card-thumb">
                        <img src="<?= $card_img ?>" alt="<?= htmlspecialchars($ev['title']) ?>" loading="lazy">
                    </div>
                    <?php endif; ?>
                    <div class="event-card-body">
                        <span class="event-date">
                            <i class="bx bx-calendar"></i>
                            <?= date('M j, Y', strtotime($ev['start_date'])) ?>
                            <?php if ($ev['start_time']): ?>
                                · <?= date('g:i A', strtotime($ev['start_time'])) ?>
                            <?php endif; ?>
                        </span>
                        <h4><?= htmlspecialchars($ev['title']) ?></h4>
                        <?php if ($ev['description']): ?>
                        <p><?= htmlspecialchars(substr($ev['description'], 0, 90)) ?>…</p>
                        <?php endif; ?>
                        <?php if ($ev['venue_name']): ?>
                        <span class="event-card-venue">
                            <i class="bx bx-map-pin"></i> <?= htmlspecialchars($ev['venue_name']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
        <?php endif; ?>

        <div style="text-align:center;margin-top:3rem;" class="reveal">
            <a href="events.php" class="btn-navy">
                View All Events <i class="bx bx-right-arrow-alt"></i>
            </a>
        </div>
    </div>
</section>

<!-- ============================================================
     TESTIMONIALS
     ============================================================ -->
<section class="testimonials" id="testimonials" aria-labelledby="testimonials-heading">
    <div class="testimonials-container">

        <span class="section-badge">
            <i class="bx bx-comment-dots"></i> TESTIMONIALS
        </span>
        <h2 class="section-title" id="testimonials-heading">What Our Members Say</h2>

        <?php if (!empty($testimonials_data)): ?>
        <div class="rating-badge">
            ⭐ 5.0 <span>Community Rating</span>
        </div>

        <div class="testimonial-card" id="testimonialCard">
            <div class="quote-icon">"</div>
            <p class="testimonial-text" id="testimonialText">
                <?= htmlspecialchars($testimonials_data[0]['quote']) ?>
            </p>
            <div class="testimonial-author" id="testimonialAuthor">
                <?php
                $t0 = $testimonials_data[0];
                $t0_img = $t0['photo_url'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($t0['name']) . '&background=0a1f44&color=fff&size=150';
                ?>
                <img id="testimonialImg" src="<?= htmlspecialchars($t0_img) ?>" alt="<?= htmlspecialchars($t0['name']) ?>">
                <div>
                    <h4 id="testimonialName"><?= htmlspecialchars($t0['name']) ?></h4>
                    <span id="testimonialMeta"><?= htmlspecialchars($t0['role'] ?? '') ?></span>
                </div>
            </div>
            <div class="testimonial-nav" role="group" aria-label="Testimonial navigation">
                <button class="nav-btn" id="prevTestimonial" aria-label="Previous testimonial">←</button>
                <button class="nav-btn active" id="nextTestimonial" aria-label="Next testimonial">→</button>
            </div>
        </div>

        <?php else: ?>
        <!-- ── EMPTY STATE ── -->
        <div class="testimonials-empty reveal">
            <div class="testimonials-empty-icon">
                <i class="bx bx-message-square-dots"></i>
            </div>
            <h3>Testimonials Coming Soon</h3>
            <p>We're gathering stories from our incredible community. Share your testimony with us and inspire others.</p>
            <a href="contact.php" class="btn-primary" style="margin-top:1.5rem;display:inline-flex;align-items:center;gap:0.5rem;">
                <i class="bx bx-edit"></i> Share Your Story
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- ============================================================
     VISIT SECTION
     ============================================================ -->
<section class="visit-section" id="contact" aria-labelledby="visit-heading">
    <div class="visit-container">

        <div class="visit-left reveal">
            <span class="badge">
                <i class="bx bx-star"></i> NEW HERE?
            </span>
            <h1 id="visit-heading">
                Visit Us<br>
                This <span>Sunday</span>
            </h1>
            <div class="underline"></div>
        </div>

        <div class="visit-right reveal reveal-delay-1">
            <h2>Plan Your Visit</h2>
            <p>
                We would love to meet you! Whether you're new to faith or looking for a church home,
                you'll find a warm welcome here. Come as you are and experience the love of Christ
                in our community.
            </p>
            <?php $addr = get_setting('contact_address'); ?>
            <a href="<?= $addr ? 'https://maps.google.com/?q=' . urlencode($addr) : '#' ?>"
               class="btn-primary" id="directionsBtn"
               <?= $addr ? 'target="_blank" rel="noopener"' : '' ?>>
                Get Directions <i class="bx bx-map-alt"></i>
            </a>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- ============================================================
     JAVASCRIPT
     ============================================================ -->
<script>
/* ── SCROLL REVEAL ── */
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

/* ── ACTIVE NAV (scroll spy) ── */
const sections  = document.querySelectorAll('section[id]');
const navLinks  = document.querySelectorAll('nav ul li a');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(s => {
        if (window.scrollY >= s.offsetTop - 120) current = s.id;
    });
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + current ||
           (current === '' && link.getAttribute('href') === 'index.php')) {
            link.classList.add('active');
        }
    });
}, { passive: true });

/* ── TESTIMONIAL SLIDER ── */
<?php if (!empty($testimonials_data)): ?>
const testimonials = <?php
    $js_testimonials = array_map(function($t) {
        return [
            'text' => $t['quote'],
            'name' => $t['name'],
            'meta' => $t['role'] ?? '',
            'img'  => $t['photo_url'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($t['name']) . '&background=0a1f44&color=fff&size=150'
        ];
    }, $testimonials_data);
    echo json_encode($js_testimonials, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
?>;

let currentIndex = 0;
const card   = document.getElementById('testimonialCard');
const tText  = document.getElementById('testimonialText');
const tName  = document.getElementById('testimonialName');
const tMeta  = document.getElementById('testimonialMeta');
const tImg   = document.getElementById('testimonialImg');

card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';

function switchTestimonial(i) {
    card.style.opacity = '0'; card.style.transform = 'translateY(10px)';
    setTimeout(() => {
        const t = testimonials[i];
        tText.textContent = t.text; tName.textContent = t.name;
        tMeta.textContent = t.meta; tImg.src = t.img; tImg.alt = t.name;
        card.style.opacity = '1'; card.style.transform = 'translateY(0)';
    }, 250);
}

if (testimonials.length > 1) {
    document.getElementById('nextTestimonial').addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % testimonials.length;
        switchTestimonial(currentIndex);
    });
    document.getElementById('prevTestimonial').addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + testimonials.length) % testimonials.length;
        switchTestimonial(currentIndex);
    });

    let autoRotate = setInterval(() => {
        currentIndex = (currentIndex + 1) % testimonials.length;
        switchTestimonial(currentIndex);
    }, 6000);

    card.addEventListener('mouseenter', () => clearInterval(autoRotate));
    card.addEventListener('mouseleave', () => {
        autoRotate = setInterval(() => {
            currentIndex = (currentIndex + 1) % testimonials.length;
            switchTestimonial(currentIndex);
        }, 6000);
    });
}
<?php endif; ?>

/* ── CARD MICRO-TILT ── */
document.querySelectorAll('.service-card, .ministry-card').forEach(card => {
    card.addEventListener('mousemove', e => {
        const rect = card.getBoundingClientRect();
        const rotX = ((e.clientY - rect.top  - rect.height/2) / (rect.height/2)) * -4;
        const rotY = ((e.clientX - rect.left - rect.width/2)  / (rect.width/2))  *  4;
        card.style.transform = `translateY(-8px) scale(1.01) rotateX(${rotX}deg) rotateY(${rotY}deg)`;
    });
    card.addEventListener('mouseleave', () => { card.style.transform = ''; });
});

/* ── SMOOTH SCROLL for hero buttons ── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
});
</script>

</body>
</html>
