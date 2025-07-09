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
    SELECT c.*, co.title as course_title 
    FROM certificates c 
    JOIN courses co ON c.course_id = co.id 
    WHERE c.student_id = ?
");
$stmt->execute([$student_id]);
$certificates = $stmt->fetchAll();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Certificates - EduTech Pro</title>
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
                            <h1 class="h2 fw-bold text-gradient mb-2">My Certificates 🏆</h1>
                            <p class="mb-0 opacity-75 fs-5">Your achievements and completed courses</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark fs-6">
                                <i class="fas fa-certificate me-1"></i><?php echo count($certificates); ?> Earned
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Certificates Grid -->
                <div class="row">
                    <?php if (empty($certificates)): ?>
                        <div class="col-12">
                            <div class="dashboard-card p-5 text-center empty-state">
                                <i class="fas fa-certificate fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-3">No certificates yet</h4>
                                <p class="text-muted mb-4">Complete courses to earn certificates and showcase your achievements</p>
                                <a href="browse_courses.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus me-2"></i>Browse Courses
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($certificates as $index => $cert): ?>
                            <div class="col-lg-6 mb-4 fade-in-up" style="animation-delay: <?php echo 0.2 + ($index * 0.1); ?>s;">
                                <div class="card h-100 position-relative certificate-card">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title fw-bold mb-0"><?php echo htmlspecialchars($cert['course_title']); ?></h5>
                                            <i class="fas fa-certificate fa-2x text-warning"></i>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                Issued: <?php echo date('M d, Y', strtotime($cert['issued_at'])); ?>
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                Certificate ID: <?php echo $cert['id']; ?>
                                            </small>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="download_certificate.php?course_id=<?php echo $cert['course_id']; ?>" class="btn btn-success btn-sm flex-fill">
                                                <i class="fas fa-file-pdf me-1"></i>Download PDF
                                            </a>
                                            <button class="btn btn-outline-primary btn-sm" onclick="viewCertificate(<?php echo $cert['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
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
        function viewCertificate(certId) {
            // You can implement certificate preview functionality here
            alert('Certificate preview feature coming soon!');
        }

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