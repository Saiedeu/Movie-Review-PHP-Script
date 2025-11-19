<?php
/**
 * Enhanced Single Review Page with Comments and Likes
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config/config.php';

// Get database instance
$db = Database::getInstance();

// Get review slug from URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Like/Unlike functionality
    if ($action === 'toggle_like') {
        $review_id = intval($_POST['review_id'] ?? 0);
        $ip_address = getClientIP();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Check if already liked
        $existing_like = $db->fetchOne("SELECT id FROM review_likes WHERE review_id = ? AND ip_address = ?", [$review_id, $ip_address]);
        
        if ($existing_like) {
            // Remove like
            $db->query("DELETE FROM review_likes WHERE review_id = ? AND ip_address = ?", [$review_id, $ip_address]);
            $db->query("UPDATE reviews SET like_count = GREATEST(like_count - 1, 0) WHERE id = ?", [$review_id]);
            $liked = false;
        } else {
            // Add like
            $db->query("INSERT INTO review_likes (review_id, ip_address, user_agent) VALUES (?, ?, ?)", [$review_id, $ip_address, $user_agent]);
            $db->query("UPDATE reviews SET like_count = like_count + 1 WHERE id = ?", [$review_id]);
            $liked = true;
        }
        
        // Get updated like count
        $like_count_result = $db->fetchOne("SELECT like_count FROM reviews WHERE id = ?", [$review_id]);
        $like_count = $like_count_result['like_count'] ?? 0;
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'liked' => $liked, 'like_count' => $like_count]);
        exit;
    }
    
    // Add comment functionality
    if ($action === 'add_comment') {
        $review_id = intval($_POST['review_id'] ?? 0);
        $parent_id = intval($_POST['parent_id'] ?? 0) ?: null;
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $comment = sanitize($_POST['comment'] ?? '');
        $ip_address = getClientIP();
        
        if (!empty($name) && !empty($email) && !empty($comment) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $inserted = $db->query("
                INSERT INTO comments (review_id, parent_id, name, email, comment, ip_address) 
                VALUES (?, ?, ?, ?, ?, ?)
            ", [$review_id, $parent_id, $name, $email, $comment, $ip_address]);
            
            if ($inserted) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'মন্তব্য সফলভাবে যোগ করা হয়েছে। অনুমোদনের পর প্রকাশিত হবে।']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'মন্তব্য যোগ করতে সমস্যা হয়েছে।']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'সব ফিল্ড সঠিকভাবে পূরণ করুন।']);
        }
        exit;
    }
}

// Get review data
$review = $db->fetchOne("
    SELECT r.*, 
           GROUP_CONCAT(c.name_bn ORDER BY c.name_bn SEPARATOR ', ') as categories,
           GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') as categories_en,
           GROUP_CONCAT(c.slug ORDER BY c.name SEPARATOR ', ') as category_slugs
    FROM reviews r 
    LEFT JOIN review_categories rc ON r.id = rc.review_id
    LEFT JOIN categories c ON rc.category_id = c.id
    WHERE r.slug = ? AND r.status = 'published'
    GROUP BY r.id
", [$slug]);

if (!$review) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Update view count
$db->query("UPDATE reviews SET view_count = view_count + 1 WHERE id = ?", [$review['id']]);
$review['view_count']++;

// Check if current user liked this review
$user_ip = getClientIP();
$user_liked = $db->fetchOne("SELECT id FROM review_likes WHERE review_id = ? AND ip_address = ?", [$review['id'], $user_ip]);

// Get approved comments
$comments = $db->fetchAll("
    SELECT * FROM comments 
    WHERE review_id = ? AND status = 'approved' 
    ORDER BY created_at ASC
", [$review['id']]);

// Organize comments into threads
$comment_threads = [];
foreach ($comments as $comment) {
    if ($comment['parent_id'] === null) {
        $comment_threads[$comment['id']] = $comment;
        $comment_threads[$comment['id']]['replies'] = [];
    } else {
        if (isset($comment_threads[$comment['parent_id']])) {
            $comment_threads[$comment['parent_id']]['replies'][] = $comment;
        }
    }
}

// Get related reviews
$related_reviews = $db->fetchAll("
    SELECT DISTINCT r2.*, 
           GROUP_CONCAT(c.name_bn ORDER BY c.name_bn SEPARATOR ', ') as categories
    FROM reviews r2 
    LEFT JOIN review_categories rc2 ON r2.id = rc2.review_id 
    LEFT JOIN review_categories rc1 ON rc2.category_id = rc1.category_id 
    LEFT JOIN categories c ON rc2.category_id = c.id
    WHERE rc1.review_id = ? 
    AND r2.id != ? 
    AND r2.status = 'published' 
    GROUP BY r2.id
    ORDER BY r2.created_at DESC 
    LIMIT 6
", [$review['id'], $review['id']]);

// SEO Meta Data
$seo_title = $review['title'] . ' - রিভিউ - ' . getSiteSetting('site_name', SITE_NAME);
$seo_description = $review['excerpt'] ? strip_tags($review['excerpt']) : 'পড়ুন ' . $review['title'] . ' এর বিস্তারিত রিভিউ, রেটিং এবং বিশ্লেষণ।';
$seo_keywords = $review['title'] . ', movie review, ' . ($review['categories'] ? str_replace(', ', ', ', $review['categories']) : '');
$seo_image = $review['poster_image'] ? UPLOADS_URL . '/reviews/' . $review['poster_image'] : '';

// Breadcrumbs
$breadcrumbs = [
    ['name' => 'হোম', 'url' => '/'],
    ['name' => 'রিভিউ', 'url' => '/reviews'],
    ['name' => $review['title'], 'url' => '/review/' . $review['slug']]
];

// Include header
include 'includes/header.php';
?>

<style>
/* Spoiler styles */
.spoiler-content {
    background: linear-gradient(45deg, #fee2e2, #fef3c7);
    border: 2px dashed #f59e0b;
    color: transparent;
    user-select: none;
    cursor: pointer;
    border-radius: 12px;
    padding: 20px;
    margin: 20px 0;
    position: relative;
    transition: all 0.3s ease;
}

.spoiler-content.revealed {
    color: #374151;
    user-select: text;
    cursor: text;
    background: #f9fafb;
    border-color: #d1d5db;
}

.spoiler-content::before {
    content: "⚠️ স্পয়লার এলার্ট - দেখতে ক্লিক করুন";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #ef4444;
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.spoiler-content.revealed::before {
    display: none;
}

.spoiler-btn {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 16px 0;
}

.spoiler-btn:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Comments styles */
.comment-item {
    border-left: 4px solid #3b82f6;
    background: #f8fafc;
    padding: 16px;
    margin: 12px 0;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.comment-item:hover {
    background: #f1f5f9;
    transform: translateX(4px);
}

.comment-reply {
    margin-left: 32px;
    border-left-color: #10b981;
    background: #f0fdf4;
}

.comment-reply:hover {
    background: #dcfce7;
}

/* Like button animation */
.like-btn {
    transition: all 0.3s ease;
    transform-origin: center;
}

.like-btn:hover {
    transform: scale(1.1);
}

.like-btn.liked {
    color: #ef4444;
    animation: heartBeat 0.6s ease;
}

@keyframes heartBeat {
    0% { transform: scale(1); }
    25% { transform: scale(1.3); }
    50% { transform: scale(1); }
    75% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

/* Mobile responsive */
@media (max-width: 640px) {
    .comment-reply {
        margin-left: 16px;
    }
    
    .spoiler-content::before {
        font-size: 14px;
        padding: 10px 16px;
    }
}
</style>

<!-- Review Hero Section -->
<section class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-black py-16 lg:py-24">
    <!-- Background Image -->
    <?php if ($review['poster_image']): ?>
        <div class="absolute inset-0 opacity-20">
            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                 alt="<?php echo htmlspecialchars($review['title']); ?>" 
                 class="w-full h-full object-cover">
        </div>
    <?php endif; ?>
    
    <div class="relative container mx-auto px-4">
        <div class="grid lg:grid-cols-3 gap-12 items-start">
            <!-- Poster -->
            <div class="lg:col-span-1">
                <div class="sticky top-8">
                    <div class="relative group">
                        <div class="aspect-[2/3] bg-gray-300 rounded-2xl overflow-hidden shadow-2xl">
                            <?php if ($review['poster_image']): ?>
                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center">
                                    <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Rating Overlay -->
                        <?php if ($review['rating'] > 0): ?>
                            <div class="absolute -bottom-6 left-1/2 transform -translate-x-1/2">
                                <div class="bg-white rounded-full p-4 shadow-xl border-4 border-gray-100">
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-gray-900">
                                            <?php echo number_format($review['rating'], 1); ?>
                                        </div>
                                        <div class="text-yellow-400 text-lg">
                                            <?php echo getStarRating($review['rating']); ?>
                                        </div>
                                        <div class="text-gray-600 text-sm">/ ৫.০</div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="lg:col-span-2 text-white space-y-6">
                <!-- Breadcrumb -->
                <nav class="flex items-center space-x-2 text-sm text-gray-300">
                    <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                        <?php if ($index === count($breadcrumbs) - 1): ?>
                            <span><?php echo htmlspecialchars($breadcrumb['name']); ?></span>
                        <?php else: ?>
                            <a href="<?php echo $breadcrumb['url']; ?>" class="hover:text-white transition-colors">
                                <?php echo htmlspecialchars($breadcrumb['name']); ?>
                            </a>
                            <span>→</span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </nav>
                
                <!-- Title -->
                <div>
                    <h1 class="text-4xl lg:text-6xl font-bold mb-4 leading-tight">
                        <?php echo htmlspecialchars($review['title']); ?>
                    </h1>
                    
                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center gap-4 text-lg text-gray-300">
                        <?php if ($review['year']): ?>
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <?php echo $review['year']; ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($review['language']): ?>
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                </svg>
                                <?php echo $review['language']; ?>
                            </span>
                        <?php endif; ?>
                        
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <?php echo number_format($review['view_count']); ?> ভিউ
                        </span>
                        
                        <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm">
                            <?php echo $review['type'] === 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?>
                        </span>
                    </div>
                </div>
                
                <!-- Categories -->
                <?php if ($review['categories']): ?>
                    <div class="flex flex-wrap gap-2">
                        <?php 
                        $cats = explode(', ', $review['categories']);
                        $cat_slugs = explode(', ', $review['category_slugs']);
                        foreach ($cats as $index => $cat): 
                        ?>
                            <a href="/category/<?php echo $cat_slugs[$index]; ?>" 
                               class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-full text-sm transition-colors backdrop-blur-sm">
                                <?php echo htmlspecialchars($cat); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Cast & Crew -->
                <div class="grid md:grid-cols-2 gap-6 text-sm">
                    <?php if ($review['director']): ?>
                        <div>
                            <h3 class="text-gray-400 font-medium mb-1">পরিচালক</h3>
                            <p class="text-white"><?php echo htmlspecialchars($review['director']); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($review['cast']): ?>
                        <div>
                            <h3 class="text-gray-400 font-medium mb-1">প্রধান অভিনেতা</h3>
                            <p class="text-white"><?php echo htmlspecialchars($review['cast']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Excerpt -->
                <?php if ($review['excerpt']): ?>
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6">
                        <h3 class="text-xl font-bold mb-4 text-yellow-400">সংক্ষিপ্ত বিবরণ</h3>
                        <p class="text-lg leading-relaxed text-gray-200">
                            <?php echo $review['excerpt']; ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4">
                    <!-- Like Button -->
                    <button onclick="toggleLike(<?php echo $review['id']; ?>)" 
                            class="like-btn bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center <?php echo $user_liked ? 'liked' : ''; ?>">
                        <svg class="w-5 h-5 mr-2" fill="<?php echo $user_liked ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span id="like-text"><?php echo $user_liked ? 'পছন্দ হয়েছে' : 'পছন্দ'; ?></span>
                        <span id="like-count" class="ml-2">(<span id="like-number"><?php echo $review['like_count']; ?></span>)</span>
                    </button>
                    
                    <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        শেয়ার
                    </button>
                    
                    <button class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                        সেভ
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Review Content -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Review Text -->
            <div class="prose prose-lg max-w-none">
                <div class="text-gray-800 leading-relaxed space-y-6">
                    <?php echo $review['content']; ?>
                </div>
            </div>
            
            <!-- Spoiler Content -->
            <?php if (!empty($review['spoiler_content'])): ?>
                <div class="mt-8">
                    <button onclick="toggleSpoiler()" class="spoiler-btn">
                        ⚠️ স্পয়লার দেখান
                    </button>
                    <div id="spoiler-section" class="spoiler-content" onclick="revealSpoiler()">
                        <div class="spoiler-text">
                            <?php echo nl2br(htmlspecialchars($review['spoiler_content'])); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Author Info -->
            <div class="mt-12 bg-gray-50 rounded-2xl p-8">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                        <?php echo strtoupper(substr($review['reviewer_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-gray-900">
                            <?php echo htmlspecialchars($review['reviewer_name']); ?>
                        </h4>
                        <p class="text-gray-600">রিভিউ লেখক</p>
                        <p class="text-sm text-gray-500 mt-1">
                            প্রকাশিত: <?php echo date('j F Y', strtotime($review['created_at'])); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Comments Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Comments Header -->
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    মন্তব্য (<?php echo count($comment_threads); ?>টি)
                </h2>
                <p class="text-gray-600">এই রিভিউ সম্পর্কে আপনার মতামত শেয়ার করুন</p>
            </div>
            
            <!-- Add Comment Form -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">মন্তব্য করুন</h3>
                <form id="comment-form" class="space-y-4">
                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                    <input type="hidden" name="parent_id" value="0">
                    <input type="hidden" name="action" value="add_comment">
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="comment_name" class="block text-sm font-medium text-gray-700 mb-2">
                                নাম <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="comment_name" 
                                   name="name" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
                                   placeholder="আপনার নাম"
                                   required>
                        </div>
                        
                        <div>
                            <label for="comment_email" class="block text-sm font-medium text-gray-700 mb-2">
                                ইমেইল <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="comment_email" 
                                   name="email" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
                                   placeholder="your@email.com"
                                   required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="comment_text" class="block text-sm font-medium text-gray-700 mb-2">
                            মন্তব্য <span class="text-red-500">*</span>
                        </label>
                        <textarea id="comment_text" 
                                  name="comment" 
                                  rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
                                  placeholder="আপনার মতামত লিখুন..."
                                  required></textarea>
                    </div>
                    
                    <div>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            মন্তব্য পোস্ট করুন
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Comments List -->
            <div id="comments-list" class="space-y-6">
                <?php foreach ($comment_threads as $comment): ?>
                    <div class="comment-item">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                <?php echo strtoupper(substr($comment['name'], 0, 1)); ?>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="font-bold text-gray-900"><?php echo htmlspecialchars($comment['name']); ?></h4>
                                    <span class="text-gray-500 text-sm"><?php echo timeAgo($comment['created_at']); ?></span>
                                </div>
                                <p class="text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                                <button onclick="showReplyForm(<?php echo $comment['id']; ?>)" 
                                        class="text-blue-600 hover:text-blue-700 text-sm font-medium mt-2">
                                    উত্তর দিন
                                </button>
                                
                                <!-- Reply Form (Hidden by default) -->
                                <div id="reply-form-<?php echo $comment['id']; ?>" class="mt-4 hidden">
                                    <form class="reply-form space-y-4">
                                        <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                        <input type="hidden" name="parent_id" value="<?php echo $comment['id']; ?>">
                                        <input type="hidden" name="action" value="add_comment">
                                        
                                        <div class="grid md:grid-cols-2 gap-4">
                                            <input type="text" 
                                                   name="name" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded text-base"
                                                   placeholder="আপনার নাম"
                                                   required>
                                            <input type="email" 
                                                   name="email" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded text-base"
                                                   placeholder="your@email.com"
                                                   required>
                                        </div>
                                        
                                        <textarea name="comment" 
                                                  rows="3" 
                                                  class="w-full px-3 py-2 border border-gray-300 rounded text-base"
                                                  placeholder="আপনার উত্তর লিখুন..."
                                                  required></textarea>
                                        
                                        <div class="flex space-x-2">
                                            <button type="submit" 
                                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                                                উত্তর পোস্ট করুন
                                            </button>
                                            <button type="button" 
                                                    onclick="hideReplyForm(<?php echo $comment['id']; ?>)"
                                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                                                বাতিল
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Replies -->
                        <?php if (!empty($comment['replies'])): ?>
                            <div class="mt-4 space-y-4">
                                <?php foreach ($comment['replies'] as $reply): ?>
                                    <div class="comment-reply comment-item">
                                        <div class="flex items-start space-x-4">
                                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                <?php echo strtoupper(substr($reply['name'], 0, 1)); ?>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-3 mb-2">
                                                    <h5 class="font-bold text-gray-900"><?php echo htmlspecialchars($reply['name']); ?></h5>
                                                    <span class="text-gray-500 text-sm"><?php echo timeAgo($reply['created_at']); ?></span>
                                                </div>
                                                <p class="text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($reply['comment'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($comment_threads)): ?>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">এখনো কোনো মন্তব্য নেই</h3>
                        <p class="text-gray-500">প্রথম মন্তব্য করুন এবং আলোচনা শুরু করুন!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Related Reviews -->
<?php if (!empty($related_reviews)): ?>
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">সংশ্লিষ্ট রিভিউ</h2>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($related_reviews as $related): ?>
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-200">
                    <div class="aspect-[16/9] bg-gray-200 overflow-hidden">
                        <?php if ($related['poster_image']): ?>
                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $related['poster_image']; ?>" 
                                 alt="<?php echo htmlspecialchars($related['title']); ?>" 
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                 loading="lazy">
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2">
                            <a href="/review/<?php echo $related['slug']; ?>" class="hover:text-blue-600 transition-colors">
                                <?php echo htmlspecialchars($related['title']); ?>
                            </a>
                        </h3>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                            <span><?php echo $related['year']; ?></span>
                            <?php if ($related['rating'] > 0): ?>
                                <div class="flex items-center">
                                    <span class="text-yellow-400 mr-1">★</span>
                                    <span><?php echo number_format($related['rating'], 1); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($related['excerpt']): ?>
                            <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                                <?php echo htmlspecialchars(strip_tags($related['excerpt'])); ?>
                            </p>
                        <?php endif; ?>
                        
                        <a href="/review/<?php echo $related['slug']; ?>" 
                           class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                            পড়ুন →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
// Like functionality
function toggleLike(reviewId) {
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=toggle_like&review_id=' + reviewId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const likeBtn = document.querySelector('.like-btn');
            const likeText = document.getElementById('like-text');
            const likeNumber = document.getElementById('like-number');
            
            if (data.liked) {
                likeBtn.classList.add('liked');
                likeText.textContent = 'পছন্দ হয়েছে';
                likeBtn.querySelector('svg').setAttribute('fill', 'currentColor');
            } else {
                likeBtn.classList.remove('liked');
                likeText.textContent = 'পছন্দ';
                likeBtn.querySelector('svg').setAttribute('fill', 'none');
            }
            
            likeNumber.textContent = data.like_count;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCustomAlert('লাইক করতে সমস্যা হয়েছে।');
    });
}

// Spoiler functionality
function toggleSpoiler() {
    const spoilerSection = document.getElementById('spoiler-section');
    spoilerSection.classList.add('revealed');
    spoilerSection.onclick = null; // Remove click handler
}

function revealSpoiler() {
    const spoilerSection = document.getElementById('spoiler-section');
    spoilerSection.classList.add('revealed');
    spoilerSection.onclick = null; // Remove click handler
}

// Comment functionality
document.getElementById('comment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    submitComment(this);
});

// Reply form functionality
function showReplyForm(commentId) {
    const replyForm = document.getElementById('reply-form-' + commentId);
    replyForm.classList.remove('hidden');
}

function hideReplyForm(commentId) {
    const replyForm = document.getElementById('reply-form-' + commentId);
    replyForm.classList.add('hidden');
}

// Handle reply form submissions
document.addEventListener('submit', function(e) {
    if (e.target.classList.contains('reply-form')) {
        e.preventDefault();
        submitComment(e.target);
    }
});

function submitComment(form) {
    const formData = new FormData(form);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showCustomAlert(data.message, 'success');
            form.reset();
            
            // Hide reply form if it's a reply
            const replyForm = form.closest('[id^="reply-form-"]');
            if (replyForm) {
                replyForm.classList.add('hidden');
            }
        } else {
            showCustomAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCustomAlert('মন্তব্য পোস্ট করতে সমস্যা হয়েছে।', 'error');
    });
}

// Custom alert function
function showCustomAlert(message, type = 'info') {
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-lg max-w-sm w-full mx-4 p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 ${bgColor} rounded-full flex items-center justify-center text-white mr-3">
                    ${type === 'success' ? '✓' : type === 'error' ? '✗' : 'ℹ'}
                </div>
                <h3 class="text-lg font-bold text-gray-900">${type === 'success' ? 'সফল!' : type === 'error' ? 'ত্রুটি!' : 'বিজ্ঞপ্তি'}</h3>
            </div>
            <p class="text-gray-700 mb-4">${message}</p>
            <div class="flex justify-end">
                <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 ${bgColor} text-white rounded hover:opacity-90">ঠিক আছে</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (modal.parentNode) {
            modal.remove();
        }
    }, 5000);
}
</script>

