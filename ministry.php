<?php
// ============================================================
// INDIVIDUAL MINISTRY PAGE
// ministry.php  —  Accepts ?slug=youth-ministry etc.
// ============================================================

require_once 'includes/config.php';

// ── Fetch ministry by slug ────────────────────────────────────
$slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($_GET['slug'] ?? ''));

if (empty($slug)) {
    header('Location: ./');
    exit;
}

// Check ministries table exists, then fetch
$ministry = null;
$res = @$conn->query(
    "SELECT * FROM `ministries` WHERE slug = '" . $conn->real_escape_string($slug) . "' AND is_active = 1 LIMIT 1"
);
if ($res && $res->num_rows > 0) {
    $ministry = $res->fetch_assoc();
}

if (!$ministry) {
    // Unknown slug — soft redirect
    header('Location: ./');
    exit;
}

// Parse schedule JSON
$schedule = [];
if (!empty($ministry['schedule'])) {
    $schedule = json_decode($ministry['schedule'], true) ?: [];
}



// ── Fetch gallery photos for this ministry ────────────────────
$photos = [];
$stmt = $conn->prepare("SELECT * FROM `gallery` WHERE category = ? ORDER BY uploaded_at DESC LIMIT 24");
if ($stmt) {
    $stmt->bind_param('s', $ministry['name']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $photos[] = $row;
    }
}

// ── SEO ───────────────────────────────────────────────────────
$seo = [
    'title'       => $ministry['name'],
    'description' => strip_tags($ministry['tagline'] ?? $ministry['description'] ?? "Learn about our {$ministry['name']} at CAC Achievers House."),
    'url'         => 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/cac/ministry.php?slug=' . $slug,
];

include 'includes/header.php';
?>
<body>
<?php include 'includes/site-header.php'; ?>

<?php
// Ministry color CSS variable per page
$m_color = htmlspecialchars($ministry['color'] ?? '#f97316');
$m_color_dark = $m_color; // Could compute a darker shade; keep same for simplicity
?>
<style>
    :root { --ministry-color: <?= $m_color ?>; }
</style>

<!-- ── MINISTRY HERO ──────────────────────────────────────────── -->
<section class="ministry-hero">
    <div class="ministry-hero-bg"
         style="background-image: url('<?= $base . ltrim(!empty($ministry['hero_bg']) ? htmlspecialchars($ministry['hero_bg']) : "assets/images/bg.jpg", '/') ?>');">
    </div>
    <div class="ministry-hero-overlay"
         style="background: linear-gradient(180deg, rgba(10,15,40,0.88) 0%, <?= $m_color ?>33 40%, rgba(6,10,28,0.92) 100%);">
    </div>
    <div class="ministry-hero-content">
        <?php if (!empty($ministry['badge_text'])): ?>
        <span class="ministry-badge">
            <i class="<?= htmlspecialchars($ministry['icon']) ?>"></i>
            <?= htmlspecialchars($ministry['badge_text']) ?>
        </span>
        <?php endif; ?>
        <h1><?= htmlspecialchars($ministry['name']) ?></h1>
        <?php if (!empty($ministry['tagline'])): ?>
        <p><?= htmlspecialchars($ministry['tagline']) ?></p>
        <?php endif; ?>
        <nav class="ministry-breadcrumb" aria-label="Breadcrumb">
            <a href="./">Home</a>
            <span>›</span>
            <span>Ministries</span>
            <span>›</span>
            <span><?= htmlspecialchars($ministry['name']) ?></span>
        </nav>
    </div>
</section>

<!-- ── ABOUT SECTION ─────────────────────────────────────────── -->
<section class="ministry-about">
    <div class="ministry-about-inner">

        <!-- Description -->
        <div class="ministry-about-text">
            <h2>About <span class="highlight"><?= htmlspecialchars($ministry['name']) ?></span></h2>
            <?php
            // Split description into paragraphs
            $paras = array_filter(explode("\n\n", trim($ministry['description'] ?? '')));
            if (empty($paras)) {
                echo '<p>' . htmlspecialchars($ministry['description'] ?? '') . '</p>';
            } else {
                foreach ($paras as $p) {
                    echo '<p>' . nl2br(htmlspecialchars(trim($p))) . '</p>';
                }
            }
            ?>

            <a href="contact.php" class="btn-primary" style="display:inline-flex;align-items:center;gap:.5rem;margin-top:1rem;
               background:<?= $m_color ?>;color:#fff;padding:.8rem 1.8rem;border-radius:50px;font-weight:700;
               text-decoration:none;font-size:.92rem;box-shadow:0 4px 20px <?= $m_color ?>55;
               transition:transform .2s,box-shadow .2s;"
               onmouseover="this.style.transform='translateY(-3px)'"
               onmouseout="this.style.transform=''">
                Join This Ministry <i class="bx bx-right-arrow-alt"></i>
            </a>
        </div>

        <!-- Schedule Card -->
        <aside class="ministry-schedule-card">
            <h3><i class="bx bx-calendar-check"></i> Schedule & Meetings</h3>

            <?php if (!empty($schedule)): ?>
            <div class="schedule-items">
                <?php foreach ($schedule as $item): ?>
                <div class="schedule-item">
                    <div class="schedule-item-dot" style="background:<?= $m_color ?>;"></div>
                    <div>
                        <div class="schedule-item-label"><?= htmlspecialchars($item['label'] ?? '') ?></div>
                        <div class="schedule-item-detail"><?= htmlspecialchars($item['detail'] ?? '') ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="color:#94a3b8;font-size:.9rem;">Schedule details coming soon.</p>
            <?php endif; ?>

        </aside>

    </div>
</section>

