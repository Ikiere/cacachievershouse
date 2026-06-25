<?php
$seo = [
    'title'       => 'Gallery',
    'description' => 'Browse photos from worship services, events, outreach programmes, and community life at CAC Achievers House.',
    'url'         => 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/cac/gallery.php',
];
include 'includes/header.php';
include 'includes/config.php';

// Fetch gallery images from DB
$images = [];
$result = $conn->query("SELECT * FROM gallery ORDER BY uploaded_at DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
}

// Collect unique categories
$categories = ['All'];
foreach ($images as $img) {
    if ($img['category'] && !in_array($img['category'], $categories)) {
        $categories[] = $img['category'];
    }
}

// Pre-selected category from ?cat= query param (e.g. from ministry pages)
$preselect_cat = htmlspecialchars($_GET['cat'] ?? 'All');
if (!in_array($preselect_cat, $categories)) { $preselect_cat = 'All'; }
?>
<body>
<?php include 'includes/site-header.php'; ?>

<!-- ── PAGE HERO ── -->
<section class="page-hero" style="background-image:url('<?= $base . ltrim('assets/images/bg.jpg', '/') ?>');">
    <div class="page-hero-overlay"></div>
    <div class="page-hero-content">
        <span class="hero-badge"><i class="bx bx-images"></i> OUR MOMENTS</span>
        <h1>Photo <span class="highlight">Gallery</span></h1>
        <p>Capturing the moments where faith, community, and joy come alive at CAC Achievers House.</p>
    </div>
</section>

<!-- ── GALLERY ── -->
<section class="page-section bg-page bg-grid-pattern" style="position: relative; overflow: hidden;" aria-labelledby="gallery-heading">
    <?php render_motion_shapes(['red' => 'top: 10%; right: -5%; opacity: 0.12;', 'gold' => 'bottom: 15%; left: -5%; opacity: 0.12;', 'blue' => 'top: 40%; left: 30%; opacity: 0.08;']); ?>
    <div class="page-container" style="position: relative; z-index: 1;">

    <div class="section-header text-center">
        <span class="section-badge"><i class="bx bx-camera"></i> MOMENTS & MEMORIES</span>
        <h2 id="gallery-heading">Life at CAC Achievers House</h2>
        <p class="section-subtitle">Browse photos from our services, events, outreach programmes, and community life.</p>
    </div>

    <?php if (!empty($categories) && count($categories) > 1): ?>
    <!-- Filter Tabs -->
    <div class="gallery-filters" id="galleryFilters">
        <?php foreach ($categories as $cat): ?>
        <button class="gallery-filter-btn <?= $cat === $preselect_cat ? 'active' : '' ?>"
                data-filter="<?= htmlspecialchars($cat) ?>">
            <?= htmlspecialchars($cat) ?>
        </button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($images)): ?>
    <div class="gallery-empty">
        <i class="bx bx-image-add"></i>
        <h3>No photos yet</h3>
        <p>Check back soon — our gallery is being filled with memories!</p>
    </div>

    <?php else: ?>
    <!-- Gallery Grid -->
    <div class="gallery-grid" id="galleryGrid">
        <?php foreach ($images as $i => $img): ?>
        <?php $src = $base . 'assets/gallery/' . htmlspecialchars($img['filename']); ?>
        <div class="gallery-item reveal <?= $i % 4 === 0 ? '' : 'reveal-delay-' . ($i % 3 + 1) ?>"
             data-category="<?= htmlspecialchars($img['category']) ?>"
             data-src="<?= $src ?>"
             data-caption="<?= htmlspecialchars($img['caption'] ?? '') ?>"
             tabindex="0"
             role="button"
             aria-label="View photo: <?= htmlspecialchars($img['caption'] ?? $img['filename']) ?>">
            <img src="<?= $src ?>"
                 alt="<?= htmlspecialchars($img['caption'] ?? 'CAC Achievers House photo') ?>"
                 loading="lazy">
            <div class="gallery-item-overlay">
                <i class="bx bx-zoom-in"></i>
                <?php if (!empty($img['caption'])): ?>
                <span><?= htmlspecialchars($img['caption']) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
