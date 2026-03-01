# Job Portal - PHP MVC Application

A complete job portal web application built with PHP (OOP), MySQL, MVC architecture, Bootstrap 5, and JavaScript.

## 🚀 Quick Start

**New here?** → See [SETUP.md](SETUP.md) for 3-step setup guide  
**Detailed setup?** → See [INSTALLATION.md](INSTALLATION.md)

## Features

- Multi-role authentication (Admin, Employer, Candidate)
- Job CRUD operations with search and filters
- Resume upload system (secure file handling)
- Application management with status tracking
- Email notifications
- Dashboard analytics
- REST API endpoints (JSON)
- Save job functionality
- AJAX-based search with pagination

## Tech Stack

- PHP 7.4+ (OOP, MVC)
- MySQL 5.7+ with PDO
- Bootstrap 5
- JavaScript (AJAX)

## Quick Setup

### 1. Install XAMPP
- Download from https://www.apachefriends.org/
- Install and start Apache & MySQL

### 2. Create Database
```bash
# Open phpMyAdmin: http://localhost/phpmyadmin
# Create database: job_portal
# Import: database/schema.sql
```

### 3. Setup Files
```bash
# Copy project to: C:\xampp\htdocs\job-portal\
# Run: setup.bat (creates upload folders)
```

### 4. Access Application
```
URL: http://localhost/job-portal/public
```

## Default Login

- **Admin**: admin@jobportal.com / Admin@123
- **Employer**: employer@test.com / Admin@123
- **Candidate**: candidate@test.com / Admin@123

⚠️ Change passwords after first login!

## Configuration

Edit `config/config.php` to update:
- Database credentials
- Application URL
- Email settings
- Upload limits

## Project Structure

```
job-portal/
├── app/
│   ├── Controllers/    # Request handlers
│   ├── Models/         # Database models
│   ├── Views/          # HTML templates
│   └── Core/           # Framework classes
├── config/             # Configuration
├── database/           # SQL schema
├── public/             # Web root (point here)
│   ├── assets/         # CSS, JS
│   └── uploads/        # User files
└── routes/             # URL routes
```

## Security Features

- Password hashing (bcrypt)
- CSRF token protection
- Prepared statements (SQL injection prevention)
- XSS prevention
- Secure file uploads
- Session timeout handling

## API Endpoints

- `GET /api/jobs` - List all jobs (JSON)
- `GET /api/jobs/{id}` - Get job details (JSON)

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite
- 5MB+ upload limit

## License

Open source - free to use and modify.

#imp
this is not completed.
