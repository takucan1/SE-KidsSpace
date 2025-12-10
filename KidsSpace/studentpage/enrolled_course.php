<?php
    session_start();
    if(!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit();
    }

    // Get course_id from URL
    if (!isset($_GET['course_id'])) {
        header('Location: courses.php');
        exit();
    }
    $course_id = $_GET['course_id'];

    // Load course utilities
    require_once __DIR__ . '/../data/courses_data.php';
    $studentEmail = $_SESSION['email'];

    // Check if student is enrolled in this course
    $enrolledCourses = getStudentCourses($studentEmail);
    $isEnrolled = false;
    $course = null;
    foreach ($enrolledCourses as $c) {
        if ($c['id'] === $course_id) {
            $isEnrolled = true;
            $course = $c;
            break;
        }
    }

    if (!$isEnrolled || !$course) {
        header('Location: courses.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['name']); ?> - KidsSpace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="courses.css">
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

<!-- Course Details -->
<div class="dashboard-container">
    <h1 class="dashboard-title"><?php echo htmlspecialchars($course['name']); ?></h1>
    <div class="course-details">
        <p><strong>Teacher:</strong> <?php
            $teacherName = getTeacherNameByEmail($course['teacher_email'] ?? '');
            echo htmlspecialchars($teacherName ?? '—');
        ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($course['description'] ?? 'No description'); ?></p>
        <p><strong>Enrolled:</strong> <?php
            $enrollments = getEnrollments();
            $enrolledAt = '';
            foreach ($enrollments as $en) {
                if ($en['course_id'] === $course_id && $en['student_email'] === $studentEmail) {
                    $enrolledAt = $en['enrolled_at'];
                    break;
                }
            }
            echo $enrolledAt ? htmlspecialchars($enrolledAt) : '—';
        ?></p>
    </div>

    <!-- Lessons Section -->
    <div class="section">
        <h2>Lessons</h2>
        <?php $lessons = getLessonsByCourse($course_id); ?>
        <?php if (empty($lessons)) : ?>
            <p>No lessons have been uploaded yet.</p>
        <?php else: ?>
            <div class="lessons-list">
                <?php foreach ($lessons as $lesson) : ?>
                    <div class="lesson-item">
                        <div class="lesson-info">
                            <h4><?php echo htmlspecialchars($lesson['title']); ?></h4>
                            <p><?php echo htmlspecialchars($lesson['description'] ?? ''); ?></p>
                            <small><i class="fas fa-file-pdf"></i>
                                <?php echo isset($lesson['file_size']) ? round($lesson['file_size']/1024/1024,2) . 'MB' : ''; ?> • <?php echo htmlspecialchars($lesson['created_at'] ?? ''); ?>
                            </small>
                        </div>
                        <div class="submission-actions">
                            <?php if (!empty($lesson['file_name'])): ?>
                                <a class="btn" href="<?php echo '../uploads/lessons/' . rawurlencode($lesson['file_name']); ?>" target="_blank" rel="noopener">View / Download</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Activities (Exams) Section -->
    <div class="section">
        <h2>Exams</h2>
        <?php $activities = getActivitiesByCourse($course_id); ?>
        <?php if (empty($activities)) : ?>
            <p>No exams have been uploaded yet.</p>
        <?php else: ?>
            <div class="activities-list">
                <?php foreach ($activities as $activity) : ?>
                    <div class="activity-item">
                        <div class="activity-info">
                            <h4><?php echo htmlspecialchars($activity['title']); ?></h4>
                            <p><?php echo htmlspecialchars($activity['description'] ?? ''); ?></p>
                            <small><?php echo htmlspecialchars($activity['created_at'] ?? ''); ?></small>
                        </div>
                        <div class="submission-actions">
                            <!-- Add link to take exam or view results if applicable -->
                            <a class="btn" href="#">Take Exam</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Announcements Section -->
    <div class="section">
        <h2>Announcements</h2>
        <?php $announcements = getAnnouncementsByCourse($course_id); ?>
        <?php if (empty($announcements)) : ?>
            <p>No announcements yet.</p>
        <?php else: ?>
            <div class="announcements-list">
                <?php foreach ($announcements as $announcement) : ?>
                    <div class="announcement-item">
                        <h4><?php echo htmlspecialchars($announcement['title']); ?></h4>
                        <p><?php echo htmlspecialchars($announcement['content']); ?></p>
                        <small><?php echo htmlspecialchars($announcement['created_at'] ?? ''); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer-version">&nbsp;</div>
</div>

<script src="script.js"></script>
<script src="student_profile.js"></script>

</body>
</html>
