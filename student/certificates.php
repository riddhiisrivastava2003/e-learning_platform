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
                    <h2 class="h3">My Certificates</h2>
                </div>
                <div class="row">
                    <?php if (empty($certificates)): ?>
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No certificates yet. Complete courses to earn certificates!</h5>
                        </div>
                    <?php else: ?>
                        <?php foreach ($certificates as $cert): ?>
                            <div class="col-lg-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($cert['course_title']); ?></h5>
                                        <p class="card-text small">Issued: <?php echo date('M d, Y', strtotime($cert['issued_at'])); ?></p>
                                        <a href="download_certificate.php?course_id=<?php echo $cert['course_id']; ?>" class="btn btn-success btn-sm"><i class="fas fa-file-pdf me-1"></i>Download PDF</a>
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