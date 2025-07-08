# ELMS - E-Learning Management System

A comprehensive, professional e-learning platform similar to Byju's or Simplilearn, built using PHP, MySQL, HTML, CSS, JavaScript, and Bootstrap. Features include Google OAuth login, payment gateway integration, course management, live classes, quizzes, certificates, and more.

## 🌟 Features

### 🔐 Authentication & User Management
- **Google OAuth Integration** - One-click login with Google accounts
- **Multi-role System** - Admin, Teacher, and Student dashboards
- **Secure Registration** - Multi-step forms for teachers and admin code verification
- **Profile Management** - Complete user profiles with avatars

### 💳 Payment & Subscription
- **Stripe Payment Gateway** - Secure payment processing for premium courses
- **Multiple Payment Methods** - Credit cards, debit cards, digital wallets
- **Subscription Plans** - Free and premium course tiers
- **Payment History** - Complete transaction tracking

### 📚 Course Management
- **Rich Course Content** - Videos, reading materials, assignments
- **Live Classes** - Real-time interactive sessions with teachers
- **Progress Tracking** - Detailed learning analytics
- **Course Categories** - Organized by subject and difficulty level

### 🎯 Learning Features
- **Interactive Quizzes** - Multiple choice, true/false, short answer
- **Certificates** - Professional certificates upon completion
- **Bounty Points** - Gamified learning with point system
- **Attendance Tracking** - Monitor student participation

### 📞 Communication
- **Contact System** - Direct messaging to company support
- **About Us Page** - Company information and team details
- **Newsletter Subscription** - Email updates and notifications

## 🚀 Quick Start

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependencies)
- SSL certificate (for payment processing)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/elms.git
   cd elms
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Database Setup**
   ```bash
   # Import the database schema
   mysql -u your_username -p your_database < database_updated.sql
   ```

