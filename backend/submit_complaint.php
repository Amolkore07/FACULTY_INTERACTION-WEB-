<?php
/**
 * Submit Complaint API
 * Trinity Academy - Faculty Performance Tracker
 * 
 * Handles student complaint submission
 */

require_once 'config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', null, 'Only POST requests are allowed', 405);
}

try {
    // Get POST data
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
    $faculty_id = isset($_POST['faculty_id']) ? (empty($_POST['faculty_id']) ? null : intval($_POST['faculty_id'])) : null;
    $description = isset($_POST['description']) ? sanitizeInput($_POST['description']) : '';
    $complaint_date = isset($_POST['complaint_date']) ? sanitizeInput($_POST['complaint_date']) : date('Y-m-d');
    $complaint_time = isset($_POST['complaint_time']) ? sanitizeInput($_POST['complaint_time']) : date('H:i:s');
    $status = 'pending'; // Default status
    
    // Validate required fields
    if ($student_id <= 0) {
        sendResponse('error', null, 'Invalid student ID', 400);
    }
    
    if (empty($description)) {
        sendResponse('error', null, 'Complaint description is required', 400);
    }
    
    if (strlen($description) < 10) {
        sendResponse('error', null, 'Complaint description must be at least 10 characters', 400);
    }
    
    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $complaint_date)) {
        sendResponse('error', null, 'Invalid date format', 400);
    }
    
    // Get database connection
    $conn = getDBConnection();
    if (!$conn) {
        sendResponse('error', null, 'Database connection failed', 500);
    }
    
    // Verify student exists
    $stmt = $conn->prepare("SELECT user_id, name FROM users WHERE user_id = ? AND role = 'student'");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        closeDBConnection($conn);
        sendResponse('error', null, 'Invalid student ID', 404);
    }
    $student = $result->fetch_assoc();
    $stmt->close();
    
    // If faculty_id is provided, verify faculty exists
    $faculty_name = 'N/A';
    if ($faculty_id !== null && $faculty_id > 0) {
        $stmt = $conn->prepare("SELECT user_id, name FROM users WHERE user_id = ? AND role = 'faculty'");
        $stmt->bind_param("i", $faculty_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            closeDBConnection($conn);
            sendResponse('error', null, 'Invalid faculty ID', 404);
        }
        $faculty = $result->fetch_assoc();
        $faculty_name = $faculty['name'];
        $stmt->close();
    }
    
    // Insert complaint
    $stmt = $conn->prepare("
        INSERT INTO complaints (student_id, faculty_id, description, complaint_date, complaint_time, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        logActivity("Submit complaint SQL prepare failed: " . $conn->error, "ERROR");
        sendResponse('error', null, 'Database query failed', 500);
    }
    
    $stmt->bind_param("sissss", $student_id, $faculty_id, $description, $complaint_date, $complaint_time, $status);
    
    if ($stmt->execute()) {
        $complaint_id = $stmt->insert_id;
        
        // Extract priority from description for logging
        $priority = 'Unknown';
        if (strpos($description, 'Priority: High') !== false) {
            $priority = 'High';
        } elseif (strpos($description, 'Priority: Medium') !== false) {
            $priority = 'Medium';
        } elseif (strpos($description, 'Priority: Low') !== false) {
            $priority = 'Low';
        }
        
        // Log successful complaint submission
        logActivity("Complaint submitted: Student '{$student['name']}' (Priority: $priority, Faculty: $faculty_name)", "WARNING");
        
        $complaintData = [
            'complaint_id' => $complaint_id,
            'student_id' => $student_id,
            'faculty_id' => $faculty_id,
            'complaint_date' => $complaint_date,
            'complaint_time' => $complaint_time,
            'status' => $status
        ];
        
        $stmt->close();
        closeDBConnection($conn);
        
        sendResponse('success', $complaintData, 'Complaint submitted successfully', 201);
        
    } else {
        logActivity("Submit complaint failed: " . $stmt->error, "ERROR");
        $stmt->close();
        closeDBConnection($conn);
        sendResponse('error', null, 'Failed to submit complaint. Please try again.', 500);
    }
    
} catch (Exception $e) {
    logActivity("Submit complaint error: " . $e->getMessage(), "ERROR");
    sendResponse('error', null, 'An error occurred while submitting complaint', 500);
}
?>
