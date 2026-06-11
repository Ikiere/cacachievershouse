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
     HERO — Premium Full-Viewport
     ============================================================ -->
<section class="hero" id="home" aria-label="Welcome to CAC Achievers House">
    <div class="hero-orb"></div>

    <div class="hero-content">
        <div class="hero-inner">

            <span class="hero-eyebrow">
                <i class="bx bx-church"></i>
                Welcome to <?= htmlspecialchars(get_setting('site_name','CAC Achievers House')) ?>
            </span>

            <h1>
                <?php
                $hero_title = get_setting('hero_title','Where Faith Meets Destiny');
                // Wrap the first word that's a keyword in a highlight span
                echo nl2br(htmlspecialchars($hero_title));
                ?>
            </h1>

            <p><?= htmlspecialchars(get_setting('hero_subtitle','A vibrant community where lives are restored, purposes are discovered, and believers are empowered to reach their God-given potential.')) ?></p>

            <div class="hero-buttons">
                <a href="#services" class="btn-primary">
                    Join Us Sunday <i class="bx bx-right-arrow-alt"></i>
                </a>
                <a href="sermons.php" class="btn-outline">
                    <i class="bx bx-play-circle"></i> Watch Sermons
                </a>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <strong>15+</strong>
                    <span>Years of Ministry</span>
                </div>
                <div class="hero-stat">
                    <strong>500+</strong>
                    <span>Active Members</span>
                </div>
                <div class="hero-stat">
                    <strong>6</strong>
                    <span>Ministry Departments</span>
                </div>
                <div class="hero-stat">
                    <strong>100+</strong>
                    <span>Sermons Preached</span>
                </div>
            </div>

        </div>
    </div>

    <div class="scroll-indicator" aria-hidden="true">
        <div class="scroll-mouse"></div>
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
                <h3>Sunday Service</h3>
                <span class="time">8:00 AM – 11:00 AM</span>
                <p>Powerful worship, inspiring messages, and fellowship with believers. The highlight of the week.</p>
            </div>

            <div class="service-card light reveal reveal-delay-1">
                <div class="icon-box-1">
                    <i class="bx bx-book-open"></i>
                </div>
                <h3>Bible Study</h3>
                <span class="time accent">Wednesday · 6:00 PM</span>
                <p>Dive deeper into God's Word with interactive study sessions and transformative discussions.</p>
            </div>

            <div class="service-card dark reveal reveal-delay-2">
                <div class="icon-box-1">
                    <i class="bx bx-heart"></i>
                </div>
                <h3>Prayer Meeting</h3>
                <span class="time accent">Friday · 7:00 PM</span>
                <p>Experience the power of corporate prayer and intercession for personal and communal breakthrough.</p>
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
                A Church Built on<br>Faith & <span>Purpose</span>
            </h2>
            <p class="section-subtitle">
                At CAC Achievers House, we believe every life carries a God-given destiny.
                Our mission is to help you discover yours — through Spirit-filled worship,
                transformative teaching, and a community that walks with you every step of the way.
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

        <div class="events-grid">

            <div class="event-featured reveal">
                <img src="https://images.unsplash.com/photo-1507874457470-272b3c8d8ee2?w=900&q=80"
                     alt="Revival Conference" loading="lazy">
                <div class="featured-overlay">
                    <span class="event-date">
                        <i class="bx bx-calendar"></i>
                        <?php
                        if ($events_result && $events_result->num_rows > 0) {
                            $events_result->data_seek(0);
                            $fe = $events_result->fetch_assoc();
                            echo date('M j, Y', strtotime($fe['start_date']));
                            $events_result->data_seek(0);
                        } else {
                            echo 'Coming Soon';
                        }
                        ?>
                    </span>
                    <?php if (isset($fe)): ?>
                    <h3><?= htmlspecialchars($fe['title']) ?></h3>
                    <p><?= htmlspecialchars(substr($fe['description'] ?? 'An exciting upcoming event.', 0, 120)) ?></p>
                    <?php else: ?>
                    <h3>Revival Conference 2026</h3>
                    <p>Three days of powerful worship, prophetic ministry, and spiritual renewal. Come ready for a breakthrough.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="event-list">
                <?php
                if ($events_result && $events_result->num_rows > 0) {
                    $events_result->data_seek(0);
                    $count = 0;
                    while ($ev = $events_result->fetch_assoc()) {
                        if ($count >= 3) break;
                        $style = $count % 2 === 0 ? 'primary' : 'light';
                        echo '<div class="event-card ' . $style . ' reveal' . ($count > 0 ? ' reveal-delay-' . $count : '') . '">';
                        echo '<span class="event-date"><i class="bx bx-calendar"></i> ' . date('M j, Y', strtotime($ev['start_date'])) . '</span>';
                        echo '<h4>' . htmlspecialchars($ev['title']) . '</h4>';
                        if ($ev['description']) {
                            echo '<p>' . htmlspecialchars(substr($ev['description'], 0, 100)) . '…</p>';
                        }
                        echo '</div>';
                        $count++;
                    }
                } else { ?>
                <div class="event-card primary reveal">
                    <span class="event-date"><i class="bx bx-calendar"></i> July 6, 2026</span>
                    <h4>Youth Outreach Program</h4>
                    <p>Community service and evangelism initiative reaching local neighborhoods with love and hope.</p>
                </div>
                <div class="event-card light reveal reveal-delay-1">
                    <span class="event-date"><i class="bx bx-calendar"></i> July 20, 2026</span>
                    <h4>Family Fun Day</h4>
                    <p>A day of games, food, and fellowship for the whole family to enjoy together in God's presence.</p>
                </div>
                <div class="event-card primary reveal reveal-delay-2">
                    <span class="event-date"><i class="bx bx-calendar"></i> August 3, 2026</span>
                    <h4>Achievers Leadership Summit</h4>
                    <p>Equipping believers with tools to lead boldly in every sphere of life and influence.</p>
                </div>
                <?php } ?>
            </div>

        </div>

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

        <div class="rating-badge">
            ⭐ 5.0 <span>Community Rating</span>
        </div>

        <div class="testimonial-card" id="testimonialCard">
            <div class="quote-icon">"</div>
            <p class="testimonial-text" id="testimonialText">
                CAC Achievers House has been a total blessing to my family. The love,
                support, and spiritual guidance we receive here have transformed our lives.
                This is truly a place where God's presence dwells and destinies are shaped.
            </p>
            <div class="testimonial-author" id="testimonialAuthor">
                <img id="testimonialImg" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sister Grace Adeyemi">
                <div>
                    <h4 id="testimonialName">Sister Grace Adeyemi</h4>
                    <span id="testimonialMeta">Member since 2018</span>
                </div>
            </div>
            <div class="testimonial-nav" role="group" aria-label="Testimonial navigation">
                <button class="nav-btn" id="prevTestimonial" aria-label="Previous testimonial">←</button>
                <button class="nav-btn active" id="nextTestimonial" aria-label="Next testimonial">→</button>
            </div>
        </div>

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
const testimonials = <?php
    if (!empty($testimonials_data)) {
        $js_testimonials = array_map(function($t) {
            return [
                'text' => $t['quote'],
                'name' => $t['name'],
                'meta' => $t['role'],
                'img'  => $t['photo_url'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($t['name']) . '&background=0a1f44&color=fff&size=150'
            ];
        }, $testimonials_data);
        echo json_encode($js_testimonials, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
    } else {
        // Fallback hardcoded testimonials
        echo json_encode([
            ['text' => "CAC Achievers House has been a total blessing to my family. The love, support, and spiritual guidance we receive here have transformed our lives.", 'name' => 'Sister Grace Adeyemi', 'meta' => 'Member since 2018', 'img' => 'https://randomuser.me/api/portraits/women/44.jpg'],
            ['text' => "I came broken, but this church welcomed me with open arms. The teachings here are life-changing. My career, family, and faith are all better because of CAC Achievers House.", 'name' => 'Deacon Emmanuel Okafor', 'meta' => 'Member since 2020', 'img' => 'https://randomuser.me/api/portraits/men/32.jpg'],
            ['text' => "The youth ministry completely changed the direction of my life. I found my purpose here and met friends who push me to be better in Christ every single day.", 'name' => 'Cynthia Babatunde', 'meta' => 'Youth member since 2021', 'img' => 'https://randomuser.me/api/portraits/women/68.jpg']
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
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
