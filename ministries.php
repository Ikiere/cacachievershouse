<?php
$seo = [
    'title'       => 'Ministries',
    'description' => 'Explore the vibrant ministries at CAC Achievers House — Youth, Children\'s Church, Women\'s Fellowship, Men\'s Fellowship, and more.',
    'url'         => 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/cac/ministries.php',
];
include 'includes/header.php';
?>
<body>
<?php include 'includes/site-header.php'; ?>

<!-- ── PAGE HERO ───────────────────────────────────────────── -->
<section class="page-hero" style="background-image:url('assets/images/bg.jpg');">
    <div class="page-hero-overlay"></div>
    <div class="page-hero-content">
        <span class="hero-badge"><i class="bx bx-crown"></i> WHAT WE OFFER</span>
        <h1>Our <span class="highlight">Ministries</span></h1>
        <p>Diverse communities designed to help every believer grow, connect and thrive in their God-given purpose.</p>
    </div>
</section>

<!-- ── MINISTRIES GRID ─────────────────────────────────────── -->
<section class="page-section bg-white" aria-labelledby="ministries-page-heading">
<div class="page-container">
    <div class="section-header text-center">
        <span class="section-badge"><i class="bx bx-heart"></i> OUR COMMUNITIES</span>
        <h2 id="ministries-page-heading">Find Your Community</h2>
        <p class="section-subtitle">Each ministry is a family built around shared purpose, growth, and the love of Christ.</p>
    </div>

    <div class="ministries-full-grid">

        <!-- Youth Ministry -->
        <article class="ministry-full-card reveal">
            <div class="mfc-image">
                <img src="https://images.unsplash.com/photo-1515169067865-5387ec356754?w=700&q=80" alt="Youth Ministry at CAC Achievers House" loading="lazy">
                <span class="mfc-badge">Ages 13–25</span>
            </div>
            <div class="mfc-body">
                <div class="mfc-icon"><i class="bx bx-meteor"></i></div>
                <h3>Youth Ministry</h3>
                <p>Empowering young people to discover their identity in Christ through dynamic worship, relevant teachings, peer-to-peer accountability, and meaningful community connections.</p>
                <ul class="mfc-details">
                    <li><i class="bx bx-time"></i> Sundays after main service</li>
                    <li><i class="bx bx-calendar"></i> Youth Night — Every Friday 6 PM</li>
                    <li><i class="bx bx-user"></i> Leader: Pastor Taiwo</li>
                </ul>
                <a href="contact.php" class="mfc-link">Join This Ministry <i class="bx bx-right-arrow-alt"></i></a>
            </div>
        </article>

        <!-- Children's Church -->
        <article class="ministry-full-card reveal reveal-delay-1">
            <div class="mfc-image mfc-image--blue">
                <div class="mfc-icon-hero"><i class="bx bx-child"></i></div>
                <span class="mfc-badge dark">Ages 3–12</span>
            </div>
            <div class="mfc-body">
                <div class="mfc-icon blue"><i class="bx bx-book-heart"></i></div>
                <h3>Children's Church</h3>
                <p>Building strong foundations of faith in children through engaging Bible stories, age-appropriate worship, interactive activities, and a safe, loving environment.</p>
                <ul class="mfc-details">
                    <li><i class="bx bx-time"></i> Sundays 8:00 AM – 11:00 AM</li>
                    <li><i class="bx bx-calendar"></i> VBS — School holidays</li>
                    <li><i class="bx bx-user"></i> Leader: Deaconess Funmi</li>
                </ul>
                <a href="contact.php" class="mfc-link">Enroll Your Child <i class="bx bx-right-arrow-alt"></i></a>
            </div>
        </article>

        <!-- Women's Fellowship -->
        <article class="ministry-full-card reveal reveal-delay-2">
            <div class="mfc-image">
                <img src="https://images.unsplash.com/photo-1520975916090-3105956dac38?w=700&q=80" alt="Women's Fellowship at CAC Achievers House" loading="lazy">
                <span class="mfc-badge">All Ages</span>
            </div>
            <div class="mfc-body">
                <div class="mfc-icon gold"><i class="bx bx-crown"></i></div>
                <h3>Women's Fellowship</h3>
                <p>A nurturing community where women grow spiritually, build lasting friendships, mentor one another, and are equipped to impact their homes, workplaces, and nation.</p>
                <ul class="mfc-details">
                    <li><i class="bx bx-time"></i> First Saturday of every month</li>
                    <li><i class="bx bx-calendar"></i> Bible Study — Wednesdays 6 PM</li>
                    <li><i class="bx bx-user"></i> Leader: Pastor Mrs. Adeyemi</li>
                </ul>
                <a href="contact.php" class="mfc-link">Connect With Us <i class="bx bx-right-arrow-alt"></i></a>
            </div>
        </article>

        <!-- Men's Fellowship -->
        <article class="ministry-full-card reveal">
            <div class="mfc-image mfc-image--dark">
                <div class="mfc-icon-hero"><i class="bx bx-shield"></i></div>
                <span class="mfc-badge">All Ages</span>
            </div>
            <div class="mfc-body">
                <div class="mfc-icon dark"><i class="bx bx-shield-quarter"></i></div>
                <h3>Men's Fellowship</h3>
                <p>Raising Kingdom men who lead with integrity, faith, and purpose. Monthly breakfasts, accountability groups, and equipping sessions for men to stand strong in every sphere of life.</p>
                <ul class="mfc-details">
                    <li><i class="bx bx-time"></i> Last Saturday of every month</li>
                    <li><i class="bx bx-calendar"></i> Men's Prayer — Fridays 7 PM</li>
                    <li><i class="bx bx-user"></i> Leader: Deacon Samuel</li>
                </ul>
                <a href="contact.php" class="mfc-link">Join The Brotherhood <i class="bx bx-right-arrow-alt"></i></a>
            </div>
        </article>

        <!-- Music & Worship -->
        <article class="ministry-full-card reveal reveal-delay-1">
            <div class="mfc-image">
                <img src="https://images.unsplash.com/photo-1507874457470-272b3c8d8ee2?w=700&q=80" alt="Worship Ministry" loading="lazy">
                <span class="mfc-badge">All Ages</span>
            </div>
            <div class="mfc-body">
                <div class="mfc-icon purple"><i class="bx bx-music"></i></div>
                <h3>Music & Worship</h3>
                <p>Ushering the congregation into God's presence through Spirit-led praise and worship. Choir, instrumentalists, and media team working together to create transformative worship experiences.</p>
                <ul class="mfc-details">
                    <li><i class="bx bx-time"></i> Rehearsals — Thursdays 5 PM</li>
                    <li><i class="bx bx-calendar"></i> Worship Night — Monthly</li>
                    <li><i class="bx bx-user"></i> Leader: Bro. Victor</li>
                </ul>
                <a href="contact.php" class="mfc-link">Audition Now <i class="bx bx-right-arrow-alt"></i></a>
            </div>
        </article>

        <!-- Outreach -->
        <article class="ministry-full-card reveal reveal-delay-2">
            <div class="mfc-image mfc-image--green">
                <div class="mfc-icon-hero"><i class="bx bx-world"></i></div>
                <span class="mfc-badge">Open to All</span>
            </div>
            <div class="mfc-body">
                <div class="mfc-icon green"><i class="bx bx-globe"></i></div>
                <h3>Outreach & Evangelism</h3>
                <p>Reaching the unreached in our communities through street evangelism, hospital visitation, prison ministry, and community development initiatives that demonstrate God's love in action.</p>
                <ul class="mfc-details">
                    <li><i class="bx bx-time"></i> Community Saturdays — Monthly</li>
                    <li><i class="bx bx-calendar"></i> Evangelism — 2nd Sunday</li>
                    <li><i class="bx bx-user"></i> Leader: Evang. Grace</li>
                </ul>
                <a href="contact.php" class="mfc-link">Serve With Us <i class="bx bx-right-arrow-alt"></i></a>
            </div>
        </article>

    </div>
</div>
</section>

<!-- ── CTA ────────────────────────────────────────────────── -->
<section class="page-cta">
    <div class="page-container text-center">
        <h2>Not Sure Where to Start?</h2>
        <p>Come speak with one of our pastors after Sunday service or reach out — we'll help you find the right community.</p>
        <a href="contact.php" class="btn-primary" style="display:inline-flex;align-items:center;gap:.5rem;">
            Contact Us <i class="bx bx-right-arrow-alt"></i>
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
