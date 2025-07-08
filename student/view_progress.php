<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}
$student_id = $_SESSION['student_id'];
$course_id = $_GET['course_id'] ?? null;
if (!$course_id) {
    header('Location: dashboard.php');
    exit();
}
$stmt = $pdo->prepare("SELECT c.title, e.progress FROM courses c LEFT JOIN enrollments e ON c.id = e.course_id AND e.student_id = ? WHERE c.id = ?");
$stmt->execute([$student_id, $course_id]);
$info = $stmt->fetch();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Progress - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
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
                        <div>
                            <i class="fas fa-graduation-cap fa-4x text-warning"></i>
                        </div>
                        <h6 class="text-white"><?php echo htmlspecialchars($student['full_name']); ?></h6>
                        <small class="text-muted">Student</small>
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
                <div class="card shadow-lg mt-4">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>Course Progress</h4>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="mb-3"><?php echo htmlspecialchars($info['title'] ?? 'Course'); ?></h5>
                        <?php if (empty($info['progress']) || $info['progress'] == 0): ?>
                            <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                            <h5 class="text-muted">No progress made till now.</h5>
                        <?php else: ?>
                            <div class="progress mb-3" style="height: 30px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: <?php echo $info['progress']; ?>%">
                                    <?php echo $info['progress']; ?>%
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 