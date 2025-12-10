<?php
session_start();
if(!isset($_SESSION['email'])){
    header("Location: index.html");
    exit();
}

include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Progress - KidsSpace LMS</title>
<link rel="stylesheet" href="style.css">
<style>
body { font-family: Arial, sans-serif; background-color: #f0f2f5; margin: 0; }
.top-nav { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.top-nav .logo { font-weight: bold; font-size: 20px; }
.dashboard { padding: 20px; }
.dashboard h1 { margin-bottom: 10px; color: #333; display: inline-block; }
.back-btn { margin-left: 20px; padding: 8px 15px; background-color: #007BFF; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
.back-btn:hover { background-color: #0056b3; }
.dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(300px,1fr)); gap: 20px; margin-top: 20px; }
.quiz-card { background-color: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s; }
.quiz-card:hover { transform: translateY(-5px); }
.quiz-card h3 { margin-top: 0; cursor: pointer; color: #007BFF; }
.student-list { list-style: none; padding-left: 0; margin-top: 10px; }
.student-list li { padding: 8px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; }
.student-list li:last-child { border-bottom: none; }
.empty-msg { font-style: italic; color: #777; }
footer { text-align: center; padding: 10px; background-color: #fff; box-shadow: 0 -2px 5px rgba(0,0,0,0.1); margin-top: 30px; }
</style>
</head>
<body>

<!-- TOP NAVBAR -->
<header class="top-nav">
    <div class="logo">KidsSpace LMS</div>
    <div class="hi">Hi, Professor!</div>
</header>

<main class="dashboard">
    <h1>Student Quiz Submissions</h1>
    <button class="back-btn" onclick="window.location.href='teacherpage.php'">Back to Dashboard</button>

    <div class="dashboard-grid" id="quiz-submissions-container">
        <!-- Quizzes will load dynamically -->
    </div>
</main>

<footer>
    Version 1.3.1
</footer>

<script>
async function loadSubmissions() {
    const container = document.getElementById('quiz-submissions-container');
    container.innerHTML = '';

    try {
        const res = await fetch('get_quiz_submissions.php');
        const quizzes = await res.json();

        quizzes.forEach(quiz => {
            const card = document.createElement('div');
            card.className = 'quiz-card';

            const title = document.createElement('h3');
            title.textContent = quiz.title;
            card.appendChild(title);

            const studentList = document.createElement('ul');
            studentList.className = 'student-list';

            if(quiz.students.length === 0){
                const li = document.createElement('li');
                li.className = 'empty-msg';
                li.textContent = 'No submissions yet';
                studentList.appendChild(li);
            } else {
                quiz.students.forEach(student => {
                    const li = document.createElement('li');
                    li.innerHTML = `<span>${student.student_name}</span><span>Score: ${student.total_score}</span>`;
                    studentList.appendChild(li);
                });
            }

            card.appendChild(studentList);
            container.appendChild(card);
        });

    } catch (err) {
        console.error('Error loading submissions:', err);
        container.innerHTML = '<p style="color:red;">Failed to load submissions</p>';
    }
}

loadSubmissions();
</script>

</body>
</html>
