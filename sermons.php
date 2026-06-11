<?php
// ============================================================
// PUBLIC SERMONS PAGE
// sermons.php
// ============================================================
require_once 'includes/config.php';

// Filter params (sanitised)
$search  = trim($_GET['q']     ?? '');
$speaker = trim($_GET['speaker'] ?? '');
$series  = trim($_GET['series']  ?? '');
$page    = max(1, intval($_GET['page'] ?? 1));
$perPage = 9;
$offset  = ($page - 1) * $perPage;

// Build query
$where   = ['1=1'];
$params  = [];
$types   = '';

if ($search !== '') {
    $where[]  = '(title LIKE ? OR description LIKE ? OR scripture LIKE ?)';
    $like     = "%$search%";
    $params   = array_merge($params, [$like, $like, $like]);
    $types   .= 'sss';
}
if ($speaker !== '') {
    $where[]  = 'speaker = ?';
    $params[] = $speaker;
    $types   .= 's';
}
if ($series !== '') {
    $where[]  = 'series = ?';
    $params[] = $series;
    $types   .= 's';
}

$whereSQL = implode(' AND ', $where);

// Count
$countStmt = $conn->prepare("SELECT COUNT(*) FROM sermons WHERE $whereSQL");
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total    = $countStmt->get_result()->fetch_row()[0];
$pages    = ceil($total / $perPage);

// Data
$dataStmt = $conn->prepare("SELECT * FROM sermons WHERE $whereSQL ORDER BY sermon_date DESC LIMIT ? OFFSET ?");
$allTypes = $types . 'ii';
$allParams = array_merge($params, [$perPage, $offset]);
$dataStmt->bind_param($allTypes, ...$allParams);
$dataStmt->execute();
$sermons  = $dataStmt->get_result();

// Distinct speakers and series for filter dropdowns
$speakers_res = $conn->query("SELECT DISTINCT speaker FROM sermons ORDER BY speaker");
$series_res   = $conn->query("SELECT DISTINCT series FROM sermons WHERE series IS NOT NULL AND series != '' ORDER BY series");

$seo = [
    'title'       => 'Sermons',
    'description' => 'Listen to sermons from ' . get_setting('site_name', 'CAC Achievers House') . '. Faith-building messages from our pastors, available to stream anytime.',
    'url'         => 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/cac/sermons.php',
];
include 'includes/header.php';
?>
<body>
<?php include 'includes/site-header.php'; ?>

<!-- PAGE HERO -->
<section class="page-hero" style="background-image:url('assets/images/bg.jpg');">
    <div class="page-hero-overlay"></div>
    <div class="page-hero-content">
        <span class="hero-eyebrow"><i class="bx bx-headphone"></i> AUDIO & VIDEO SERMONS</span>
        <h1>Sermons &amp; <span class="highlight">Messages</span></h1>
        <p>Every message is a word in season. Discover faith-building teachings from our pastors and guest speakers.</p>
    </div>
</section>

<!-- FILTER BAR -->
<section style="background:var(--bg-white);padding:2rem 1.5rem;border-bottom:1px solid var(--border-light);">
    <div class="page-container">
        <form class="sermon-filter" method="GET" action="sermons.php" id="filterForm">
            <i class="bx bx-search" style="color:var(--text-light);font-size:1.2rem;"></i>
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Search by title, keyword, or scripture…"
                   aria-label="Search sermons">

            <select name="speaker" aria-label="Filter by speaker">
                <option value="">All Speakers</option>
                <?php if ($speakers_res): while ($row = $speakers_res->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['speaker']) ?>"
                    <?= $speaker === $row['speaker'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['speaker']) ?>
                </option>
                <?php endwhile; endif; ?>
            </select>

            <select name="series" aria-label="Filter by series">
                <option value="">All Series</option>
                <?php if ($series_res): while ($row = $series_res->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['series']) ?>"
                    <?= $series === $row['series'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['series']) ?>
                </option>
                <?php endwhile; endif; ?>
            </select>

            <button type="submit" class="btn-primary" style="padding:0.7rem 1.4rem;">
                <i class="bx bx-filter-alt"></i> Filter
            </button>
            <?php if ($search || $speaker || $series): ?>
            <a href="sermons.php" class="btn-navy" style="padding:0.7rem 1.2rem;font-size:0.88rem;">
                <i class="bx bx-x"></i> Clear
            </a>
            <?php endif; ?>
        </form>
    </div>
</section>

