<?php
/**
 * User Registration API
 * Trinity Academy - Faculty Performance Tracker
 * 
 * Handles new user registration (primarily for students)
 */

require_once 'config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', null, 'Only POST requests are allowed', 405);
}

try {
    // Get POST data
    $role = isset($_POST['role']) ? sanitizeInput($_POST['role']) : '';
    $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Role-specific fields
    $enrollment_no = isset($_POST['enrollment_no']) ? sanitizeInput($_POST['enrollment_no']) : null;
    $class = isset($_POST['class']) ? sanitizeInput($_POST['class']) : null;
    $subject = isset($_POST['subject']) ? sanitizeInput($_POST['subject']) : null;
    $department = isset($_POST['department']) ? sanitizeInput($_POST['department']) : null;
    
    // Validate required fields
    if (empty($role) || empty($name) || empty($email) || empty($password)) {
        sendResponse('error', null, 'All required fields must be filled', 400);
    }
    
    // Validate email
    if (!validateEmail($email)) {
        sendResponse('error', null, 'Invalid email format', 400);
    }
    
    // Validate password length
    if (strlen($password) < 6) {
        sendResponse('error', null, 'Password must be at least 6 characters long', 400);
    }
    
    // Validate role
    $allowedRoles = ['student', 'faculty', 'hod'];
    if (!in_array($role, $allowedRoles)) {
        sendResponse('error', null, 'Invalid role selected', 400);
    }
    
    // Note: In production, you may want to restrict faculty and HOD registration
    if ($role === 'faculty' || $role === 'hod') {
        // Uncomment to restrict:
        // sendResponse('error', null, 'Only students can self-register. Contact admin for faculty/HOD accounts.', 403);
    }
    
    // Get database connection
    $conn = getDBConnection();
    if (!$conn) {
        sendResponse('error', null, 'Database connection failed', 500);
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        closeDBConnection($conn);
        sendResponse('error', null, 'Email already registered', 409);
    }
    $stmt->close();
    
    // Hash password
    $hashedPassword = hashPassword($password);
    
    // Insert new user
    $stmt = $conn->prepare("
        INSERT INTO users (name, email, password, role, enrollment_no, class, subject, department)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        logActivity("Signup SQL prepare failed: " . $conn->error, "ERROR");
        sendResponse('error', null, 'Database query failed', 500);
    }
    
    $stmt->bind_param("ssssssss", $name, $email, $hashedPassword, $role, $enrollment_no, $class, $subject, $department);
    
    if ($stmt->execute()) {
        $userId = $stmt->insert_id;
        
        // Log successful registration
        logActivity("New user registered: $name (email: $email, role: $role)", "INFO");
        
        // Prepare user data for response
        $userData = [
            'id' => $userId,
            'user_id' => $userId,
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'enrollment_no' => $enrollment_no,
            'class' => $class,
            'subject' => $subject,
            'department' => $department
        ];
        
        $stmt->close();
        closeDBConnection($conn);
        
        sendResponse('success', $userData, 'Registration successful! You can now login.', 201);
        
    } else {
        logActivity("Signup failed for $email: " . $stmt->error, "ERROR");
        $stmt->close();
        closeDBConnection($conn);
        sendResponse('error', null, 'Registration failed. Please try again.', 500);
    }
    
} catch (Exception $e) {
    logActivity("Signup error: " . $e->getMessage(), "ERROR");
    sendResponse('error', null, 'An error occurred during registration', 500);
}
?>
