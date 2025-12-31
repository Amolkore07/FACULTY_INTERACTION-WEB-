# 🚀 QUICK START GUIDE
## Trinity Academy Faculty Tracker - Backend Setup

Follow these simple steps to get your backend running in **5 minutes**!

---

## ✅ Step 1: Install XAMPP

1. Download XAMPP: https://www.apachefriends.org/download.html
2. Install to default location: `C:\xampp`
3. Open **XAMPP Control Panel**
4. Start **Apache** and **MySQL** modules (click Start buttons)

---

## ✅ Step 2: Copy Backend Files

1. Copy the `backend` folder
2. Paste it into: `C:\xampp\htdocs\`
3. Final path should be: `C:\xampp\htdocs\backend\`

---

## ✅ Step 3: Create Database

1. Open browser and go to: **http://localhost/phpmyadmin**
2. Click **"New"** in left sidebar
3. Enter database name: **faculty_tracker**
4. Select collation: **utf8mb4_unicode_ci**
5. Click **"Create"**

---

## ✅ Step 4: Import Database

1. In phpMyAdmin, click on **faculty_tracker** database
2. Click **"Import"** tab at the top
3. Click **"Choose File"** button
4. Select: `C:\xampp\htdocs\backend\database.sql`
5. Click **"Go"** button at bottom
6. Wait for "Import has been successfully finished" message

---

## ✅ Step 5: Test Backend

Open these URLs in your browser:

1. **Test Faculty List**:
   ```
   http://localhost/backend/get_faculty.php
   ```
   ✅ You should see JSON with faculty list

2. **Test Config**:
   ```
   http://localhost/backend/config.php
   ```
   ✅ Should see blank page (no errors)

---

## ✅ Step 6: Update Frontend

1. Open your `faculty_tracker.html` file
2. Find all API URLs (search for `http://localhost/backend/`)
3. They should already be correct, but verify they point to:
   ```
   http://localhost/backend/login.php
   http://localhost/backend/signup.php
   http://localhost/backend/get_faculty.php
   etc.
   ```

---

## ✅ Step 7: Test Login

1. Open `faculty_tracker.html` in browser
2. Try logging in with test account:
   - **Role**: Student
   - **Email**: rahul.d@student.trinity.edu
   - **Password**: student123

3. Click **Login** button
4. ✅ You should see the student dashboard!

---

## 🎉 SUCCESS!

Your backend is now running! Try these test accounts:

### 🎓 HOD Account
- Email: `hod@trinity.edu`
- Password: `hod123`

### 👨‍🏫 Faculty Account
- Email: `amit.sharma@trinity.edu`
- Password: `faculty123`

### 👨‍🎓 Student Account
- Email: `rahul.d@student.trinity.edu`
- Password: `student123`

---

## 🐛 Common Issues

### Issue: "Database connection failed"
**Fix**: 
- Make sure MySQL is running in XAMPP Control Panel (green light)
- Check database name is exactly: `faculty_tracker`

### Issue: "404 Not Found"
**Fix**:
- Verify backend files are in: `C:\xampp\htdocs\backend\`
- Make sure Apache is running in XAMPP (green light)

### Issue: CORS errors in browser
**Fix**:
1. Open: `C:\xampp\apache\conf\httpd.conf`
2. Find and uncomment (remove # from):
   ```
   LoadModule headers_module modules/mod_headers.so
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. Save file
4. Restart Apache in XAMPP Control Panel

### Issue: Can't import database.sql
**Fix**:
- Try smaller imports: Copy-paste SQL content directly into phpMyAdmin SQL tab
- Increase max upload size in php.ini if needed

---

## 📂 Folder Structure

```
C:\xampp\htdocs\backend\
│
├── config.php              ← Database connection
├── database.sql            ← Database schema & sample data
├── .htaccess              ← CORS & security settings
│
├── login.php              ← User authentication
├── signup.php             ← User registration
│
├── get_faculty.php        ← Get faculty list
│
├── submit_feedback.php    ← Submit feedback
├── get_feedback.php       ← Get feedbacks
│
├── submit_complaint.php   ← Submit complaint
├── get_complaints.php     ← Get complaints
│
├── logs/                  ← Auto-created for logs
│   ├── activity.log
│   └── php_errors.log
│
└── README.md              ← Full documentation
```

---

## 🔍 Verify Everything Works

### Test Checklist:

1. ✅ Apache running (XAMPP green)
2. ✅ MySQL running (XAMPP green)
3. ✅ Database created (faculty_tracker)
4. ✅ Tables imported (users, feedbacks, complaints)
5. ✅ Can access: http://localhost/backend/get_faculty.php
6. ✅ Can login with test account
7. ✅ Can submit feedback
8. ✅ Can submit complaint

---

## 📱 Next Steps

1. **Customize** - Change college name, colors, etc.
2. **Add Faculty** - Log in as HOD and add real faculty members
3. **Register Students** - Have students sign up with real accounts
4. **Test Features** - Submit feedback, complaints, view dashboards
5. **Go Live** - Deploy to production server when ready

---

## 🆘 Need Help?

1. Check `logs/activity.log` for errors
2. Check browser console (F12) for errors
3. Review full README.md for detailed documentation
4. Verify all steps were completed exactly

---

## 🎯 Quick Commands

### Start XAMPP Services:
```
Open XAMPP Control Panel → Start Apache & MySQL
```

### Access phpMyAdmin:
```
http://localhost/phpmyadmin
```

### Access Backend API:
```
http://localhost/backend/
```

### View Logs:
```
C:\xampp\htdocs\backend\logs\activity.log
```

---

**Setup Time**: ~5 minutes
**Difficulty**: Easy
**Support**: Check README.md for detailed help

---

✨ **You're all set! Happy tracking!** ✨
