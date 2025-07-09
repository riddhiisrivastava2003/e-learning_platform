<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

$teacher_id = $_SESSION['teacher_id'];

// Fetch teacher info for sidebar
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzes - Coming Soon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                        <h1><i class="fas fa-question-circle me-3"></i>Quiz Management</h1>
                        <p class="mb-0">Create and manage interactive quizzes for your students</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="teacher-stat-card">
                            <i class="fas fa-clipboard-check fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Coming Soon Content -->
            <div class="teacher-content-card">
                <div class="text-center py-5">
                    <div class="coming-soon-content">
                        <div class="coming-soon-icon mb-4">
                            <i class="fas fa-question-circle fa-6x text-warning"></i>
                        </div>
                        <h2 class="coming-soon-title mb-4">Quiz System Coming Soon!</h2>
                        <p class="coming-soon-description mb-5">
                            We're developing a comprehensive quiz system that will help you assess 
                            student understanding and track their progress effectively.
                        </p>
                        
                        <div class="row mb-5">
                            <div class="col-md-4 mb-4">
                                <div class="feature-card">
                                    <div class="feature-icon">
                                        <i class="fas fa-edit fa-2x text-warning"></i>
                                    </div>
                                    <h4>Quiz Creation</h4>
                                    <p>Create multiple choice, true/false, and essay questions</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="feature-card">
                                    <div class="feature-icon">
                                        <i class="fas fa-clock fa-2x text-warning"></i>
                                    </div>
                                    <h4>Timed Assessments</h4>
                                    <p>Set time limits and auto-submit functionality</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="feature-card">
                                    <div class="feature-icon">
                                        <i class="fas fa-chart-bar fa-2x text-warning"></i>
                                    </div>
                                    <h4>Analytics</h4>
                                    <p>Detailed performance analytics and reports</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="coming-soon-actions">
                            <a href="dashboard.php" class="btn btn-warning btn-lg teacher-btn me-3">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                            <a href="courses.php" class="btn btn-outline-warning btn-lg teacher-btn">
                                <i class="fas fa-book me-2"></i>View My Courses
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add hover effects to feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
                this.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.2)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
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