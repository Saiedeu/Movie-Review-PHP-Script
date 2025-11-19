<?php
/**
 * MSR Admin Dashboard
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../config/config.php';

// Check authentication
requireAuth();

// Initialize database connection
$db = Database::getInstance();

// Get current admin info
$current_admin = getCurrentAdmin();

// Get statistics with proper error handling
try {
    $total_reviews = $db->count("SELECT COUNT(*) FROM reviews");
    $published_reviews = $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'");
    $pending_reviews = $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'pending'");
    $total_categories = $db->count("SELECT COUNT(*) FROM categories WHERE status = 'active'");
    $total_views = $db->count("SELECT SUM(view_count) FROM reviews");
    $pending_comments = $db->count("SELECT COUNT(*) FROM comments WHERE status = 'pending'");
    
    // Get recent reviews
    $recent_reviews = $db->fetchAll("
        SELECT r.*, c.name as category_name 
        FROM reviews r 
        LEFT JOIN review_categories rc ON r.id = rc.review_id
        LEFT JOIN categories c ON rc.category_id = c.id
        ORDER BY r.created_at DESC 
        LIMIT 5
    ");
    
    // Get popular reviews
    $popular_reviews = $db->fetchAll("
        SELECT r.*, c.name as category_name 
        FROM reviews r 
        LEFT JOIN review_categories rc ON r.id = rc.review_id
        LEFT JOIN categories c ON rc.category_id = c.id
        WHERE r.status = 'published'
        ORDER BY r.view_count DESC 
        LIMIT 5
    ");
    
    // Get pending user submissions
    $pending_submissions = $db->fetchAll("
        SELECT * FROM reviews 
        WHERE status = 'pending' AND created_by = 'user'
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    
} catch (Exception $e) {
    $total_reviews = 0;
    $published_reviews = 0;
    $pending_reviews = 0;
    $total_categories = 0;
    $total_views = 0;
    $pending_comments = 0;
    $recent_reviews = [];
    $popular_reviews = [];
    $pending_submissions = [];
}

// Include admin header
include 'includes/header.php';
?>

<!-- Page Header -->
<div class="bg-white shadow-sm border-b border-gray-200">
    <div class="px-6 py-4">
        <h1 class="text-2xl font-bold text-gray-900">ড্যাশবোর্ড</h1>
        <p class="text-gray-600">আপনার রিভিউ সাইটের সম্পূর্ণ ওভারভিউ</p>
    </div>
</div>

<div class="p-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Reviews -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">মোট রিভিউ</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo number_format($total_reviews); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4zM9 6v12h6V6H9z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 text-sm font-medium"><?php echo $published_reviews; ?> প্রকাশিত</span>
            </div>
        </div>
        
        <!-- Pending Reviews -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">অপেক্ষমাণ রিভিউ</p>
                    <p class="text-3xl font-bold text-orange-600"><?php echo number_format($pending_reviews); ?></p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="reviews/pending.php" class="text-blue-600 hover:text-blue-700 text-sm font-medium">পর্যালোচনা করুন →</a>
            </div>
        </div>
        
        <!-- Categories -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">ক্যাটেগরি</p>
                    <p class="text-3xl font-bold text-purple-600"><?php echo number_format($total_categories); ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="categories/index.php" class="text-blue-600 hover:text-blue-700 text-sm font-medium">ব্যবস্থাপনা →</a>
            </div>
        </div>
        
        <!-- Total Views -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">মোট ভিউ</p>
                    <p class="text-3xl font-bold text-green-600"><?php echo number_format($total_views); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-gray-500 text-sm">সকল রিভিউর সম্মিলিত ভিউ</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">দ্রুত অ্যাকশন</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="reviews/add.php" class="flex flex-col items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors group">
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mb-3 group-hover:bg-blue-600 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">নতুন রিভিউ</span>
            </a>
            
            <a href="categories/add.php" class="flex flex-col items-center p-4 bg-green-50 hover:bg-green-100 rounded-xl transition-colors group">
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mb-3 group-hover:bg-green-600 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">নতুন ক্যাটেগরি</span>
            </a>
            
            <a href="settings/index.php" class="flex flex-col items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-xl transition-colors group">
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mb-3 group-hover:bg-purple-600 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">সাইট সেটিংস</span>
            </a>
            
            <a href="../" target="_blank" class="flex flex-col items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-xl transition-colors group">
                <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mb-3 group-hover:bg-orange-600 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">সাইট ভিজিট</span>
            </a>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Reviews -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">সাম্প্রতিক রিভিউ</h2>
                <a href="reviews/index.php" class="text-blue-600 hover:text-blue-700 text-sm font-medium">সব দেখুন</a>
            </div>
            
            <div class="space-y-4">
                <?php if (!empty($recent_reviews)): ?>
                    <?php foreach ($recent_reviews as $review): ?>
                        <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                            <div class="w-12 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                <?php if (!empty($review['poster_image'])): ?>
                                    <img src="<?php echo (defined('UPLOADS_URL') ? UPLOADS_URL : '/uploads'); ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($review['title']); ?>"
                                         class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4zM9 6v12h6V6H9z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate">
                                    <a href="reviews/edit.php?id=<?php echo $review['id']; ?>">
                                        <?php echo htmlspecialchars($review['title']); ?>
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600">
                                    <?php echo $review['category_name'] ? htmlspecialchars($review['category_name']) : 'সাধারণ'; ?> 
                                    <?php if (isset($review['year'])): ?>• <?php echo $review['year']; ?><?php endif; ?>
                                </p>
                                <div class="flex items-center mt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        <?php echo $review['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                        <?php echo $review['status'] === 'published' ? 'প্রকাশিত' : 'অপেক্ষমাণ'; ?>
                                    </span>
                                    <span class="text-xs text-gray-500 ml-2">
                                        <?php echo date('d M Y', strtotime($review['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php if (isset($review['rating'])): ?>
                                    <span class="text-yellow-500">⭐</span>
                                    <span class="font-medium"><?php echo $review['rating']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4zM9 6v12h6V6H9z"></path>
                        </svg>
                        <p class="text-gray-500">কোন রিভিউ পাওয়া যায়নি</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pending Submissions -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">ব্যবহারকারীর জমা দেওয়া রিভিউ</h2>
                <a href="reviews/pending.php" class="text-blue-600 hover:text-blue-700 text-sm font-medium">সব দেখুন</a>
            </div>
            
            <div class="space-y-4">
                <?php if (!empty($pending_submissions)): ?>
                    <?php foreach ($pending_submissions as $submission): ?>
                        <div class="flex items-center space-x-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <div class="w-12 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                <?php if (!empty($submission['poster_image'])): ?>
                                    <img src="<?php echo (defined('UPLOADS_URL') ? UPLOADS_URL : '/uploads'); ?>/reviews/<?php echo $submission['poster_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($submission['title']); ?>" 
                                         class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4zM9 6v12h6V6H9z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 truncate">
                                    <?php echo htmlspecialchars($submission['title']); ?>
                                </h3>
                                <p class="text-sm text-gray-600">
                                    জমা দিয়েছেন: <?php echo htmlspecialchars($submission['reviewer_name'] ?? 'অজানা'); ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?php echo date('d M Y', strtotime($submission['created_at'])); ?>
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="reviews/edit.php?id=<?php echo $submission['id']; ?>" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                    পর্যালোচনা
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500">কোনো অপেক্ষমাণ রিভিউ নেই</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="mt-8 bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">সিস্টেম তথ্য</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                </svg>
                <h3 class="font-semibold text-gray-900">PHP ভার্সন</h3>
                <p class="text-gray-600"><?php echo PHP_VERSION; ?></p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                </svg>
                <h3 class="font-semibold text-gray-900">ডেটাবেস</h3>
                <p class="text-gray-600">MySQL</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <svg class="w-8 h-8 text-purple-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="font-semibold text-gray-900">শেষ লগইন</h3>
                <p class="text-gray-600"><?php echo isset($current_admin['login_time']) && $current_admin['login_time'] ? date('d M Y H:i', $current_admin['login_time']) : 'এই প্রথম'; ?></p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>