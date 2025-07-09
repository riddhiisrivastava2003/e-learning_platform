<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTech Pro - Premium E-Learning Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/text-visibility.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
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
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white mb-4">
                        Transform Your Learning Journey
                    </h1>
                    <p class="lead text-white-50 mb-4">
                        Access premium courses, live classes, and interactive quizzes. Earn certificates and bounty points while mastering new skills.
                    </p>
                    <div class="d-flex gap-3 mb-4">
                        <a href="student/login.php" class="btn btn-primary btn-lg" style="position: relative; z-index: 10; cursor: pointer;">
                            <i class="fas fa-play me-2"></i>Start Learning
                        </a>
                        <a href="#pricing" class="btn btn-outline-light btn-lg" style="position: relative; z-index: 10; cursor: pointer;">
                            <i class="fas fa-crown me-2"></i>Premium Plans
                        </a>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="auth/google-login.php" class="btn btn-light btn-lg" style="position: relative; z-index: 10; cursor: pointer;">
                            <i class="fab fa-google me-2"></i>Continue with Google
                        </a>
                        <a href="student/register.php" class="btn btn-outline-light btn-lg" style="position: relative; z-index: 10; cursor: pointer;">
                            <i class="fas fa-user-plus me-2"></i>Sign Up
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="https://img.freepik.com/free-vector/online-tutorials-concept_52683-37480.jpg" alt="E-Learning" class="img-fluid rounded-3 shadow">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Quick Access</h2>
                <p class="lead text-muted">Get started with our platform quickly</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center quick-link-card">
                        <div class="card-body p-4">
                            <div class="quick-link-icon mb-3">
                                <i class="fas fa-user-graduate fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Student Portal</h5>
                            <p class="card-text text-muted mb-3">Access your courses, track progress, and earn certificates</p>
                            <div class="d-grid gap-2">
                                <a href="student/login.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-sign-in-alt me-2"></i>Student Login
                                </a>
                                <a href="student/register.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-user-plus me-2"></i>Register
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center quick-link-card">
                        <div class="card-body p-4">
                            <div class="quick-link-icon mb-3">
                                <i class="fas fa-chalkboard-teacher fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title">Teacher Portal</h5>
                            <p class="card-text text-muted mb-3">Create courses, manage students, and conduct live classes</p>
                            <div class="d-grid gap-2">
                                <a href="teacher/login.php" class="btn btn-success btn-sm">
                                    <i class="fas fa-sign-in-alt me-2"></i>Teacher Login
                                </a>
                                <a href="teacher/register.php" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-user-plus me-2"></i>Register
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center quick-link-card">
                        <div class="card-body p-4">
                            <div class="quick-link-icon mb-3">
                                <i class="fas fa-user-shield fa-3x text-warning"></i>
                            </div>
                            <h5 class="card-title">Admin Portal</h5>
                            <p class="card-text text-muted mb-3">Manage the platform, users, and system settings</p>
                            <div class="d-grid gap-2">
                                <a href="admin/login.php" class="btn btn-warning btn-sm">
                                    <i class="fas fa-sign-in-alt me-2"></i>Admin Login
                                </a>
                                <a href="admin/register.php" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-user-plus me-2"></i>Register
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center quick-link-card">
                        <div class="card-body p-4">
                            <div class="quick-link-icon mb-3">
                                <i class="fas fa-info-circle fa-3x text-info"></i>
                            </div>
                            <h5 class="card-title">Learn More</h5>
                            <p class="card-text text-muted mb-3">Discover our platform features and get support</p>
                            <div class="d-grid gap-2">
                                <a href="about.php" class="btn btn-info btn-sm">
                                    <i class="fas fa-info me-2"></i>About Us
                                </a>
                                <a href="contact.php" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-envelope me-2"></i>Contact
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Why Choose EduTech Pro?</h2>
                <p class="lead text-muted">Experience the future of education with our cutting-edge features</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-video fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Live Classes</h5>
                            <p class="card-text">Attend interactive live sessions with expert teachers and real-time doubt clearing.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-trophy fa-3x text-warning"></i>
                            </div>
                            <h5 class="card-title">Certificates & Bounty</h5>
                            <p class="card-text">Earn certificates and bounty points for completing courses and passing quizzes.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-mobile-alt fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title">Learn Anywhere</h5>
                            <p class="card-text">Access your courses on any device, anytime, anywhere with our responsive platform.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section id="courses" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Popular Courses</h2>
                <p class="lead text-muted">Explore our wide range of courses designed by industry experts</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card course-card h-100 border-0 shadow-sm">
                        <img src="https://img.freepik.com/free-vector/programming-concept-illustration_114360-1351.jpg" class="card-img-top" alt="Web Development">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary">Premium</span>
                                <span class="text-muted"><i class="fas fa-star text-warning"></i> 4.8</span>
                            </div>
                            <h5 class="card-title">Complete Web Development</h5>
                            <p class="card-text">Master HTML, CSS, JavaScript, PHP, and MySQL to build modern web applications.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">₹2,999</span>
                                <a href="student/login.php" class="btn btn-outline-primary btn-sm">Enroll Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card course-card h-100 border-0 shadow-sm">
                        <img src="https://img.freepik.com/free-vector/data-analysis-concept-illustration_114360-3887.jpg" class="card-img-top" alt="Data Science">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-success">Free</span>
                                <span class="text-muted"><i class="fas fa-star text-warning"></i> 4.6</span>
                            </div>
                            <h5 class="card-title">Data Science Fundamentals</h5>
                            <p class="card-text">Learn the basics of data analysis, statistics, and machine learning algorithms.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-success">Free</span>
                                <a href="student/login.php" class="btn btn-outline-success btn-sm">Start Free</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card course-card h-100 border-0 shadow-sm">
                        <img src="https://img.freepik.com/free-vector/mobile-development-concept-illustration_114360-1350.jpg" class="card-img-top" alt="Mobile Development">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary">Premium</span>
                                <span class="text-muted"><i class="fas fa-star text-warning"></i> 4.9</span>
                            </div>
                            <h5 class="card-title">Mobile App Development</h5>
                            <p class="card-text">Build iOS and Android apps using React Native and Flutter frameworks.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">₹3,499</span>
                                <a href="student/login.php" class="btn btn-outline-primary btn-sm">Enroll Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Choose Your Plan</h2>
                <p class="lead text-muted">Select the perfect plan that fits your learning goals</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="card pricing-card h-100 border-0 shadow">
                        <div class="card-body text-center p-5">
                            <h5 class="card-title text-primary">Free Plan</h5>
                            <div class="price mb-4">
                                <span class="display-4 fw-bold">₹0</span>
                                <span class="text-muted">/month</span>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>5 Free Courses</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Basic Quizzes</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Reading Materials</li>
                                <li class="mb-2 text-muted"><i class="fas fa-times text-danger me-2"></i>Live Classes</li>
                                <li class="mb-2 text-muted"><i class="fas fa-times text-danger me-2"></i>Certificates</li>
                            </ul>
                            <a href="student/register.php" class="btn btn-outline-primary w-100">Get Started</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card pricing-card h-100 border-0 shadow border-primary">
                        <div class="card-body text-center p-5">
                            <div class="badge bg-primary mb-3">Most Popular</div>
                            <h5 class="card-title text-primary">Premium Plan</h5>
                            <div class="price mb-4">
                                <span class="display-4 fw-bold">₹999</span>
                                <span class="text-muted">/month</span>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Unlimited Courses</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Live Classes</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Premium Quizzes</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Certificates</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Bounty Points</li>
                            </ul>
                            <a href="student/register.php" class="btn btn-primary w-100">Subscribe Now</a>
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
                        <a href="#" class="text-white me-10"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-10"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-10"><i class="fab fa-linkedin-in"></i></a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Debug script to check if buttons are clickable
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, checking buttons...');
            
            // Check hero section buttons
            const startLearningBtn = document.querySelector('a[href="student/login.php"]');
            const signUpBtn = document.querySelector('a[href="student/register.php"]');
            
            if (startLearningBtn) {
                console.log('Start Learning button found:', startLearningBtn);
                startLearningBtn.addEventListener('click', function(e) {
                    console.log('Start Learning clicked!');
                });
            } else {
                console.error('Start Learning button not found!');
            }
            
            if (signUpBtn) {
                console.log('Sign Up button found:', signUpBtn);
                signUpBtn.addEventListener('click', function(e) {
                    console.log('Sign Up clicked!');
                });
            } else {
                console.error('Sign Up button not found!');
            }
            
            // Check all buttons in hero section
            const heroButtons = document.querySelectorAll('.hero-section a');
            console.log('Total hero buttons found:', heroButtons.length);
            heroButtons.forEach((btn, index) => {
                console.log(`Button ${index + 1}:`, btn.href, btn.textContent.trim());
            });
        });
    </script>
</body>
</html> 