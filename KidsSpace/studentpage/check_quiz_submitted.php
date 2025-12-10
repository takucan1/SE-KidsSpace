<?php
header('Content-Type: application/json');
session_start();
include 'db_connect.php';

if(!isset($_SESSION['email'])){
    echo json_encode(['status'=>'error','message'=>'Not logged in']);
    exit;
}

$quiz_id = intval($_GET['quiz_id'] ?? 0);
if($quiz_id <= 0){
    echo json_encode(['status'=>'error','message'=>'Invalid quiz_id']);
    exit;
}

try {
    $student_email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT id FROM students WHERE email=?");
    $stmt->bind_param("s", $student_email);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows === 0) throw new Exception("Student not found.");
    $student_id = $res->fetch_assoc()['id'];
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM quiz_answers WHERE student_id=? AND quiz_id=?");
    $stmt->bind_param("ii", $student_id, $quiz_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $count = $res->fetch_assoc()['count'];
    $stmt->close();

    echo json_encode(['status'=>'success','submitted'=>$count > 0]);
} catch(Exception $e){
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
?>
