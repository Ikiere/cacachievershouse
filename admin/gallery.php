<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $caption = trim(strip_tags($_POST['caption'] ?? ''));
    $category = trim(strip_tags($_POST['category'] ?? 'General'));
    if (!$category) $category = 'General';

    $file = $_FILES['image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Upload failed with error code: " . $file['error'];
    } elseif (!in_array($file['type'], $allowed_types)) {
        $error = "Only JPG, PNG and WEBP are allowed.";
    } elseif ($file['size'] > 5 * 1024 * 1024) {
        $error = "File size must be under 5MB.";
    } else {
        // Strip EXIF metadata & Rename file securely
        $ext = match($file['type']) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg'
        };

        $new_filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $target_path = '../assets/gallery/' . $new_filename;

        // Use GD to re-save the image, which strips EXIF metadata
        $image = false;
        if ($file['type'] === 'image/jpeg') {
            $image = @imagecreatefromjpeg($file['tmp_name']);
        } elseif ($file['type'] === 'image/png') {
            $image = @imagecreatefrompng($file['tmp_name']);
        } elseif ($file['type'] === 'image/webp') {
            $image = @imagecreatefromwebp($file['tmp_name']);
        }

        if ($image) {
            $saved = false;
            if ($ext === 'jpg') {
                $saved = imagejpeg($image, $target_path, 90);
            } elseif ($ext === 'png') {
                $saved = imagepng($image, $target_path);
            } elseif ($ext === 'webp') {
                $saved = imagewebp($image, $target_path, 90);
            }
            imagedestroy($image);

            if ($saved) {
                $stmt = $conn->prepare("INSERT INTO gallery (filename, caption, category) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $new_filename, $caption, $category);
                if ($stmt->execute()) {
                    $success = "Image uploaded securely.";
                } else {
                    $error = "Database error: " . $conn->error;
                    unlink($target_path);
                }
            } else {
                $error = "Failed to process image.";
            }
        } else {
            $error = "Invalid image file.";
        }
    }
}

// Fetch existing
$images = [];
$res = $conn->query("SELECT * FROM gallery ORDER BY uploaded_at DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $images[] = $row;
    }
}

include 'includes/navbar.php';
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
/* Reusing some dashboard styles */
body { font-family: 'Inter', sans-serif; background: #f8fafc; margin: 0; }
.dashboard-container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
.card { background: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 2rem; }
h2 { margin-top: 0; color: #0f172a; }

.upload-form { display: grid; gap: 1rem; max-width: 500px; }
.form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem; color: #334155; }
.form-group input { width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; }
.btn-primary { background: #2563eb; color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; }
.alert { padding: 1rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 500; }
.alert.error { background: #fee2e2; color: #991b1b; }
.alert.success { background: #dcfce7; color: #166534; }

.gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
.gallery-item { border-radius: 8px; overflow: hidden; background: #f1f5f9; position: relative; }
.gallery-item img { width: 100%; height: 150px; object-fit: cover; display: block; }
.gallery-info { padding: 0.75rem; font-size: 0.8rem; }
.gallery-info p { margin: 0 0 0.5rem 0; font-weight: 600; color: #334155; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.badge { background: #e2e8f0; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem; color: #475569; }
.btn-delete { position: absolute; top: 0.5rem; right: 0.5rem; background: #ef4444; color: #fff; border: none; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
</style>

<div class="dashboard-container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <div>
            <h1 style="margin:0; font-size:1.8rem;">Gallery Management</h1>
            <p style="margin:0.5rem 0 0 0; color:#64748b;">Upload and manage photos for the public gallery.</p>
        </div>
        <a href="dashboard.php" style="color:#2563eb; text-decoration:none; font-weight:600;"><i class='bx bx-arrow-back'></i> Back to Dashboard</a>
    </div>

    <div class="card">
        <h2>Upload New Photo</h2>
        <?php if ($error): ?> <div class="alert error"><?= htmlspecialchars($error) ?></div> <?php endif; ?>
        <?php if ($success): ?> <div class="alert success"><?= htmlspecialchars($success) ?></div> <?php endif; ?>

        <form action="gallery.php" method="POST" enctype="multipart/form-data" class="upload-form">
            <div class="form-group">
                <label>Image File (JPG, PNG, WEBP — max 5MB)</label>
                <input type="file" name="image" accept="image/jpeg, image/png, image/webp" required>
                <small style="color:#64748b;display:block;margin-top:0.3rem;">EXIF metadata (GPS, camera info) will be automatically stripped for security.</small>
            </div>
            <div class="form-group">
                <label>Caption (Optional)</label>
                <input type="text" name="caption" placeholder="E.g. Sunday Worship 2025">
            </div>
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" placeholder="E.g. Events, Youth, Worship" value="General">
            </div>
            <button type="submit" class="btn-primary">Upload Securely</button>
        </form>
    </div>

    <div class="card">
        <h2>Uploaded Photos (<?= count($images) ?>)</h2>
        <?php if (empty($images)): ?>
            <p style="color:#64748b;">No photos uploaded yet.</p>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($images as $img): ?>
                <div class="gallery-item" id="img-<?= $img['id'] ?>">
                    <img src="../assets/gallery/<?= htmlspecialchars($img['filename']) ?>" alt="">
                    <button class="btn-delete" onclick="deleteImage(<?= $img['id'] ?>)" title="Delete"><i class='bx bx-trash'></i></button>
                    <div class="gallery-info">
                        <p><?= htmlspecialchars($img['caption'] ?: 'No caption') ?></p>
                        <span class="badge"><?= htmlspecialchars($img['category']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
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
            fetch('delete_gallery.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            })
            .then(res => res.text())
            .then(data => {
                if (data === 'success') {
                    document.getElementById('img-' + id).remove();
                    Swal.fire('Deleted!', 'The photo has been deleted.', 'success');
                } else {
                    Swal.fire('Error', data, 'error');
                }
            });
        }
    });
}
</script>
</body>
</html>
