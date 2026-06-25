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

// Fetch ministries for dropdown (graceful fallback if table missing)
$nav_ministries = [];
$nav_m_res = @$conn->query("SELECT slug, name, icon FROM `ministries` WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 10");
if ($nav_m_res && $nav_m_res->num_rows > 0) {
    while ($nm = $nav_m_res->fetch_assoc()) {
        $nav_ministries[] = $nm;
    }
}
// Fallback hardcoded if DB not seeded yet
if (empty($nav_ministries)) {
    $nav_ministries = [
        ['slug' => 'youth-ministry',       'name' => 'Youth Ministry',        'icon' => 'bx bx-meteor'],
        ['slug' => 'childrens-church',      'name' => "Children's Church",     'icon' => 'bx bx-book-heart'],
        ['slug' => 'womens-fellowship',     'name' => "Women's Fellowship",    'icon' => 'bx bx-crown'],
        ['slug' => 'evangelism-committee',  'name' => 'Evangelism Committee',  'icon' => 'bx bx-world'],
    ];
}

// Is any ministry page active?
$ministry_active = ($current === 'ministry.php');
?>
<header id="header" role="banner">
    <div class="nav-container">

        <!-- Logo (dynamic from Site Settings) -->
        <div class="logo">
            <a href="<?= $current === 'index.php' ? '#home' : $base ?>" class="logo-link">
                <img src="<?= $base . htmlspecialchars(ltrim(get_setting('logo_path', 'assets/logo/cac-logo.png'), '/')) ?>"
                     alt="<?= htmlspecialchars(get_setting('site_name', 'CAC Achievers House')) ?> Logo"
                     class="site-logo-img"
                     onerror="this.style.display='none'">
                <span class="site-title-text"><?= htmlspecialchars(get_setting('site_name', 'CAC Achievers House')) ?></span>
            </a>
        </div>

        <!-- Desktop Nav -->
        <nav role="navigation" aria-label="Main navigation">
            <ul>
                <li><a href="<?= $base ?>"      <?= $current === 'index.php'    ? 'class="active"' : '' ?>>Home</a></li>
                <li><a href="<?= $base ?>about-us.php"   <?= $current === 'about-us.php' ? 'class="active"' : '' ?>>About Us</a></li>

                <!-- ── Ministries Dropdown ── -->
                <li class="nav-dropdown" id="desktopDropdown">
                    <a href="javascript:void(0)"
                       <?= $ministry_active ? 'class="active"' : '' ?>
                       aria-haspopup="true" aria-expanded="false" id="desktopDropdownToggle">
                        Ministries
                        <span class="nav-dropdown-arrow"><i class="bx bx-chevron-down"></i></span>
                    </a>
                    <div class="nav-dropdown-menu" role="menu">
                        <?php foreach ($nav_ministries as $nm): ?>
                        <a href="<?= $base ?>ministry.php?slug=<?= urlencode($nm['slug']) ?>"
                           <?= (isset($_GET['slug']) && $_GET['slug'] === $nm['slug']) ? 'style="color:#fff !important"' : '' ?>
                           role="menuitem">
                            <i class="<?= htmlspecialchars($nm['icon']) ?>"></i>
                            <?= htmlspecialchars($nm['name']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </li>
                <!-- ── End Dropdown ── -->

                <li><a href="<?= $base ?>sermons.php"    <?= $current === 'sermons.php'  ? 'class="active"' : '' ?>>Sermons</a></li>
                <li><a href="<?= $base ?>events.php"     <?= $current === 'events.php'   ? 'class="active"' : '' ?>>Events</a></li>
                <li><a href="<?= $base ?>gallery.php"    <?= $current === 'gallery.php'  ? 'class="active"' : '' ?>>Gallery</a></li>
                <li><a href="<?= $base ?>contact.php"    <?= $current === 'contact.php'  ? 'class="active"' : '' ?>>Contact</a></li>
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
        <li><a href="<?= $base ?>"    <?= $current === 'index.php'    ? 'style="color:#fff;background:rgba(0,71,171,0.25)"' : '' ?>>
            <i class="bx bx-home-alt"></i> Home</a></li>
        <li><a href="<?= $base ?>about-us.php" <?= $current === 'about-us.php' ? 'style="color:#fff;background:rgba(0,71,171,0.25)"' : '' ?>>
            <i class="bx bx-info-circle"></i> About Us</a></li>

        <!-- ── Mobile Ministries Accordion ── -->
        <li>
            <button class="mobile-dropdown-toggle" id="mobileMinBtn" aria-expanded="false" aria-controls="mobileMinSubMenu">
                <span style="display:flex;align-items:center;gap:.8rem;">
                    <i class="bx bx-crown" style="color:var(--accent-gold,#f5c518);font-size:1.1rem;"></i>
                    Ministries
                </span>
                <i class="bx bx-chevron-down toggle-icon"></i>
            </button>
            <div class="mobile-submenu" id="mobileMinSubMenu" aria-hidden="true">
                <?php foreach ($nav_ministries as $nm): ?>
                <a href="<?= $base ?>ministry.php?slug=<?= urlencode($nm['slug']) ?>">
                    <i class="<?= htmlspecialchars($nm['icon']) ?>"></i>
                    <?= htmlspecialchars($nm['name']) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </li>
        <!-- ── End Mobile Dropdown ── -->

        <li><a href="<?= $base ?>sermons.php"  <?= $current === 'sermons.php'  ? 'style="color:#fff;background:rgba(0,71,171,0.25)"' : '' ?>>
            <i class="bx bx-headphone"></i> Sermons</a></li>
        <li><a href="<?= $base ?>events.php"   <?= $current === 'events.php'   ? 'style="color:#fff;background:rgba(0,71,171,0.25)"' : '' ?>>
            <i class="bx bx-calendar-event"></i> Events</a></li>
        <li><a href="<?= $base ?>gallery.php"  <?= $current === 'gallery.php'  ? 'style="color:#fff;background:rgba(0,71,171,0.25)"' : '' ?>>
            <i class="bx bx-images"></i> Gallery</a></li>
        <li><a href="<?= $base ?>contact.php"  <?= $current === 'contact.php'  ? 'style="color:#fff;background:rgba(0,71,171,0.25)"' : '' ?>>
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

    /* open / close mobile menu */
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
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });

    /* ── Mobile Ministries accordion ── */
    const mobileMinBtn    = document.getElementById('mobileMinBtn');
    const mobileMinSub    = document.getElementById('mobileMinSubMenu');

    if (mobileMinBtn && mobileMinSub) {
        mobileMinBtn.addEventListener('click', () => {
            const isOpen = mobileMinSub.classList.toggle('open');
            mobileMinBtn.classList.toggle('open', isOpen);
            mobileMinBtn.setAttribute('aria-expanded', isOpen);
            mobileMinSub.setAttribute('aria-hidden', !isOpen);
        });
    }

    /* ── Desktop dropdown: click toggle ── */
    const desktopDropdown = document.getElementById('desktopDropdown');
    const desktopToggle   = document.getElementById('desktopDropdownToggle');

    if (desktopDropdown && desktopToggle) {
        desktopToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = desktopDropdown.classList.toggle('open');
            desktopToggle.setAttribute('aria-expanded', isOpen);
        });

        desktopToggle.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const isOpen = desktopDropdown.classList.toggle('open');
                desktopToggle.setAttribute('aria-expanded', isOpen);
                if (isOpen) {
                    const firstLink = desktopDropdown.querySelector('.nav-dropdown-menu a');
                    if (firstLink) firstLink.focus();
                }
            }
        });
    }
})();
</script>
