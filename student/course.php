<?php
session_start();
require_once '../config/database.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = $_GET['id'] ?? null;

if (!$course_id) {
    header('Location: dashboard.php');
    exit();
}

// Get course details and check enrollment
$stmt = $pdo->prepare("
    SELECT c.*, u.full_name as instructor_name, e.progress, e.enrolled_at
    FROM courses c 
    LEFT JOIN users u ON c.instructor_id = u.id 
    LEFT JOIN enrollments e ON c.id = e.course_id AND e.student_id = ?
    WHERE c.id = ?
");
$stmt->execute([$student_id, $course_id]);
$course = $stmt->fetch();

if (!$course) {
    header('Location: dashboard.php');
    exit();
}

// Get lessons
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY order_number");
$stmt->execute([$course_id]);
$lessons = $stmt->fetchAll();

// Get quizzes
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE course_id = ?");
$stmt->execute([$course_id]);
$quizzes = $stmt->fetchAll();

// Check if enrolled
$isEnrolled = !empty($course['enrolled_at']);

// Handle enrollment success message
$enrollmentSuccess = isset($_GET['enrolled']) && $_GET['enrolled'] == '1';

// Fetch student data for sidebar
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$student_id]);
$student = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-graduation-cap me-2"></i>EduTech Pro
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <?php if ($enrollmentSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Success!</strong> You have been successfully enrolled in this course. Welcome to your learning journey!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Course Info -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="https://img.freepik.com/free-vector/programming-concept-illustration_114360-1351.jpg" class="img-fluid rounded" alt="Course">
                            </div>
                            <div class="col-md-8">
                                <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                                <p class="text-muted"><?php echo htmlspecialchars($course['description']); ?></p>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Instructor:</strong><br>
                                        <span class="text-muted"><?php echo htmlspecialchars($course['instructor_name']); ?></span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Level:</strong><br>
                                        <span class="badge bg-primary"><?php echo ucfirst($course['level']); ?></span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Category:</strong><br>
                                        <span class="text-muted"><?php echo htmlspecialchars($course['category']); ?></span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Type:</strong><br>
                                        <span class="badge <?php echo $course['is_premium'] ? 'bg-warning' : 'bg-success'; ?>">
                                            <?php echo $course['is_premium'] ? 'Premium' : 'Free'; ?>
                                        </span>
                                    </div>
                                </div>
                                <?php if ($isEnrolled): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Progress:</strong> <?php echo $course['progress']; ?>% Complete
                                        <div class="progress mt-2">
                                            <div class="progress-bar" style="width: <?php echo $course['progress']; ?>%"></div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        You need to enroll in this course to access the content.
                                        <?php if ($course['is_premium'] && $course['price'] > 0): ?>
                                            <div class="mt-3">
                                                <strong>Course Price:</strong> ₹<?php echo number_format($course['price'], 2); ?>
                                                <form method="POST" action="../payment/process-payment.php" class="d-inline">
                                                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                                    <input type="hidden" name="amount" value="<?php echo $course['price']; ?>">
                                                    <button type="submit" class="btn btn-primary btn-sm ms-2">
                                                        <i class="fas fa-credit-card me-1"></i>Buy Now
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <a href="enroll.php?course_id=<?php echo $course_id; ?>" class="btn btn-success btn-sm ms-2">
                                                <i class="fas fa-plus-circle me-1"></i>Enroll Now
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($isEnrolled): ?>
                    <!-- Lessons -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-play-circle me-2"></i>Course Lessons
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($lessons)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-video fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No lessons available yet</h5>
                                    <p class="text-muted">Lessons will be added by your instructor soon.</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($lessons as $index => $lesson): ?>
                                        <div class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <i class="fas fa-play-circle me-2 text-primary"></i>
                                                        Lesson <?php echo $index + 1; ?>: <?php echo htmlspecialchars($lesson['title']); ?>
                                                    </h6>
                                                    <p class="mb-1 text-muted"><?php echo htmlspecialchars($lesson['description']); ?></p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i><?php echo $lesson['duration']; ?> minutes
                                                    </small>
                                                </div>
                                                <div>
                                                    <?php if ($lesson['video_url']): ?>
                                                        <a href="lesson.php?id=<?php echo $lesson['id']; ?>" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-play me-1"></i>Watch
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($lesson['reading_material']): ?>
                                                        <a href="reading.php?id=<?php echo $lesson['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-book me-1"></i>Read
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Quizzes -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-question-circle me-2"></i>Course Quizzes
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($quizzes)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No quizzes available yet</h5>
                                    <p class="text-muted">Quizzes will be added by your instructor soon.</p>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($quizzes as $quiz): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title"><?php echo htmlspecialchars($quiz['title']); ?></h6>
                                                    <p class="card-text small"><?php echo htmlspecialchars($quiz['description']); ?></p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">
                                                            Passing Score: <?php echo $quiz['passing_score']; ?>%
                                                        </small>
                                                        <a href="quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-play me-1"></i>Take Quiz
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
                <?php endif; ?>
            </div>
            <!-- Sidebar (right) -->
            <div class="col-lg-4">
                <!-- Course Stats -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Course Statistics
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary"><?php echo count($lessons); ?></h4>
                                <small class="text-muted">Lessons</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-warning"><?php echo count($quizzes); ?></h4>
                                <small class="text-muted">Quizzes</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Quick Actions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if ($isEnrolled): ?>
                                <a href="certificates.php" class="btn btn-success"><i class="fas fa-certificate me-1"></i>View Certificate</a>
                                <a href="view_progress.php?course_id=<?php echo $course_id; ?>" class="btn btn-outline-primary"><i class="fas fa-chart-line me-1"></i>View Progress</a>
                                <a href="#myNotesSection" class="btn btn-outline-secondary"><i class="fas fa-sticky-note me-1"></i>My Notes</a>
                            <?php else: ?>
                                <a href="enroll.php?course_id=<?php echo $course_id; ?>" class="btn btn-success">
                                    <i class="fas fa-plus-circle me-2"></i>Enroll Now
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- Course Features -->
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-star me-2"></i>Course Features
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>Video Lectures
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>Reading Materials
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>Interactive Quizzes
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>Progress Tracking
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>Certificate of Completion
                            </li>
                            <?php if ($course['is_premium']): ?>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>Live Classes
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>Priority Support
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- My Notes Section -->
        <div id="myNotesSection" class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>My Notes</h5>
            </div>
            <div class="card-body text-center">
                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                <h5 class="text-muted">This option will be available soon.</h5>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
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