<?php
/**
 * User Login API
 * Trinity Academy - Faculty Performance Tracker
 * 
 * Handles user authentication for students, faculty, and HOD
 */

require_once 'config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', null, 'Only POST requests are allowed', 405);
}

try {
    // Get POST data
    $role = isset($_POST['role']) ? sanitizeInput($_POST['role']) : '';
    $identifier = isset($_POST['identifier']) ? sanitizeInput($_POST['identifier']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validate input
    if (empty($role) || empty($identifier) || empty($password)) {
        sendResponse('error', null, 'All fields are required', 400);
    }
    
    // Validate role
    $allowedRoles = ['student', 'faculty', 'hod'];
    if (!in_array($role, $allowedRoles)) {
        sendResponse('error', null, 'Invalid role selected', 400);
    }
    
    // Get database connection
    $conn = getDBConnection();
    if (!$conn) {
        sendResponse('error', null, 'Database connection failed', 500);
    }
    
    // Prepare SQL query - identifier can be email or name
    $stmt = $conn->prepare("
        SELECT user_id, name, email, password, role, enrollment_no, class, subject, department
        FROM users 
        WHERE (email = ? OR name = ?) AND role = ?
        LIMIT 1
    ");
    
    if (!$stmt) {
        logActivity("Login SQL prepare failed: " . $conn->error, "ERROR");
        sendResponse('error', null, 'Database query failed', 500);
    }
    
    $stmt->bind_param("sss", $identifier, $identifier, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if user exists
    if ($result->num_rows === 0) {
        logActivity("Failed login attempt for: $identifier (role: $role)", "WARNING");
        sendResponse('error', null, 'Invalid credentials or role mismatch', 401);
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password
    if (!verifyPassword($password, $user['password'])) {
        logActivity("Failed login attempt - wrong password for: $identifier", "WARNING");
        sendResponse('error', null, 'Invalid credentials', 401);
    }
    
    // Remove password from response
    unset($user['password']);
    
    // Add user ID as 'id' for frontend compatibility
    $user['id'] = $user['user_id'];
    
    // Log successful login
    logActivity("Successful login: {$user['name']} (role: {$user['role']})", "INFO");
    
    // Close connection
    $stmt->close();
    closeDBConnection($conn);
    
    // Send success response
    sendResponse('success', $user, 'Login successful', 200);
    
} catch (Exception $e) {
    logActivity("Login error: " . $e->getMessage(), "ERROR");
    sendResponse('error', null, 'An error occurred during login', 500);
}
?>
