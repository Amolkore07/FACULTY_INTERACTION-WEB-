<?php
/**
 * Get Feedback API
 * Trinity Academy - Faculty Performance Tracker
 * 
 * Retrieves feedback based on role and filters
 */

require_once 'config.php';

// Allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse('error', null, 'Only GET requests are allowed', 405);
}

try {
    // Get query parameters
    $role = isset($_GET['role']) ? sanitizeInput($_GET['role']) : '';
    $faculty_id = isset($_GET['faculty_id']) ? intval($_GET['faculty_id']) : 0;
    $student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
    
    // Get database connection
    $conn = getDBConnection();
    if (!$conn) {
        sendResponse('error', null, 'Database connection failed', 500);
    }
    
    // Build query based on role
    $query = "
        SELECT 
            f.feedback_id,
            f.student_id,
            s.name AS student_name,
            f.faculty_id,
            fac.name AS faculty_name,
            f.subject,
            f.rating,
            f.comments,
            f.feedback_date,
            f.feedback_time,
            f.created_at AS timestamp
        FROM feedbacks f
        JOIN users s ON f.student_id = s.user_id
        JOIN users fac ON f.faculty_id = fac.user_id
    ";
    
    $conditions = [];
    $params = [];
    $types = "";
    
    // Add conditions based on role and filters
    if ($role === 'faculty' && $faculty_id > 0) {
        // Faculty viewing their own feedback
        $conditions[] = "f.faculty_id = ?";
        $params[] = $faculty_id;
        $types .= "i";
    } elseif ($role === 'student' && $student_id > 0) {
        // Student viewing their submitted feedback
        $conditions[] = "f.student_id = ?";
        $params[] = $student_id;
        $types .= "i";
    } elseif ($role === 'hod') {
        // HOD can view all feedback
        // No additional conditions needed
    } else {
        // Default: return empty if no valid role/filter
        closeDBConnection($conn);
        sendResponse('success', [], 'No feedback found', 200);
    }
    
    // Add WHERE clause if conditions exist
    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    
    // Order by most recent first
    $query .= " ORDER BY f.created_at DESC";
    
    // Prepare and execute query
    if (count($params) > 0) {
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            logActivity("Get feedback SQL prepare failed: " . $conn->error, "ERROR");
            sendResponse('error', null, 'Database query failed', 500);
        }
        
        // Bind parameters dynamically
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($query);
        if (!$result) {
            logActivity("Get feedback query failed: " . $conn->error, "ERROR");
            sendResponse('error', null, 'Failed to retrieve feedback', 500);
        }
    }
    
    // Fetch all feedback
    $feedbacks = [];
    while ($row = $result->fetch_assoc()) {
        // For faculty and HOD viewing all feedback, anonymize student names
        if ($role === 'hod' || ($role === 'faculty' && $faculty_id > 0)) {
            $row['student_name'] = 'Anonymous Student';
        }
        $feedbacks[] = $row;
    }
    
    // Close connection
    if (isset($stmt)) {
        $stmt->close();
    }
    closeDBConnection($conn);
    
    // Log activity
    logActivity("Feedback retrieved: " . count($feedbacks) . " records (role: $role)", "INFO");
    
    // Send response
    sendResponse('success', $feedbacks, 'Feedback retrieved successfully', 200);
    
} catch (Exception $e) {
    logActivity("Get feedback error: " . $e->getMessage(), "ERROR");
    sendResponse('error', null, 'An error occurred while retrieving feedback', 500);
}
?>
