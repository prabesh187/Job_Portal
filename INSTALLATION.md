# Installation Guide

## Prerequisites

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- XAMPP (for Windows)

## Step-by-Step Installation

### 1. Install XAMPP (Windows)

1. Download from https://www.apachefriends.org/
2. Run installer (use default settings)
3. Open XAMPP Control Panel
4. Start Apache and MySQL (both should turn green)

### 2. Database Setup

#### Create Database
```sql
CREATE DATABASE job_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Import Schema
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click on `job_portal` database
3. Click "Import" tab
4. Choose file: `database/schema.sql`
5. Click "Go"

Or via command line:
```bash
mysql -u root -p job_portal < database/schema.sql
```

### 3. Project Setup

#### Copy Files
```bash
# Copy job-portal folder to:
C:\xampp\htdocs\job-portal\
```

#### Create Upload Directories

**Windows:**
```cmd
cd C:\xampp\htdocs\job-portal
mkdir public\uploads\resumes
mkdir public\uploads\logos
mkdir storage\logs
```

Or run `setup.bat` script.

**Linux/Mac:**
```bash
mkdir -p public/uploads/resumes
mkdir -p public/uploads/logos
mkdir -p storage/logs
chmod -R 755 public/uploads
chmod -R 755 storage/logs
```

### 4. Configuration

Edit `config/config.php`:

```php
'database' => [
    'host' => 'localhost',
    'dbname' => 'job_portal',
    'username' => 'root',
    'password' => '',  // Your MySQL password
]
```

Update application URL if needed:
```php
'app' => [
    'url' => 'http://localhost/job-portal/public',
]
```

### 5. Web Server Configuration

#### Apache (.htaccess included)

Point document root to `public` directory.

Enable mod_rewrite:
```bash
# Linux
sudo a2enmod rewrite
sudo service apache2 restart
```

#### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/job-portal/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 6. Access Application

Open browser:
```
http://localhost/job-portal/public
```

### 7. Default Credentials

**Admin:**
- Email: admin@jobportal.com
- Password: Admin@123

**Employer:**
- Email: employer@test.com
- Password: Admin@123

**Candidate:**
- Email: candidate@test.com
- Password: Admin@123

⚠️ **Change these passwords immediately!**

## Troubleshooting

### Apache Won't Start
- **Issue**: Port 80 in use
- **Fix**: Close Skype or change Apache port to 8080

### Database Connection Failed
- Verify MySQL is running
- Check database name is `job_portal`
- Verify credentials in `config/config.php`

### 404 on All Pages
- Enable mod_rewrite in Apache
- Check `.htaccess` exists in `public/` folder

### File Upload Errors
- Verify upload folders exist
- Check folder permissions (755 or 777)

## Production Deployment

### Security Checklist
- [ ] Set `'debug' => false` in config
- [ ] Change all default passwords
- [ ] Enable HTTPS
- [ ] Set proper file permissions
- [ ] Configure email settings
- [ ] Enable error logging
- [ ] Set up database backups

### File Permissions (Linux)
```bash
chmod 644 config/config.php
chmod 755 public/uploads
chmod 755 storage/logs
```

### Email Configuration

Edit `config/config.php`:
```php
'email' => [
    'enabled' => true,
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'your-email@gmail.com',
    'password' => 'your-app-password',
]
```

## Testing

1. Register as employer
2. Post a job
3. Register as candidate
4. Apply for job
5. Check application management

## Support

For issues:
1. Check error logs: `storage/logs/`
2. Check Apache logs: `C:\xampp\apache\logs\error.log`
3. Check PHP logs: `C:\xampp\php\logs\php_error_log`
