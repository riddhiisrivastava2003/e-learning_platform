<?php
session_start();
require_once '../config/database.php';
require_once '../services/EmailService.php';

if (!isset($_SESSION['google_user'])) {
    header('Location: ../index.php');
    exit();
}

$google_user = $_SESSION['google_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    
    // Insert user into database
    $stmt = $pdo->prepare("INSERT INTO users (google_id, email, full_name, role, profile_picture, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    
    if ($stmt->execute([$google_user['google_id'], $google_user['email'], $google_user['name'], $role, $google_user['picture']])) {
        $user_id = $pdo->lastInsertId();
        
        // Send welcome email
        $email_sent = EmailService::sendWelcomeEmail($google_user['email'], $google_user['name'], $role);
        
        // Set session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $google_user['name'];
        $_SESSION['user_role'] = $role;
        
        // Clear google user session
        unset($_SESSION['google_user']);
        
        // Redirect based on role
        switch ($role) {
            case 'admin':
                header('Location: ../admin/dashboard.php');
                break;
            case 'teacher':
                header('Location: ../teacher/dashboard.php');
                break;
            case 'student':
                header('Location: ../student/dashboard.php');
                break;
            default:
                header('Location: ../index.php');
        }
        exit();
    } else {
        $error = "Failed to create account. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Role - ELMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4><i class="fas fa-user-plus"></i> Complete Your Registration</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img src="<?php echo $google_user['picture']; ?>" alt="Profile" class="rounded-circle" width="80" height="80">
                            <h5 class="mt-2"><?php echo $google_user['name']; ?></h5>
                            <p class="text-muted"><?php echo $google_user['email']; ?></p>
                        </div>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Select Your Role:</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="role" id="student" value="student" checked>
                                            <label class="form-check-label" for="student">
                                                <i class="fas fa-user-graduate text-primary"></i> Student
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="role" id="teacher" value="teacher">
                                            <label class="form-check-label" for="teacher">
                                                <i class="fas fa-chalkboard-teacher text-success"></i> Teacher
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="role" id="admin" value="admin">
                                            <label class="form-check-label" for="admin">
                                                <i class="fas fa-user-shield text-danger"></i> Admin
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check"></i> Complete Registration
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="../index.php" class="text-decoration-none">
                                <i class="fas fa-arrow-left"></i> Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 