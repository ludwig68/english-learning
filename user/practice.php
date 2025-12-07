<?php
// user/practice.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$hideFooter = true;

// Lấy level_id từ POST (khi submit) hoặc GET (lần vào đầu)
$level_id = isset($_POST['level_id'])
    ? (int)$_POST['level_id']
    : (int)($_GET['level_id'] ?? 0);

$userId = (int)($_SESSION['user_id'] ?? 0);

if ($level_id <= 0) {
    $pageTitle = 'Practice';
    require_once __DIR__ . '/../includes/header.php';
    require_once __DIR__ . '/../includes/user_navbar.php';
    echo '<div class="mt-4 text-sm text-red-600">Level không hợp lệ.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

/* ================== TIẾN ĐỘ HIỆN TẠI ================== */

// Tổng số câu (số từ vựng trong level)
$stmtTotal = $pdo->prepare("
    SELECT COUNT(*) 
    FROM vocabularies 
    WHERE level_id = ? AND deleted_at IS NULL
");
$stmtTotal->execute([$level_id]);
$totalQuestions = (int)$stmtTotal->fetchColumn();

// Tiến độ user
$stmtProg = $pdo->prepare("
    SELECT answered_questions, total_questions
    FROM user_level_progress
    WHERE user_id = ? AND level_id = ?
    LIMIT 1
");
$stmtProg->execute([$userId, $level_id]);
$prog = $stmtProg->fetch(PDO::FETCH_ASSOC);

$answeredQuestions = 0;
if ($prog) {
    $answeredQuestions = (int)$prog['answered_questions'];
    $totalQuestions    = max($totalQuestions, (int)$prog['total_questions']);
}

/* ================== XỬ LÝ SUBMIT CÂU TRẢ LỜI ================== */

$feedbackMsg  = '';
$feedbackType = ''; // success | error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vocabId       = (int)($_POST['vocab_id'] ?? 0);
    $questionMode  = (int)($_POST['question_mode'] ?? 1);
    $userAnswerRaw = trim($_POST['answer'] ?? '');

    if ($vocabId > 0 && $userAnswerRaw !== '') {
        // Lấy vocab tương ứng
        $stmt = $pdo->prepare("
            SELECT *
            FROM vocabularies
            WHERE id = ? AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([$vocabId]);
        $vocab = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($vocab) {
            $correctWord = $vocab['word'];
            $userAnswer  = $userAnswerRaw;

            // So sánh không phân biệt hoa/thường, trim khoảng trắng
            $isCorrect = (strcasecmp(trim($userAnswer), trim($correctWord)) === 0) ? 1 : 0;

            // ---- Lưu bản ghi tổng vào learning_history ----
            $stmt = $pdo->prepare("
                INSERT INTO learning_history
                    (user_id, level_id, mode, vocab_count, correct_count, total_questions, note, created_at)
                VALUES
                    (?, ?, 'practice', ?, ?, ?, NULL, NOW())
            ");
            $vocabCount    = 1;
            $correctCount  = $isCorrect ? 1 : 0;
            $totalQThisRun = 1;

            $stmt->execute([
                $userId,
                $level_id,
                $vocabCount,
                $correctCount,
                $totalQThisRun
            ]);

            $historyId = (int)$pdo->lastInsertId();

            // ---- Lưu chi tiết vào learning_history_items ----
            if ($questionMode === 1) {
                $questionText = 'Nghĩa: ' . ($vocab['meaning'] ?? '');
            } elseif ($questionMode === 2 && !empty($vocab['image_url'])) {
                $questionText = 'Hình ảnh + nghĩa: ' . ($vocab['meaning'] ?? '');
            } elseif ($questionMode === 3 && !empty($vocab['example_sentence'])) {
                $questionText = 'Điền từ vào câu: ' . ($vocab['example_sentence'] ?? '');
            } else {
                $questionText = 'Nghĩa: ' . ($vocab['meaning'] ?? '');
            }

            $stmt = $pdo->prepare("
                INSERT INTO learning_history_items
                    (history_id, question_text, user_answer, correct_answer, is_correct, created_at)
                VALUES
                    (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $historyId,
                $questionText,
                $userAnswer,
                $correctWord,
                $isCorrect
            ]);

            // ---- Cập nhật tiến độ user_level_progress ----
            if ($prog) {
                $answeredQuestions = min($totalQuestions, $answeredQuestions + 1);
                $stmt = $pdo->prepare("
                    UPDATE user_level_progress
                    SET answered_questions = ?, total_questions = ?
                    WHERE user_id = ? AND level_id = ?
                ");
                $stmt->execute([$answeredQuestions, $totalQuestions, $userId, $level_id]);
            } else {
                $answeredQuestions = 1;
                $stmt = $pdo->prepare("
                    INSERT INTO user_level_progress
                        (user_id, level_id, answered_questions, total_questions)
                    VALUES (?,?,?,?)
                ");
                $stmt->execute([$userId, $level_id, $answeredQuestions, $totalQuestions]);
            }

            // ---- Thông báo feedback ----
            if ($isCorrect) {
                $feedbackType = 'success';
                $feedbackMsg  = 'Chính xác! Bạn đã trả lời đúng từ "' . htmlspecialchars($correctWord) . '".';
            } else {
                $feedbackType = 'error';
                $feedbackMsg  = 'Sai rồi. Đáp án đúng là "' . htmlspecialchars($correctWord) . '".';
            }
        }
    }
}

/* ================== LẤY CÂU HỎI MỚI ================== */

$stmt = $pdo->prepare("
    SELECT *
    FROM vocabularies
    WHERE level_id = ? AND deleted_at IS NULL
    ORDER BY RAND()
    LIMIT 1
");
$stmt->execute([$level_id]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$question) {
    $pageTitle = 'Practice';
    require_once __DIR__ . '/../includes/header.php';
    require_once __DIR__ . '/../includes/user_navbar.php';
    echo '<p class="mt-4 text-sm text-slate-500">Chưa có từ vựng cho Level này.</p>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Lấy 3 đáp án nhiễu
$stmt = $pdo->prepare("
    SELECT word 
    FROM vocabularies
    WHERE level_id = ? AND deleted_at IS NULL AND id <> ?
    ORDER BY RAND()
    LIMIT 3
");
$stmt->execute([$level_id, $question['id']]);
$distractors = $stmt->fetchAll(PDO::FETCH_COLUMN);

$options = $distractors;
$options[] = $question['word'];
shuffle($options);

// Random kiểu câu hỏi
$questionMode = rand(1, 3);

$pageTitle = 'Practice - Level ' . (int)$level_id;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/user_navbar.php';
?>

<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-xl font-semibold">Practice – Level <?= (int)$level_id ?></h2>
        <p class="text-xs text-slate-500">
            Trả lời từng câu hỏi, hệ thống sẽ chấm điểm và lưu lịch sử học tập.
        </p>
    </div>
    <a href="/user/learn.php?level_id=<?= (int)$level_id ?>"
       class="text-xs text-slate-500 hover:text-[#16a34a]">
        <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại
    </a>
</div>

<?php if ($totalQuestions > 0): ?>
    <div class="mb-3">
        <p class="text-[0.7rem] text-slate-500 mb-1">
            Tiến độ câu hỏi (<?= $answeredQuestions ?>/<?= $totalQuestions ?>):
        </p>
        <div class="flex flex-wrap gap-1">
            <?php for ($i = 1; $i <= $totalQuestions; $i++): ?>
                <?php
                $done = $i <= $answeredQuestions;
                $boxClass = $done
                    ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
                    : 'bg-rose-50 text-rose-400 border-rose-200';
                ?>
                <span class="w-5 h-5 rounded-md border text-[0.65rem] flex items-center justify-center <?= $boxClass ?>">
                    <?= $i ?>
                </span>
            <?php endfor; ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($feedbackMsg): ?>
    <div class="mb-3 rounded-2xl px-4 py-3 text-[0.8rem]
        <?= $feedbackType === 'success'
            ? 'bg-emerald-50 border border-emerald-200 text-emerald-800'
            : 'bg-red-50 border border-red-200 text-red-700' ?>">
        <i class="fa-solid <?= $feedbackType === 'success' ? 'fa-circle-check' : 'fa-circle-xmark' ?> mr-1"></i>
        <?= $feedbackMsg ?>
    </div>
<?php endif; ?>

<div class="card-glass p-5 animate__animated animate__fadeInUp">
    <form id="practice-form" method="post" class="space-y-4">
        <!-- Giữ level_id khi submit -->
        <input type="hidden" name="level_id" value="<?= (int)$level_id ?>">
        <input type="hidden" name="vocab_id" value="<?= (int)$question['id'] ?>">
        <input type="hidden" name="question_mode" value="<?= (int)$questionMode ?>">

        <?php if ($questionMode === 1): ?>
            <!-- Nghĩa -> chọn từ -->
            <p class="text-sm">
                <span class="text-[#16a34a] font-semibold">Nghĩa:</span>
                <?= htmlspecialchars($question['meaning']) ?>
            </p>
            <div class="space-y-2 mt-2">
                <?php foreach ($options as $idx => $opt): ?>
                    <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                        <input type="radio" name="answer" value="<?= htmlspecialchars($opt) ?>"
                               class="w-3 h-3 text-[#7AE582] border-slate-300" required>
                        <span><?= chr(65 + $idx) ?>. <?= htmlspecialchars($opt) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

        <?php elseif ($questionMode === 2 && !empty($question['image_url'])): ?>
            <!-- Nhìn hình -> nhập từ -->
            <p class="text-sm mb-2">
                Nhìn hình dưới đây và nhập từ tiếng Anh tương ứng:
            </p>
            <img src="<?= htmlspecialchars($question['image_url']) ?>"
                 alt=""
                 class="max-h-40 rounded-md mb-3 object-contain mx-auto">
            <input type="text" name="answer"
                   class="w-full rounded-md bg-white border border-slate-300 text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#7AE582]"
                   placeholder="Nhập từ tiếng Anh..." required>

        <?php elseif (
            $questionMode === 3 &&
            !empty($question['audio_url']) &&
            !empty($question['example_sentence'])
        ): ?>
            <!-- Nghe audio -> điền từ -->
            <p class="text-sm mb-2">
                Nghe audio và điền từ còn thiếu vào câu:
            </p>
            <audio controls class="mb-3 w-full">
                <source src="<?= htmlspecialchars($question['audio_url']) ?>" type="audio/mpeg">
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
            <!-- Fallback: Nghĩa -> nhập từ -->
            <p class="text-sm mb-2">
                <span class="text-[#16a34a] font-semibold">Nghĩa:</span>
                <?= htmlspecialchars($question['meaning']) ?>
            </p>
            <input type="text" name="answer"
                   class="w-full rounded-md bg-white border border-slate-300 text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#7AE582]"
                   placeholder="Nhập từ tiếng Anh..." required>
        <?php endif; ?>

        <div class="mt-3 flex items-center justify-between gap-3">
            <button
                class="inline-flex items-center gap-2 text-xs px-4 py-2 rounded-md bg-[#7AE582] text-slate-900 font-semibold hover:bg-emerald-300 transition">
                <i class="fa-solid fa-check"></i> Kiểm tra đáp án
            </button>

            <a href="/user/practice.php?level_id=<?= (int)$level_id ?>"
               class="text-[0.7rem] text-slate-500 hover:text-[#16a34a]">
                Bỏ qua câu này
            </a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
