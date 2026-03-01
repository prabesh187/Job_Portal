@echo off
echo ========================================
echo Job Portal - Windows Setup Script
echo ========================================
echo.

REM Check if running from correct directory
if not exist "public\index.php" (
    echo ERROR: Please run this script from the job-portal root directory
    pause
    exit /b 1
)

echo Step 1: Creating upload directories...
if not exist "public\uploads\resumes" mkdir "public\uploads\resumes"
if not exist "public\uploads\logos" mkdir "public\uploads\logos"
if not exist "storage\logs" mkdir "storage\logs"
echo [OK] Directories created
echo.

echo Step 2: Checking configuration file...
if exist "config\config.php" (
    echo [OK] Configuration file exists
) else (
    echo [WARNING] config.php not found
    echo Please ensure config/config.php exists
)
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Next Steps:
echo 1. Make sure XAMPP is installed and running
echo 2. Start Apache and MySQL in XAMPP Control Panel
echo 3. Open phpMyAdmin: http://localhost/phpmyadmin
echo 4. Create database: job_portal
echo 5. Import: database/schema.sql
echo 6. Access application: http://localhost/job-portal/public
echo.
echo Default Login:
echo   Email: admin@jobportal.com
echo   Password: Admin@123
echo.
echo For detailed instructions, see QUICKSTART.md
echo.
pause
