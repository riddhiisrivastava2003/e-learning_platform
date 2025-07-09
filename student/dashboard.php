<?php
session_start();
require_once '../config/database.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$student_id = $_SESSION['student_id'];

// Get student info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

// Get enrolled courses (limited to 100 for performance)
$stmt = $pdo->prepare("
    SELECT e.course_id, c.title, c.description, c.is_premium, e.progress, u.full_name as instructor_name 
    FROM enrollments e 
    JOIN courses c ON e.course_id = c.id 
    LEFT JOIN users u ON c.instructor_id = u.id 
    WHERE e.student_id = ? 
    ORDER BY e.enrolled_at DESC
    LIMIT 100
");
$stmt->execute([$student_id]);
$enrolledCourses = $stmt->fetchAll();
$enrolledCoursesCount = count($enrolledCourses);

// Get available courses (limited to 150 for performance)
$stmt = $pdo->prepare("
    SELECT c.id, c.title, c.description, c.is_premium, c.price, u.full_name as instructor_name 
    FROM courses c 
    LEFT JOIN users u ON c.instructor_id = u.id 
    WHERE c.id NOT IN (SELECT course_id FROM enrollments WHERE student_id = ?)
    ORDER BY c.created_at DESC
    LIMIT 150
");
$stmt->execute([$student_id]);
$availableCourses = $stmt->fetchAll();
$availableCoursesCount = count($availableCourses);

// Get bounty points
$stmt = $pdo->prepare("SELECT SUM(points) as total_points FROM bounty_points WHERE student_id = ?");
$stmt->execute([$student_id]);
$bountyPoints = $stmt->fetch()['total_points'] ?? 0;

// Get certificates (limited to 50 for performance)
$stmt = $pdo->prepare("
    SELECT c.id, c.course_id, c.issued_at, co.title as course_title 
    FROM certificates c 
    JOIN courses co ON c.course_id = co.id 
    WHERE c.student_id = ?
    ORDER BY c.issued_at DESC
    LIMIT 50
");
$stmt->execute([$student_id]);
$certificates = $stmt->fetchAll();
$certificatesCount = count($certificates);

// Get recent activities (limited to 5 for performance)
$stmt = $pdo->prepare("
    SELECT 'enrollment' as type, e.enrolled_at as date, co.title as course_title, 'Enrolled in course' as description
    FROM enrollments e 
    JOIN courses co ON e.course_id = co.id 
    WHERE e.student_id = ?
    UNION ALL
    SELECT 'quiz' as type, qa.attempted_at as date, co.title as course_title, CONCAT('Completed quiz with ', qa.score, '% score') as description
    FROM quiz_attempts qa 
    JOIN quizzes q ON qa.quiz_id = q.id 
    JOIN courses co ON q.course_id = co.id 
    WHERE qa.student_id = ?
    ORDER BY date DESC 
    LIMIT 5
");
$stmt->execute([$student_id, $student_id]);
$recentActivities = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/student-dashboard.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Simple loading indicator */
        .loading-indicator {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            z-index: 9999;
            animation: loading 1s ease-in-out infinite;
        }
        
        @keyframes loading {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        /* Hide loading indicator when page is ready */
        .page-loaded .loading-indicator {
            display: none;
        }
    </style>
</head>
<body>
    <div class="loading-indicator"></div>
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
    <div class="container-fluid" style="margin-top: 76px;">
        <div class="row">
            <!-- Sidebar -->
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
                            <a class="nav-link active" href="dashboard.php">
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

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 dashboard-main-content">
                <!-- Welcome Section -->
                <div class="dashboard-hero mb-4 p-4 rounded-4 shadow-lg d-flex justify-content-between align-items-center fade-in-up">
                    <div>
                        <h1 class="h2 fw-bold mb-2 text-gradient">Welcome back, <?php echo htmlspecialchars($student['full_name']); ?>! 👋</h1>
                        <p class="mb-0 opacity-75 fs-5">Empowering your learning journey with premium courses and interactive features.</p>
                        <div class="mt-3">
                            <span class="badge bg-light text-dark me-2">
                                <i class="fas fa-calendar-check me-1"></i><?php echo date('l, F j'); ?>
                            </span>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-clock me-1"></i><?php echo date('g:i A'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="bounty-points-card bg-light rounded-4 p-3 shadow-sm">
                            <div class="d-flex align-items-center">
                                <div class="bounty-icon me-3">
                                    <i class="fas fa-coins fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 fw-bold text-dark"><?php echo number_format($bountyPoints); ?></h4>
                                    <small class="text-muted">Bounty Points</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.1s;">
                        <div class="stats-card shadow rounded-4 p-4 text-white" style="background: var(--primary-gradient);">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 data-target="<?php echo $enrolledCoursesCount; ?>" class="mb-1">0</h3>
                                    <p class="mb-0 fw-semibold">Enrolled Courses</p>
                                    <small class="opacity-75">Active learning</small>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-book fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.2s;">
                        <div class="stats-card shadow rounded-4 p-4 text-white" style="background: var(--success-gradient);">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 data-target="<?php echo $certificatesCount; ?>" class="mb-1">0</h3>
                                    <p class="mb-0 fw-semibold">Certificates</p>
                                    <small class="opacity-75">Achievements earned</small>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-certificate fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.3s;">
                        <div class="stats-card shadow rounded-4 p-4 text-white" style="background: var(--warning-gradient);">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 data-target="<?php echo $bountyPoints; ?>" class="mb-1">0</h3>
                                    <p class="mb-0 fw-semibold">Bounty Points</p>
                                    <small class="opacity-75">Rewards collected</small>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-coins fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.4s;">
                        <div class="stats-card shadow rounded-4 p-4 text-white" style="background: var(--secondary-gradient);">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 data-target="<?php echo $availableCoursesCount; ?>" class="mb-1">0</h3>
                                    <p class="mb-0 fw-semibold">Available Courses</p>
                                    <small class="opacity-75">Ready to explore</small>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-plus-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Courses Section -->
                <div class="row mb-4" id="myCoursesSection">
                    <div class="col-12 fade-in-up" style="animation-delay: 0.5s;">
                        <div class="section-header mb-4 p-4 rounded-4 shadow-sm d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fs-4 fw-bold text-white"><i class="fas fa-book me-2"></i>My Courses</span>
                                <p class="mb-0 text-white opacity-75">Continue your learning journey</p>
                            </div>
                            <a href="enrolled_courses.php" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-right me-1"></i>View All
                            </a>
                        </div>
                        <div class="dashboard-card p-4">
                            <?php if (empty($enrolledCourses)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No courses enrolled yet</h5>
                                    <p class="text-muted">Start your learning journey by browsing available courses</p>
                                    <a href="browse_courses.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Browse Courses
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="row" id="myCoursesList">
                                    <?php foreach ($enrolledCourses as $course): ?>
                                        <div class="col-lg-6 mb-4 my-course-item">
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
                                                        <small class="text-muted">
                                                            <i class="fas fa-user-tie me-1"></i>
                                                            <?php echo htmlspecialchars($course['instructor_name']); ?>
                                                        </small>
                                                        <div>
                                                            <a href="course.php?id=<?php echo isset($course['course_id']) ? $course['course_id'] : (isset($course['id']) ? $course['id'] : ''); ?>" class="btn btn-primary btn-sm">
                                                                <i class="fas fa-play me-1"></i>Continue
                                                            </a>
                                                            <?php if ($course['progress'] == 100): ?>
                                                                <a href="download_certificate.php?course_id=<?php echo isset($course['course_id']) ? $course['course_id'] : (isset($course['id']) ? $course['id'] : ''); ?>" class="btn btn-success btn-sm ms-2">
                                                                    <i class="fas fa-certificate me-1"></i>Certificate
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Browse Courses Section -->
                <div class="row mb-4" id="browseCoursesSection">
                    <div class="col-12 fade-in-up" style="animation-delay: 0.6s;">
                        <div class="section-header mb-4 p-4 rounded-4 shadow-sm d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fs-4 fw-bold text-white"><i class="fas fa-plus me-2"></i>Browse Courses</span>
                                <p class="mb-0 text-white opacity-75">Discover new learning opportunities</p>
                            </div>
                            <a href="browse_courses.php" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-right me-1"></i>View All
                            </a>
                        </div>
                        <div class="dashboard-card p-4">
                            <?php if (empty($availableCourses)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No courses available</h5>
                                    <p class="text-muted">Check back later for new courses</p>
                                </div>
                            <?php else: ?>
                                <div class="row" id="browseCoursesList">
                                    <?php foreach (array_slice($availableCourses, 0, 4) as $course): ?>
                                        <div class="col-lg-6 mb-4 browse-course-item">
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
                                                        <small class="text-muted">
                                                            <i class="fas fa-user-tie me-1"></i>
                                                            <?php echo htmlspecialchars($course['instructor_name']); ?>
                                                        </small>
                                                    </div>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="price-tag">
                                                            <span class="fw-bold fs-5 text-primary">₹<?php echo number_format($course['price'], 2); ?></span>
                                                            <?php if ($course['is_premium']): ?>
                                                                <small class="text-muted d-block">Premium Course</small>
                                                            <?php else: ?>
                                                                <small class="text-success d-block">Free Course</small>
                                                            <?php endif; ?>
                                                        </div>
                                                        <a href="enroll.php?course_id=<?php echo $course['id']; ?>" class="btn btn-success btn-sm">
                                                            <i class="fas fa-plus me-1"></i>Enroll Now
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities & Certificates -->
                <div class="row">
                    <div class="col-lg-8 fade-in-up" style="animation-delay: 0.7s;">
                        <div class="dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-history me-2 text-primary"></i>Recent Activities
                                </h5>
                                <span class="badge bg-primary"><?php echo count($recentActivities); ?> activities</span>
                            </div>
                            <?php if (empty($recentActivities)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">No recent activities</h6>
                                    <p class="text-muted small">Start learning to see your activity history</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Activity</th>
                                                <th>Course</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentActivities as $activity): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="activity-icon me-3">
                                                            <i class="fas fa-<?php echo $activity['type'] == 'enrollment' ? 'user-plus' : 'question-circle'; ?> text-primary"></i>
                                                        </div>
                                                        <span class="fw-medium"><?php echo htmlspecialchars($activity['description']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="fw-medium"><?php echo htmlspecialchars($activity['course_title']); ?></td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        <?php echo date('M d, Y', strtotime($activity['date'])); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-4 fade-in-up" style="animation-delay: 0.8s;">
                        <div class="dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-certificate me-2 text-warning"></i>My Certificates
                                </h5>
                                <span class="badge bg-warning"><?php echo count($certificates); ?> earned</span>
                            </div>
                            <?php if (empty($certificates)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">No certificates yet</h6>
                                    <p class="text-muted small">Complete courses to earn certificates!</p>
                                    <a href="browse_courses.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Start Learning
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="certificate-section">
                                    <?php foreach (array_slice($certificates, 0, 3) as $cert): ?>
                                        <div class="certificate-item">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($cert['course_title']); ?></h6>
                                                <i class="fas fa-certificate text-warning"></i>
                                            </div>
                                            <small class="text-muted d-block mb-3">
                                                <i class="fas fa-calendar me-1"></i>
                                                Issued: <?php echo date('M d, Y', strtotime($cert['issued_at'])); ?>
                                            </small>
                                            <div class="d-flex gap-2">
                                                <a href="certificate.php?id=<?php echo $cert['id']; ?>" class="btn btn-outline-primary btn-sm flex-fill">
                                                    <i class="fas fa-download me-1"></i>View
                                                </a>
                                                <a href="download_certificate.php?course_id=<?php echo $cert['course_id']; ?>" class="btn btn-success btn-sm">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (count($certificates) > 3): ?>
                                        <div class="text-center mt-3">
                                            <a href="certificates.php" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-arrow-right me-1"></i>View All Certificates
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    
    <script>
        // Simple and fast counter animation
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading indicator
            document.body.classList.add('page-loaded');
            
            const counters = document.querySelectorAll('[data-target]');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                let current = 0;
                const step = Math.max(1, Math.floor(target / 20)); // Faster animation
                
                const updateCounter = () => {
                    if (current < target) {
                        current = Math.min(current + step, target);
                        counter.textContent = current;
                        if (current < target) {
                            requestAnimationFrame(updateCounter);
                        }
                    }
                };
                
                // Start animation after a short delay
                setTimeout(updateCounter, 100);
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