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

$editing = false;
$course = [
    'title' => '',
    'description' => '',
    'price' => '',
    'is_premium' => 0
];

if (isset($_GET['id'])) {
    $editing = true;
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = ? AND instructor_id = ?');
    $stmt->execute([$_GET['id'], $teacher_id]);
    $course = $stmt->fetch();
    if (!$course) {
        header('Location: courses.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $is_premium = isset($_POST['is_premium']) ? 1 : 0;
    if ($editing) {
        $stmt = $pdo->prepare('UPDATE courses SET title=?, description=?, price=?, is_premium=? WHERE id=? AND instructor_id=?');
        $stmt->execute([$title, $description, $price, $is_premium, $course['id'], $teacher_id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO courses (title, description, price, is_premium, instructor_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$title, $description, $price, $is_premium, $teacher_id]);
    }
    header('Location: courses.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editing ? 'Edit' : 'Create'; ?> Course - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
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
            <div class="container mt-5 pt-4">
                <h2 class="mb-4"><?php echo $editing ? 'Edit' : 'Create'; ?> Course</h2>
                <div class="card p-4 mb-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($course['title']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price (INR)</label>
                            <input type="number" name="price" class="form-control" min="0" step="0.01" value="<?php echo htmlspecialchars($course['price']); ?>" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_premium" id="is_premium" value="1" <?php if ($course['is_premium']) echo 'checked'; ?>>
                            <label class="form-check-label" for="is_premium">Premium Course</label>
                        </div>
                        <button type="submit" class="btn btn-warning fw-bold"><?php echo $editing ? 'Update' : 'Create'; ?> Course</button>
                        <a href="courses.php" class="btn btn-secondary ms-2">Cancel</a>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 