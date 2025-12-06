<?php
// ajax/check_answer.php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

$vocab_id = isset($_POST['vocab_id']) ? (int)$_POST['vocab_id'] : 0;
$answer   = trim($_POST['answer'] ?? '');

if ($vocab_id <= 0 || $answer === '') {
    echo json_encode([
        'correct' => false,
        'message' => 'Dữ liệu không hợp lệ.'
    ]);
    exit;
}

$stmt = $pdo->prepare("SELECT word FROM vocabularies WHERE id = ?");
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
$isCorrect = mb_strtolower($answer) === mb_strtolower($correctWord);

echo json_encode([
    'correct' => $isCorrect,
    'message' => $isCorrect
        ? "Chính xác! Đáp án là <b>{$correctWord}</b>."
        : "Chưa đúng. Đáp án đúng là <b>{$correctWord}</b>."
]);
