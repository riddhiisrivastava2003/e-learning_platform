<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['teacher_id'])) {
    header('Location: login.php');
    exit();
}
$teacher_id = $_SESSION['teacher_id'];

// Example: Fetch attendance data for the last 7 days (replace with real query as needed)
$labels = [];
$data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('D', strtotime($date));
    // Count unique students marked present for this teacher's courses on this date
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT a.student_id) as present_count FROM attendance a JOIN courses c ON a.course_id = c.id WHERE c.instructor_id = ? AND DATE(a.marked_at) = ? AND a.status = 'present'");
    $stmt->execute([$teacher_id, $date]);
    $data[] = (int)($stmt->fetch()['present_count'] ?? 0);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/teacher-dashboard.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">
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
                    <h6 class="text-white"><?php echo htmlspecialchars($_SESSION['teacher_name'] ?? 'Teacher'); ?></h6>
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
                        <a class="nav-link active" href="attendance.php">
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
                            <h1><i class="fas fa-calendar-check me-3"></i>Attendance Overview</h1>
                            <p class="mb-0">Track student attendance and engagement in your courses</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="teacher-stat-card">
                                <i class="fas fa-chart-line fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Attendance Stats -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="teacher-stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users fa-2x text-warning"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number"><?php echo array_sum($data); ?></h3>
                                <p class="stat-label">Total Present (7 days)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="teacher-stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-day fa-2x text-warning"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number"><?php echo round(array_sum($data) / 7, 1); ?></h3>
                                <p class="stat-label">Daily Average</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="teacher-stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-trending-up fa-2x text-warning"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number"><?php echo max($data); ?></h3>
                                <p class="stat-label">Peak Attendance</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="teacher-content-card">
                    <div class="chart-container">
                        <canvas id="attendanceChart" height="100"></canvas>
                    </div>
                    <div class="text-center mt-4">
                        <p class="text-muted">This graph shows the number of unique students present in your courses over the last 7 days.</p>
                    </div>
                </div>
            </div>
            <script>
                const ctx = document.getElementById('attendanceChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($labels); ?>,
                        datasets: [{
                            label: 'Students Present',
                            data: <?php echo json_encode($data); ?>,
                            borderColor: '#f59e42',
                            backgroundColor: 'rgba(245, 158, 66, 0.2)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 6,
                            pointBackgroundColor: '#f59e42',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                ticks: { 
                                    stepSize: 1,
                                    font: {
                                        family: 'Inter',
                                        weight: '600'
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        family: 'Inter',
                                        weight: '600'
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        }
                    }
                });
            </script>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animate stat numbers
        function animateNumbers() {
            document.querySelectorAll('.stat-number').forEach(element => {
                const finalNumber = parseInt(element.textContent);
                let currentNumber = 0;
                const increment = finalNumber / 50;
                
                const timer = setInterval(() => {
                    currentNumber += increment;
                    if (currentNumber >= finalNumber) {
                        element.textContent = finalNumber;
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(currentNumber);
                    }
                }, 50);
            });
        }
        
        // Run animation when page loads
        document.addEventListener('DOMContentLoaded', animateNumbers);

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