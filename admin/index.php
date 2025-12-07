<?php
// admin/index.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

// Tổng quan
$totalLevels       = (int)$pdo->query("SELECT COUNT(*) FROM levels")->fetchColumn();
$totalVocab        = (int)$pdo->query("SELECT COUNT(*) FROM vocabularies WHERE deleted_at IS NULL")->fetchColumn();
$totalFlashcards   = (int)$pdo->query("SELECT COUNT(*) FROM vocabularies WHERE deleted_at IS NULL AND type = 'flashcard'")->fetchColumn();
$totalQuizPractice = (int)$pdo->query("SELECT COUNT(*) FROM vocabularies WHERE deleted_at IS NULL AND type IN ('fill_gap','mixed')")->fetchColumn();

// Levels overview + số từ trong mỗi level
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
    <title>Admin Dashboard | English Learning</title>
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
                    class="flex items-center gap-2 px-3 py-2 rounded-2xl bg-primary text-emerald-950 font-semibold mb-1">
                    <i class="fa-solid fa-gauge text-xs"></i>
                    <span>Dashboard</span>
                </a>

                <a href="/admin/levels.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-2xl text-slate-600 hover:bg-slate-100 mb-1">
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
                    <h1 class="text-lg font-semibold text-slate-900">Dashboard</h1>
                    <p class="text-[0.75rem] text-slate-400 mt-0.5">
                        Tổng quan levels, từ vựng và hoạt động luyện tập trong hệ thống.
                    </p>
                </div>
            </header>

            <!-- Content -->
            <section class="flex-1 p-6 overflow-x-hidden overflow-y-auto scrollbar-thin">
                <div class="max-w-6xl mx-auto space-y-6">

                    <!-- Stats cards -->
                    <div class="grid gap-4 md:grid-cols-4">
                        <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-slate-500">Tổng Levels</p>
                                <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center">
                                    <i class="fa-solid fa-layer-group text-[0.75rem] text-primary-dark"></i>
                                </span>
                            </div>
                            <p class="text-2xl font-semibold text-slate-900 mb-1"><?= $totalLevels ?></p>
                            <p class="text-[0.7rem] text-slate-400">Cấu trúc lộ trình học</p>
                        </div>

                        <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-slate-500">Tổng từ vựng</p>
                                <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center">
                                    <i class="fa-solid fa-book-open text-[0.75rem] text-primary-dark"></i>
                                </span>
                            </div>
                            <p class="text-2xl font-semibold text-slate-900 mb-1"><?= $totalVocab ?></p>
                            <p class="text-[0.7rem] text-slate-400">Đang sử dụng cho Flashcard & Practice</p>
                        </div>

                        <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-slate-500">Flashcards</p>
                                <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center">
                                    <i class="fa-regular fa-clone text-[0.75rem] text-primary-dark"></i>
                                </span>
                            </div>
                            <p class="text-2xl font-semibold text-slate-900 mb-1"><?= $totalFlashcards ?></p>
                            <p class="text-[0.7rem] text-slate-400">Từ có hình ảnh / audio minh họa</p>
                        </div>

                        <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-slate-500">Quiz & Practice</p>
                                <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center">
                                    <i class="fa-solid fa-clipboard-question text-[0.75rem] text-primary-dark"></i>
                                </span>
                            </div>
                            <p class="text-2xl font-semibold text-slate-900 mb-1"><?= $totalQuizPractice ?></p>
                            <p class="text-[0.7rem] text-slate-400">Từ dùng cho câu hỏi trắc nghiệm / điền từ</p>
                        </div>
                    </div>

                    <!-- Levels Overview -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-sm font-semibold text-slate-900">Levels Overview</h2>
                                <p class="text-[0.75rem] text-slate-400">
                                    Danh sách level hiện có và số lượng từ vựng trong từng level.
                                </p>
                            </div>
                            <a href="/admin/levels.php"
                                class="hidden sm:inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-primary/20 text-[0.7rem] text-primary-dark hover:bg-primary/30">
                                <i class="fa-solid fa-layer-group text-[0.7rem]"></i>
                                Quản lý Levels
                            </a>
                        </div>

                        <?php if ($levels): ?>
                            <div class="space-y-2">
                                <?php foreach ($levels as $lv): ?>
                                    <div class="flex items-center gap-3 bg-slate-50 rounded-2xl px-3 sm:px-4 py-3">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-slate-900">
                                                <?= htmlspecialchars($lv['name']) ?>
                                            </p>
                                            <p class="text-[0.75rem] text-slate-500">
                                                <?= htmlspecialchars($lv['description']) ?>
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 rounded-full bg-primary text-[0.7rem] text-emerald-950 font-semibold">
                                                <?= (int)$lv['vocab_count'] ?> từ
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-xs text-slate-500">
                                Chưa có Level nào trong hệ thống. Hãy thêm Level mới ở mục Quản lý Levels.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>

</html>