<?php
    session_start();
    if(!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit();
    }

    // Get quiz_id and course_id from URL
    if (!isset($_GET['quiz_id']) || !isset($_GET['course_id'])) {
        header('Location: student_page.php');
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
        header('Location: student_page.php');
        exit();
    }

    // Get quiz data
    $quiz = getQuizById($quiz_id);
    if (!$quiz) {
        header('Location: enrolled_course.php?course_id=' . urlencode($course_id));
        exit();
    }

    // Get student's submission
    $submissions = getQuizSubmissions($quiz_id);
    $submission = null;
    foreach ($submissions as $sub) {
        if ($sub['student_email'] === $studentEmail) {
            $submission = $sub;
            break;
        }
    }

    if (!$submission) {
        header('Location: enrolled_course.php?course_id=' . urlencode($course_id));
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - KidsSpace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="courses.css">
    <style>
        .result-container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .result-header { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem; text-align: center; }
        .score-display { font-size: 3rem; font-weight: bold; margin: 1rem 0; }
        .score-good { color: #22c55e; }
        .score-average { color: #f59e0b; }
        .score-poor { color: #ef4444; }
        .result-summary { display: flex; justify-content: space-around; margin: 1rem 0; }
        .summary-item { text-align: center; }
        .summary-value { font-size: 1.5rem; font-weight: bold; }
        .summary-label { color: #666; font-size: 0.9rem; }
        .question-review { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1rem; }
        .question-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .question-status { padding: 0.5rem 1rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
        .status-correct { background: #dcfce7; color: #166534; }
        .status-incorrect { background: #fee2e2; color: #991b1b; }
        .question-text { font-weight: 600; margin-bottom: 1rem; color: #333; }
        .options { display: flex; flex-direction: column; gap: 0.5rem; }
        .option { padding: 0.8rem; border-radius: 4px; border: 2px solid #e5e7eb; }
        .option.correct { border-color: #22c55e; background: #dcfce7; }
        .option.incorrect { border-color: #ef4444; background: #fee2e2; }
        .option.selected { border-color: #3b82f6; background: #e0f2fe; }
        .option.selected.incorrect { border-color: #ef4444; background: #fee2e2; }
        .actions { text-align: center; margin-top: 2rem; }
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

<div class="result-container">
    <div class="result-header">
        <h1><?php echo htmlspecialchars($quiz['title']); ?> - Results</h1>
        <div class="score-display <?php
            $percentage = ($submission['score'] / $submission['total_questions']) * 100;
            if ($percentage >= 80) echo 'score-good';
            elseif ($percentage >= 60) echo 'score-average';
            else echo 'score-poor';
        ?>">
            <?php echo $submission['score']; ?>/<?php echo $submission['total_questions']; ?>
        </div>
        <p><?php echo round(($submission['score'] / $submission['total_questions']) * 100, 1); ?>% Correct</p>

        <div class="result-summary">
            <div class="summary-item">
                <div class="summary-value"><?php echo $submission['score']; ?></div>
                <div class="summary-label">Correct</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?php echo $submission['total_questions'] - $submission['score']; ?></div>
                <div class="summary-label">Incorrect</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?php echo round(($submission['score'] / $submission['total_questions']) * 100, 1); ?>%</div>
                <div class="summary-label">Score</div>
            </div>
        </div>
    </div>

    <h2>Question Review</h2>

    <?php foreach ($quiz['questions'] as $index => $question): ?>
        <div class="question-review">
            <div class="question-header">
                <h3>Question <?php echo $index + 1; ?></h3>
                <div class="question-status <?php
                    $studentAnswer = isset($submission['answers'][$index]) ? (int)$submission['answers'][$index] : -1;
                    $correctAnswer = $question['correct_answer'];
                    echo ($studentAnswer === $correctAnswer) ? 'status-correct' : 'status-incorrect';
                ?>">
                    <?php echo ($studentAnswer === $correctAnswer) ? 'Correct' : 'Incorrect'; ?>
                </div>
            </div>

            <div class="question-text"><?php echo htmlspecialchars($question['question']); ?></div>

            <div class="options">
                <?php foreach ($question['options'] as $optIndex => $option): ?>
                    <div class="option <?php
                        $isCorrect = ($optIndex === $question['correct_answer']);
                        $isSelected = (isset($submission['answers'][$index]) && (int)$submission['answers'][$index] === $optIndex);

                        if ($isCorrect) echo 'correct';
                        elseif ($isSelected) echo 'selected incorrect';
                    ?>">
                        <?php echo htmlspecialchars($option); ?>
                        <?php if ($isCorrect): ?>
                            <i class="fas fa-check" style="color: #22c55e; margin-left: 0.5rem;"></i>
                        <?php elseif ($isSelected && !$isCorrect): ?>
                            <i class="fas fa-times" style="color: #ef4444; margin-left: 0.5rem;"></i>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="actions">
        <a href="enrolled_course.php?course_id=<?php echo urlencode($course_id); ?>" class="btn btn-primary">Back to Course</a>
        <a href="student_page.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<script src="script.js"></script>
<script src="student_profile.js"></script>

</body>
</html>
