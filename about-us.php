<?php
// ============================================================
// ABOUT US PAGE
// about-us.php
// ============================================================
require_once 'includes/config.php';

$seo = [
    'title'       => 'About Us',
    'description' => 'Christ Apostolic Church Achievers House — a vibrant, Christ-centred church in Derby dedicated to prayer, the teaching of God\'s Word, and heartfelt worship.',
    'url'         => 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/cac/about-us.php',
];
include 'includes/header.php';
?>
<body>
<?php include 'includes/site-header.php'; ?>

    <!-- ── PAGE HERO ── -->
    <section class="page-hero" style="background-image:url('<?= $base . ltrim('assets/images/bg.jpg', '/') ?>');">
        <div class="page-hero-overlay"></div>
        <div class="page-hero-content reveal">
            <span class="hero-badge"><i class="bx bx-info-circle"></i> OUR STORY</span>
            <h1>About <span class="highlight">Us</span></h1>
            <p>Raising Kingdom Achievers for Christ.</p>
        </div>
    </section>

    <!-- ── ABOUT US — THE STORY ── -->
    <section class="about-story" aria-labelledby="about-heading">
        <div class="about-container">
            <div class="about-story-content reveal">
                <span class="about-pill">WHO WE ARE</span>
                <h2 id="about-heading">Christ Apostolic Church <span class="about-accent">Achievers House</span></h2>
                <p class="about-lead">
                    Christ Apostolic Church Achievers House is a vibrant, Christ-centred church in Derby, under the leadership of <strong>Pastor Tosin Adegbola</strong>, we are a family of believers dedicated to prayer, the teaching of God's Word, and heartfelt worship.
                </p>
                <p>
                    Our vision is to raise Kingdom Achievers for Christ—men, women, and young people who are grounded in faith, empowered by the Holy Spirit, and equipped to fulfill God's purpose for their lives. We are committed to nurturing spiritual growth, building strong Christian character, and fostering a loving community where everyone feels valued and supported.
                </p>
                <p>
                    At CAC Achievers House, we believe in the power of faith, love, service and unity to transform lives and impact communities. Whether you are new to the faith or seeking a church home, where everyone can encounter God's presence, you are warmly welcome to worship, grow, and serve with us.
                </p>
            </div>
        </div>
    </section>

    <!-- ── MEET THE PASTOR — Premium Profile ── -->
    <section class="pastor-profile" aria-labelledby="pastor-heading">
        <div class="about-container">
            <div class="pastor-grid">
                <div class="pastor-image-wrapper reveal">
                    <div class="pastor-image-frame">
                        <img src="assets/images/pastor.jpg" alt="Pastor Tosin Adegbola" class="pastor-img">
                        <div class="pastor-image-accent"></div>
                    </div>
                </div>
                <div class="pastor-content reveal reveal-delay-1">
                    <span class="about-pill">LEAD PASTOR</span>
                    <h2 id="pastor-heading">Pastor Tosin <span class="about-accent">Adegbola</span></h2>
                    <p class="pastor-lead">
                        A visionary leader dedicated to raising Kingdom Achievers through the undiluted teaching of God's Word, fervent prayer, and genuine love.
                    </p>
                    <p>
                        Pastor Tosin Adegbola carries a profound mandate to equip believers for impactful living. Under his dynamic leadership, CAC Achievers House has grown into a vibrant family of faith where people discover their God-given purpose and step into their divine destinies.
                    </p>
                    <p>
                        With a passion for spiritual excellence and community transformation, he continually inspires the congregation to deepen their relationship with Christ and reflect His love in every sphere of life.
                    </p>
                    
                    <div class="pastor-signature-area">
                        <div class="pastor-title">Lead Pastor, CAC Achievers House</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── TIMES OF SERVICE ── -->
    <section class="about-services" aria-labelledby="services-heading">
        <div class="about-container">
            <div class="about-services-header reveal">
                <span class="about-pill">JOIN US</span>
                <h2 id="services-heading">Times of <span class="about-accent">Service</span></h2>
            </div>
            <div class="about-services-grid reveal reveal-delay-1">
                <div class="about-service-card primary">
                    <div class="about-service-icon"><i class='bx bx-sun'></i></div>
                    <h3>Sunday Worship</h3>
                    <span class="about-service-time">10:00am – 12:00pm</span>
                    <p>Powerful worship, inspiring messages, and fellowship with believers. The highlight of the week.</p>
                </div>
                <div class="about-service-card">
                    <div class="about-service-icon"><i class='bx bx-heart'></i></div>
                    <h3>Monday-Achievers Pray</h3>
                    <span class="about-service-time">8:30pm – 9:30pm</span>
                    <p>Corporate prayer and intercession for personal and communal breakthrough.</p>
                </div>
                <div class="about-service-card">
                    <div class="about-service-icon"><i class='bx bx-book-open'></i></div>
                    <h3>Wednesday-Bible Study</h3>
                    <span class="about-service-time">7:00pm – 8:10pm</span>
                    <p>Dive deeper into God's Word with interactive study sessions and transformative discussions.</p>
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
                    <h3>Faith</h3>
                    <p>Grounded in the truth of Scripture, we walk by faith and trust in God's unfailing promises.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class='bx bx-heart'></i></div>
                    <h3>Love</h3>
                    <p>Emulating Christ's love, we foster an environment of grace, compassion, and genuine connection.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class='bx bx-donate-heart'></i></div>
                    <h3>Service</h3>
                    <p>We serve with humility and excellence, reflecting Christ in everything we do for His glory.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class='bx bx-group'></i></div>
                    <h3>Unity</h3>
                    <p>Together as one body in Christ, we build authentic relationships and walk in love as a family.</p>
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
