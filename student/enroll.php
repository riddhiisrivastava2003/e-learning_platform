<?php
session_start();
require_once '../config/database.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = $_GET['course_id'] ?? null;

if (!$course_id) {
    header('Location: dashboard.php');
    exit();
}

// Get course details
$stmt = $pdo->prepare("
    SELECT c.*, u.full_name as instructor_name 
    FROM courses c 
    LEFT JOIN users u ON c.instructor_id = u.id 
    WHERE c.id = ?
");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    header('Location: dashboard.php');
    exit();
}

// Check if already enrolled
$stmt = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
$stmt->execute([$student_id, $course_id]);
$existingEnrollment = $stmt->fetch();

if ($existingEnrollment) {
    header('Location: course.php?id=' . $course_id);
    exit();
}

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($course['is_premium'] && $course['price'] > 0) {
        // Handle payment for premium courses
        $payment_method = $_POST['payment_method'];
        $transaction_id = 'TXN' . time() . rand(1000, 9999);
        
        // Insert payment record
        $stmt = $pdo->prepare("INSERT INTO payments (student_id, course_id, amount, payment_method, transaction_id, status) VALUES (?, ?, ?, ?, ?, 'completed')");
        $stmt->execute([$student_id, $course_id, $course['price'], $payment_method, $transaction_id]);
    }
    
    // Enroll student
    $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
    if ($stmt->execute([$student_id, $course_id])) {
        // Add bounty points for enrollment
        $bountyPoints = $course['is_premium'] ? 50 : 25;
        $stmt = $pdo->prepare("INSERT INTO bounty_points (student_id, points, source) VALUES (?, ?, 'Course Enrollment')");
        $stmt->execute([$student_id, $bountyPoints]);
        
        header('Location: course.php?id=' . $course_id . '&enrolled=1');
        exit();
    } else {
        $error = "Enrollment failed! Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll in Course - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-graduation-cap me-2"></i>EduTech Pro
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Enroll in Course
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-4">
                                <img src="https://img.freepik.com/free-vector/programming-concept-illustration_114360-1351.jpg" class="img-fluid rounded" alt="Course">
                            </div>
                            <div class="col-md-8">
                                <h5><?php echo htmlspecialchars($course['title']); ?></h5>
                                <p class="text-muted"><?php echo htmlspecialchars($course['description']); ?></p>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Instructor:</strong><br>
                                        <span class="text-muted"><?php echo htmlspecialchars($course['instructor_name']); ?></span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Level:</strong><br>
                                        <span class="badge bg-primary"><?php echo ucfirst($course['level']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Category:</strong><br>
                                        <span class="text-muted"><?php echo htmlspecialchars($course['category']); ?></span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Type:</strong><br>
                                        <span class="badge <?php echo $course['is_premium'] ? 'bg-warning' : 'bg-success'; ?>">
                                            <?php echo $course['is_premium'] ? 'Premium' : 'Free'; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Course Price:</strong> ₹<?php echo number_format($course['price'], 2); ?>
                                    <?php if ($course['is_premium']): ?>
                                        <br><small>This is a premium course with advanced features and live support.</small>
                                    <?php else: ?>
                                        <br><small>This is a free course with basic features.</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <form method="POST" class="needs-validation" novalidate>
                            <?php if ($course['is_premium'] && $course['price'] > 0): ?>
                                <h6 class="mb-3">Payment Method</h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" required>
                                            <label class="form-check-label" for="credit_card">
                                                <i class="fas fa-credit-card me-2"></i>Credit Card
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="debit_card" value="debit_card" required>
                                            <label class="form-check-label" for="debit_card">
                                                <i class="fas fa-credit-card me-2"></i>Debit Card
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="upi" value="upi" required>
                                            <label class="form-check-label" for="upi">
                                                <i class="fas fa-mobile-alt me-2"></i>UPI
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="net_banking" value="net_banking" required>
                                            <label class="form-check-label" for="net_banking">
                                                <i class="fas fa-university me-2"></i>Net Banking
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Demo Payment:</strong> This is a demo payment system. No actual charges will be made.
                                </div>
                            <?php endif; ?>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a> and understand that 
                                    <?php if ($course['is_premium']): ?>
                                        I will be charged ₹<?php echo number_format($course['price'], 2); ?> for this course.
                                    <?php else: ?>
                                        this course is free to enroll.
                                    <?php endif; ?>
                                </label>
                                <div class="invalid-feedback">
                                    You must agree before enrolling.
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    <?php if ($course['is_premium'] && $course['price'] > 0): ?>
                                        Pay ₹<?php echo number_format($course['price'], 2); ?> & Enroll
                                    <?php else: ?>
                                        Enroll for Free
                                    <?php endif; ?>
                                </button>
                                <a href="dashboard.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
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
    </script>
</body>
</html> 