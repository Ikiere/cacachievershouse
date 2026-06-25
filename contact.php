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

// ── Load PHPMailer ────────────────────────────────────────────
$phpmailer_path = __DIR__ . '/vendor/phpmailer/src/PHPMailer.php';
$use_smtp = file_exists($phpmailer_path) && get_setting('smtp_host') && get_setting('smtp_username');

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
        // 1. Always save to contacts table
        $saved = false;
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param('ssss', $name, $email, $subject, $message);
            $saved = $stmt->execute();
        }

        // 1b. If it's a Testimonial, also save to testimonials table (pending status)
        if ($subject === 'Testimonial') {
            $t_role = 'Church Member';
            $t_stmt = $conn->prepare("INSERT INTO testimonials (name, role, quote, is_active) VALUES (?, ?, ?, 0)");
            if ($t_stmt) {
                $t_stmt->bind_param('sss', $name, $t_role, $message);
                $t_stmt->execute();
            }
        }

        // 2. Try to send email via SMTP if configured
        $mail_sent = false;
        $mail_error = '';
        if ($use_smtp) {
            try {
                require_once __DIR__ . '/vendor/phpmailer/src/Exception.php';
                require_once __DIR__ . '/vendor/phpmailer/src/PHPMailer.php';
                require_once __DIR__ . '/vendor/phpmailer/src/SMTP.php';

                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = get_setting('smtp_host');
                $mail->SMTPAuth   = true;
                $mail->Username   = get_setting('smtp_username');
                $mail->Password   = get_setting('smtp_password');
                $enc              = get_setting('smtp_encryption', 'tls');
                $mail->SMTPSecure = $enc === 'ssl' ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = intval(get_setting('smtp_port', '587'));

                $from_email = get_setting('smtp_from_email', get_setting('contact_email', 'noreply@cacachievers.com'));
                $from_name  = get_setting('smtp_from_name', get_setting('site_name', 'CAC Achievers House'));
                $admin_to   = get_setting('smtp_admin_email', get_setting('contact_email', ''));

                if (empty($admin_to)) {
                    $admin_to = get_setting('smtp_username'); // Fallback to the authenticated SMTP user
                }

                $mail->setFrom($from_email, $from_name);
                if (!empty($admin_to)) {
                    $mail->addAddress($admin_to);
                } else {
                    throw new Exception('Admin receiving email is not configured in settings.');
                }
                $mail->addReplyTo($email, $name);

                $mail->isHTML(true);
                $mail->Subject = 'Contact Form: ' . ($subject ?: 'New Message') . ' — from ' . $name;
                $mail->Body    = "
                    <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
                        <div style='background:#0f172a;padding:24px 32px;border-radius:8px 8px 0 0;'>
                            <h2 style='color:#fff;margin:0;font-size:20px;'>New Contact Form Submission</h2>
                            <p style='color:#94a3b8;margin:4px 0 0;font-size:14px;'>CAC Achievers House Website</p>
                        </div>
                        <div style='background:#f8fafc;padding:28px 32px;border:1px solid #e2e8f0;'>
                            <table style='width:100%;border-collapse:collapse;font-size:15px;'>
                                <tr><td style='color:#64748b;padding:8px 0;width:120px;'>Name</td><td style='font-weight:600;color:#0f172a;'>" . htmlspecialchars($name) . "</td></tr>
                                <tr><td style='color:#64748b;padding:8px 0;'>Email</td><td><a href='mailto:" . htmlspecialchars($email) . "' style='color:#2563eb;'>" . htmlspecialchars($email) . "</a></td></tr>
                                <tr><td style='color:#64748b;padding:8px 0;'>Subject</td><td style='color:#0f172a;'>" . htmlspecialchars($subject ?: 'Not specified') . "</td></tr>
                            </table>
                            <hr style='border:none;border-top:1px solid #e2e8f0;margin:16px 0;'>
                            <p style='color:#64748b;font-size:13px;margin-bottom:8px;'>MESSAGE</p>
                            <div style='background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:18px;color:#334155;line-height:1.7;'>" . nl2br(htmlspecialchars($message)) . "</div>
                        </div>
                        <div style='background:#f1f5f9;padding:14px 32px;border-radius:0 0 8px 8px;border:1px solid #e2e8f0;border-top:none;text-align:center;'>
                            <p style='color:#94a3b8;font-size:12px;margin:0;'>Sent from " . htmlspecialchars(get_setting('site_name', 'CAC Achievers House')) . " website contact form</p>
                        </div>
                    </div>";
                $mail->AltBody = "Name: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$message";

                $mail->send();
                $mail_sent = true;

                // 3. Send confirmation copy to the user
                $mail->clearAddresses();
                $mail->clearReplyTos();
                
                $mail->addAddress($email, $name);
                $mail->addReplyTo($admin_to, $from_name);
                $mail->Subject = 'We received your message: ' . ($subject ?: 'General Enquiry');
                $mail->Body = "
                    <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
                        <div style='background:#0047AB;padding:24px 32px;border-radius:8px 8px 0 0;'>
                            <h2 style='color:#fff;margin:0;font-size:20px;'>Message Received</h2>
                        </div>
                        <div style='background:#f8fafc;padding:28px 32px;border:1px solid #e2e8f0;border-top:none;'>
                            <p style='font-size:16px;color:#334155;'>Hello " . htmlspecialchars($name) . ",</p>
                            <p style='font-size:15px;color:#475569;line-height:1.6;'>
                                Thank you for contacting <strong>" . htmlspecialchars(get_setting('site_name', 'CAC Achievers House')) . "</strong>. 
                                We have successfully received your message regarding <em>" . htmlspecialchars($subject ?: 'General Enquiry') . "</em>.
                            </p>
                            <p style='font-size:15px;color:#475569;line-height:1.6;'>
                                Our team will review your message and get back to you within 24 hours.
                            </p>
                            <hr style='border:none;border-top:1px solid #e2e8f0;margin:24px 0;'>
                            <p style='color:#64748b;font-size:13px;margin-bottom:8px;'>YOUR ORIGINAL MESSAGE:</p>
                            <div style='background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:16px;color:#64748b;font-size:14px;line-height:1.6;font-style:italic;'>
                                " . nl2br(htmlspecialchars($message)) . "
                            </div>
                        </div>
                    </div>";
                $mail->AltBody = "Hello $name,\n\nThank you for contacting " . get_setting('site_name', 'CAC Achievers House') . ". We have successfully received your message and will get back to you within 24 hours.\n\nYour message:\n$message";
                
                // We ignore exceptions here so the main flow isn't interrupted if the auto-reply fails
                try { $mail->send(); } catch (\Exception $e) {}
            } catch (\Exception $e) {
                $mail_error = $e->getMessage();
            }
        }

        if ($saved) {
            if ($use_smtp && $mail_sent) {
                $success = 'Thank you, ' . htmlspecialchars($name) . '! Your message has been sent. We will respond within 24 hours.';
            } elseif ($use_smtp && !$mail_sent) {
                $success = 'Your message was received, but the email notification failed (' . htmlspecialchars($mail_error) . '). We will still get back to you.';
            } else {
                $success = 'Thank you! Your message has been received. We will get back to you within 24 hours.';
            }
        } else {
            $error = 'Something went wrong saving your message. Please try again.';
        }
    }
}

