<?php
/**
 * Comments API Endpoint
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../config/config.php';

// Set JSON response header
header('Content-Type: application/json; charset=UTF-8');

// Only allow POST and GET requests
if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'GET', 'PUT', 'DELETE'])) {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'অনুরোধের পদ্ধতি সমর্থিত নয়।'
    ]);
    exit;
}

// Get database instance
$db = Database::getInstance();

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $path_info = $_SERVER['PATH_INFO'] ?? '';
    $path_parts = array_filter(explode('/', $path_info));
    
    // Parse request
    if ($method === 'GET') {
        $input = $_GET;
    } else {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = $_POST;
        }
    }
    
    switch ($method) {
        case 'GET':
            handleGetComments($db, $input);
            break;
            
        case 'POST':
            handleAddComment($db, $input);
            break;
            
        case 'PUT':
            handleUpdateComment($db, $input, $path_parts);
            break;
            
        case 'DELETE':
            handleDeleteComment($db, $input, $path_parts);
            break;
    }
    
} catch (Exception $e) {
    error_log('Comments API error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'সার্ভার ত্রুটি। দয়া করে পরে আবার চেষ্টা করুন।',
        'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
    ]);
}

/**
 * Handle GET requests - Fetch comments
 */
function handleGetComments($db, $input) {
    $review_id = intval($input['review_id'] ?? 0);
    $status = sanitize($input['status'] ?? 'approved');
    $page = max(1, intval($input['page'] ?? 1));
    $per_page = min(50, max(5, intval($input['per_page'] ?? 20)));
    $offset = ($page - 1) * $per_page;
    
    if ($review_id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'রিভিউ ID প্রয়োজন।'
        ]);
        return;
    }
    
    // Verify review exists
    $review = $db->fetchOne("SELECT id, title FROM reviews WHERE id = ? AND status = 'published'", [$review_id]);
    if (!$review) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'রিভিউ পাওয়া যায়নি।'
        ]);
        return;
    }
    
    // Get total count
    $total_comments = $db->count("SELECT COUNT(*) FROM comments WHERE review_id = ? AND status = ?", [$review_id, $status]);
    
    // Get comments
    $comments = $db->fetchAll("
        SELECT id, parent_id, name, comment, created_at, status
        FROM comments 
        WHERE review_id = ? AND status = ?
        ORDER BY created_at ASC
        LIMIT ? OFFSET ?
    ", [$review_id, $status, $per_page, $offset]);
    
    // Organize into threads
    $threads = [];
    $replies = [];
    
    foreach ($comments as $comment) {
        $comment['created_at_formatted'] = timeAgo($comment['created_at']);
        $comment['replies'] = [];
        
        if ($comment['parent_id'] === null) {
            $threads[$comment['id']] = $comment;
        } else {
            $replies[$comment['parent_id']][] = $comment;
        }
    }
    
    // Attach replies to parent comments
    foreach ($replies as $parent_id => $parent_replies) {
        if (isset($threads[$parent_id])) {
            $threads[$parent_id]['replies'] = $parent_replies;
        }
    }
    
    // Calculate pagination
    $total_pages = ceil($total_comments / $per_page);
    $has_next = $page < $total_pages;
    $has_prev = $page > 1;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'comments' => array_values($threads),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $per_page,
                'total_comments' => $total_comments,
                'total_pages' => $total_pages,
                'has_next' => $has_next,
                'has_prev' => $has_prev
            ],
            'review' => [
                'id' => $review['id'],
                'title' => $review['title']
            ]
        ]
    ]);
}

/**
 * Handle POST requests - Add new comment
 */
