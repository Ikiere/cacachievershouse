<!-- ============================================================
     SITE HEADER / NAVBAR
     includes/site-header.php
     ============================================================ -->
<?php
// Ensure config is loaded (may already be loaded by the page)
if (!function_exists('get_setting')) {
    require_once __DIR__ . '/config.php';
}
// Determine active page for nav highlighting
$current = basename($_SERVER['PHP_SELF']);
$base = defined('BASE_URL') ? BASE_URL : '/';
?>
<header id="header" role="banner">
    <div class="nav-container">

        <!-- Logo (dynamic from Site Settings) -->
        <div class="logo">
            <a href="<?= $current === 'index.php' ? '#home' : $base . 'index.php' ?>" class="logo-link">
                <img src="<?= $base . htmlspecialchars(get_setting('logo_path', 'assets/logo/cac-logo.png')) ?>"
                     alt="<?= htmlspecialchars(get_setting('site_name', 'CAC Achievers House')) ?> Logo"
                     class="site-logo-img"
                     onerror="this.style.display='none'">
                <span class="site-title-text"><?= htmlspecialchars(get_setting('site_name', 'CAC Achievers House')) ?></span>
            </a>
        </div>

        <!-- Desktop Nav -->
        <nav role="navigation" aria-label="Main navigation">
            <ul>
                <li><a href="<?= $base ?>index.php"      <?= $current === 'index.php'       ? 'class="active"' : '' ?>>Home</a></li>
                <li><a href="<?= $base ?>about-us.php"   <?= $current === 'about-us.php'    ? 'class="active"' : '' ?>>About Us</a></li>
                <li><a href="<?= $base ?>ministries.php" <?= $current === 'ministries.php'  ? 'class="active"' : '' ?>>Ministries</a></li>
                <li><a href="<?= $base ?>sermons.php"    <?= $current === 'sermons.php'     ? 'class="active"' : '' ?>>Sermons</a></li>
                <li><a href="<?= $base ?>events.php"     <?= $current === 'events.php'      ? 'class="active"' : '' ?>>Events</a></li>
                <li><a href="<?= $base ?>contact.php"    <?= $current === 'contact.php'     ? 'class="active"' : '' ?>>Contact</a></li>
            </ul>
        </nav>

        <!-- Give CTA -->
        <?php $give_url = get_setting('give_url', '#'); ?>
        <a href="<?= htmlspecialchars($give_url) ?>"
           class="btn-give"
           id="giveBtn"
           <?= $give_url !== '#' ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
            Give Online
        </a>

        <!-- Hamburger -->
        <button class="hamburger" id="hamburgerBtn" aria-label="Open mobile menu" aria-expanded="false" aria-controls="mobileMenu">
            <i class="bx bx-menu"></i>
        </button>

    </div>
</header>

<!-- Mobile Overlay -->
<div class="mobile-overlay" id="mobileOverlay" aria-hidden="true"></div>

<!-- Mobile Slide-in Menu -->
<div class="mobile-menu" id="mobileMenu" role="dialog" aria-modal="true" aria-label="Mobile navigation">
    <div class="mobile-menu-top">
        <span class="mobile-brand"><?= htmlspecialchars(get_setting('site_name', 'CAC Achievers House')) ?></span>
        <button class="close" id="closeMenu" aria-label="Close mobile menu">
            <i class="bx bx-x"></i>
        </button>
    </div>

    <ul>
        <li><a href="<?= $base ?>index.php"      <?= $current === 'index.php'       ? 'style="color:#fff;background:rgba(0,71,171,0.25)"' : '' ?>>
            <i class="bx bx-home-alt"></i> Home</a></li>
        <li><a href="<?= $base ?>about-us.php"   <?= $current === 'about-us.php'    ? 'style="color:#fff;background:rgba(0,71,171,0.25)"' : '' ?>>
            <i class="bx bx-info-circle"></i> About Us</a></li>
        <li><a href="<?= $base ?>ministries.php" <?= $current === 'ministries.php'  ? 'style="color:#fff;background:rgba(0,71,171,0.25)"' : '' ?>>
            <i class="bx bx-crown"></i> Ministries</a></li>
        <li><a href="<?= $base ?>sermons.php"    <?= $current === 'sermons.php'     ? 'style="color:#fff;background:rgba(0,71,171,0.25)"' : '' ?>>
            <i class="bx bx-headphone"></i> Sermons</a></li>
        <li><a href="<?= $base ?>events.php"     <?= $current === 'events.php'      ? 'style="color:#fff;background:rgba(0,71,171,0.25)"' : '' ?>>
            <i class="bx bx-calendar-event"></i> Events</a></li>
        <li><a href="<?= $base ?>contact.php"    <?= $current === 'contact.php'     ? 'style="color:#fff;background:rgba(0,71,171,0.25)"' : '' ?>>
            <i class="bx bx-envelope"></i> Contact</a></li>
    </ul>

    <a href="<?= htmlspecialchars($give_url) ?>"
       class="btn-give-2"
       <?= $give_url !== '#' ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
        Give Online <i class="bx bx-heart"></i>
    </a>
</div>

<!-- Navbar JS -->
<script>
(function(){
    const header       = document.getElementById('header');
    const hamburger    = document.getElementById('hamburgerBtn');
    const mobileMenu   = document.getElementById('mobileMenu');
    const overlay      = document.getElementById('mobileOverlay');
    const closeBtn     = document.getElementById('closeMenu');

    /* scroll effect */
    const onScroll = () => {
        header.classList.toggle('scrolled', window.scrollY > 50);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    /* open / close */
    const openMenu = () => {
        mobileMenu.classList.add('active');
        overlay.classList.add('active');
        hamburger.setAttribute('aria-expanded', 'true');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    };
    const closeMenu = () => {
        mobileMenu.classList.remove('active');
        overlay.classList.remove('active');
        hamburger.setAttribute('aria-expanded', 'false');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    };

    hamburger.addEventListener('click', openMenu);
    closeBtn.addEventListener('click', closeMenu);
    overlay.addEventListener('click', closeMenu);

    /* close on Escape */
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });
})();
</script>
