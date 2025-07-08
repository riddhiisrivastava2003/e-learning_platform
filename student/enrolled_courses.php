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
$stmt = $pdo->prepare("
    SELECT e.*, c.*, u.full_name as instructor_name 
    FROM enrollments e 
    JOIN courses c ON e.course_id = c.id 
    LEFT JOIN users u ON c.instructor_id = u.id 
    WHERE e.student_id = ? 
    ORDER BY e.enrolled_at DESC
");
$stmt->execute([$student_id]);
$enrolledCourses = $stmt->fetchAll();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Enrolled Courses - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2 class="h3">My Enrolled Courses</h2>
                </div>
                <div class="row">
                    <?php if (empty($enrolledCourses)): ?>
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No courses enrolled yet</h5>
                        </div>
                    <?php else: ?>
                        <?php foreach ($enrolledCourses as $course): ?>
                            <div class="col-lg-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                                        <p class="card-text small"><?php echo htmlspecialchars(substr($course['description'], 0, 100)) . '...'; ?></p>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge <?php echo $course['is_premium'] ? 'bg-warning' : 'bg-success'; ?>">
                                                <?php echo $course['is_premium'] ? 'Premium' : 'Free'; ?>
                                            </span>
                                            <small class="text-muted">Progress: <?php echo $course['progress']; ?>%</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="course.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-primary btn-sm">Go to Course</a>
                                            <?php if ($course['progress'] == 100): ?>
                                                <a href="download_certificate.php?course_id=<?php echo $course['id']; ?>" class="btn btn-success btn-sm"><i class="fas fa-certificate me-1"></i>Download Certificate</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 