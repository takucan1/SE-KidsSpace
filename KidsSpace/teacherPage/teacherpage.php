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
                <a href="Courses.html" class="dashboard-card courses-card">
                    <div class="card-icon">📚</div>
                    <h2>Courses</h2>
                    <p>Manage and view all your courses</p>
                </a>
            

            <!-- Quizzes & Activities Card -->

                <div class="dashboard-card quizzes-card">
                    <a href="Quizzes_Activities.html">
                    <div class="card-icon">✏️</div>
                    <h2>Quizzes & Activities</h2>
                      <p>Create and manage assessm  ents</p>
                    </a>
                </div>
         
            <!-- Student Progress Card -->
             <div class="dashboard-card quizzes-card">
                    <a href="view_quiz_submissions.php">
                    <div class="glass-icon">✏️</div>
                    <h2>View Submissions</h2>
                      <p>View student submissions</p>
                    </a>
                </div>

           
        </div>
    </main>

    <footer class="bottom-nav">
        Version 1.3.1
    </footer>
 </body>
 </html>   