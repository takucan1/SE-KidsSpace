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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="student_page.css">
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
            <h3>Student Name</h3>
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

<!-- Dashboard: Upcoming Quizzes -->
<?php
    // Load course utilities and get upcoming quizzes for enrolled courses
    require_once __DIR__ . '/../data/courses_data.php';
    $studentEmail = $_SESSION['email'];
    $enrolledCourses = getStudentCourses($studentEmail);

    $upcomingQuizzes = [];
    foreach ($enrolledCourses as $course) {
        $quizzes = getQuizzesByCourse($course['id']);
        foreach ($quizzes as $quiz) {
            // Check if student has already submitted this quiz
            $submissions = getQuizSubmissions($quiz['id']);
            $hasSubmitted = false;
            foreach ($submissions as $sub) {
                if ($sub['student_email'] === $studentEmail) {
                    $hasSubmitted = true;
                    break;
                }
            }
            if (!$hasSubmitted) {
                $upcomingQuizzes[] = [
                    'quiz' => $quiz,
                    'course' => $course
                ];
            }
        }
    }
?>
<div class="dashboard-container">
    <h1 class="dashboard-title">Upcoming Quizzes</h1>

    <div class="submissions-list">
        <?php if (empty($upcomingQuizzes)) : ?>
            <div class="no-data">
                <i class="fas fa-check-circle"></i>
                <p>No upcoming quizzes. Great job staying on top of your coursework!</p>
            </div>
        <?php else: ?>
            <?php foreach ($upcomingQuizzes as $item) : ?>
                <div class="submission-item" tabindex="0">
                    <div class="submission-info">
                        <h3 class="submission-title"><?php echo htmlspecialchars($item['quiz']['title']); ?></h3>
                        <p class="submission-meta"><strong>Course:</strong> <?php echo htmlspecialchars($item['course']['name']); ?> &nbsp;•&nbsp; <strong>Created:</strong> <?php echo htmlspecialchars($item['quiz']['created_at'] ?? 'Recently'); ?></p>
                        <div class="submission-details">
                            <p class="details-text"><?php echo htmlspecialchars($item['quiz']['description'] ?? 'No description available'); ?></p>
                            <p class="details-extra"><strong>Questions:</strong> <?php echo count($item['quiz']['questions']); ?> questions</p>
                        </div>
                    </div>
                    <div class="submission-actions">
                        <a href="take_quiz.php?quiz_id=<?php echo urlencode($item['quiz']['id']); ?>&course_id=<?php echo urlencode($item['course']['id']); ?>" class="btn">Take Quiz</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="footer-version">Version 1.3.1</div>
</div>




<script src="script.js"></script>
<script src="student_profile.js"></script>
    
</body>
</html>