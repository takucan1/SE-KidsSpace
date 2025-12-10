<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['email'])) {
    header('Location: ../login_register.php');
    exit();
}

require_once '../data/courses_data.php';

// Get ONLY courses this student is enrolled in
$student_email = $_SESSION['email'];
$courses = getStudentCourses($student_email);

// Handle PDF download/view
if (isset($_GET['download']) || isset($_GET['view'])) {
    $lesson_id = $_GET['download'] ?? $_GET['view'];
    $all_lessons = [];
    
    $lessons_file = dirname(__DIR__) . '/data/lessons.json';
    if (file_exists($lessons_file)) {
        $all_lessons = json_decode(file_get_contents($lessons_file), true) ?: [];
    }
    
    $lesson = null;
    foreach ($all_lessons as $l) {
        if ($l['id'] === $lesson_id) {
            $lesson = $l;
            break;
        }
    }
    
    if (!$lesson) {
        die('Lesson not found');
    }
    
    $file_path = dirname(__DIR__) . '/uploads/lessons/' . $lesson['file_name'];
    
    if (!file_exists($file_path)) {
        die('File not found');
    }
    
    if (isset($_GET['download'])) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . urlencode($lesson['title']) . '.pdf"');
    } else {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . urlencode($lesson['title']) . '.pdf"');
    }
    
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student - Browse Courses & Lessons</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../student_page.css">
    <style>
        .lessons-main { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .lessons-main h1 { font-size: 2rem; margin-bottom: 2rem; color: #333; display: flex; align-items: center; gap: 0.8rem; }
        
        .courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem; }
        .course-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease; }
        .course-card:hover { transform: translateY(-4px); box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
        
        .course-header { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; padding: 1.5rem; }
        .course-header h3 { font-size: 1.3rem; margin-bottom: 0.5rem; }
        .course-header p { font-size: 0.9rem; opacity: 0.9; }
        
        .course-body { padding: 1.5rem; }
        .lessons-in-course { display: flex; flex-direction: column; gap: 1rem; }
        .lesson-item { background: #f9fafb; padding: 1rem; border-radius: 4px; border-left: 4px solid #3b82f6; }
        .lesson-item h4 { font-size: 1rem; color: #333; margin-bottom: 0.3rem; }
        .lesson-item p { font-size: 0.85rem; color: #666; margin-bottom: 0.8rem; }
        .lesson-meta { font-size: 0.8rem; color: #999; margin-bottom: 1rem; }
        
        .lesson-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .lesson-actions a { padding: 0.6rem 1rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; transition: all 0.3s ease; }
        .btn-view { background: #3b82f6; color: white; }
        .btn-view:hover { background: #2563eb; }
        .btn-download { background: #10b981; color: white; }
        .btn-download:hover { background: #059669; }
        
        .no-courses { text-align: center; padding: 4rem 1rem; color: #999; }
        .no-courses i { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }
        
        .expand-btn { background: none; border: none; color: #3b82f6; cursor: pointer; font-weight: 600; padding: 0; font-size: 0.9rem; }
        .expand-btn:hover { text-decoration: underline; }
        
        @media (max-width: 768px) {
            .courses-grid { grid-template-columns: 1fr; }
            .course-header h3 { font-size: 1.1rem; }
            .lessons-in-course { gap: 0.8rem; }
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <header class="header">
        <section class="flex">
            <a href="../student_page.php" class="logo">KidsSpace</a>
            <div class="icons">
                <a href="../student_page.php" class="fa fa-home" title="Home"></a>
                <div id="user-btn" class="fa fa-user" title="Profile"></div>
            </div>
            <div class="profile">
                <img src="../images/profile.png" alt="">
                <h3>Student</h3>
                <a href="../logout.php" class="btn">Logout</a>
            </div>
        </section>
    </header>

    <!-- MAIN CONTENT -->
    <main class="lessons-main">
        <h1><i class="fas fa-book-open"></i> Available Courses & Lessons</h1>
        
        <?php if (empty($courses)): ?>
            <div class="no-courses">
                <i class="fas fa-inbox"></i>
                <p>No courses available yet. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="courses-grid">
                <?php foreach ($courses as $course):
                    $lessons = getLessonsByCourse($course['id']);
                ?>
                    <div class="course-card">
                        <div class="course-header">
                            <h3><?php echo htmlspecialchars($course['name']); ?></h3>
                            <p><?php echo htmlspecialchars($course['description'] ?? 'No description'); ?></p>
                        </div>
                        
                        <div class="course-body">
                            <?php if (empty($lessons)): ?>
                                <p style="color: #999; text-align: center;">No lessons in this course yet</p>
                            <?php else: ?>
                                <div class="lessons-in-course">
                                    <?php foreach ($lessons as $lesson): ?>
                                        <div class="lesson-item">
                                            <h4><i class="fas fa-file-pdf"></i> <?php echo htmlspecialchars($lesson['title']); ?></h4>
                                            <p><?php echo htmlspecialchars($lesson['description'] ?? 'No description'); ?></p>
                                            <div class="lesson-meta">
                                                <small><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($lesson['created_at'])); ?></small>
                                                <small style="margin-left: 1rem;"><i class="fas fa-database"></i> <?php echo round($lesson['file_size'] / 1024 / 1024, 2); ?>MB</small>
                                            </div>
                                            <div class="lesson-actions">
                                                <a href="?view=<?php echo urlencode($lesson['id']); ?>" class="btn-view" target="_blank">
                                                    <i class="fas fa-eye"></i> View PDF
                                                </a>
                                                <a href="?download=<?php echo urlencode($lesson['id']); ?>" class="btn-download">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script src="../student_profile.js"></script>
</body>
</html>
