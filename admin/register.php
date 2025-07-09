<?php
session_start();
require_once '../config/database_simple.php';
// require_once '../includes/EmailService.php';

// Check if admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $position = $_POST['position'];
    $address = $_POST['address'];
    
    // Validation
    $errors = [];
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long!";
    }
    
    if (empty($department)) {
        $errors[] = "Please enter your department!";
    }
    
    if (empty($position)) {
        $errors[] = "Please enter your position!";
    }
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $errors[] = "Username already exists!";
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "Email already exists!";
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, role, address) VALUES (?, ?, ?, ?, ?, 'admin', ?)");
        
        if ($stmt->execute([$username, $email, $hashed_password, $full_name, $phone, $address])) {
            $admin_id = $pdo->lastInsertId();
            
            // Insert admin details
            $stmt = $pdo->prepare("INSERT INTO admin_details (admin_id, department, position) VALUES (?, ?, ?)");
            $stmt->execute([$admin_id, $department, $position]);
            
            // Set session and redirect to dashboard
            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['admin_name'] = $full_name;
            header('Location: dashboard.php');
            exit();
        } else {
            $errors[] = "Registration failed! Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin-dashboard.css" rel="stylesheet">
    <style>
        .register-container {
            min-height: 100vh;
            background: url('../uploads/register.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
            position: relative;
        }
        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        .register-card {
            position: relative;
            z-index: 2;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
        }
        .register-header {
            background: linear-gradient(135deg, #0d6efd, #0056b3);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .register-body {
            padding: 2rem;
        }
        .form-floating {
            margin-bottom: 1rem;
        }
        .btn-register {
            background: linear-gradient(135deg, #0d6efd, #0056b3);
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
        }
        .admin-code-section {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <i class="fas fa-user-shield fa-3x mb-3"></i>
                <h3>Admin Registration</h3>
                <p class="mb-0">Create administrator account for EduTech Pro</p>
            </div>
            <div class="register-body">
                <?php if (!empty($errors)): ?>
                    <div style="color: #dc3545; font-weight: 500; margin-bottom: 1rem; text-align: center;">
                        <?php foreach ($errors as $error): ?>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <div class="mt-2">
                            <a href="login.php" class="btn btn-success btn-sm">
                                <i class="fas fa-sign-in-alt me-2"></i>Go to Login
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="needs-validation" novalidate>
                    <h5 class="mb-3"><i class="fas fa-user me-2"></i>Personal Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                                <div class="invalid-feedback">
                                    Please enter a username.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                <label for="email"><i class="fas fa-envelope me-2"></i>Email</label>
                                <div class="invalid-feedback">
                                    Please enter a valid email.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                                <div class="invalid-feedback">
                                    Password must be at least 8 characters.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                                <label for="confirm_password"><i class="fas fa-lock me-2"></i>Confirm Password</label>
                                <div class="invalid-feedback">
                                    Please confirm your password.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name" required>
                                <label for="full_name"><i class="fas fa-user-tag me-2"></i>Full Name</label>
                                <div class="invalid-feedback">
                                    Please enter your full name.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone">
                                <label for="phone"><i class="fas fa-phone me-2"></i>Phone</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="department" name="department" placeholder="Department" required>
                                <label for="department"><i class="fas fa-building me-2"></i>Department</label>
                                <div class="invalid-feedback">
                                    Please enter your department.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="position" name="position" placeholder="Position" required>
                                <label for="position"><i class="fas fa-briefcase me-2"></i>Position</label>
                                <div class="invalid-feedback">
                                    Please enter your position.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating">
                        <input type="text" class="form-control" id="address" name="address" placeholder="Address">
                        <label for="address"><i class="fas fa-map-marker-alt me-2"></i>Address</label>
                    </div>
                    <button type="submit" class="btn btn-register mt-3"><i class="fas fa-user-plus me-2"></i>Register</button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="../index.php" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-2"></i>Back to Home
                    </a>
                </div>
                
                <div class="mt-4 p-3 bg-light rounded">
                    <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Important Notes:</h6>
                    <ul class="small text-muted mb-0">
                        <li>Admin accounts have full system access</li>
                        <li>Keep your credentials secure</li>
                        <li>Contact support for admin code</li>
                        <li>Default admin code: <strong>ADMIN2024</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html> 