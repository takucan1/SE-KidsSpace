<?php
// TEMP: Enable errors to help debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');
require 'db_connect.php';

// Check login session
if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

// Get student info from DB using email in session
$student_email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id FROM students WHERE email=?");
$stmt->bind_param("s", $student_email);
$stmt->execute();
$res = $stmt->get_result();

if (!$row = $res->fetch_assoc()) {
    echo json_encode(['status' => 'error', 'message' => 'Student not found']);
    exit();
}
$student_id = $row['id'];
$stmt->close();

// Read JSON body
$json = file_get_contents("php://input");
if (!$json) {
    echo json_encode(['status'=>'error','message'=>'No JSON submitted']);
    exit();
}

$data = json_decode($json, true);
if (!$data) {
    echo json_encode(['status'=>'error','message'=>'Invalid JSON format']);
    exit();
}

$quiz_id = $data['quiz_id'] ?? null;
$answers = $data['answers'] ?? null;

if (!$quiz_id || !is_array($answers)) {
    echo json_encode(['status'=>'error','message'=>'Missing quiz_id or answers']);
    exit();
}

$total_score = 0;

// Loop answers
foreach ($answers as $question_id => $selected_option) {

    // Validate option
    if (!in_array($selected_option, ['A','B','C','D'])) {
        echo json_encode(['status'=>'error','message'=>'Invalid option value']);
        exit();
    }

    // Get correct option
    $q = $conn->prepare("SELECT correct_option FROM quiz_questions WHERE id=? AND quiz_id=?");
    $q->bind_param("ii", $question_id, $quiz_id);
    $q->execute();
    $correct_res = $q->get_result();

    if (!$qRow = $correct_res->fetch_assoc()) {
        echo json_encode([
            'status'=>'error',
            'message'=>"Question ID $question_id not found in quiz"
        ]);
        exit();
    }
    $q->close();

    $score = ($selected_option === $qRow['correct_option']) ? 1 : 0;
    $total_score += $score;

    // Insert student answer
    $insert = $conn->prepare("
        INSERT INTO quiz_answers (student_id, quiz_id, question_id, selected_option, score)
        VALUES (?, ?, ?, ?, ?)
    ");

    if (!$insert) {
        echo json_encode(['status'=>'error','message'=>'Prep failed: '.$conn->error]);
        exit();
    }

    $insert->bind_param("iiisi", $student_id, $quiz_id, $question_id, $selected_option, $score);

    if (!$insert->execute()) {
        echo json_encode(['status'=>'error','message'=>'SQL Error: '.$insert->error]);
        exit();
    }

    $insert->close();
}

// Return result
echo json_encode([
    'status' => 'success',
    'message' => 'Quiz submitted successfully',
    'total_score' => $total_score
]);

$conn->close();