</section>

<!-- ── LIGHTBOX ── -->
<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Photo viewer">
    <button class="lightbox-close" id="lightboxClose" aria-label="Close">&times;</button>
    <button class="lightbox-prev" id="lightboxPrev" aria-label="Previous photo">&#8249;</button>
    <button class="lightbox-next" id="lightboxNext" aria-label="Next photo">&#8250;</button>
    <div class="lightbox-inner">
        <img id="lightboxImg" src="" alt="">
        <p id="lightboxCaption" class="lightbox-caption"></p>
    </div>
</div>
<div class="lightbox-backdrop" id="lightboxBackdrop"></div>

<script>
/* ── GALLERY FILTER ── */
const filterBtns  = document.querySelectorAll('.gallery-filter-btn');
const galleryItems = document.querySelectorAll('.gallery-item');

filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const filter = btn.dataset.filter;
        galleryItems.forEach(item => {
            const match = filter === 'All' || item.dataset.category === filter;
            item.style.display = match ? '' : 'none';
        });
    });
});

// Run initial filter if ?cat= was passed
const initialFilter = '<?= $preselect_cat ?>';
if (initialFilter !== 'All') {
    galleryItems.forEach(item => {
        const match = item.dataset.category === initialFilter;
        item.style.display = match ? '' : 'none';
    });
    // Scroll to gallery section automatically
    document.getElementById('gallery-heading').scrollIntoView({ behavior: 'smooth' });
}

/* ── LIGHTBOX ── */
const lightbox        = document.getElementById('lightbox');
const lightboxBackdrop = document.getElementById('lightboxBackdrop');
const lightboxImg     = document.getElementById('lightboxImg');
const lightboxCaption = document.getElementById('lightboxCaption');
const lightboxClose   = document.getElementById('lightboxClose');
const lightboxPrev    = document.getElementById('lightboxPrev');
const lightboxNext    = document.getElementById('lightboxNext');

function getVisibleItems() {
    return Array.from(document.querySelectorAll('.gallery-item')).filter(item => item.style.display !== 'none');
}
let currentLightbox = 0;

function openLightbox(index) {
    const visibleItems = getVisibleItems();
    if (visibleItems.length === 0) return;
    if (index < 0) index = visibleItems.length - 1;
    if (index >= visibleItems.length) index = 0;

    const item = visibleItems[index];
    lightboxImg.src = item.dataset.src;
    lightboxImg.alt = item.dataset.caption || '';
    lightboxCaption.textContent = item.dataset.caption || '';
    currentLightbox = index;
    lightbox.classList.add('active');
    lightboxBackdrop.classList.add('active');
    document.body.style.overflow = 'hidden';
    lightboxPrev.style.display = visibleItems.length > 1 ? '' : 'none';
    lightboxNext.style.display = visibleItems.length > 1 ? '' : 'none';
}

function closeLightbox() {
    lightbox.classList.remove('active');
    lightboxBackdrop.classList.remove('active');
    document.body.style.overflow = '';
}

function prevPhoto() {
    openLightbox(currentLightbox - 1);
}

function nextPhoto() {
    openLightbox(currentLightbox + 1);
}

galleryItems.forEach((item) => {
    const handleOpen = () => {
        const visibleItems = getVisibleItems();
        const idx = visibleItems.indexOf(item);
        if (idx !== -1) openLightbox(idx);
    };
    item.addEventListener('click', handleOpen);
    item.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') handleOpen(); });
});

lightboxClose.addEventListener('click', closeLightbox);
lightboxBackdrop.addEventListener('click', closeLightbox);
lightboxPrev.addEventListener('click', prevPhoto);
lightboxNext.addEventListener('click', nextPhoto);

document.addEventListener('keydown', e => {
    if (!lightbox.classList.contains('active')) return;
    if (e.key === 'Escape')     closeLightbox();
    if (e.key === 'ArrowLeft')  prevPhoto();
    if (e.key === 'ArrowRight') nextPhoto();
});

/* ── SCROLL REVEAL ── */
if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); }});
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
} else {
    document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible'));
}
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