<!-- JSON-LD Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Review",
    "itemReviewed": {
        "@type": "<?php echo $review['type'] === 'movie' ? 'Movie' : 'TVSeries'; ?>",
        "name": "<?php echo htmlspecialchars($review['title']); ?>",
        "image": "<?php echo $seo_image; ?>",
        "dateCreated": "<?php echo $review['year']; ?>",
        "director": "<?php echo htmlspecialchars($review['director'] ?? ''); ?>",
        "actor": "<?php echo htmlspecialchars($review['cast'] ?? ''); ?>"
    },
    "reviewRating": {
        "@type": "Rating",
        "ratingValue": "<?php echo $review['rating']; ?>",
        "bestRating": "5",
        "worstRating": "1"
    },
    "author": {
        "@type": "Person",
        "name": "<?php echo htmlspecialchars($review['reviewer_name']); ?>"
    },
    "datePublished": "<?php echo date('c', strtotime($review['created_at'])); ?>",
    "reviewBody": "<?php echo htmlspecialchars(strip_tags($review['content'])); ?>",
    "publisher": {
        "@type": "Organization",
        "name": "<?php echo getSiteSetting('site_name', SITE_NAME); ?>",
        "url": "<?php echo SITE_URL; ?>"
    }
}
</script>

<?php include 'includes/footer.php'; ?>