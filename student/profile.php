<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student data
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$student_id]);
$student = $stmt->fetch();

// Handle profile update
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $update_query = 'UPDATE users SET full_name = ?, email = ?';
    $params = [$full_name, $email];
    if (!empty($password)) {
        $update_query .= ', password = ?';
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }
    $update_query .= ' WHERE id = ?';
    $params[] = $student_id;
    $stmt = $pdo->prepare($update_query);
    if ($stmt->execute($params)) {
        $success = 'Profile updated successfully!';
        // Refresh data
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$student_id]);
        $student = $stmt->fetch();
    } else {
        $error = 'Failed to update profile.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/student-dashboard.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                            <h1 class="h2 fw-bold text-gradient mb-2">My Profile 👤</h1>
                            <p class="mb-0 opacity-75 fs-5">Manage your account settings and personal information</p>
                        </div>
                        <div class="text-end">
                            <a href="dashboard.php" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="row">
                    <div class="col-lg-8 fade-in-up" style="animation-delay: 0.2s;">
                        <div class="dashboard-card p-4">
                            <div class="text-center mb-4">
                                <div class="profile-avatar mb-3">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['full_name']); ?>&background=667eea&color=fff&size=120&font-size=0.4" class="rounded-circle shadow-lg" alt="Avatar">
                                    <div class="avatar-badge">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-gradient mb-1"><?php echo htmlspecialchars($student['full_name']); ?></h3>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-graduation-cap me-1"></i>Student
                                </p>
                            </div>

                            <?php if ($success): ?>
                                <div class="alert alert-success d-flex align-items-center" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php echo $success; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" autocomplete="off">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-user me-1 text-primary"></i>Full Name
                                        </label>
                                        <input type="text" name="full_name" class="form-control form-control-lg" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-at me-1 text-primary"></i>Username
                                        </label>
                                        <input type="text" class="form-control form-control-lg" value="<?php echo htmlspecialchars($student['username']); ?>" readonly>
                                        <small class="form-text text-muted">Username cannot be changed</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-envelope me-1 text-primary"></i>Email Address
                                    </label>
                                    <input type="email" name="email" class="form-control form-control-lg" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-lock me-1 text-primary"></i>New Password
                                    </label>
                                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Leave blank to keep current password">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Leave blank to keep your current password
                                    </small>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 fade-in-up" style="animation-delay: 0.4s;">
                        <div class="dashboard-card p-4">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-info-circle me-2 text-primary"></i>Account Information
                            </h5>
                            
                            <div class="info-item mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="info-icon me-3">
                                        <i class="fas fa-calendar text-success"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Member Since</small>
                                        <div class="fw-semibold"><?php echo date('M d, Y', strtotime($student['created_at'])); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="info-item mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="info-icon me-3">
                                        <i class="fas fa-clock text-warning"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Last Updated</small>
                                        <div class="fw-semibold"><?php echo date('M d, Y', strtotime($student['updated_at'] ?? $student['created_at'])); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="info-item mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="info-icon me-3">
                                        <i class="fas fa-shield-alt text-info"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Account Status</small>
                                        <div class="fw-semibold text-success">Active</div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="text-center">
                                <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete()">
                                    <i class="fas fa-trash me-1"></i>Delete Account
                                </button>
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
        function confirmDelete() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                // You can implement the delete functionality here
                alert('Account deletion feature is not implemented yet.');
            }
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