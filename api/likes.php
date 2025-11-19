<?php
/**
 * Likes API Endpoint
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
if (!checkRateLimit('toggle_like', 10, 60)) { // 10 likes per minute
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => 'অনেক বেশি অনুরোধ। একটু পরে আবার চেষ্টা করুন।'
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
    
    $action = sanitize($input['action'] ?? '');
    $review_id = intval($input['review_id'] ?? 0);
    
    if ($action !== 'toggle_like' || $review_id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'অবৈধ অনুরোধ।'
        ]);
        exit;
    }
    
    // Verify review exists and is published
    $review = $db->fetchOne("SELECT id, like_count FROM reviews WHERE id = ? AND status = 'published'", [$review_id]);
    if (!$review) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'রিভিউ পাওয়া যায়নি।'
        ]);
        exit;
    }
    
    $ip_address = getClientIP();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Check if already liked
    $existing_like = $db->fetchOne("SELECT id FROM review_likes WHERE review_id = ? AND ip_address = ?", [$review_id, $ip_address]);
    
    $db->beginTransaction();
    
    try {
        if ($existing_like) {
            // Remove like
            $db->query("DELETE FROM review_likes WHERE review_id = ? AND ip_address = ?", [$review_id, $ip_address]);
            $db->query("UPDATE reviews SET like_count = GREATEST(like_count - 1, 0) WHERE id = ?", [$review_id]);
            $liked = false;
            $action_performed = 'unliked';
        } else {
            // Add like
            $db->query("INSERT INTO review_likes (review_id, ip_address, user_agent) VALUES (?, ?, ?)", [$review_id, $ip_address, $user_agent]);
            $db->query("UPDATE reviews SET like_count = like_count + 1 WHERE id = ?", [$review_id]);
            $liked = true;
            $action_performed = 'liked';
        }
        
        // Get updated like count
        $updated_review = $db->fetchOne("SELECT like_count FROM reviews WHERE id = ?", [$review_id]);
        $like_count = $updated_review['like_count'];
        
        $db->commit();
        
        // Log the action
        logSecurityEvent('review_' . $action_performed, [
            'review_id' => $review_id,
            'like_count' => $like_count
        ]);
        
        echo json_encode([
            'success' => true,
            'liked' => $liked,
            'like_count' => $like_count,
            'message' => $liked ? 'রিভিউটি পছন্দের তালিকায় যোগ করা হয়েছে।' : 'রিভিউটি পছন্দের তালিকা থেকে সরানো হয়েছে।'
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log('Like API error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'সার্ভার ত্রুটি। দয়া করে পরে আবার চেষ্টা করুন।',
        'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
    ]);
}

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
?>