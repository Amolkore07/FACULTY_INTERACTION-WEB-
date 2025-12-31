<?php
/**
 * Submit Feedback API
 * Trinity Academy - Faculty Performance Tracker
 * 
 * Handles student feedback submission for faculty
 */

require_once 'config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', null, 'Only POST requests are allowed', 405);
}

try {
    // Get POST data
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
    $faculty_id = isset($_POST['faculty_id']) ? intval($_POST['faculty_id']) : 0;
    $subject = isset($_POST['subject']) ? sanitizeInput($_POST['subject']) : '';
    $rating = isset($_POST['rating']) ? floatval($_POST['rating']) : 0;
    $comments = isset($_POST['comments']) ? sanitizeInput($_POST['comments']) : '';
    $feedback_date = isset($_POST['feedback_date']) ? sanitizeInput($_POST['feedback_date']) : date('Y-m-d');
    $feedback_time = isset($_POST['feedback_time']) ? sanitizeInput($_POST['feedback_time']) : date('H:i:s');
    
    // Validate required fields
    if ($student_id <= 0 || $faculty_id <= 0) {
        sendResponse('error', null, 'Invalid student or faculty ID', 400);
    }
    
    if (empty($subject)) {
        sendResponse('error', null, 'Subject is required', 400);
    }
    
    if ($rating < 1 || $rating > 5) {
        sendResponse('error', null, 'Rating must be between 1 and 5', 400);
    }
    
    if (empty($comments)) {
        sendResponse('error', null, 'Comments are required', 400);
    }
    
    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $feedback_date)) {
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
    
    // Verify faculty exists
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
    $stmt->close();
    
    // Insert feedback
    $stmt = $conn->prepare("
        INSERT INTO feedbacks (student_id, faculty_id, subject, rating, comments, feedback_date, feedback_time)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        logActivity("Submit feedback SQL prepare failed: " . $conn->error, "ERROR");
        sendResponse('error', null, 'Database query failed', 500);
    }
    
    $stmt->bind_param("iisdsss", $student_id, $faculty_id, $subject, $rating, $comments, $feedback_date, $feedback_time);
    
    if ($stmt->execute()) {
        $feedback_id = $stmt->insert_id;
        
        // Log successful feedback submission
        logActivity("Feedback submitted: Student '{$student['name']}' rated Faculty '{$faculty['name']}' - Rating: $rating", "INFO");
        
        $feedbackData = [
            'feedback_id' => $feedback_id,
            'student_id' => $student_id,
            'faculty_id' => $faculty_id,
            'subject' => $subject,
            'rating' => $rating,
            'feedback_date' => $feedback_date,
            'feedback_time' => $feedback_time
        ];
        
        $stmt->close();
        closeDBConnection($conn);
        
        sendResponse('success', $feedbackData, 'Feedback submitted successfully', 201);
        
    } else {
        logActivity("Submit feedback failed: " . $stmt->error, "ERROR");
        $stmt->close();
        closeDBConnection($conn);
        sendResponse('error', null, 'Failed to submit feedback. Please try again.', 500);
    }
    
} catch (Exception $e) {
    logActivity("Submit feedback error: " . $e->getMessage(), "ERROR");
    sendResponse('error', null, 'An error occurred while submitting feedback', 500);
}
?>
