<?php
/**
 * Database Configuration File
 * Trinity Academy - Faculty Performance Tracker
 * 
 * This file contains database connection settings and common functions
 */

// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // Change this to your MySQL username
define('DB_PASS', '');               // Change this to your MySQL password
define('DB_NAME', 'faculty_tracker');
define('DB_CHARSET', 'utf8mb4');

// Application Settings
define('APP_NAME', 'Trinity Academy Faculty Tracker');
define('APP_VERSION', '1.0.0');
define('TIMEZONE', 'Asia/Kolkata');

// Set timezone
date_default_timezone_set(TIMEZONE);

// CORS Headers - Allow frontend to access API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * Create database connection
 * @return mysqli|null Database connection object or null on failure
 */
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        // Set charset
        $conn->set_charset(DB_CHARSET);
        
        return $conn;
        
    } catch (Exception $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        return null;
    }
}

/**
 * Send JSON response
 * @param string $status 'success' or 'error'
 * @param mixed $data Data to send
 * @param string $message Optional message
 * @param int $httpCode HTTP status code
 */
function sendResponse($status, $data = null, $message = '', $httpCode = 200) {
    http_response_code($httpCode);
    
    $response = [
        'status' => $status,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

/**
 * Sanitize input data
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email address
 * @param string $email Email to validate
 * @return bool True if valid, false otherwise
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Hash password securely
 * @param string $password Plain text password
 * @return string Hashed password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Verify password
 * @param string $password Plain text password
 * @param string $hash Hashed password
 * @return bool True if password matches, false otherwise
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Log activity to file
 * @param string $message Log message
 * @param string $level Log level (INFO, WARNING, ERROR)
 */
function logActivity($message, $level = 'INFO') {
    $logFile = __DIR__ . '/logs/activity.log';
    $logDir = dirname($logFile);
    
    // Create logs directory if it doesn't exist
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * Check if user is authenticated (basic check)
 * @param array $postData POST data containing user credentials
 * @return bool True if authenticated
 */
function isAuthenticated($postData) {
    // This is a basic implementation
    // In production, use sessions or JWT tokens
    return isset($postData['user_id']) && !empty($postData['user_id']);
}

/**
 * Generate random token
 * @param int $length Token length
 * @return string Random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Close database connection
 * @param mysqli $conn Database connection
 */
function closeDBConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Test database connection on include (optional, comment out in production)
$testConn = getDBConnection();
if ($testConn) {
    closeDBConnection($testConn);
    logActivity("Database connection successful", "INFO");
} else {
    logActivity("Database connection failed", "ERROR");
}
?>
