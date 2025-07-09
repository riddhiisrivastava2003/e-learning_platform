<?php
session_start();
require_once '../config/database.php';

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header('Location: login.php');
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Get teacher info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch();

// Get teacher's courses
$stmt = $pdo->prepare("
    SELECT c.*, COUNT(e.id) as enrollment_count 
    FROM courses c 
    LEFT JOIN enrollments e ON c.id = e.course_id 
    WHERE c.instructor_id = ? 
    GROUP BY c.id 
    ORDER BY c.created_at DESC
");
$stmt->execute([$teacher_id]);
$courses = $stmt->fetchAll();

// Get total students
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT e.student_id) as total_students 
    FROM enrollments e 
    JOIN courses c ON e.course_id = c.id 
    WHERE c.instructor_id = ?
");
$stmt->execute([$teacher_id]);
$totalStudents = $stmt->fetch()['total_students'];

// Get recent enrollments
$stmt = $pdo->prepare("
    SELECT e.*, u.full_name as student_name, c.title as course_title 
    FROM enrollments e 
    JOIN users u ON e.student_id = u.id 
    JOIN courses c ON e.course_id = c.id 
    WHERE c.instructor_id = ? 
    ORDER BY e.enrolled_at DESC 
    LIMIT 5
");
$stmt->execute([$teacher_id]);
$recentEnrollments = $stmt->fetchAll();

// Get upcoming live classes
$stmt = $pdo->prepare("
    SELECT lc.*, c.title as course_title 
    FROM live_classes lc 
    JOIN courses c ON lc.course_id = c.id 
    WHERE c.instructor_id = ? AND lc.scheduled_at > NOW() 
    ORDER BY lc.scheduled_at ASC 
    LIMIT 5
");
$stmt->execute([$teacher_id]);
$upcomingClasses = $stmt->fetchAll();

// Get quiz attempts
$stmt = $pdo->prepare("
    SELECT qa.*, q.title as quiz_title, c.title as course_title, u.full_name as student_name 
    FROM quiz_attempts qa 
    JOIN quizzes q ON qa.quiz_id = q.id 
    JOIN courses c ON q.course_id = c.id 
    JOIN users u ON qa.student_id = u.id 
    WHERE c.instructor_id = ? 
    ORDER BY qa.attempted_at DESC 
    LIMIT 5
");
$stmt->execute([$teacher_id]);
$quizAttempts = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection code...

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = "teacher" AND status = "active"');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Login success: set session, redirect
    } else {
        // Error: username/password invalid
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/text-visibility.css" rel="stylesheet">
    <link href="../assets/css/teacher-dashboard.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
    <div class="container-fluid" style="margin-top: 76px;">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky">
                    <div class="text-center mb-4">
                        <div class="teacher-avatar">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($teacher['full_name']); ?>&background=ff6b35&color=fff&size=80&font-size=0.4" class="rounded-circle w-100 h-100" alt="Teacher Avatar">
                        </div>
                        <h5 class="text-white"><?php echo htmlspecialchars($teacher['full_name']); ?></h5>
                        <span class="badge teacher-badge-primary">
                            <i class="fas fa-chalkboard-teacher me-1"></i>Teacher
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
                    <!-- Teacher Hero Section -->
                    <div class="teacher-hero mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h2 fw-bold mb-2">Welcome back, <?php echo htmlspecialchars($teacher['full_name']); ?>! 👨‍🏫</h1>
                            <p class="mb-0 fs-5">Empower your students with knowledge and track your teaching impact.</p>
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
                            <a href="create_course.php" class="btn btn-teacher-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Create New Course
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Teacher Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4 teacher-fade-in-up" style="animation-delay: 0.1s;">
                        <div class="teacher-stats-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 data-target="<?php echo count($courses); ?>">0</h3>
                                    <p class="mb-0">My Courses</p>
                                    <small class="text-muted">Active teaching</small>
                                </div>
                                <div class="teacher-stats-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4 teacher-fade-in-up" style="animation-delay: 0.2s;">
                        <div class="teacher-stats-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 data-target="<?php echo $totalStudents; ?>">0</h3>
                                    <p class="mb-0">Total Students</p>
                                    <small class="text-muted">Enrolled learners</small>
                                </div>
                                <div class="teacher-stats-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4 teacher-fade-in-up" style="animation-delay: 0.3s;">
                        <div class="teacher-stats-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 data-target="<?php echo count($upcomingClasses); ?>">0</h3>
                                    <p class="mb-0">Upcoming Classes</p>
                                    <small class="text-muted">Scheduled sessions</small>
                                </div>
                                <div class="teacher-stats-icon">
                                    <i class="fas fa-video"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4 teacher-fade-in-up" style="animation-delay: 0.4s;">
                        <div class="teacher-stats-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 data-target="<?php echo count($quizAttempts); ?>">0</h3>
                                    <p class="mb-0">Quiz Attempts</p>
                                    <small class="text-muted">Student assessments</small>
                                </div>
                                <div class="teacher-stats-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Courses Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="teacher-form-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0">
                                    <i class="fas fa-book me-2 text-warning"></i>My Courses
                                </h4>
                                <a href="create_course.php" class="btn btn-teacher-primary">
                                    <i class="fas fa-plus me-2"></i>Create Course
                                </a>
                            </div>
                            
                            <?php if (empty($courses)): ?>
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="fas fa-book-open fa-4x text-muted"></i>
                                    </div>
                                    <h4 class="text-muted mb-3">No courses created yet</h4>
                                    <p class="text-muted mb-4">Start your teaching journey by creating your first course!</p>
                                    <a href="create_course.php" class="btn btn-teacher-primary btn-lg">
                                        <i class="fas fa-plus me-2"></i>Create Your First Course
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($courses as $index => $course): ?>
                                        <div class="col-lg-4 col-md-6 mb-4 teacher-fade-in-up" style="animation-delay: <?php echo 0.1 + ($index * 0.1); ?>s;">
                                            <div class="teacher-course-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                                                        <span class="badge <?php echo $course['is_premium'] ? 'teacher-badge-warning' : 'teacher-badge-success'; ?>">
                                                            <?php echo $course['is_premium'] ? 'Premium' : 'Free'; ?>
                                                        </span>
                                                    </div>
                                                    
                                                    <p class="card-text"><?php echo htmlspecialchars(substr($course['description'], 0, 120)) . '...'; ?></p>
                                                    
                                                    <div class="teacher-course-meta">
                                                        <span>
                                                            <i class="fas fa-users me-1"></i>
                                                            <?php echo $course['enrollment_count']; ?> students
                                                        </span>
                                                        <span class="fw-bold text-warning">
                                                            ₹<?php echo number_format($course['price'], 2); ?>
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="teacher-course-actions">
                                                        <a href="edit-course.php?id=<?php echo $course['id']; ?>" class="btn btn-teacher-secondary btn-sm">
                                                            <i class="fas fa-edit me-1"></i>Edit
                                                        </a>
                                                        <a href="course-students.php?id=<?php echo $course['id']; ?>" class="btn btn-teacher-success btn-sm">
                                                            <i class="fas fa-users me-1"></i>Students
                                                        </a>
                                                        <a href="course-details.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye me-1"></i>View
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

                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="dashboard-card">
                            <h5 class="card-title">
                                <i class="fas fa-user-plus me-2"></i>Recent Enrollments
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Course</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentEnrollments as $enrollment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($enrollment['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($enrollment['course_title']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($enrollment['enrolled_at'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="dashboard-card">
                            <h5 class="card-title">
                                <i class="fas fa-video me-2"></i>Upcoming Live Classes
                            </h5>
                            <?php if (empty($upcomingClasses)): ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-video fa-2x text-muted mb-2"></i>
                                    <p class="text-muted small">No upcoming live classes scheduled.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($upcomingClasses as $class): ?>
                                    <div class="border rounded p-3 mb-2">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($class['title']); ?></h6>
                                        <small class="text-muted">Course: <?php echo htmlspecialchars($class['course_title']); ?></small>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('M d, Y H:i', strtotime($class['scheduled_at'])); ?>
                                            </small>
                                            <a href="live-class.php?id=<?php echo $class['id']; ?>" class="btn btn-sm btn-outline-warning float-end">
                                                <i class="fas fa-video me-1"></i>Join
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quiz Attempts -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="dashboard-card">
                            <h5 class="card-title">
                                <i class="fas fa-question-circle me-2"></i>Recent Quiz Attempts
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Quiz</th>
                                            <th>Course</th>
                                            <th>Score</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($quizAttempts as $attempt): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($attempt['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($attempt['quiz_title']); ?></td>
                                            <td><?php echo htmlspecialchars($attempt['course_title']); ?></td>
                                            <td><?php echo $attempt['score']; ?>%</td>
                                            <td>
                                                <?php if ($attempt['passed']): ?>
                                                    <span class="badge bg-success">Passed</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Failed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($attempt['attempted_at'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
        // Animated counters for statistics
        function animateCounter(element, target, duration = 2000) {
            const start = 0;
            const increment = target / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 16);
        }

        // Initialize counters when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('[data-target]');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                animateCounter(counter, target);
            });
        });

        // Add loading animation to cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.teacher-stats-card, .teacher-course-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Enhanced hover effects
        document.addEventListener('DOMContentLoaded', function() {
            const courseCards = document.querySelectorAll('.teacher-course-card');
            courseCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
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