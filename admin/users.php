<?php
// admin/users.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

// ===== Stats cho sidebar =====
$totalLevels = (int)$pdo->query("SELECT COUNT(*) FROM levels")->fetchColumn();
$totalVocab  = (int)$pdo->query("SELECT COUNT(*) FROM vocabularies WHERE deleted_at IS NULL")->fetchColumn();
$totalUsers  = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL")->fetchColumn();

// ===== Thông báo =====
$success = $_GET['success'] ?? '';
$error   = $_GET['error'] ?? '';

// ===== Xử lý hành động =====

// Ẩn (soft delete) người dùng
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Không cho xóa chính mình hoặc user admin mặc định (tuỳ bạn sửa)
    if (isset($_SESSION['user_id']) && $id === (int)$_SESSION['user_id']) {
        header('Location: users.php?error=Không thể ẩn tài khoản đang đăng nhập');
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE users 
        SET deleted_at = NOW()
        WHERE id = ? AND deleted_at IS NULL
    ");
    $stmt->execute([$id]);

    header('Location: users.php?success=Đã ẩn tài khoản khỏi danh sách');
    exit;
}

// Khôi phục người dùng
if (isset($_GET['restore'])) {
    $id = (int)$_GET['restore'];

    $stmt = $pdo->prepare("
        UPDATE users
        SET deleted_at = NULL
        WHERE id = ? AND deleted_at IS NOT NULL
    ");
    $stmt->execute([$id]);

    header('Location: users.php?success=Đã khôi phục tài khoản');
    exit;
}

// Khóa tài khoản
if (isset($_GET['block'])) {
    $id = (int)$_GET['block'];

    if (isset($_SESSION['user_id']) && $id === (int)$_SESSION['user_id']) {
        header('Location: users.php?error=Không thể khóa tài khoản đang đăng nhập');
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE users
        SET status = 'blocked'
        WHERE id = ? AND deleted_at IS NULL
    ");
    $stmt->execute([$id]);

    header('Location: users.php?success=Đã khóa tài khoản');
    exit;
}

// Mở khóa tài khoản
if (isset($_GET['unblock'])) {
    $id = (int)$_GET['unblock'];

    $stmt = $pdo->prepare("
        UPDATE users
        SET status = 'active'
        WHERE id = ? AND deleted_at IS NULL
    ");
    $stmt->execute([$id]);

    header('Location: users.php?success=Đã mở khóa tài khoản');
    exit;
}

// ===== Filter & Search =====
$filterRole   = trim($_GET['role']   ?? '');        // '' | admin | user
$filterStatus = trim($_GET['status'] ?? 'active');  // active | blocked | deleted | all
$q            = trim($_GET['q']      ?? '');

// Build query
$where  = [];
$params = [];

// Trạng thái + deleted_at
if ($filterStatus === 'deleted') {
    $where[] = "u.deleted_at IS NOT NULL";
} elseif ($filterStatus === 'all') {
    // không ràng buộc deleted_at
} else {
    // active hoặc blocked
    $where[]  = "u.deleted_at IS NULL";
    $where[]  = "u.status = ?";
    $params[] = $filterStatus === 'blocked' ? 'blocked' : 'active';
}

// Role
if ($filterRole !== '') {
    $where[]  = "u.role = ?";
    $params[] = $filterRole;
}

// Tìm kiếm
if ($q !== '') {
    $where[] = "(u.username LIKE ? OR u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
    $kw = '%' . $q . '%';
    $params[] = $kw;
    $params[] = $kw;
    $params[] = $kw;
    $params[] = $kw;
}

$sql = "
    SELECT u.*
    FROM users u
";

if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$sql .= " ORDER BY u.created_at DESC, u.id DESC LIMIT 200";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Người dùng | English Learning</title>
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
                        <p class="font-semibold mb-0.5"><?= $totalUsers ?></p>
                        <p class="text-[0.65rem]">Người dùng</p>
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
                    class="flex items-center gap-2 px-3 py-2 rounded-2xl bg-primary text-emerald-950 font-semibold mb-1">
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
                    <h1 class="text-lg font-semibold text-slate-900">Quản lý Người dùng</h1>
                    <p class="text-[0.75rem] text-slate-400 mt-0.5">
                        Tìm kiếm, lọc theo vai trò, trạng thái và quản lý tài khoản hệ thống.
                    </p>
                </div>
                <div class="hidden sm:flex items-center gap-3 text-xs">
                    <!-- Nút thêm người dùng -->
                    <a href="/admin/user_form.php"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-emerald-950 font-semibold hover:bg-primary-dark hover:text-emerald-50">
                        <i class="fa-solid fa-user-plus text-[0.75rem]"></i>
                        Thêm người dùng
                    </a>

                    <!-- Badge tổng số tài khoản -->
                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500">
                        Tổng: <?= $totalUsers ?> tài khoản
                    </span>
                </div>

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

                    <!-- Bộ lọc -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-4 mb-4">
                        <form method="get" class="flex flex-wrap gap-4 items-end">

                            <!-- Tìm kiếm -->
                            <div class="w-80 min-w-[230px]">
                                <label class="text-[0.75rem] text-slate-500 mb-1 block">Tìm kiếm</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-300 text-xs">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </span>
                                    <input
                                        type="text"
                                        name="q"
                                        value="<?= htmlspecialchars($q) ?>"
                                        placeholder="Username, họ tên, email, số điện thoại..."
                                        class="w-full h-8 pl-8 pr-3 rounded-2xl border border-slate-200 bg-slate-50 text-[0.75rem] placeholder:text-slate-400 focus:bg-white">
                                </div>
                            </div>

                            <!-- Role -->
                            <div class="w-40">
                                <label class="text-[0.75rem] text-slate-500 mb-1 block">Vai trò</label>
                                <select name="role"
                                    class="w-full h-8 rounded-2xl border border-slate-200 bg-white text-[0.7rem] px-3 leading-tight">
                                    <option value="">Tất cả</option>
                                    <option value="admin" <?= $filterRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="user" <?= $filterRole === 'user'  ? 'selected' : '' ?>>User</option>
                                </select>
                            </div>

                            <!-- Trạng thái -->
                            <div class="w-40">
                                <label class="text-[0.75rem] text-slate-500 mb-1 block">Trạng thái</label>
                                <select name="status"
                                    class="w-full h-8 rounded-2xl border border-slate-200 bg-white text-[0.7rem] px-3 leading-tight">
                                    <option value="active" <?= $filterStatus === 'active'  ? 'selected' : '' ?>>Đang hoạt động</option>
                                    <option value="blocked" <?= $filterStatus === 'blocked' ? 'selected' : '' ?>>Bị khóa</option>
                                    <option value="deleted" <?= $filterStatus === 'deleted' ? 'selected' : '' ?>>Đã ẩn</option>
                                    <option value="all" <?= $filterStatus === 'all'     ? 'selected' : '' ?>>Tất cả</option>
                                </select>
                            </div>

                            <!-- Nút -->
                            <div class="ml-auto flex items-end gap-2">
                                <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-2xl bg-primary text-emerald-950 text-[0.75rem] font-semibold hover:bg-primary-dark hover:text-emerald-50">
                                    <i class="fa-solid fa-filter text-[0.7rem]"></i> Lọc
                                </button>
                                <a href="/admin/users.php"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-2xl bg-slate-100 text-slate-600 text-[0.75rem] hover:bg-slate-200">
                                    <i class="fa-solid fa-rotate-left text-[0.7rem]"></i> Reset
                                </a>
                            </div>

                        </form>
                    </div>

                    <!-- Danh sách users -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center">
                                    <i class="fa-solid fa-users text-[0.75rem] text-primary-dark"></i>
                                </span>
                                Danh sách người dùng
                            </h2>
                            <span class="text-[0.7rem] text-slate-400">
                                Hiển thị tối đa 200 bản ghi (<?= count($users) ?> kết quả)
                            </span>
                        </div>

                        <?php if ($users): ?>
                            <div class="space-y-2">
                                <?php foreach ($users as $u): ?>
                                    <?php
                                    $isDeleted = !empty($u['deleted_at']);
                                    $isBlocked = ($u['status'] === 'blocked');
                                    $role      = $u['role'];
                                    $roleLabel = $role === 'admin' ? 'Admin' : 'User';
                                    $created   = $u['created_at'] ? date('d/m/Y H:i', strtotime($u['created_at'])) : '';
                                    ?>
                                    <div class="flex items-start gap-3 rounded-2xl px-3 sm:px-4 py-3
                                    <?= $isDeleted ? 'bg-red-50 border border-red-100' : 'bg-slate-50' ?>">

                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            <?php if (!empty($u['avatar'])): ?>
                                                <img src="/<?= htmlspecialchars($u['avatar']) ?>"
                                                    alt=""
                                                    class="w-10 h-10 rounded-full object-cover border border-slate-200">
                                            <?php else: ?>
                                                <div class="w-10 h-10 rounded-full bg-primary/80 flex items-center justify-center text-xs font-semibold text-emerald-950">
                                                    <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-1">
                                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                                <p class="text-sm font-medium text-slate-900">
                                                    <?= htmlspecialchars($u['full_name'] ?: $u['username']) ?>
                                                </p>
                                                <span class="px-2 py-0.5 rounded-full bg-white border border-slate-200 text-[0.65rem] text-slate-500">
                                                    @<?= htmlspecialchars($u['username']) ?>
                                                </span>
                                                <span class="px-2 py-0.5 rounded-full text-[0.65rem]
                                                <?= $role === 'admin' ? 'bg-amber-100 text-amber-700' : 'bg-sky-100 text-sky-700' ?>">
                                                    <?= $roleLabel ?>
                                                </span>
                                                <?php if ($isBlocked && !$isDeleted): ?>
                                                    <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-[0.65rem]">
                                                        Đang bị khóa
                                                    </span>
                                                <?php endif; ?>
                                                <?php if ($isDeleted): ?>
                                                    <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[0.65rem]">
                                                        Đã ẩn
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="text-[0.75rem] text-slate-600 space-y-0.5">
                                                <?php if (!empty($u['email'])): ?>
                                                    <p>
                                                        <i class="fa-solid fa-envelope text-[0.7rem] mr-1 text-slate-400"></i>
                                                        <?= htmlspecialchars($u['email']) ?>
                                                    </p>
                                                <?php endif; ?>
                                                <?php if (!empty($u['phone'])): ?>
                                                    <p>
                                                        <i class="fa-solid fa-phone text-[0.7rem] mr-1 text-slate-400"></i>
                                                        <?= htmlspecialchars($u['phone']) ?>
                                                    </p>
                                                <?php endif; ?>
                                                <?php if ($created): ?>
                                                    <p class="text-[0.7rem] text-slate-400">
                                                        <i class="fa-solid fa-clock text-[0.7rem] mr-1"></i>
                                                        Tạo lúc <?= $created ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex flex-col items-end gap-1 text-[0.7rem]">
                                            <a href="/admin/user_form.php?id=<?= (int)$u['id'] ?>"
                                                class="px-3 py-1 rounded-lg bg-white border border-slate-300 text-slate-600 hover:border-primary hover:text-primary-dark">
                                                <i class="fa-solid fa-pen text-[0.65rem]"></i> Sửa
                                            </a>

                                            <?php if (!$isDeleted): ?>
                                                <?php if ($isBlocked): ?>
                                                    <a href="/admin/users.php?unblock=<?= (int)$u['id'] ?>"
                                                        class="px-3 py-1 rounded-lg bg-emerald-100 text-emerald-800 hover:bg-emerald-200">
                                                        <i class="fa-solid fa-lock-open text-[0.65rem]"></i> Mở khóa
                                                    </a>
                                                <?php else: ?>
                                                    <a href="/admin/users.php?block=<?= (int)$u['id'] ?>"
                                                        onclick="return confirm('Khóa tài khoản này? Người dùng sẽ không đăng nhập được.');"
                                                        class="px-3 py-1 rounded-lg bg-white border border-orange-200 text-orange-600 hover:bg-orange-50">
                                                        <i class="fa-solid fa-lock text-[0.65rem]"></i> Khóa
                                                    </a>
                                                <?php endif; ?>

                                                <a href="/admin/users.php?delete=<?= (int)$u['id'] ?>"
                                                    onclick="return confirm('Ẩn tài khoản này? Người dùng sẽ không xuất hiện trong danh sách mặc định.');"
                                                    class="px-3 py-1 rounded-lg bg-white border border-red-200 text-red-500 hover:bg-red-50">
                                                    <i class="fa-solid fa-user-slash text-[0.65rem]"></i> Ẩn
                                                </a>
                                            <?php else: ?>
                                                <a href="/admin/users.php?restore=<?= (int)$u['id'] ?>"
                                                    class="px-3 py-1 rounded-lg bg-emerald-100 text-emerald-800 hover:bg-emerald-200">
                                                    <i class="fa-solid fa-rotate-left text-[0.65rem]"></i> Khôi phục
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-[0.75rem] text-slate-500">
                                Không tìm thấy tài khoản nào khớp với bộ lọc hiện tại.
                            </p>
                        <?php endif; ?>
                    </div>

                </div>
            </section>
        </main>
    </div>
</body>

</html>