4. **Configuration**
   
   **Database Configuration** (`config/database.php`):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'edutech_pro');
   define('DB_USER', 'your_db_username');
   define('DB_PASS', 'your_db_password');
   ```

   **Google OAuth Configuration** (`config/google_config.php`):
   ```php
   define('GOOGLE_CLIENT_ID', 'your_google_client_id');
   define('GOOGLE_CLIENT_SECRET', 'your_google_client_secret');
   define('GOOGLE_REDIRECT_URI', 'http://yourdomain.com/elms/auth/google-callback.php');
   ```

   **Stripe Payment Configuration** (`config/payment_config.php`):
   ```php
   define('STRIPE_PUBLISHABLE_KEY', 'pk_test_your_stripe_key');
   define('STRIPE_SECRET_KEY', 'sk_test_your_stripe_secret');
   define('STRIPE_WEBHOOK_SECRET', 'whsec_your_webhook_secret');
   ```

5. **Set up Google OAuth**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project or select existing one
   - Enable Google+ API
   - Create OAuth 2.0 credentials
   - Add authorized redirect URIs
   - Copy Client ID and Client Secret to config file

6. **Set up Stripe Payment**
   - Create account at [Stripe Dashboard](https://dashboard.stripe.com/)
   - Get API keys from Developers section
   - Set up webhook endpoints for payment notifications
   - Configure webhook secret

7. **File Permissions**
   ```bash
   chmod 755 uploads/
   chmod 644 config/*.php
   ```

8. **Access the platform**
   - Open your browser and navigate to `http://yourdomain.com/elms`
   - Default admin credentials: admin@edutechpro.com / password

## 📁 Project Structure

```
elms/
├── admin/                 # Admin dashboard and management
├── assets/               # CSS, JS, and static files
├── auth/                 # Google OAuth authentication
├── config/               # Configuration files
├── payment/              # Payment processing
├── student/              # Student dashboard and features
├── teacher/              # Teacher dashboard and features
├── about.php            # About us page
├── contact.php          # Contact form
├── index.php            # Main landing page
└── README.md            # This file
```

## 🔧 Configuration Details

### Google OAuth Setup
1. Visit [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project
3. Enable Google+ API
4. Go to Credentials → Create Credentials → OAuth 2.0 Client ID
5. Set application type to "Web application"
6. Add authorized redirect URIs:
   - `http://localhost/elms/auth/google-callback.php` (for development)
   - `https://yourdomain.com/elms/auth/google-callback.php` (for production)
7. Copy the Client ID and Client Secret to `config/google_config.php`

### Stripe Payment Setup
1. Sign up at [Stripe](https://stripe.com/)
2. Go to Developers → API keys
3. Copy Publishable key and Secret key
4. Set up webhook endpoint:
   - URL: `https://yourdomain.com/elms/payment/webhook.php`
   - Events: `payment_intent.succeeded`, `payment_intent.payment_failed`
5. Copy webhook secret to config file

## 👥 User Roles

### Admin
- Manage all users (students, teachers, admins)
- Create and manage courses
- View analytics and reports
- Manage system settings
- Handle contact messages

### Teacher
- Create and manage courses
- Upload course content (videos, materials)
- Conduct live classes
- Create quizzes and assignments
- Track student progress

### Student
- Browse and enroll in courses
- Access course content
- Take quizzes and earn certificates
- Attend live classes
- Earn bounty points

## 💰 Payment Integration

The platform supports multiple payment scenarios:

### Free Courses
- Students can enroll directly without payment
- Access to basic course content
- Limited features

### Premium Courses
- Payment required before enrollment
- Stripe payment gateway integration
- Multiple payment methods
- Automatic enrollment after successful payment

### Payment Flow
1. Student selects premium course
2. Clicks "Buy Now" button
3. Redirected to Stripe payment form
4. Payment processed securely
5. Automatic enrollment upon success
6. Access granted to course content

## 📞 Contact System

The contact system allows users to:
- Send messages to company support
- Track message status (unread, read, replied, archived)
- Receive email notifications
- Admin can manage all messages from dashboard

## 🔒 Security Features

- **Password Hashing** - Bcrypt encryption for all passwords
- **SQL Injection Protection** - Prepared statements throughout
- **XSS Protection** - Input sanitization and output escaping
- **CSRF Protection** - Token-based form protection
- **Session Security** - Secure session management
- **HTTPS Required** - SSL encryption for all sensitive operations

## 🎨 UI/UX Features

- **Responsive Design** - Works on all devices
- **Modern Interface** - Bootstrap 5 with custom styling
- **Intuitive Navigation** - Easy-to-use dashboard layouts
- **Progress Indicators** - Visual progress tracking
- **Interactive Elements** - Hover effects and animations
- **Accessibility** - WCAG compliant design

## 📊 Analytics & Reporting

- **Student Progress Tracking** - Detailed learning analytics
- **Course Performance** - Popularity and completion rates
- **Payment Analytics** - Revenue and transaction reports
- **User Engagement** - Activity and participation metrics
- **Quiz Performance** - Success rates and difficulty analysis

## 🚀 Deployment

### Production Checklist
- [ ] SSL certificate installed
- [ ] Database optimized and backed up
- [ ] Error logging configured
- [ ] File permissions set correctly
- [ ] Environment variables configured
- [ ] Payment gateway in live mode
- [ ] Google OAuth configured for production domain
- [ ] Email notifications set up
- [ ] Regular backups scheduled

### Performance Optimization
- Enable PHP OPcache
- Configure MySQL query cache
- Use CDN for static assets
- Implement image optimization
- Enable Gzip compression
- Set up caching headers

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Email: info@elms.com
- Phone: +91 98765 43210
- Contact Form: [Contact Us](contact.php)

## 🔄 Updates

### Version 2.0 (Current)
- ✅ Google OAuth integration
- ✅ Stripe payment gateway
- ✅ Enhanced contact system
- ✅ About us page
- ✅ Improved UI/UX
- ✅ Payment processing
- ✅ Course enrollment with payment

### Planned Features
- [ ] Mobile app development
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] Video conferencing integration
- [ ] AI-powered recommendations
- [ ] Social learning features

---

**Made with ❤️ for education** 