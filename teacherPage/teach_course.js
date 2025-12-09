 const API_URL = 'api.php';

        // Load courses on page load
        document.addEventListener('DOMContentLoaded', loadCourses);

        // Create Course
        document.getElementById('createCourseForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const statusDiv = document.getElementById('courseStatus');
            
            const formData = new FormData();
            formData.append('action', 'create_course');
            formData.append('course_name', document.getElementById('courseName').value);
            formData.append('course_description', document.getElementById('courseDesc').value);

            try {
                const response = await fetch(API_URL, { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    showStatus(statusDiv, 'Course created successfully!', 'success');
                    document.getElementById('createCourseForm').reset();
                    loadCourses();
                } else {
                    showStatus(statusDiv, result.message || 'Failed to create course', 'error');
                }
            } catch (error) {
                showStatus(statusDiv, 'Error: ' + error.message, 'error');
            }
        });

        // Upload Lesson
        document.getElementById('uploadLessonForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const statusDiv = document.getElementById('lessonStatus');
            const fileInput = document.getElementById('lessonFile');
            
            // Validate PDF
            if (!fileInput.files[0] || !fileInput.files[0].name.endsWith('.pdf')) {
                showStatus(statusDiv, 'Please upload a valid PDF file', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'upload_lesson');
            formData.append('course_id', document.getElementById('currentCourseId').value);
            formData.append('lesson_title', document.getElementById('lessonTitle').value);
            formData.append('lesson_description', document.getElementById('lessonDesc').value);
            formData.append('pdf_file', fileInput.files[0]);

            try {
                const response = await fetch(API_URL, { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    showStatus(statusDiv, 'Lesson uploaded successfully!', 'success');
                    document.getElementById('uploadLessonForm').reset();
                    setTimeout(() => closeUploadModal(), 1500);
                    loadCourses();
                } else {
                    showStatus(statusDiv, result.message || 'Failed to upload lesson', 'error');
                }
            } catch (error) {
                showStatus(statusDiv, 'Error: ' + error.message, 'error');
            }
        });

        async function loadCourses() {
            try {
                const response = await fetch(`${API_URL}?action=get_courses`);
                const result = await response.json();
                
                const container = document.getElementById('coursesContainer');
                
                if (!result.courses || result.courses.length === 0) {
                    container.innerHTML = `
                        <div class="no-data" style="grid-column: 1 / -1;">
                            <i class="fas fa-inbox"></i>
                            <p>No courses yet. Create one to get started!</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = result.courses.map(course => `
                    <div class="course-card">
                        <h3>${course.name}</h3>
                        <p>${course.description || 'No description'}</p>
                        <div class="course-meta">
                            <small><i class="fas fa-calendar"></i> ${new Date(course.created_at).toLocaleDateString()}</small>
                        </div>
                        <div class="course-actions">
                            <button class="btn btn-primary" onclick="openUploadModal('${course.id}')">
                                <i class="fas fa-plus"></i> Add Lesson
                            </button>
                            <button class="btn btn-secondary" onclick="viewLessons('${course.id}', '${course.name}')">
                                <i class="fas fa-list"></i> View Lessons
                            </button>
                            <button class="btn btn-secondary" onclick="openEnrollModal('${course.id}')">
                                <i class="fas fa-user-plus"></i> Enroll Student
                            </button>
                               <button class="btn btn-secondary" onclick="openRegisteredStudents('${course.id}')">
                                   <i class="fas fa-user-check"></i> Pick Student
                               </button>
                            <button class="btn btn-secondary" onclick="viewStudents('${course.id}')">
                                <i class="fas fa-users"></i> View Students
                            </button>
                        </div>
                        <div id="lessons-${course.id}" class="lessons-list" style="margin-top: 1rem; display: none;"></div>
                        <div id="students-${course.id}" class="students-list" style="margin-top: 1rem; display: none;"></div>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading courses:', error);
            }
        }

        async function viewLessons(courseId, courseName) {
            const lessonsDiv = document.getElementById(`lessons-${courseId}`);
            
            if (lessonsDiv.style.display === 'block') {
                lessonsDiv.style.display = 'none';
                return;
            }

            try {
                const response = await fetch(`${API_URL}?action=get_lessons&course_id=${courseId}`);
                const result = await response.json();
                
                if (!result.lessons || result.lessons.length === 0) {
                    lessonsDiv.innerHTML = '<p class="no-data">No lessons uploaded yet</p>';
                } else {
                    lessonsDiv.innerHTML = result.lessons.map(lesson => `
                        <div class="lesson-item">
                            <div class="lesson-info">
                                <h4>${lesson.title}</h4>
                                <p>${lesson.description || 'No description'}</p>
                                <small style="color: #999;"><i class="fas fa-file-pdf"></i> ${(lesson.file_size / 1024 / 1024).toFixed(2)}MB | ${new Date(lesson.created_at).toLocaleDateString()}</small>
                            </div>
                            <div class="lesson-actions">
                                <button class="btn btn-danger" onclick="deleteLesson('${lesson.id}', '${courseId}')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    `).join('');
                }
                lessonsDiv.style.display = 'block';
            } catch (error) {
                lessonsDiv.innerHTML = '<p class="no-data">Error loading lessons</p>';
                lessonsDiv.style.display = 'block';
            }
        }

        async function deleteLesson(lessonId, courseId) {
            if (!confirm('Are you sure you want to delete this lesson?')) return;

            const formData = new FormData();
            formData.append('action', 'delete_lesson');
            formData.append('lesson_id', lessonId);

            try {
                const response = await fetch(API_URL, { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    viewLessons(courseId, '');
                } else {
                    alert(result.message || 'Failed to delete lesson');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        function openUploadModal(courseId) {
            document.getElementById('currentCourseId').value = courseId;
            document.getElementById('uploadLessonModal').classList.add('show');
        }

        function closeUploadModal() {
            document.getElementById('uploadLessonModal').classList.remove('show');
            document.getElementById('uploadLessonForm').reset();
            document.getElementById('lessonStatus').classList.remove('show');
        }

        function showStatus(element, message, type) {
            element.textContent = message;
            element.className = 'status-msg show ' + type;
            if (type === 'success') {
                setTimeout(() => element.classList.remove('show'), 3000);
            }
        }

        // Close modal when clicking outside
        document.getElementById('uploadLessonModal').addEventListener('click', (e) => {
            if (e.target.id === 'uploadLessonModal') closeUploadModal();
        });

        document.getElementById('enrollStudentModal').addEventListener('click', (e) => {
            if (e.target.id === 'enrollStudentModal') closeEnrollModal();
        });

        // Enroll Student Form
        document.getElementById('enrollStudentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const statusDiv = document.getElementById('enrollStatus');
            
            const formData = new FormData();
            formData.append('action', 'enroll_student');
            formData.append('course_id', document.getElementById('enrollCourseId').value);
            formData.append('student_email', document.getElementById('studentEmail').value);

            try {
                const response = await fetch(API_URL, { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    showStatus(statusDiv, 'Student enrolled successfully!', 'success');
                    document.getElementById('enrollStudentForm').reset();
                    setTimeout(() => closeEnrollModal(), 1500);
                    loadCourses();
                } else {
                    showStatus(statusDiv, result.message || 'Failed to enroll student', 'error');
                }
            } catch (error) {
                showStatus(statusDiv, 'Error: ' + error.message, 'error');
            }
        });

        // Open Registered Students modal (pick from list)
        async function openRegisteredStudents(courseId) {
            const modal = document.getElementById('registeredStudentsModal');
            const list = document.getElementById('registeredStudentsList');
            const status = document.getElementById('registeredStudentsStatus');
            list.innerHTML = '<p class="loading">Loading students...</p>';
            modal.classList.add('show');

            try {
                const response = await fetch(`${API_URL}?action=get_registered_students`);
                const result = await response.json();
                if (!result.students || result.students.length === 0) {
                    list.innerHTML = '<p style="color:#999; text-align:center;">No registered students found</p>';
                    return;
                }

                list.innerHTML = result.students.map(s => `
                    <div class="student-item">
                        <div>
                            <div class="student-item-name"><i class="fas fa-user-circle"></i> ${s.name || '—'}</div>
                            <div class="student-item-email">${s.email}</div>
                        </div>
                        <div class="student-item-actions">
                            <button class="btn btn-primary" onclick="enrollFromList('${courseId}', '${s.email.replace("'", "\'")}')">
                                <i class="fas fa-user-plus"></i> Enroll
                            </button>
                        </div>
                    </div>
                `).join('');
            } catch (err) {
                list.innerHTML = '<p style="color:#999; text-align:center;">Error loading students</p>';
            }
        }

        async function enrollFromList(courseId, studentEmail) {
            const status = document.getElementById('registeredStudentsStatus');
            const formData = new FormData();
            formData.append('action', 'enroll_student');
            formData.append('course_id', courseId);
            formData.append('student_email', studentEmail);

            try {
                const response = await fetch(API_URL, { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    showStatus(status, 'Student enrolled successfully!', 'success');
                    loadCourses();
                } else {
                    showStatus(status, result.message || 'Failed to enroll student', 'error');
                }
            } catch (err) {
                showStatus(status, 'Error: ' + err.message, 'error');
            }
        }

        async function viewStudents(courseId) {
            const studentsDiv = document.getElementById(`students-${courseId}`);
            
            if (studentsDiv.style.display === 'block') {
                studentsDiv.style.display = 'none';
                return;
            }

            try {
                const response = await fetch(`${API_URL}?action=get_enrolled_students&course_id=${courseId}`);
                const result = await response.json();
                
                if (!result.students || result.students.length === 0) {
                    studentsDiv.innerHTML = '<p style="color: #999; text-align: center;">No students enrolled yet</p>';
                } else {
                    studentsDiv.innerHTML = '<h4><i class="fas fa-users"></i> Enrolled Students (' + result.students.length + ')</h4>' +
                        result.students.map(email => `
                            <div class="student-item">
                                <div>
                                    <div class="student-item-name"><i class="fas fa-user-circle"></i> ${email}</div>
                                </div>
                                <div class="student-item-actions">
                                    <button class="btn btn-danger" onclick="removeStudent('${courseId}', '${email}')">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        `).join('');
                }
                studentsDiv.style.display = 'block';
            } catch (error) {
                studentsDiv.innerHTML = '<p style="color: #999;">Error loading students</p>';
                studentsDiv.style.display = 'block';
            }
        }

        async function removeStudent(courseId, studentEmail) {
            if (!confirm('Remove this student from the course?')) return;

            const formData = new FormData();
            formData.append('action', 'remove_student');
            formData.append('course_id', courseId);
            formData.append('student_email', studentEmail);

            try {
                const response = await fetch(API_URL, { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    viewStudents(courseId);
                } else {
                    alert(result.message || 'Failed to remove student');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        function openEnrollModal(courseId) {
            document.getElementById('enrollCourseId').value = courseId;
            document.getElementById('enrollStudentModal').classList.add('show');
        }

        function closeEnrollModal() {
            document.getElementById('enrollStudentModal').classList.remove('show');
            document.getElementById('enrollStudentForm').reset();
            document.getElementById('enrollStatus').classList.remove('show');
        }

        function closeRegisteredStudentsModal() {
            const modal = document.getElementById('registeredStudentsModal');
            modal.classList.remove('show');
            document.getElementById('registeredStudentsList').innerHTML = '';
            document.getElementById('registeredStudentsStatus').classList.remove('show');
        }

        // Close registered students modal when clicking outside
        document.getElementById('registeredStudentsModal').addEventListener('click', (e) => {
            if (e.target.id === 'registeredStudentsModal') closeRegisteredStudentsModal();
        });