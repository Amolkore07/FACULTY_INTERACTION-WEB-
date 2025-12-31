# Trinity Academy - Faculty Performance Tracker Backend

Complete PHP and MySQL backend for the Faculty Performance & Interaction Tracker system.

## 📋 Table of Contents
- [System Requirements](#system-requirements)
- [Installation Guide](#installation-guide)
- [Database Setup](#database-setup)
- [Configuration](#configuration)
- [API Endpoints](#api-endpoints)
- [Testing](#testing)
- [Security](#security)
- [Troubleshooting](#troubleshooting)

## 🖥️ System Requirements

- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher (or MariaDB 10.2+)
- **Web Server**: Apache 2.4+ with mod_rewrite enabled
- **Extensions**: mysqli, json
- **XAMPP/WAMP/LAMP**: Recommended for local development

## 📦 Installation Guide

### Step 1: Install XAMPP (if not already installed)

1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP to `C:\xampp` (Windows) or `/opt/lampp` (Linux)
3. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Copy Backend Files

1. Copy the entire `backend` folder to XAMPP's `htdocs` directory:
   ```
   C:\xampp\htdocs\backend\
   ```

2. Your structure should look like:
   ```
   C:\xampp\htdocs\backend\
   ├── config.php
   ├── database.sql
   ├── login.php
   ├── signup.php
   ├── get_faculty.php
   ├── submit_feedback.php
   ├── get_feedback.php
   ├── submit_complaint.php
   ├── get_complaints.php
   ├── .htaccess
   └── logs/ (will be created automatically)
   ```

### Step 3: Database Setup

1. Open your web browser and go to: `http://localhost/phpmyadmin`

2. Create a new database:
   - Click "New" in the left sidebar
   - Database name: `faculty_tracker`
   - Collation: `utf8mb4_unicode_ci`
   - Click "Create"

3. Import the database schema:
   - Select the `faculty_tracker` database
   - Click on "Import" tab
   - Choose file: `backend/database.sql`
   - Click "Go" to import

4. Verify tables are created:
   - You should see: `users`, `feedbacks`, `complaints`
   - Sample data is automatically inserted

### Step 4: Configure Database Connection

1. Open `backend/config.php` in a text editor

2. Update database credentials if needed (default XAMPP settings):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');           // Your MySQL username
   define('DB_PASS', '');               // Your MySQL password
   define('DB_NAME', 'faculty_tracker');
   ```

3. Save the file

### Step 5: Test Backend API

1. Open browser and navigate to: `http://localhost/backend/get_faculty.php`

2. You should see JSON response with faculty list:
   ```json
   {
     "status": "success",
     "message": "Faculty list retrieved successfully",
     "data": [...],
     "timestamp": "2025-10-31 12:00:00"
   }
   ```

3. If you see errors, check [Troubleshooting](#troubleshooting) section

## 🔧 Configuration

### Database Configuration (`config.php`)

```php
// Database settings
define('DB_HOST', 'localhost');      // Database host
define('DB_USER', 'root');           // Database username
define('DB_PASS', '');               // Database password
define('DB_NAME', 'faculty_tracker'); // Database name

// Timezone setting
define('TIMEZONE', 'Asia/Kolkata');   // Your timezone
```

### CORS Configuration (`.htaccess`)

By default, CORS is enabled for all origins (`*`). For production, restrict to your domain:

```apache
Header set Access-Control-Allow-Origin "https://yourdomain.com"
```

## 🌐 API Endpoints

### Base URL
```
http://localhost/backend/
```

---

### 1. User Authentication

#### **Login**
- **Endpoint**: `POST /login.php`
- **Description**: Authenticate users (student, faculty, HOD)
- **Parameters**:
  ```
  role: 'student' | 'faculty' | 'hod'
  identifier: email or name
  password: user password
  ```
- **Response**:
  ```json
  {
    "status": "success",
    "message": "Login successful",
    "data": {
      "id": 1,
      "user_id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "student"
    }
  }
  ```

#### **Sign Up**
- **Endpoint**: `POST /signup.php`
- **Description**: Register new users (primarily students)
- **Parameters**:
  ```
  role: 'student' | 'faculty' | 'hod'
  name: Full name
  email: Email address
  password: Password (min 6 characters)
  enrollment_no: (optional for students)
  class: (optional for students)
  subject: (optional for faculty)
  department: (optional for faculty/HOD)
  ```
- **Response**:
  ```json
  {
    "status": "success",
    "message": "Registration successful! You can now login.",
    "data": { user details }
  }
  ```

---

### 2. Faculty Management

#### **Get Faculty List**
- **Endpoint**: `GET /get_faculty.php`
- **Description**: Retrieve all faculty members
- **Parameters**: None
- **Response**:
  ```json
  {
    "status": "success",
    "message": "Faculty list retrieved successfully",
    "data": [
      {
        "faculty_id": 2,
        "name": "Prof. Amit Sharma",
        "email": "amit.sharma@trinity.edu",
        "subject": "Data Structures",
        "department": "Computer Engineering"
      }
    ]
  }
  ```

---

### 3. Feedback Management

#### **Submit Feedback**
- **Endpoint**: `POST /submit_feedback.php`
- **Description**: Submit student feedback for faculty
- **Parameters**:
  ```
  student_id: Student user ID
  faculty_id: Faculty user ID
  subject: Subject name
  rating: Rating (1-5)
  comments: Feedback comments
  feedback_date: Date (YYYY-MM-DD)
  feedback_time: Time (HH:MM:SS)
  ```
- **Response**:
  ```json
  {
    "status": "success",
    "message": "Feedback submitted successfully",
    "data": { feedback details }
  }
  ```

#### **Get Feedback**
- **Endpoint**: `GET /get_feedback.php`
- **Description**: Retrieve feedback based on role
- **Parameters**:
  ```
  role: 'student' | 'faculty' | 'hod'
  faculty_id: (optional) Filter by faculty
  student_id: (optional) Filter by student
  ```
- **Response**:
  ```json
  {
    "status": "success",
    "message": "Feedback retrieved successfully",
    "data": [
      {
        "feedback_id": 1,
        "student_name": "Anonymous Student",
        "faculty_name": "Prof. Amit Sharma",
        "subject": "Data Structures",
        "rating": "5.0",
        "comments": "Excellent teaching...",
        "feedback_date": "2025-10-15",
        "timestamp": "2025-10-15 10:30:00"
      }
    ]
  }
  ```

---

### 4. Complaint Management

#### **Submit Complaint**
- **Endpoint**: `POST /submit_complaint.php`
- **Description**: Submit student complaint
- **Parameters**:
  ```
  student_id: Student user ID
  faculty_id: (optional) Faculty user ID
  description: Complaint description
  complaint_date: Date (YYYY-MM-DD)
  complaint_time: Time (HH:MM:SS)
  ```
- **Response**:
  ```json
  {
    "status": "success",
    "message": "Complaint submitted successfully",
    "data": { complaint details }
  }
  ```

#### **Get Complaints**
- **Endpoint**: `GET /get_complaints.php`
- **Description**: Retrieve complaints based on role
- **Parameters**:
  ```
  role: 'student' | 'faculty' | 'hod'
  faculty_id: (optional) Filter by faculty
  student_id: (optional) Filter by student
  status: (optional) 'pending' | 'resolved' | 'in_progress'
  ```
- **Response**:
  ```json
  {
    "status": "success",
    "message": "Complaints retrieved successfully",
    "data": [
      {
        "complaint_id": 1,
        "student_name": "Anonymous Student",
        "faculty_name": "Prof. Vikram Singh",
        "description": "Teaching pace too fast...",
        "complaint_date": "2025-10-20",
        "status": "pending"
      }
    ]
  }
  ```

---

## 🧪 Testing

### Default Test Accounts

The database comes pre-populated with test accounts:

#### HOD Account
- **Email**: `hod@trinity.edu`
- **Password**: `hod123`
- **Role**: HOD

#### Faculty Accounts
- **Email**: `amit.sharma@trinity.edu`
- **Password**: `faculty123`
- **Role**: Faculty

- **Email**: `priya.mehta@trinity.edu`
- **Password**: `faculty123`
- **Role**: Faculty

#### Student Accounts
- **Email**: `rahul.d@student.trinity.edu`
- **Password**: `student123`
- **Role**: Student

- **Email**: `ananya.k@student.trinity.edu`
- **Password**: `student123`
- **Role**: Student

### Testing with cURL

Test login endpoint:
```bash
curl -X POST http://localhost/backend/login.php \
  -d "role=student" \
  -d "identifier=rahul.d@student.trinity.edu" \
  -d "password=student123"
```

Test get faculty:
```bash
curl http://localhost/backend/get_faculty.php
```

### Testing with Postman

1. Import the following endpoints into Postman
2. Set request type (GET/POST)
3. Add parameters as form-data
4. Send request and verify response

---

## 🔒 Security

### Password Security
- All passwords are hashed using PHP's `password_hash()` with BCRYPT
- Never store plain text passwords
- Minimum password length: 6 characters

### SQL Injection Prevention
- All queries use prepared statements
- Input sanitization on all user data
- Parameter binding for all SQL operations

### XSS Prevention
- HTML special characters are escaped
- Input validation on all endpoints
- Content-Type headers properly set

### CSRF Protection
- CORS headers configured
- Origin validation recommended for production
- Session-based authentication recommended for production

### Recommendations for Production

1. **Change default passwords** for all test accounts
2. **Enable HTTPS** - Uncomment HTTPS redirect in `.htaccess`
3. **Restrict CORS** - Change `*` to your specific domain
4. **Disable error display** - Set `display_errors = Off` in PHP
5. **Implement JWT tokens** for authentication
6. **Add rate limiting** to prevent abuse
7. **Use environment variables** for sensitive config
8. **Regular database backups**
9. **Keep PHP and MySQL updated**
10. **Monitor log files** in `logs/` directory

---

## 🐛 Troubleshooting

### Problem: "Database connection failed"

**Solution**:
1. Check if MySQL is running in XAMPP Control Panel
2. Verify database credentials in `config.php`
3. Ensure database `faculty_tracker` exists
4. Check MySQL error log: `C:\xampp\mysql\data\mysql_error.log`

### Problem: "404 Not Found" for API endpoints

**Solution**:
1. Verify files are in `C:\xampp\htdocs\backend\`
2. Check Apache is running in XAMPP
3. Ensure URL is `http://localhost/backend/filename.php`
4. Check Apache error log: `C:\xampp\apache\logs\error.log`

### Problem: CORS errors in browser console

**Solution**:
1. Ensure `.htaccess` file exists in backend folder
2. Enable `mod_headers` and `mod_rewrite` in Apache
3. In `C:\xampp\apache\conf\httpd.conf`, uncomment:
   ```
   LoadModule headers_module modules/mod_headers.so
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
4. Restart Apache

### Problem: "Access denied" for database user

**Solution**:
1. Check MySQL username/password in `config.php`
2. For custom MySQL users, grant permissions:
   ```sql
   GRANT ALL PRIVILEGES ON faculty_tracker.* TO 'username'@'localhost';
   FLUSH PRIVILEGES;
   ```

### Problem: PHP errors not showing

**Solution**:
1. In `config.php`, ensure:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
2. Check `logs/activity.log` for application logs
3. Check `logs/php_errors.log` for PHP errors

### Problem: Frontend can't connect to backend

**Solution**:
1. Verify backend URL in HTML file (should be `http://localhost/backend/`)
2. Test backend endpoints directly in browser
3. Check browser console for error messages
4. Verify CORS headers are being sent (check Network tab)

---

## 📊 Database Schema

### Users Table
```sql
- user_id (INT, PK, AUTO_INCREMENT)
- name (VARCHAR)
- email (VARCHAR, UNIQUE)
- password (VARCHAR, HASHED)
- role (ENUM: student, faculty, hod)
- enrollment_no (VARCHAR, optional)
- class (VARCHAR, optional)
- subject (VARCHAR, optional)
- department (VARCHAR, optional)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### Feedbacks Table
```sql
- feedback_id (INT, PK, AUTO_INCREMENT)
- student_id (INT, FK -> users)
- faculty_id (INT, FK -> users)
- subject (VARCHAR)
- rating (DECIMAL 2,1)
- comments (TEXT)
- feedback_date (DATE)
- feedback_time (TIME)
- created_at (TIMESTAMP)
```

### Complaints Table
```sql
- complaint_id (INT, PK, AUTO_INCREMENT)
- student_id (INT, FK -> users)
- faculty_id (INT, FK -> users, optional)
- description (TEXT)
- complaint_date (DATE)
- complaint_time (TIME)
- status (ENUM: pending, resolved, in_progress)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

---

## 📝 Logs

Application logs are stored in `backend/logs/`:
- `activity.log` - All API requests and activities
- `php_errors.log` - PHP runtime errors

Monitor these files for debugging and security auditing.

---

## 🚀 Deployment to Production

### Pre-deployment Checklist

- [ ] Change all default passwords
- [ ] Update database credentials
- [ ] Enable HTTPS
- [ ] Restrict CORS to your domain
- [ ] Disable error display
- [ ] Set up automatic backups
- [ ] Test all endpoints
- [ ] Review security settings
- [ ] Set up monitoring
- [ ] Document custom configurations

### Recommended Hosting

- **Shared Hosting**: Any PHP hosting with MySQL
- **VPS**: DigitalOcean, Linode, Vultr
- **Cloud**: AWS, Google Cloud, Azure
- **Requirements**: PHP 7.4+, MySQL 5.7+, Apache/Nginx

---

## 📞 Support

For issues or questions:
1. Check the [Troubleshooting](#troubleshooting) section
2. Review log files in `logs/` directory
3. Verify database tables and data
4. Test endpoints individually
5. Check browser console for frontend errors

---

## 📄 License

This project is created for Trinity Academy of Engineering.
© 2025 KJ's Education Institutes - All Rights Reserved

---

## 🔄 Version History

- **v1.0.0** (2025-10-31)
  - Initial release
  - Complete CRUD operations for users, feedback, and complaints
  - Role-based access control
  - Sample data included
  - Comprehensive API documentation

---

**Last Updated**: October 31, 2025
