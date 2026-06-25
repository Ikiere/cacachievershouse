<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

// Handle POST size limit overflow
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES)) {
    $maxSize = ini_get('post_max_size');
    $error = "The uploaded file is too large. The server's POST size limit is " . htmlspecialchars($maxSize) . ". Please choose a smaller file.";
}

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $caption = trim(strip_tags($_POST['caption'] ?? ''));
    $category = trim(strip_tags($_POST['category'] ?? 'General'));
    if (!$category) $category = 'General';

    $file = $_FILES['image'];
    $allowed_types = [
        'image/jpeg', 
        'image/jpg', 
        'image/pjpeg', 
        'image/png', 
        'image/x-png', 
        'image/webp'
    ];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $error = "The uploaded image is too large. Server limit is " . ini_get('upload_max_filesize') . ".";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $error = "The uploaded image exceeds the limit specified in the form.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $error = "The image was only partially uploaded. Please try again.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $error = "No image file was selected.";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $error = "Server configuration error: missing temporary folder.";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $error = "Failed to write the uploaded image to disk. Check folder permissions.";
                break;
            default:
                $error = "Upload failed (Error code: " . $file['error'] . ").";
                break;
        }
    } elseif (!in_array($file['type'], $allowed_types)) {
        $error = "Only JPG, PNG and WEBP are allowed.";
    } elseif ($file['size'] > 10 * 1024 * 1024) {
        $error = "File size must be under 10MB.";
    } else {
        // Map file extension safely
        switch ($file['type']) {
            case 'image/png':
            case 'image/x-png':
                $ext = 'png';
                break;
            case 'image/webp':
                $ext = 'webp';
                break;
            default:
                $ext = 'jpg';
                break;
        }

        $new_filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $target_dir = '../assets/gallery/';
        
        // Ensure directory exists recursively
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        $target_path = $target_dir . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            chmod($target_path, 0644);
            $stmt = $conn->prepare("INSERT INTO gallery (filename, caption, category) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $new_filename, $caption, $category);
            if ($stmt->execute()) {
                $success = "Image uploaded securely.";
            } else {
                $error = "Database error: " . $conn->error;
                @unlink($target_path);
            }
        } else {
            $error = "Failed to save uploaded image. Check directory write permissions.";
        }
    }
}

// Fetch existing images
$images = [];
$res = $conn->query("SELECT * FROM gallery ORDER BY uploaded_at DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $images[] = $row;
    }
}

// Fetch ministry categories for dropdown
$gallery_categories = ['General'];
$cat_res = @$conn->query("SELECT name FROM `ministries` WHERE is_active = 1 ORDER BY sort_order ASC");
if ($cat_res && $cat_res->num_rows > 0) {
    while ($crow = $cat_res->fetch_assoc()) {
        $gallery_categories[] = $crow['name'];
    }
} else {
    // Fallback if ministries table not yet created
    $gallery_categories = array_merge($gallery_categories, ["Youth Ministry", "Children's Church", "Women's Fellowship", "Evangelism Committee", "Events", "Worship"]);
}
// Also add any existing categories from gallery that might not be in ministries
$existing_cats_res = @$conn->query("SELECT DISTINCT category FROM gallery WHERE category != 'General' ORDER BY category");
if ($existing_cats_res) {
    while ($ec = $existing_cats_res->fetch_assoc()) {
        if (!in_array($ec['category'], $gallery_categories)) {
            $gallery_categories[] = $ec['category'];
        }
    }
}

// Define count helper
if (!function_exists('db_count')) {
    function db_count(mysqli $db, string $sql): int {
        $res = @$db->query($sql);
        if (!$res) return 0;
        $row = $res->fetch_assoc();
        return (int) ($row['n'] ?? 0);
    }
}

$members_count  = db_count($conn, "SELECT COUNT(*) as n FROM admins");
$events_count   = db_count($conn, "SELECT COUNT(*) as n FROM events WHERE status='upcoming'");
$gallery_count  = db_count($conn, "SELECT COUNT(*) as n FROM gallery");
$messages_count = db_count($conn, "SELECT COUNT(*) as n FROM contacts WHERE is_read=0");
$sermons_count  = db_count($conn, "SELECT COUNT(*) as n FROM sermons");
$testimonials_count = db_count($conn, "SELECT COUNT(*) as n FROM testimonials");
$ministries_count = db_count($conn, "SELECT COUNT(*) as n FROM ministries");

