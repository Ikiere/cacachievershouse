<?php
// ============================================================
// CONTACT PAGE
// contact.php
// ============================================================
require_once 'includes/config.php';

$seo = [
    'title'       => 'Contact Us',
    'description' => 'Get in touch with ' . get_setting('site_name', 'CAC Achievers House') . ' — ask questions, plan your visit, request prayer, or connect with a ministry.',
    'url'         => 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/cac/contact.php',
];
include 'includes/header.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim(strip_tags($_POST['name']    ?? ''));
    $email   = trim($_POST['email']   ?? '');
    $subject = trim(strip_tags($_POST['subject'] ?? ''));
    $message = trim(strip_tags($_POST['message'] ?? ''));

    if (!$name || !$email || !$message) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $name, $email, $subject, $message);
        if ($stmt->execute()) {
            $success = 'Thank you! Your message has been received. We will get back to you within 24 hours.';
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    }
}

// Dynamic contact details
$phone   = get_setting('contact_phone', '+234 800 000 0000');
$email_addr = get_setting('contact_email', 'info@cacachievers.com');
$address = get_setting('contact_address', '12 Faith Avenue, Lagos, Nigeria');
$map_embed = get_setting('map_embed_url', '');
$fb_url  = get_setting('facebook_url', '#');
$ig_url  = get_setting('instagram_url', '#');
$yt_url  = get_setting('youtube_url', '#');
$tw_url  = get_setting('twitter_url', '#');
$wa_num  = get_setting('whatsapp_number', '');
?>
<body>
<?php include 'includes/site-header.php'; ?>

<!-- ── PAGE HERO ── -->
<section class="page-hero" style="background-image:url('assets/images/bg.jpg');">
    <div class="page-hero-overlay"></div>
    <div class="page-hero-content">
        <span class="hero-badge"><i class="bx bx-envelope"></i> GET IN TOUCH</span>
        <h1>Contact <span class="highlight">Us</span></h1>
        <p>We would love to hear from you. Reach out, ask questions, or plan your first visit.</p>
    </div>
</section>

