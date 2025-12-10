<?php
header('Content-Type: application/json');
include 'db_connect.php';

// Read JSON input from AJAX
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
    exit;
}

// Validate input
$title = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$questions = $data['questions'] ?? [];

if ($title === '' || empty($questions)) {
    echo json_encode(['status' => 'error', 'message' => 'Title or questions missing']);
    exit;
}

// Insert quiz
$stmt = $conn->prepare("INSERT INTO quizzes (title, description) VALUES (?, ?)");
$stmt->bind_param("ss", $title, $description);
if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to insert quiz']);
    exit;
}
$quiz_id = $stmt->insert_id;
$stmt->close();

// Prepare statement to insert questions
$stmt_q = $conn->prepare("INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");

foreach ($questions as $q) {
    // Sanitize input
    $question_text = trim($q['question'] ?? '');
    $option_a = trim($q['option_a'] ?? '');
    $option_b = trim($q['option_b'] ?? '');
    $option_c = trim($q['option_c'] ?? '');
    $option_d = trim($q['option_d'] ?? '');
    $correct_option = strtoupper(trim($q['correct_option'] ?? ''));

    if ($question_text === '' || $option_a === '' || $option_b === '' || $option_c === '' || $option_d === '' || !in_array($correct_option, ['A','B','C','D'])) {
        continue; // skip invalid question
    }

    $stmt_q->bind_param("issssss", $quiz_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option);
    $stmt_q->execute();
}

$stmt_q->close();

echo json_encode(['status' => 'success', 'quiz_id' => $quiz_id]);
?>
