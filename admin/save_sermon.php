<?php
// ============================================================
// SERMON SAVE HANDLER
// admin/save_sermon.php
// ============================================================
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// ── DELETE ──────────────────────────────────────────────────
if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) { echo json_encode(['success'=>false,'message'=>'Invalid ID']); exit; }

    // Get files to remove
    $res = $conn->prepare('SELECT audio_file, thumbnail FROM sermons WHERE id = ?');
    $res->bind_param('i', $id);
    $res->execute();
    $row = $res->get_result()->fetch_assoc();

    if ($row) {
        foreach (['audio_file','thumbnail'] as $field) {
            if ($row[$field] && str_starts_with($row[$field], 'assets/')) {
                @unlink('../' . $row[$field]);
            }
        }
    }

    $stmt = $conn->prepare('DELETE FROM sermons WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Sermon deleted.']);
    exit;
}

// ── SAVE (insert or update) ─────────────────────────────────
$id          = intval($_POST['sermon_id'] ?? 0);
$title       = trim($_POST['title']      ?? '');
$speaker     = trim($_POST['speaker']    ?? '');
$series      = trim($_POST['series']     ?? '');
$description = trim($_POST['description']?? '');
$scripture   = trim($_POST['scripture']  ?? '');
$video_url   = trim($_POST['video_url']  ?? '');
$sermon_date = $_POST['sermon_date']     ?? date('Y-m-d');

if (!$title || !$speaker || !$sermon_date) {
    echo json_encode(['success'=>false,'message'=>'Title, speaker and date are required.']);
    exit;
}

// Handle audio upload
$audio_file = '';
if (!empty($_FILES['audio_file']['name'])) {
    $allowed_audio = ['audio/mpeg','audio/mp3','audio/wav','audio/ogg','audio/mp4','audio/x-m4a'];
    $mime = mime_content_type($_FILES['audio_file']['tmp_name']);
    if (!in_array($mime, $allowed_audio)) {
        echo json_encode(['success'=>false,'message'=>'Invalid audio format. Use MP3, WAV, OGG, or M4A.']);
        exit;
    }
    if ($_FILES['audio_file']['size'] > 50 * 1024 * 1024) { // 50MB
        echo json_encode(['success'=>false,'message'=>'Audio file too large (max 50MB).']);
        exit;
    }
    $ext  = pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION);
    $name = 'sermon_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
    $dir  = '../assets/uploads/sermons/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $dir . $name)) {
        chmod($dir . $name, 0644);
        $audio_file = 'assets/uploads/sermons/' . $name;
    }
}

// Handle thumbnail upload
$thumbnail = '';
if (!empty($_FILES['thumbnail']['name'])) {
    $allowed_img = ['image/jpeg','image/png','image/webp','image/gif'];
    $mime = mime_content_type($_FILES['thumbnail']['tmp_name']);
    if (!in_array($mime, $allowed_img)) {
        echo json_encode(['success'=>false,'message'=>'Invalid image format. Use JPG, PNG, or WebP.']);
        exit;
    }
    if ($_FILES['thumbnail']['size'] > 3 * 1024 * 1024) {
        echo json_encode(['success'=>false,'message'=>'Thumbnail too large (max 3MB).']);
        exit;
    }
    $ext  = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
    $name = 'thumb_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
    $dir  = '../assets/uploads/sermons/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dir . $name)) {
        chmod($dir . $name, 0644);
        $thumbnail = 'assets/uploads/sermons/' . $name;
    }
}

if ($id > 0) {
    // UPDATE
    if ($audio_file && $thumbnail) {
        $stmt = $conn->prepare('UPDATE sermons SET title=?,speaker=?,series=?,description=?,scripture=?,video_url=?,audio_file=?,thumbnail=?,sermon_date=? WHERE id=?');
        $stmt->bind_param('sssssssssi',$title,$speaker,$series,$description,$scripture,$video_url,$audio_file,$thumbnail,$sermon_date,$id);
    } elseif ($audio_file) {
        $stmt = $conn->prepare('UPDATE sermons SET title=?,speaker=?,series=?,description=?,scripture=?,video_url=?,audio_file=?,sermon_date=? WHERE id=?');
        $stmt->bind_param('ssssssssi',$title,$speaker,$series,$description,$scripture,$video_url,$audio_file,$sermon_date,$id);
    } elseif ($thumbnail) {
        $stmt = $conn->prepare('UPDATE sermons SET title=?,speaker=?,series=?,description=?,scripture=?,video_url=?,thumbnail=?,sermon_date=? WHERE id=?');
        $stmt->bind_param('ssssssssi',$title,$speaker,$series,$description,$scripture,$video_url,$thumbnail,$sermon_date,$id);
    } else {
        $stmt = $conn->prepare('UPDATE sermons SET title=?,speaker=?,series=?,description=?,scripture=?,video_url=?,sermon_date=? WHERE id=?');
        $stmt->bind_param('sssssssi',$title,$speaker,$series,$description,$scripture,$video_url,$sermon_date,$id);
    }
} else {
    // INSERT
    $stmt = $conn->prepare('INSERT INTO sermons (title,speaker,series,description,scripture,video_url,audio_file,thumbnail,sermon_date) VALUES (?,?,?,?,?,?,?,?,?)');
    $stmt->bind_param('sssssssss',$title,$speaker,$series,$description,$scripture,$video_url,$audio_file,$thumbnail,$sermon_date);
}

if ($stmt->execute()) {
    echo json_encode(['success'=>true,'message'=>'Sermon saved successfully.','id'=>$id ?: $conn->insert_id]);
} else {
    echo json_encode(['success'=>false,'message'=>'Database error: ' . $conn->error]);
}
