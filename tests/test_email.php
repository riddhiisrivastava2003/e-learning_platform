<?php
require_once '../services/EmailService.php';

$test_result = '';
$email_sent = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $test_email = $_POST['test_email'];
    $test_name = $_POST['test_name'];
    $test_role = $_POST['test_role'];
    
    if (!empty($test_email) && !empty($test_name)) {
        $email_sent = EmailService::sendWelcomeEmail($test_email, $test_name, $test_role);
        $test_result = $email_sent ? 'Email sent successfully!' : 'Failed to send email. Check server configuration.';
    } else {
        $test_result = 'Please fill in all fields.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test - EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .test-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 50px auto;
        }
        .test-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
        .test-body {
            padding: 30px;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-test {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .status-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-radius: 10px;
            padding: 15px;
        }
        .status-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-radius: 10px;
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-card">
            <div class="test-header">
                <h2><i class="fas fa-envelope"></i> Email Service Test</h2>
                <p class="mb-0">Test the welcome email functionality</p>
            </div>
            
            <div class="test-body">
                <?php if ($test_result): ?>
                    <div class="<?php echo $email_sent ? 'status-success' : 'status-error'; ?> mb-4">
                        <i class="fas <?php echo $email_sent ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-2"></i>
                        <?php echo $test_result; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="test_email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Test Email Address
                        </label>
                        <input type="email" class="form-control" id="test_email" name="test_email" 
                               placeholder="Enter your email address" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="test_name" class="form-label">
                            <i class="fas fa-user me-2"></i>Test Name
                        </label>
                        <input type="text" class="form-control" id="test_name" name="test_name" 
                               placeholder="Enter a test name" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="test_role" class="form-label">
                            <i class="fas fa-user-tag me-2"></i>Test Role
                        </label>
                        <select class="form-control" id="test_role" name="test_role" required>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-test btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Send Test Email
                        </button>
                    </div>
                </form>
                
                <div class="mt-4">
                    <h5><i class="fas fa-info-circle me-2"></i>Email Service Information</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Uses PHP mail() function</li>
                        <li><i class="fas fa-check text-success me-2"></i>HTML email templates</li>
                        <li><i class="fas fa-check text-success me-2"></i>Role-specific content</li>
                        <li><i class="fas fa-check text-success me-2"></i>Email logging system</li>
                    </ul>
                </div>
                
                <div class="mt-4">
                    <h5><i class="fas fa-cog me-2"></i>Server Requirements</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas <?php echo function_exists('mail') ? 'fa-check text-success' : 'fa-times text-danger'; ?> me-2"></i>PHP mail() function</li>
                        <li><i class="fas <?php echo is_writable('logs') || is_writable('.') ? 'fa-check text-success' : 'fa-times text-danger'; ?> me-2"></i>Writable logs directory</li>
                        <li><i class="fas <?php echo function_exists('file_put_contents') ? 'fa-check text-success' : 'fa-times text-danger'; ?> me-2"></i>File writing permissions</li>
                    </ul>
                </div>
                
                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 