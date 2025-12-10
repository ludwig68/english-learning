<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/header.php';

$level_id = isset($_GET['level_id']) ? (int)$_GET['level_id'] : 0;
if ($level_id <= 0) {
    echo '<div class="flex items-center justify-between mb-4">';
    echo '    <p class="text-sm text-red-600">Level không hợp lệ.</p>';
    echo '    <a href="/user/dashboard.php" class="text-xs text-slate-500 hover:text-[#16a34a]">';
    echo '        <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại ';
    echo '    </a>';
    echo '</div>';

    $hideFooter = true;
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}


$stmt = $pdo->prepare("
    SELECT v.*, l.name AS level_name
    FROM vocabularies v
    JOIN levels l ON v.level_id = l.id
    WHERE v.level_id = ? AND v.deleted_at IS NULL
    ORDER BY v.id
");
$stmt->execute([$level_id]);
$vocabList = $stmt->fetchAll();

if (!$vocabList) {
    echo '<p class="text-sm text-slate-500">Chưa có từ vựng cho Level này.</p>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$level_name = $vocabList[0]['level_name'];
?>

<?php require_once __DIR__ . '/../includes/user_navbar.php'; ?>

<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-xl font-semibold">Flashcard – Level <?= htmlspecialchars($level_name) ?></h2>
        <p class="text-xs text-slate-500">
            Click vào thẻ để lật. Dùng icon <i class="fa-solid fa-volume-high"></i> để nghe phát âm.
        </p>
    </div>
    <a href="/user/learn.php?level_id=<?= $level_id ?>"
        class="text-xs text-slate-500 hover:text-[#16a34a]">
        <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại
    </a>
</div>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($vocabList as $v): ?>
        <div class="flashcard card-glass p-0 animate__animated animate__fadeInUp">
            <div class="flashcard-inner">
                <!-- FRONT -->
                <div class="flashcard-face flashcard-front">
                    <div class="flex flex-col items-center gap-3">
                        <div class="text-lg font-semibold">
                            <?= htmlspecialchars($v['word']) ?>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-slate-500">
                            <?php if (!empty($v['audio_url'])): ?>
                                <button
                                    class="px-3 py-1 rounded-full bg-white border border-slate-300 text-[0.7rem] flex items-center gap-2 hover:border-[#7AE582]"
                                    onclick="event.stopPropagation(); new Audio('<?= htmlspecialchars($v['audio_url']) ?>').play();">
                                    <i class="fa-solid fa-volume-high"></i> Nghe
                                </button>
                            <?php endif; ?>
                            <span class="px-2 py-0.5 rounded-full border border-slate-200">
                                <?= htmlspecialchars($v['type']) ?>
                            </span>
                        </div>
                    </div>
                </div>


                <!-- BACK -->
                <div class="flashcard-face flashcard-back">
                    <div class="text-sm font-semibold mb-1">
                        <?= htmlspecialchars($v['meaning']) ?>
                    </div>

                    <?php if (!empty($v['image_url'])): ?>
                        <img src="<?= htmlspecialchars($v['image_url']) ?>"
                            alt="" class="mt-2 max-h-24 rounded-md object-contain">
                    <?php endif; ?>

                    <?php if (!empty($v['example_sentence'])): ?>
                        <p class="mt-3 text-xs text-emerald-900">
                            <?= htmlspecialchars($v['example_sentence']) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php
$hideFooter = true;
require_once __DIR__ . '/../includes/footer.php';
?>