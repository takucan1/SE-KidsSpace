<?php
    session_start();
    if(!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: student_page.php');
        exit();
    }

    // Get form data
    $quiz_id = $_POST['quiz_id'] ?? '';
    $course_id = $_POST['course_id'] ?? '';
    $answers = $_POST['answers'] ?? [];

    if (empty($quiz_id) || empty($course_id)) {
        header('Location: student_page.php');
        exit();
    }

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

    // Calculate score
    $score = 0;
    $totalQuestions = count($quiz['questions']);

    foreach ($quiz['questions'] as $index => $question) {
        $studentAnswer = isset($answers[$index]) ? (int)$answers[$index] : -1;
        $correctAnswer = $question['correct_answer'];

        if ($studentAnswer === $correctAnswer) {
            $score++;
        }
    }

    // Save submission
    $submissionData = [
        'quiz_id' => $quiz_id,
        'student_email' => $studentEmail,
        'answers' => $answers,
        'score' => $score,
        'total_questions' => $totalQuestions,
        'submitted_at' => date('Y-m-d H:i:s')
    ];

    saveQuizSubmission($submissionData);

    // Redirect to results page
    header('Location: quiz_result.php?quiz_id=' . urlencode($quiz_id) . '&course_id=' . urlencode($course_id));
    exit();
?>
