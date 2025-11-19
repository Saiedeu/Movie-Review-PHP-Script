<?php
/**
 * Submit Review API Endpoint
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
if (!checkRateLimit('submit_review', 3, 3600)) { // 3 submissions per hour
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
    $required_fields = ['title', 'reviewer_name', 'reviewer_email', 'content', 'rating'];
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
    $title = sanitize($input['title']);
    $reviewer_name = sanitize($input['reviewer_name']);
    $reviewer_email = sanitize($input['reviewer_email']);
    $year = sanitize($input['year'] ?? '');
    $type = sanitize($input['type'] ?? 'movie');
    $language = sanitize($input['language'] ?? '');
    $director = sanitize($input['director'] ?? '');
    $cast = sanitize($input['cast'] ?? '');
    $rating = floatval($input['rating']);
    $content = $input['content'] ?? '';
    $category_ids = $input['categories'] ?? [];
    
    // Validate email
    if (!filter_var($reviewer_email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'সঠিক ইমেইল ঠিকানা প্রদান করুন।'
        ]);
        exit;
    }
    
    // Validate rating
    if ($rating < 1 || $rating > 5) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'রেটিং ১ থেকে ৫ এর মধ্যে হতে হবে।'
        ]);
        exit;
    }
    
    // Validate content length
    $word_count = str_word_count(strip_tags($content));
    if ($word_count < 50) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'রিভিউ কমপক্ষে ৫০ শব্দের হতে হবে। বর্তমানে ' . $word_count . ' শব্দ আছে।'
        ]);
        exit;
    }
    
    // Generate slug
    $slug = generateSlug($title);
    
    // Check if slug exists
    $existing = $db->fetchOne("SELECT id FROM reviews WHERE slug = ?", [$slug]);
    if ($existing) {
        $slug .= '-' . time();
    }
    
    // Handle file upload if present
    $poster_image = '';
    if (isset($_FILES['poster_image']) && $_FILES['poster_image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadFile($_FILES['poster_image'], 'reviews');
        if ($upload_result['success']) {
            $poster_image = $upload_result['filename'];
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ফাইল আপলোড ত্রুটি: ' . $upload_result['message']
            ]);
            exit;
        }
    }
    
    // Generate excerpt
    $excerpt = truncateText(strip_tags($content), 200);
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Insert review
        $review_id = $db->query("
            INSERT INTO reviews (
                title, slug, reviewer_name, reviewer_email, year, type, language, 
                director, cast, rating, content, excerpt, poster_image, status, 
                created_by, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'user', NOW())
        ", [
            $title, $slug, $reviewer_name, $reviewer_email, $year, $type, $language,
            $director, $cast, $rating, $content, $excerpt, $poster_image
        ]);
        
        if (!$review_id) {
            throw new Exception('রিভিউ সংরক্ষণ করতে সমস্যা হয়েছে।');
        }
        
        $review_id = $db->lastInsertId();
        
        // Insert categories
        if (!empty($category_ids)) {
            foreach ($category_ids as $category_id) {
                $category_id = intval($category_id);
                if ($category_id > 0) {
                    $db->query("INSERT INTO review_categories (review_id, category_id) VALUES (?, ?)", [$review_id, $category_id]);
                }
            }
        }
        
        // Log submission
        logSecurityEvent('review_submitted', [
            'review_id' => $review_id,
            'title' => $title,
            'reviewer_email' => $reviewer_email
        ]);
        
        // Commit transaction
        $db->commit();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'আপনার রিভিউ সফলভাবে জমা দেওয়া হয়েছে! অ্যাডমিন অনুমোদনের পর এটি প্রকাশিত হবে।',
            'review_id' => $review_id,
            'data' => [
                'title' => $title,
                'slug' => $slug,
                'rating' => $rating,
                'status' => 'pending'
            ]
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction
        $db->rollback();
        
        // Delete uploaded file if exists
        if ($poster_image) {
            deleteFile('reviews', $poster_image);
        }
        
        throw $e;
    }
    
} catch (Exception $e) {
    // Log error
    error_log('Review submission error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'সার্ভার ত্রুটি। দয়া করে পরে আবার চেষ্টা করুন।',
        'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
    ]);
}
?>