<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

// Stats cho sidebar
$totalLevels = (int)$pdo->query("SELECT COUNT(*) FROM levels")->fetchColumn();
$totalVocab  = (int)$pdo->query("SELECT COUNT(*) FROM vocabularies WHERE deleted_at IS NULL")->fetchColumn();

// Thư mục lưu file upload
$imageDir = __DIR__ . '/../uploads/images/';
$audioDir = __DIR__ . '/../uploads/audio/';
if (!is_dir($imageDir)) @mkdir($imageDir, 0777, true);
if (!is_dir($audioDir)) @mkdir($audioDir, 0777, true);

// Lấy danh sách level
$levels = $pdo->query("SELECT * FROM levels ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// Biến dùng cho form
$id                = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit            = $id > 0;
$errors            = [];

$level_id          = '';
$word              = '';
$meaning           = '';
$type              = 'flashcard';
$example_sentence  = '';
$image_url         = '';
$audio_url         = '';

// Nếu là sửa -> load dữ liệu
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM vocabularies WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$data) {
        die('Không tìm thấy từ vựng.');
    }

    $level_id         = $data['level_id'];
    $word             = $data['word'];
    $meaning          = $data['meaning'];
    $type             = $data['type'];
    $example_sentence = $data['example_sentence'];
    // DB hiện đang dùng image_url, audio_url (theo file sample)
    $image_url        = $data['image_url'] ?? '';
    $audio_url        = $data['audio_url'] ?? '';
}

// Xử lý submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $level_id         = (int)($_POST['level_id'] ?? 0);
    $word             = trim($_POST['word'] ?? '');
    $meaning          = trim($_POST['meaning'] ?? '');
    $type             = $_POST['type'] ?? 'flashcard';
    $example_sentence = trim($_POST['example_sentence'] ?? '');
    $image_url        = trim($_POST['image_url'] ?? '');
    $audio_url        = trim($_POST['audio_url'] ?? '');

    // Validate cơ bản
    if ($level_id <= 0)  $errors[] = 'Vui lòng chọn Level.';
    if ($word === '')    $errors[] = 'Vui lòng nhập Word.';
    if ($meaning === '') $errors[] = 'Vui lòng nhập Meaning.';

    // Upload ảnh (nếu có)
    if (!empty($_FILES['image_file']['name'])) {
        $file = $_FILES['image_file'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allow = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($ext, $allow)) {
                $errors[] = 'Ảnh chỉ hỗ trợ: ' . implode(', ', $allow);
            } else {
                $newName = 'img_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $imageDir . $newName)) {
                    // Lưu path local vào DB
                    $image_url = 'uploads/images/' . $newName;
                } else {
                    $errors[] = 'Không lưu được file ảnh lên server.';
                }
            }
        } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Có lỗi xảy ra khi upload ảnh.';
        }
    }

    // Upload audio (nếu có)
    if (!empty($_FILES['audio_file']['name'])) {
        $file = $_FILES['audio_file'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allow = ['mp3', 'wav', 'ogg', 'm4a'];
            if (!in_array($ext, $allow)) {
                $errors[] = 'Audio chỉ hỗ trợ: ' . implode(', ', $allow);
            } else {
                $newName = 'aud_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $audioDir . $newName)) {
                    // Lưu path local vào DB
                    $audio_url = 'uploads/audio/' . $newName;
                } else {
                    $errors[] = 'Không lưu được file audio lên server.';
                }
            }
        } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Có lỗi xảy ra khi upload audio.';
        }
    }

    // Nếu không có lỗi -> lưu DB
    if (!$errors) {
        if ($isEdit) {
            $stmt = $pdo->prepare("
                UPDATE vocabularies
                SET level_id = ?, word = ?, meaning = ?, type = ?, example_sentence = ?, image_url = ?, audio_url = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $level_id,
                $word,
                $meaning,
                $type,
                $example_sentence,
                $image_url,
                $audio_url,
                $id
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO vocabularies (level_id, word, meaning, type, example_sentence, image_url, audio_url)
                VALUES (?,?,?,?,?,?,?)
            ");
            $stmt->execute([
                $level_id,
                $word,
                $meaning,
                $type,
                $example_sentence,
                $image_url,
                $audio_url
            ]);
        }

        header('Location: /admin/vocab_list.php');
        exit;
    }
}

