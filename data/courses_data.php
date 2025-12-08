<?php
// courses_data.php - Utility functions for course and lesson management

function getCourseDataFile() {
    $dir = dirname(__DIR__) . '/data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir . '/courses.json';
}

function getLessonDataFile() {
    $dir = dirname(__DIR__) . '/data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir . '/lessons.json';
}

function getActivityDataFile() {
    $dir = dirname(__DIR__) . '/data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir . '/activities.json';
}

function getQuizDataFile() {
    $dir = dirname(__DIR__) . '/data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir . '/quizzes.json';
}

function getQuizSubmissionDataFile() {
    $dir = dirname(__DIR__) . '/data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir . '/quiz_submissions.json';
}

function getAnnouncementDataFile() {
    $dir = dirname(__DIR__) . '/data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir . '/announcements.json';
}

function getTeachersDataFile() {
    $dir = dirname(__DIR__) . '/data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir . '/teachers.json';
}

function getAllCourses() {
    $file = getCourseDataFile();
    if (!file_exists($file)) {
        return [];
    }
    return json_decode(file_get_contents($file), true) ?: [];
}

function getCourseById($id) {
    $courses = getAllCourses();
    foreach ($courses as $course) {
        if ($course['id'] === $id) {
            return $course;
        }
    }
    return null;
}

function saveCourse($course) {
    $courses = getAllCourses();
    $courses[] = $course;
    $file = getCourseDataFile();
    return file_put_contents($file, json_encode($courses, JSON_PRETTY_PRINT));
}

function getLessonsByCourse($course_id) {
    $file = getLessonDataFile();
    if (!file_exists($file)) {
        return [];
    }
    $lessons = json_decode(file_get_contents($file), true) ?: [];
    return array_filter($lessons, function($lesson) use ($course_id) {
        return $lesson['course_id'] === $course_id;
    });
}

function saveLesson($lesson) {
    $lessons = [];
    $file = getLessonDataFile();
    if (file_exists($file)) {
        $lessons = json_decode(file_get_contents($file), true) ?: [];
    }
    $lessons[] = $lesson;
    return file_put_contents($file, json_encode($lessons, JSON_PRETTY_PRINT));
}

function deleteLesson($lesson_id) {
    $file = getLessonDataFile();
    if (!file_exists($file)) {
        return false;
    }
    $lessons = json_decode(file_get_contents($file), true) ?: [];
    $lessons = array_filter($lessons, function($lesson) use ($lesson_id) {
        return $lesson['id'] !== $lesson_id;
    });
    return file_put_contents($file, json_encode(array_values($lessons), JSON_PRETTY_PRINT));
}

function getEnrollmentDataFile() {
    $dir = dirname(__DIR__) . '/data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir . '/enrollments.json';
}

function enrollStudent($course_id, $student_email) {
    $enrollments = getEnrollments();
    
    // Check if already enrolled
    foreach ($enrollments as $enrollment) {
        if ($enrollment['course_id'] === $course_id && $enrollment['student_email'] === $student_email) {
            return false; // Already enrolled
        }
    }
    
    $enrollment = [
        'id' => uniqid('enrollment_', true),
        'course_id' => $course_id,
        'student_email' => $student_email,
        'enrolled_at' => date('Y-m-d H:i:s')
    ];
    
    $enrollments[] = $enrollment;
    return file_put_contents(getEnrollmentDataFile(), json_encode($enrollments, JSON_PRETTY_PRINT));
}

function getEnrollments() {
    $file = getEnrollmentDataFile();
    if (!file_exists($file)) {
        return [];
    }
    return json_decode(file_get_contents($file), true) ?: [];
}

function getStudentCourses($student_email) {
    $enrollments = getEnrollments();
    $course_ids = array_map(function($e) { return $e['course_id']; }, 
        array_filter($enrollments, function($e) use ($student_email) {
            return $e['student_email'] === $student_email;
        })
    );
    
    $courses = getAllCourses();
    return array_filter($courses, function($course) use ($course_ids) {
        return in_array($course['id'], $course_ids);
    });
}

