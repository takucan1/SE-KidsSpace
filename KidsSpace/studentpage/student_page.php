<?php
session_start();
if(!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

include 'db_connect.php';

$student_id = 0;
$student_name = "Student";

$student_email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT id, name FROM students WHERE email = ?");
$stmt->bind_param("s", $student_email);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()) {
    $student_id = $row['id'];
    $student_name = $row['name'];
}
$stmt->close();



// Fetch student info
$student_result = $conn->prepare("SELECT id, name FROM students WHERE email = ?");
$student_result->bind_param("s", $student_email);
$student_result->execute();
$student_result->bind_result($student_id, $student_name);
$student_result->fetch();
$student_result->close();

$student_id = 0;
$student_name = "Student";

// Prepare and execute query
$stmt = $conn->prepare("SELECT id, name FROM students WHERE email = ?");
$stmt->bind_param("s", $student_email);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()) {
    $student_id = $row['id'];
    $student_name = $row['name'];
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="student_page.css">
    <style>
        .quiz-block { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 8px; background-color: #f9f9f9; }
        .quiz-question { margin-bottom: 10px; }
        .quiz-options input { margin-right: 5px; }
        .submit-quiz-btn { margin-top: 10px; padding: 5px 10px; cursor: pointer; }
        .completed-quiz { background-color: #e0ffe0; border-color: #7ad17a; }
    </style>
</head>
<body>

<!-- Start of Header Section -->
<header class="header">
    <section class="flex">
        <a href="student_page.php" class="logo">KidsSpace</a>

        <form action="" method="post" class="search-form">
            <input type="text" name="search-box" placeholder="search courses..." required maxlength="100">
            <button type="submit" class="fa fa-search" name="search_box"></button>
        </form>

        <div class="icons">
            <div id="menu-btn" class="fa fa-bars"></div>
            <div id="search-btn" class="fa fa-search"></div>
            <div id="user-btn" class="fa fa-user"></div>
            <div id="toggle-btn" class="fa fa-sun"></div>
        </div>

        <div class="profile">
            <img src="studentprofile.png" alt="">
            <h3><?php echo htmlspecialchars($student_name); ?></h3>
            <span>student</span>
            <a href="profile.html" class="btn">View Profile</a>
            <div class="flex-btn">
                <a href="login_register.php" class="btn">Login</a>
                <a href="login_register.php" class="btn">Register</a>
            </div>
        </div>
    </section>
</header>
<!-- End of Header Section -->

<!-- Start of Side bar Section -->
<div class="side-bar">
    <div class="profile">
        <img src="studentprofile.png" alt="">
        <h3><?php echo htmlspecialchars($student_name); ?></h3>
        <span>student</span>
        <a href="profile.html" class="btn">View Profile</a>
    </div>

    <nav class="navbar">
        <a href="student_page.php"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="about_us.html"><i class="fas fa-question"></i><span>About us</span></a>
        <a href="courses.php"><i class="fas fa-book"></i><span>Courses</span></a>
        <a href="teachers.html"><i class="fas fa-chalkboard-teacher"></i><span>Teachers</span></a>
        <a href="contact.html"><i class="fas fa-headset"></i><span>Contact us</span></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </nav>
</div>
<!-- End of Side Bar Section -->

<!-- Dashboard Content -->
<div class="dashboard-container">
    <h1 class="dashboard-title">Upcoming Submissions</h1>
    <div class="submissions-list">
        <!-- Placeholder items -->
        <div class="submission-item" tabindex="0">
            <div class="submission-info">
                <h3 class="submission-title">[Assignment Title]</h3>
                <p class="submission-meta"><strong>Course:</strong> [Course Name] &nbsp;•&nbsp; <strong>Due:</strong> [YYYY-MM-DD HH:MM]</p>
                <div class="submission-details">
                    <p class="details-text">Brief description: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Include submission instructions, file types accepted, and any rubric notes here.</p>
                    <p class="details-extra"><strong>Attachments:</strong> syllabus.pdf, resources.zip</p>
                </div>
            </div>
            <div class="submission-actions">
                <a href="#" class="btn">View</a>
            </div>
        </div>
    </div>

    <!-- Global Quizzes Section -->
    <h2 class="dashboard-title">Global Quizzes</h2>
    <div id="quizzes-container">
        <!-- Quizzes loaded dynamically -->
    </div>

    <div class="footer-version">Version 1.3.1</div>
</div>

<script>
const studentId = <?php echo $student_id; ?>;

async function loadQuizzes() {
    const res = await fetch('get_quizzes.php');
    const quizzes = await res.json();
    const container = document.getElementById('quizzes-container');
    container.innerHTML = '';

    for (const quiz of quizzes) {
        // Check if student already submitted this quiz
        const checkRes = await fetch(`check_quiz_submitted.php?student_id=${studentId}&quiz_id=${quiz.id}`);
        const {submitted, total_score} = await checkRes.json();

        const quizDiv = document.createElement('div');
        quizDiv.classList.add('quiz-block');
        if(submitted) quizDiv.classList.add('completed-quiz');

        // Quiz header (clickable)
        const header = document.createElement('div');
        header.classList.add('quiz-header');
        header.style.cursor = 'pointer';
        header.innerHTML = `<h3>${quiz.title}</h3><p>${quiz.description}</p>`;
        quizDiv.appendChild(header);

        // Container for full quiz questions (hidden initially)
        const quizContent = document.createElement('div');
        quizContent.classList.add('quiz-content');
        quizContent.style.display = 'none';
        quizDiv.appendChild(quizContent);

        if(submitted) {
            const completedMsg = document.createElement('p');
            completedMsg.style.color = 'green';
            completedMsg.innerHTML = `You have already completed this quiz. Score: ${total_score}`;
            quizContent.appendChild(completedMsg);
        } else {
            // Load questions dynamically when clicked
            header.addEventListener('click', async () => {
                // Toggle visibility
                quizContent.style.display = quizContent.style.display === 'none' ? 'block' : 'none';

                // If already loaded, do not reload
                if(quizContent.dataset.loaded) return;

                const qRes = await fetch(`get_quiz_questions.php?quiz_id=${quiz.id}`);
                const questions = await qRes.json();

                if(questions.length === 0) {
                    quizContent.innerHTML = '<p>No questions available for this quiz.</p>';
                    return;
                }

                const form = document.createElement('form');
                form.setAttribute('data-quiz-id', quiz.id);

                questions.forEach(q => {
                    const qDiv = document.createElement('div');
                    qDiv.classList.add('quiz-question');
                    qDiv.innerHTML = `<strong>${q.question}</strong><br>
                        <div class="quiz-options">
                            <label><input type="radio" name="q${q.id}" value="A"> ${q.option_a}</label><br>
                            <label><input type="radio" name="q${q.id}" value="B"> ${q.option_b}</label><br>
                            <label><input type="radio" name="q${q.id}" value="C"> ${q.option_c}</label><br>
                            <label><input type="radio" name="q${q.id}" value="D"> ${q.option_d}</label>
                        </div>`;
                    form.appendChild(qDiv);
                });

                const submitBtn = document.createElement('button');
                submitBtn.textContent = 'Submit Quiz';
                submitBtn.type = 'submit';
                submitBtn.classList.add('submit-quiz-btn');
                form.appendChild(submitBtn);

                form.addEventListener('submit', async e => {
                    e.preventDefault();
                    const formData = new FormData(form);
                    const answers = {};
                    let allAnswered = true;

                    questions.forEach(q => {
                        const answer = formData.get(`q${q.id}`);
                        if(!answer) allAnswered = false;
                        answers[q.id] = answer;
                    });
                    
                    console.log({
                    student_id: studentId,
                    quiz_id: quiz.id,
                    answers: answers,
                     });


                    if(!allAnswered) {
                        alert('Please answer all questions before submitting.');
                        return;
                    }

                    try {
                        const res = await fetch('submit_quiz.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({
                                student_id: studentId,
                                quiz_id: quiz.id,
                                answers: answers
                            })
                        });

                        const result = await res.json();
                        if(result.status === 'success') {
                            alert('Quiz submitted! Total Score: ' + result.total_score);
                            loadQuizzes(); // reload quizzes to update status
                        } else {
                            alert('Error: ' + (result.message || 'Failed to submit quiz'));
                        }
                    } catch(err) {
                        console.error(err);
                        alert('Network or server error. Please try again.');
                    }
                });

                quizContent.appendChild(form);
                quizContent.dataset.loaded = true; // mark as loaded
            });
        }

        container.appendChild(quizDiv);
    }
}

loadQuizzes();


</script>

<script src="script.js"></script>
<script src="student_profile.js"></script>
    
</body>
</html>
