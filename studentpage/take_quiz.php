<?php
    session_start();
    if(!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit();
    }

    // Get quiz_id and course_id from URL
    if (!isset($_GET['quiz_id']) || !isset($_GET['course_id'])) {
        header('Location: courses.php');
        exit();
    }
    $quiz_id = $_GET['quiz_id'];
    $course_id = $_GET['course_id'];

    // Load course utilities
    require_once __DIR__ . '/../data/courses_data.php';
    $studentEmail = $_SESSION['email'];

    // Check if student is enrolled in this course
    $enrolledCourses = getStudentCourses($studentEmail);
    $isEnrolled = false;
    foreach ($enrolledCourses as $c) {
        if ($c['id'] === $course_id) {
            $isEnrolled = true;
            break;
        }
    }

    if (!$isEnrolled) {
        header('Location: courses.php');
        exit();
    }

    // Get quiz data
    $quiz = getQuizById($quiz_id);
    if (!$quiz) {
        header('Location: enrolled_course.php?course_id=' . urlencode($course_id));
        exit();
    }

    // Check if student has already submitted this quiz
    $submissions = getQuizSubmissions($quiz_id);
    $hasSubmitted = false;
    foreach ($submissions as $sub) {
        if ($sub['student_email'] === $studentEmail) {
            $hasSubmitted = true;
            break;
        }
    }

    if ($hasSubmitted) {
        header('Location: enrolled_course.php?course_id=' . urlencode($course_id));
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz['title']); ?> - KidsSpace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="courses.css">
    <style>
        .quiz-container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .quiz-header { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .quiz-question { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1rem; }
        .question-text { font-size: 1.2rem; margin-bottom: 1rem; color: #333; }
        .options { display: flex; flex-direction: column; gap: 0.5rem; }
        .option { padding: 0.8rem; border: 2px solid #e5e7eb; border-radius: 4px; cursor: pointer; transition: all 0.3s ease; }
        .option:hover { border-color: #3b82f6; background: #f0f7ff; }
        .option.selected { border-color: #3b82f6; background: #e0f2fe; }
        .option input[type="radio"] { display: none; }
        .option label { cursor: pointer; display: block; }
        .quiz-navigation { display: flex; justify-content: space-between; margin-top: 2rem; }
        .quiz-progress { text-align: center; margin-bottom: 1rem; }
        .progress-bar { width: 100%; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(135deg, #3b82f6, #2563eb); transition: width 0.3s ease; }
        .timer { font-size: 1.1rem; color: #ef4444; font-weight: bold; text-align: center; margin-bottom: 1rem; }
    </style>
</head>
<body>

<!-- Start of Header Section -->

<header class="header">
    <section class="flex">
        <a href="student_page.php" class="logo">KidsSpace</a>
        <div class="icons">
            <div id="menu-btn" class="fa fa-bars"></div>
            <div id="user-btn" class="fa fa-user"></div>
        </div>
        <div class="profile">
            <img src="studentprofile.png" alt="">
            <h3>Student Name</h3>
            <span>student</span>
            <a href="profile.html" class="btn">View Profile</a>
        </div>
    </section>
</header>

<!-- End of Header Section -->

<!-- Start of Side bar Section -->

<div class="side-bar">
    <div class="profile">
        <img src="studentprofile.png" alt="">
        <h3>Student Name</h3>
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

<div class="quiz-container">
    <div class="quiz-header">
        <h1><?php echo htmlspecialchars($quiz['title']); ?></h1>
        <p><?php echo htmlspecialchars($quiz['description'] ?? ''); ?></p>
        <div class="quiz-progress">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 0%"></div>
            </div>
            <p>Question <span id="currentQuestion">1</span> of <?php echo count($quiz['questions']); ?></p>
        </div>
        <div class="timer" id="timer">Time remaining: <span id="timeRemaining">30:00</span></div>
    </div>

    <form id="quizForm" method="POST" action="submit_quiz.php">
        <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($quiz_id); ?>">
        <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course_id); ?>">

        <?php foreach ($quiz['questions'] as $index => $question): ?>
            <div class="quiz-question" id="question-<?php echo $index; ?>" <?php if ($index > 0) echo 'style="display: none;"'; ?>>
                <div class="question-text"><?php echo htmlspecialchars($question['question']); ?></div>
                <div class="options">
                    <?php foreach ($question['options'] as $optIndex => $option): ?>
                        <div class="option">
                            <input type="radio" id="q<?php echo $index; ?>_opt<?php echo $optIndex; ?>" name="answers[<?php echo $index; ?>]" value="<?php echo $optIndex; ?>" required>
                            <label for="q<?php echo $index; ?>_opt<?php echo $optIndex; ?>"><?php echo htmlspecialchars($option); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="quiz-navigation">
            <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">Previous</button>
            <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
            <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">Submit Quiz</button>
        </div>
    </form>
</div>

<script>
    const questions = <?php echo json_encode($quiz['questions']); ?>;
    let currentQuestionIndex = 0;
    let timeRemaining = 30 * 60; // 30 minutes in seconds
    let timerInterval;

    function updateProgress() {
        const progress = ((currentQuestionIndex + 1) / questions.length) * 100;
        document.getElementById('progressFill').style.width = progress + '%';
        document.getElementById('currentQuestion').textContent = currentQuestionIndex + 1;
    }

    function showQuestion(index) {
        // Hide all questions
        questions.forEach((_, i) => {
            document.getElementById(`question-${i}`).style.display = 'none';
        });

        // Show current question
        document.getElementById(`question-${index}`).style.display = 'block';

        // Update navigation buttons
        document.getElementById('prevBtn').style.display = index > 0 ? 'block' : 'none';
        document.getElementById('nextBtn').style.display = index < questions.length - 1 ? 'block' : 'none';
        document.getElementById('submitBtn').style.display = index === questions.length - 1 ? 'block' : 'none';

        updateProgress();
    }

    function updateTimer() {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        document.getElementById('timeRemaining').textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            document.getElementById('quizForm').submit();
        } else {
            timeRemaining--;
        }
    }

    // Navigation buttons
    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
            showQuestion(currentQuestionIndex);
        }
    });

    document.getElementById('nextBtn').addEventListener('click', () => {
        if (currentQuestionIndex < questions.length - 1) {
            currentQuestionIndex++;
            showQuestion(currentQuestionIndex);
        }
    });

    // Option selection
    document.querySelectorAll('.option').forEach(option => {
        option.addEventListener('click', () => {
            const radio = option.querySelector('input[type="radio"]');
            radio.checked = true;

            // Remove selected class from all options in this question
            option.parentElement.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
            // Add selected class to clicked option
            option.classList.add('selected');
        });
    });

    // Start timer
    timerInterval = setInterval(updateTimer, 1000);

    // Initialize
    showQuestion(0);
</script>

<script src="script.js"></script>
<script src="student_profile.js"></script>

</body>
</html>
