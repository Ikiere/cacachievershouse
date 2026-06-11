<?php
$seo = [
    'title'       => 'Events',
    'description' => 'Upcoming events at CAC Achievers House — revival conferences, youth outreach, family fun days, and more. Join us!',
    'url'         => 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/cac/events.php',
    'type'        => 'website',
];
include 'includes/header.php';
include 'includes/config.php';

// Fetch upcoming events from DB
$events_db = [];
$result = $conn->query("SELECT * FROM events ORDER BY start_date ASC LIMIT 20");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events_db[] = $row;
    }
}

// Static fallback events
$static_events = [
    ['title' => 'Revival Conference 2025',     'description' => 'Three days of powerful worship, prophetic ministry, and spiritual renewal. Come ready for a breakthrough.',  'start_date' => '2025-03-15', 'end_date' => '2025-03-17', 'venue_name' => 'CAC Achievers House Auditorium', 'event_type' => 'Conference', 'status' => 'upcoming', 'image' => 'https://images.unsplash.com/photo-1507874457470-272b3c8d8ee2?w=800&q=80'],
    ['title' => 'Youth Outreach Program',       'description' => 'Community service and evangelism initiative reaching out to local neighborhoods with love and hope.',          'start_date' => '2025-03-08', 'end_date' => null,           'venue_name' => 'Various Community Centers',             'event_type' => 'Outreach',    'status' => 'upcoming', 'image' => ''],
    ['title' => 'Family Fun Day',               'description' => 'A day of games, food, and fellowship for the whole family to enjoy together in God\'s presence.',            'start_date' => '2025-03-22', 'end_date' => null,           'venue_name' => 'Church Grounds',                        'event_type' => 'Social',      'status' => 'upcoming', 'image' => ''],
    ['title' => 'Achievers Leadership Summit',  'description' => 'Equipping believers with tools to lead boldly in every sphere of life and influence.',                       'start_date' => '2025-04-05', 'end_date' => null,           'venue_name' => 'CAC Achievers House',                   'event_type' => 'Conference',  'status' => 'planning', 'image' => ''],
    ['title' => 'Annual Harvest Thanksgiving',  'description' => 'Come with a heart of gratitude as we celebrate God\'s faithfulness and provision over the year.',            'start_date' => '2025-11-30', 'end_date' => null,           'venue_name' => 'CAC Achievers House Auditorium',        'event_type' => 'Worship',     'status' => 'planning', 'image' => ''],
    ['title' => 'Night of Breakthrough',        'description' => 'An all-night prayer and worship session to end the year and step into the new year with victory.',           'start_date' => '2025-12-31', 'end_date' => null,           'venue_name' => 'CAC Achievers House',                   'event_type' => 'Prayer',      'status' => 'planning', 'image' => ''],
];

$events = !empty($events_db) ? $events_db : $static_events;

// Type icons map
$type_icons = ['Conference' => 'bx-microphone', 'Outreach' => 'bx-world', 'Social' => 'bx-smile', 'Prayer' => 'bx-heart', 'Worship' => 'bx-music', 'Default' => 'bx-calendar-event'];
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

<!-- ── EVENTS SECTION ── -->
<section class="page-section bg-dark-events" aria-labelledby="events-page-heading">
<div class="page-container">

    <div class="section-header text-center" style="color:#fff;">
        <span class="section-badge" style="background:rgba(245,158,11,.12);border-color:rgba(245,158,11,.3);color:#fbbf24;">
            <i class="bx bx-calendar"></i> SCHEDULED GATHERINGS
        </span>
        <h2 id="events-page-heading">All Events</h2>
        <p class="section-subtitle" style="color:rgba(203,213,225,.8);">Every gathering is an opportunity to encounter God and connect with community.</p>
    </div>

    <div class="events-page-grid">
        <?php foreach ($events as $i => $ev): ?>
        <?php
            $icon_key = $ev['event_type'] ?? 'Default';
            $icon     = $type_icons[$icon_key] ?? $type_icons['Default'];
            $date_fmt = date('M j, Y', strtotime($ev['start_date']));
            $end_fmt  = !empty($ev['end_date']) ? ' – ' . date('M j', strtotime($ev['end_date'])) : '';
            $status   = $ev['status'] ?? 'upcoming';
            $has_img  = !empty($ev['image']);
        ?>
        <article class="ev-card reveal <?= $i % 3 === 0 ? '' : 'reveal-delay-' . ($i % 3) ?>">
            <?php if ($has_img): ?>
            <div class="ev-card-img">
                <img src="<?= htmlspecialchars($ev['image']) ?>" alt="<?= htmlspecialchars($ev['title']) ?>" loading="lazy">
            </div>
            <?php else: ?>
            <div class="ev-card-img ev-card-img--icon">
                <i class="bx <?= $icon ?>"></i>
            </div>
            <?php endif; ?>

            <div class="ev-card-body">
                <div class="ev-card-meta">
                    <span class="ev-date"><i class="bx bx-calendar"></i> <?= $date_fmt . $end_fmt ?></span>
                    <span class="ev-status <?= $status ?>"><?= ucfirst($status) ?></span>
                </div>
                <h3><?= htmlspecialchars($ev['title']) ?></h3>
                <p><?= htmlspecialchars($ev['description'] ?? '') ?></p>
                <?php if (!empty($ev['venue_name'])): ?>
                <div class="ev-venue"><i class="bx bx-map-pin"></i> <?= htmlspecialchars($ev['venue_name']) ?></div>
                <?php endif; ?>
                <a href="contact.php" class="ev-link">Register Interest <i class="bx bx-right-arrow-alt"></i></a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

</div>
</section>

<!-- JSON-LD Event Schema for first event -->
<?php if (!empty($events)): $e = $events[0]; ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Event",
    "name": "<?= addslashes(htmlspecialchars($e['title'])) ?>",
    "startDate": "<?= $e['start_date'] ?>",
    "endDate": "<?= $e['end_date'] ?? $e['start_date'] ?>",
    "location": {
        "@type": "Place",
        "name": "<?= addslashes(htmlspecialchars($e['venue_name'] ?? 'CAC Achievers House')) ?>"
    },
    "organizer": {
        "@type": "Organization",
        "name": "CAC Achievers House"
    },
    "description": "<?= addslashes(htmlspecialchars($e['description'] ?? '')) ?>"
}
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
</body>
</html>
