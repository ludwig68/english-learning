<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();
$hideFooter = true;
require_once __DIR__ . '/../includes/header.php';

$userId   = (int)($_SESSION['user_id'] ?? 0);
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

    // Lấy danh sách level + tiến độ (nếu có) cho user hiện tại
    $stmt = $pdo->prepare("
        SELECT 
            l.*,
            p.answered_questions,
            p.total_questions,
            p.correct_answers
        FROM levels l
        LEFT JOIN user_level_progress p
            ON p.level_id = l.id
           AND p.user_id  = ?
        ORDER BY l.id
    ");
    $stmt->execute([$userId]);
    $levels = $stmt->fetchAll();
    ?>
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-semibold mb-1">Chọn Level để học</h2>
            <p class="text-xs text-slate-500">
                Mỗi level có bộ từ vựng và bài luyện tập riêng.
            </p>
        </div>
        <a href="/user/dashboard.php"
           class="text-xs text-slate-500 hover:text-[#16a34a]">
            <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($levels as $lv): ?>
            <?php
            $answered = (int)($lv['answered_questions'] ?? 0);
            $total    = (int)($lv['total_questions'] ?? 0);
            $correct  = (int)($lv['correct_answers'] ?? 0);

            $statusLabel = '';
            $statusClass = '';

            if ($answered > 0 && $total > 0) {
                if ($answered >= $total) {
                    if ($correct >= $total) {
                        $statusLabel = 'Đúng hết';
                        $statusClass = 'bg-emerald-100 text-emerald-700 border border-emerald-300';
                    } else {
                        $statusLabel = 'Hoàn thành';
                        $statusClass = 'bg-sky-100 text-sky-700 border border-sky-300';
                    }
                } else {
                    $statusLabel = 'Đang làm';
                    $statusClass = 'bg-amber-100 text-amber-700 border border-amber-300';
                }
            }
            ?>
            <a href="?level_id=<?= (int)$lv['id'] ?>"
               class="card-glass p-4 flex flex-col hover:border-[#7AE582] transition cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-base">
                        <?= htmlspecialchars($lv['name']) ?>
                    </h3>

                    <div class="flex flex-col items-end gap-1">
                        <span class="text-[0.7rem] px-2 py-0.5 rounded-full border border-slate-200 text-slate-500">
                            Level #<?= (int)$lv['id'] ?>
                        </span>

                        <?php if ($statusLabel): ?>
                            <span class="text-[0.7rem] px-2 py-0.5 rounded-full <?= $statusClass ?>">
                                <?= htmlspecialchars($statusLabel) ?>
                            </span>
                        <?php endif; ?>
                    </div>
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
    <a href="/user/dashboard.php"
       class="text-xs text-slate-500 hover:text-[#16a34a]">
        <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại
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
        <a href="/user/flashcard.php?level_id=<?= (int)$level_id ?>"
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
        <a href="/user/practice.php?level_id=<?= (int)$level_id ?>"
           class="mt-auto inline-flex items-center gap-2 text-xs px-3 py-2 rounded-md bg-white border border-slate-300 text-slate-800 hover:border-[#7AE582] hover:text-[#16a34a] transition">
            Vào luyện tập
            <i class="fa-solid fa-arrow-right text-[0.7rem]"></i>
        </a>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
