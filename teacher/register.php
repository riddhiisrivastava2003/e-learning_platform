<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../config/database_simple.php';
// require_once '../includes/EmailService.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $qualification = $_POST['qualification'];
    $experience = $_POST['experience'];
    $specialization = $_POST['specialization'];
    $bio = $_POST['bio'];
    $address = $_POST['address'];
    $linkedin = $_POST['linkedin'];
    $website = $_POST['website'];
    
    // Validation
    $errors = [];
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long!";
    }
    
    if (empty($qualification)) {
        $errors[] = "Please enter your qualification!";
    }
    
    if (empty($experience)) {
        $errors[] = "Please enter your experience!";
    }
    
    if (empty($specialization)) {
        $errors[] = "Please enter your specialization!";
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
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, role, address) VALUES (?, ?, ?, ?, ?, 'teacher', ?)");
        
        if ($stmt->execute([$username, $email, $hashed_password, $full_name, $phone, $address])) {
            $teacher_id = $pdo->lastInsertId();
            
            // Insert teacher details
            $stmt = $pdo->prepare("INSERT INTO teacher_details (teacher_id, qualification, experience, specialization, bio, linkedin, website) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$teacher_id, $qualification, $experience, $specialization, $bio, $linkedin, $website]);
            
            // Set session and redirect to dashboard
            $_SESSION['teacher_id'] = $teacher_id;
            $_SESSION['teacher_name'] = $full_name;
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
    <title>Teacher Registration - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
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
            max-width: 800px;
            width: 100%;
        }
        .register-header {
            background: linear-gradient(135deg, #ffc107, #e0a800);
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
            background: linear-gradient(135deg, #ffc107, #e0a800);
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            color: #000;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
            color: #000;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.5rem;
            font-weight: bold;
        }
        .step.active {
            background: #ffc107;
            color: #000;
        }
        .step.completed {
            background: #198754;
            color: white;
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
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                <h3>Teacher Registration</h3>
                <p class="mb-0">Join our teaching community and share your expertise</p>
            </div>
            <div class="register-body">
                <div class="step-indicator">
                    <div class="step active">1</div>
                    <div class="step">2</div>
                    <div class="step">3</div>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php foreach ($errors as $error): ?>
                            <div><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="needs-validation" novalidate id="teacherForm">
                    <!-- Step 1: Basic Information -->
                    <div class="step-content" id="step1">
                        <h5 class="mb-3"><i class="fas fa-user me-2"></i>Basic Information</h5>
                        
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
                                        Password must be at least 6 characters.
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
                        
                        <div class="form-floating">
                            <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name" required>
                            <label for="full_name"><i class="fas fa-id-card me-2"></i>Full Name</label>
                            <div class="invalid-feedback">
                                Please enter your full name.
                            </div>
                        </div>
                        
                        <div class="form-floating">
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
                            <label for="phone"><i class="fas fa-phone me-2"></i>Phone Number</label>
                            <div class="invalid-feedback">
                                Please enter your phone number.
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='login.php'">
                                <i class="fas fa-arrow-left me-2"></i>Back to Login
                            </button>
                            <button type="button" class="btn btn-warning" onclick="nextStep()">
                                Next <i class="fas fa-arrow-right me-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Professional Information -->
                    <div class="step-content" id="step2" style="display: none;">
                        <h5 class="mb-3"><i class="fas fa-graduation-cap me-2"></i>Professional Information</h5>
                        
                        <div class="form-floating">
                            <input type="text" class="form-control" id="qualification" name="qualification" placeholder="Highest Qualification" required>
                            <label for="qualification"><i class="fas fa-certificate me-2"></i>Highest Qualification</label>
                            <div class="invalid-feedback">
                                Please enter your highest qualification.
                            </div>
                        </div>
                        
                        <div class="form-floating">
                            <input type="text" class="form-control" id="experience" name="experience" placeholder="Years of Experience" required>
                            <label for="experience"><i class="fas fa-briefcase me-2"></i>Years of Experience</label>
                            <div class="invalid-feedback">
                                Please enter your years of experience.
                            </div>
                        </div>
                        
                        <div class="form-floating">
                            <input type="text" class="form-control" id="specialization" name="specialization" placeholder="Specialization/Subject" required>
                            <label for="specialization"><i class="fas fa-star me-2"></i>Specialization/Subject</label>
                            <div class="invalid-feedback">
                                Please enter your specialization.
                            </div>
                        </div>
                        
                        <div class="form-floating">
                            <textarea class="form-control" id="bio" name="bio" placeholder="Bio/About You" style="height: 100px"></textarea>
                            <label for="bio"><i class="fas fa-user-edit me-2"></i>Bio/About You</label>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">
                                <i class="fas fa-arrow-left me-2"></i>Previous
                            </button>
                            <button type="button" class="btn btn-warning" onclick="nextStep()">
                                Next <i class="fas fa-arrow-right me-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Additional Information -->
                    <div class="step-content" id="step3" style="display: none;">
                        <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Additional Information</h5>
                        
                        <div class="form-floating">
                            <textarea class="form-control" id="address" name="address" placeholder="Address" style="height: 100px"></textarea>
                            <label for="address"><i class="fas fa-map-marker-alt me-2"></i>Address</label>
                        </div>
                        
                        <div class="form-floating">
                            <input type="url" class="form-control" id="linkedin" name="linkedin" placeholder="LinkedIn Profile">
                            <label for="linkedin"><i class="fab fa-linkedin me-2"></i>LinkedIn Profile (Optional)</label>
                        </div>
                        
                        <div class="form-floating">
                            <input type="url" class="form-control" id="website" name="website" placeholder="Personal Website">
                            <label for="website"><i class="fas fa-globe me-2"></i>Personal Website (Optional)</label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a> and 
                                <a href="#" class="text-decoration-none">Privacy Policy</a>
                            </label>
                            <div class="invalid-feedback">
                                You must agree before submitting.
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">
                                <i class="fas fa-arrow-left me-2"></i>Previous
                            </button>
                            <button type="submit" class="btn btn-warning btn-register">
                                <i class="fas fa-user-plus me-2"></i>Complete Registration
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <a href="../index.php" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-2"></i>Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 3;

        function nextStep() {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    document.getElementById('step' + currentStep).style.display = 'none';
                    currentStep++;
                    document.getElementById('step' + currentStep).style.display = 'block';
                    updateStepIndicator();
                }
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                document.getElementById('step' + currentStep).style.display = 'none';
                currentStep--;
                document.getElementById('step' + currentStep).style.display = 'block';
                updateStepIndicator();
            }
        }

        function updateStepIndicator() {
            const steps = document.querySelectorAll('.step');
            steps.forEach((step, index) => {
                step.classList.remove('active', 'completed');
                if (index + 1 < currentStep) {
                    step.classList.add('completed');
                } else if (index + 1 === currentStep) {
                    step.classList.add('active');
                }
            });
        }

        function validateCurrentStep() {
            const currentStepElement = document.getElementById('step' + currentStep);
            const inputs = currentStepElement.querySelectorAll('input[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            return isValid;
        }

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