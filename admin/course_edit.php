<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: courses.php');
    exit();
}

$course_id = $_GET['id'];
$success = false;
$error = '';

// Fetch all teachers for instructor dropdown
$stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE role = 'teacher' ORDER BY full_name ASC");
$stmt->execute();
$teachers = $stmt->fetchAll();

// Fetch course data
$stmt = $pdo->prepare('SELECT * FROM courses WHERE id = ?');
$stmt->execute([$course_id]);
$course = $stmt->fetch();
if (!$course) {
    header('Location: courses.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $instructor_id = $_POST['instructor_id'];
    $price = floatval($_POST['price']);
    $is_premium = isset($_POST['is_premium']) ? 1 : 0;
    $level = $_POST['level'];
    $stmt = $pdo->prepare('UPDATE courses SET title=?, description=?, instructor_id=?, price=?, is_premium=?, level=? WHERE id=?');
    if ($stmt->execute([$title, $description, $instructor_id, $price, $is_premium, $level, $course_id])) {
        $success = true;
        // Refresh course data
        $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = ?');
        $stmt->execute([$course_id]);
        $course = $stmt->fetch();
    } else {
        $error = 'Failed to update course.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course - Admin | EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-user-shield me-2"></i>
                <i class="fas fa-graduation-cap me-2"></i>
                EduTech Pro
            </a>
        </div>
    </nav>
    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">Edit Course</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($course['title']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Instructor</label>
                                <select name="instructor_id" class="form-select" required>
                                    <option value="">Select Instructor</option>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?php echo $teacher['id']; ?>" <?php if ($teacher['id'] == $course['instructor_id']) echo 'selected'; ?>><?php echo htmlspecialchars($teacher['full_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Price (INR)</label>
                                <input type="number" name="price" class="form-control" min="0" step="0.01" value="<?php echo htmlspecialchars($course['price']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Level</label>
                                <select name="level" class="form-select" required>
                                    <option value="beginner" <?php if ($course['level'] == 'beginner') echo 'selected'; ?>>Beginner</option>
                                    <option value="intermediate" <?php if ($course['level'] == 'intermediate') echo 'selected'; ?>>Intermediate</option>
                                    <option value="advanced" <?php if ($course['level'] == 'advanced') echo 'selected'; ?>>Advanced</option>
                                </select>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_premium" id="is_premium" value="1" <?php if ($course['is_premium']) echo 'checked'; ?>>
                                <label class="form-check-label" for="is_premium">Premium Course</label>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Update Course</button>
                                <a href="courses.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Course Updated!',
            text: 'The course has been updated successfully.',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        }).then(() => { window.location = 'courses.php'; });
    </script>
    <?php elseif ($error): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo $error; ?>',
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    </script>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 