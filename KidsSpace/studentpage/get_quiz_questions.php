<?php
header('Content-Type: application/json');
include 'db_connect.php';

$quiz_id = intval($_GET['quiz_id'] ?? 0);

if($quiz_id === 0) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT id, question, option_a, option_b, option_c, option_d FROM quiz_questions WHERE quiz_id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

$stmt->close();
echo json_encode($questions);
?>
