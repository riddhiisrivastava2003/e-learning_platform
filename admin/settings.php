<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Settings file path
$settingsFile = __DIR__ . '/../config/settings.json';

// Load current settings or set defaults
$defaultSettings = [
    'theme' => 'auto',
    'registration_enabled' => true,
    'payments_enabled' => true,
    'default_dashboard' => 'admin',
    'site_title' => 'EduTech Pro',
    'contact_email' => 'support@edutechpro.com',
    'maintenance_mode' => false,
    'default_language' => 'en',
    'allow_profile_editing' => true,
    'show_announcements' => true,
];
if (file_exists($settingsFile)) {
    $settings = json_decode(file_get_contents($settingsFile), true);
    if (!is_array($settings)) $settings = $defaultSettings;
} else {
    $settings = $defaultSettings;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings['theme'] = $_POST['theme'] ?? 'auto';
    $settings['registration_enabled'] = isset($_POST['registration_enabled']);
    $settings['payments_enabled'] = isset($_POST['payments_enabled']);
    $settings['default_dashboard'] = $_POST['default_dashboard'] ?? 'admin';
    $settings['site_title'] = trim($_POST['site_title'] ?? 'EduTech Pro');
    $settings['contact_email'] = trim($_POST['contact_email'] ?? 'support@edutechpro.com');
    $settings['maintenance_mode'] = isset($_POST['maintenance_mode']);
    $settings['default_language'] = $_POST['default_language'] ?? 'en';
    $settings['allow_profile_editing'] = isset($_POST['allow_profile_editing']);
    $settings['show_announcements'] = isset($_POST['show_announcements']);
    file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin | EduTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admin-dashboard.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .settings-card { border-radius: 1.25rem; box-shadow: 0 2px 16px rgba(0,0,0,0.07); }
        .form-switch .form-check-input { cursor: pointer; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-graduation-cap me-2"></i>EduTech Pro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid" style="margin-top: 76px;">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky">
                    <div class="text-center mb-4 d-flex flex-column align-items-center">
                        <i class="fas fa-user-shield fa-2x text-warning mb-2"></i>
                        <i class="fas fa-graduation-cap fa-2x text-warning mb-2"></i>
                        <h5 class="text-white mt-2">EduTech Pro</h5>
                        <small class="text-muted">Admin Panel</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users me-2"></i>
                                Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="courses.php">
                                <i class="fas fa-book me-2"></i>
                                Manage Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="enrollments.php">
                                <i class="fas fa-user-graduate me-2"></i>
                                Enrollments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="payments.php">
                                <i class="fas fa-credit-card me-2"></i>
                                Payments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="attendance.php">
                                <i class="fas fa-calendar-check me-2"></i>
                                Attendance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="certificates.php">
                                <i class="fas fa-certificate me-2"></i>
                                Certificates
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports.php">
                                <i class="fas fa-chart-bar me-2"></i>
                                Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="settings.php">
                                <i class="fas fa-cog me-2"></i>
                                Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="fas fa-user-cog me-2"></i>
                                Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 dashboard-main-content">
                <div class="container mt-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card settings-card p-4">
                                <h2 class="mb-4"><i class="fas fa-cog me-2"></i>Global Settings</h2>
                                <?php if (!empty($success)): ?>
                                    <div class="alert alert-success">Settings updated successfully!</div>
                                <?php endif; ?>
                                <form method="post">
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Theme <span class="text-muted">(Affects all dashboards)</span></label>
                                        <select name="theme" class="form-select">
                                            <option value="auto" <?php if($settings['theme']==='auto') echo 'selected'; ?>>Auto</option>
                                            <option value="light" <?php if($settings['theme']==='light') echo 'selected'; ?>>Light</option>
                                            <option value="dark" <?php if($settings['theme']==='dark') echo 'selected'; ?>>Dark</option>
                                        </select>
                                        <div class="form-text">Users can override in their profile, but this is the default for all dashboards.</div>
                                    </div>
                                    <div class="mb-4 form-switch">
                                        <input class="form-check-input" type="checkbox" id="registration_enabled" name="registration_enabled" <?php if($settings['registration_enabled']) echo 'checked'; ?>>
                                        <label class="form-check-label fw-semibold" for="registration_enabled">Enable Registration <span class="text-muted">(Affects Student & Teacher dashboards)</span></label>
                                        <div class="form-text">If disabled, new users cannot register from any dashboard.</div>
                                    </div>
                                    <div class="mb-4 form-switch">
                                        <input class="form-check-input" type="checkbox" id="payments_enabled" name="payments_enabled" <?php if($settings['payments_enabled']) echo 'checked'; ?>>
                                        <label class="form-check-label fw-semibold" for="payments_enabled">Enable Payments <span class="text-muted">(Affects all dashboards)</span></label>
                                        <div class="form-text">If disabled, payment options will be hidden everywhere.</div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Default Dashboard on Login <span class="text-muted">(Affects all users)</span></label>
                                        <select name="default_dashboard" class="form-select">
                                            <option value="admin" <?php if($settings['default_dashboard']==='admin') echo 'selected'; ?>>Admin</option>
                                            <option value="teacher" <?php if($settings['default_dashboard']==='teacher') echo 'selected'; ?>>Teacher</option>
                                            <option value="student" <?php if($settings['default_dashboard']==='student') echo 'selected'; ?>>Student</option>
                                        </select>
                                        <div class="form-text">Users will be redirected to this dashboard after login unless overridden by their role.</div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Site Title <span class="text-muted">(Affects all dashboards)</span></label>
                                        <input type="text" name="site_title" class="form-control" value="<?php echo htmlspecialchars($settings['site_title']); ?>" required>
                                        <div class="form-text">Displayed in the navbar and page titles.</div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Contact Email <span class="text-muted">(Affects all dashboards)</span></label>
                                        <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($settings['contact_email']); ?>" required>
                                        <div class="form-text">Shown in footers and for support contact.</div>
                                    </div>
                                    <div class="mb-4 form-switch">
                                        <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" <?php if($settings['maintenance_mode']) echo 'checked'; ?>>
                                        <label class="form-check-label fw-semibold" for="maintenance_mode">Maintenance Mode <span class="text-muted">(Affects all dashboards)</span></label>
                                        <div class="form-text">If enabled, only admins can access the site. Others see a maintenance message.</div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Default Language <span class="text-muted">(Affects all dashboards)</span></label>
                                        <select name="default_language" class="form-select">
                                            <option value="en" <?php if($settings['default_language']==='en') echo 'selected'; ?>>English</option>
                                            <option value="hi" <?php if($settings['default_language']==='hi') echo 'selected'; ?>>Hindi</option>
                                        </select>
                                        <div class="form-text">Sets the default language for all dashboards. Users can override in their profile.</div>
                                    </div>
                                    <div class="mb-4 form-switch">
                                        <input class="form-check-input" type="checkbox" id="allow_profile_editing" name="allow_profile_editing" <?php if($settings['allow_profile_editing']) echo 'checked'; ?>>
                                        <label class="form-check-label fw-semibold" for="allow_profile_editing">Allow Profile Editing <span class="text-muted">(Affects all dashboards)</span></label>
                                        <div class="form-text">If disabled, users cannot edit their profile information.</div>
                                    </div>
                                    <div class="mb-4 form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_announcements" name="show_announcements" <?php if($settings['show_announcements']) echo 'checked'; ?>>
                                        <label class="form-check-label fw-semibold" for="show_announcements">Show Announcements Banner <span class="text-muted">(Affects all dashboards)</span></label>
                                        <div class="form-text">If enabled, a banner for important announcements will be shown on all dashboards.</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary px-4">Save Settings</button>
                                </form>
                                <hr class="my-4">
                                <h5>How these settings work:</h5>
                                <ul class="mb-0">
                                    <li><strong>Theme</strong>: Sets the default theme for all dashboards (admin, teacher, student).</li>
                                    <li><strong>Enable Registration</strong>: Controls whether new users can register from any dashboard.</li>
                                    <li><strong>Enable Payments</strong>: Controls payment visibility and access everywhere.</li>
                                    <li><strong>Default Dashboard</strong>: Sets the default dashboard users see after login.</li>
                                    <li><strong>Site Title</strong>: Sets the site title displayed in the navbar and page titles.</li>
                                    <li><strong>Contact Email</strong>: Sets the contact email shown in footers and for support contact.</li>
                                    <li><strong>Maintenance Mode</strong>: Controls site access. If enabled, only admins can access the site. Others see a maintenance message.</li>
                                    <li><strong>Default Language</strong>: Sets the default language for all dashboards. Users can override in their profile.</li>
                                    <li><strong>Allow Profile Editing</strong>: Controls whether users can edit their profile information.</li>
                                    <li><strong>Show Announcements Banner</strong>: Controls whether a banner for important announcements is shown on all dashboards.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggler = document.querySelector('.navbar-toggler');
            const sidebar = document.querySelector('.sidebar');
            
            if (navbarToggler) {
                navbarToggler.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(event.target) && !navbarToggler.contains(event.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });
        });
    </script>
</body>
</html> 