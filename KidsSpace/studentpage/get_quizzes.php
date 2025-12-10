<?php
header('Content-Type: application/json');
include 'db_connect.php';

$result = $conn->query("SELECT id, title, description FROM quizzes ORDER BY created_at DESC");
$quizzes = [];

while($row = $result->fetch_assoc()) {
    $quizzes[] = $row;
}

echo json_encode($quizzes);
?>