function handleAddComment($db, $input) {
    // Rate limiting for comments
    if (!checkRateLimit('add_comment', 5, 900)) { // 5 comments per 15 minutes
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => 'অনেক বেশি মন্তব্য। ১৫ মিনিট পরে আবার চেষ্টা করুন।'
        ]);
        return;
    }
    
    // Verify CSRF token
    if (!verifyCSRFToken($input['csrf_token'] ?? '')) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'নিরাপত্তা টোকেন যাচাই করা যায়নি।'
        ]);
        return;
    }
    
    // Validate required fields
    $required_fields = ['review_id', 'name', 'email', 'comment'];
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
        return;
    }
    
    // Sanitize and validate
    $review_id = intval($input['review_id']);
    $parent_id = !empty($input['parent_id']) ? intval($input['parent_id']) : null;
    $name = sanitize($input['name']);
    $email = sanitize($input['email']);
    $comment = sanitize($input['comment']);
    $ip_address = getClientIP();
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'সঠিক ইমেইল ঠিকানা প্রদান করুন।'
        ]);
        return;
    }
    
    // Validate comment length
    if (strlen(trim($comment)) < 10) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'মন্তব্য কমপক্ষে ১০ অক্ষরের হতে হবে।'
        ]);
        return;
    }
    
    if (strlen($comment) > 1000) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'মন্তব্য সর্বোচ্চ ১০০০ অক্ষরের হতে পারে।'
        ]);
        return;
    }
    
    // Verify review exists and is published
    $review = $db->fetchOne("SELECT id, title FROM reviews WHERE id = ? AND status = 'published'", [$review_id]);
    if (!$review) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'রিভিউ পাওয়া যায়নি।'
        ]);
        return;
    }
    
    // Verify parent comment exists if provided
    if ($parent_id) {
        $parent_comment = $db->fetchOne("SELECT id FROM comments WHERE id = ? AND review_id = ?", [$parent_id, $review_id]);
        if (!$parent_comment) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'মূল মন্তব্য পাওয়া যায়নি।'
            ]);
            return;
        }
    }
    
    // Check for spam/duplicate comments
    $recent_comment = $db->fetchOne("
        SELECT id FROM comments 
        WHERE email = ? AND comment = ? AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
    ", [$email, $comment]);
    
    if ($recent_comment) {
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => 'একই মন্তব্য সম্প্রতি পোস্ট করা হয়েছে। ৫ মিনিট পরে আবার চেষ্টা করুন।'
        ]);
        return;
    }
    
    // Auto-moderate based on simple rules
    $status = 'pending';
    $comment_lower = strtolower($comment);
    
    // Simple spam detection
    $spam_indicators = ['spam', 'casino', 'loan', 'pharmacy', 'viagra', 'cialis'];
    $is_spam = false;
    foreach ($spam_indicators as $indicator) {
        if (strpos($comment_lower, $indicator) !== false) {
            $is_spam = true;
            break;
        }
    }
    
    if ($is_spam) {
        $status = 'spam';
    } elseif (strlen($comment) > 50 && !preg_match('/[^\w\s]/', $comment)) {
        // Auto-approve longer, clean comments
        $status = 'approved';
    }
    
    // Insert comment
    try {
        $comment_id = $db->query("
            INSERT INTO comments (review_id, parent_id, name, email, comment, status, ip_address, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ", [$review_id, $parent_id, $name, $email, $comment, $status, $ip_address]);
        
        if (!$comment_id) {
            throw new Exception('মন্তব্য সংরক্ষণ করতে সমস্যা হয়েছে।');
        }
        
        $comment_id = $db->lastInsertId();
        
        // Log the comment
        logSecurityEvent('comment_added', [
            'comment_id' => $comment_id,
            'review_id' => $review_id,
            'email' => $email,
            'status' => $status
        ]);
        
        $message = $status === 'approved' 
            ? 'আপনার মন্তব্য সফলভাবে যোগ করা হয়েছে।'
            : 'আপনার মন্তব্য সফলভাবে জমা দেওয়া হয়েছে। অনুমোদনের পর প্রকাশিত হবে।';
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => [
                'comment_id' => $comment_id,
                'status' => $status,
                'auto_approved' => $status === 'approved'
            ]
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'মন্তব্য সংরক্ষণ করতে সমস্যা হয়েছে।'
        ]);
    }
}

/**
 * Handle PUT requests - Update comment (Admin only)
 */
