<?php 
    session_start();
    if(!isset($_SESSION['email'])) {
        header("Location: index.html");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <title>KidsSpace</title>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
    
    <!-- TOP NAVBAR -->
    <header class="top-nav">

        <div class="nav-left">
            <button class="icon-btn">☰</button>
            <div class="logo">KidsSpace LMS</div>
        </div>

        <div class="nav-right">
            <div class="hi">Hi, Professor!</div>
            <div class="profile">
                <img src="teacherlogo.png" alt="Profile">
            </div>
        </div>  
    </header>

    <!-- MAIN CONTENT -->
    <main class="dashboard">
        <div class="dashboard-grid">
            <!-- Courses Card -->
           
                <div class="dashboard-card courses-card"> 
                     <a href="Courses.html">              
                    <div class="card-icon">📚</div>
                    <h2>Courses</h2>
                     <p>Manage and view all your courses</p> 
                     </a>                   
                </div>
            

            <!-- Quizzes & Activities Card -->

                <div class="dashboard-card quizzes-card">
                    <a href="Quizzes_Activities.html">
                    <div class="card-icon">✏️</div>
                    <h2>Quizzes & Activities</h2>
                      <p>Create and manage assessments</p>
                    </a>
                </div>
         
  

            <!-- Learning Materials & Modules Card -->
            <div class="dashboard-card materials-card">
                <div class="card-icon">📄</div>
                <h2>Learning Materials & Modules</h2>
                    <p>Upload and organize course materials</p>
            </div>

            <!-- Student Performance Tracking Card -->
            <div class="dashboard-card performance-card">
                <div class="card-icon">📊</div>
                <h2>Student Performance Tracking</h2>
                <p>Monitor student progress and grades</p>
            </div>
        </div>
    </main>

    <footer class="bottom-nav">
        Version 1.3.1
    </footer>
 </body>   