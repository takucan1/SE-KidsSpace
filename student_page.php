<?php
    session_start();
    if(!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #ffff;">
    <div class="box">
        <h1>Student's Page</h1>
        <button onclick="window.location.href='logout.php'">Logout</button>
    </div>
</body>
</html>