<!-- ── GALLERY SECTION ───────────────────────────────────────── -->
<section class="ministry-gallery-section">
    <div class="section-inner">
        <div class="section-heading">
            <span class="section-badge" style="background:<?= $m_color ?>18;color:<?= $m_color ?>;border:1px solid <?= $m_color ?>33;">
                <i class="bx bx-images"></i> OUR MOMENTS
            </span>
            <h2><?= htmlspecialchars($ministry['name']) ?> Gallery</h2>
            <p>A snapshot of our community, events and special moments.</p>
        </div>

        <?php if (empty($photos)): ?>
        <div class="ministry-gallery-empty">
            <i class="bx bx-image-add"></i>
            <h3>No photos yet</h3>
            <p>Check back soon — our gallery is being updated with memories!</p>
        </div>
        <?php else: ?>
        <div class="ministry-gallery-grid" id="mgGallery">
            <?php foreach ($photos as $photo):
                $src = $base . 'assets/gallery/' . htmlspecialchars($photo['filename']);
            ?>
            <div class="ministry-gallery-item"
                 data-src="<?= $src ?>"
                 data-caption="<?= htmlspecialchars($photo['caption'] ?? '') ?>"
                 tabindex="0" role="button"
                 aria-label="View photo: <?= htmlspecialchars($photo['caption'] ?? $photo['filename']) ?>">
                <img src="<?= $src ?>" alt="<?= htmlspecialchars($photo['caption'] ?? $ministry['name'] . ' photo') ?>" loading="lazy">
                <div class="ministry-gallery-item-overlay">
                    <span><?= htmlspecialchars($photo['caption'] ?? '') ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center;margin-top:2.5rem;">
            <a href="gallery.php?cat=<?= urlencode($ministry['name']) ?>"
               style="display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.8rem;border-radius:50px;
                      background:transparent;border:2px solid <?= $m_color ?>;color:<?= $m_color ?>;font-weight:700;
                      font-size:.88rem;text-decoration:none;transition:background .2s,color .2s;"
               onmouseover="this.style.background='<?= $m_color ?>';this.style.color='#fff'"
               onmouseout="this.style.background='transparent';this.style.color='<?= $m_color ?>'">
                View All Photos <i class="bx bx-right-arrow-alt"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ── CTA ───────────────────────────────────────────────────── -->
<section class="ministry-cta">
    <h2>Ready to Join <?= htmlspecialchars($ministry['name']) ?>?</h2>
    <p>Whether you're a long-time member or brand new to our church family, there's a place for you here. Come as you are.</p>
    <a href="contact.php" class="btn-primary">
        Get In Touch <i class="bx bx-envelope"></i>
    </a>
</section>

<!-- ── LIGHTBOX ──────────────────────────────────────────────── -->
<div class="lightbox" id="mgLightbox" role="dialog" aria-modal="true" aria-label="Photo viewer" style="
    background:rgba(0,0,0,0.95);
    flex-direction:column;">
    <button onclick="closeMgLightbox()" aria-label="Close" style="position:absolute;top:1.5rem;right:1.5rem;
        background:rgba(255,255,255,0.1);border:none;color:#fff;font-size:2rem;width:48px;height:48px;
        border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;">
        <i class="bx bx-x"></i>
    </button>
    <button onclick="mgPrev()" aria-label="Previous" style="position:absolute;left:1.5rem;top:50%;transform:translateY(-50%);
        background:rgba(255,255,255,0.1);border:none;color:#fff;font-size:2rem;width:52px;height:52px;
        border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;">
        <i class="bx bx-chevron-left"></i>
    </button>
    <img id="mgLightboxImg" src="" alt="" style="max-width:90vw;max-height:85vh;object-fit:contain;border-radius:12px;">
    <p id="mgLightboxCaption" style="color:rgba(255,255,255,0.7);margin-top:1rem;font-size:.9rem;max-width:600px;text-align:center;"></p>
    <button onclick="mgNext()" aria-label="Next" style="position:absolute;right:1.5rem;top:50%;transform:translateY(-50%);
        background:rgba(255,255,255,0.1);border:none;color:#fff;font-size:2rem;width:52px;height:52px;
        border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;">
        <i class="bx bx-chevron-right"></i>
    </button>
</div>

<script>
const mgItems = Array.from(document.querySelectorAll('.ministry-gallery-item'));
let mgCurrent = 0;
const mgLb = document.getElementById('mgLightbox');
const mgImg = document.getElementById('mgLightboxImg');
const mgCap = document.getElementById('mgLightboxCaption');

function openMgLightbox(i) {
    if (mgItems.length === 0) return;
    if (i < 0) i = mgItems.length - 1;
    if (i >= mgItems.length) i = 0;

    mgCurrent = i;
    mgImg.src = mgItems[i].dataset.src;
    mgImg.alt = mgItems[i].dataset.caption || '';
    mgCap.textContent = mgItems[i].dataset.caption || '';
    mgLb.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeMgLightbox() {
    mgLb.classList.remove('active');
    document.body.style.overflow = '';
}

function mgPrev() { openMgLightbox(mgCurrent - 1); }
function mgNext() { openMgLightbox(mgCurrent + 1); }

mgItems.forEach((item, i) => {
    item.addEventListener('click', () => openMgLightbox(i));
    item.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') openMgLightbox(i); });
});

document.addEventListener('keydown', e => {
    if (!mgLb.classList.contains('active')) return;
    if (e.key === 'Escape')     closeMgLightbox();
    if (e.key === 'ArrowLeft')  mgPrev();
    if (e.key === 'ArrowRight') mgNext();
});

// Click backdrop to close
mgLb.addEventListener('click', e => { if (e.target === mgLb) closeMgLightbox(); });
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
