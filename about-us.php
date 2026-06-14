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
    <section class="page-hero" style="background-image:url('assets/images/hero-bg.jpg');">
        <div class="page-hero-overlay"></div>
        <div class="page-hero-content reveal">
            <span class="hero-badge"><i class="bx bx-info-circle"></i> OUR STORY</span>
            <h1>About <span class="highlight">Us</span></h1>
            <p>Building Faith, Strengthening Community, Transforming Lives.</p>
        </div>
    </section>

    <!-- ── PREMIUM STORY SECTION ── -->
    <section class="premium-about-section bg-white" aria-labelledby="about-heading">
        <div class="page-container">
            <div class="premium-about-grid">
                
                <!-- Left: Elegant Typography -->
                <div class="premium-about-text reveal">
                    <div class="premium-section-pill">WELCOME HOME</div>
                    <h2 id="about-heading" class="premium-heading">A Church Built on <br><span class="text-gradient">Faith and Love</span></h2>
                    
                    <div class="premium-text-body">
                        <p class="lead-text">
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

                    <div class="premium-stats-grid mt-4">
                        <div class="p-stat">
                            <span class="p-stat-number text-gradient">10+</span>
                            <span class="p-stat-label">Years of Grace</span>
                        </div>
                        <div class="p-stat">
                            <span class="p-stat-number text-gradient">5k+</span>
                            <span class="p-stat-label">Lives Touched</span>
                        </div>
                        <div class="p-stat">
                            <span class="p-stat-number text-gradient">24/7</span>
                            <span class="p-stat-label">Prayers</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Glassmorphic Core Values -->
                <div class="premium-about-values reveal reveal-delay-1">
                    <div class="glass-value-card">
                        <div class="gvc-icon"><i class='bx bx-bible'></i></div>
                        <h3>Word-Based</h3>
                        <p>We are anchored in the undiluted truth of the Scriptures, finding daily direction in God's eternal Word.</p>
                    </div>
                    
                    <div class="glass-value-card">
                        <div class="gvc-icon"><i class='bx bx-heart'></i></div>
                        <h3>Love-Driven</h3>
                        <p>We emulate Christ’s love, fostering an environment of grace, healing, and genuine connection.</p>
                    </div>

                    <div class="glass-value-card">
                        <div class="gvc-icon"><i class='bx bx-rocket'></i></div>
                        <h3>Excellence</h3>
                        <p>We strive to represent the Kingdom with the highest standards, ensuring our actions reflect His glory.</p>
                    </div>
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
