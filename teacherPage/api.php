<?php
session_start();
header('Content-Type: application/json');

// Set error handling to prevent HTML error output
error_reporting(E_ALL);
ini_set('display_errors', '0');

try {
    require_once '../data/courses_data.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to load dependencies']);
    exit();
}

// Check if teacher is logged in
if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Handle different actions
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// CREATE COURSE
if ($action === 'create_course' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = trim($_POST['course_name'] ?? '');
    $course_desc = trim($_POST['course_description'] ?? '');
    
    if (empty($course_name)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Course name is required']);
        exit();
    }
    
    $course = [
        'id' => uniqid('course_', true),
        'name' => htmlspecialchars($course_name),
        'description' => htmlspecialchars($course_desc),
        'teacher_email' => $_SESSION['email'],
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    if (saveCourse($course)) {
        echo json_encode(['success' => true, 'message' => 'Course created successfully', 'course' => $course]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create course']);
    }
    exit();
}

// UPLOAD LESSON
if ($action === 'upload_lesson' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = trim($_POST['course_id'] ?? '');
    $lesson_title = trim($_POST['lesson_title'] ?? '');
    $lesson_desc = trim($_POST['lesson_description'] ?? '');
    
    if (empty($course_id) || empty($lesson_title)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Course and lesson title are required']);
        exit();
    }
    
    if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No PDF file uploaded']);
        exit();
    }
    
    $file = $_FILES['pdf_file'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Validate PDF file
    if ($file_ext !== 'pdf') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Only PDF files are allowed']);
        exit();
    }
    
    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if ($mime_type !== 'application/pdf') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid PDF file']);
        exit();
    }
    
    // Validate file size (50MB max)
    $max_size = 50 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File size exceeds 50MB limit']);
        exit();
    }
    
    // Create uploads directory
    $upload_dir = dirname(__DIR__) . '/uploads/lessons/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Save file with unique name
    $unique_filename = uniqid('lesson_', true) . '.pdf';
    $upload_path = $upload_dir . $unique_filename;
    
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save file']);
        exit();
    }
    
    $lesson = [
        'id' => uniqid('lesson_', true),
        'course_id' => htmlspecialchars($course_id),
        'title' => htmlspecialchars($lesson_title),
        'description' => htmlspecialchars($lesson_desc),
        'file_name' => $unique_filename,
        'teacher_email' => $_SESSION['email'],
        'created_at' => date('Y-m-d H:i:s'),
        'file_size' => $file['size']
    ];
    
    if (saveLesson($lesson)) {
        echo json_encode(['success' => true, 'message' => 'Lesson uploaded successfully', 'lesson' => $lesson]);
    } else {
        unlink($upload_path);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save lesson metadata']);
    }
    exit();
}

// GET COURSES
if ($action === 'get_courses') {
    $courses = getAllCourses();
    echo json_encode(['success' => true, 'courses' => $courses]);
    exit();
}

// GET LESSONS BY COURSE
if ($action === 'get_lessons' && isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    $lessons = getLessonsByCourse($course_id);
    echo json_encode(['success' => true, 'lessons' => array_values($lessons)]);
    exit();
}

// GET REGISTERED STUDENTS (for teacher to pick from)
if ($action === 'get_registered_students') {
    try {
        require_once '../config.php';
        if (!isset($conn) || $conn->connect_error) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database connection failed']);
            exit();
        }

        $sql = "SELECT name, email FROM users WHERE role = 'student' ORDER BY name ASC";
        $result = $conn->query($sql);
        $students = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $students[] = $row;
            }
        }

        echo json_encode(['success' => true, 'students' => $students]);
        exit();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to load students']);
        exit();
    }
}

// ENROLL STUDENT
if ($action === 'enroll_student' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'] ?? '';
    $student_email = $_POST['student_email'] ?? '';
    
    if (empty($course_id) || empty($student_email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Course ID and student email are required']);
        exit();
    }
    
    // Verify student exists in database
    try {
        require_once '../config.php';
        
        if (!isset($conn) || $conn->connect_error) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database connection failed']);
            exit();
        }
        
        // Check against the correct users table and ensure role is 'student'
        $stmt = $conn->prepare("SELECT email, role FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $student_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Student not found in database']);
            exit();
        }

        $row = $result->fetch_assoc();
        if (!isset($row['role']) || strtolower($row['role']) !== 'student') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User is not a student']);
            exit();
        }
        $stmt->close();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
    
    if (enrollStudent($course_id, $student_email)) {
        echo json_encode(['success' => true, 'message' => 'Student enrolled successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Student already enrolled in this course']);
    }
    exit();
}

// GET ENROLLED STUDENTS
if ($action === 'get_enrolled_students' && isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    $students = getEnrolledStudents($course_id);
    echo json_encode(['success' => true, 'students' => $students]);
    exit();
}

// REMOVE STUDENT FROM COURSE
if ($action === 'remove_student' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'] ?? '';
    $student_email = $_POST['student_email'] ?? '';
    
    if (empty($course_id) || empty($student_email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Course ID and student email are required']);
        exit();
    }
    
    if (removeStudentFromCourse($course_id, $student_email)) {
        echo json_encode(['success' => true, 'message' => 'Student removed from course']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to remove student']);
    }
    exit();
}

// DELETE LESSON
if ($action === 'delete_lesson' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $lesson_id = $_POST['lesson_id'] ?? '';
    
    if (empty($lesson_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Lesson ID is required']);
        exit();
    }
    
    if (deleteLesson($lesson_id)) {
        echo json_encode(['success' => true, 'message' => 'Lesson deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete lesson']);
    }
    exit();
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
