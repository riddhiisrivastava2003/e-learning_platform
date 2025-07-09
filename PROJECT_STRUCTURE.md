# ELMS Project Structure

This document explains the organized folder structure of the ELMS (E-Learning Management System) project.

## 📁 Directory Structure

```
elms/
├── 📁 admin/                 # Admin panel files
├── 📁 assets/               # CSS, JS, and other static assets
│   ├── 📁 css/
│   └── 📁 js/
├── 📁 auth/                 # Authentication related files
├── 📁 config/               # Configuration files
├── 📁 database/             # Database SQL files
├── 📁 docs/                 # Documentation files
├── 📁 payment/              # Payment processing files
├── 📁 services/             # Service classes (EmailService, etc.)
├── 📁 student/              # Student panel files
├── 📁 teacher/              # Teacher panel files
├── 📁 tests/                # Test and debug files
├── 📁 uploads/              # Uploaded images and media files
├── 📁 fpdf/                 # PDF generation library
├── index.php                # Main landing page
├── about.php                # About page
├── contact.php              # Contact page
├── 404.php                  # Error page
├── logout.php               # Logout functionality
├── install.php              # Installation script
├── setup_database.php       # Database setup
├── composer.json            # PHP dependencies
└── .htaccess               # Apache configuration
```

## 📂 Folder Descriptions

### 🎯 Core Application Folders
- **`admin/`** - Admin panel interface and functionality
- **`student/`** - Student dashboard and course management
- **`teacher/`** - Teacher dashboard and course creation
- **`auth/`** - Authentication and authorization files

### 🛠️ Configuration & Services
- **`config/`** - Database connections, API keys, and settings
- **`services/`** - Business logic classes (EmailService, etc.)
- **`payment/`** - Payment gateway integration

### 📚 Documentation & Assets
- **`docs/`** - Project documentation, guides, and README files
- **`assets/`** - CSS, JavaScript, and other static resources
- **`uploads/`** - User-uploaded images and media files

### 🗄️ Database & Testing
- **`database/`** - SQL files for database setup and migrations
- **`tests/`** - Test files and debugging scripts

### 📄 Main Files
- **`index.php`** - Landing page and main entry point
- **`about.php`** - About page with team information
- **`contact.php`** - Contact form and information
- **`install.php`** - Installation and setup wizard

## 🔄 Recent Changes

The project has been reorganized to improve maintainability:

1. **Moved all images** to `uploads/` folder
2. **Organized documentation** in `docs/` folder
3. **Separated test files** into `tests/` folder
4. **Consolidated database files** in `database/` folder
5. **Moved service classes** to `services/` folder
6. **Updated file paths** in all PHP files to reflect new structure

## 🚀 Getting Started

1. **Installation**: Run `install.php` to set up the database
2. **Configuration**: Update settings in `config/` folder
3. **Documentation**: Check `docs/` folder for detailed guides
4. **Testing**: Use files in `tests/` folder for debugging

## 📝 File Path Updates

All file references have been updated to reflect the new structure:
- Image paths now point to `uploads/` folder
- Service includes now point to `services/` folder
- Database files are referenced from `database/` folder
- Documentation links point to `docs/` folder

This organization makes the project more maintainable and easier to navigate for developers. 