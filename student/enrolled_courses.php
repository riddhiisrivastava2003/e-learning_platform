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
                            <h1 class="h2 fw-bold text-gradient mb-2">My Enrolled Courses 📚</h1>
                            <p class="mb-0 opacity-75 fs-5">Continue your learning journey with these enrolled courses</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark fs-6">
                                <i class="fas fa-book me-1"></i><?php echo count($enrolledCourses); ?> Courses
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Courses Grid -->
                <div class="row">
                    <?php if (empty($enrolledCourses)): ?>
                        <div class="col-12">
                            <div class="dashboard-card p-5 text-center empty-state">
                                <i class="fas fa-book-open fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-3">No courses enrolled yet</h4>
                                <p class="text-muted mb-4">Start your learning journey by browsing and enrolling in courses</p>
                                <a href="browse_courses.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus me-2"></i>Browse Courses
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($enrolledCourses as $course): ?>
                            <div class="col-lg-6 mb-4 fade-in-up">
                                <div class="card h-100 position-relative">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title fw-bold mb-0"><?php echo htmlspecialchars($course['title']); ?></h5>
                                            <span class="badge <?php echo $course['is_premium'] ? 'bg-warning' : 'bg-success'; ?>">
                                                <i class="fas fa-<?php echo $course['is_premium'] ? 'crown' : 'check-circle'; ?> me-1"></i>
                                                <?php echo $course['is_premium'] ? 'Premium' : 'Free'; ?>
                                            </span>
                                        </div>
                                        
                                        <p class="card-text text-muted mb-3"><?php echo htmlspecialchars(substr($course['description'], 0, 120)) . '...'; ?></p>
                                        
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted fw-semibold">Progress</small>
                                                <small class="text-muted"><?php echo $course['progress']; ?>%</small>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar" role="progressbar" style="width: <?php echo $course['progress']; ?>%" aria-valuenow="<?php echo $course['progress']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted">
                                                    <i class="fas fa-user-tie me-1"></i>
                                                    <?php echo htmlspecialchars($course['instructor_name']); ?>
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    Enrolled: <?php echo date('M d, Y', strtotime($course['enrolled_at'])); ?>
                                                </small>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="course.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-play me-1"></i>Continue
                                                </a>
                                                <?php if ($course['progress'] == 100): ?>
                                                    <a href="download_certificate.php?course_id=<?php echo $course['id']; ?>" class="btn btn-success btn-sm">
                                                        <i class="fas fa-certificate me-1"></i>Certificate
                                                    </a>
                                                <?php endif; ?>
                                            </div>
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