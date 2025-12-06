<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

$username = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header('Location: /admin/index.php');
        } else {
            header('Location: /user/learn.php');
        }
        exit;
    } else {
        $error = 'Sai username hoặc mật khẩu.';
    }
}
?>

<div class="flex items-center justify-center min-h-[calc(100vh-120px)]">
    <div class="card-glass w-full max-w-md p-6">
        <h2 class="text-xl font-semibold mb-1 text-center">Đăng nhập</h2>
        <p class="text-xs text-slate-500 mb-5 text-center">
            Tiếp tục với các bài học tiếng Anh của bạn.
        </p>

        <?php if ($error): ?>
            <div class="mb-4 text-xs text-red-600 bg-red-50 border border-red-200 rounded-md p-3">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-3">
            <div>
                <label class="text-xs text-slate-600 mb-1 block">Username</label>
                <input type="text" name="username"
                       class="w-full rounded-md bg-white border border-slate-300 text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#7AE582]"
                       value="<?= htmlspecialchars($username) ?>">
            </div>
            <div>
                <label class="text-xs text-slate-600 mb-1 block">Mật khẩu</label>
                <input type="password" name="password"
                       class="w-full rounded-md bg-white border border-slate-300 text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#7AE582]">
            </div>
            <button
                class="w-full mt-2 px-4 py-2 rounded-md bg-[#7AE582] text-slate-900 text-sm font-semibold hover:bg-emerald-300 transition">
                Đăng nhập
            </button>
        </form>

        <p class="mt-4 text-center text-xs text-slate-500">
            Chưa có tài khoản?
            <a href="/auth/register.php" class="text-[#16a34a] hover:underline">Đăng ký</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
