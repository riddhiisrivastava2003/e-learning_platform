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
    <title>Lessons - Coming Soon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
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
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 dashboard-main-content d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="text-center">
            <i class="fas fa-tools fa-4x text-warning mb-3"></i>
            <h2 class="fw-bold">Lessons - Coming Soon</h2>
            <p class="lead">This feature is under development. Stay tuned!</p>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 