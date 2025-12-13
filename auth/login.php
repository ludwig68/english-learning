<?php
// auth/login.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$username = '';
$error    = '';

// Nhận thông báo lỗi khi bị chuyển hướng do tài khoản bị khóa/xóa
$errorCode = $_GET['error'] ?? '';
if ($errorCode === 'blocked') {
    $error = 'Tài khoản đã bị khóa hoặc không còn tồn tại. Vui lòng liên hệ quản trị viên.';
}

// ================== Xử lý ĐĂNG NHẬP (TRƯỚC KHI GỌI HEADER.PHP) ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if (!empty($user['deleted_at'])) {
            $error = 'Tài khoản đã bị xóa. Liên hệ quản trị viên để được khôi phục.';
        } elseif ($user['status'] === 'blocked') {
            $error = 'Tài khoản đang bị khóa. Liên hệ quản trị viên để mở khóa.';
        } else {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: /admin/index.php');
            } else {
                header('Location: /user/dashboard.php');
            }
            exit;
        }
    } else {
        $error = 'Sai tên đăng nhập hoặc mật khẩu.';
    }
}

// ================== HTML ==================
$pageTitle = 'Đăng nhập | English Learning';
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
            Chào mừng trở lại!
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mb-6">
            Đăng nhập để tiếp tục học tập và ôn luyện từ vựng. Phiên sẽ kết thúc khi
            bạn đóng trình duyệt để bảo vệ tài khoản.
        </p>

        <?php if ($error): ?>
            <div class="mb-4 text-xs text-red-600 bg-red-50 border border-red-200 rounded-md p-3">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <!-- Username -->
            <div class="space-y-1">
                <label class="text-xs font-medium text-slate-700">Tên đăng nhập</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fa-regular fa-envelope text-sm"></i>
                    </span>
                    <input
                        type="text"
                        name="username"
                        value="<?= htmlspecialchars($username) ?>"
                        class="w-full rounded-lg bg-slate-50 border border-slate-200 text-sm px-10 py-2.5 placeholder:text-slate-400 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#7AE582] focus:border-[#7AE582]"
                        placeholder="Tên đăng nhập"
                        required>
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
                        class="w-full rounded-lg bg-slate-50 border border-slate-200 text-sm px-10 py-2.5 placeholder:text-slate-400 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#7AE582] focus:border-[#7AE582]"
                        placeholder="••••••••"
                        required>
                    <!-- icon mắt chỉ là decor -->
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                        <i class="fa-regular fa-eye text-sm"></i>
                    </span>
                </div>
            </div>

            <!-- Forgot password placeholder -->
            <div class="flex justify-end text-[0.75rem] text-slate-500">
                <a href="#" class="text-[#7AE582] hover:underline">
                    Quên mật khẩu?
                </a>
            </div>

            <!-- Button login (gradient xanh #7AE582) -->
            <button
                type="submit"
                class="w-full mt-1 py-2.5 text-sm font-semibold text-white rounded-lg flex items-center justify-center gap-2 transition shadow-md"
                style="background: linear-gradient(90deg, #7AE582, #54CC6D);">
                <span>Đăng nhập</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

        <!-- Divider -->
        <div class="flex items-center gap-3 my-6">
            <span class="flex-1 h-px bg-slate-200"></span>
            <span class="text-[0.7rem] text-slate-400">Hoặc đăng nhập với</span>
            <span class="flex-1 h-px bg-slate-200"></span>
        </div>

        <!-- Social buttons (dummy) -->
        <div class="grid grid-cols-2 gap-3 mb-4">
            <button
                type="button"
                class="flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white py-2 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition">
                <i class="fa-brands fa-google text-sm"></i>
                <span>Google</span>
            </button>
            <button
                type="button"
                class="flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white py-2 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition">
                <i class="fa-brands fa-facebook-f text-sm text-[#1877F2]"></i>
                <span>Facebook</span>
            </button>
        </div>

        <!-- Register link -->
        <p class="mt-4 text-center text-xs text-slate-500">
            Chưa có tài khoản?
            <a href="/auth/register.php" class="font-medium" style="color:#7AE582;">
                Đăng ký ngay
            </a>
        </p>
        <!-- ========= FORM DEMO ========= -->
        <div class="mt-5 relative overflow-hidden rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 p-4">
            <div class="absolute -top-6 -right-6 w-16 h-16 bg-blue-100 rounded-full blur-xl opacity-50"></div>

            <div class="relative z-10">
                <h4 class="text-xs font-bold text-blue-800 flex items-center gap-2 mb-3">
                    <i class="fa-solid fa-key"></i> Tài khoản đang thử
                </h4>

                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 flex items-center justify-between bg-white/60 backdrop-blur-sm border border-blue-100 px-3 py-2 rounded-md shadow-sm">
                        <span class="text-[0.7rem] font-semibold text-slate-500">ADMIN</span>
                        <div class="text-xs font-mono text-slate-700">
                            admin <span class="text-slate-300 mx-1">/</span> 123456
                        </div>
                    </div>

                    <div class="flex-1 flex items-center justify-between bg-white/60 backdrop-blur-sm border border-blue-100 px-3 py-2 rounded-md shadow-sm">
                        <span class="text-[0.7rem] font-semibold text-slate-500">USER</span>
                        <div class="text-xs font-mono text-slate-700">
                            user <span class="text-slate-300 mx-1">/</span> 123456
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ========= HẾT FORM DEMO ========= -->
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