<!-- ── CONTACT SECTION ── -->
<section class="page-section bg-white" aria-labelledby="contact-page-heading">
<div class="page-container">

    <div class="contact-grid">

        <!-- LEFT: Form -->
        <div class="contact-form-wrap reveal">
            <h2 id="contact-page-heading">Send Us a Message</h2>
            <p class="contact-subtitle">Fill in the form below and we'll get back to you within 24 hours.</p>

            <?php if ($success): ?>
            <div class="form-success">
                <i class="bx bx-check-circle"></i>
                <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="form-error">
                <i class="bx bx-error-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="contact.php" class="contact-form" id="contactForm" novalidate>
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact-name">Full Name <span class="required">*</span></label>
                        <input type="text" id="contact-name" name="name" placeholder="Your full name" required
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="contact-email">Email Address <span class="required">*</span></label>
                        <input type="email" id="contact-email" name="email" placeholder="your@email.com" required
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact-subject">Subject</label>
                    <select id="contact-subject" name="subject">
                        <option value="">Select a topic…</option>
                        <option value="General Enquiry"     <?= (($_POST['subject'] ?? '') === 'General Enquiry')     ? 'selected' : '' ?>>General Enquiry</option>
                        <option value="Plan My Visit"       <?= (($_POST['subject'] ?? '') === 'Plan My Visit')       ? 'selected' : '' ?>>Plan My Visit</option>
                        <option value="Prayer Request"      <?= (($_POST['subject'] ?? '') === 'Prayer Request')      ? 'selected' : '' ?>>Prayer Request</option>
                        <option value="Ministry Membership" <?= (($_POST['subject'] ?? '') === 'Ministry Membership') ? 'selected' : '' ?>>Ministry Membership</option>
                        <option value="Counselling"         <?= (($_POST['subject'] ?? '') === 'Counselling')         ? 'selected' : '' ?>>Counselling</option>
                        <option value="Partnership"         <?= (($_POST['subject'] ?? '') === 'Partnership')         ? 'selected' : '' ?>>Partnership</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="contact-message">Message <span class="required">*</span></label>
                    <textarea id="contact-message" name="message" rows="6" placeholder="Write your message here…" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn-primary" style="width:100%;justify-content:center;display:flex;align-items:center;gap:.5rem;">
                    Send Message <i class="bx bx-send"></i>
                </button>
            </form>
        </div>

        <!-- RIGHT: Info -->
        <aside class="contact-info reveal reveal-delay-1">
            <div class="contact-info-card">
                <h3>Find Us</h3>

                <div class="contact-detail">
                    <div class="cd-icon"><i class="bx bx-map-pin"></i></div>
                    <div>
                        <strong>Address</strong>
                        <p><?= nl2br(htmlspecialchars($address)) ?></p>
                    </div>
                </div>

                <div class="contact-detail">
                    <div class="cd-icon"><i class="bx bx-phone"></i></div>
                    <div>
                        <strong>Phone</strong>
                        <p><a href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', $phone)) ?>"><?= htmlspecialchars($phone) ?></a></p>
                    </div>
                </div>

                <div class="contact-detail">
                    <div class="cd-icon"><i class="bx bx-envelope"></i></div>
                    <div>
                        <strong>Email</strong>
                        <p><a href="mailto:<?= htmlspecialchars($email_addr) ?>"><?= htmlspecialchars($email_addr) ?></a></p>
                    </div>
                </div>

                <?php if ($wa_num): ?>
                <div class="contact-detail">
                    <div class="cd-icon" style="background:#dcfce7;color:#16a34a;"><i class="bx bxl-whatsapp"></i></div>
                    <div>
                        <strong>WhatsApp</strong>
                        <p><a href="https://wa.me/<?= htmlspecialchars($wa_num) ?>" target="_blank" rel="noopener">Chat with us</a></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="contact-info-card">
                <h3>Service Times</h3>
                <div class="service-time-list">
                    <div class="st-item">
                        <span class="st-day"><i class="bx bx-sun"></i> Sunday</span>
                        <span class="st-time">8:00 AM – 11:00 AM</span>
                    </div>
                    <div class="st-item">
                        <span class="st-day"><i class="bx bx-book-open"></i> Wednesday</span>
                        <span class="st-time">6:00 PM – 8:00 PM</span>
                    </div>
                    <div class="st-item">
                        <span class="st-day"><i class="bx bx-heart"></i> Friday</span>
                        <span class="st-time">7:00 PM – 9:00 PM</span>
                    </div>
                </div>
            </div>

            <div class="contact-info-card social-card">
                <h3>Follow Us</h3>
                <div class="contact-socials">
                    <a href="<?= htmlspecialchars($fb_url) ?>" aria-label="Facebook" <?= $fb_url !== '#' ? 'target="_blank" rel="noopener"' : '' ?>><i class="bx bxl-facebook"></i></a>
                    <a href="<?= htmlspecialchars($ig_url) ?>" aria-label="Instagram" <?= $ig_url !== '#' ? 'target="_blank" rel="noopener"' : '' ?>><i class="bx bxl-instagram"></i></a>
                    <a href="<?= htmlspecialchars($yt_url) ?>" aria-label="YouTube" <?= $yt_url !== '#' ? 'target="_blank" rel="noopener"' : '' ?>><i class="bx bxl-youtube"></i></a>
                    <a href="<?= htmlspecialchars($tw_url) ?>" aria-label="Twitter" <?= $tw_url !== '#' ? 'target="_blank" rel="noopener"' : '' ?>><i class="bx bxl-twitter"></i></a>
                </div>
            </div>
        </aside>

    </div>
</div>
</section>

<!-- ── MAP SECTION ── -->
<section class="contact-map-section" aria-label="Church location map">
    <?php if ($map_embed): ?>
    <iframe
        src="<?= htmlspecialchars($map_embed) ?>"
        width="100%" height="450"
        style="border:0;" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        title="Church Location Map">
    </iframe>
    <?php else: ?>
    <!-- Fallback: generate map from address -->
    <iframe
        src="https://maps.google.com/maps?q=<?= urlencode($address) ?>&t=&z=15&ie=UTF8&iwloc=&output=embed"
        width="100%" height="450"
        style="border:0;" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        title="Church Location Map">
    </iframe>
    <?php endif; ?>
</section>

<!-- JSON-LD: FAQ -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "What time is Sunday service?",
            "acceptedAnswer": { "@type": "Answer", "text": "Sunday service holds from 8:00 AM to 11:00 AM." }
        },
        {
            "@type": "Question",
            "name": "Where is <?= addslashes(get_setting('site_name', 'CAC Achievers House')) ?> located?",
            "acceptedAnswer": { "@type": "Answer", "text": "We are located at <?= addslashes($address) ?>. Contact us for directions." }
        },
        {
            "@type": "Question",
            "name": "How can I join a ministry?",
            "acceptedAnswer": { "@type": "Answer", "text": "Fill in our contact form and select 'Ministry Membership' as the subject, and we will connect you." }
        }
    ]
}
</script>

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
