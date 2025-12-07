<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/header.php';

$level_id = isset($_GET['level_id']) ? (int)$_GET['level_id'] : 0;

if ($level_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM levels WHERE id = ?");
    $stmt->execute([$level_id]);
    $level = $stmt->fetch();
    if (!$level) {
        $level_id = 0;
    }
}

if ($level_id === 0) {
    $levels = $pdo->query("SELECT * FROM levels ORDER BY id")->fetchAll();
    ?>
    <h2 class="text-xl font-semibold mb-3">Chọn Level để học</h2>
    <p class="text-xs text-slate-500 mb-5">
        Mỗi level có bộ từ vựng và bài luyện tập riêng.
    </p>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($levels as $lv): ?>
            <a href="?level_id=<?= $lv['id'] ?>"
               class="card-glass p-4 flex flex-col hover:border-[#7AE582] transition cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-base">
                        <?= htmlspecialchars($lv['name']) ?>
                    </h3>
                    <span class="text-[0.7rem] px-2 py-0.5 rounded-full border border-slate-200 text-slate-500">
                        Level #<?= (int)$lv['id'] ?>
                    </span>
                </div>
                <p class="text-xs text-slate-500">
                    <?= htmlspecialchars(mb_strimwidth($lv['description'], 0, 110, '...')) ?>
                </p>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}
?>
<?php require_once __DIR__ . '/../includes/user_navbar.php'; ?>

<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-xl font-semibold">Level: <?= htmlspecialchars($level['name']) ?></h2>
        <p class="text-xs text-slate-500">
            <?= htmlspecialchars($level['description']) ?>
        </p>
    </div>
    <a href="/index.php" class="text-xs text-slate-500 hover:text-[#16a34a]">
        <i class="fa-solid fa-arrow-left mr-1"></i> Trang chủ
    </a>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div class="card-glass p-5 flex flex-col animate__animated animate__fadeInLeft">
        <h3 class="font-semibold mb-2 flex items-center gap-2">
            <span class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center text-[#16a34a]">
                <i class="fa-regular fa-clone"></i>
            </span>
            Mode 1 – Flashcard
        </h3>
        <p class="text-xs text-slate-500 mb-4">
            Lật thẻ để ghi nhớ từ vựng, hình ảnh minh hoạ và câu ví dụ.
        </p>
        <a href="/user/flashcard.php?level_id=<?= $level_id ?>"
           class="mt-auto inline-flex items-center gap-2 text-xs px-3 py-2 rounded-md bg-[#7AE582] text-slate-900 font-semibold hover:bg-emerald-300 transition">
            Học bằng Flashcard
            <i class="fa-solid fa-arrow-right text-[0.7rem]"></i>
        </a>
    </div>

    <div class="card-glass p-5 flex flex-col animate__animated animate__fadeInRight">
        <h3 class="font-semibold mb-2 flex items-center gap-2">
            <span class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center text-[#16a34a]">
                <i class="fa-solid fa-clipboard-question"></i>
            </span>
            Mode 2 – Practice
        </h3>
        <p class="text-xs text-slate-500 mb-4">
            Trắc nghiệm A/B/C/D, nhìn hình nhập từ, nghe audio điền từ vào chỗ trống.
        </p>
        <a href="/user/practice.php?level_id=<?= $level_id ?>"
           class="mt-auto inline-flex items-center gap-2 text-xs px-3 py-2 rounded-md bg-white border border-slate-300 text-slate-800 hover:border-[#7AE582] hover:text-[#16a34a] transition">
            Vào luyện tập
            <i class="fa-solid fa-arrow-right text-[0.7rem]"></i>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
