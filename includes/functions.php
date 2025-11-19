<?php
/**
 * Enhanced Functions with Auto Sitemap Generation and Extended Features
 */

if (!defined('MSR_ACCESS')) {
    die('Direct access not permitted.');
}

/**
 * Sanitize input data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate slug from string
 */
function generateSlug($string) {
    // Transliterate Bengali to English
    $bengali_to_english = [
        'আ' => 'a', 'ই' => 'i', 'উ' => 'u', 'এ' => 'e', 'ও' => 'o',
        'ক' => 'k', 'খ' => 'kh', 'গ' => 'g', 'ঘ' => 'gh', 'ঙ' => 'ng',
        'চ' => 'ch', 'ছ' => 'chh', 'জ' => 'j', 'ঝ' => 'jh', 'ঞ' => 'ny',
        'ট' => 't', 'ঠ' => 'th', 'ড' => 'd', 'ঢ' => 'dh', 'ণ' => 'n',
        'ত' => 't', 'থ' => 'th', 'দ' => 'd', 'ধ' => 'dh', 'ন' => 'n',
        'প' => 'p', 'ফ' => 'ph', 'ব' => 'b', 'ভ' => 'bh', 'ম' => 'm',
        'য' => 'y', 'র' => 'r', 'ল' => 'l', 'শ' => 'sh', 'ষ' => 'sh',
        'স' => 's', 'হ' => 'h', 'া' => 'a', 'ি' => 'i', 'ী' => 'i',
        'ু' => 'u', 'ূ' => 'u', 'ে' => 'e', 'ৈ' => 'oi', 'ো' => 'o', 'ৌ' => 'ou'
    ];
    
    $string = strtr($string, $bengali_to_english);
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

/**
 * Upload file
 */
function uploadFile($file, $directory, $allowed_types = null, $max_size = null) {
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'ফাইল আপলোড ত্রুটি।'];
    }
    
    $allowed_types = $allowed_types ?: ALLOWED_IMAGE_TYPES;
    $max_size = $max_size ?: UPLOAD_MAX_SIZE;
    
    // Check file size
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'ফাইল সাইজ অনেক বড়।'];
    }
    
    // Check file type
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'message' => 'অনুমোদিত ফাইল ফরম্যাট নয়।'];
    }
    
    // Create directory if not exists
    $upload_path = UPLOADS_PATH . '/' . $directory;
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $filepath = $upload_path . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'ফাইল সেভ করতে সমস্যা হয়েছে।'];
}

/**
 * Delete file
 */
