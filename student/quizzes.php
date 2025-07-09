<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}
$student_id = $_SESSION['student_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzes - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/student-dashboard.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-graduation-cap me-2"></i>EduTech Pro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-light fw-semibold">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($student['full_name']); ?>
                        </span>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid" style="margin-top: 76px;">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky">
                    <div class="text-center mb-4">
                        <div class="student-avatar mb-3">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['full_name']); ?>&background=667eea&color=fff&size=80&font-size=0.4" class="rounded-circle shadow-lg" alt="Avatar">
                        </div>
                        <h5 class="text-white fw-bold mb-1"><?php echo htmlspecialchars($student['full_name']); ?></h5>
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-graduation-cap me-1"></i>Student
                        </span>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="enrolled_courses.php">
                                <i class="fas fa-book me-2"></i>
                                My Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="browse_courses.php">
                                <i class="fas fa-plus me-2"></i>
                                Browse Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="certificates.php">
                                <i class="fas fa-certificate me-2"></i>
                                Certificates
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="quizzes.php">
                                <i class="fas fa-question-circle me-2"></i>
                                Quizzes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="live_classes.php">
                                <i class="fas fa-video me-2"></i>
                                Live Classes
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
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 dashboard-main-content">
                <!-- Page Header -->
                <div class="dashboard-hero mb-4 p-4 rounded-4 shadow-lg fade-in-up">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h2 fw-bold text-gradient mb-2">Quizzes 📝</h1>
                            <p class="mb-0 opacity-75 fs-5">Test your knowledge and track your progress</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark fs-6">
                                <i class="fas fa-clock me-1"></i>Coming Soon
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quiz Content -->
                <div class="row">
                    <div class="col-12">
                        <div class="dashboard-card p-5 text-center empty-state">
                            <div class="quiz-coming-soon mb-4">
                                <i class="fas fa-question-circle fa-4x text-primary mb-4"></i>
                                <div class="quiz-animation">
                                    <div class="quiz-icon">
                                        <i class="fas fa-pencil-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <h4 class="text-muted mb-3">Quizzes Coming Soon!</h4>
                            <p class="text-muted mb-4">We're working hard to bring you interactive quizzes to test your knowledge and track your learning progress.</p>
                            <div class="row justify-content-center">
                                <div class="col-md-4 mb-3">
                                    <div class="feature-card p-3">
                                        <i class="fas fa-brain fa-2x text-primary mb-2"></i>
                                        <h6>Knowledge Testing</h6>
                                        <small class="text-muted">Test your understanding of course materials</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="feature-card p-3">
                                        <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                                        <h6>Progress Tracking</h6>
                                        <small class="text-muted">Monitor your learning progress</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="feature-card p-3">
                                        <i class="fas fa-trophy fa-2x text-warning mb-2"></i>
                                        <h6>Certificates</h6>
                                        <small class="text-muted">Earn certificates upon completion</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="dashboard.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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