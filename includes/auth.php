<?php
/**
 * Authentication Functions
 */

if (!defined('MSR_ACCESS')) {
    die('Direct access not permitted.');
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Get current admin user
 */
function getCurrentAdmin() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['admin_id'] ?? null,
        'username' => $_SESSION['admin_username'] ?? null,
        'email' => $_SESSION['admin_email'] ?? null,
        'role' => $_SESSION['admin_role'] ?? null,
        'login_time' => $_SESSION['login_time'] ?? null
    ];
}

/**
 * Require admin authentication
 */
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
    
    // Check session timeout
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > SESSION_LIFETIME) {
        logout();
        header('Location: login.php?timeout=1');
        exit;
    }
    
    // Update last activity
    $_SESSION['login_time'] = time();
}

/**
 * Logout user
 */
function logout() {
    // Clear remember token from database
    if (isLoggedIn()) {
        $db = Database::getInstance();
        $db->query("UPDATE users SET remember_token = NULL WHERE id = ?", [$_SESSION['admin_id']]);
    }
    
    // Clear session
    $_SESSION = [];
    session_destroy();
    
    // Clear remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
}

/**
 * Check remember me token
 */
function checkRememberToken() {
    if (isLoggedIn() || !isset($_COOKIE['remember_token'])) {
        return false;
    }
    
    $token = $_COOKIE['remember_token'];
    $hashed_token = hash('sha256', $token);
    
    $db = Database::getInstance();
    $user = $db->fetchOne("SELECT * FROM users WHERE remember_token = ? AND status = 'active'", [$hashed_token]);
    
    if ($user) {
        // Auto-login user
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_email'] = $user['email'];
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['login_time'] = time();
        
        return true;
    } else {
        // Invalid token, clear cookie
        setcookie('remember_token', '', time() - 3600, '/');
        return false;
    }
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => HASH_COST]);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if user has permission
 */
function hasPermission($permission) {
    $admin = getCurrentAdmin();
    if (!$admin) {
        return false;
    }
    
    // Admin role has all permissions
    if ($admin['role'] === 'admin') {
        return true;
    }
    
    // Add more role-based permissions here
    return false;
}

/**
 * Require specific permission
 */
function requirePermission($permission) {
    if (!hasPermission($permission)) {
        header('HTTP/1.1 403 Forbidden');
        die('Access denied');
    }
}

/**
 * Generate secure random token
 */
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Rate limiting check
 */
function checkRateLimit($action, $limit = 5, $window = 300) {
    $ip = getClientIP();
    $key = "rate_limit_{$action}_{$ip}";
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'reset' => time() + $window];
    }
    
    $data = $_SESSION[$key];
    
    // Reset if window expired
    if (time() > $data['reset']) {
        $_SESSION[$key] = ['count' => 1, 'reset' => time() + $window];
        return true;
    }
    
    // Check limit
    if ($data['count'] >= $limit) {
        return false;
    }
    
    // Increment counter
    $_SESSION[$key]['count']++;
    return true;
}

/**
 * Log security event
 */
function logSecurityEvent($event, $details = []) {
    $db = Database::getInstance();
    $db->query(
        "INSERT INTO security_logs (event_type, ip_address, user_agent, details, created_at) VALUES (?, ?, ?, ?, NOW())",
        [
            $event,
            getClientIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            json_encode($details, JSON_UNESCAPED_UNICODE)
        ]
    );
}
?>