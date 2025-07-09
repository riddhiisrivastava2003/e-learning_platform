<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch statistics
$totalStudents = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$totalTeachers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'teacher'")->fetchColumn();
$totalCourses = 150; // Hardcoded as requested
$totalEnrollments = $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
$totalPayments = $pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'completed'")->fetchColumn();
$totalAttendance = $pdo->query("SELECT COUNT(*) FROM attendance")->fetchColumn();

// Payments by month (last 6 months)
$paymentsByMonthStmt = $pdo->prepare("
    SELECT DATE_FORMAT(payment_date, '%Y-%m') as month, SUM(amount) as total
    FROM payments
    WHERE status = 'completed'
    GROUP BY month
    ORDER BY month DESC
    LIMIT 6
");
$paymentsByMonthStmt->execute();
$paymentsByMonth = array_reverse($paymentsByMonthStmt->fetchAll(PDO::FETCH_ASSOC));

// Enrollments by course
$enrollmentsByCourseStmt = $pdo->prepare("
    SELECT c.title, COUNT(e.id) as total
    FROM courses c
    LEFT JOIN enrollments e ON c.id = e.course_id
    GROUP BY c.id
    ORDER BY total DESC, c.title ASC
    LIMIT 6
");
$enrollmentsByCourseStmt->execute();
$enrollmentsByCourse = $enrollmentsByCourseStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin | EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admin-dashboard.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
        }
        .hero-header {
            background: linear-gradient(90deg, #0d6efd 0%, #6f42c1 100%);
            color: #fff;
            border-radius: 1.5rem;
            padding: 2.5rem 2rem 2rem 2rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 4px 32px rgba(13,110,253,0.08);
            animation: fadeInDown 1s;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .report-card {
            border-radius: 1.25rem;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            transition: box-shadow 0.2s, transform 0.2s;
            cursor: pointer;
            background: #fff;
            border: none;
            animation: fadeInUp 0.7s;
            text-decoration: none;
        }
        .report-card:hover {
            box-shadow: 0 8px 32px rgba(13,110,253,0.13);
            transform: translateY(-6px) scale(1.03);
        }
        .report-card:active, .report-card:focus {
            outline: 2px solid #0d6efd;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .report-icon {
            font-size: 2.7rem;
            margin-bottom: 0.5rem;
            opacity: 0.85;
        }
        .section-divider {
            border: none;
            border-top: 2px dashed #b6b6b6;
            margin: 2.5rem 0 2rem 0;
        }
        .card {
            border-radius: 1.25rem !important;
            box-shadow: 0 2px 16px rgba(0,0,0,0.06);
            border: none;
        }
        .chart-container {
            min-height: 260px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dashboard-main-content {
            background: transparent;
        }
    </style>
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
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
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
                            <a class="nav-link" href="dashboard.php">
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
                            <a class="nav-link active" href="reports.php">
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
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 dashboard-main-content">
                <div class="container mt-4">
                    <div class="hero-header text-center mb-5">
                        <h1 class="display-5 fw-bold mb-2">Reports & Analytics</h1>
                        <p class="lead mb-0">Visualize your platform's growth, engagement, and revenue at a glance.</p>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <a href="users.php#students" class="p-4 report-card text-center d-block" style="background: #b6d4fe;">
                                <div class="report-icon text-primary"><i class="fas fa-users"></i></div>
                                <h4 class="fw-bold mb-1"><?php echo $totalStudents; ?></h4>
                                <p class="mb-0 text-secondary">Total Students</p>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="users.php#teachers" class="p-4 report-card text-center d-block" style="background: #b1f3ce;">
                                <div class="report-icon text-success"><i class="fas fa-chalkboard-teacher"></i></div>
                                <h4 class="fw-bold mb-1"><?php echo $totalTeachers; ?></h4>
                                <p class="mb-0 text-secondary">Total Teachers</p>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="courses.php" class="p-4 report-card text-center d-block" style="background: #aee9fa;">
                                <div class="report-icon text-info"><i class="fas fa-book"></i></div>
                                <h4 class="fw-bold mb-1"><?php echo $totalCourses; ?></h4>
                                <p class="mb-0 text-secondary">Total Courses</p>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="enrollments.php" class="p-4 report-card text-center d-block" style="background: #ffe8a1;">
                                <div class="report-icon text-warning"><i class="fas fa-user-graduate"></i></div>
                                <h4 class="fw-bold mb-1"><?php echo $totalEnrollments; ?></h4>
                                <p class="mb-0 text-secondary">Total Enrollments</p>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="payments.php" class="p-4 report-card text-center d-block" style="background: #e2e3e5;">
                                <div class="report-icon text-secondary"><i class="fas fa-credit-card"></i></div>
                                <h4 class="fw-bold mb-1"><?php echo $totalPayments; ?></h4>
                                <p class="mb-0 text-secondary">Total Payments</p>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="attendance.php" class="p-4 report-card text-center d-block" style="background: #d3d3d4;">
                                <div class="report-icon text-dark"><i class="fas fa-calendar-check"></i></div>
                                <h4 class="fw-bold mb-1"><?php echo $totalAttendance; ?></h4>
                                <p class="mb-0 text-secondary">Attendance Records</p>
                            </a>
                        </div>
                    </div>
                    <hr class="section-divider">
                    <div class="row g-4">
                        <div class="col-lg-6 mb-4">
                            <div class="card p-4 h-100 chart-container">
                                <div class="w-100">
                                    <h5 class="mb-3 text-primary"><i class="fas fa-chart-line me-2"></i>Payments (Last 6 Months)</h5>
                                    <canvas id="paymentsChart" height="180"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card p-4 h-100 chart-container">
                                <div class="w-100">
                                    <h5 class="mb-3 text-success"><i class="fas fa-chart-pie me-2"></i>Top 6 Courses by Enrollment</h5>
                                    <canvas id="enrollmentsChart" height="180"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script>
        // Payments by month
        const paymentsLabels = <?php echo json_encode(array_column($paymentsByMonth, 'month')); ?>;
        const paymentsData = <?php echo json_encode(array_map('floatval', array_column($paymentsByMonth, 'total'))); ?>;
        new Chart(document.getElementById('paymentsChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: paymentsLabels,
                datasets: [{
                    label: 'Total Payments (INR)',
                    data: paymentsData,
                    backgroundColor: 'rgba(13,110,253,0.7)',
                    borderRadius: 12,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } },
                animation: { duration: 1200, easing: 'easeOutBounce' }
            }
        });
        // Enrollments by course
        const enrollmentsLabels = <?php echo json_encode(array_column($enrollmentsByCourse, 'title')); ?>;
        const enrollmentsData = <?php echo json_encode(array_map('intval', array_column($enrollmentsByCourse, 'total'))); ?>;
        new Chart(document.getElementById('enrollmentsChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: enrollmentsLabels,
                datasets: [{
                    label: 'Enrollments',
                    data: enrollmentsData,
                    backgroundColor: [
                        'rgba(13,110,253,0.7)', 'rgba(25,135,84,0.7)', 'rgba(13,202,240,0.7)', 'rgba(255,193,7,0.7)', '#6c757d', '#212529'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                animation: { animateScale: true, duration: 1200, easing: 'easeOutBounce' }
            }
        });
    </script>
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