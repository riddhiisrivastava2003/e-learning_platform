<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_students FROM users WHERE role = 'student'");
$totalStudents = $stmt->fetch()['total_students'];

$stmt = $pdo->query("SELECT COUNT(*) as total_teachers FROM users WHERE role = 'teacher'");
$totalTeachers = $stmt->fetch()['total_teachers'];

// $stmt = $pdo->query("SELECT COUNT(*) as total_courses FROM courses");
// $totalCourses = $stmt->fetch()['total_courses'];
$totalCourses = 150;

$stmt = $pdo->query("SELECT COUNT(*) as total_enrollments FROM enrollments");
$totalEnrollments = $stmt->fetch()['total_enrollments'];

// Get recent enrollments
$stmt = $pdo->query("
    SELECT e.*, u.full_name as student_name, c.title as course_title 
    FROM enrollments e 
    JOIN users u ON e.student_id = u.id 
    JOIN courses c ON e.course_id = c.id 
    ORDER BY e.enrolled_at DESC 
    LIMIT 5
");
$recentEnrollments = $stmt->fetchAll();

// Get recent payments
$stmt = $pdo->query("
    SELECT p.*, u.full_name as student_name, c.title as course_title 
    FROM payments p 
    JOIN users u ON p.student_id = u.id 
    LEFT JOIN courses c ON p.course_id = c.id 
    ORDER BY p.payment_date DESC 
    LIMIT 5
");
$recentPayments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin-dashboard.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
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
                    <div class="text-center mb-4 d-flex flex-column align-items-center">
                        <i class="fas fa-user-shield fa-2x text-warning mb-2"></i>
                        <i class="fas fa-graduation-cap fa-2x text-warning mb-2"></i>
                        <h5 class="text-white mt-2">EduTech Pro</h5>
                        <small class="text-muted">Admin Panel</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users me-2"></i>
                                Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="courses.php">
                                <i class="fas fa-book me-2"></i>
                                Manage Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="enrollments.php">
                                <i class="fas fa-user-graduate me-2"></i>
                                Enrollments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="payments.php">
                                <i class="fas fa-credit-card me-2"></i>
                                Payments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="attendance.php">
                                <i class="fas fa-calendar-check me-2"></i>
                                Attendance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="certificates.php">
                                <i class="fas fa-certificate me-2"></i>
                                Certificates
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports.php">
                                <i class="fas fa-chart-bar me-2"></i>
                                Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
                                <i class="fas fa-cog me-2"></i>
                                Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="fas fa-user-cog me-2"></i>
                                Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 dashboard-main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printReports()">Print Reports</button>
                        </div>
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" id="weekDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-calendar me-1"></i>
                                <span id="weekLabel">This week</span>
                            </button>
                            <div class="dropdown-menu p-3" aria-labelledby="weekDropdown" style="min-width: 250px;">
                                <label for="weekPicker" class="form-label mb-1">Select week:</label>
                                <input type="week" class="form-control" id="weekPicker">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports Section Start -->
                <div id="reports-section">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="stats-card">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3 data-target="<?php echo $totalStudents; ?>">0</h3>
                                        <p class="mb-0">Total Students</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="stats-card">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3 data-target="<?php echo $totalTeachers; ?>">0</h3>
                                        <p class="mb-0">Total Teachers</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="stats-card">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3 data-target="<?php echo $totalCourses; ?>">0</h3>
                                        <p class="mb-0">Total Courses</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-book fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="stats-card">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3 data-target="<?php echo $totalEnrollments; ?>">0</h3>
                                        <p class="mb-0">Total Enrollments</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-user-graduate fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Row -->
                        <div class="row mb-4">
                            <div class="col-lg-8">
                                <div class="dashboard-card">
                                    <h5 class="card-title">Enrollment Trends</h5>
                                    <canvas id="enrollmentChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="dashboard-card">
                                    <h5 class="card-title">Course Categories</h5>
                                    <canvas id="categoryChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Reports Section End -->

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
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentEnrollments as $enrollment): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($enrollment['student_name']); ?></td>
                                                <td><?php echo htmlspecialchars($enrollment['course_title']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($enrollment['enrolled_at'])); ?></td>
                                                <td>
                                                    <span class="badge bg-success">Enrolled</span>
                                                </td>
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
                                    <i class="fas fa-credit-card me-2"></i>Recent Payments
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentPayments as $payment): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                                                <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                                                <td>
                                                    <?php if ($payment['status'] == 'completed'): ?>
                                                        <span class="badge bg-success">Completed</span>
                                                    <?php elseif ($payment['status'] == 'pending'): ?>
                                                        <span class="badge bg-warning">Pending</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Failed</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="dashboard-card">
                                <h5 class="card-title">Quick Actions</h5>
                                <div class="row g-2">
                                    <div class="col-md-3 mb-3">
                                        <a href="users.php" class="btn btn-primary w-100">
                                            <i class="fas fa-users me-2"></i>Manage Users
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="users.php?action=add" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-user-plus me-2"></i>Add User
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="courses.php" class="btn btn-success w-100">
                                            <i class="fas fa-book me-2"></i>Manage Courses
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="courses.php?action=add" class="btn btn-outline-success w-100">
                                            <i class="fas fa-plus me-2"></i>Add Course
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="enrollments.php" class="btn btn-info w-100">
                                            <i class="fas fa-user-graduate me-2"></i>Enrollments
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="payments.php" class="btn btn-warning w-100">
                                            <i class="fas fa-credit-card me-2"></i>Payments
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="attendance.php" class="btn btn-secondary w-100">
                                            <i class="fas fa-calendar-check me-2"></i>Attendance
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="certificates.php" class="btn btn-light w-100">
                                            <i class="fas fa-certificate me-2"></i>Certificates
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="reports.php" class="btn btn-dark w-100">
                                            <i class="fas fa-chart-bar me-2"></i>Reports
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="settings.php" class="btn btn-outline-dark w-100">
                                            <i class="fas fa-cog me-2"></i>Settings
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="profile.php" class="btn btn-outline-info w-100">
                                            <i class="fas fa-user-cog me-2"></i>Profile
                                        </a>
                                    </div>
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
        // Enrollment Chart
        const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
        new Chart(enrollmentCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Enrollments',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Programming', 'Data Science', 'Design', 'Business'],
                datasets: [{
                    data: [40, 25, 20, 15],
                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#ffc107',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Update week label when a week is picked
        document.addEventListener('DOMContentLoaded', function() {
            var weekPicker = document.getElementById('weekPicker');
            var weekLabel = document.getElementById('weekLabel');
            if (weekPicker) {
                weekPicker.addEventListener('change', function() {
                    if (weekPicker.value) {
                        const [year, week] = weekPicker.value.split('-W');
                        weekLabel.textContent = `Week ${week}, ${year}`;
                    } else {
                        weekLabel.textContent = 'This week';
                    }
                });
            }
        });

        function printReports() {
            var printContents = document.getElementById('reports-section').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
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