function getEnrolledStudents($course_id) {
    $enrollments = getEnrollments();
    return array_map(function($e) { return $e['student_email']; },
        array_filter($enrollments, function($e) use ($course_id) {
            return $e['course_id'] === $course_id;
        })
    );
}

function removeStudentFromCourse($course_id, $student_email) {
    $enrollments = getEnrollments();
    $enrollments = array_filter($enrollments, function($e) use ($course_id, $student_email) {
        return !($e['course_id'] === $course_id && $e['student_email'] === $student_email);
    });
    return file_put_contents(getEnrollmentDataFile(), json_encode(array_values($enrollments), JSON_PRETTY_PRINT));
}

function getActivitiesByCourse($course_id) {
    $file = getActivityDataFile();
    if (!file_exists($file)) {
        return [];
    }
    $activities = json_decode(file_get_contents($file), true) ?: [];
    return array_filter($activities, function($activity) use ($course_id) {
        return $activity['course_id'] === $course_id;
    });
}

function saveActivity($activity) {
    $activities = [];
    $file = getActivityDataFile();
    if (file_exists($file)) {
        $activities = json_decode(file_get_contents($file), true) ?: [];
    }
    $activities[] = $activity;
    return file_put_contents($file, json_encode($activities, JSON_PRETTY_PRINT));
}

function getAnnouncementsByCourse($course_id) {
    $file = getAnnouncementDataFile();
    if (!file_exists($file)) {
        return [];
    }
    $announcements = json_decode(file_get_contents($file), true) ?: [];
    return array_filter($announcements, function($announcement) use ($course_id) {
        return $announcement['course_id'] === $course_id;
    });
}

function saveAnnouncement($announcement) {
    $announcements = [];
    $file = getAnnouncementDataFile();
    if (file_exists($file)) {
        $announcements = json_decode(file_get_contents($file), true) ?: [];
    }
    $announcements[] = $announcement;
    return file_put_contents($file, json_encode($announcements, JSON_PRETTY_PRINT));
}

function getTeacherNameByEmail($email) {
    $file = getTeachersDataFile();
    if (!file_exists($file)) {
        return null;
    }
    $teachers = json_decode(file_get_contents($file), true) ?: [];
    foreach ($teachers as $teacher) {
        if ($teacher['email'] === $email) {
            return $teacher['name'];
        }
    }
    return null;
}

function getQuizzesByCourse($course_id) {
    $file = getQuizDataFile();
    if (!file_exists($file)) {
        return [];
    }
    $quizzes = json_decode(file_get_contents($file), true) ?: [];
    return array_filter($quizzes, function($quiz) use ($course_id) {
        return $quiz['course_id'] === $course_id;
    });
}

function saveQuiz($quiz) {
    $quizzes = [];
    $file = getQuizDataFile();
    if (file_exists($file)) {
        $quizzes = json_decode(file_get_contents($file), true) ?: [];
    }
    $quizzes[] = $quiz;
    return file_put_contents($file, json_encode($quizzes, JSON_PRETTY_PRINT));
}

function getAllQuizzes() {
    $file = getQuizDataFile();
    if (!file_exists($file)) {
        return [];
    }
    return json_decode(file_get_contents($file), true) ?: [];
}

function getQuizSubmissions($quiz_id) {
    $file = getQuizSubmissionDataFile();
    if (!file_exists($file)) {
        return [];
    }
    $submissions = json_decode(file_get_contents($file), true) ?: [];
    return array_filter($submissions, function($submission) use ($quiz_id) {
        return $submission['quiz_id'] === $quiz_id;
    });
}

function saveQuizSubmission($submission) {
    $submissions = [];
    $file = getQuizSubmissionDataFile();
    if (file_exists($file)) {
        $submissions = json_decode(file_get_contents($file), true) ?: [];
    }
    $submissions[] = $submission;
    return file_put_contents($file, json_encode($submissions, JSON_PRETTY_PRINT));
}
?>
