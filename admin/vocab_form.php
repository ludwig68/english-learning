<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$imageDir = __DIR__ . '/../uploads/images/';
$audioDir = __DIR__ . '/../uploads/audio/';
if (!is_dir($imageDir)) @mkdir($imageDir, 0777, true);
if (!is_dir($audioDir)) @mkdir($audioDir, 0777, true);

$levels = $pdo->query("SELECT * FROM levels ORDER BY id")->fetchAll();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$errors = [];

$level_id = '';
$word = '';
$meaning = '';
$image_path = '';
$audio_path = '';
$type = 'flashcard';
$example_sentence = '';

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM vocabularies WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
    if (!$data) {
        die('Không tìm thấy từ vựng.');
    }
    $level_id = $data['level_id'];
    $word = $data['word'];
    $meaning = $data['meaning'];
    $image_path = $data['image_path'];
    $audio_path = $data['audio_path'];
    $type = $data['type'];
    $example_sentence = $data['example_sentence'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $level_id = (int)($_POST['level_id'] ?? 0);
    $word = trim($_POST['word'] ?? '');
    $meaning = trim($_POST['meaning'] ?? '');
    $type = $_POST['type'] ?? 'flashcard';
    $example_sentence = trim($_POST['example_sentence'] ?? '');
    $image_path = $_POST['current_image'] ?? '';
    $audio_path = $_POST['current_audio'] ?? '';

    if ($level_id <= 0) $errors[] = 'Chọn Level.';
    if ($word === '') $errors[] = 'Nhập Word.';
    if ($meaning === '') $errors[] = 'Nhập Meaning.';

    // Upload image
    if (!empty($_FILES['image_file']['name'])) {
        $file = $_FILES['image_file'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allow = ['jpg','jpeg','png','gif','webp'];
            if (!in_array($ext, $allow)) {
                $errors[] = 'Ảnh chỉ hỗ trợ: ' . implode(', ', $allow);
            } else {
                $newName = 'img_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $imageDir.$newName)) {
                    $image_path = 'uploads/images/'.$newName;
                } else {
                    $errors[] = 'Không lưu được file ảnh.';
                }
            }
        } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Lỗi upload ảnh.';
        }
    }

    // Upload audio
    if (!empty($_FILES['audio_file']['name'])) {
        $file = $_FILES['audio_file'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allow = ['mp3','wav','ogg','m4a'];
            if (!in_array($ext, $allow)) {
                $errors[] = 'Audio chỉ hỗ trợ: ' . implode(', ', $allow);
            } else {
                $newName = 'aud_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $audioDir.$newName)) {
                    $audio_path = 'uploads/audio/'.$newName;
                } else {
                    $errors[] = 'Không lưu được file audio.';
                }
            }
        } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Lỗi upload audio.';
        }
    }

    if (!$errors) {
        if ($isEdit) {
            $stmt = $pdo->prepare("
                UPDATE vocabularies
                SET level_id=?, word=?, meaning=?, image_path=?, audio_path=?, type=?, example_sentence=?
                WHERE id=?
            ");
            $stmt->execute([$level_id,$word,$meaning,$image_path,$audio_path,$type,$example_sentence,$id]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO vocabularies(level_id,word,meaning,image_path,audio_path,type,example_sentence)
                VALUES (?,?,?,?,?,?,?)
            ");
            $stmt->execute([$level_id,$word,$meaning,$image_path,$audio_path,$type,$example_sentence]);
        }
        header('Location: /admin/vocab_list.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $isEdit ? 'Sửa' : 'Thêm' ?> từ vựng | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-dark navbar-lightblue">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/admin/vocab_list.php" class="nav-link">Từ vựng</a>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="/admin/index.php" class="brand-link">
            <span class="brand-text font-weight-light">English Admin</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="/admin/index.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i> <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/vocab_list.php" class="nav-link active">
                            <i class="nav-icon fa-solid fa-book"></i> <p>Từ vựng</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content pt-3">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?= $isEdit ? 'Sửa từ vựng' : 'Thêm từ vựng' ?></h3>
                    </div>
                    <div class="card-body">
                        <?php if ($errors): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $e): ?>
                                        <li><?= htmlspecialchars($e) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Level</label>
                                <select name="level_id" class="form-control">
                                    <option value="">-- Chọn Level --</option>
                                    <?php foreach ($levels as $lv): ?>
                                        <option value="<?= $lv['id'] ?>"
                                            <?= $lv['id']==$level_id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($lv['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Word</label>
                                <input type="text" name="word" class="form-control"
                                       value="<?= htmlspecialchars($word) ?>">
                            </div>

                            <div class="form-group">
                                <label>Meaning</label>
                                <input type="text" name="meaning" class="form-control"
                                       value="<?= htmlspecialchars($meaning) ?>">
                            </div>

                            <div class="form-group">
                                <label>Ảnh minh họa</label>
                                <?php if ($image_path): ?>
                                    <div class="mb-2">
                                        <img src="/<?= htmlspecialchars($image_path) ?>" style="height:60px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="image_file" class="form-control-file">
                                <small class="form-text text-muted">jpg, jpeg, png, gif, webp.</small>
                                <input type="hidden" name="current_image"
                                       value="<?= htmlspecialchars($image_path) ?>">
                            </div>

                            <div class="form-group">
                                <label>Audio</label>
                                <?php if ($audio_path): ?>
                                    <div class="mb-2">
                                        <audio controls style="max-width:200px;">
                                            <source src="/<?= htmlspecialchars($audio_path) ?>" type="audio/mpeg">
                                        </audio>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="audio_file" class="form-control-file">
                                <small class="form-text text-muted">mp3, wav, ogg, m4a.</small>
                                <input type="hidden" name="current_audio"
                                       value="<?= htmlspecialchars($audio_path) ?>">
                            </div>

                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" class="form-control">
                                    <option value="flashcard" <?= $type==='flashcard'?'selected':'' ?>>flashcard</option>
                                    <option value="fill_gap" <?= $type==='fill_gap'?'selected':'' ?>>fill_gap</option>
                                    <option value="mixed" <?= $type==='mixed'?'selected':'' ?>>mixed</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Example sentence</label>
                                <textarea name="example_sentence" rows="3" class="form-control"><?= htmlspecialchars($example_sentence) ?></textarea>
                            </div>

                            <button class="btn btn-success">
                                <i class="fa-solid fa-floppy-disk"></i> Lưu
                            </button>
                            <a href="/admin/vocab_list.php" class="btn btn-secondary">Quay lại</a>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="main-footer text-sm">
        <strong>&copy; <?= date('Y') ?> English Learning.</strong>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
