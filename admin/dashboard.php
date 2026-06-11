<?php
// ============================================================
// ADMIN DASHBOARD
// admin/dashboard.php
// ============================================================
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// ── Data queries ───────────────────────────────────────────────
$admin_name  = htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator');
$admin_email = htmlspecialchars($_SESSION['admin_email'] ?? '');
$admin_role  = $_SESSION['admin_role'] ?? 'editor';
$avatar_char = strtoupper(substr($admin_name, 0, 1));

$members_count  = $conn->query("SELECT COUNT(*) as n FROM admins")->fetch_assoc()['n'];
$events_count   = $conn->query("SELECT COUNT(*) as n FROM events WHERE status='upcoming'")->fetch_assoc()['n'];
$gallery_count  = $conn->query("SELECT COUNT(*) as n FROM gallery")->fetch_assoc()['n'];
$messages_count = $conn->query("SELECT COUNT(*) as n FROM contacts WHERE is_read=0")->fetch_assoc()['n'];

$events_result   = $conn->query("SELECT * FROM events ORDER BY start_date DESC LIMIT 12");
$gallery_result  = $conn->query("SELECT * FROM gallery ORDER BY uploaded_at DESC LIMIT 18");
$members_result  = $conn->query("SELECT * FROM admins ORDER BY created_at DESC");
$messages_result = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 20");
$sermons_result  = $conn->query("SELECT * FROM sermons ORDER BY sermon_date DESC LIMIT 50");
$sermons_count   = $conn->query("SELECT COUNT(*) as n FROM sermons")->fetch_assoc()['n'];
$testimonials_result = $conn->query("SELECT * FROM testimonials ORDER BY sort_order ASC, created_at DESC");
$testimonials_count  = $conn->query("SELECT COUNT(*) as n FROM testimonials")->fetch_assoc()['n'];

// Site settings
$s = [];
$sres = $conn->query("SELECT setting_key, setting_value FROM site_settings");
while ($row = $sres->fetch_assoc()) {
    $s[$row['setting_key']] = $row['setting_value'];
}
$get = fn($k, $d = '') => $s[$k] ?? $d;

