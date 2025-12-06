<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

$username = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($username === '') $errors[] = 'Username không được để trống.';
    if ($password === '') $errors[] = 'Mật khẩu không được để trống.';
    if ($password !== $password_confirm) $errors[] = 'Mật khẩu xác nhận không khớp.';

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'Username đã tồn tại.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username,password,role) VALUES (?,?, 'user')");
            $stmt->execute([$username, $hash]);
            header('Location: /auth/login.php');
            exit;
        }
    }
}
?>

<div class="flex items-center justify-center min-h-[calc(100vh-120px)]">
    <div class="card-glass w-full max-w-md p-6">
        <h2 class="text-xl font-semibold mb-1 text-center">Tạo tài khoản</h2>
        <p class="text-xs text-slate-500 mb-5 text-center">
            Học tiếng Anh miễn phí, lưu tiến độ và lộ trình của bạn.
        </p>

        <?php if ($errors): ?>
            <div class="mb-4 text-xs text-red-600 bg-red-50 border border-red-200 rounded-md p-3">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
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
            <div>
                <label class="text-xs text-slate-600 mb-1 block">Nhập lại mật khẩu</label>
                <input type="password" name="password_confirm"
                       class="w-full rounded-md bg-white border border-slate-300 text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#7AE582]">
            </div>
            <button
                class="w-full mt-2 px-4 py-2 rounded-md bg-[#7AE582] text-slate-900 text-sm font-semibold hover:bg-emerald-300 transition">
                Đăng ký
            </button>
        </form>

        <p class="mt-4 text-center text-xs text-slate-500">
            Đã có tài khoản?
            <a href="/auth/login.php" class="text-[#16a34a] hover:underline">Đăng nhập</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
