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
                        <div>
                            <i class="fas fa-graduation-cap fa-4x text-warning"></i>
                        </div>
                        <h6 class="text-white"><?php echo htmlspecialchars($student['full_name']); ?></h6>
                        <small class="text-muted">Student</small>
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
                <div class="dashboard-hero mb-4 p-4 rounded-4 shadow-lg d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff;">
                    <div>
                        <h1 class="h2 fw-bold mb-2">Welcome back, <?php echo htmlspecialchars($student['full_name']); ?>!</h1>
                        <p class="mb-0 opacity-75">Empowering your learning journey with premium courses and interactive features.</p>
                    </div>
                    <div class="text-end">
                        <span class="btn btn-light btn-lg fw-bold shadow-sm">
                            <i class="fas fa-coins me-2 text-warning"></i><?php echo $bountyPoints; ?> Bounty Points
                        </span>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card shadow rounded-4 p-3 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 data-target="<?php echo $enrolledCoursesCount; ?>">0</h3>
                                    <p class="mb-0">Enrolled Courses</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-book fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card shadow rounded-4 p-3 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 data-target="<?php echo $certificatesCount; ?>">0</h3>
                                    <p class="mb-0">Certificates</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-certificate fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card shadow rounded-4 p-3 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 data-target="<?php echo $bountyPoints; ?>">0</h3>
                                    <p class="mb-0">Bounty Points</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-coins fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card shadow rounded-4 p-3 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 data-target="<?php echo $availableCoursesCount; ?>">0</h3>
                                    <p class="mb-0">Available Courses</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-plus-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Courses Section -->
                <div class="row mb-4" id="myCoursesSection">
                    <div class="col-12">
                        <div class="section-header mb-3 p-4 rounded-4 shadow-sm d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff;">
                            <span class="fs-4 fw-bold"><i class="fas fa-book me-2"></i>My Courses</span>
                        </div>
                        <div class="dashboard-card">
                            <div class="row" id="myCoursesList">
                                <?php foreach ($enrolledCourses as $course): ?>
                                    <div class="col-lg-6 mb-3 my-course-item">
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
                                                    <a href="course.php?id=<?php echo isset($course['course_id']) ? $course['course_id'] : (isset($course['id']) ? $course['id'] : ''); ?>" class="btn btn-outline-primary btn-sm">Go to Course</a>
                                                    <?php if ($course['progress'] == 100): ?>
                                                        <a href="download_certificate.php?course_id=<?php echo isset($course['course_id']) ? $course['course_id'] : (isset($course['id']) ? $course['id'] : ''); ?>" class="btn btn-success btn-sm"><i class="fas fa-certificate me-1"></i>Download Certificate</a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Browse Courses Section -->
                <div class="row mb-4" id="browseCoursesSection">
                    <div class="col-12">
                        <div class="section-header mb-3 p-4 rounded-4 shadow-sm d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff;">
                            <span class="fs-4 fw-bold"><i class="fas fa-plus me-2"></i>Browse Courses</span>
                        </div>
                        <div class="dashboard-card">
                            <div class="row" id="browseCoursesList">
                                <?php foreach ($availableCourses as $course): ?>
                                    <div class="col-lg-6 mb-3 browse-course-item">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                                                <p class="card-text small"><?php echo htmlspecialchars(substr($course['description'], 0, 100)) . '...'; ?></p>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge <?php echo $course['is_premium'] ? 'bg-warning' : 'bg-success'; ?>">
                                                        <?php echo $course['is_premium'] ? 'Premium' : 'Free'; ?>
                                                    </span>
                                                    <small class="text-muted">Instructor: <?php echo htmlspecialchars($course['instructor_name']); ?></small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold text-primary">₹<?php echo number_format($course['price'], 2); ?></span>
                                                    <a href="enroll.php?course_id=<?php echo $course['id']; ?>" class="btn btn-success btn-sm">Enroll Now</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="dashboard-card">
                            <h5 class="card-title">
                                <i class="fas fa-history me-2"></i>Recent Activities
                            </h5>
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
                                                <i class="fas fa-<?php echo $activity['type'] == 'enrollment' ? 'user-plus' : 'question-circle'; ?> me-2 text-primary"></i>
                                                <?php echo htmlspecialchars($activity['description']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($activity['course_title']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($activity['date'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="dashboard-card">
                            <h5 class="card-title">
                                <i class="fas fa-certificate me-2"></i>My Certificates
                            </h5>
                            <?php if (empty($certificates)): ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-certificate fa-2x text-muted mb-2"></i>
                                    <p class="text-muted small">No certificates yet. Complete courses to earn certificates!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($certificates as $cert): ?>
                                    <div class="border rounded p-3 mb-2">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($cert['course_title']); ?></h6>
                                        <small class="text-muted">Issued: <?php echo date('M d, Y', strtotime($cert['issued_at'])); ?></small>
                                        <div class="mt-2">
                                            <a href="certificate.php?id=<?php echo $cert['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                            <a href="download_certificate.php?course_id=<?php echo $cert['course_id']; ?>" class="btn btn-sm btn-success mt-2">
                                                <i class="fas fa-file-pdf me-1"></i>Download PDF
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
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
    </script>
</body>
</html> 