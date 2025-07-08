<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Test - ELMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Link Test Page</h1>
        <p>Testing if the links work properly:</p>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <h3>Hero Section Links</h3>
                <div class="d-grid gap-3">
                    <a href="student/login.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-play me-2"></i>Start Learning
                    </a>
                    <a href="student/register.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Sign Up
                    </a>
                    <a href="auth/google-login.php" class="btn btn-light btn-lg">
                        <i class="fab fa-google me-2"></i>Continue with Google
                    </a>
                </div>
            </div>
            
            <div class="col-md-6">
                <h3>Quick Access Links</h3>
                <div class="d-grid gap-3">
                    <a href="student/login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Student Login
                    </a>
                    <a href="student/register.php" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i>Student Register
                    </a>
                    <a href="teacher/login.php" class="btn btn-success">
                        <i class="fas fa-sign-in-alt me-2"></i>Teacher Login
                    </a>
                    <a href="admin/login.php" class="btn btn-warning">
                        <i class="fas fa-sign-in-alt me-2"></i>Admin Login
                    </a>
                </div>
            </div>
        </div>
        
        <div class="mt-5">
            <h3>File Existence Check</h3>
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>student/login.php:</strong> 
                    <?php echo file_exists('student/login.php') ? '<span class="text-success">✓ Exists</span>' : '<span class="text-danger">✗ Missing</span>'; ?>
                </li>
                <li class="list-group-item">
                    <strong>student/register.php:</strong> 
                    <?php echo file_exists('student/register.php') ? '<span class="text-success">✓ Exists</span>' : '<span class="text-danger">✗ Missing</span>'; ?>
                </li>
                <li class="list-group-item">
                    <strong>teacher/login.php:</strong> 
                    <?php echo file_exists('teacher/login.php') ? '<span class="text-success">✓ Exists</span>' : '<span class="text-danger">✗ Missing</span>'; ?>
                </li>
                <li class="list-group-item">
                    <strong>admin/login.php:</strong> 
                    <?php echo file_exists('admin/login.php') ? '<span class="text-success">✓ Exists</span>' : '<span class="text-danger">✗ Missing</span>'; ?>
                </li>
            </ul>
        </div>
        
        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Homepage
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add click event listeners to test if buttons are clickable
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('a');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    console.log('Link clicked:', this.href);
                    // Uncomment the next line to prevent navigation for testing
                    // e.preventDefault();
                });
            });
        });
    </script>
</body>
</html> 