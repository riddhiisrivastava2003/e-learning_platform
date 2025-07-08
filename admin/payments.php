<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all payments with student, course, and instructor info
$stmt = $pdo->prepare('
    SELECT p.*, u.full_name as student_name, c.title as course_title, t.full_name as instructor_name
    FROM payments p
    JOIN users u ON p.student_id = u.id
    LEFT JOIN courses c ON p.course_id = c.id
    LEFT JOIN users t ON c.instructor_id = t.id
    ORDER BY p.payment_date DESC
');
$stmt->execute();
$payments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments - Admin | EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
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
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky">
                    <div class="text-center mb-4 d-flex flex-column align-items-center">
                        <i class="fas fa-user-shield fa-2x text-warning mb-2"></i>
                        <i class="fas fa-graduation-cap fa-2x text-warning mb-2"></i>
                        <h5 class="text-white mt-2">EduTech Pro</h5>
                        <small class="text-muted">Admin Panel</small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="users.php"><i class="fas fa-users me-2"></i>Manage Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="courses.php"><i class="fas fa-book me-2"></i>Manage Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="enrollments.php"><i class="fas fa-user-graduate me-2"></i>Enrollments</a></li>
                        <li class="nav-item"><a class="nav-link" href="payments.php"><i class="fas fa-credit-card me-2"></i>Payments</a></li>
                        <li class="nav-item"><a class="nav-link" href="attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a></li>
                        <li class="nav-item"><a class="nav-link" href="certificates.php"><i class="fas fa-certificate me-2"></i>Certificates</a></li>
                        <li class="nav-item"><a class="nav-link" href="reports.php"><i class="fas fa-chart-bar me-2"></i>Reports</a></li>
                        <li class="nav-item"><a class="nav-link" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user-cog me-2"></i>Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 dashboard-main-content">
                <div class="container mt-4">
                    <h2 class="mb-4"><i class="fas fa-credit-card me-2"></i>Manage Payments</h2>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Instructor</th>
                                    <th>Amount (INR)</th>
                                    <th>Status</th>
                                    <th>Payment Method</th>
                                    <th>Transaction ID</th>
                                    <th>Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['course_title'] ?: 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($payment['instructor_name'] ?: 'N/A'); ?></td>
                                    <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($payment['status'])); ?></td>
                                    <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 