$admin_name  = htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator');
$admin_email = htmlspecialchars($_SESSION['admin_email'] ?? '');
$admin_role  = $_SESSION['admin_role'] ?? 'editor';
$avatar_char = strtoupper(substr($admin_name, 0, 1));

// Retrieve Site Settings
$s = [];
$sres = @$conn->query("SELECT setting_key, setting_value FROM site_settings");
if ($sres) {
    while ($row = $sres->fetch_assoc()) {
        $s[$row['setting_key']] = $row['setting_value'];
    }
}
$get = function($k, $d = '') use (&$s) { return $s[$k] ?? $d; };
$logo_path = $get('logo_path', 'assets/logo/cac-logo.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gallery Management — <?= htmlspecialchars($get('site_name', 'CAC Achievers House')) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="assets/admin.css">
<style>
    :root { --primary: <?= htmlspecialchars($get('primary_color', '#f97316')) ?>; }
</style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- SIDEBAR -->
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

        <a class="nav-item" href="dashboard.php#overview">
            <i class='bx bx-grid-alt'></i>
            <span>Dashboard</span>
        </a>

        <a class="nav-item" href="dashboard.php#members">
            <i class='bx bx-user-circle'></i>
            <span>Members</span>
        </a>

        <a class="nav-item" href="dashboard.php#events">
            <i class='bx bx-calendar-event'></i>
            <span>Events</span>
        </a>

        <a class="nav-item active" href="gallery.php">
            <i class='bx bx-images'></i>
            <span>Gallery</span>
            <?php if ($gallery_count > 0): ?>
            <span class="nav-badge"><?= $gallery_count ?></span>
            <?php endif; ?>
        </a>

        <a class="nav-item" href="dashboard.php#sermons">
            <i class='bx bx-headphone'></i>
            <span>Sermons</span>
            <?php if ($sermons_count > 0): ?>
            <span class="nav-badge"><?= $sermons_count ?></span>
            <?php endif; ?>
        </a>

        <a class="nav-item" href="dashboard.php#ministries">
            <i class='bx bx-crown'></i>
            <span>Ministries</span>
            <?php if ($ministries_count > 0): ?>
            <span class="nav-badge"><?= $ministries_count ?></span>
            <?php endif; ?>
        </a>

        <a class="nav-item" href="dashboard.php#testimonials">
            <i class='bx bx-comment-dots'></i>
            <span>Testimonials</span>
            <?php if ($testimonials_count > 0): ?>
            <span class="nav-badge"><?= $testimonials_count ?></span>
            <?php endif; ?>
        </a>

        <a class="nav-item" href="dashboard.php#messages">
            <i class='bx bx-message-square-dots'></i>
            <span>Messages</span>
            <?php if ($messages_count > 0): ?>
            <span class="nav-badge"><?= $messages_count ?></span>
            <?php endif; ?>
        </a>

        <span class="nav-section-label">Site</span>

        <a class="nav-item" href="dashboard.php#settings">
            <i class='bx bx-cog'></i>
            <span>Site Settings</span>
        </a>

        <a class="nav-item" href="../" target="_blank">
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

<!-- MAIN CONTENT -->
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
                <strong>Gallery</strong>
            </div>
        </div>
        <div class="topbar-right">
            <a href="../" target="_blank" class="topbar-view-site">
                <i class='bx bx-globe'></i>
                <span>View Site</span>
            </a>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="admin-content">
        <div class="page-header">
            <div class="page-header-left">
                <h1>Gallery Management</h1>
                <p>Upload and manage photos for the public gallery.</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div style="background:#fee2e2; color:#991b1b; padding:1rem; border-radius:10px; margin-bottom:1.5rem; font-weight:500;">
                <i class='bx bx-error-circle'></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div style="background:#dcfce7; color:#166534; padding:1rem; border-radius:10px; margin-bottom:1.5rem; font-weight:500;">
                <i class='bx bx-check-circle'></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div class="admin-card" style="margin-bottom:2rem;">
            <h3><i class='bx bx-cloud-upload'></i> Upload New Photo</h3>
            <form action="gallery.php" method="POST" enctype="multipart/form-data" style="margin-top:1.5rem; max-width:600px;">
                <div class="form-group" style="margin-bottom:1.2rem;">
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.88rem; color:#475569;">Image File (JPG, PNG, WEBP — max 10MB) <span style="color:#ef4444;">*</span></label>
                    <input type="file" name="image" accept="image/jpeg, image/png, image/webp" required class="form-control premium-input">
                    <small style="color:#64748b;display:block;margin-top:0.3rem;">EXIF metadata (GPS, camera info) will be automatically stripped for security.</small>
                </div>
                
                <div class="form-group" style="margin-bottom:1.2rem;">
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.88rem; color:#475569;">Caption (Optional)</label>
                    <input type="text" name="caption" placeholder="E.g. Sunday Worship 2026" class="form-control premium-input">
                </div>

                <div class="form-group" style="margin-bottom:1.5rem;">
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.88rem; color:#475569;">Category <small style="color:#64748b;font-weight:400;">(determines where this photo is displayed)</small></label>
                    <select name="category" class="form-control premium-input">
                        <?php foreach ($gallery_categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $cat === 'General' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color:#64748b;display:block;margin-top:.3rem;">Photos tagged with a ministry will also appear on that ministry's page.</small>
                </div>

                <button type="submit" class="premium-submit-btn" style="margin-top:0.5rem; max-width:200px;">
                    <i class='bx bx-upload'></i> Upload Securely
                </button>
            </form>
        </div>

        <div class="admin-card">
            <h3><i class='bx bx-images'></i> Uploaded Photos (<?= count($images) ?>)</h3>
            
            <?php if (empty($images)): ?>
                <div style="text-align:center; padding:3rem; color:var(--text-muted);">
                    <i class='bx bx-image-alt' style="font-size:3rem; display:block; margin-bottom:1rem;"></i>
                    No photos uploaded yet.
                </div>
            <?php else: ?>
                <div class="gallery-admin-grid">
                    <?php foreach ($images as $img): ?>
                    <div class="gallery-card" id="img-<?= $img['id'] ?>" style="background: #fff; border-radius:12px; overflow:hidden; border:1px solid #e2e8f0; display:flex; flex-direction:column; box-shadow: var(--shadow-xs);">
                        <div class="gallery-thumb" style="aspect-ratio:1/1; border-radius:0; box-shadow:none;">
                            <img src="../assets/gallery/<?= htmlspecialchars($img['filename']) ?>" alt="" loading="lazy">
                            <div class="gallery-thumb-overlay">
                                <button class="action-btn delete" style="background:#ef4444; color:#fff; border:none; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer;" onclick="deleteImage(<?= $img['id'] ?>)" title="Delete">
                                    <i class='bx bx-trash' style="font-size:1.1rem;"></i>
                                </button>
                            </div>
                        </div>
                        <div style="padding:12px; flex-grow:1; display:flex; flex-direction:column; justify-content:space-between; gap:6px;">
                            <strong style="display:block; font-size:0.8rem; color:#1e293b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($img['caption'] ?: 'No caption') ?>">
                                <?= htmlspecialchars($img['caption'] ?: 'No caption') ?>
                            </strong>
                            <div>
                                <span class="badge" style="display:inline-block; font-size:0.68rem; padding:3px 8px; background:#f1f5f9; color:#475569; border-radius:4px; font-weight:500; max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    <?= htmlspecialchars($img['category']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
// Mobile Sidebar Toggle
const sidebar  = document.getElementById('adminSidebar');
const overlay  = document.getElementById('sidebarOverlay');
const toggle   = document.getElementById('sidebarToggle');

if (toggle && sidebar && overlay) {
    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });
}

// Logout
const logoutBtn = document.getElementById('logoutBtn');
if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
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
}

function deleteImage(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This photo will be permanently deleted.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            fetch('delete_gallery.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id,
                credentials: 'same-origin'
            })
            .then(res => {
                console.log('Delete response status:', res.status);
                return res.text();
            })
            .then(data => {
                console.log('Delete response body:', JSON.stringify(data));
                const cleaned = data.trim();
                if (cleaned === 'success') {
                    const el = document.getElementById('img-' + id);
                    if (el) el.remove();
                    Swal.fire('Deleted!', 'The photo has been deleted.', 'success');
                } else {
                    Swal.fire('Error', 'Server responded: ' + cleaned, 'error');
                }
            })
            .catch(err => {
                console.error('Delete fetch error:', err);
                Swal.fire('Network Error', 'Could not reach the server. Check your connection.', 'error');
            });
        }
    });
}
</script>
</body>
</html>