// Dynamic contact details
$phone   = get_setting('contact_phone', '+234 800 000 0000');
$email_addr = get_setting('contact_email', 'info@cacachievers.com');
$address = get_setting('contact_address', 'Colville Common Room and Community Space, DE22 3AT');
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
<section class="page-hero" style="background-image:url('<?= $base . ltrim('assets/images/bg.jpg', '/') ?>');">
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
                        <option value="Testimonial"         <?= (($_POST['subject'] ?? '') === 'Testimonial')         ? 'selected' : '' ?>>Submit a Testimonial</option>
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
                        <p style="letter-spacing:0.5px;"><?= nl2br(htmlspecialchars($address)) ?></p>
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
                <h3>Times of Service</h3>
                <div class="service-time-list">
                    <div class="st-item">
                        <span class="st-day"><i class="bx bx-sun"></i> Sunday Worship</span>
                        <span class="st-time">10:00 AM – 12:00 PM</span>
                    </div>
                    <div class="st-item">
                        <span class="st-day"><i class="bx bx-heart"></i> Monday-Achievers Pray</span>
                        <span class="st-time">8:30 PM – 9:30 PM</span>
                    </div>
                    <div class="st-item">
                        <span class="st-day"><i class="bx bx-book-open"></i> Wednesday-Bible Study</span>
                        <span class="st-time">7:00 PM – 8:10 PM</span>
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
            "acceptedAnswer": { "@type": "Answer", "text": "Sunday Worship holds from 10:00 AM to 12:00 PM. Monday Achievers Pray is 8:30 PM to 9:30 PM. Wednesday Bible Study is 7:00 PM to 8:10 PM." }
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
if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); }});
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
} else {
    document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible'));
}
</script>
</body>
</html>
