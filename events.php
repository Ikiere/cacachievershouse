<?php
// ============================================================
// EVENTS PAGE
// events.php — loads all events from the database
// ============================================================
require_once 'includes/config.php';

$seo = [
    'title'       => 'Events',
    'description' => 'Upcoming events at CAC Achievers House — revival conferences, worship nights, outreach, and more. Join us!',
    'url'         => 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/cac/events.php',
];
include 'includes/header.php';

// ── Pagination Logic ──────────────────────────────────────────
$events_per_page = 9;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $events_per_page;

$total_res = $conn->query("SELECT COUNT(*) as total FROM events");
$total_events = $total_res ? $total_res->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_events / $events_per_page);

// ── Fetch paginated events from database ──────────────────────
$events = [];
$stmt = $conn->prepare("SELECT * FROM events ORDER BY start_date ASC LIMIT ? OFFSET ?");
if ($stmt) {
    $stmt->bind_param("ii", $events_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

$type_icons = [
    'Conference' => 'bx-microphone',
    'Outreach'   => 'bx-world',
    'Social'     => 'bx-smile',
    'Prayer'     => 'bx-heart',
    'Worship'    => 'bx-music',
    'Service'    => 'bx-church',
    'Fellowship' => 'bx-group',
    'Default'    => 'bx-calendar-event',
];
?>
<body>
<?php include 'includes/site-header.php'; ?>

<!-- ── PAGE HERO ── -->
<section class="page-hero" style="background-image:url('assets/images/bg.jpg');">
    <div class="page-hero-overlay"></div>
    <div class="page-hero-content">
        <span class="hero-badge"><i class="bx bx-calendar-event"></i> WHAT'S HAPPENING</span>
        <h1>Upcoming <span class="highlight">Events</span></h1>
        <p>Join us for these special gatherings and experience God's presence and power.</p>
    </div>
</section>

<!-- ── EVENTS GRID ── -->
<section class="bg-dark-events" style="padding: 5rem 0;" aria-labelledby="events-page-heading">
    <div class="page-container">

        <div class="section-header text-center" style="color:#fff; margin-bottom: 3rem;">
            <span class="section-badge" style="background:rgba(245,158,11,.12);border-color:rgba(245,158,11,.3);color:#fbbf24;">
                <i class="bx bx-calendar"></i> SCHEDULED GATHERINGS
            </span>
            <h2 id="events-page-heading">All Events</h2>
            <p class="section-subtitle" style="color:rgba(203,213,225,.8);">
                Every gathering is an opportunity to encounter God and connect with community.
            </p>
        </div>

        <?php if (empty($events)): ?>
            <!-- Empty State -->
            <div style="text-align:center; padding: 5rem 1rem; color: #94a3b8;">
                <i class='bx bx-calendar-x' style="font-size: 4rem; opacity: 0.4; display:block; margin-bottom:1rem;"></i>
                <p style="font-size: 1.2rem;">No events scheduled right now. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="events-page-grid">
                <?php foreach ($events as $i => $ev):
                    $icon_key = $ev['event_type'] ?? 'Default';
                    $icon     = $type_icons[$icon_key] ?? $type_icons['Default'];
                    $date_fmt = date('M j, Y', strtotime($ev['start_date']));
                    $end_fmt  = !empty($ev['end_date']) ? ' – ' . date('M j', strtotime($ev['end_date'])) : '';
                    $status   = $ev['status'] ?? 'upcoming';
                    $has_img  = !empty($ev['image']);
                    $desc     = htmlspecialchars(mb_substr($ev['description'] ?? '', 0, 120));
                    if (mb_strlen($ev['description'] ?? '') > 120) $desc .= '…';
                ?>
                <article class="ev-card">
                    <?php if ($has_img): ?>
                    <a href="single-event.php?id=<?= $ev['id'] ?>" class="ev-card-img">
                        <img src="<?= $base . htmlspecialchars(ltrim($ev['image'], '/')) ?>" alt="<?= htmlspecialchars($ev['title']) ?>" loading="lazy">
                    </a>
                    <?php else: ?>
                    <a href="single-event.php?id=<?= $ev['id'] ?>" class="ev-card-img ev-card-img--icon">
                        <i class="bx <?= $icon ?>"></i>
                    </a>
                    <?php endif; ?>

                    <div class="ev-card-body">
                        <div class="ev-card-meta">
                            <span class="ev-date"><i class="bx bx-calendar"></i> <?= $date_fmt . $end_fmt ?></span>
                            <span class="ev-status <?= htmlspecialchars($status) ?>"><?= ucfirst($status) ?></span>
                        </div>
                        <h3><?= htmlspecialchars($ev['title']) ?></h3>
                        <?php if ($desc): ?><p><?= $desc ?></p><?php endif; ?>
                        <?php if (!empty($ev['venue_name'])): ?>
                        <div class="ev-venue"><i class="bx bx-map-pin"></i> <?= htmlspecialchars($ev['venue_name']) ?></div>
                        <?php endif; ?>
                        <a href="single-event.php?id=<?= $ev['id'] ?>" class="ev-link">
                            View Details <i class="bx bx-right-arrow-alt"></i>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div style="display:flex;justify-content:center;gap:0.5rem;margin-top:4rem;">
                <?php if ($page > 1): ?>
                <a href="events.php?page=<?= $page - 1 ?>" style="padding:10px 18px;border-radius:8px;background:#1e293b;color:#fff;text-decoration:none;font-weight:600;"><i class='bx bx-chevron-left'></i> Prev</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="events.php?page=<?= $i ?>" style="padding:10px 18px;border-radius:8px;background:<?= $i === $page ? 'var(--primary,#0047AB)' : '#1e293b' ?>;color:#fff;text-decoration:none;font-weight:600;"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                <a href="events.php?page=<?= $page + 1 ?>" style="padding:10px 18px;border-radius:8px;background:#1e293b;color:#fff;text-decoration:none;font-weight:600;">Next <i class='bx bx-chevron-right'></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
