<?php
// auth/register.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$username = '';
$errors   = [];

// ================== XỬ LÝ ĐĂNG KÝ (TRƯỚC KHI GỌI HEADER) ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username         = trim($_POST['username'] ?? '');
    $password         = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($username === '') {
        $errors[] = 'Username không được để trống.';
    }

    if ($password === '') {
        $errors[] = 'Mật khẩu không được để trống.';
    }

    if ($password !== $password_confirm) {
        $errors[] = 'Mật khẩu xác nhận không khớp.';
    }

    if (!$errors) {
        // Kiểm tra username tồn tại
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $errors[] = 'Username đã tồn tại.';
        } else {
            // Tạo tài khoản mới
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            $stmt->execute([$username, $hash]);

            header('Location: /auth/login.php');
            exit;
        }
    }
}

// ================== HTML ==================
$pageTitle = 'Đăng ký | English Learning';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-center min-h-[calc(100vh-140px)]">
    <div class="w-full max-w-md bg-white/95 border border-slate-200 rounded-2xl shadow-lg px-8 py-8">
        <!-- Logo + tên hệ thống -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center shadow-md"
                 style="background: #7AE582;">
                <i class="fa-solid fa-graduation-cap text-white text-lg"></i>
            </div>
            <div class="flex flex-col leading-tight">
                <span class="text-sm font-semibold text-slate-900">English Learning</span>
                <span class="text-[0.75rem] text-slate-400">Học tiếng Anh miễn phí mỗi ngày</span>
            </div>
        </div>

        <!-- Tiêu đề -->
        <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 mb-1">
            Tạo tài khoản mới
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mb-6">
            Đăng ký để lưu tiến độ học và lộ trình tiếng Anh của bạn.
        </p>

        <?php if ($errors): ?>
            <div class="mb-4 text-xs text-red-600 bg-red-50 border border-red-200 rounded-md p-3">
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <!-- Username -->
            <div class="space-y-1">
                <label class="text-xs font-medium text-slate-700">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fa-regular fa-user text-sm"></i>
                    </span>
                    <input
                        type="text"
                        name="username"
                        value="<?= htmlspecialchars($username) ?>"
                        class="w-full rounded-lg bg-slate-50 border border-slate-200 text-sm px-10 py-2.5
                               placeholder:text-slate-400 text-slate-800
                               focus:outline-none focus:ring-2 focus:ring-[#7AE582] focus:border-[#7AE582]"
                        placeholder="nhập username của bạn"
                        required
                    >
                </div>
            </div>

            <!-- Password -->
            <div class="space-y-1">
                <label class="text-xs font-medium text-slate-700">Mật khẩu</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </span>
                    <input
                        type="password"
                        name="password"
                        class="w-full rounded-lg bg-slate-50 border border-slate-200 text-sm px-10 py-2.5
                               placeholder:text-slate-400 text-slate-800
                               focus:outline-none focus:ring-2 focus:ring-[#7AE582] focus:border-[#7AE582]"
                        placeholder="Tối thiểu 6 ký tự"
                        required
                    >
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1">
                <label class="text-xs font-medium text-slate-700">Nhập lại mật khẩu</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </span>
                    <input
                        type="password"
                        name="password_confirm"
                        class="w-full rounded-lg bg-slate-50 border border-slate-200 text-sm px-10 py-2.5
                               placeholder:text-slate-400 text-slate-800
                               focus:outline-none focus:ring-2 focus:ring-[#7AE582] focus:border-[#7AE582]"
                        placeholder="Nhập lại mật khẩu"
                        required
                    >
                </div>
            </div>

            <!-- Nút Đăng ký -->
            <button
                type="submit"
                class="w-full mt-1 py-2.5 text-sm font-semibold text-white rounded-lg
                       flex items-center justify-center gap-2
                       transition shadow-md"
                style="background: linear-gradient(90deg, #7AE582, #54CC6D);"
            >
                <span>Đăng ký</span>
                <i class="fa-solid fa-user-plus text-xs"></i>
            </button>
        </form>

        <!-- Link sang đăng nhập -->
        <p class="mt-6 text-center text-xs text-slate-500">
            Đã có tài khoản?
            <a href="/auth/login.php" class="font-medium" style="color:#7AE582;">
                Đăng nhập
            </a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
