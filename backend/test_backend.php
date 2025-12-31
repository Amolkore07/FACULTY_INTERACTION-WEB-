<?php
/**
 * Backend Test & Verification Script
 * Trinity Academy - Faculty Performance Tracker
 * 
 * Run this script to verify your backend setup
 * Access: http://localhost/backend/test_backend.php
 */

// Prevent direct output before JSON
ob_start();

// Start output
$results = [];
$allPassed = true;

// Test 1: PHP Version
$results['php_version'] = [
    'test' => 'PHP Version Check',
    'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? 'PASS' : 'FAIL',
    'details' => 'Current: ' . PHP_VERSION . ' (Required: 7.4+)',
    'critical' => true
];
if ($results['php_version']['status'] === 'FAIL') $allPassed = false;

// Test 2: Required Extensions
$requiredExtensions = ['mysqli', 'json'];
foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    $results["extension_$ext"] = [
        'test' => "Extension: $ext",
        'status' => $loaded ? 'PASS' : 'FAIL',
        'details' => $loaded ? 'Loaded' : 'Not loaded',
        'critical' => true
    ];
    if (!$loaded) $allPassed = false;
}

// Test 3: Config File
$configFile = __DIR__ . '/config.php';
$configExists = file_exists($configFile);
$results['config_file'] = [
    'test' => 'Config File',
    'status' => $configExists ? 'PASS' : 'FAIL',
    'details' => $configExists ? 'Found' : 'config.php not found',
    'critical' => true
];
if (!$configExists) {
    $allPassed = false;
} else {
    require_once $configFile;
}

// Test 4: Database Connection
if ($configExists) {
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $dbConnected = !$conn->connect_error;
    $results['database_connection'] = [
        'test' => 'Database Connection',
        'status' => $dbConnected ? 'PASS' : 'FAIL',
        'details' => $dbConnected ? 'Connected to ' . DB_NAME : 'Error: ' . $conn->connect_error,
        'critical' => true
    ];
    if (!$dbConnected) $allPassed = false;
    
    // Test 5: Database Tables
    if ($dbConnected) {
        $tables = ['users', 'feedbacks', 'complaints'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            $exists = $result && $result->num_rows > 0;
            $results["table_$table"] = [
                'test' => "Table: $table",
                'status' => $exists ? 'PASS' : 'FAIL',
                'details' => $exists ? 'Exists' : 'Not found',
                'critical' => true
            ];
            if (!$exists) $allPassed = false;
        }
        
        // Test 6: Sample Data
        $userCount = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
        $results['sample_data'] = [
            'test' => 'Sample Data',
            'status' => $userCount > 0 ? 'PASS' : 'WARNING',
            'details' => "$userCount users found",
            'critical' => false
        ];
        
        $conn->close();
    }
} else {
    $results['database_connection'] = [
        'test' => 'Database Connection',
        'status' => 'SKIP',
        'details' => 'Config file not found',
        'critical' => true
    ];
}

// Test 7: API Endpoints
$apiFiles = [
    'login.php',
    'signup.php',
    'get_faculty.php',
    'submit_feedback.php',
    'get_feedback.php',
    'submit_complaint.php',
    'get_complaints.php'
];

foreach ($apiFiles as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $results["api_$file"] = [
        'test' => "API: $file",
        'status' => $exists ? 'PASS' : 'FAIL',
        'details' => $exists ? 'Found' : 'Missing',
        'critical' => true
    ];
    if (!$exists) $allPassed = false;
}

// Test 8: Logs Directory
$logsDir = __DIR__ . '/logs';
$logsDirWritable = is_dir($logsDir) || @mkdir($logsDir, 0755, true);
$results['logs_directory'] = [
    'test' => 'Logs Directory',
    'status' => $logsDirWritable ? 'PASS' : 'WARNING',
    'details' => $logsDirWritable ? 'Writable' : 'Cannot create/write',
    'critical' => false
];

// Test 9: .htaccess
$htaccessExists = file_exists(__DIR__ . '/.htaccess');
$results['htaccess'] = [
    'test' => '.htaccess File',
    'status' => $htaccessExists ? 'PASS' : 'WARNING',
    'details' => $htaccessExists ? 'Found' : 'Not found (CORS may not work)',
    'critical' => false
];

// Test 10: Apache Modules (if available)
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    $requiredModules = ['mod_rewrite', 'mod_headers'];
    foreach ($requiredModules as $mod) {
        $loaded = in_array($mod, $modules);
        $results["apache_$mod"] = [
            'test' => "Apache: $mod",
            'status' => $loaded ? 'PASS' : 'WARNING',
            'details' => $loaded ? 'Loaded' : 'Not loaded (CORS/routing may not work)',
            'critical' => false
        ];
    }
} else {
    $results['apache_modules'] = [
        'test' => 'Apache Modules',
        'status' => 'SKIP',
        'details' => 'Cannot check (not running under Apache or function disabled)',
        'critical' => false
    ];
}

// Clear output buffer
ob_end_clean();

// Send JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

echo json_encode([
    'overall_status' => $allPassed ? 'PASS' : 'FAIL',
    'timestamp' => date('Y-m-d H:i:s'),
    'tests_run' => count($results),
    'tests_passed' => count(array_filter($results, function($r) { return $r['status'] === 'PASS'; })),
    'tests_failed' => count(array_filter($results, function($r) { return $r['status'] === 'FAIL'; })),
    'tests_warning' => count(array_filter($results, function($r) { return $r['status'] === 'WARNING'; })),
    'results' => $results,
    'recommendations' => $allPassed ? [
        '✅ All critical tests passed!',
        '✅ Backend is ready to use',
        '✅ You can now run the frontend application'
    ] : [
        '❌ Some critical tests failed',
        '1. Check the failed tests above',
        '2. Review the QUICK_START.md guide',
        '3. Verify database setup in phpMyAdmin',
        '4. Check config.php settings',
        '5. Ensure all files are copied correctly'
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
