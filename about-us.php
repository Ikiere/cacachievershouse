<?php
// ============================================================
// ABOUT US PAGE
// about-us.php
// ============================================================
require_once 'includes/config.php';

$seo = [
    'title'       => 'About Us',
    'description' => 'Learn about ' . get_setting('site_name', 'CAC Achievers House') . ', our mission, our vision, and what drives our community of faith.',
    'url'         => 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/cac/about-us.php',
];
include 'includes/header.php';
?>
<body>
<?php include 'includes/site-header.php'; ?>

    <!-- ── PAGE HERO ── -->
    <section class="page-hero" style="background-image:url('assets/images/bg.jpg');">
        <div class="page-hero-overlay"></div>
        <div class="page-hero-content reveal">
            <span class="hero-badge"><i class="bx bx-info-circle"></i> OUR STORY</span>
            <h1>About <span class="highlight">Us</span></h1>
            <p>Building Faith, Strengthening Community, Transforming Lives.</p>
        </div>
    </section>

    <!-- ── OUR STORY ── -->
    <section class="about-story" aria-labelledby="about-heading">
        <div class="about-container">
            <div class="about-story-content reveal">
                <span class="about-pill">WHO WE ARE</span>
                <h2 id="about-heading">A Church Built on <span class="about-accent">Faith and Love</span></h2>
                <p class="about-lead">
                    CAC Achievers House is a vibrant, Spirit-filled community committed to
                    helping every believer discover and walk in their God-given destiny.
                </p>
                <p>
                    Our name, <strong>"Achievers House,"</strong> reflects our commitment to being a place where
                    people find answers, hope, and healing through Jesus Christ. We believe that
                    every person who walks through our doors is searching for something, and
                    we're here to point them to the ultimate solution: a relationship with God.
                </p>
                <p>
                    Over the years, we've remained faithful to our core mission: preaching the
                    Gospel, making disciples, and serving our community with excellence and
                    compassion. We invite you to join us on this incredible journey.
                </p>
            </div>
        </div>
    </section>

    <!-- ── MISSION & VISION ── -->
    <section class="about-mission">
        <div class="about-container">
            <div class="mission-grid reveal">
                <div class="mission-card">
                    <div class="mission-icon"><i class='bx bx-target-lock'></i></div>
                    <h3>Our Mission</h3>
                    <p>To preach the undiluted Gospel, make disciples of all nations, and raise a generation of achievers who fulfill their God-given destiny through the power of the Holy Spirit.</p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon"><i class='bx bx-show'></i></div>
                    <h3>Our Vision</h3>
                    <p>To be a leading light in our community — a church where every member is equipped, empowered, and sent out to transform their world for the glory of God.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── CORE VALUES ── -->
    <section class="about-values">
        <div class="about-container">
            <div class="about-values-header reveal">
                <span class="about-pill">WHAT DRIVES US</span>
                <h2>Our Core <span class="about-accent">Values</span></h2>
            </div>
            <div class="values-grid reveal reveal-delay-1">
                <div class="value-card">
                    <div class="value-icon"><i class='bx bx-bible'></i></div>
                    <h3>Word-Based</h3>
                    <p>Anchored in the undiluted truth of the Scriptures, finding daily direction in God's eternal Word.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class='bx bx-heart'></i></div>
                    <h3>Love-Driven</h3>
                    <p>Emulating Christ's love, fostering an environment of grace, healing, and genuine connection.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class='bx bx-rocket'></i></div>
                    <h3>Excellence</h3>
                    <p>Representing the Kingdom with the highest standards, ensuring our actions reflect His glory.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class='bx bx-group'></i></div>
                    <h3>Community</h3>
                    <p>Building authentic relationships and walking together in faith as one family in Christ.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── STATS BAR ── -->
    <section class="about-stats">
        <div class="about-container">
            <div class="stats-bar reveal">
                <div class="stat-item">
                    <span class="stat-num">10+</span>
                    <span class="stat-label">Years of Grace</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num">5k+</span>
                    <span class="stat-label">Lives Touched</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num">24/7</span>
                    <span class="stat-label">Prayer Support</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num">12+</span>
                    <span class="stat-label">Active Ministries</span>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>

<script>
/* Scroll reveal */
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); }});
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
</body>
</html>
