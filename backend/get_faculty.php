<?php
/**
 * Get Faculty List API
 * Trinity Academy - Faculty Performance Tracker
 * 
 * Returns list of all faculty members
 */

require_once 'config.php';

// Allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse('error', null, 'Only GET requests are allowed', 405);
}

try {
    // Get database connection
    $conn = getDBConnection();
    if (!$conn) {
        sendResponse('error', null, 'Database connection failed', 500);
    }
    
    // Get all faculty members
    $query = "
        SELECT 
            user_id as faculty_id, 
            name, 
            email, 
            subject, 
            department,
            created_at
        FROM users 
        WHERE role = 'faculty'
        ORDER BY name ASC
    ";
    
    $result = $conn->query($query);
    
    if (!$result) {
        logActivity("Get faculty query failed: " . $conn->error, "ERROR");
        sendResponse('error', null, 'Failed to retrieve faculty list', 500);
    }
    
    $faculty = [];
    while ($row = $result->fetch_assoc()) {
        $faculty[] = $row;
    }
    
    // Close connection
    closeDBConnection($conn);
    
    // Log activity
    logActivity("Faculty list retrieved: " . count($faculty) . " members", "INFO");
    
    // Send response
    if (count($faculty) > 0) {
        sendResponse('success', $faculty, 'Faculty list retrieved successfully', 200);
    } else {
        sendResponse('success', [], 'No faculty members found', 200);
    }
    
} catch (Exception $e) {
    logActivity("Get faculty error: " . $e->getMessage(), "ERROR");
    sendResponse('error', null, 'An error occurred while retrieving faculty list', 500);
}
?>
