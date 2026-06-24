<?php
// ============================================================
// SINGLE EVENT PAGE  —  single-event.php
// Premium $10,000 template layout
// ============================================================
require_once 'includes/config.php';
$base = defined('BASE_URL') ? BASE_URL : '/';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) { header('Location: events.php'); exit; }

$stmt = $conn->prepare('SELECT * FROM events WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) { header('Location: events.php'); exit; }
$ev = $result->fetch_assoc();
$stmt->close();

// ── Format dates / times ──────────────────────────────────
$sd        = strtotime($ev['start_date']);
$ed        = !empty($ev['end_date'])  ? strtotime($ev['end_date'])  : null;
$st        = !empty($ev['start_time']) && $ev['start_time'] !== '00:00:00' ? strtotime($ev['start_time']) : null;
$et        = !empty($ev['end_time'])   && $ev['end_time']   !== '00:00:00' ? strtotime($ev['end_time'])   : null;

$date_main = date('l, F j, Y', $sd);
$date_end  = ($ed && date('Y-m-d', $ed) !== date('Y-m-d', $sd)) ? ' – ' . date('F j, Y', $ed) : '';
$date_full = $date_main . $date_end;

$time_str  = $st ? date('g:i A', $st) . ($et ? ' – ' . date('g:i A', $et) : '') : null;

$venue     = trim($ev['venue_name'] ?? '') ?: 'CAC Achievers House';

// ── Hero image ────────────────────────────────────────────
$hero_img  = $base . 'assets/images/bg.jpg';
if (!empty($ev['image'])) {
    $hero_img = $base . ltrim($ev['image'], '/');
}

// ── Other upcoming events for sidebar ─────────────────────
$others = [];
$res2 = $conn->prepare('SELECT id, title, start_date, event_type FROM events WHERE id != ? ORDER BY start_date ASC LIMIT 3');
$res2->bind_param('i', $id);
$res2->execute();
$res2->bind_result($oid, $otitle, $odate, $otype);
while ($res2->fetch()) { $others[] = compact('oid','otitle','odate','otype'); }
$res2->close();

// ── SEO ───────────────────────────────────────────────────
$seo = [
    'title'       => htmlspecialchars($ev['title']),
    'description' => mb_substr(strip_tags($ev['description'] ?? ''), 0, 155),
    'url'         => 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/cac/single-event.php?id=' . $id,
];
include 'includes/header.php';
?>
<body>
<?php include 'includes/site-header.php'; ?>

<!-- ══════════════════════════════════════════════════════════
     HERO BANNER
════════════════════════════════════════════════════════════ -->
<section class="sev-hero">
    <div class="sev-hero-bg" style="background-image:url('<?= htmlspecialchars($hero_img) ?>')"></div>
    <div class="sev-hero-overlay"></div>
    <div class="sev-hero-inner">
        <a href="events.php" class="sev-back"><i class='bx bx-arrow-back'></i> All Events</a>
        <div class="sev-hero-badges">
            <span class="sev-type-badge"><?= htmlspecialchars($ev['event_type'] ?? 'Event') ?></span>
            <span class="sev-status-badge sev-status-<?= htmlspecialchars($ev['status'] ?? 'upcoming') ?>"><?= ucfirst($ev['status'] ?? 'upcoming') ?></span>
        </div>
        <h1 class="sev-hero-title"><?= htmlspecialchars($ev['title']) ?></h1>
        <div class="sev-hero-meta">
            <div class="sev-meta-pill">
                <i class='bx bx-calendar-alt'></i>
                <span><?= $date_full ?></span>
            </div>
            <?php if ($time_str): ?>
            <div class="sev-meta-pill">
                <i class='bx bx-time-five'></i>
                <span><?= $time_str ?></span>
            </div>
            <?php endif; ?>
            <div class="sev-meta-pill">
                <i class='bx bx-map-pin'></i>
                <span><?= htmlspecialchars($venue) ?></span>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════
     EVENT FLYER / IMAGE (Full size display)
════════════════════════════════════════════════════════════ -->
<?php if (!empty($ev['image'])): ?>
<section class="sev-flyer-section">
    <div class="sev-flyer-inner">
        <p class="sev-flyer-label"><i class='bx bx-image-alt'></i> Event Flyer</p>
        <div class="sev-flyer-img-wrap" onclick="openLightbox('<?= $base . htmlspecialchars(ltrim($ev['image'], '/')) ?>')" title="Click to zoom">
            <img class="sev-flyer-img"
                 src="<?= $base . htmlspecialchars(ltrim($ev['image'], '/')) ?>"
                 alt="<?= htmlspecialchars($ev['title']) ?> — Event Flyer"
                 loading="lazy">
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════
     CONTENT AREA
════════════════════════════════════════════════════════════ -->
<section class="sev-content-section">
    <div class="sev-content-container">

        <!-- ── LEFT COLUMN: DESCRIPTION ── -->
        <main class="sev-main-col">
            <div class="sev-card sev-description-card">
                <div class="sev-card-header">
                    <span class="sev-card-icon"><i class='bx bx-book-open'></i></span>
                    <h2>About This Event</h2>
                </div>
                <div class="sev-description-body">
                    <?php
                    $desc = htmlspecialchars($ev['description'] ?? '');
                    // Render newlines as proper HTML paragraphs
                    $paragraphs = preg_split('/\r?\n\r?\n/', trim($desc));
                    foreach ($paragraphs as $para) {
                        $para = trim(nl2br($para));
                        if ($para) echo '<p>' . $para . '</p>';
                    }
                    ?>
                </div>
            </div>

            <!-- Programme schedule note if no structured data -->
            <div class="sev-card sev-location-card">
                <div class="sev-card-header">
                    <span class="sev-card-icon"><i class='bx bx-map'></i></span>
                    <h2>Location</h2>
                </div>
                <div class="sev-location-body">
                    <p class="sev-location-name"><?= htmlspecialchars($venue) ?></p>
                    <a href="https://maps.google.com/?q=<?= urlencode($venue) ?>" target="_blank" rel="noopener" class="sev-map-link">
                        <i class='bx bx-directions'></i> Get Directions
                    </a>
                </div>
            </div>
        </main>

        <!-- ── RIGHT COLUMN: SIDEBAR ── -->
        <aside class="sev-sidebar-col">

            <!-- Details Card -->
            <div class="sev-card sev-details-card">
                <div class="sev-card-header">
                    <span class="sev-card-icon"><i class='bx bx-info-circle'></i></span>
                    <h3>Event Details</h3>
                </div>
                <ul class="sev-detail-list">
                    <li>
                        <div class="sev-dl-icon"><i class='bx bx-calendar'></i></div>
                        <div>
                            <span class="sev-dl-label">Date</span>
                            <span class="sev-dl-value"><?= $date_full ?></span>
                        </div>
                    </li>
                    <?php if ($time_str): ?>
                    <li>
                        <div class="sev-dl-icon"><i class='bx bx-time'></i></div>
                        <div>
                            <span class="sev-dl-label">Time</span>
                            <span class="sev-dl-value"><?= $time_str ?></span>
                        </div>
                    </li>
                    <?php endif; ?>
                    <li>
                        <div class="sev-dl-icon"><i class='bx bx-map-pin'></i></div>
                        <div>
                            <span class="sev-dl-label">Venue</span>
                            <span class="sev-dl-value"><?= htmlspecialchars($venue) ?></span>
                        </div>
                    </li>
                    <?php if (!empty($ev['event_type'])): ?>
                    <li>
                        <div class="sev-dl-icon"><i class='bx bx-category'></i></div>
                        <div>
                            <span class="sev-dl-label">Type</span>
                            <span class="sev-dl-value"><?= htmlspecialchars($ev['event_type']) ?></span>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>

                <div class="sev-actions">
                    <a href="contact.php" class="sev-btn sev-btn-primary">
                        <i class='bx bx-envelope'></i> Register / RSVP
                    </a>
                    <button class="sev-btn sev-btn-ghost" id="shareBtn">
                        <i class='bx bx-share-alt'></i> Share Event
                    </button>
                </div>
            </div>

            <!-- Other Events -->
            <?php if (!empty($others)): ?>
            <div class="sev-card sev-other-card">
                <div class="sev-card-header">
                    <span class="sev-card-icon"><i class='bx bx-calendar-week'></i></span>
                    <h3>More Events</h3>
                </div>
                <div class="sev-other-list">
                    <?php foreach ($others as $o): ?>
                    <a href="single-event.php?id=<?= $o['oid'] ?>" class="sev-other-item">
                        <div class="sev-other-dot"></div>
                        <div>
                            <p class="sev-other-title"><?= htmlspecialchars($o['otitle']) ?></p>
                            <span class="sev-other-date"><?= date('M j, Y', strtotime($o['odate'])) ?></span>
                        </div>
                        <i class='bx bx-chevron-right sev-other-arrow'></i>
                    </a>
                    <?php endforeach; ?>
                    <a href="events.php" class="sev-all-link">View all events <i class='bx bx-right-arrow-alt'></i></a>
                </div>
            </div>
            <?php endif; ?>

        </aside>
    </div>
</section>

<!-- Lightbox -->
<div class="sev-lightbox" id="sevLightbox" onclick="closeLightbox()">
    <button class="sev-lightbox-close" onclick="closeLightbox()" aria-label="Close"><i class='bx bx-x'></i></button>
    <img id="sevLightboxImg" src="" alt="Event Flyer">
</div>

<?php include 'includes/footer.php'; ?>

<script>
// ── Share Button ──────────────────────────────────────────
document.getElementById('shareBtn').addEventListener('click', function() {
    if (navigator.share) {
        navigator.share({ title: <?= json_encode($ev['title']) ?>, url: window.location.href });
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            this.innerHTML = '<i class="bx bx-check"></i> Link Copied!';
            setTimeout(() => { this.innerHTML = '<i class="bx bx-share-alt"></i> Share Event'; }, 2000);
        });
    }
});

// ── Lightbox ─────────────────────────────────────────────
function openLightbox(src) {
    document.getElementById('sevLightboxImg').src = src;
    document.getElementById('sevLightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('sevLightbox').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>
</body>
</html>