// Chuẩn bị preview ảnh & audio (URL ngoài hoặc path local)
$imgPreview   = $image_url;
$audioPreview = $audio_url;

if ($imgPreview && !preg_match('~^https?://~i', $imgPreview)) {
    $imgPreview = '/' . ltrim($imgPreview, '/');
}
if ($audioPreview && !preg_match('~^https?://~i', $audioPreview)) {
    $audioPreview = '/' . ltrim($audioPreview, '/');
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= $isEdit ? 'Sửa từ vựng' : 'Thêm từ vựng' ?> | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7AE582',
                        'primary-dark': '#16a34a'
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .scrollbar-thin::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.7);
            border-radius: 999px;
        }
    </style>
</head>

<body class="bg-slate-50">

    <div class="h-screen flex overflow-hidden">

        <!-- SIDEBAR (giống admin/index.php, Từ vựng active) -->
        <!-- SIDEBAR (giống index/levels) -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed inset-y-0 left-0">
            <!-- Logo -->
            <div class="flex items-center gap-2 px-5 py-4 border-b border-slate-100">
                <div
                    class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-primary-dark to-primary flex items-center justify-center text-white">
                    <i class="fa-solid fa-chalkboard-user text-sm"></i>
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="text-[0.7rem] text-slate-400">English Learning System</span>
                </div>
            </div>

            <!-- Admin info -->
            <div class="px-5 pt-4 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-primary/80 flex items-center justify-center text-sm font-semibold text-emerald-950">
                        A
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-slate-800">
                            Administrator
                        </span>
                        <span class="text-[0.7rem] text-slate-400">
                            Quản trị viên
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-center text-[0.7rem]">
                    <div class="rounded-xl bg-primary/20 text-emerald-900 py-2">
                        <p class="font-semibold mb-0.5"><?= $totalLevels ?></p>
                        <p class="text-[0.65rem]">Levels</p>
                    </div>
                    <div class="rounded-xl bg-primary/20 text-emerald-900 py-2">
                        <p class="font-semibold mb-0.5"><?= $totalVocab ?></p>
                        <p class="text-[0.65rem]">Từ vựng</p>
                    </div>
                </div>
            </div>

            <!-- Menu -->
            <nav class="flex-1 px-3 py-4 text-sm">
                <p class="px-2 mb-2 text-[0.7rem] uppercase tracking-wide text-slate-400">
                    Điều hướng
                </p>

                <a href="/admin/index.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-2xl text-slate-600 hover:bg-slate-100 mb-1">
                    <i class="fa-solid fa-gauge text-xs"></i>
                    <span>Dashboard</span>
                </a>

                <a href="/admin/levels.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-2xl text-slate-600 hover:bg-slate-100 mb-1">
                    <i class="fa-solid fa-layer-group text-xs"></i>
                    <span>Quản lý Levels</span>
                </a>

                <a href="/admin/vocab_list.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-2xl bg-primary text-emerald-950 font-semibold mb-1">
                    <i class="fa-solid fa-book-open text-xs"></i>
                    <span>Quản lý Từ vựng</span>
                </a>

                <a href="/admin/users.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-2xl text-slate-600 hover:bg-slate-100 mb-1">
                    <i class="fa-solid fa-users text-xs"></i>
                    <span>Người dùng</span>
                </a>

                <a href="/admin/stats.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-2xl text-slate-600 hover:bg-slate-100 mb-1">
                    <i class="fa-solid fa-chart-line text-xs"></i>
                    <span>Thống kê</span>
                </a>
            </nav>

            <!-- Logout -->
            <div class="px-4 py-4 border-t border-slate-100">
                <a href="/index.php"
                    class="flex items-center justify-center gap-2 text-xs text-slate-500 hover:text-primary-dark mb-2">
                    <i class="fa-solid fa-arrow-left"></i> Về trang học viên
                </a>
                <a href="/auth/logout.php"
                    class="w-full flex items-center justify-center gap-2 text-xs font-semibold px-3 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-red-50 hover:text-red-500">
                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    <span>Đăng xuất</span>
                </a>
            </div>
        </aside>
        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col ml-64">
            <!-- Top bar -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">
                        <?= $isEdit ? 'Sửa từ vựng' : 'Thêm từ vựng mới' ?>
                    </h1>
                    <p class="text-[0.75rem] text-slate-400 mt-0.5">
                        Quản lý dữ liệu từ vựng dùng cho Flashcard & Practice.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <a href="/admin/vocab_list.php"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-2xl bg-slate-100 text-slate-600 hover:bg-slate-200">
                        <i class="fa-solid fa-arrow-left text-[0.7rem]"></i>
                        <span>Quay lại danh sách</span>
                    </a>
                </div>
            </header>

            <!-- Content -->
            <section class="flex-1 p-6 overflow-x-hidden overflow-y-auto scrollbar-thin">
                <div class="max-w-5xl mx-auto">

                    <?php if ($errors): ?>
                        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
                            <p class="font-semibold mb-1">Vui lòng kiểm tra lại:</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                <?php foreach ($errors as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data"
                        class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-6 space-y-5">

                        <!-- Hàng 1: Level + Word + Meaning -->
                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                    Level <span class="text-red-500">*</span>
                                </label>
                                <select name="level_id"
                                    class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3">
                                    <option value="">-- Chọn Level --</option>
                                    <?php foreach ($levels as $lv): ?>
                                        <option value="<?= (int)$lv['id'] ?>"
                                            <?= $lv['id'] == $level_id ? 'selected' : '' ?>>
                                            Level <?= (int)$lv['id'] ?> – <?= htmlspecialchars($lv['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                    Word <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="word"
                                    class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3"
                                    placeholder="Ví dụ: Apple"
                                    value="<?= htmlspecialchars($word) ?>">
                            </div>

                            <div>
                                <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                    Meaning (nghĩa) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="meaning"
                                    class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3"
                                    placeholder="Ví dụ: Quả táo"
                                    value="<?= htmlspecialchars($meaning) ?>">
                            </div>
                        </div>

                        <!-- Hàng 2: Type + Example sentence -->
                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                    Type
                                </label>
                                <select name="type"
                                    class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3">
                                    <option value="flashcard" <?= $type === 'flashcard' ? 'selected' : '' ?>>flashcard</option>
                                    <option value="fill_gap" <?= $type === 'fill_gap' ? 'selected' : '' ?>>fill_gap</option>
                                    <option value="mixed" <?= $type === 'mixed' ? 'selected' : '' ?>>mixed</option>
                                </select>
                                <p class="mt-1 text-[0.7rem] text-slate-400 leading-snug">
                                    • <b>flashcard</b>: dùng cho thẻ học từ (hiển thị hình, audio, nghĩa.<br>
                                    • <b>fill_gap</b>: dùng cho câu hỏi điền từ vào chỗ trống trong Practice.<br>
                                    • <b>mixed</b>: dùng cho cả flashcard & practice, hệ thống có thể random dạng câu hỏi.
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                    Example sentence (câu ví dụ)
                                </label>
                                <textarea name="example_sentence" rows="2"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 py-2"
                                    placeholder="Ví dụ: The apple is red."><?= htmlspecialchars($example_sentence) ?></textarea>
                            </div>
                        </div>

                        <!-- Hàng 3: Media (Ảnh + Audio) -->
                        <div class="grid gap-4 md:grid-cols-2">
                            <!-- Ảnh -->
                            <div class="border border-slate-200 rounded-2xl p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-[0.8rem] font-semibold text-slate-800 flex items-center gap-1">
                                        <i class="fa-solid fa-image text-primary-dark text-xs"></i>
                                        Ảnh minh họa
                                    </p>
                                    <?php if ($imgPreview): ?>
                                        <span class="text-[0.65rem] text-slate-400">Đã có ảnh</span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($imgPreview): ?>
                                    <div class="mb-3">
                                        <img src="<?= htmlspecialchars($imgPreview) ?>"
                                            alt=""
                                            class="w-full max-h-32 rounded-lg border border-slate-200 object-contain bg-slate-50">
                                    </div>
                                <?php endif; ?>

                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-[0.7rem] text-slate-500 mb-1">
                                            Upload file ảnh (tùy chọn)
                                        </label>
                                        <input type="file" name="image_file"
                                            class="block w-full text-[0.75rem] text-slate-600">
                                        <p class="text-[0.65rem] text-slate-400 mt-0.5">
                                            Hỗ trợ: jpg, jpeg, png, gif, webp.
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-[0.7rem] text-slate-500 mb-1">
                                            Hoặc nhập URL ảnh
                                        </label>
                                        <input type="text" name="image_url"
                                            class="w-full h-8 rounded-2xl border border-slate-200 bg-slate-50 text-[0.75rem] px-3"
                                            placeholder="Ví dụ: https://loremflickr.com/400/300/apple"
                                            value="<?= htmlspecialchars($image_url) ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Audio -->
                            <div class="border border-slate-200 rounded-2xl p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-[0.8rem] font-semibold text-slate-800 flex items-center gap-1">
                                        <i class="fa-solid fa-volume-high text-primary-dark text-xs"></i>
                                        File audio
                                    </p>
                                    <?php if ($audioPreview): ?>
                                        <span class="text-[0.65rem] text-slate-400">Đã có audio</span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($audioPreview): ?>
                                    <div class="mb-3">
                                        <audio controls class="w-full">
                                            <source src="<?= htmlspecialchars($audioPreview) ?>" type="audio/mpeg">
                                        </audio>
                                    </div>
                                <?php endif; ?>

                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-[0.7rem] text-slate-500 mb-1">
                                            Upload file audio (tùy chọn)
                                        </label>
                                        <input type="file" name="audio_file"
                                            class="block w-full text-[0.75rem] text-slate-600">
                                        <p class="text-[0.65rem] text-slate-400 mt-0.5">
                                            Hỗ trợ: mp3, wav, ogg, m4a.
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-[0.7rem] text-slate-500 mb-1">
                                            Hoặc nhập URL audio
                                        </label>
                                        <input type="text" name="audio_url"
                                            class="w-full h-8 rounded-2xl border border-slate-200 bg-slate-50 text-[0.75rem] px-3"
                                            placeholder="Ví dụ: https://ssl.gstatic.com/dictionary/static/sounds/oxford/apple--_gb_1.mp3"
                                            value="<?= htmlspecialchars($audio_url) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nút -->
                        <div class="flex items-center justify-between pt-2">
                            <p class="text-[0.7rem] text-slate-400">
                                Các trường có dấu <span class="text-red-500">*</span> là bắt buộc.
                            </p>
                            <div class="flex gap-2">
                                <a href="/admin/vocab_list.php"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-2xl bg-slate-100 text-[0.75rem] text-slate-600 hover:bg-slate-200">
                                    <i class="fa-solid fa-arrow-left text-[0.7rem]"></i>
                                    <span>Hủy</span>
                                </a>
                                <button
                                    class="inline-flex items-center gap-1 px-4 py-1.5 rounded-2xl bg-primary text-emerald-950 text-[0.75rem] font-semibold hover:bg-primary-dark hover:text-emerald-50">
                                    <i class="fa-solid fa-floppy-disk text-[0.75rem]"></i>
                                    <span>Lưu từ vựng</span>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </section>
        </main>
    </div>

</body>

</html>