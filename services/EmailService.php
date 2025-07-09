<?php
/**
 * Email Service Class
 * Handles sending emails including welcome emails to new users
 */
class EmailService {
    
    /**
     * Send welcome email to newly registered user
     * 
     * @param string $email User's email address
     * @param string $full_name User's full name
     * @param string $role User's role (student, teacher, admin)
     * @return bool Success status
     */
    public static function sendWelcomeEmail($email, $full_name, $role) {
        $subject = "Welcome to EduTech Pro - Your Learning Journey Begins!";
        
        $role_display = ucfirst($role);
        $dashboard_url = self::getDashboardUrl($role);
        
        $message = self::getWelcomeEmailTemplate($full_name, $role_display, $dashboard_url);
        
        return self::sendEmail($email, $subject, $message);
    }
    
    /**
     * Get dashboard URL based on user role
     */
    private static function getDashboardUrl($role) {
        $base_url = "http://localhost/elms";
        
        switch ($role) {
            case 'student':
                return $base_url . "/student/dashboard.php";
            case 'teacher':
                return $base_url . "/teacher/dashboard.php";
            case 'admin':
                return $base_url . "/admin/dashboard.php";
            default:
                return $base_url;
        }
    }
    
    /**
     * Get welcome email HTML template
     */
    private static function getWelcomeEmailTemplate($full_name, $role_display, $dashboard_url) {
        $html = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Welcome to EduTech Pro</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #f4f4f4;
                }
                .email-container {
                    background: white;
                    border-radius: 10px;
                    padding: 30px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .header {
                    text-align: center;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 30px;
                    border-radius: 10px 10px 0 0;
                    margin: -30px -30px 30px -30px;
                }
                .header h1 {
                    margin: 0;
                    font-size: 28px;
                    font-weight: bold;
                }
                .header p {
                    margin: 10px 0 0 0;
                    opacity: 0.9;
                }
                .content {
                    padding: 20px 0;
                }
                .welcome-text {
                    font-size: 18px;
                    margin-bottom: 20px;
                    color: #2c3e50;
                }
                .features {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                    margin: 20px 0;
                }
                .features h3 {
                    color: #667eea;
                    margin-top: 0;
                }
                .features ul {
                    margin: 0;
                    padding-left: 20px;
                }
                .features li {
                    margin-bottom: 8px;
                }
                .cta-button {
                    display: inline-block;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 15px 30px;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: bold;
                    margin: 20px 0;
                    text-align: center;
                }
                .footer {
                    text-align: center;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #eee;
                    color: #666;
                    font-size: 14px;
                }
                .social-links {
                    margin: 20px 0;
                }
                .social-links a {
                    display: inline-block;
                    margin: 0 10px;
                    color: #667eea;
                    text-decoration: none;
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="header">
                    <h1>🎓 Welcome to EduTech Pro!</h1>
                    <p>Your Learning Journey Starts Here</p>
                </div>
                
                <div class="content">
                    <div class="welcome-text">
                        <p>Dear <strong>' . htmlspecialchars($full_name) . '</strong>,</p>
                        
                        <p>Welcome to EduTech Pro! We\'re thrilled to have you join our community of learners and educators.</p>
                        
                        <p>You have successfully registered as a <strong>' . htmlspecialchars($role_display) . '</strong> on our platform. Your account is now active and ready to use.</p>
                    </div>
                    
                    <div class="features">
                        <h3>🚀 What You Can Do Now:</h3>
                        <ul>';
        
        if ($role_display == 'Student') {
            $html .= '
                            <li>Browse and enroll in courses</li>
                            <li>Access learning materials and resources</li>
                            <li>Track your progress and achievements</li>
                            <li>Connect with teachers and fellow students</li>
                            <li>Participate in discussions and forums</li>';
        } elseif ($role_display == 'Teacher') {
            $html .= '
                            <li>Create and manage your courses</li>
                            <li>Upload learning materials and assignments</li>
                            <li>Track student progress and performance</li>
                            <li>Communicate with your students</li>
                            <li>Access teaching resources and tools</li>';
        } else {
            $html .= '
                            <li>Manage the entire platform</li>
                            <li>Oversee users, courses, and content</li>
                            <li>Generate reports and analytics</li>
                            <li>Monitor system performance</li>
                            <li>Configure platform settings</li>';
        }
        
        $html .= '
                        </ul>
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="' . $dashboard_url . '" class="cta-button">
                            🎯 Access Your Dashboard
                        </a>
                    </div>
                    
                    <p style="margin-top: 30px;">
                        <strong>Need Help?</strong><br>
                        If you have any questions or need assistance, don\'t hesitate to contact our support team at 
                        <a href="mailto:support@edutechpro.com">support@edutechpro.com</a> or visit our 
                        <a href="http://localhost/elms/contact.php">Contact Page</a>.
                    </p>
                </div>
                
                <div class="footer">
                    <div class="social-links">
                        <a href="#">📘 Facebook</a>
                        <a href="#">🐦 Twitter</a>
                        <a href="#">💼 LinkedIn</a>
                        <a href="#">📷 Instagram</a>
                    </div>
                    
                    <p>
                        <strong>EduTech Pro</strong><br>
                        Empowering Education Through Technology<br>
                        <a href="http://localhost/elms">www.edutechpro.com</a>
                    </p>
                    
                    <p style="font-size: 12px; color: #999;">
                        This email was sent to ' . htmlspecialchars($email) . ' because you registered for an EduTech Pro account.<br>
                        If you didn\'t create this account, please ignore this email.
                    </p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Send email using PHP mail() function
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $message Email message (HTML)
     * @return bool Success status
     */
    private static function sendEmail($to, $subject, $message) {
        // Email headers
        $headers = array(
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: EduTech Pro <noreply@edutechpro.com>',
            'Reply-To: support@edutechpro.com',
            'X-Mailer: PHP/' . phpversion()
        );
        
        // Convert headers array to string
        $headers_string = implode("\r\n", $headers);
        
        // Send email
        $result = mail($to, $subject, $message, $headers_string);
        
        // Log email attempt
        self::logEmailAttempt($to, $subject, $result);
        
        return $result;
    }
    
    /**
     * Log email sending attempts
     */
    private static function logEmailAttempt($to, $subject, $success) {
        $log_file = __DIR__ . '/../logs/email_log.txt';
        $log_dir = dirname($log_file);
        
        // Create logs directory if it doesn't exist
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $status = $success ? 'SUCCESS' : 'FAILED';
        $log_entry = "[$timestamp] $status - To: $to, Subject: $subject\n";
        
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Test email functionality
     */
    public static function testEmail($test_email) {
        return self::sendWelcomeEmail($test_email, 'Test User', 'student');
    }
}
?> 