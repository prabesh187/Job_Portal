# Quick Setup Guide

## 3 Simple Steps

### Step 1: Install XAMPP
1. Download: https://www.apachefriends.org/
2. Install and open XAMPP Control Panel
3. Start Apache and MySQL

### Step 2: Setup Database
1. Open: http://localhost/phpmyadmin
2. Create database: `job_portal`
3. Import file: `database/schema.sql`

### Step 3: Run Application
1. Copy `job-portal` folder to `C:\xampp\htdocs\`
2. Run `setup.bat` (creates folders)
3. Open: http://localhost/job-portal/public

## Login

- Admin: admin@jobportal.com / Admin@123
- Employer: employer@test.com / Admin@123
- Candidate: candidate@test.com / Admin@123

## Common Issues

**Apache won't start?**
- Close Skype (uses port 80)

**Database error?**
- Check MySQL is running
- Verify database name is `job_portal`

**404 errors?**
- See INSTALLATION.md for mod_rewrite setup

## Need More Help?

See INSTALLATION.md for detailed instructions.
