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
                    <div class="text-center mb-4">
                        <div>
                            <i class="fas fa-graduation-cap fa-4x text-warning"></i>
                        </div>
                        <h6 class="text-white"><?php echo htmlspecialchars($student['full_name']); ?></h6>
                        <small class="text-muted">Student</small>
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
                <div class="container mt-5" style="max-width: 600px;">
                    <a href="dashboard.php" class="btn btn-outline-success mb-3"><i class="fas fa-arrow-left me-2"></i>Back to Dashboard</a>
                    <div class="card shadow-lg">
                        <div class="card-header text-center p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px 15px 0 0;">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['full_name']); ?>&background=667eea&color=fff&size=128" class="rounded-circle mb-2" alt="Avatar">
                            <h3 class="fw-bold mb-0 text-white"><?php echo htmlspecialchars($student['full_name']); ?></h3>
                            <small class="text-white-50">Student</small>
                        </div>
                        <div class="card-body p-4">
                            <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
                            <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                            <form method="POST" autocomplete="off">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['username']); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                                    <small class="form-text text-muted">Leave blank to keep your current password.</small>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">Update Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html> 