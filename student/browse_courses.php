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
    SELECT c.*, u.full_name as instructor_name 
    FROM courses c 
    LEFT JOIN users u ON c.instructor_id = u.id 
    WHERE c.id NOT IN (SELECT course_id FROM enrollments WHERE student_id = ?)
    ORDER BY c.created_at DESC
    LIMIT 150
");
$stmt->execute([$student_id]);
$availableCourses = $stmt->fetchAll();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Courses - EduTech Pro</title>
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
                            <h1 class="h2 fw-bold text-gradient mb-2">Browse Courses 🔍</h1>
                            <p class="mb-0 opacity-75 fs-5">Discover amazing courses and expand your knowledge</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark fs-6">
                                <i class="fas fa-plus me-1"></i><?php echo count($availableCourses); ?> Available
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter Section -->
                <div class="dashboard-card p-4 mb-4 fade-in-up" style="animation-delay: 0.2s;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <form class="d-flex" id="browseCoursesSearchForm">
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input class="form-control" type="search" placeholder="Search courses by title, instructor, or description..." aria-label="Search" id="browseCoursesSearchInput">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search me-1"></i>Search
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="filterCourses('all')">
                                    <i class="fas fa-th me-1"></i>All
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="filterCourses('free')">
                                    <i class="fas fa-check-circle me-1"></i>Free
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="filterCourses('premium')">
                                    <i class="fas fa-crown me-1"></i>Premium
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courses Grid -->
                <div class="row" id="browseCoursesList">
                    <?php if (empty($availableCourses)): ?>
                        <div class="col-12">
                            <div class="dashboard-card p-5 text-center empty-state">
                                <i class="fas fa-search fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-3">No courses available</h4>
                                <p class="text-muted mb-4">Check back later for new courses or contact an administrator</p>
                                <a href="dashboard.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-home me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($availableCourses as $index => $course): ?>
                            <div class="col-lg-6 mb-4 browse-course-item fade-in-up" style="animation-delay: <?php echo 0.3 + ($index * 0.1); ?>s;" data-course-type="<?php echo $course['is_premium'] ? 'premium' : 'free'; ?>">
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
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                Created: <?php echo date('M d, Y', strtotime($course['created_at'])); ?>
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
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Enhanced search functionality
    document.getElementById('browseCoursesSearchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const filter = document.getElementById('browseCoursesSearchInput').value.toLowerCase();
        document.querySelectorAll('.browse-course-item').forEach(function(item) {
            const title = item.querySelector('.card-title').textContent.toLowerCase();
            const description = item.querySelector('.card-text').textContent.toLowerCase();
            const instructor = item.querySelector('.text-muted').textContent.toLowerCase();
            const isVisible = title.includes(filter) || description.includes(filter) || instructor.includes(filter);
            item.style.display = isVisible ? '' : 'none';
        });
    });

    // Filter courses by type
    function filterCourses(type) {
        document.querySelectorAll('.browse-course-item').forEach(function(item) {
            const courseType = item.getAttribute('data-course-type');
            if (type === 'all' || courseType === type) {
                item.style.display = '';
                item.classList.add('fade-in-up');
            } else {
                item.style.display = 'none';
            }
        });
        
        // Update active filter button
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active', 'btn-primary', 'btn-success', 'btn-warning');
            btn.classList.add('btn-outline-primary', 'btn-outline-success', 'btn-outline-warning');
        });
        
        event.target.classList.remove('btn-outline-primary', 'btn-outline-success', 'btn-outline-warning');
        if (type === 'all') {
            event.target.classList.add('btn-primary');
        } else if (type === 'free') {
            event.target.classList.add('btn-success');
        } else if (type === 'premium') {
            event.target.classList.add('btn-warning');
        }
    }

    // Real-time search
    document.getElementById('browseCoursesSearchInput').addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        if (filter.length === 0) {
            document.querySelectorAll('.browse-course-item').forEach(function(item) {
                item.style.display = '';
            });
        } else {
            document.querySelectorAll('.browse-course-item').forEach(function(item) {
                const title = item.querySelector('.card-title').textContent.toLowerCase();
                const description = item.querySelector('.card-text').textContent.toLowerCase();
                const instructor = item.querySelector('.text-muted').textContent.toLowerCase();
                const isVisible = title.includes(filter) || description.includes(filter) || instructor.includes(filter);
                item.style.display = isVisible ? '' : 'none';
            });
        }
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