<?php
// admin/levels.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

// Số liệu cho sidebar
$totalLevels = (int)$pdo->query("SELECT COUNT(*) FROM levels")->fetchColumn();
$totalVocab  = (int)$pdo->query("SELECT COUNT(*) FROM vocabularies WHERE deleted_at IS NULL")->fetchColumn();

// Thông báo
$success = $_GET['success'] ?? '';
$error   = $_GET['error'] ?? '';

// Xử lý thêm / sửa Level
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_level'])) {
    $id   = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if ($name === '') {
        $error = 'Tên Level không được để trống.';
    } else {
        if ($id > 0) {
            // Update
            $stmt = $pdo->prepare("UPDATE levels SET name = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $desc, $id]);
            header('Location: levels.php?success=Đã cập nhật Level thành công');
            exit;
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO levels (name, description) VALUES (?, ?)");
            $stmt->execute([$name, $desc]);
            header('Location: levels.php?success=Đã thêm Level mới');
            exit;
        }
    }
}

// Xử lý xóa Level
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Kiểm tra còn từ vựng hay không
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vocabularies WHERE level_id = ? AND deleted_at IS NULL");
    $stmt->execute([$id]);
    $countVocab = (int)$stmt->fetchColumn();

    if ($countVocab > 0) {
        header('Location: levels.php?error=Không thể xóa Level vì còn ' . $countVocab . ' từ vựng đang sử dụng');
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM levels WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: levels.php?success=Đã xóa Level thành công');
    exit;
}

// Nếu có tham số edit -> lấy dữ liệu để đổ vào form
$editingLevel = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt   = $pdo->prepare("SELECT * FROM levels WHERE id = ?");
    $stmt->execute([$editId]);
    $editingLevel = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Danh sách Levels + số từ vựng
$stmt = $pdo->query("
    SELECT 
        l.id,
        l.name,
        l.description,
        COUNT(v.id) AS vocab_count
    FROM levels l
    LEFT JOIN vocabularies v 
        ON v.level_id = l.id AND v.deleted_at IS NULL
    GROUP BY l.id, l.name, l.description
    ORDER BY l.id
");
$levels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Levels | English Learning</title>
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
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

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

    <!-- SIDEBAR -->
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
               class="flex items-center gap-2 px-3 py-2 rounded-2xl bg-primary text-emerald-950 font-semibold mb-1">
                <i class="fa-solid fa-layer-group text-xs"></i>
                <span>Quản lý Levels</span>
            </a>

            <a href="/admin/vocab_list.php"
               class="flex items-center gap-2 px-3 py-2 rounded-2xl text-slate-600 hover:bg-slate-100 mb-1">
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
                <h1 class="text-lg font-semibold text-slate-900">Quản lý Levels</h1>
                <p class="text-[0.75rem] text-slate-400 mt-0.5">
                    Thêm, chỉnh sửa và xóa các Level trong hệ thống học tiếng Anh.
                </p>
            </div>
            <div>
                <a href="/admin/index.php"
                   class="text-xs text-slate-500 hover:text-primary-dark flex items-center gap-1">
                    <i class="fa-solid fa-arrow-left"></i> Về Dashboard
                </a>
        </header>

        <!-- Content -->
        <section class="flex-1 p-6 overflow-x-hidden overflow-y-auto scrollbar-thin">
            <div class="max-w-6xl mx-auto space-y-4">

                <!-- Alerts -->
                <?php if ($success): ?>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">
                        <i class="fa-solid fa-circle-check mr-1"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="grid gap-4 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
                    <!-- Form Level -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5">
                        <h2 class="text-sm font-semibold text-slate-900 mb-2 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center">
                                <i class="fa-solid fa-pen-to-square text-[0.75rem] text-primary-dark"></i>
                            </span>
                            <?= $editingLevel ? 'Chỉnh sửa Level' : 'Thêm Level mới' ?>
                        </h2>
                        <p class="text-[0.75rem] text-slate-500 mb-4">
                            Đặt tên rõ ràng theo trình độ (ví dụ: Pre-Beginner, Junior, IELTS 5.5–7.0...).
                        </p>

                        <form method="post" class="space-y-3">
                            <input type="hidden" name="id"
                                   value="<?= $editingLevel ? (int)$editingLevel['id'] : 0 ?>">

                            <div>
                                <label class="text-xs text-slate-600 mb-1 block">Tên Level</label>
                                <input type="text" name="name"
                                       value="<?= htmlspecialchars($editingLevel['name'] ?? '') ?>"
                                       class="w-full rounded-xl bg-white border border-slate-300 text-sm px-3 py-2
                                              focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                            </div>

                            <div>
                                <label class="text-xs text-slate-600 mb-1 block">Mô tả</label>
                                <textarea name="description" rows="4"
                                          class="w-full rounded-xl bg-white border border-slate-300 text-sm px-3 py-2
                                                 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary"><?= htmlspecialchars($editingLevel['description'] ?? '') ?></textarea>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <button type="submit" name="save_level"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-emerald-950 text-xs font-semibold hover:bg-primary-dark hover:text-emerald-50 transition">
                                    <i class="fa-solid fa-floppy-disk text-[0.75rem]"></i>
                                    <?= $editingLevel ? 'Lưu thay đổi' : 'Thêm Level' ?>
                                </button>

                                <?php if ($editingLevel): ?>
                                    <a href="/admin/levels.php"
                                       class="text-[0.75rem] text-slate-400 hover:text-slate-600">
                                        Hủy chỉnh sửa
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <!-- Danh sách Levels -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center">
                                    <i class="fa-solid fa-layer-group text-[0.75rem] text-primary-dark"></i>
                                </span>
                                Danh sách Levels
                            </h2>
                            <span class="text-[0.7rem] text-slate-400">
                                Tổng: <?= count($levels) ?> level
                            </span>
                        </div>

                        <?php if ($levels): ?>
                            <div class="space-y-2">
                                <?php foreach ($levels as $lv): ?>
                                    <div class="flex items-center gap-3 bg-slate-50 rounded-2xl px-3 sm:px-4 py-3">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-slate-900 flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-white border border-slate-200 text-[0.7rem] text-slate-500">
                                                    #<?= (int)$lv['id'] ?>
                                                </span>
                                                <?= htmlspecialchars($lv['name']) ?>
                                            </p>
                                            <p class="text-[0.75rem] text-slate-500 mt-0.5">
                                                <?= htmlspecialchars($lv['description']) ?>
                                            </p>
                                        </div>
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="px-3 py-1 rounded-full bg-primary text-[0.7rem] text-emerald-950 font-semibold">
                                                <?= (int)$lv['vocab_count'] ?> từ
                                            </span>
                                            <div class="flex gap-1 text-[0.7rem]">
                                                <a href="/admin/levels.php?edit=<?= (int)$lv['id'] ?>"
                                                   class="px-2 py-1 rounded-lg bg-white border border-slate-300 text-slate-600 hover:border-primary hover:text-primary-dark">
                                                    <i class="fa-solid fa-pen text-[0.65rem]"></i> Sửa
                                                </a>
                                                <a href="/admin/levels.php?delete=<?= (int)$lv['id'] ?>"
                                                   onclick="return confirm('Xóa Level này? Hành động không thể hoàn tác.');"
                                                   class="px-2 py-1 rounded-lg bg-white border border-red-200 text-red-500 hover:bg-red-50">
                                                    <i class="fa-solid fa-trash text-[0.65rem]"></i> Xóa
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-[0.75rem] text-slate-500">
                                Chưa có Level nào. Hãy thêm Level mới ở khung bên trái.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </section>
    </main>
</div>
</body>
</html>
