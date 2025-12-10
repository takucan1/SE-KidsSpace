<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lms_quiz_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    // Return JSON error, not HTML
    header('Content-Type: application/json');
    echo json_encode(['status'=>'error','message'=>'DB connection failed: '.$conn->connect_error]);
    exit();
}
?>
