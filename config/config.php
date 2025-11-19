<?php
/**
 * MSR - Movie & Series Review Website
 * Main Configuration File
 */

// Prevent direct access
if (!defined('MSR_ACCESS')) {
    die('Direct access not permitted.');
}

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Dhaka');

// Database Configuration
define('DB_HOST', 'sql300.infinityfree.com');
define('DB_NAME', 'if0_39024958_CineReview');
define('DB_USER', 'if0_39024958');
define('DB_PASS', 'SaidurRahman10');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

// Site Configuration
define('SITE_NAME', 'MSR - Movie & Series Review');
define('SITE_URL', 'https://cine-review.rf.gd'); // Change this to your domain
define('SITE_DESCRIPTION', 'বাংলা ভাষায় সেরা সিনেমা ও সিরিয়াল রিভিউ');
define('SITE_KEYWORDS', 'movie review, series review, বাংলা রিভিউ, সিনেমা, ড্রামা');

// Path Configuration
define('BASE_PATH', dirname(__DIR__));
define('ADMIN_PATH', BASE_PATH . '/admin');
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('ASSETS_PATH', BASE_PATH . '/assets');
define('UPLOADS_PATH', ASSETS_PATH . '/uploads');

// URL Configuration
define('ASSETS_URL', SITE_URL . '/assets');
define('UPLOADS_URL', ASSETS_URL . '/uploads');

// Security
define('HASH_COST', 12);
define('SESSION_LIFETIME', 3600); // 1 hour
define('CSRF_TOKEN_LENGTH', 32);

// TinyMCE Configuration
define('TINYMCE_API_KEY', 'hhiyirqkh3fnrmgjs7nq6tpk6nqb62m3vww7smgrz7kjfv6v');

// Upload Configuration
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'webp']);

// Pagination
define('POSTS_PER_PAGE', 12);
define('ADMIN_POSTS_PER_PAGE', 20);

// Admin Credentials (for initial setup)
define('ADMIN_USERNAME', 'MSR');
define('ADMIN_EMAIL', 'msa.masum.bd@gmail.com');
define('ADMIN_PASSWORD_HASH', password_hash('MasumAhmed@10', PASSWORD_DEFAULT, ['cost' => HASH_COST]));

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/seo.php';
?>