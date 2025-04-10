<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Create logs directory if it doesn't exist
if (!file_exists(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0777, true);
}

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error = [
        'timestamp' => date('Y-m-d H:i:s'),
        'type' => getErrorType($errno),
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline,
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown URI',
        'user_id' => $_SESSION['id'] ?? 'Not logged in'
    ];
    
    // Log error
    error_log(json_encode($error) . "\n", 3, __DIR__ . '/../logs/error.log');
    
    // Return true to prevent PHP's internal error handler
    return true;
}

// Custom exception handler
function customExceptionHandler($exception) {
    $error = [
        'timestamp' => date('Y-m-d H:i:s'),
        'type' => 'Exception',
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString(),
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown URI',
        'user_id' => $_SESSION['id'] ?? 'Not logged in'
    ];
    
    // Log exception
    error_log(json_encode($error) . "\n", 3, __DIR__ . '/../logs/error.log');
    
    // Return JSON response for API requests
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'An internal error occurred']);
    } else {
        // Display user-friendly error page
        include __DIR__ . '/../error.html';
    }
}

// Helper function to get error type string
function getErrorType($type) {
    switch($type) {
        case E_ERROR:
            return 'E_ERROR';
        case E_WARNING:
            return 'E_WARNING';
        case E_PARSE:
            return 'E_PARSE';
        case E_NOTICE:
            return 'E_NOTICE';
        case E_CORE_ERROR:
            return 'E_CORE_ERROR';
        case E_CORE_WARNING:
            return 'E_CORE_WARNING';
        case E_COMPILE_ERROR:
            return 'E_COMPILE_ERROR';
        case E_COMPILE_WARNING:
            return 'E_COMPILE_WARNING';
        case E_USER_ERROR:
            return 'E_USER_ERROR';
        case E_USER_WARNING:
            return 'E_USER_WARNING';
        case E_USER_NOTICE:
            return 'E_USER_NOTICE';
        case E_STRICT:
            return 'E_STRICT';
        case E_RECOVERABLE_ERROR:
            return 'E_RECOVERABLE_ERROR';
        case E_DEPRECATED:
            return 'E_DEPRECATED';
        case E_USER_DEPRECATED:
            return 'E_USER_DEPRECATED';
        default:
            return 'UNKNOWN';
    }
}

// Set custom error and exception handlers
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');

// Function to log custom messages
function logMessage($message, $type = 'INFO', $context = []) {
    $log = [
        'timestamp' => date('Y-m-d H:i:s'),
        'type' => $type,
        'message' => $message,
        'context' => $context,
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown URI',
        'user_id' => $_SESSION['id'] ?? 'Not logged in'
    ];
    
    error_log(json_encode($log) . "\n", 3, __DIR__ . '/../logs/application.log');
}
?> 