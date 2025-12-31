<?php
/**
 * Get Complaints API
 * Trinity Academy - Faculty Performance Tracker
 * 
 * Retrieves complaints based on role and filters
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
    $status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
    
    // Get database connection
    $conn = getDBConnection();
    if (!$conn) {
        sendResponse('error', null, 'Database connection failed', 500);
    }
    
    // Build query based on role
    $query = "
        SELECT 
            c.complaint_id,
            c.student_id,
            s.name AS student_name,
            c.faculty_id,
            fac.name AS faculty_name,
            c.description,
            c.complaint_date,
            c.complaint_time,
            c.status,
            c.created_at AS date,
            c.updated_at
        FROM complaints c
        JOIN users s ON c.student_id = s.user_id
        LEFT JOIN users fac ON c.faculty_id = fac.user_id
    ";
    
    $conditions = [];
    $params = [];
    $types = "";
    
    // Add conditions based on role and filters
    if ($role === 'faculty' && $faculty_id > 0) {
        // Faculty viewing complaints about them
        $conditions[] = "c.faculty_id = ?";
        $params[] = $faculty_id;
        $types .= "i";
    } elseif ($role === 'student' && $student_id > 0) {
        // Student viewing their submitted complaints
        $conditions[] = "c.student_id = ?";
        $params[] = $student_id;
        $types .= "i";
    } elseif ($role === 'hod') {
        // HOD can view all complaints
        // Optional: filter by faculty_id if provided
        if ($faculty_id > 0) {
            $conditions[] = "c.faculty_id = ?";
            $params[] = $faculty_id;
            $types .= "i";
        }
    } else {
        // Default: return empty if no valid role/filter
        closeDBConnection($conn);
        sendResponse('success', [], 'No complaints found', 200);
    }
    
    // Add status filter if provided
    if (!empty($status) && in_array($status, ['pending', 'resolved', 'in_progress'])) {
        $conditions[] = "c.status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    // Add WHERE clause if conditions exist
    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    
    // Order by most recent first
    $query .= " ORDER BY c.created_at DESC";
    
    // Prepare and execute query
    if (count($params) > 0) {
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            logActivity("Get complaints SQL prepare failed: " . $conn->error, "ERROR");
            sendResponse('error', null, 'Database query failed', 500);
        }
        
        // Bind parameters dynamically
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($query);
        if (!$result) {
            logActivity("Get complaints query failed: " . $conn->error, "ERROR");
            sendResponse('error', null, 'Failed to retrieve complaints', 500);
        }
    }
    
    // Fetch all complaints
    $complaints = [];
    while ($row = $result->fetch_assoc()) {
        // For faculty and HOD viewing all complaints, anonymize student names if needed
        if ($role === 'hod' || ($role === 'faculty' && $faculty_id > 0)) {
            $row['student_name'] = 'Anonymous Student';
        }
        
        // Handle null faculty
        if ($row['faculty_name'] === null) {
            $row['faculty_name'] = '';
        }
        
        $complaints[] = $row;
    }
    
    // Close connection
    if (isset($stmt)) {
        $stmt->close();
    }
    closeDBConnection($conn);
    
    // Log activity
    logActivity("Complaints retrieved: " . count($complaints) . " records (role: $role)", "INFO");
    
    // Send response
    sendResponse('success', $complaints, 'Complaints retrieved successfully', 200);
    
} catch (Exception $e) {
    logActivity("Get complaints error: " . $e->getMessage(), "ERROR");
    sendResponse('error', null, 'An error occurred while retrieving complaints', 500);
}
?>