$logo_path = $get('logo_path', 'assets/logo/cac-logo.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — <?= htmlspecialchars($get('site_name', 'CAC Achievers House')) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="assets/admin.css">
<style>
    /* Dynamic primary color override */
    :root { --primary: <?= htmlspecialchars($get('primary_color', '#f97316')) ?>; }
</style>
</head>
<body>

<!-- ============================================================
     SIDEBAR OVERLAY (mobile)
     ============================================================ -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ============================================================
     SIDEBAR
     ============================================================ -->
<aside class="admin-sidebar" id="adminSidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="sidebar-logo">
            <?php if ($logo_path && file_exists(dirname(__DIR__) . '/' . $logo_path)): ?>
                <img src="../<?= htmlspecialchars($logo_path) ?>?v=<?= filemtime(dirname(__DIR__) . '/' . $logo_path) ?>"
                     alt="<?= htmlspecialchars($get('site_name')) ?>" id="sidebarLogoImg">
            <?php else: ?>
                <i class='bx bx-church'></i>
            <?php endif; ?>
        </div>
        <div class="sidebar-brand-text">
            <h2><?= htmlspecialchars($get('site_name', 'CAC Admin')) ?></h2>
            <span>Admin Panel</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav" aria-label="Admin navigation">

        <span class="nav-section-label">Main</span>

        <a class="nav-item active" data-panel="overview" href="#" onclick="switchPanel('overview',this);return false;">
            <i class='bx bx-grid-alt'></i>
            <span>Dashboard</span>
        </a>

        <a class="nav-item" data-panel="members" href="#" onclick="switchPanel('members',this);return false;">
            <i class='bx bx-user-circle'></i>
            <span>Members</span>
        </a>

        <a class="nav-item" data-panel="events" href="#" onclick="switchPanel('events',this);return false;">
            <i class='bx bx-calendar-event'></i>
            <span>Events</span>
        </a>

        <a class="nav-item" data-panel="gallery" href="#" onclick="switchPanel('gallery',this);return false;">
            <i class='bx bx-images'></i>
            <span>Gallery</span>
            <?php if ($gallery_count > 0): ?>
            <span class="nav-badge"><?= $gallery_count ?></span>
            <?php endif; ?>
        </a>

        <a class="nav-item" data-panel="sermons" href="#" onclick="switchPanel('sermons',this);return false;">
            <i class='bx bx-headphone'></i>
            <span>Sermons</span>
            <?php if ($sermons_count > 0): ?>
            <span class="nav-badge"><?= $sermons_count ?></span>
            <?php endif; ?>
        </a>

        <a class="nav-item" data-panel="testimonials" href="#" onclick="switchPanel('testimonials',this);return false;">
            <i class='bx bx-comment-dots'></i>
            <span>Testimonials</span>
            <?php if ($testimonials_count > 0): ?>
            <span class="nav-badge"><?= $testimonials_count ?></span>
            <?php endif; ?>
        </a>

        <a class="nav-item" data-panel="messages" href="#" onclick="switchPanel('messages',this);return false;">
            <i class='bx bx-message-square-dots'></i>
            <span>Messages</span>
            <?php if ($messages_count > 0): ?>
            <span class="nav-badge"><?= $messages_count ?></span>
            <?php endif; ?>
        </a>

        <span class="nav-section-label">Site</span>

        <a class="nav-item" data-panel="settings" href="#" onclick="switchPanel('settings',this);return false;">
            <i class='bx bx-cog'></i>
            <span>Site Settings</span>
        </a>

        <a class="nav-item" href="../index.php" target="_blank">
            <i class='bx bx-link-external'></i>
            <span>View Website</span>
        </a>

    </nav>

    <!-- User footer -->
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar"><?= $avatar_char ?></div>
            <div class="user-info">
                <strong><?= $admin_name ?></strong>
                <span><?= ucfirst($admin_role) ?></span>
            </div>
            <button class="sidebar-logout" id="logoutBtn" title="Logout">
                <i class='bx bx-log-out'></i>
            </button>
        </div>
    </div>

</aside>

<!-- ============================================================
     MAIN
     ============================================================ -->
<main class="admin-main">

    <!-- TOP BAR -->
    <div class="admin-topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class='bx bx-menu'></i>
            </button>
            <div class="topbar-breadcrumb" id="topbarBreadcrumb">
                <i class='bx bx-home-alt'></i>
                <span>Admin</span>
                <i class='bx bx-chevron-right'></i>
                <strong id="breadcrumbPage">Dashboard</strong>
            </div>
        </div>
        <div class="topbar-right">
            <button class="topbar-action" title="Notifications">
                <i class='bx bx-bell'></i>
                <span class="notif-dot"></span>
            </button>
            <a href="../index.php" target="_blank" class="topbar-view-site">
                <i class='bx bx-globe'></i>
                <span>View Site</span>
            </a>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="admin-content">

        <!-- ══════════════════════════════════════════════════
             PANEL: OVERVIEW / DASHBOARD
             ══════════════════════════════════════════════════ -->
        <div class="admin-panel active" id="panel-overview">
            <div class="page-header">
                <div class="page-header-left">
                    <h1>Welcome back, <?= $admin_name ?> 👋</h1>
                    <p>Here's what's happening at <?= htmlspecialchars($get('site_name', 'your church')) ?> today.</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon orange"><i class='bx bx-user-circle'></i></div>
                    <div class="stat-body">
                        <span class="stat-change up">Active</span>
                        <h3><?= $members_count ?></h3>
                        <p>Admin Members</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class='bx bx-calendar-event'></i></div>
                    <div class="stat-body">
                        <span class="stat-change up">Upcoming</span>
                        <h3><?= $events_count ?></h3>
                        <p>Events Scheduled</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class='bx bx-images'></i></div>
                    <div class="stat-body">
                        <span class="stat-change up">Published</span>
                        <h3><?= $gallery_count ?></h3>
                        <p>Gallery Photos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class='bx bx-message-square-dots'></i></div>
                    <div class="stat-body">
                        <span class="stat-change warn"><?= $messages_count ?> New</span>
                        <h3><?= $messages_count ?></h3>
                        <p>Unread Messages</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class='bx bx-headphone'></i></div>
                    <div class="stat-body">
                        <span class="stat-change up">Published</span>
                        <h3><?= $sermons_count ?></h3>
                        <p>Sermons Uploaded</p>
                    </div>
                </div>
            </div>

            <!-- Dashboard grid -->
            <div class="dash-grid">

                <!-- Recent Activity -->
                <div class="admin-card">
                    <h3><i class='bx bx-time-five'></i> Recent Activity</h3>
                    <ul class="activity-list">
                        <li>
                            <div class="activity-icon green"><i class='bx bx-user-plus'></i></div>
                            <div class="activity-info">
                                <strong>Admin panel accessed</strong>
                                <small>Just now</small>
                            </div>
                        </li>
                        <?php if ($events_result && $events_result->num_rows > 0):
                            $events_result->data_seek(0);
                            $e = $events_result->fetch_assoc();
                            $events_result->data_seek(0);
                        ?>
                        <li>
                            <div class="activity-icon blue"><i class='bx bx-calendar'></i></div>
                            <div class="activity-info">
                                <strong>Latest event: <?= htmlspecialchars($e['title']) ?></strong>
                                <small><?= date('M j, Y', strtotime($e['start_date'])) ?></small>
                            </div>
                        </li>
                        <?php endif; ?>
                        <?php if ($gallery_result && $gallery_result->num_rows > 0):
                            $gallery_result->data_seek(0);
                            $g = $gallery_result->fetch_assoc();
                            $gallery_result->data_seek(0);
                        ?>
                        <li>
                            <div class="activity-icon orange"><i class='bx bx-image'></i></div>
                            <div class="activity-info">
                                <strong>Photo uploaded: <?= htmlspecialchars($g['caption'] ?? $g['filename']) ?></strong>
                                <small><?= date('M j, Y', strtotime($g['uploaded_at'])) ?></small>
                            </div>
                        </li>
                        <?php endif; ?>
                        <?php if ($messages_result && $messages_result->num_rows > 0):
                            $messages_result->data_seek(0);
                            $m = $messages_result->fetch_assoc();
                            $messages_result->data_seek(0);
                        ?>
                        <li>
                            <div class="activity-icon purple"><i class='bx bx-envelope'></i></div>
                            <div class="activity-info">
                                <strong>Message from <?= htmlspecialchars($m['name']) ?></strong>
                                <small><?= date('M j, Y', strtotime($m['created_at'])) ?></small>
                            </div>
                        </li>
                        <?php endif; ?>
                        <li>
                            <div class="activity-icon gray"><i class='bx bx-shield-check'></i></div>
                            <div class="activity-info">
                                <strong>System running normally</strong>
                                <small>PHP + MySQL active</small>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Quick Actions -->
                <div class="admin-card">
                    <h3><i class='bx bx-zap'></i> Quick Actions</h3>
                    <div class="quick-actions-grid">
                        <button class="quick-action orange" onclick="switchPanel('members',null)">
                            <i class='bx bx-user-plus'></i>
                            <span>Add Member</span>
                        </button>
                        <button class="quick-action blue" onclick="switchPanel('events',null);setTimeout(()=>document.getElementById('openEventModal').click(),400)">
                            <i class='bx bx-calendar-plus'></i>
                            <span>Create Event</span>
                        </button>
                        <button class="quick-action green" onclick="switchPanel('gallery',null)">
                            <i class='bx bx-image-add'></i>
                            <span>Upload Photo</span>
                        </button>
                        <button class="quick-action purple" onclick="switchPanel('settings',null)">
                            <i class='bx bx-palette'></i>
                            <span>Edit Site</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- ══════════════════════════════════════════════════
             PANEL: MEMBERS
             ══════════════════════════════════════════════════ -->
        <div class="admin-panel" id="panel-members">
            <div class="page-header">
                <div class="page-header-left">
                    <h1>Members</h1>
                    <p>Manage admin users and roles</p>
                </div>
            </div>

            <div class="admin-card">
                <div class="panel-toolbar">
                    <h2>Admin Directory</h2>
                    <div class="toolbar-actions">
                        <div class="search-box">
                            <i class='bx bx-search'></i>
                            <input type="text" placeholder="Search members…" id="memberSearch">
                        </div>
                        <button class="btn btn-primary" onclick="openAddMember()">
                            <i class='bx bx-user-plus'></i> Add Member
                        </button>
                    </div>
                </div>

                <table class="data-table" id="membersTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($members_result && $members_result->num_rows > 0): ?>
                        <?php while ($row = $members_result->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Name">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="user-avatar" style="width:32px;height:32px;font-size:12px;">
                                        <?= strtoupper(substr($row['name'], 0, 1)) ?>
                                    </div>
                                    <?= htmlspecialchars($row['name']) ?>
                                </div>
                            </td>
                            <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
                            <td data-label="Role">
                                <span class="badge <?= $row['role'] === 'super_admin' ? 'badge-info' : 'badge-muted' ?>">
                                    <?= ucfirst(str_replace('_', ' ', $row['role'])) ?>
                                </span>
                            </td>
                            <td data-label="Joined"><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                            <td data-label="Actions">
                                <div class="action-btns">
                                    <button class="action-btn edit" title="Edit"><i class='bx bx-edit'></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px;">No members found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════
             PANEL: EVENTS
             ══════════════════════════════════════════════════ -->
        <div class="admin-panel" id="panel-events">
            <div class="page-header">
                <div class="page-header-left">
                    <h1>Events</h1>
                    <p>Create and manage church events</p>
                </div>
                <button class="btn btn-primary" id="openEventModal">
                    <i class='bx bx-plus'></i> Create Event
                </button>
            </div>

            <div class="events-grid" id="eventsGrid">
                <?php if ($events_result && $events_result->num_rows > 0):
                    $events_result->data_seek(0);
                    while ($ev = $events_result->fetch_assoc()):
                        $badgeClass = match($ev['status']) {
                            'upcoming' => 'upcoming',
                            'planning' => 'planning',
                            default    => 'past'
                        };
                ?>
                <div class="event-card">
                    <div class="event-card-header">
                        <h4 style="font-size:14px;font-weight:700;"><?= htmlspecialchars($ev['title']) ?></h4>
                        <span class="event-badge <?= $badgeClass ?>"><?= ucfirst($ev['status']) ?></span>
                    </div>
                    <div class="event-meta">
                        <span><i class='bx bx-calendar'></i> <?= date('M j, Y', strtotime($ev['start_date'])) ?></span>
                        <?php if ($ev['start_time']): ?>
                        <span><i class='bx bx-time'></i> <?= date('g:i A', strtotime($ev['start_time'])) ?></span>
                        <?php endif; ?>
                        <?php if ($ev['venue_name']): ?>
                        <span><i class='bx bx-map'></i> <?= htmlspecialchars($ev['venue_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($ev['description']): ?>
                    <p style="font-size:12.5px;color:var(--text-muted);line-height:1.4;"><?= htmlspecialchars(substr($ev['description'], 0, 90)) ?>…</p>
                    <?php endif; ?>
                    <div class="action-btns mt-4">
                        <button class="action-btn edit" title="Edit event"><i class='bx bx-edit'></i></button>
                        <button class="action-btn delete" title="Delete event"><i class='bx bx-trash'></i></button>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <div style="grid-column:1/-1;text-align:center;padding:48px;color:var(--text-muted);">
                    <i class='bx bx-calendar-x' style="font-size:48px;display:block;margin-bottom:12px;"></i>
                    No events yet. Click "Create Event" to add one.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════
             PANEL: GALLERY
             ══════════════════════════════════════════════════ -->
        <div class="admin-panel" id="panel-gallery">
            <div class="page-header">
                <div class="page-header-left">
                    <h1>Gallery</h1>
                    <p>Manage church photos and media</p>
                </div>
                <a href="gallery.php" class="btn btn-primary">
                    <i class='bx bx-image-add'></i> Manage Gallery
                </a>
            </div>

            <div class="gallery-admin-grid">
                <?php if ($gallery_result && $gallery_result->num_rows > 0):
                    $gallery_result->data_seek(0);
                    while ($img = $gallery_result->fetch_assoc()):
                ?>
                <div class="gallery-thumb">
                    <img src="../assets/gallery/<?= htmlspecialchars($img['filename']) ?>"
                         alt="<?= htmlspecialchars($img['caption'] ?? '') ?>"
                         loading="lazy">
                    <div class="gallery-thumb-overlay">
                        <button class="action-btn delete" title="Delete"
                                onclick="deleteGalleryItem(<?= $img['id'] ?>)">
                            <i class='bx bx-trash'></i>
                        </button>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <div style="grid-column:1/-1;text-align:center;padding:48px;color:var(--text-muted);">
                    <i class='bx bx-images' style="font-size:48px;display:block;margin-bottom:12px;"></i>
                    No photos yet. Use "Manage Gallery" to upload.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════
             PANEL: MESSAGES
             ══════════════════════════════════════════════════ -->
        <div class="admin-panel" id="panel-messages">
            <div class="page-header">
                <div class="page-header-left">
                    <h1>Messages</h1>
                    <p>Contact form submissions from your website</p>
                </div>
            </div>

            <div class="message-list">
                <?php if ($messages_result && $messages_result->num_rows > 0):
                    $messages_result->data_seek(0);
                    while ($msg = $messages_result->fetch_assoc()):
                        $initials = strtoupper(substr($msg['name'], 0, 1));
                ?>
                <div class="message-item <?= !$msg['is_read'] ? 'unread' : '' ?>">
                    <div class="msg-avatar"><?= $initials ?></div>
                    <div class="msg-body">
                        <strong><?= htmlspecialchars($msg['name']) ?></strong>
                        <div class="subject"><?= htmlspecialchars($msg['subject'] ?? '(No subject)') ?></div>
                        <p><?= htmlspecialchars($msg['message']) ?></p>
                    </div>
                    <div class="msg-meta">
                        <?= date('M j', strtotime($msg['created_at'])) ?>
                        <div class="action-btns" style="margin-top:8px;">
                            <button class="action-btn view" title="View message"
                                    onclick="viewMessage('<?= addslashes(htmlspecialchars($msg['name'])) ?>','<?= addslashes(htmlspecialchars($msg['email'])) ?>','<?= addslashes(htmlspecialchars($msg['subject'] ?? '')) ?>','<?= addslashes(htmlspecialchars($msg['message'])) ?>')">
                                <i class='bx bx-show'></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <div style="text-align:center;padding:48px;color:var(--text-muted);">
                    <i class='bx bx-message-square-x' style="font-size:48px;display:block;margin-bottom:12px;"></i>
                    No messages yet.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════
             PANEL: SERMONS
             ══════════════════════════════════════════════════ -->
        <div class="admin-panel" id="panel-sermons">
            <div class="page-header">
                <div class="page-header-left">
                    <h1>Sermons</h1>
                    <p>Upload and manage audio/video sermons for your congregation</p>
                </div>
                <div class="page-header-right">
                    <button class="btn-upload-sermon" onclick="openSermonForm()">
                        <i class='bx bx-cloud-upload'></i>
                        <span>Upload Sermon</span>
                    </button>
                </div>
            </div>

            <div class="admin-card no-pad">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Sermon Details</th>
                                <th>Speaker</th>
                                <th>Date</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($sermons_result && $sermons_result->num_rows > 0):
                                $sermons_result->data_seek(0);
                                while ($s = $sermons_result->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <div class="member-info">
                                        <div class="member-avatar" style="background:var(--primary-light);color:var(--primary);border-radius:8px;">
                                            <i class='bx bx-headphone'></i>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($s['title']) ?></strong>
                                            <span style="display:block;font-size:0.8rem;color:var(--text-muted);margin-top:2px;">
                                                <?= htmlspecialchars($s['series'] ?: 'Single Message') ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($s['speaker']) ?></td>
                                <td><?= date('M j, Y', strtotime($s['sermon_date'])) ?></td>
                                <td style="text-align:right;">
                                    <div class="action-btns">
                                        <?php if ($s['audio_file']): ?>
                                        <a href="../<?= htmlspecialchars($s['audio_file']) ?>" target="_blank" class="action-btn view" title="Play audio">
                                            <i class='bx bx-play'></i>
                                        </a>
                                        <?php endif; ?>
                                        <button class="action-btn edit" onclick='editSermon(<?= json_encode($s, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)' title="Edit">
                                            <i class='bx bx-edit-alt'></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteSermon(<?= $s['id'] ?>)" title="Delete">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center;padding:48px;color:var(--text-muted);">
                                    <i class='bx bx-microphone-off' style="font-size:48px;display:block;margin-bottom:12px;"></i>
                                    No sermons uploaded yet. Click "Upload Sermon" to add one.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════
             PANEL: TESTIMONIALS
             ══════════════════════════════════════════════════ -->
        <div class="admin-panel" id="panel-testimonials">
            <div class="page-header">
                <div class="page-header-left">
                    <h1>Testimonials</h1>
                    <p>Manage member testimonials displayed on the homepage</p>
                </div>
                <div class="page-header-right">
                    <button class="btn-upload-sermon" onclick="openTestimonialForm()">
                        <i class='bx bx-plus-circle'></i>
                        <span>Add Testimonial</span>
                    </button>
                </div>
            </div>

            <div class="admin-card no-pad">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Person</th>
                                <th>Quote</th>
                                <th>Status</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($testimonials_result && $testimonials_result->num_rows > 0):
                                $testimonials_result->data_seek(0);
                                while ($tm = $testimonials_result->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <div class="member-info">
                                        <div class="member-avatar" style="border-radius:50%;overflow:hidden;">
                                            <?php if ($tm['photo_url']): ?>
                                            <img src="<?= htmlspecialchars($tm['photo_url']) ?>" alt="<?= htmlspecialchars($tm['name']) ?>" style="width:100%;height:100%;object-fit:cover;">
                                            <?php else: ?>
                                            <i class='bx bx-user'></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($tm['name']) ?></strong>
                                            <span style="display:block;font-size:0.8rem;color:var(--text-muted);margin-top:2px;">
                                                <?= htmlspecialchars($tm['role']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td style="max-width:300px;">
                                    <p style="font-size:12.5px;color:var(--text-secondary);white-space:normal;line-height:1.5;">
                                        <?= htmlspecialchars(mb_substr($tm['quote'], 0, 100)) ?><?= mb_strlen($tm['quote']) > 100 ? '…' : '' ?>
                                    </p>
                                </td>
                                <td>
                                    <?php if ($tm['is_active']): ?>
                                    <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                    <span class="badge badge-muted">Hidden</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right;">
                                    <div class="action-btns">
                                        <button class="action-btn edit" onclick='editTestimonial(<?= json_encode($tm, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)' title="Edit">
                                            <i class='bx bx-edit-alt'></i>
                                        </button>
                                        <button class="action-btn <?= $tm['is_active'] ? 'view' : 'edit' ?>" onclick="toggleTestimonial(<?= $tm['id'] ?>, <?= $tm['is_active'] ? 0 : 1 ?>)" title="<?= $tm['is_active'] ? 'Hide' : 'Show' ?>">
                                            <i class='bx bx-<?= $tm['is_active'] ? 'hide' : 'show' ?>'></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteTestimonial(<?= $tm['id'] ?>)" title="Delete">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center;padding:48px;color:var(--text-muted);">
                                    <i class='bx bx-comment-dots' style="font-size:48px;display:block;margin-bottom:12px;"></i>
                                    No testimonials yet. Click "Add Testimonial" to create one.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════
             PANEL: SITE SETTINGS
             ══════════════════════════════════════════════════ -->
        <div class="admin-panel" id="panel-settings">
            <div class="page-header">
                <div class="page-header-left">
                    <h1>Site Settings</h1>
                    <p>Control your church website content, logo, and branding</p>
                </div>
            </div>

            <div class="settings-layout">

                <!-- Settings sidebar nav -->
                <div class="settings-sidebar">
                    <button class="settings-nav-item active" onclick="switchSettingsTab('general', this)">
                        <i class='bx bx-cog'></i> General
                    </button>
                    <button class="settings-nav-item" onclick="switchSettingsTab('hero', this)">
                        <i class='bx bx-home'></i> Hero Section
                    </button>
                    <button class="settings-nav-item" onclick="switchSettingsTab('logo', this)">
                        <i class='bx bx-image'></i> Logo
                    </button>
                    <button class="settings-nav-item" onclick="switchSettingsTab('contact', this)">
                        <i class='bx bx-phone'></i> Contact Info
                    </button>
                    <button class="settings-nav-item" onclick="switchSettingsTab('social', this)">
                        <i class='bx bxl-facebook'></i> Social Media
                    </button>
                </div>

                <!-- Settings forms -->
                <div style="flex:1;min-width:0;">

                    <!-- General -->
                    <div class="settings-panel active" id="st-general">
                        <form id="form-general">
                        <div class="settings-section">
                            <h3><i class='bx bx-globe'></i> General Settings</h3>
                            <div class="form-group">
                                <label>Site Name</label>
                                <input name="site_name" class="form-control" value="<?= htmlspecialchars($get('site_name')) ?>" placeholder="CAC Achievers House">
                            </div>
                            <div class="form-group">
                                <label>Tagline <span>(shown in footer and metadata)</span></label>
                                <input name="site_tagline" class="form-control" value="<?= htmlspecialchars($get('site_tagline')) ?>" placeholder="Where Faith Meets Destiny">
                            </div>
                            <div class="form-group">
                                <label>Primary Brand Color</label>
                                <div class="color-swatch-row">
                                    <input type="color" class="color-input" id="colorPicker"
                                           value="<?= htmlspecialchars($get('primary_color', '#f97316')) ?>"
                                           oninput="document.getElementById('colorHex').value=this.value; document.documentElement.style.setProperty('--primary',this.value)">
                                    <input type="text" id="colorHex" name="primary_color" class="color-hex-input"
                                           value="<?= htmlspecialchars($get('primary_color', '#f97316')) ?>"
                                           oninput="document.getElementById('colorPicker').value=this.value; document.documentElement.style.setProperty('--primary',this.value)"
                                           placeholder="#f97316">
                                </div>
                                <p class="form-hint">Changes the accent color across the entire site instantly.</p>
                            </div>
                            <div class="form-group">
                                <label>Give / Donate URL</label>
                                <input name="give_url" class="form-control" value="<?= htmlspecialchars($get('give_url', '#')) ?>" placeholder="https://...">
                            </div>
                        </div>
                        <div class="settings-save-bar">
                            <span class="save-status" id="save-status-general"><i class='bx bx-check-circle'></i> Saved!</span>
                            <button type="submit" class="btn btn-primary"><i class='bx bx-save'></i> Save Changes</button>
                        </div>
                        </form>
                    </div>

                    <!-- Hero -->
                    <div class="settings-panel" id="st-hero">
                        <form id="form-hero">
                        <div class="settings-section">
                            <h3><i class='bx bx-slideshow'></i> Hero Section</h3>
                            <div class="form-group">
                                <label>Hero Title <span>(main headline on the homepage)</span></label>
                                <input name="hero_title" class="form-control" value="<?= htmlspecialchars($get('hero_title')) ?>" placeholder="Where Faith Meets Destiny">
                            </div>
                            <div class="form-group">
                                <label>Hero Subtitle <span>(sub-text below the headline)</span></label>
                                <textarea name="hero_subtitle" class="form-control" placeholder="A vibrant community where lives are restored…"><?= htmlspecialchars($get('hero_subtitle')) ?></textarea>
                            </div>
                        </div>
                        <div class="settings-save-bar">
                            <span class="save-status" id="save-status-hero"><i class='bx bx-check-circle'></i> Saved!</span>
                            <button type="submit" class="btn btn-primary"><i class='bx bx-save'></i> Save Changes</button>
                        </div>
                        </form>
                    </div>

                    <!-- Logo -->
                    <div class="settings-panel" id="st-logo">
                        <div class="settings-section">
                            <h3><i class='bx bx-image-alt'></i> Site Logo</h3>
                            <p style="font-size:13px;color:var(--text-secondary);margin-bottom:18px;">
                                Upload a new logo to replace the current one. PNG or SVG with transparent background recommended.
                            </p>
                            <div class="logo-upload-area" onclick="document.getElementById('logoFileInput').click()">
                                <?php if ($logo_path && file_exists(dirname(__DIR__) . '/' . $logo_path)): ?>
                                <img src="../<?= htmlspecialchars($logo_path) ?>?v=<?= time() ?>"
                                     alt="Current Logo" class="logo-preview-img" id="logoPreviewImg">
                                <?php else: ?>
                                <i class='bx bx-cloud-upload' style="font-size:40px;color:var(--text-muted);margin-bottom:8px;display:block;"></i>
                                <?php endif; ?>
                                <p class="logo-upload-label">
                                    <strong>Click to upload</strong> or drag and drop<br>
                                    <span style="font-size:12px;color:var(--text-muted);">PNG, JPG, SVG, WebP · Max 2 MB</span>
                                </p>
                            </div>
                            <form id="form-logo" enctype="multipart/form-data">
                                <input type="file" id="logoFileInput" name="logo" accept="image/*" style="display:none"
                                       onchange="previewLogo(this)">
                                <div class="settings-save-bar" style="margin-top:16px;">
                                    <span class="save-status" id="save-status-logo"><i class='bx bx-check-circle'></i> Logo updated!</span>
                                    <button type="submit" class="btn btn-primary" id="saveLogoBtn" disabled>
                                        <i class='bx bx-upload'></i> Upload Logo
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="settings-panel" id="st-contact">
                        <form id="form-contact">
                        <div class="settings-section">
                            <h3><i class='bx bx-map-pin'></i> Contact Information</h3>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input name="contact_phone" class="form-control" value="<?= htmlspecialchars($get('contact_phone')) ?>" placeholder="+234 800 000 0000">
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($get('contact_email')) ?>" placeholder="info@cacachievers.com">
                            </div>
                            <div class="form-group">
                                <label>Physical Address</label>
                                <textarea name="contact_address" class="form-control" placeholder="12 Faith Avenue, Lagos, Nigeria"><?= htmlspecialchars($get('contact_address')) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Google Maps Embed URL <span>(Optional — overrides auto-generated map)</span></label>
                                <input name="map_embed_url" class="form-control" value="<?= htmlspecialchars($get('map_embed_url')) ?>" placeholder="https://www.google.com/maps/embed?pb=...">
                                <p class="form-hint">Go to Google Maps → Search your location → Share → Embed → Copy the <code>src</code> URL from the iframe code. Leave empty to auto-generate from the address above.</p>
                            </div>
                        </div>
                        <div class="settings-save-bar">
                            <span class="save-status" id="save-status-contact"><i class='bx bx-check-circle'></i> Saved!</span>
                            <button type="submit" class="btn btn-primary"><i class='bx bx-save'></i> Save Changes</button>
                        </div>
                        </form>
                    </div>

                    <!-- Social Media -->
                    <div class="settings-panel" id="st-social">
                        <form id="form-social">
                        <div class="settings-section">
                            <h3><i class='bx bxl-facebook'></i> Social Media Links</h3>
                            <div class="form-group">
                                <label><i class='bx bxl-facebook' style="color:#1877f2;"></i> Facebook URL</label>
                                <input name="facebook_url" class="form-control" value="<?= htmlspecialchars($get('facebook_url', '#')) ?>" placeholder="https://facebook.com/cacachievershouse">
                            </div>
                            <div class="form-group">
                                <label><i class='bx bxl-youtube' style="color:#ff0000;"></i> YouTube URL</label>
                                <input name="youtube_url" class="form-control" value="<?= htmlspecialchars($get('youtube_url', '#')) ?>" placeholder="https://youtube.com/@cacachievershouse">
                            </div>
                            <div class="form-group">
                                <label><i class='bx bxl-instagram' style="color:#e1306c;"></i> Instagram URL</label>
                                <input name="instagram_url" class="form-control" value="<?= htmlspecialchars($get('instagram_url', '#')) ?>" placeholder="https://instagram.com/cacachievershouse">
                            </div>
                            <div class="form-group">
                                <label><i class='bx bxl-twitter' style="color:#1da1f2;"></i> Twitter / X URL</label>
                                <input name="twitter_url" class="form-control" value="<?= htmlspecialchars($get('twitter_url', '#')) ?>" placeholder="https://twitter.com/cacachievershouse">
                            </div>
                            <div class="form-group">
                                <label><i class='bx bxl-whatsapp' style="color:#25d366;"></i> WhatsApp Number <span>(with country code)</span></label>
                                <input name="whatsapp_number" class="form-control" value="<?= htmlspecialchars($get('whatsapp_number')) ?>" placeholder="2348012345678">
                            </div>
                        </div>
                        <div class="settings-save-bar">
                            <span class="save-status" id="save-status-social"><i class='bx bx-check-circle'></i> Saved!</span>
                            <button type="submit" class="btn btn-primary"><i class='bx bx-save'></i> Save Changes</button>
                        </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div><!-- /admin-content -->
</main>

<!-- ============================================================
     EVENT MODAL
     ============================================================ -->
<div class="modal-overlay" id="eventModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class='bx bx-calendar-plus' style="color:var(--primary)"></i> Create Event</h3>
            <button class="modal-close" onclick="document.getElementById('eventModal').classList.remove('open')">&times;</button>
        </div>
        <form id="eventForm" enctype="multipart/form-data">
            <div class="form-group">
                <label>Event Title *</label>
                <input name="title" class="form-control" required placeholder="e.g. Sunday Worship Service">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Event Type *</label>
                    <select name="event_type" class="form-control" required>
                        <option value="">Select type</option>
                        <option>Worship</option>
                        <option>Conference</option>
                        <option>Outreach</option>
                        <option>Prayer</option>
                        <option>Fellowship</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="upcoming">Upcoming</option>
                        <option value="planning">Planning</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Start Date *</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Start Time *</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
                <div class="form-group">
                    <label>End Time</label>
                    <input type="time" name="end_time" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Venue Name</label>
                <input name="venue_name" class="form-control" placeholder="e.g. Main Auditorium">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" placeholder="Brief description of the event…"></textarea>
            </div>
            <div class="form-group">
                <label>Event Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:8px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('eventModal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class='bx bx-save'></i> Save Event</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     JAVASCRIPT
     ============================================================ -->
<script>
// ── Panel Switching ─────────────────────────────────────────
const panelTitles = {
    overview: 'Dashboard',
    members:  'Members',
    events:   'Events',
    gallery:  'Gallery',
    messages: 'Messages',
    settings: 'Site Settings'
};

function switchPanel(name, linkEl) {
    // Deactivate all panels
    document.querySelectorAll('.admin-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));

    // Activate target
    const panel = document.getElementById('panel-' + name);
    if (panel) panel.classList.add('active');

    // Highlight link
    if (linkEl) linkEl.classList.add('active');
    else {
        const link = document.querySelector(`.nav-item[data-panel="${name}"]`);
        if (link) link.classList.add('active');
    }

    // Update breadcrumb
    document.getElementById('breadcrumbPage').textContent = panelTitles[name] || name;

    // Close mobile sidebar
    closeMobileSidebar();
}

// ── Mobile Sidebar ──────────────────────────────────────────
const sidebar  = document.getElementById('adminSidebar');
const overlay  = document.getElementById('sidebarOverlay');
const toggle   = document.getElementById('sidebarToggle');

toggle.addEventListener('click', () => {
    sidebar.classList.toggle('mobile-open');
    overlay.classList.toggle('active');
});

overlay.addEventListener('click', closeMobileSidebar);

function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
}

// ── Logout ──────────────────────────────────────────────────
document.getElementById('logoutBtn').addEventListener('click', () => {
    Swal.fire({
        title: 'Sign out?',
        text: 'You will be logged out of the admin panel.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        confirmButtonText: 'Yes, sign out',
        cancelButtonText: 'Stay here',
        borderRadius: '14px'
    }).then(r => {
        if (r.isConfirmed) window.location.href = 'logout.php';
    });
});

// ── Settings Tab Switching ──────────────────────────────────
function switchSettingsTab(tab, btn) {
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.settings-nav-item').forEach(b => b.classList.remove('active'));
    document.getElementById('st-' + tab).classList.add('active');
    btn.classList.add('active');
}

// ── Settings Save ───────────────────────────────────────────
function saveSettings(formId, statusId) {
    const form = document.getElementById(formId);
    const data = new FormData(form);
    const statusEl = document.getElementById(statusId);

    fetch('save_settings.php', {
        method: 'POST',
        body: data
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            statusEl.classList.add('visible');
            setTimeout(() => statusEl.classList.remove('visible'), 3000);
        } else {
            Swal.fire('Error', res.errors?.join('<br>') || 'Could not save.', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'Network error — please try again.', 'error'));
}

['general','hero','contact','social'].forEach(tab => {
    const form = document.getElementById('form-' + tab);
    if (form) {
        form.addEventListener('submit', e => {
            e.preventDefault();
            saveSettings('form-' + tab, 'save-status-' + tab);
        });
    }
});

// ── Logo Upload ─────────────────────────────────────────────
function previewLogo(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        let img = document.getElementById('logoPreviewImg');
        if (!img) {
            img = document.createElement('img');
            img.id = 'logoPreviewImg';
            img.className = 'logo-preview-img';
            input.parentElement.querySelector('.logo-upload-area').prepend(img);
        }
        img.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
    document.getElementById('saveLogoBtn').disabled = false;
}

document.getElementById('form-logo').addEventListener('submit', e => {
    e.preventDefault();
    const data = new FormData(e.target);
    const statusEl = document.getElementById('save-status-logo');
    const btn = document.getElementById('saveLogoBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Uploading…';

    fetch('save_settings.php', { method: 'POST', body: data })
    .then(r => r.json())
    .then(res => {
        btn.innerHTML = '<i class="bx bx-upload"></i> Upload Logo';
        if (res.success) {
            statusEl.classList.add('visible');
            // Update sidebar logo
            const sidebarLogo = document.getElementById('sidebarLogoImg');
            if (sidebarLogo) {
                sidebarLogo.src = sidebarLogo.src.split('?')[0] + '?v=' + Date.now();
            }
            setTimeout(() => statusEl.classList.remove('visible'), 3000);
        } else {
            Swal.fire('Upload Error', res.errors?.join('<br>') || 'Failed.', 'error');
        }
    })
    .catch(() => {
        btn.innerHTML = '<i class="bx bx-upload"></i> Upload Logo';
        Swal.fire('Error', 'Network error — please try again.', 'error');
    });
});

// ── Sermons sliding panel logic ─────────────────────────────
function openSermonForm() {
    document.getElementById('sermonForm').reset();
    document.getElementById('sm_id').value = '';
    document.getElementById('sm_panel_title').textContent = 'Upload Sermon';
    document.getElementById('audio_preview').style.display = 'none';
    document.getElementById('audio_preview').src = '';
    document.getElementById('slideSermon').classList.add('active');
}
function closeSermonForm() { document.getElementById('slideSermon').classList.remove('active'); }

function editSermon(s) {
    document.getElementById('sermonForm').reset();
    document.getElementById('sm_panel_title').textContent = 'Edit Sermon';
    document.getElementById('sm_id').value = s.id || '';
    document.getElementById('sm_title').value = s.title || '';
    document.getElementById('sm_speaker').value = s.speaker || '';
    document.getElementById('sm_series').value = s.series || '';
    document.getElementById('sm_date').value = s.sermon_date || '';
    document.getElementById('sm_scripture').value = s.scripture || '';
    document.getElementById('sm_video').value = s.video_url || '';
    document.getElementById('sm_desc').value = s.description || '';

    const audioEl = document.getElementById('audio_preview');
    if (s.audio_file) {
        audioEl.style.display = 'block';
        audioEl.src = '../' + s.audio_file;
    } else {
        audioEl.style.display = 'none';
        audioEl.src = '';
    }
    
    document.getElementById('slideSermon').classList.add('active');
}

function saveSermon(e) {
    e.preventDefault();
    const data = new FormData(document.getElementById('sermonForm'));
    const btn = document.getElementById('saveSermonBtn');
    btn.disabled = true; btn.innerHTML = 'Saving…';

    fetch('save_sermon.php', { method: 'POST', body: data })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            Swal.fire('Saved!', 'Sermon saved successfully.', 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', res.message, 'error');
            btn.disabled = false; btn.innerHTML = 'Save Sermon';
        }
    }).catch(() => {
        Swal.fire('Error', 'Network error', 'error');
        btn.disabled = false; btn.innerHTML = 'Save Sermon';
    });
}

function deleteSermon(id) {
    Swal.fire({
        title: 'Delete Sermon?',
        text: "This will remove the sermon and any uploaded audio/thumbnail. This cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);
            fetch('save_sermon.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if (res.success) location.reload();
                else Swal.fire('Error', res.message, 'error');
            });
        }
    });
}

// ── Add Member ──────────────────────────────────────────────
function openAddMember() {
    Swal.fire({
        title: 'Add New Admin Member',
        html: `
            <input id="s-name"  class="swal2-input" placeholder="Full Name">
            <input id="s-email" class="swal2-input" type="email" placeholder="Email Address">
            <input id="s-phone" class="swal2-input" placeholder="Phone Number">
            <input id="s-date"  class="swal2-input" type="date">
            <select id="s-role" class="swal2-input">
                <option value="editor">Editor</option>
                <option value="super_admin">Super Admin</option>
            </select>
        `,
        confirmButtonText: 'Save Member',
        confirmButtonColor: '#f97316',
        showCancelButton: true,
        preConfirm: () => ({
            name:   document.getElementById('s-name').value,
            email:  document.getElementById('s-email').value,
            phone:  document.getElementById('s-phone').value,
            joined: document.getElementById('s-date').value,
            role:   document.getElementById('s-role').value
        })
    }).then(r => {
        if (r.isConfirmed) {
            fetch('save_member.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(r.value)
            }).then(() => location.reload());
        }
    });
}

// Member table search
document.getElementById('memberSearch')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#membersTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

// ── Event Modal ─────────────────────────────────────────────
document.getElementById('openEventModal').addEventListener('click', () => {
    document.getElementById('eventModal').classList.add('open');
});

document.getElementById('eventForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const data = new FormData(this);
    fetch('save_event.php', { method: 'POST', body: data })
    .then(r => r.json())
    .then(res => {
        document.getElementById('eventModal').classList.remove('open');
        Swal.fire({
            icon: res.success ? 'success' : 'error',
            title: res.success ? 'Event Created!' : 'Error',
            text: res.message || '',
            confirmButtonColor: '#f97316'
        }).then(() => { if (res.success) location.reload(); });
    });
});

// ── View Message ────────────────────────────────────────────
function viewMessage(name, email, subject, message) {
    Swal.fire({
        title: `Message from ${name}`,
        html: `
            <div style="text-align:left;font-size:14px;">
                <p><strong>From:</strong> ${name} &lt;${email}&gt;</p>
                <p><strong>Subject:</strong> ${subject || '(none)'}</p>
                <hr style="margin:12px 0;">
                <p style="line-height:1.6;">${message}</p>
            </div>
        `,
        confirmButtonColor: '#f97316',
        confirmButtonText: 'Close'
    });
}

// ── Delete Gallery Item ─────────────────────────────────────
function deleteGalleryItem(id) {
    Swal.fire({
        title: 'Delete this photo?',
        text: 'This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Yes, delete'
    }).then(r => {
        if (r.isConfirmed) {
            fetch('delete_gallery.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            }).then(() => location.reload());
        }
    });
}

// ── URL hash navigation ──────────────────────────────────────
const hash = window.location.hash.replace('#', '');
if (hash && document.getElementById('panel-' + hash)) {
    switchPanel(hash, document.querySelector(`.nav-item[data-panel="${hash}"]`));
}
</script>
<!-- ══════════════════════════════════════════════════
     SLIDING PANELS
     ══════════════════════════════════════════════════ -->

<!-- SERMON UPLOAD FORM -->
<div class="slide-panel" id="slideSermon">
    <div class="slide-panel-header">
        <h2 id="sm_panel_title">Upload Sermon</h2>
        <button class="close-panel" onclick="closeSermonForm()"><i class='bx bx-x'></i></button>
    </div>
    <div class="slide-panel-body">
        <form id="sermonForm" onsubmit="saveSermon(event)" enctype="multipart/form-data">
            <input type="hidden" name="sermon_id" id="sm_id">
            
            <div class="form-group">
                <label>Sermon Title *</label>
                <input type="text" name="title" id="sm_title" class="form-control" required placeholder="e.g. Walking in Faith">
            </div>
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label>Speaker *</label>
                    <input type="text" name="speaker" id="sm_speaker" class="form-control" required placeholder="e.g. Pastor John">
                </div>
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="sermon_date" id="sm_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Series (Optional)</label>
                <input type="text" name="series" id="sm_series" class="form-control" placeholder="e.g. The Book of Romans">
            </div>
            
            <div class="form-group">
                <label>Scripture Reference</label>
                <input type="text" name="scripture" id="sm_scripture" class="form-control" placeholder="e.g. Romans 8:28">
            </div>

            <div class="form-group">
                <label>Video URL (YouTube/Vimeo)</label>
                <input type="url" name="video_url" id="sm_video" class="form-control" placeholder="https://youtube.com/...">
            </div>
            
            <div class="form-group">
                <label>Audio File (MP3/WAV)</label>
                <input type="file" name="audio_file" id="sm_audio" class="form-control" accept="audio/*">
                <audio id="audio_preview" controls style="width:100%;margin-top:10px;display:none;"></audio>
            </div>
            
            <div class="form-group">
                <label>Thumbnail Image (JPG/PNG/WebP)</label>
                <input type="file" name="thumbnail" id="sm_thumb" class="form-control" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="sm_desc" class="form-control" rows="3" placeholder="Brief summary of the message"></textarea>
            </div>
            
            <button type="submit" class="btn-primary" id="saveSermonBtn" style="width:100%;">Save Sermon</button>
        </form>
    </div>
</div>

<!-- TESTIMONIAL FORM -->
<div class="slide-panel" id="slideTestimonial">
    <div class="slide-panel-header">
        <h2 id="tm_panel_title">Add Testimonial</h2>
        <button class="close-panel" onclick="closeTestimonialForm()"><i class='bx bx-x'></i></button>
    </div>
    <div class="slide-panel-body">
        <form id="testimonialForm" onsubmit="saveTestimonial(event)">
            <input type="hidden" name="testimonial_id" id="tm_id">

            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" id="tm_name" class="form-control" required placeholder="e.g. Sister Grace Adeyemi">
            </div>

            <div class="form-group">
                <label>Role / Membership Info</label>
                <input type="text" name="role" id="tm_role" class="form-control" placeholder="e.g. Member since 2018">
            </div>

            <div class="form-group">
                <label>Testimonial Quote *</label>
                <textarea name="quote" id="tm_quote" class="form-control" rows="5" required placeholder="Share their testimony in their own words…"></textarea>
            </div>

            <div class="form-group">
                <label>Photo URL <span style="font-weight:400;color:var(--text-muted);">(Optional — paste an image URL)</span></label>
                <input type="url" name="photo_url" id="tm_photo" class="form-control" placeholder="https://example.com/photo.jpg">
            </div>

            <div class="form-group">
                <label>Display Order</label>
                <input type="number" name="sort_order" id="tm_sort" class="form-control" value="0" min="0" placeholder="0">
            </div>

            <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                <input type="checkbox" name="is_active" id="tm_active" value="1" checked style="width:18px;height:18px;accent-color:var(--primary);">
                <label for="tm_active" style="margin:0;cursor:pointer;">Show on website</label>
            </div>

            <button type="submit" class="btn-primary" id="saveTestimonialBtn" style="width:100%;">Save Testimonial</button>
        </form>
    </div>
</div>

<script>
// ── Testimonials sliding panel logic ────────────────────────
const slideTestimonial = document.getElementById('slideTestimonial');

function openTestimonialForm() {
    document.getElementById('testimonialForm').reset();
    document.getElementById('tm_id').value = '';
    document.getElementById('tm_panel_title').textContent = 'Add Testimonial';
    document.getElementById('tm_active').checked = true;
    slideTestimonial.classList.add('active');
}

function closeTestimonialForm() { slideTestimonial.classList.remove('active'); }

function editTestimonial(t) {
    document.getElementById('testimonialForm').reset();
    document.getElementById('tm_panel_title').textContent = 'Edit Testimonial';
    document.getElementById('tm_id').value = t.id || '';
    document.getElementById('tm_name').value = t.name || '';
    document.getElementById('tm_role').value = t.role || '';
    document.getElementById('tm_quote').value = t.quote || '';
    document.getElementById('tm_photo').value = t.photo_url || '';
    document.getElementById('tm_sort').value = t.sort_order || 0;
    document.getElementById('tm_active').checked = !!parseInt(t.is_active);
    slideTestimonial.classList.add('active');
}

function saveTestimonial(e) {
    e.preventDefault();
    const form = document.getElementById('testimonialForm');
    const data = new FormData(form);
    // Checkbox handling — if unchecked, it won't be in FormData
    if (!document.getElementById('tm_active').checked) {
        data.set('is_active', '0');
    }
    const btn = document.getElementById('saveTestimonialBtn');
    btn.disabled = true; btn.innerHTML = 'Saving…';

    fetch('save_testimonial.php', { method: 'POST', body: data })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            Swal.fire('Saved!', res.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', res.message, 'error');
            btn.disabled = false; btn.innerHTML = 'Save Testimonial';
        }
    }).catch(() => {
        Swal.fire('Error', 'Network error', 'error');
        btn.disabled = false; btn.innerHTML = 'Save Testimonial';
    });
}

function deleteTestimonial(id) {
    Swal.fire({
        title: 'Delete Testimonial?',
        text: "This cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);
            fetch('save_testimonial.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if (res.success) location.reload();
                else Swal.fire('Error', res.message, 'error');
            });
        }
    });
}

function toggleTestimonial(id, newStatus) {
    const fd = new FormData();
    fd.append('action', 'toggle');
    fd.append('id', id);
    fd.append('is_active', newStatus);
    fetch('save_testimonial.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.success) location.reload();
        else Swal.fire('Error', res.message, 'error');
    });
}
</script>

</body>
</html>