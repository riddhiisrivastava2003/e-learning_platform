<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../config/database_simple.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = 'teacher'");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['teacher_id'] = $user['id'];
        $_SESSION['teacher_name'] = $user['full_name'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .login-container {
            min-height: 100vh;
            background: url('../uploads/e_learning.jpg') center center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 2rem 0;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%);
            z-index: 1;
        }
        
        .login-card {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #000;
            padding: 3rem 2rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .login-header i {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            padding: 1rem;
            margin-bottom: 1rem;
            backdrop-filter: blur(10px);
        }

        .login-header h3 {
            position: relative;
            z-index: 2;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            position: relative;
            z-index: 2;
            opacity: 0.8;
            font-weight: 300;
        }

        .login-body {
            padding: 2.5rem 2rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem 1rem 1rem 3rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
            background: white;
        }

        .form-floating label {
            padding-left: 3rem;
            color: #6c757d;
        }

        .form-floating .form-control:focus + label,
        .form-floating .form-control:not(:placeholder-shown) + label {
            color: #e0a800;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 3;
            transition: color 0.3s ease;
        }

        .form-control:focus ~ .input-icon {
            color: #e0a800;
        }

        .btn-login {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            border: none;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            color: #000;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 193, 7, 0.4);
            color: #000;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .links-section {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }

        .links-section a {
            display: inline-block;
            margin: 0 1rem;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            color: #e0a800;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: rgba(255, 193, 7, 0.1);
        }

        .links-section a:hover {
            background: rgba(255, 193, 7, 0.2);
            transform: translateY(-2px);
            color: #b8860b;
        }

        .demo-section {
            margin-top: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            border-left: 4px solid #ffc107;
        }

        .demo-section h6 {
            color: #e0a800;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .demo-section small {
            color: #6c757d;
            line-height: 1.6;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 40px;
            height: 40px;
            top: 40%;
            left: 80%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @media (max-width: 768px) {
            .login-card {
                margin: 1rem;
                max-width: 100%;
            }
            
            .login-body {
                padding: 2rem 1.5rem;
            }
            
            .links-section a {
                display: block;
                margin: 0.5rem 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">
                <i class="fas fa-chalkboard-teacher me-2"></i>EduTech Pro
            </a>
        </div>
    </nav>
    <div class="login-container">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-chalkboard-teacher fa-3x"></i>
                <h3>Teacher Login</h3>
                <p class="mb-0">Welcome back! Access your teaching dashboard</p>
            </div>
            <div class="login-body">
                <?php if (!empty($error)): ?>
                    <div style="color: #dc3545; font-weight: 500; margin-top: 1rem; text-align: center;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="needs-validation" novalidate>
                    <div class="form-floating">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                        <label for="username">Username</label>
                        <div class="invalid-feedback">
                            Please enter your username.
                        </div>
                    </div>
                    
                    <div class="form-floating">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        <div class="invalid-feedback">
                            Please enter your password.
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-warning btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </form>
                
                <div class="links-section">
                    <a href="register.php">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </a>
                    <a href="../index.php">
                        <i class="fas fa-arrow-left me-2"></i>Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // // Form validation
        // (function() {
        //     'use strict';
        //     window.addEventListener('load', function() {
        //         var forms = document.getElementsByClassName('needs-validation');
        //         var validation = Array.prototype.filter.call(forms, function(form) {
        //             form.addEventListener('submit', function(event) {
        //                 if (form.checkValidity() === false) {
        //                     event.preventDefault();
        //                     event.stopPropagation();
        //                 }
        //                 form.classList.add('was-validated');
        //             }, false);
        //         });
        //     }, false);
        // })();

        // // Add loading animation to button
        // document.querySelector('.btn-login').addEventListener('click', function() {
        //     if (this.form.checkValidity()) {
        //         this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
        //         this.disabled = true;
        //     }
        // });
        const form = document.querySelector('.needs-validation');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const usernameFeedback = usernameInput.nextElementSibling;
        const passwordFeedback = passwordInput.nextElementSibling;

        form.addEventListener('submit', function(event) {
            let valid = true;
            if (usernameInput.value.trim() === '') {
                usernameInput.classList.add('is-invalid');
                usernameFeedback.style.display = 'block';
                valid = false;
            } else {
                usernameInput.classList.remove('is-invalid');
                usernameFeedback.style.display = '';
            }
            if (passwordInput.value.trim() === '') {
                passwordInput.classList.add('is-invalid');
                passwordFeedback.style.display = 'block';
                valid = false;
            } else {
                passwordInput.classList.remove('is-invalid');
                passwordFeedback.style.display = '';
            }
            if (!valid) {
                event.preventDefault();
                event.stopPropagation();
            }
        });

        Add loading animation to button
        document.querySelector('.btn-login').addEventListener('click', function() {
            if (this.form.checkValidity()) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
                this.disabled = true;
            }
        });
    </script>
</body>
</html> 