function deleteFile($directory, $filename) {
    if (empty($filename)) return false;
    
    $filepath = UPLOADS_PATH . '/' . $directory . '/' . $filename;
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Get site setting
 */
function getSiteSetting($key, $default = '') {
    static $settings = null;
    
    if ($settings === null) {
        $db = Database::getInstance();
        $result = $db->fetchAll("SELECT setting_key, setting_value FROM site_settings");
        $settings = [];
        foreach ($result as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings[$key] ?? $default;
}

/**
 * Get SEO setting
 */
function getSeoSetting($key, $default = '') {
    static $seo_settings = null;
    
    if ($seo_settings === null) {
        $db = Database::getInstance();
        $result = $db->fetchAll("SELECT setting_key, setting_value FROM seo_settings");
        $seo_settings = [];
        foreach ($result as $row) {
            $seo_settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $seo_settings[$key] ?? $default;
}

/**
 * Format date in Bengali
 */
function formatBengaliDate($date) {
    $english_months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $bengali_months = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
    
    $formatted = date('j F Y', strtotime($date));
    return str_replace($english_months, $bengali_months, $formatted);
}

/**
 * Time ago in Bengali
 */
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) {
        return 'এইমাত্র';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . ' মিনিট আগে';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . ' ঘন্টা আগে';
    } elseif ($time < 2592000) {
        $days = floor($time / 86400);
        return $days . ' দিন আগে';
    } elseif ($time < 31104000) {
        $months = floor($time / 2592000);
        return $months . ' মাস আগে';
    } else {
        $years = floor($time / 31104000);
        return $years . ' বছর আগে';
    }
}

/**
 * Generate star rating HTML
 */
function getStarRating($rating, $total = 5) {
    $output = '';
    for ($i = 1; $i <= $total; $i++) {
        if ($i <= $rating) {
            $output .= '<span class="text-yellow-400">★</span>';
        } else {
            $output .= '<span class="text-gray-300">★</span>';
        }
    }
    return $output;
}

/**
 * Truncate text
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length, 'UTF-8') . $suffix;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect helper
 */
function redirect($url, $permanent = false) {
    if ($permanent) {
        header('HTTP/1.1 301 Moved Permanently');
    }
    header('Location: ' . $url);
    exit;
}

/**
 * Get current URL
 */
function getCurrentURL() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Check if string contains Bengali text
 */
function hasBengaliText($text) {
    return preg_match('/[\x{0980}-\x{09FF}]/u', $text);
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ip_keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Format number in Bengali
 */
function formatBengaliNumber($number) {
    $bengali_numbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    $english_numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    
    return str_replace($english_numbers, $bengali_numbers, (string)$number);
}

/**
 * Check if user has liked a review
 */
function hasUserLikedReview($review_id, $ip_address = null) {
    if (!$ip_address) {
        $ip_address = getClientIP();
    }
    
    $db = Database::getInstance();
    return $db->fetchOne("SELECT id FROM review_likes WHERE review_id = ? AND ip_address = ?", [$review_id, $ip_address]) !== false;
}

/**
 * Get review like count
 */
function getReviewLikeCount($review_id) {
    $db = Database::getInstance();
    return $db->count("SELECT COUNT(*) FROM review_likes WHERE review_id = ?", [$review_id]);
}

/**
 * Get review comment count
 */
function getReviewCommentCount($review_id, $status = 'approved') {
    $db = Database::getInstance();
    return $db->count("SELECT COUNT(*) FROM comments WHERE review_id = ? AND status = ?", [$review_id, $status]);
}

/**
 * Add like to review
 */
function addReviewLike($review_id, $ip_address = null, $user_agent = null) {
    if (!$ip_address) {
        $ip_address = getClientIP();
    }
    if (!$user_agent) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    
    $db = Database::getInstance();
    
    // Check if already liked
    if (hasUserLikedReview($review_id, $ip_address)) {
        return false;
    }
    
    // Add like
    $inserted = $db->query("INSERT INTO review_likes (review_id, ip_address, user_agent) VALUES (?, ?, ?)", 
                          [$review_id, $ip_address, $user_agent]);
    
    if ($inserted) {
        // Update like count in reviews table
        $db->query("UPDATE reviews SET like_count = like_count + 1 WHERE id = ?", [$review_id]);
        return true;
    }
    
    return false;
}

/**
 * Remove like from review
 */
function removeReviewLike($review_id, $ip_address = null) {
    if (!$ip_address) {
        $ip_address = getClientIP();
    }
    
    $db = Database::getInstance();
    
    // Remove like
    $deleted = $db->query("DELETE FROM review_likes WHERE review_id = ? AND ip_address = ?", 
                         [$review_id, $ip_address]);
    
    if ($deleted) {
        // Update like count in reviews table
        $db->query("UPDATE reviews SET like_count = GREATEST(like_count - 1, 0) WHERE id = ?", [$review_id]);
        return true;
    }
    
    return false;
}

/**
 * Add comment to review
 */
function addReviewComment($review_id, $name, $email, $comment, $parent_id = null, $ip_address = null) {
    if (!$ip_address) {
        $ip_address = getClientIP();
    }
    
    $db = Database::getInstance();
    
    // Sanitize inputs
    $name = sanitize($name);
    $email = sanitize($email);
    $comment = sanitize($comment);
    
    // Validate
    if (empty($name) || empty($email) || empty($comment) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Insert comment
    $inserted = $db->query("
        INSERT INTO comments (review_id, parent_id, name, email, comment, ip_address) 
        VALUES (?, ?, ?, ?, ?, ?)
    ", [$review_id, $parent_id, $name, $email, $comment, $ip_address]);
    
    return $inserted ? $db->lastInsertId() : false;
}

/**
 * Get review comments
 */
function getReviewComments($review_id, $status = 'approved') {
    $db = Database::getInstance();
    
    $comments = $db->fetchAll("
        SELECT * FROM comments 
        WHERE review_id = ? AND status = ? 
        ORDER BY created_at ASC
    ", [$review_id, $status]);
    
    // Organize into threads
    $threads = [];
    foreach ($comments as $comment) {
        if ($comment['parent_id'] === null) {
            $threads[$comment['id']] = $comment;
            $threads[$comment['id']]['replies'] = [];
        } else {
            if (isset($threads[$comment['parent_id']])) {
                $threads[$comment['parent_id']]['replies'][] = $comment;
            }
        }
    }
    
    return $threads;
}

/**
 * Get spoiler content safely
 */
function getSpoilerContent($review_id) {
    $db = Database::getInstance();
    $review = $db->fetchOne("SELECT spoiler_content FROM reviews WHERE id = ?", [$review_id]);
    return $review ? $review['spoiler_content'] : '';
}

/**
 * Check if review has spoiler content
 */
function reviewHasSpoiler($review_id) {
    $spoiler_content = getSpoilerContent($review_id);
    return !empty(trim($spoiler_content));
}

/**
 * Check if user is admin (for API endpoints)
 */
function isAdminAuthenticated() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Validate email address
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Clean HTML content (for user-submitted content)
 */
function cleanHtml($content) {
    // Allow only safe HTML tags
    $allowed_tags = '<p><br><strong><em><u><a><ul><ol><li><blockquote>';
    return strip_tags($content, $allowed_tags);
}

/**
 * Generate excerpt from content
 */
function generateExcerpt($content, $length = 200) {
    $text = strip_tags($content);
    return truncateText($text, $length);
}

/**
 * Check if content contains spam
 */
function isSpamContent($content) {
    $spam_patterns = [
        '/\b(viagra|cialis|pharmacy|casino|poker|loan|debt|credit)\b/i',
        '/\b(buy now|click here|limited time|act now)\b/i',
        '/https?:\/\/[^\s]+\.(tk|ml|ga|cf)\b/i', // Suspicious domains
        '/\$\d+|\d+\$/', // Money patterns
    ];
    
    foreach ($spam_patterns as $pattern) {
        if (preg_match($pattern, $content)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Get file extension from filename
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Format file size in human readable format
 */
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

/**
 * Check if URL is valid
 */
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Generate a secure random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// ===================================
// SEO META FUNCTIONS
// ===================================

/**
 * Get SEO meta title
 */
function getSeoMetaTitle($page_title = '') {
    $meta_title = getSeoSetting('meta_title', '');
    
    if (!empty($meta_title)) {
        if (!empty($page_title)) {
            return $page_title . ' - ' . $meta_title;
        }
        return $meta_title;
    }
    
    // Fallback to site name
    $site_name = getSiteSetting('site_name', SITE_NAME);
    if (!empty($page_title)) {
        return $page_title . ' - ' . $site_name;
    }
    return $site_name;
}

// ===================================
// SITEMAP GENERATION FUNCTIONS
// ===================================

/**
 * Generate complete sitemap.xml
 */
function generateSitemap() {
    try {
        $db = Database::getInstance();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Add homepage
        $xml .= '<url>' . "\n";
        $xml .= '<loc>' . SITE_URL . '</loc>' . "\n";
        $xml .= '<lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
        $xml .= '<changefreq>daily</changefreq>' . "\n";
        $xml .= '<priority>1.0</priority>' . "\n";
        $xml .= '</url>' . "\n";
        
        // Add published reviews
        $reviews = $db->fetchAll("
            SELECT slug, updated_at, created_at 
            FROM reviews 
            WHERE status = 'published' 
            ORDER BY updated_at DESC
        ");
        
        foreach ($reviews as $review) {
            $xml .= '<url>' . "\n";
            $xml .= '<loc>' . SITE_URL . '/review/' . htmlspecialchars($review['slug']) . '</loc>' . "\n";
            $xml .= '<lastmod>' . date('Y-m-d', strtotime($review['updated_at'] ?: $review['created_at'])) . '</lastmod>' . "\n";
            $xml .= '<changefreq>weekly</changefreq>' . "\n";
            $xml .= '<priority>0.8</priority>' . "\n";
            $xml .= '</url>' . "\n";
        }
        
        // Add active categories
        $categories = $db->fetchAll("SELECT slug FROM categories WHERE status = 'active'");
        foreach ($categories as $category) {
            $xml .= '<url>' . "\n";
            $xml .= '<loc>' . SITE_URL . '/category/' . htmlspecialchars($category['slug']) . '</loc>' . "\n";
            $xml .= '<lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
            $xml .= '<changefreq>weekly</changefreq>' . "\n";
            $xml .= '<priority>0.6</priority>' . "\n";
            $xml .= '</url>' . "\n";
        }
        
        // Add static pages
        $static_pages = [
            '/reviews' => ['changefreq' => 'daily', 'priority' => '0.9'],
            '/submit-review' => ['changefreq' => 'monthly', 'priority' => '0.7'],
            '/categories' => ['changefreq' => 'weekly', 'priority' => '0.6'],
            '/about' => ['changefreq' => 'monthly', 'priority' => '0.5'],
            '/contact' => ['changefreq' => 'monthly', 'priority' => '0.5'],
            '/privacy' => ['changefreq' => 'yearly', 'priority' => '0.3'],
            '/terms' => ['changefreq' => 'yearly', 'priority' => '0.3'],
        ];
        
        foreach ($static_pages as $page => $settings) {
            $xml .= '<url>' . "\n";
            $xml .= '<loc>' . SITE_URL . $page . '</loc>' . "\n";
            $xml .= '<lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
            $xml .= '<changefreq>' . $settings['changefreq'] . '</changefreq>' . "\n";
            $xml .= '<priority>' . $settings['priority'] . '</priority>' . "\n";
            $xml .= '</url>' . "\n";
        }
        
        $xml .= '</urlset>';
        
        // Write sitemap file
        $sitemap_path = ROOT_PATH . '/sitemap.xml';
        $result = file_put_contents($sitemap_path, $xml);
        
        if ($result !== false) {
            // Update sitemap generation timestamp
            updateSeoSetting('sitemap_last_generated', date('Y-m-d H:i:s'));
            logActivity('Sitemap generated successfully');
            return true;
        }
        
        return false;
        
    } catch (Exception $e) {
        logActivity('Sitemap generation failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Generate robots.txt file
 */
function generateRobotsTxt() {
    try {
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n\n";
        
        // Disallow sensitive areas
        $disallow_paths = [
            '/admin/',
            '/config/',
            '/includes/',
            '/vendor/',
            '/api/',
            '/uploads/temp/',
            '/cache/',
            '/.git/',
            '/.env',
        ];
        
        foreach ($disallow_paths as $path) {
            $robots .= "Disallow: $path\n";
        }
        
        $robots .= "\n";
        
        // Add crawl delay (optional)
        $robots .= "Crawl-delay: 1\n\n";
        
        // Add sitemap reference
        $robots .= "Sitemap: " . SITE_URL . "/sitemap.xml\n";
        
        // Write robots.txt file
        $robots_path = ROOT_PATH . '/robots.txt';
        $result = file_put_contents($robots_path, $robots);
        
        if ($result !== false) {
            logActivity('Robots.txt generated successfully');
            return true;
        }
        
        return false;
        
    } catch (Exception $e) {
        logActivity('Robots.txt generation failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Auto-generate sitemap when content changes
 */
function autoGenerateSitemap() {
    // Check if auto-generation is enabled
    if (getSeoSetting('auto_sitemap', 0)) {
        return generateSitemap();
    }
    return false;
}

/**
 * Hook for review save/update to auto-generate sitemap
 */
function onReviewSaved($review_id, $status = 'published') {
    if ($status === 'published') {
        autoGenerateSitemap();
        
        // Ping search engines (optional)
        pingSearchEngines();
    }
}

/**
 * Hook for category save/update to auto-generate sitemap
 */
function onCategorySaved($category_id, $status = 'active') {
    if ($status === 'active') {
        autoGenerateSitemap();
    }
}

/**
 * Ping search engines about sitemap update
 */
function pingSearchEngines() {
    $sitemap_url = SITE_URL . '/sitemap.xml';
    
    $ping_urls = [
        'https://www.google.com/ping?sitemap=' . urlencode($sitemap_url),
        'https://www.bing.com/ping?sitemap=' . urlencode($sitemap_url),
    ];
    
    foreach ($ping_urls as $ping_url) {
        // Use a non-blocking request to avoid slowing down the user experience
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'ignore_errors' => true
            ]
        ]);
        
        @file_get_contents($ping_url, false, $context);
    }
}

/**
 * Update SEO setting
 */
function updateSeoSetting($key, $value) {
    $db = Database::getInstance();
    
    $existing = $db->fetchOne("SELECT id FROM seo_settings WHERE setting_key = ?", [$key]);
    if ($existing) {
        return $db->query("UPDATE seo_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
    } else {
        return $db->query("INSERT INTO seo_settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
    }
}

/**
 * Log activity for debugging
 */
function logActivity($message) {
    $log_file = ROOT_PATH . '/logs/activity.log';
    $log_dir = dirname($log_file);
    
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message" . PHP_EOL;
    
    @file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
}

// ===================================
// COLOR & DESIGN FUNCTIONS
// ===================================

/**
 * Get CSS custom properties for colors
 */
function getColorCSS() {
    $primary_color = getSiteSetting('primary_color', '#3B82F6');
    $secondary_color = getSiteSetting('secondary_color', '#10B981');
    $body_color = getSiteSetting('body_color', '#F9FAFB');
    $text_color = getSiteSetting('text_color', '#1F2937');
    $header_color = getSiteSetting('header_color', '#FFFFFF');
    $footer_color = getSiteSetting('footer_color', '#1F2937');
    
    return "
    :root {
        --color-primary: {$primary_color};
        --color-secondary: {$secondary_color};
        --color-body: {$body_color};
        --color-text: {$text_color};
        --color-header: {$header_color};
        --color-footer: {$footer_color};
    }
    ";
}

/**
 * Get custom CSS
 */
function getCustomCSS() {
    return getSiteSetting('custom_css', '');
}

/**
 * Generate complete CSS output
 */
function generateDynamicCSS() {
    $css = getColorCSS();
    $custom_css = getCustomCSS();
    
    if (!empty($custom_css)) {
        $css .= "\n/* Custom CSS */\n" . $custom_css;
    }
    
    return $css;
}

// ===================================
// SECURITY FUNCTIONS
// ===================================

/**
 * Verify reCAPTCHA
 */
function verifyRecaptcha($recaptcha_response) {
    $secret_key = getSiteSetting('recaptcha_secret_key', '');
    
    if (empty($secret_key) || empty($recaptcha_response)) {
        return false;
    }
    
    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secret_key,
        'response' => $recaptcha_response,
        'remoteip' => getClientIP()
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($data),
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($verify_url, false, $context);
    
    if ($response) {
        $result = json_decode($response, true);
        return isset($result['success']) && $result['success'] === true;
    }
    
    return false;
}

/**
 * Check if site is in maintenance mode
 */
function isMaintenanceMode() {
    return getSiteSetting('maintenance_mode', 0) == 1;
}

/**
 * Display maintenance page
 */
function showMaintenancePage() {
    if (!isAdminAuthenticated() && isMaintenanceMode()) {
        http_response_code(503);
        include ROOT_PATH . '/maintenance.html';
        exit;
    }
}

// ===================================
// CLOUDFLARE FUNCTIONS
// ===================================

/**
 * Purge Cloudflare cache
 */
function purgeCloudflareCache($urls = null) {
    $zone_id = getSiteSetting('cloudflare_zone_id', '');
    $api_token = getSiteSetting('cloudflare_api_token', '');
    
    if (empty($zone_id) || empty($api_token)) {
        return false;
    }
    
    $api_url = "https://api.cloudflare.com/client/v4/zones/{$zone_id}/purge_cache";
    
    $data = [];
    if ($urls) {
        $data['files'] = is_array($urls) ? $urls : [$urls];
    } else {
        $data['purge_everything'] = true;
    }
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Authorization: Bearer ' . $api_token,
                'Content-Type: application/json'
            ],
            'content' => json_encode($data),
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($api_url, false, $context);
    
    if ($response) {
        $result = json_decode($response, true);
        return isset($result['success']) && $result['success'] === true;
    }
    
    return false;
}

/**
 * Auto-purge cache when content changes
 */
function autoPurgeCache($urls = null) {
    // Only purge if Cloudflare is configured
    if (getSiteSetting('cloudflare_zone_id', '') && getSiteSetting('cloudflare_api_token', '')) {
        return purgeCloudflareCache($urls);
    }
    return false;
}

// ===================================
// ENHANCED REVIEW FUNCTIONS
// ===================================

/**
 * Save review with automatic sitemap generation
 */
function saveReview($data, $update_id = null) {
    $db = Database::getInstance();
    
    try {
        if ($update_id) {
            // Update existing review
            $result = $db->query("
                UPDATE reviews SET 
                title = ?, slug = ?, reviewer_name = ?, year = ?, type = ?, 
                language = ?, director = ?, cast = ?, rating = ?, content = ?, 
                spoiler_content = ?, excerpt = ?, poster_image = ?, trailer_url = ?, 
                featured = ?, status = ?, meta_title = ?, meta_description = ?, 
                meta_keywords = ?, updated_at = NOW()
                WHERE id = ?
            ", [
                $data['title'], $data['slug'], $data['reviewer_name'], $data['year'], $data['type'],
                $data['language'], $data['director'], $data['cast'], $data['rating'], $data['content'],
                $data['spoiler_content'], $data['excerpt'], $data['poster_image'], $data['trailer_url'],
                $data['featured'], $data['status'], $data['meta_title'], $data['meta_description'],
                $data['meta_keywords'], $update_id
            ]);
            
            $review_id = $update_id;
        } else {
            // Insert new review
            $result = $db->query("
                INSERT INTO reviews (
                    title, slug, reviewer_name, reviewer_email, year, type, language, director, cast, 
                    rating, content, spoiler_content, excerpt, poster_image, trailer_url, featured, 
                    status, created_by, meta_title, meta_description, meta_keywords, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ", [
                $data['title'], $data['slug'], $data['reviewer_name'], $data['reviewer_email'] ?? '',
                $data['year'], $data['type'], $data['language'], $data['director'], $data['cast'],
                $data['rating'], $data['content'], $data['spoiler_content'], $data['excerpt'], 
                $data['poster_image'], $data['trailer_url'], $data['featured'], $data['status'],
                $data['created_by'] ?? 'admin', $data['meta_title'], $data['meta_description'], $data['meta_keywords']
            ]);
            
            $review_id = $db->lastInsertId();
        }
        
        if ($result && isset($data['categories'])) {
            // Clear existing categories
            $db->query("DELETE FROM review_categories WHERE review_id = ?", [$review_id]);
            
            // Insert new categories
            foreach ($data['categories'] as $category_id) {
                $db->query("INSERT INTO review_categories (review_id, category_id) VALUES (?, ?)", 
                          [$review_id, $category_id]);
            }
        }
        
        // Auto-generate sitemap if review is published
        if ($result && $data['status'] === 'published') {
            onReviewSaved($review_id, $data['status']);
            
            // Auto-purge cache for this review
            $review_url = SITE_URL . '/review/' . $data['slug'];
            autoPurgeCache([$review_url, SITE_URL, SITE_URL . '/reviews']);
        }
        
        return $result;
        
    } catch (Exception $e) {
        logActivity('Review save failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Delete review with sitemap regeneration
 */
function deleteReview($review_id) {
    $db = Database::getInstance();
    
    try {
        // Get review details before deletion
        $review = $db->fetchOne("SELECT slug, poster_image, status FROM reviews WHERE id = ?", [$review_id]);
        
        if (!$review) {
            return false;
        }
        
        // Delete associated data
        $db->query("DELETE FROM review_categories WHERE review_id = ?", [$review_id]);
        $db->query("DELETE FROM review_likes WHERE review_id = ?", [$review_id]);
        $db->query("DELETE FROM comments WHERE review_id = ?", [$review_id]);
        
        // Delete the review
        $result = $db->query("DELETE FROM reviews WHERE id = ?", [$review_id]);
        
        if ($result) {
            // Delete poster image
            if (!empty($review['poster_image'])) {
                deleteFile('reviews', $review['poster_image']);
            }
            
            // Regenerate sitemap if review was published
            if ($review['status'] === 'published') {
                autoGenerateSitemap();
                
                // Purge cache
                autoPurgeCache();
            }
        }
        
        return $result;
        
    } catch (Exception $e) {
        logActivity('Review deletion failed: ' . $e->getMessage());
        return false;
    }
}

// ===================================
// MAINTENANCE & CRON FUNCTIONS
// ===================================

/**
 * Clean up old log files
 */
function cleanupLogs($days = 30) {
    $log_dir = ROOT_PATH . '/logs';
    if (!is_dir($log_dir)) {
        return;
    }
    
    $files = glob($log_dir . '/*.log');
    $cutoff_time = time() - ($days * 24 * 60 * 60);
    
    foreach ($files as $file) {
        if (filemtime($file) < $cutoff_time) {
            @unlink($file);
        }
    }
}

/**
 * Daily maintenance tasks
 */
function runDailyMaintenance() {
    // Clean up old logs
    cleanupLogs(30);
    
    // Regenerate sitemap
    if (getSeoSetting('auto_sitemap', 0)) {
        generateSitemap();
    }
    
    // Log maintenance run
    logActivity('Daily maintenance completed');
}

// Initialize maintenance check
if (defined('MSR_ACCESS')) {
    showMaintenancePage();
}
?>