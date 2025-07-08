<?php
session_start();
require_once 'config/database.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // Insert message into database
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        
        if ($stmt->execute([$name, $email, $subject, $message])) {
            $success_message = "Your message has been sent successfully! We'll get back to you soon.";
            
            // Clear form data
            $name = $email = $subject = $message = '';
        } else {
            $error_message = "Failed to send message. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - ELMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        /* Custom Contact Page Styles */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .contact-hero {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            padding: 120px 0 80px;
            position: relative;
            overflow: hidden;
        }
        
        .contact-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .contact-hero .container {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(45deg, #ffffff, #f8f9fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255,255,255,0.9);
            font-weight: 300;
        }
        
        .contact-section {
            background: #f8f9fa;
            padding: 80px 0;
            position: relative;
        }
        
        .contact-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: none;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .contact-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.15);
        }
        
        .contact-info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .contact-info-card:hover {
            transform: scale(1.05);
        }
        
        .contact-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            backdrop-filter: blur(10px);
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
            transform: translateY(-2px);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .btn-contact {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 15px 40px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-contact::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-contact:hover::before {
            left: 100%;
        }
        
        .btn-contact:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .shape {
            position: absolute;
            background: rgba(255,255,255,0.1);
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
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .contact-stats {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin: 0 5px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .social-links a:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-3px);
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .contact-hero {
                padding: 100px 0 60px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i>EduTech Pro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="contact.php">Contact</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if ($_SESSION['user_role'] == 'admin'): ?>
                                    <li><a class="dropdown-item" href="admin/dashboard.php">Dashboard</a></li>
                                <?php elseif ($_SESSION['user_role'] == 'teacher'): ?>
                                    <li><a class="dropdown-item" href="teacher/dashboard.php">Dashboard</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="student/dashboard.php">Dashboard</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>Login
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="student/login.php">Student Login</a></li>
                                <li><a class="dropdown-item" href="teacher/login.php">Teacher Login</a></li>
                                <li><a class="dropdown-item" href="admin/login.php">Admin Login</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="student/register.php">Student Registration</a></li>
                                <li><a class="dropdown-item" href="teacher/register.php">Teacher Registration</a></li>
                                <li><a class="dropdown-item" href="admin/register.php">Admin Registration</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="hero-title mb-4">Get In Touch</h1>
                    <p class="hero-subtitle mb-0">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <!-- Stats Row -->
            <div class="row mb-5">
                <div class="col-md-3 col-6 mb-3">
                    <div class="contact-stats">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Happy Students</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="contact-stats">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Expert Teachers</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="contact-stats">
                        <div class="stat-number">100+</div>
                        <div class="stat-label">Courses Available</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="contact-stats">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Support Available</div>
                    </div>
                </div>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Contact Information -->
                <div class="col-lg-4 mb-4">
                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h5 class="mb-3">Our Location</h5>
                        <p class="mb-4">Gla University, Mathura<br>Uttar Pradesh<br>India</p>
                        
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h5 class="mb-3">Phone & Email</h5>
                        <p class="mb-4">
                            <i class="fas fa-phone me-2"></i> +91 96217 01317<br>
                            <i class="fas fa-envelope me-2"></i> riddhisrivastavaofficial@gmail.com<br>
                            <i class="fas fa-clock me-2"></i> Mon-Fri: 9AM-6PM
                        </p>
                        
                        <div class="contact-icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <h5 class="mb-3">Follow Us</h5>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="contact-card">
                        <div class="card-header bg-gradient text-white py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h4 class="mb-0">
                                <i class="fas fa-paper-plane me-2"></i> Send us a Message
                            </h4>
                            <p class="mb-0 mt-2 opacity-75">We'll get back to you within 24 hours</p>
                        </div>
                        <div class="card-body p-5">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-user me-2"></i>Full Name *
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" 
                                               placeholder="Enter your full name" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope me-2"></i>Email Address *
                                        </label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                                               placeholder="Enter your email address" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="subject" class="form-label">
                                        <i class="fas fa-tag me-2"></i>Subject *
                                    </label>
                                    <input type="text" class="form-control" id="subject" name="subject" 
                                           value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>" 
                                           placeholder="What's this about?" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="message" class="form-label">
                                        <i class="fas fa-comment me-2"></i>Message *
                                    </label>
                                    <textarea class="form-control" id="message" name="message" rows="6" 
                                              placeholder="Tell us more about your inquiry..." required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-contact btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i> Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><i class="fas fa-graduation-cap"></i> ELMS</h5>
                    <p class="text-white">Empowering education through technology. Join thousands of learners worldwide.</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="about.php" class="text-white text-decoration-none">About Us</a></li>
                        <li><a href="contact.php" class="text-white text-decoration-none">Contact</a></li>
                        <li><a href="student/login.php" class="text-white text-decoration-none">Student Login</a></li>
                        <li><a href="teacher/login.php" class="text-white text-decoration-none">Teacher Login</a></li>
                        <li><a href="admin/login.php" class="text-white text-decoration-none">Admin Login</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6>Courses</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none">Programming</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Design</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Business</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Marketing</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Data Science</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Mobile Development</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6>Newsletter</h6>
                    <p class="text-white">Subscribe to our newsletter for updates and exclusive content.</p>
                    <form class="d-flex">
                        <input type="email" class="form-control me-2" placeholder="Your email">
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-white">&copy; 2024 ELMS. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-white">Made with <i class="fas fa-heart text-danger"></i> for education</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add smooth scrolling and animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate form elements on focus
            const formControls = document.querySelectorAll('.form-control');
            formControls.forEach(control => {
                control.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-5px)';
                });
                
                control.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
            
            // Add loading animation to submit button
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('.btn-contact');
            
            form.addEventListener('submit', function() {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html> 