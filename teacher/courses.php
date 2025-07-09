<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['teacher_id'])) {
    header('Location: login.php');
    exit();
}
$teacher_id = $_SESSION['teacher_id'];

// Fetch teacher info for sidebar
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch();

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM courses WHERE id = ? AND instructor_id = ?');
    $stmt->execute([$_GET['delete'], $teacher_id]);
    header('Location: courses.php');
    exit();
}

// Get all courses for this teacher
$stmt = $pdo->prepare('SELECT * FROM courses WHERE instructor_id = ? ORDER BY created_at DESC');
$stmt->execute([$teacher_id]);
$courses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/teacher-dashboard.css" rel="stylesheet">
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
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 dashboard-main-content">
            <div class="container-fluid pt-5">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1><i class="fas fa-book me-3"></i>My Courses</h1>
                            <p class="mb-0">Manage and organize your educational content</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="teacher-stat-card">
                                <i class="fas fa-graduation-cap fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="course-stats">
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                            <i class="fas fa-book me-2"></i><?php echo count($courses); ?> Courses
                        </span>
                    </div>
                    <a href="create_course.php" class="btn btn-warning btn-lg fw-bold teacher-btn">
                        <i class="fas fa-plus me-2"></i>Add New Course
                    </a>
                </div>
                
                <div class="teacher-content-card">
                    <?php if (empty($courses)): ?>
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-book-open fa-4x text-muted mb-4"></i>
                                <h3 class="text-muted mb-3">No Courses Yet</h3>
                                <p class="text-muted mb-4">Start creating your first course to share your knowledge with students</p>
                                <a href="create_course.php" class="btn btn-warning btn-lg teacher-btn">
                                    <i class="fas fa-plus me-2"></i>Create Your First Course
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover teacher-table">
                                <thead class="table-warning">
                                    <tr>
                                        <th><i class="fas fa-book me-2"></i>Title</th>
                                        <th><i class="fas fa-align-left me-2"></i>Description</th>
                                        <th><i class="fas fa-rupee-sign me-2"></i>Price</th>
                                        <th><i class="fas fa-crown me-2"></i>Premium</th>
                                        <th><i class="fas fa-calendar me-2"></i>Created</th>
                                        <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($courses as $course): ?>
                                    <tr class="course-row">
                                        <td>
                                            <div class="course-title">
                                                <strong><?php echo htmlspecialchars($course['title']); ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="course-description">
                                                <?php echo htmlspecialchars(substr($course['description'], 0, 60)); ?>...
                                            </div>
                                        </td>
                                        <td>
                                            <span class="price-badge">
                                                ₹<?php echo number_format($course['price'], 2); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($course['is_premium']): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-crown me-1"></i>Premium
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-book me-1"></i>Standard
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('M d, Y', strtotime($course['created_at'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="create_course.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-primary teacher-btn">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                                <a href="courses.php?delete=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-danger teacher-btn" 
                                                   onclick="return confirm('Are you sure you want to delete this course? This action cannot be undone.');">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add hover effects to course rows
        document.querySelectorAll('.course-row').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });
        
        // Add loading animation to delete buttons
        document.querySelectorAll('a[href*="delete"]').forEach(btn => {
            btn.addEventListener('click', function() {
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Deleting...';
                this.classList.add('disabled');
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