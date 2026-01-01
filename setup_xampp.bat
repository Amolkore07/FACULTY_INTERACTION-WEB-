@echo off
title Trinity Academy - XAMPP Setup Script
color 0B
echo =========================================
echo   Trinity Academy Faculty Tracker Setup
echo =========================================
echo.

:: Check if XAMPP is installed
if exist "C:\xampp\htdocs" (
    echo [OK] XAMPP found at C:\xampp
) else (
    echo [ERROR] XAMPP not found at C:\xampp
    echo Please install XAMPP first from: https://www.apachefriends.org/
    echo.
    pause
    exit /b 1
)

echo.
echo Step 1: Copying backend files to XAMPP htdocs...
echo.

:: Create backup if backend folder exists
if exist "C:\xampp\htdocs\backend" (
    echo [INFO] Existing backend folder found, creating backup...
    if exist "C:\xampp\htdocs\backend_backup" rmdir /s /q "C:\xampp\htdocs\backend_backup"
    move "C:\xampp\htdocs\backend" "C:\xampp\htdocs\backend_backup" >nul 2>&1
)

:: Copy backend folder
xcopy "%~dp0backend" "C:\xampp\htdocs\backend\" /E /I /Y >nul 2>&1
if %errorlevel% equ 0 (
    echo [OK] Backend files copied to C:\xampp\htdocs\backend\
) else (
    echo [ERROR] Failed to copy backend files
    pause
    exit /b 1
)

echo.
echo Step 2: Opening XAMPP Control Panel...
echo.
echo [IMPORTANT] Please start Apache and MySQL in XAMPP Control Panel
echo.

:: Try to start XAMPP Control Panel
if exist "C:\xampp\xampp-control.exe" (
    start "" "C:\xampp\xampp-control.exe"
    echo [INFO] XAMPP Control Panel opened
) else (
    echo [WARNING] Could not open XAMPP Control Panel automatically
    echo Please open it manually from C:\xampp\xampp-control.exe
)

echo.
echo =========================================
echo   NEXT STEPS (MANUAL):
echo =========================================
echo.
echo 1. In XAMPP Control Panel:
echo    - Click "Start" next to Apache
echo    - Click "Start" next to MySQL
echo.
echo 2. Open your browser and go to:
echo    http://localhost/phpmyadmin
echo.
echo 3. Create database:
echo    - Click "New" in left sidebar
echo    - Name: faculty_tracker
echo    - Click "Create"
echo.
echo 4. Import database:
echo    - Click on "faculty_tracker" database
echo    - Click "Import" tab
echo    - Click "Choose File"
echo    - Select: C:\xampp\htdocs\backend\database.sql
echo    - Click "Go"
echo.
echo 5. Test backend:
echo    Open: http://localhost/backend/test_backend.php
echo.
echo 6. Open your Faculty Tracker HTML file in browser
echo.
echo =========================================
echo.
pause
