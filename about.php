<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ELMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
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
                        <a class="nav-link active" href="about.php">About</a>
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
    <section class="text-white py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">About ELMS</h1>
                    <p class="lead mb-4">Empowering education through innovative technology and personalized learning experiences.</p>
                    <a href="#mission" class="btn btn-light btn-lg">Learn More</a>
                </div>
                <div class="col-lg-6">
                    <img src="https://img.freepik.com/free-vector/online-tutorials-concept_52683-37480.jpg" alt="ELMS Platform" class="img-fluid rounded shadow about-hero">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section id="mission" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 border-0 shadow">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="fas fa-bullseye text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="card-title">Our Mission</h3>
                            <p class="card-text text-muted">To democratize education by providing high-quality, accessible learning experiences to students worldwide through cutting-edge technology and expert instruction.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 border-0 shadow">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="fas fa-eye text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="card-title">Our Vision</h3>
                            <p class="card-text text-muted">To become the leading global platform for online education, fostering a community of lifelong learners and empowering individuals to achieve their educational goals.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Why Choose ELMS?</h2>
                <p class="lead text-muted">Discover what makes us different</p>
            </div>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <img src="https://img.freepik.com/free-vector/teaching-concept-illustration_114360-1700.jpg" alt="Expert Instructors" class="img-fluid mb-3" style="max-height: 120px;">
                            </div>
                            <h5>Expert Instructors</h5>
                            <p class="text-muted">Learn from industry professionals and certified educators with years of experience.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <img src="https://img.freepik.com/free-vector/online-learning-concept-illustration_114360-1701.jpg" alt="Interactive Learning" class="img-fluid mb-3" style="max-height: 120px;">
                            </div>
                            <h5>Interactive Learning</h5>
                            <p class="text-muted">Engage with multimedia content, live classes, and hands-on projects for better retention.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <img src="https://img.freepik.com/free-vector/certificate-concept-illustration_114360-1702.jpg" alt="Certified Courses" class="img-fluid mb-3" style="max-height: 120px;">
                            </div>
                            <h5>Certified Courses</h5>
                            <p class="text-muted">Earn recognized certificates and build a portfolio to advance your career.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-clock text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5>Flexible Schedule</h5>
                            <p class="text-muted">Learn at your own pace with 24/7 access to course materials and resources.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-headset text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5>24/7 Support</h5>
                            <p class="text-muted">Get help whenever you need it with our dedicated support team and community forums.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-mobile-alt text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5>Mobile Friendly</h5>
                            <p class="text-muted">Access your courses on any device with our responsive mobile-optimized platform.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="mb-3">
                        <i class="fas fa-users" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="fw-bold">10,000+</h2>
                    <p class="mb-0">Active Students</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="mb-3">
                        <i class="fas fa-chalkboard-teacher" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="fw-bold">500+</h2>
                    <p class="mb-0">Expert Instructors</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="mb-3">
                        <i class="fas fa-book" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="fw-bold">1,000+</h2>
                    <p class="mb-0">Courses Available</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="mb-3">
                        <i class="fas fa-globe" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="fw-bold">50+</h2>
                    <p class="mb-0">Countries Served</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Our Team</h2>
                <p class="lead text-muted">Meet the passionate people behind EduTech Pro</p>
            </div>
            
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 shadow text-center">
                        <div class="card-body p-4">
                            <img src="uploads/dk.jpg" alt="CEO" class="rounded-circle mb-3 team-member" width="120" height="120" style="object-fit: cover;">
                            <h5>Divyanshu Khandelwal</h5>
                            <p class="text-muted">CEO & Founder</p>
                            <p class="small text-muted">10+ years of experience in educational technology and online learning platforms.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 shadow text-center">
                        <div class="card-body p-4">
                            <img src="uploads/ankit.jpg" alt="CTO" class="rounded-circle mb-3 team-member" width="120" height="120" style="object-fit: cover;">
                            <h5>Ankit Verma</h5>
                            <p class="text-muted">Co-Founder</p>
                            <p class="small text-muted">Driven by a passion for accessible education and community building.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 shadow text-center">
                        <div class="card-body p-4">
                            <img src="uploads/ananya.jpg" alt="CDO" class="rounded-circle mb-3 team-member" width="120" height="120" style="object-fit: cover;">
                            <h5>Ananya Shukla</h5>
                            <p class="text-muted">Chief Design Officer</p>
                            <p class="small text-muted">Passionate about creating intuitive and engaging user experiences.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 shadow text-center">
                        <div class="card-body p-4">
                            <img src="uploads/riddhi.jpg" alt="Co-Founder" class="rounded-circle mb-3 team-member" width="120" height="120" style="object-fit: cover;">
                            <h5>Riddhi Srivastava</h5>
                            <p class="text-muted">Chief Technology Officer</p>
                            <p class="small text-muted">Expert in software development and educational technology solutions.</p>
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
</body>
</html> 