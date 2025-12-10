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

<!-- Student Enrolled Courses -->
<?php
    // Load course utilities and show enrolled courses for logged-in student
    require_once __DIR__ . '/../data/courses_data.php';
    $studentEmail = $_SESSION['email'];
    $enrolledCourses = getStudentCourses($studentEmail);
?>
<div class="dashboard-container">
    <h1 class="dashboard-title">My Courses</h1>
    <div class="accordion">
        <?php if (empty($enrolledCourses)) : ?>
            <div class="no-data">
                <i class="fas fa-book-reader"></i>
                <p>You are not enrolled in any courses yet. Visit the <a href="../view_courses.php">Courses</a> page to browse available classes.</p>
            </div>
        <?php else: ?>
            <?php foreach ($enrolledCourses as $course): ?>
                <a href="enrolled_course.php?course_id=<?php echo urlencode($course['id']); ?>" class="course-link">
                    <div class="accordion-item">
                        <span><?php echo htmlspecialchars($course['name']); ?></span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="footer-version">&nbsp;</div>
</div>


<script src="script.js"></script>
<script src="student_profile.js"></script>
<script src="courses.js"></script>
    
</body>
</html>