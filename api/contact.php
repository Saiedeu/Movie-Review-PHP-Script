<?php
/**
 * Contact Form API Endpoint
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../config/config.php';

// Set JSON response header
header('Content-Type: application/json; charset=UTF-8');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'শুধুমাত্র POST অনুরোধ গ্রহণযোগ্য।'
    ]);
    exit;
}

// Rate limiting
if (!checkRateLimit('contact_form', 5, 3600)) { // 5 messages per hour
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => 'অনেক বেশি অনুরোধ। এক ঘন্টা পরে আবার চেষ্টা করুন।'
    ]);
    exit;
}

// Get database instance
$db = Database::getInstance();

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // If JSON input is empty, try form data
    if (empty($input)) {
        $input = $_POST;
    }
    
    // Verify CSRF token
    if (!verifyCSRFToken($input['csrf_token'] ?? '')) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'নিরাপত্তা টোকেন যাচাই করা যায়নি।'
        ]);
        exit;
    }
    
    // Validate required fields
    $required_fields = ['name', 'email', 'message'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'প্রয়োজনীয় ফিল্ড অনুপস্থিত: ' . implode(', ', $missing_fields)
        ]);
        exit;
    }
    
    // Sanitize input data
    $name = sanitize($input['name']);
    $email = sanitize($input['email']);
    $subject = sanitize($input['subject'] ?? 'সাধারণ অনুসন্ধান');
    $message = sanitize($input['message']);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'সঠিক ইমেইল ঠিকানা প্রদান করুন।'
        ]);
        exit;
    }
    
    // Validate message length
    if (strlen($message) < 10) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'বার্তা কমপক্ষে ১০ অক্ষরের হতে হবে।'
        ]);
        exit;
    }
    
    if (strlen($message) > 5000) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'বার্তা সর্বোচ্চ ৫০০০ অক্ষরের হতে পারে।'
        ]);
        exit;
    }
    
    // Check for spam keywords (basic spam protection)
    $spam_keywords = ['viagra', 'casino', 'lottery', 'prize', 'winner', 'congratulations', 'click here', 'buy now'];
    $message_lower = strtolower($message);
    
    foreach ($spam_keywords as $keyword) {
        if (strpos($message_lower, $keyword) !== false) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'আপনার বার্তায় অনুমোদিত নয় এমন শব্দ রয়েছে।'
            ]);
            exit;
        }
    }
    
    // Create contact messages table if not exists
    $db->query("
        CREATE TABLE IF NOT EXISTS contact_messages (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            subject VARCHAR(200),
            message TEXT NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            status ENUM('new', 'read', 'replied') DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Insert contact message
    $result = $db->query("
        INSERT INTO contact_messages (name, email, subject, message, ip_address, user_agent, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ", [
        $name, 
        $email, 
        $subject, 
        $message, 
        getClientIP(), 
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
    
    if (!$result) {
        throw new Exception('বার্তা সংরক্ষণ করতে সমস্যা হয়েছে।');
    }
    
    $message_id = $db->lastInsertId();
    
    // Log contact form submission
    logSecurityEvent('contact_form_submitted', [
        'message_id' => $message_id,
        'name' => $name,
        'email' => $email,
        'subject' => $subject
    ]);
    
    // Send email notification (if configured)
    if (defined('SMTP_HOST') && !empty(SMTP_HOST)) {
        try {
            // Email content
            $email_subject = "নতুন যোগাযোগ বার্তা: " . $subject;
            $email_body = "
নতুন যোগাযোগ বার্তা পেয়েছেন:

নাম: {$name}
ইমেইল: {$email}
বিষয়: {$subject}

বার্তা:
{$message}

IP ঠিকানা: " . getClientIP() . "
সময়: " . date('Y-m-d H:i:s') . "

অ্যাডমিন প্যানেলে গিয়ে উত্তর দিন।
            ";
            
            // Send email (implement your email sending logic here)
            // mail(ADMIN_EMAIL, $email_subject, $email_body);
            
        } catch (Exception $e) {
            // Email sending failed, but don't fail the whole request
            error_log('Email notification failed: ' . $e->getMessage());
        }
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'আপনার বার্তা সফলভাবে পাঠানো হয়েছে। আমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।',
        'message_id' => $message_id,
        'data' => [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log('Contact form error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'সার্ভার ত্রুটি। দয়া করে পরে আবার চেষ্টা করুন।',
        'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
    ]);
}
?>