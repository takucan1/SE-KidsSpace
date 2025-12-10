<?php
session_start();
if(!isset($_SESSION['email'])){
    echo json_encode([]);
    exit();
}

include 'db_connect.php';

// Get all quizzes
$quizRes = $conn->query("SELECT id, title FROM quizzes ORDER BY created_at DESC");
$quizzes = [];

while($quiz = $quizRes->fetch_assoc()){
    $quiz_id = $quiz['id'];

    // Get students who submitted this quiz
    $stmt = $conn->prepare("
        SELECT s.id AS student_id, s.name AS student_name, SUM(a.score) AS total_score
        FROM quiz_answers a
        JOIN students s ON a.student_id = s.id
        WHERE a.quiz_id = ?
        GROUP BY a.student_id
        ORDER BY total_score DESC
    ");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $res = $stmt->get_result();

    $students = [];
    while($row = $res->fetch_assoc()){
        $students[] = [
            'student_id' => $row['student_id'],
            'student_name' => $row['student_name'],
            'total_score' => $row['total_score']
        ];
    }

    $quizzes[] = [
        'quiz_id' => $quiz_id,
        'title' => $quiz['title'],
        'students' => $students
    ];

    $stmt->close();
}

echo json_encode($quizzes);
$conn->close();
