<?php
session_start();
require_once '../config/database.php';
require_once '../config/payment_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = $_POST['course_id'];
    $amount = $_POST['amount'];
    
    // Get course details
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();
    
    if (!$course) {
        $_SESSION['error'] = "Course not found.";
        header('Location: ../student/course.php?id=' . $course_id);
        exit();
    }
    
    // Check if already enrolled
    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    $existing_enrollment = $stmt->fetch();
    
    if ($existing_enrollment) {
        $_SESSION['error'] = "You are already enrolled in this course.";
        header('Location: ../student/course.php?id=' . $course_id);
        exit();
    }
    
    // Create payment intent
    $payment_intent = createStripePaymentIntent($amount);
    
    if (isset($payment_intent['error'])) {
        $_SESSION['error'] = "Payment initialization failed: " . $payment_intent['error'];
        header('Location: ../student/course.php?id=' . $course_id);
        exit();
    }
    
    // Store payment intent in session
    $_SESSION['payment_intent'] = $payment_intent;
    $_SESSION['enrolling_course_id'] = $course_id;
    $_SESSION['enrolling_amount'] = $amount;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - ELMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4><i class="fas fa-credit-card"></i> Complete Payment</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h5><?php echo $course['title']; ?></h5>
                            <p class="text-muted">Course Enrollment</p>
                            <div class="alert alert-info">
                                <strong>Amount:</strong> <?php echo CURRENCY_SYMBOL; ?><?php echo number_format($amount, 2); ?>
                            </div>
                        </div>
                        
                        <form id="payment-form">
                            <div class="mb-3">
                                <label for="card-element" class="form-label">Credit or debit card</label>
                                <div id="card-element" class="form-control">
                                    <!-- Stripe Elements will create input elements here -->
                                </div>
                                <div id="card-errors" class="text-danger mt-2" role="alert"></div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" id="submit-button">
                                    <i class="fas fa-lock"></i> Pay <?php echo CURRENCY_SYMBOL; ?><?php echo number_format($amount, 2); ?>
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="../student/course.php?id=<?php echo $course_id; ?>" class="text-decoration-none">
                                <i class="fas fa-arrow-left"></i> Back to Course
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Create a Stripe client
        const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');
        const elements = stripe.elements();
        
        // Create an instance of the card Element
        const card = elements.create('card');
        card.mount('#card-element');
        
        // Handle form submission
        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            const {paymentIntent, error} = await stripe.confirmCardPayment(
                '<?php echo $_SESSION['payment_intent']['client_secret']; ?>', {
                    payment_method: {
                        card: card,
                        billing_details: {
                            name: '<?php echo $_SESSION['user_name']; ?>'
                        }
                    }
                }
            );
            
            if (error) {
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-lock"></i> Pay <?php echo CURRENCY_SYMBOL; ?><?php echo number_format($amount, 2); ?>';
            } else {
                // Payment successful
                window.location.href = 'payment-success.php?payment_intent=' + paymentIntent.id;
            }
        });
    </script>
</body>
</html> 