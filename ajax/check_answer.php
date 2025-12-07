<?php
// ajax/check_answer.php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Đảm bảo session có user_id
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($userId <= 0) {
    echo json_encode([
        'correct' => false,
        'message' => 'Bạn cần đăng nhập để làm bài luyện tập.'
    ]);
    exit;
}

$vocab_id = isset($_POST['vocab_id']) ? (int)$_POST['vocab_id'] : 0;
$answer   = trim($_POST['answer'] ?? '');

if ($vocab_id <= 0 || $answer === '') {
    echo json_encode([
        'correct' => false,
        'message' => 'Dữ liệu không hợp lệ.'
    ]);
    exit;
}

// Lấy từ vựng + level_id
$stmt = $pdo->prepare("
    SELECT word, level_id, meaning, image_url, example_sentence
    FROM vocabularies
    WHERE id = ? AND deleted_at IS NULL
");
$stmt->execute([$vocab_id]);
$row = $stmt->fetch();

if (!$row) {
    echo json_encode([
        'correct' => false,
        'message' => 'Không tìm thấy câu hỏi.'
    ]);
    exit;
}

$correctWord = trim($row['word']);
$levelId     = (int)$row['level_id'];
$meaning     = $row['meaning'] ?? '';
$imageUrl    = $row['image_url'] ?? '';
$example     = $row['example_sentence'] ?? '';
$questionMode= isset($_POST['question_mode']) ? (int)$_POST['question_mode'] : 1;

// So sánh không phân biệt hoa thường
$isCorrect = mb_strtolower($answer) === mb_strtolower($correctWord);

// ================== CẬP NHẬT TIẾN ĐỘ user_level_progress ==================
try {
    // Tổng số câu (số từ) trong level này
    $stmtTotal = $pdo->prepare("
        SELECT COUNT(*) 
        FROM vocabularies 
        WHERE level_id = ? AND deleted_at IS NULL
    ");
    $stmtTotal->execute([$levelId]);
    $totalQuestions = (int)$stmtTotal->fetchColumn();

    // Lấy tiến độ hiện tại (nếu có)
    $stmtProg = $pdo->prepare("
        SELECT id, answered_questions, correct_answers
        FROM user_level_progress
        WHERE user_id = ? AND level_id = ?
        LIMIT 1
    ");
    $stmtProg->execute([$userId, $levelId]);
    $prog = $stmtProg->fetch();

    if ($prog) {
        // Đã có record -> update
        $answeredNew = (int)$prog['answered_questions'] + 1;
        $correctNew  = (int)$prog['correct_answers'] + ($isCorrect ? 1 : 0);

        $stmtUpdate = $pdo->prepare("
            UPDATE user_level_progress
            SET total_questions     = :total,
                answered_questions  = :answered,
                correct_answers     = :correct
            WHERE id = :id
        ");
        $stmtUpdate->execute([
            ':total'    => $totalQuestions,
            ':answered' => $answeredNew,
            ':correct'  => $correctNew,
            ':id'       => $prog['id']
        ]);
    } else {
        // Chưa có record -> insert mới
        $answeredNew = 1;
        $correctNew  = $isCorrect ? 1 : 0;

        $stmtInsert = $pdo->prepare("
            INSERT INTO user_level_progress
                (user_id, level_id, total_questions, answered_questions, correct_answers)
            VALUES
                (:user_id, :level_id, :total, :answered, :correct)
        ");
        $stmtInsert->execute([
            ':user_id'  => $userId,
            ':level_id' => $levelId,
            ':total'    => $totalQuestions,
            ':answered' => $answeredNew,
            ':correct'  => $correctNew
        ]);
    }
} catch (Exception $e) {
    // Nếu có lỗi DB thì vẫn trả lời bình thường, chỉ không cập nhật tiến độ
    // (Tránh làm JS phía client bị crash)
}

// ================== Lưu lịch sử làm bài (learning_history + items) ==================
try {
    $pdo->beginTransaction();

    $stmtHistory = $pdo->prepare("
        INSERT INTO learning_history
            (user_id, level_id, mode, vocab_count, correct_count, total_questions, note, created_at)
        VALUES (?, ?, 'practice', ?, ?, ?, NULL, NOW())
    ");
    $vocabCount    = 1;
    $correctCount  = $isCorrect ? 1 : 0;
    $totalQThisRun = 1;
    $stmtHistory->execute([
        $userId,
        $levelId,
        $vocabCount,
        $correctCount,
        $totalQThisRun
    ]);

    $historyId = (int)$pdo->lastInsertId();

    // Chuẩn bị text câu hỏi để hiển thị trong lịch sử
    if ($questionMode === 1) {
        $questionText = 'Nghĩa: ' . $meaning;
    } elseif ($questionMode === 2 && !empty($imageUrl)) {
        $questionText = 'Hình ảnh + nghĩa: ' . $meaning;
    } elseif ($questionMode === 3 && !empty($example)) {
        $questionText = 'Điền từ vào câu: ' . $example;
    } else {
        $questionText = 'Nghĩa: ' . $meaning;
    }

    $stmtItem = $pdo->prepare("
        INSERT INTO learning_history_items
            (history_id, question_text, user_answer, correct_answer, is_correct, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmtItem->execute([
        $historyId,
        $questionText,
        $answer,
        $correctWord,
        $isCorrect ? 1 : 0
    ]);

    $pdo->commit();
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Không chặn phản hồi tới client nếu chỉ lỗi lưu lịch sử
}

// ================== TRẢ KẾT QUẢ VỀ CHO JS ==================
echo json_encode([
    'correct' => $isCorrect,
    'message' => $isCorrect
        ? "Chính xác! Đáp án là <b>{$correctWord}</b>."
        : "Chưa đúng. Đáp án đúng là <b>{$correctWord}</b>."
]);
