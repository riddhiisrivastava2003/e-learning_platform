<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['teacher_id'])) {
    header('Location: login.php');
    exit();
}
$teacher_id = $_SESSION['teacher_id'];

// Fetch teacher info for sidebar
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch();

// Get all students enrolled in teacher's courses
$stmt = $pdo->prepare('
    SELECT DISTINCT u.id, u.full_name, u.email
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    JOIN users u ON e.student_id = u.id
    WHERE c.instructor_id = ?
    ORDER BY u.full_name ASC
');
$stmt->execute([$teacher_id]);
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Students - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/teacher-dashboard.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-chalkboard-teacher me-2"></i>EduTech Pro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="position-sticky">
                <div class="text-center mb-4">
                    <div>
                        <i class="fas fa-chalkboard-teacher fa-4x text-warning"></i>
                    </div>
                    <h6 class="text-white"><?php echo htmlspecialchars($teacher['full_name']); ?></h6>
                    <small class="text-muted">Teacher</small>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="courses.php">
                            <i class="fas fa-book me-2"></i>
                            My Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create_course.php">
                            <i class="fas fa-plus-circle me-2"></i>
                            Add Course
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="students.php">
                            <i class="fas fa-users me-2"></i>
                            My Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="lessons_coming_soon.php">
                            <i class="fas fa-video me-2"></i>
                            Lessons <span class="badge bg-secondary ms-2"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="quizzes_coming_soon.php">
                            <i class="fas fa-question-circle me-2"></i>
                            Quizzes <span class="badge bg-secondary ms-2"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="live_classes_coming_soon.php">
                            <i class="fas fa-video me-2"></i>
                            Live Classes <span class="badge bg-secondary ms-2"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="attendance.php">
                            <i class="fas fa-calendar-check me-2"></i>
                            Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user-cog me-2"></i>
                            Profile
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 dashboard-main-content">
            <div class="container-fluid pt-5">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1><i class="fas fa-users me-3"></i>My Students</h1>
                            <p class="mb-0">View and manage students enrolled in your courses</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="teacher-stat-card">
                                <i class="fas fa-user-graduate fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="student-stats">
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                            <i class="fas fa-users me-2"></i><?php echo count($students); ?> Students
                        </span>
                    </div>
                </div>
                
                <div class="teacher-content-card">
                    <?php if (empty($students)): ?>
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-user-graduate fa-4x text-muted mb-4"></i>
                                <h3 class="text-muted mb-3">No Students Yet</h3>
                                <p class="text-muted mb-4">Students will appear here once they enroll in your courses</p>
                                <a href="courses.php" class="btn btn-warning btn-lg teacher-btn">
                                    <i class="fas fa-book me-2"></i>View My Courses
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($students as $student): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="student-card">
                                    <div class="student-avatar">
                                        <i class="fas fa-user-graduate fa-2x text-warning"></i>
                                    </div>
                                    <div class="student-info">
                                        <h5 class="student-name"><?php echo htmlspecialchars($student['full_name']); ?></h5>
                                        <p class="student-email"><?php echo htmlspecialchars($student['email']); ?></p>
                                        <div class="student-status">
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Enrolled
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add hover effects to student cards
        document.querySelectorAll('.student-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.2)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.1)';
            });
        });

        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggler = document.querySelector('.navbar-toggler');
            const sidebar = document.querySelector('.sidebar');
            
            if (navbarToggler) {
                navbarToggler.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(event.target) && !navbarToggler.contains(event.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });
        });
    </script>
</body>
</html> 