function handleUpdateComment($db, $input, $path_parts) {
    // Check admin authentication
    if (!isAdminAuthenticated()) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'অ্যাডমিন প্রমাণীকরণ প্রয়োজন।'
        ]);
        return;
    }
    
    $comment_id = intval($path_parts[0] ?? 0);
    
    if ($comment_id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'মন্তব্য ID প্রয়োজন।'
        ]);
        return;
    }
    
    // Verify comment exists
    $comment = $db->fetchOne("SELECT * FROM comments WHERE id = ?", [$comment_id]);
    if (!$comment) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'মন্তব্য পাওয়া যায়নি।'
        ]);
        return;
    }
    
    // Update allowed fields
    $allowed_fields = ['status', 'comment'];
    $updates = [];
    $params = [];
    
    foreach ($allowed_fields as $field) {
        if (isset($input[$field])) {
            $updates[] = "$field = ?";
            if ($field === 'status') {
                $params[] = in_array($input[$field], ['approved', 'pending', 'spam']) ? $input[$field] : 'pending';
            } else {
                $params[] = sanitize($input[$field]);
            }
        }
    }
    
    if (empty($updates)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'আপডেট করার মতো কোনো ফিল্ড নেই।'
        ]);
        return;
    }
    
    $params[] = $comment_id;
    
    $updated = $db->query(
        "UPDATE comments SET " . implode(', ', $updates) . " WHERE id = ?",
        $params
    );
    
    if ($updated) {
        echo json_encode([
            'success' => true,
            'message' => 'মন্তব্য সফলভাবে আপডেট করা হয়েছে।'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'মন্তব্য আপডেট করতে সমস্যা হয়েছে।'
        ]);
    }
}

/**
 * Handle DELETE requests - Delete comment (Admin only)
 */
function handleDeleteComment($db, $input, $path_parts) {
    // Check admin authentication
    if (!isAdminAuthenticated()) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'অ্যাডমিন প্রমাণীকরণ প্রয়োজন।'
        ]);
        return;
    }
    
    $comment_id = intval($path_parts[0] ?? 0);
    
    if ($comment_id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'মন্তব্য ID প্রয়োজন।'
        ]);
        return;
    }
    
    // Verify comment exists
    $comment = $db->fetchOne("SELECT * FROM comments WHERE id = ?", [$comment_id]);
    if (!$comment) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'মন্তব্য পাওয়া যায়নি।'
        ]);
        return;
    }
    
    // Delete comment and its replies
    $db->beginTransaction();
    
    try {
        // Delete replies first
        $db->query("DELETE FROM comments WHERE parent_id = ?", [$comment_id]);
        
        // Delete the comment
        $deleted = $db->query("DELETE FROM comments WHERE id = ?", [$comment_id]);
        
        if (!$deleted) {
            throw new Exception('মন্তব্য মুছতে সমস্যা হয়েছে।');
        }
        
        $db->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'মন্তব্য সফলভাবে মুছে ফেলা হয়েছে।'
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'মন্তব্য মুছতে সমস্যা হয়েছে।'
        ]);
    }
}

/**
 * Helper functions
 */

function checkRateLimit($action, $limit, $window) {
    $ip = getClientIP();
    $key = $action . '_' . md5($ip);
    $cache_file = sys_get_temp_dir() . '/rate_limit_' . $key;
    
    $current_time = time();
    $requests = [];
    
    if (file_exists($cache_file)) {
        $data = file_get_contents($cache_file);
        $requests = json_decode($data, true) ?: [];
    }
    
    // Remove old requests outside the window
    $requests = array_filter($requests, function($timestamp) use ($current_time, $window) {
        return ($current_time - $timestamp) < $window;
    });
    
    // Check if limit exceeded
    if (count($requests) >= $limit) {
        return false;
    }
    
    // Add current request
    $requests[] = $current_time;
    
    // Save to cache
    file_put_contents($cache_file, json_encode($requests));
    
    return true;
}

function logSecurityEvent($event_type, $details = []) {
    $db = Database::getInstance();
    
    $db->query("
        INSERT INTO security_logs (event_type, ip_address, user_agent, details, created_at) 
        VALUES (?, ?, ?, ?, NOW())
    ", [
        $event_type,
        getClientIP(),
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        json_encode($details)
    ]);
}

function isAdminAuthenticated() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
?>