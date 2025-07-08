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
    <link href="../assets/css/style.css" rel="stylesheet">
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
            <div class="container mt-5 pt-4">
                <h2 class="mb-4"><i class="fas fa-calendar-check me-2"></i>Attendance Overview</h2>
                <div class="card p-4 mb-4">
                    <canvas id="attendanceChart" height="100"></canvas>
                </div>
                <p class="text-muted">This graph shows the number of unique students present in your courses over the last 7 days.</p>
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
                            tension: 0.3,
                            fill: true,
                            pointRadius: 5,
                            pointBackgroundColor: '#f59e42',
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            </script>
        </main>
    </div>
</body>
</html> 