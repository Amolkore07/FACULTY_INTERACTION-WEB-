@echo off
REM Trinity Academy Faculty Tracker - Backend Setup Script
REM This script helps verify your setup is correct

echo ================================================
echo  Trinity Academy Faculty Tracker
echo  Backend Setup Verification
echo ================================================
echo.

REM Check if running from correct directory
if not exist "backend\config.php" (
    echo ERROR: This script must be run from the FacultyTracker folder
    echo Current directory: %CD%
    echo Expected files: backend\config.php
    echo.
    pause
    exit /b 1
)

echo [1/7] Checking folder structure...
if exist "backend\" (
    echo     [OK] Backend folder exists
) else (
    echo     [FAIL] Backend folder not found!
    goto :error
)

echo [2/7] Checking required files...
set MISSING=0

if exist "backend\config.php" (
    echo     [OK] config.php found
) else (
    echo     [FAIL] config.php missing!
    set MISSING=1
)

if exist "backend\database.sql" (
    echo     [OK] database.sql found
) else (
    echo     [FAIL] database.sql missing!
    set MISSING=1
)

if exist "backend\login.php" (
    echo     [OK] login.php found
) else (
    echo     [FAIL] login.php missing!
    set MISSING=1
)

if %MISSING%==1 (
    echo     Some required files are missing!
    goto :error
)

echo [3/7] Checking XAMPP installation...
if exist "C:\xampp\php\php.exe" (
    echo     [OK] XAMPP found at C:\xampp
) else (
    echo     [WARNING] XAMPP not fully installed at C:\xampp
    echo.
    echo     XAMPP needs to be properly installed.
    echo     Opening download page...
    start https://www.apachefriends.org/download.html
    echo.
    echo     After downloading and installing XAMPP:
    echo     1. Install to C:\xampp (default location)
    echo     2. Run this script again
    echo.
    pause
    exit /b 1
)

echo [4/7] Checking backend location...
if exist "C:\xampp\htdocs\backend\config.php" (
    echo     [OK] Backend is in correct location
) else (
    echo     [WARNING] Backend not found in C:\xampp\htdocs\
    echo     Please copy the 'backend' folder to C:\xampp\htdocs\
    echo.
    echo     To copy automatically, press Y (or N to skip)
    choice /C YN /M "Copy backend folder now?"
    if errorlevel 2 goto :skip_copy
    if errorlevel 1 goto :do_copy
    
    :do_copy
    echo     Copying backend folder...
    xcopy /E /I /Y "backend" "C:\xampp\htdocs\backend\"
    if errorlevel 1 (
        echo     [FAIL] Copy failed! Copy manually.
        goto :error
    ) else (
        echo     [OK] Backend copied successfully!
    )
    
    :skip_copy
)

echo [5/7] Checking XAMPP services...
echo     Starting XAMPP Control Panel...
if exist "C:\xampp\xampp-control.exe" (
    start "" "C:\xampp\xampp-control.exe"
    echo     [INFO] XAMPP Control Panel opened
    echo     Please click START next to Apache and MySQL
    echo.
    timeout /t 5 >nul
)

tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I /N "httpd.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo     [OK] Apache is running
) else (
    echo     [WARNING] Apache may not be running
    echo     Please start Apache from XAMPP Control Panel
)

tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo     [OK] MySQL is running
) else (
    echo     [WARNING] MySQL may not be running
    echo     Please start MySQL from XAMPP Control Panel
)

echo [6/7] Opening setup resources...
echo     - Opening automatic database setup...
start http://localhost/backend/auto_setup.php

timeout /t 3 >nul

echo     - Opening backend test page...
start http://localhost/backend/test_backend.php

timeout /t 2 >nul

echo [7/7] Setup verification complete!
echo.
echo ================================================
echo  Next Steps:
echo ================================================
echo.
echo  1. Auto Setup page opened in browser
echo     - This creates the database automatically!
echo     - Wait for "Setup Complete!" message
echo.
echo  2. Check test page results (opened in browser)
echo     - All tests should show PASS
echo.
echo  3. Open faculty_tracker.html in browser
echo     - Test login with sample accounts below
echo.
echo ================================================
echo  Test Accounts:
echo ================================================
echo.
echo  HOD:
echo    Email: hod@trinity.edu
echo    Password: hod123
echo.
echo  Faculty:
echo    Email: amit.sharma@trinity.edu
echo    Password: faculty123
echo.
echo  Student:
echo    Email: rahul.d@student.trinity.edu
echo    Password: student123
echo.
echo ================================================
echo  Setup completed successfully!
echo ================================================
echo.
pause
exit /b 0

:error
echo.
echo ================================================
echo  Setup encountered errors!
echo ================================================
echo.
echo  Please check the errors above and:
echo  1. Install XAMPP if not installed
echo  2. Ensure all backend files are present
echo  3. Copy backend folder to C:\xampp\htdocs\
echo  4. Start Apache and MySQL services
echo.
echo  For detailed help, see: QUICK_START.md
echo.
pause
exit /b 1
