<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/header.php';

$level_id = isset($_GET['level_id']) ? (int)$_GET['level_id'] : 0;
if ($level_id <= 0) {
    echo '<p class="text-sm text-red-600">Level không hợp lệ.</p>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Lấy random 1 từ
$stmt = $pdo->prepare("
    SELECT * FROM vocabularies
    WHERE level_id = ? AND deleted_at IS NULL
    ORDER BY RAND() LIMIT 1
");
$stmt->execute([$level_id]);
$question = $stmt->fetch();

if (!$question) {
    echo '<p class="text-sm text-slate-500">Chưa có từ vựng cho Level này.</p>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Distractors
$stmt = $pdo->prepare("
    SELECT word FROM vocabularies
    WHERE level_id = ? AND deleted_at IS NULL AND id <> ?
    ORDER BY RAND() LIMIT 3
");
$stmt->execute([$level_id, $question['id']]);
$distractors = $stmt->fetchAll(PDO::FETCH_COLUMN);

$options = $distractors;
$options[] = $question['word'];
shuffle($options);

// random mode
$mode = rand(1, 3);
?>

<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-xl font-semibold">Practice – Level <?= $level_id ?></h2>
        <p class="text-xs text-slate-500">
            Trả lời câu hỏi, hệ thống sẽ chấm bằng SweetAlert2.
        </p>
    </div>
    <a href="/user/learn.php?level_id=<?= $level_id ?>"
       class="text-xs text-slate-500 hover:text-[#16a34a]">
        <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại
    </a>
</div>

<div class="card-glass p-5 animate__animated animate__fadeInUp">
    <form id="practice-form" class="space-y-4">
        <input type="hidden" name="vocab_id" value="<?= $question['id'] ?>">
        <input type="hidden" name="mode" value="<?= $mode ?>">

        <?php if ($mode === 1): ?>
            <p class="text-sm">
                <span class="text-[#16a34a] font-semibold">Nghĩa:</span>
                <?= htmlspecialchars($question['meaning']) ?>
            </p>
            <div class="space-y-2 mt-2">
                <?php foreach ($options as $idx => $opt): ?>
                    <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                        <input type="radio" name="answer" value="<?= htmlspecialchars($opt) ?>"
                               class="w-3 h-3 text-[#7AE582] border-slate-300">
                        <span><?= chr(65 + $idx) ?>. <?= htmlspecialchars($opt) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

        <?php elseif ($mode === 2 && $question['image_path']): ?>
            <p class="text-sm mb-2">
                Nhìn hình dưới đây và nhập từ tiếng Anh tương ứng:
            </p>
            <img src="/<?= htmlspecialchars($question['image_path']) ?>"
                 alt="" class="max-h-40 rounded-md mb-3 object-contain">
            <input type="text" name="answer"
                   class="w-full rounded-md bg-white border border-slate-300 text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#7AE582]"
                   placeholder="Nhập từ tiếng Anh..." required>

        <?php elseif ($mode === 3 && $question['audio_path'] && $question['example_sentence']): ?>
            <p class="text-sm mb-2">
                Nghe audio và điền từ còn thiếu vào câu:
            </p>
            <audio controls class="mb-3 w-full">
                <source src="/<?= htmlspecialchars($question['audio_path']) ?>" type="audio/mpeg">
            </audio>
            <p class="text-xs text-slate-600 mb-2">
                <?php
                $sentence = str_ireplace($question['word'], '____', $question['example_sentence']);
                echo htmlspecialchars($sentence);
                ?>
            </p>
            <input type="text" name="answer"
                   class="w-full rounded-md bg-white border border-slate-300 text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#7AE582]"
                   placeholder="Nhập từ còn thiếu..." required>

        <?php else: ?>
            <p class="text-sm mb-2">
                <span class="text-[#16a34a] font-semibold">Nghĩa:</span>
                <?= htmlspecialchars($question['meaning']) ?>
            </p>
            <input type="text" name="answer"
                   class="w-full rounded-md bg-white border border-slate-300 text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#7AE582]"
                   placeholder="Nhập từ tiếng Anh..." required>
        <?php endif; ?>

        <button
            class="mt-3 inline-flex items-center gap-2 text-xs px-4 py-2 rounded-md bg-[#7AE582] text-slate-900 font-semibold hover:bg-emerald-300 transition">
            <i class="fa-solid fa-check"></i> Check
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
