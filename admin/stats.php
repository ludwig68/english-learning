<?php
// admin/stats.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

/* ================== STATS CƠ BẢN ================== */
$totalLevels = (int)$pdo->query("SELECT COUNT(*) FROM levels")->fetchColumn();
$totalVocab  = (int)$pdo->query("SELECT COUNT(*) FROM vocabularies WHERE deleted_at IS NULL")->fetchColumn();
$totalUsers  = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL")->fetchColumn();

$admins = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND deleted_at IS NULL")->fetchColumn();
$students = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user' AND deleted_at IS NULL")->fetchColumn();

/* ================== VOCAB PER LEVEL (BAR CHART) ================== */
$stmt = $pdo->query("
    SELECT 
        l.id,
        l.name,
        COUNT(v.id) AS vocab_count
    FROM levels l
    LEFT JOIN vocabularies v 
        ON v.level_id = l.id AND v.deleted_at IS NULL
    GROUP BY l.id, l.name
    ORDER BY l.id
");
$levelStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$levelLabels = [];
$levelCounts = [];
foreach ($levelStats as $row) {
    $levelLabels[] = $row['name'];
    $levelCounts[] = (int)$row['vocab_count'];
}

/* ================== VOCAB TYPE RATIO (PIE / DOUGHNUT) ================== */
$stmt = $pdo->query("
    SELECT type, COUNT(*) AS cnt
    FROM vocabularies
    WHERE deleted_at IS NULL
    GROUP BY type
");
$typeRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Bảo đảm luôn có đủ 3 loại
$typeMap = [
    'flashcard' => 0,
    'fill_gap'  => 0,
    'mixed'     => 0
];
foreach ($typeRows as $r) {
    if (isset($typeMap[$r['type']])) {
        $typeMap[$r['type']] = (int)$r['cnt'];
    }
}

$typeLabels = ['Flashcard', 'Fill Gap', 'Mixed'];
$typeData   = [
    $typeMap['flashcard'],
    $typeMap['fill_gap'],
    $typeMap['mixed']
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thống kê | English Learning Admin</title>
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
               class="flex items-center gap-2 px-3 py-2 rounded-2xl bg-primary text-emerald-950 font-semibold mb-1">
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
                <h1 class="text-lg font-semibold text-slate-900">Thống kê hệ thống</h1>
                <p class="text-[0.75rem] text-slate-400 mt-0.5">
                    Tổng quan dữ liệu Levels, Từ vựng và Người dùng, kèm biểu đồ trực quan.
                </p>
            </div>
            <div class="hidden sm:flex items-center gap-3 text-xs">
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500">
                    Tổng <?= $totalUsers ?> tài khoản
                </span>
            </div>
        </header>

        <!-- Content -->
        <section class="flex-1 p-6 overflow-x-hidden overflow-y-auto scrollbar-thin">
            <div class="max-w-6xl mx-auto space-y-6">

                <!-- Cards tổng quan -->
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs text-slate-500">Levels</p>
                            <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center">
                                <i class="fa-solid fa-layer-group text-[0.75rem] text-primary-dark"></i>
                            </span>
                        </div>
                        <p class="text-2xl font-semibold text-slate-900 mb-1"><?= $totalLevels ?></p>
                        <p class="text-[0.7rem] text-slate-400">Lộ trình học được cấu hình</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs text-slate-500">Tổng từ vựng</p>
                            <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center">
                                <i class="fa-solid fa-book-open text-[0.75rem] text-primary-dark"></i>
                            </span>
                        </div>
                        <p class="text-2xl font-semibold text-slate-900 mb-1"><?= $totalVocab ?></p>
                        <p class="text-[0.7rem] text-slate-400">Đang dùng cho Flashcard & Practice</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs text-slate-500">Người dùng</p>
                            <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center">
                                <i class="fa-solid fa-users text-[0.75rem] text-primary-dark"></i>
                            </span>
                        </div>
                        <p class="text-2xl font-semibold text-slate-900 mb-1"><?= $totalUsers ?></p>
                        <p class="text-[0.7rem] text-slate-400">
                            <?= $admins ?> Admin · <?= $students ?> User
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs text-slate-500">Tỷ lệ loại từ vựng</p>
                            <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center">
                                <i class="fa-solid fa-chart-pie text-[0.75rem] text-primary-dark"></i>
                            </span>
                        </div>
                        <p class="text-2xl font-semibold text-slate-900 mb-1">
                            <?php
                            $nonZero = array_filter($typeData);
                            echo count($nonZero);
                            ?>
                        </p>
                        <p class="text-[0.7rem] text-slate-400">Số loại có dữ liệu (Flashcard / Fill Gap / Mixed)</p>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid gap-4 lg:grid-cols-2">
                    <!-- Biểu đồ cột: vocab per level -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center">
                                    <i class="fa-solid fa-chart-column text-[0.75rem] text-primary-dark"></i>
                                </span>
                                Từ vựng theo từng Level
                            </h2>
                            <span class="text-[0.7rem] text-slate-400">
                                Số lượng từ vựng / Level
                            </span>
                        </div>
                        <div class="h-64">
                            <canvas id="levelBarChart"></canvas>
                        </div>
                    </div>

                    <!-- Biểu đồ tròn: tỉ lệ loại từ -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center">
                                    <i class="fa-solid fa-circle-half-stroke text-[0.75rem] text-primary-dark"></i>
                                </span>
                                Tỉ lệ loại câu hỏi
                            </h2>
                            <span class="text-[0.7rem] text-slate-400">
                                Flashcard vs Fill Gap vs Mixed
                            </span>
                        </div>
                        <div class="h-64 flex items-center justify-center">
                            <canvas id="typePieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    // Dữ liệu PHP -> JS
    const levelLabels = <?= json_encode($levelLabels, JSON_UNESCAPED_UNICODE) ?>;
    const levelCounts = <?= json_encode($levelCounts) ?>;

    const typeLabels = <?= json_encode($typeLabels, JSON_UNESCAPED_UNICODE) ?>;
    const typeData   = <?= json_encode($typeData) ?>;

    // Biểu đồ cột: vocab per level
    const ctxBar = document.getElementById('levelBarChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: levelLabels,
            datasets: [{
                label: 'Số từ vựng',
                data: levelCounts,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Biểu đồ tròn (doughnut): vocab type
    const ctxPie = document.getElementById('typePieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeData,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: { size: 11 }
                    }
                }
            },
            cutout: '55%'
        }
    });
</script>

</body>
</html>
