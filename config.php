<?php
// Application Configuration
define('APP_NAME', 'ویو کلاب کنگان');
define('BASE_PATH', dirname(__FILE__));

// Auto-detect APP_URL from the directory this script runs in
define('APP_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'wave_club');
define('DB_USER', 'root');
define('DB_PASS', '');

// Session
define('SESSION_LIFETIME', 7200); // 2 hours

// Debugging (set to false in production)
define('APP_DEBUG', true);

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Tehran');

// Include helper functions (not classes, so autoloader won't load them)
require_once BASE_PATH . '/includes/Helpers.php';
require_once BASE_PATH . '/includes/Database.php';
require_once BASE_PATH . '/includes/Auth.php';
require_once BASE_PATH . '/includes/CSRF.php';
require_once BASE_PATH . '/includes/Jalali.php';

// Autoload class includes (for any future classes)
spl_autoload_register(function ($class) {
    $file = BASE_PATH . '/includes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}