<!-- SERMONS GRID -->
<section class="page-section bg-page">
    <div class="page-container">

        <?php if ($total > 0): ?>
        <p style="color:var(--text-muted);font-size:0.88rem;margin-bottom:2rem;">
            Showing <?= min($offset + 1, $total) ?>–<?= min($offset + $perPage, $total) ?> of <?= $total ?> sermon<?= $total !== 1 ? 's' : '' ?>
        </p>

        <div class="sermons-page-grid">
            <?php while ($s = $sermons->fetch_assoc()): ?>
            <article class="sermon-page-card reveal" id="sermon-<?= $s['id'] ?>">

                <div class="sermon-thumbnail">
                    <?php if ($s['thumbnail']): ?>
                    <img src="<?= htmlspecialchars($s['thumbnail']) ?>" alt="<?= htmlspecialchars($s['title']) ?>">
                    <?php else: ?>
                    <div class="sermon-thumbnail-icon"><i class="bx bx-microphone"></i></div>
                    <?php endif; ?>
                    <?php if ($s['video_url'] || $s['audio_file']): ?>
                    <div class="sermon-play-overlay">
                        <div class="play-btn-lg"><i class="bx bx-play"></i></div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="sermon-body">
                    <span class="sermon-tag">
                        <?= htmlspecialchars($s['series'] ?: 'Sunday Message') ?>
                    </span>
                    <h3><?= htmlspecialchars($s['title']) ?></h3>

                    <?php if ($s['description']): ?>
                    <p><?= htmlspecialchars($s['description']) ?></p>
                    <?php endif; ?>

                    <?php if ($s['audio_file']): ?>
                    <audio class="sermon-audio" controls preload="none"
                           src="<?= htmlspecialchars($s['audio_file']) ?>">
                        Your browser does not support audio.
                    </audio>
                    <?php endif; ?>

                    <?php if ($s['video_url']): ?>
                    <a href="<?= htmlspecialchars($s['video_url']) ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="btn-primary"
                       style="margin-top:0.75rem;font-size:0.85rem;padding:0.6rem 1.2rem;">
                        <i class="bx bx-play-circle"></i> Watch Video
                    </a>
                    <?php endif; ?>

                    <div class="sermon-footer">
                        <div>
                            <strong><?= htmlspecialchars($s['speaker']) ?></strong>
                            <?php if ($s['scripture']): ?>
                            <span style="display:block;font-size:0.75rem;color:var(--primary);font-style:italic;margin-top:2px;">
                                <?= htmlspecialchars($s['scripture']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <span><?= date('M j, Y', strtotime($s['sermon_date'])) ?></span>
                    </div>
                </div>

            </article>
            <?php endwhile; ?>
        </div>

        <!-- PAGINATION -->
        <?php if ($pages > 1): ?>
        <nav class="pagination" aria-label="Sermon page navigation"
             style="display:flex;justify-content:center;gap:0.5rem;margin-top:3rem;flex-wrap:wrap;">
            <?php for ($i = 1; $i <= $pages; $i++):
                $qStr = http_build_query(['q'=>$search,'speaker'=>$speaker,'series'=>$series,'page'=>$i]);
            ?>
            <a href="sermons.php?<?= $qStr ?>"
               style="width:40px;height:40px;display:grid;place-items:center;border-radius:var(--r-sm);font-size:0.88rem;font-weight:600;border:1.5px solid <?= $i===$page ? 'var(--primary)' : 'var(--border-light)' ?>;background:<?= $i===$page ? 'var(--primary)' : 'var(--bg-white)' ?>;color:<?= $i===$page ? '#fff' : 'var(--text-muted)' ?>;transition:all 0.2s;">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>

        <?php else: ?>
        <div style="text-align:center;padding:5rem 1.5rem;">
            <i class="bx bx-microphone-off" style="font-size:4rem;color:var(--text-light);"></i>
            <h3 style="font-size:1.4rem;font-weight:700;color:var(--text-navy);margin:1rem 0 0.5rem;">
                No sermons found
            </h3>
            <p style="color:var(--text-muted);margin-bottom:1.5rem;">
                <?= ($search || $speaker || $series) ? 'Try a different search term or clear your filters.' : 'Sermons will appear here once uploaded by our admin team.' ?>
            </p>
            <?php if ($search || $speaker || $series): ?>
            <a href="sermons.php" class="btn-primary">Clear Filters</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- CTA -->
<section class="page-cta" aria-label="Join us in person">
    <div class="page-container text-center">
        <h2>Experience Worship In Person</h2>
        <p>Sermons are powerful, but there's nothing like being in the room. Join us this Sunday for a live encounter with God.</p>
        <a href="contact.php" class="btn-gold">Plan Your Visit <i class="bx bx-right-arrow-alt"></i></a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); }});
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
</body>
</html>
