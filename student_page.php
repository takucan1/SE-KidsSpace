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
<body">

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
            <img src="images/profile.png" alt="">
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
            <img src="images/profile.png" alt="">
            <h3>Student Name</h3>
            <span>student</span>
            <a href="profile.html" class="btn">View Profile</a>
    </div>

    <nav class="navbar">
        <a href="student_page.php"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="sidebar/about_us.html"><i class="fas fa-question"></i><span>About us</span></a>
        <a href="sidebar/course.html"><i class="fas fa-book"></i><span>Courses</span></a>
        <a href="teachers.html"><i class="fas fa-chalkboard-teacher"></i><span>Teachers</span></a>
        <a href="sidebar/contact_us.html"><i class="fas fa-headset"></i><span>Contact us</span></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </nav>
</div>

<!-- End of Side Bar Section -->

<!-- Dashboard: Upcoming submissions outline -->
<div class="dashboard-container">
    <h1 class="dashboard-title">Upcoming Submissions</h1>

    <div class="submissions-list">
        <!-- Placeholder items; replace with dynamic content later -->
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

        <div class="submission-item" tabindex="0">
            <div class="submission-info">
                <h3 class="submission-title">[Assignment Title]</h3>
                <p class="submission-meta"><strong>Course:</strong> [Course Name] &nbsp;•&nbsp; <strong>Due:</strong> [YYYY-MM-DD HH:MM]</p>
                <div class="submission-details">
                    <p class="details-text">Brief description: Add details for this assignment. Include steps to submit and any notes about late penalties.</p>
                    <p class="details-extra"><strong>Attachments:</strong> handout.docx</p>
                </div>
            </div>
            <div class="submission-actions">
                <a href="#" class="btn">View</a>
            </div>
        </div>

        <div class="no-more">Show more submissions below or load dynamically.</div>
    </div>

    <div class="footer-version">Version 1.3.1</div>
</div>


<script src="script.js"></script>
<script src="student_profile.js"></script>
    
</body>
</html>