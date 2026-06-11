<!-- ============================================================
     SITE FOOTER — dynamic via site_settings DB
     ============================================================ -->
<footer class="site-footer" id="footer" role="contentinfo">
    <div class="footer-container">

        <!-- Top Row -->
        <div class="footer-top">

            <!-- Brand -->
            <div class="footer-brand">
                <div class="logo-box">
                    <i class="bx bx-church"></i>
                </div>
                <div>
                    <h3><?= htmlspecialchars(get_setting('site_name', 'CAC Achievers House')) ?></h3>
                    <p><?= htmlspecialchars(get_setting('site_tagline', 'Where Faith Meets Destiny')) ?></p>
                </div>
            </div>

            <!-- Social Icons -->
            <div class="footer-socials" aria-label="Social media links">
                <?php $fb = get_setting('facebook_url', '#'); ?>
                <a href="<?= htmlspecialchars($fb) ?>" aria-label="Facebook" <?= $fb !== '#' ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
                    <i class="bx bxl-facebook"></i>
                </a>
                <?php $ig = get_setting('instagram_url', '#'); ?>
                <a href="<?= htmlspecialchars($ig) ?>" aria-label="Instagram" <?= $ig !== '#' ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
                    <i class="bx bxl-instagram"></i>
                </a>
                <?php $yt = get_setting('youtube_url', '#'); ?>
                <a href="<?= htmlspecialchars($yt) ?>" aria-label="YouTube" <?= $yt !== '#' ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
                    <i class="bx bxl-youtube"></i>
                </a>
                <?php $tw = get_setting('twitter_url', '#'); ?>
                <a href="<?= htmlspecialchars($tw) ?>" aria-label="Twitter/X" <?= $tw !== '#' ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
                    <i class="bx bxl-twitter"></i>
                </a>
                <?php $wa = get_setting('whatsapp_number', ''); ?>
                <?php if ($wa): ?>
                <a href="https://wa.me/<?= htmlspecialchars($wa) ?>" aria-label="WhatsApp" target="_blank" rel="noopener noreferrer">
                    <i class="bx bxl-whatsapp"></i>
                </a>
                <?php endif; ?>
            </div>

        </div>

        <hr>

        <!-- Links Grid -->
        <div class="footer-grid">

            <!-- Quick Links -->
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about-us.php">About Us</a></li>
                    <li><a href="ministries.php">Ministries</a></li>
                    <li><a href="sermons.php">Sermons</a></li>
                    <li><a href="events.php">Events</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-col">
                <h4>Contact Us</h4>
                <ul>
                    <?php $phone = get_setting('contact_phone'); ?>
                    <?php if ($phone): ?>
                    <li><a href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', $phone)) ?>">
                        <i class="bx bx-phone"></i> <?= htmlspecialchars($phone) ?>
                    </a></li>
                    <?php endif; ?>

                    <?php $email = get_setting('contact_email'); ?>
                    <?php if ($email): ?>
                    <li><a href="mailto:<?= htmlspecialchars($email) ?>">
                        <i class="bx bx-envelope"></i> <?= htmlspecialchars($email) ?>
                    </a></li>
                    <?php endif; ?>

                    <?php $address = get_setting('contact_address'); ?>
                    <?php if ($address): ?>
                    <li><span>
                        <i class="bx bx-map-pin"></i> <?= nl2br(htmlspecialchars($address)) ?>
                    </span></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Resources -->
            <div class="footer-col">
                <h4>Resources</h4>
                <ul>
                    <li><a href="sermons.php">Sermons</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Prayer Requests</a></li>
                    <?php $give = get_setting('give_url', '#'); ?>
                    <li><a href="<?= htmlspecialchars($give) ?>" <?= $give !== '#' ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>Give Online</a></li>
                </ul>
            </div>

            <!-- Subscribe -->
            <div class="footer-col subscribe">
                <h4>Stay Connected</h4>
                <p>
                    Subscribe to receive updates, prayer requests,
                    and inspirational messages directly to your inbox.
                </p>
                <form class="subscribe-form" onsubmit="handleSubscribe(event)">
                    <input type="email" id="subscribeEmail" placeholder="Your email address" required autocomplete="email">
                    <button type="submit">
                        Subscribe <i class="bx bx-right-arrow-alt"></i>
                    </button>
                </form>
            </div>

        </div>

        <!-- Bottom Bar -->
        <div class="footer-bottom">
            <p>© <?php echo date('Y'); ?> <?= htmlspecialchars(get_setting('site_name', 'CAC Achievers House')) ?>. All Rights Reserved.</p>
            <span>Built with faith &amp; purpose</span>
        </div>

    </div>
</footer>

<script>
function handleSubscribe(e) {
    e.preventDefault();
    const email = document.getElementById('subscribeEmail');
    const btn = e.target.querySelector('button');
    const original = btn.innerHTML;
    btn.innerHTML = '✓ Subscribed!';
    btn.style.background = 'linear-gradient(135deg,#10b981,#059669)';
    email.value = '';
    setTimeout(() => {
        btn.innerHTML = original;
        btn.style.background = '';
    }, 3000);
}
</script>
