<?php
session_start();
require_once '../config/database.php';
require_once '../config/payment_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

if (!isset($_GET['payment_intent'])) {
    header('Location: ../student/dashboard.php');
    exit();
}

$payment_intent_id = $_GET['payment_intent'];

// Verify payment with Stripe
$payment_intent = confirmStripePayment($payment_intent_id);

if (isset($payment_intent['error'])) {
    $_SESSION['error'] = "Payment verification failed.";
    header('Location: ../student/dashboard.php');
    exit();
}

if ($payment_intent->status === 'succeeded') {
    // Get enrollment details from session
    $course_id = $_SESSION['enrolling_course_id'];
    $amount = $_SESSION['enrolling_amount'];
    
    // Create enrollment record
    $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id, enrollment_date, status, payment_amount, payment_id) VALUES (?, ?, NOW(), ?, ?, ?)");
    
    if ($stmt->execute([$_SESSION['user_id'], $course_id, ENROLLMENT_ACTIVE, $amount, $payment_intent_id])) {
        // Create payment record
        $stmt = $pdo->prepare("INSERT INTO payments (student_id, course_id, amount, payment_method, payment_id, status, created_at) VALUES (?, ?, ?, 'stripe', ?, ?, NOW())");
        $stmt->execute([$_SESSION['user_id'], $course_id, $amount, $payment_intent_id, PAYMENT_COMPLETED]);
        
        // Clear session data
        unset($_SESSION['payment_intent']);
        unset($_SESSION['enrolling_course_id']);
        unset($_SESSION['enrolling_amount']);
        
        $success = true;
    } else {
        $error = "Failed to create enrollment record.";
    }
} else {
    $error = "Payment was not successful.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - ELMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <?php if (isset($success)): ?>
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-success mb-3">Payment Successful!</h3>
                            <p class="text-muted mb-4">Your course enrollment has been completed successfully.</p>
                            
                            <div class="alert alert-success">
                                <strong>Payment ID:</strong> <?php echo $payment_intent_id; ?><br>
                                <strong>Amount:</strong> <?php echo CURRENCY_SYMBOL; ?><?php echo number_format($amount, 2); ?>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="../student/dashboard.php" class="btn btn-primary">
                                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                                </a>
                                <a href="../student/course.php?id=<?php echo $course_id; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-play"></i> Start Learning
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="mb-4">
                                <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-danger mb-3">Payment Failed</h3>
                            <p class="text-muted mb-4"><?php echo $error; ?></p>
                            
                            <div class="d-grid gap-2">
                                <a href="../student/dashboard.php" class="btn btn-primary">
                                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                                </a>
                                <a href="../student/course.php?id=<?php echo $course_id; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-redo"></i> Try Again
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 