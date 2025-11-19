<?php
/**
 * Enhanced Admin Reviews Management with Mobile Scrolling Fix
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../../config/config.php';

// Require authentication
requireAuth();

// Get database instance
$db = Database::getInstance();

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    $action = $_POST['bulk_action'];
    $review_ids = $_POST['review_ids'] ?? [];
    
    if (!empty($review_ids) && verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $placeholders = str_repeat('?,', count($review_ids) - 1) . '?';
        
        switch ($action) {
            case 'publish':
                $db->query("UPDATE reviews SET status = 'published' WHERE id IN ($placeholders)", $review_ids);
                $_SESSION['success_message'] = count($review_ids) . ' টি রিভিউ প্রকাশিত হয়েছে।';
                break;
            case 'draft':
                $db->query("UPDATE reviews SET status = 'draft' WHERE id IN ($placeholders)", $review_ids);
                $_SESSION['success_message'] = count($review_ids) . ' টি রিভিউ ড্রাফট করা হয়েছে।';
                break;
            case 'delete':
                // Delete associated data
                $db->query("DELETE FROM review_categories WHERE review_id IN ($placeholders)", $review_ids);
                $db->query("DELETE FROM comments WHERE review_id IN ($placeholders)", $review_ids);
                $db->query("DELETE FROM review_likes WHERE review_id IN ($placeholders)", $review_ids);
                // Delete reviews
                $db->query("DELETE FROM reviews WHERE id IN ($placeholders)", $review_ids);
                $_SESSION['success_message'] = count($review_ids) . ' টি রিভিউ মুছে ফেলা হয়েছে।';
                break;
        }
        
        header('Location: index.php');
        exit;
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = ADMIN_POSTS_PER_PAGE;
$offset = ($page - 1) * $per_page;

// Build query
$where_conditions = [];
$params = [];

if ($status_filter) {
    $where_conditions[] = "r.status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $where_conditions[] = "(r.title LIKE ? OR r.reviewer_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_query = "SELECT COUNT(*) FROM reviews r $where_clause";
$total_reviews = $db->count($count_query, $params);
$total_pages = ceil($total_reviews / $per_page);

// Get reviews with enhanced data
$reviews_query = "
    SELECT r.*, 
           GROUP_CONCAT(c.name SEPARATOR ', ') as categories,
           COALESCE(comment_count.total, 0) as comment_count
    FROM reviews r 
    LEFT JOIN review_categories rc ON r.id = rc.review_id
    LEFT JOIN categories c ON rc.category_id = c.id
    LEFT JOIN (
        SELECT review_id, COUNT(*) as total 
        FROM comments 
        WHERE status = 'approved' 
        GROUP BY review_id
    ) comment_count ON r.id = comment_count.review_id
    $where_clause
    GROUP BY r.id
    ORDER BY r.created_at DESC 
    LIMIT $per_page OFFSET $offset
";

$reviews = $db->fetchAll($reviews_query, $params);

// Get status counts
$status_counts = [
    'all' => $db->count("SELECT COUNT(*) FROM reviews"),
    'published' => $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"),
    'pending' => $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'pending'"),
    'draft' => $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'draft'")
];

// Include admin header
include '../includes/header.php';
?>

<style>
/* Enhanced Mobile Scrolling Fix */
@media (max-width: 768px) {
    body {
        overflow-x: hidden;
        height: auto;
    }
    
    .admin-layout {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    .admin-sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        width: 280px;
        height: 100vh;
        z-index: 1000;
        transition: left 0.3s ease;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .admin-sidebar.mobile-open {
        left: 0;
    }
    
    .admin-content {
        flex: 1;
        margin-left: 0 !important;
        padding: 16px;
        width: 100%;
        height: auto;
        min-height: calc(100vh - 60px);
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding-top: 70px; /* Account for mobile header */
    }
    
    .mobile-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: white;
        border-bottom: 1px solid #e5e7eb;
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 16px;
    }
    
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0 -16px;
        padding: 0 16px;
    }
    
    .table-responsive {
        min-width: 800px;
    }
    
    /* Fixed action buttons for mobile */
    .mobile-actions {
        position: sticky;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid #e5e7eb;
        padding: 12px 16px;
        z-index: 100;
    }
}

/* Desktop styles */
@media (min-width: 769px) {
    .mobile-header {
        display: none;
    }
    
    .admin-content {
        overflow-y: auto;
        height: 100vh;
    }
}

/* Table improvements for mobile */
.table-responsive table {
    font-size: 14px;
}

.table-responsive td {
    white-space: nowrap;
    padding: 8px 12px;
}

.table-responsive .review-info {
    max-width: 200px;
    white-space: normal;
}

/* Enhanced mobile menu */
.mobile-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.mobile-overlay.active {
    opacity: 1;
    visibility: visible;
}

/* Better button spacing for mobile */
.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

@media (max-width: 640px) {
    .action-buttons {
        flex-direction: column;
        gap: 4px;
    }
    
    .action-buttons .btn {
        width: 100%;
        text-align: center;
    }
}
</style>

<!-- Mobile Header -->
<div class="mobile-header">
    <button id="mobile-menu-btn" class="p-2 rounded-lg hover:bg-gray-100">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
    <h1 class="text-lg font-bold">রিভিউ ব্যবস্থাপনা</h1>
    <div class="w-8"></div>
</div>

<!-- Mobile Overlay -->
<div id="mobile-overlay" class="mobile-overlay" onclick="closeMobileMenu()"></div>

<div class="flex-1 bg-gray-100 admin-content">
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 hidden md:block">রিভিউ ব্যবস্থাপনা</h1>
                    <p class="text-gray-600 hidden md:block">সব রিভিউ দেখুন ও পরিচালনা করুন</p>
                </div>
                <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm md:text-base">
                    <svg class="w-4 h-4 md:w-5 md:h-5 inline mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span class="hidden sm:inline">নতুন রিভিউ</span>
                    <span class="sm:hidden">নতুন</span>
                </a>
            </div>
        </div>
    </div>
    
    <div class="p-4 md:p-6">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="p-4 md:p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <!-- Status Filter Tabs -->
                    <div class="flex flex-wrap gap-2">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => '', 'page' => 1])); ?>" 
                           class="px-3 py-2 rounded-lg font-medium transition-colors text-sm <?php echo $status_filter === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            সব (<?php echo $status_counts['all']; ?>)
                        </a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'published', 'page' => 1])); ?>" 
                           class="px-3 py-2 rounded-lg font-medium transition-colors text-sm <?php echo $status_filter === 'published' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            প্রকাশিত (<?php echo $status_counts['published']; ?>)
                        </a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'pending', 'page' => 1])); ?>" 
                           class="px-3 py-2 rounded-lg font-medium transition-colors text-sm <?php echo $status_filter === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            অপেক্ষমাণ (<?php echo $status_counts['pending']; ?>)
                        </a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'draft', 'page' => 1])); ?>" 
                           class="px-3 py-2 rounded-lg font-medium transition-colors text-sm <?php echo $status_filter === 'draft' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            ড্রাফট (<?php echo $status_counts['draft']; ?>)
                        </a>
                    </div>
                    
                    <!-- Search -->
                    <form method="GET" class="flex space-x-2">
                        <?php foreach ($_GET as $key => $value): ?>
                            <?php if ($key !== 'search' && $key !== 'page'): ?>
                                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <input type="text" 
                               name="search" 
                               value="<?php echo htmlspecialchars($search); ?>"
                               placeholder="রিভিউ বা লেখক খুঁজুন..." 
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base">
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Reviews Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <?php if (!empty($reviews)): ?>
                <form method="POST" id="bulk-form">
                    <div class="p-4 md:p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                            <div class="flex items-center space-x-4">
                                <input type="checkbox" id="select-all" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                <label for="select-all" class="text-sm font-medium text-gray-700">সব নির্বাচন</label>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <select name="bulk_action" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <option value="">বাল্ক অ্যাকশন</option>
                                    <option value="publish">প্রকাশ করুন</option>
                                    <option value="draft">ড্রাফট করুন</option>
                                    <option value="delete">মুছে ফেলুন</option>
                                </select>
                                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    প্রয়োগ করুন
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table class="w-full table-responsive">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">রিভিউ</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">লেখক</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ক্যাটেগরি</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">রেটিং</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">পরিসংখ্যান</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">স্ট্যাটাস</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">তারিখ</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">অ্যাকশন</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($reviews as $review): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="review_ids[]" value="<?php echo $review['id']; ?>" class="review-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded">
                                        </td>
                                        <td class="px-4 py-4 review-info">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                                    <?php if ($review['poster_image']): ?>
                                                        <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                                             alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                                             class="w-full h-full object-cover">
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 line-clamp-2">
                                                        <?php echo htmlspecialchars($review['title']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        <?php echo $review['type'] === 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?> • <?php echo $review['year']; ?>
                                                    </div>
                                                    <?php if (!empty($review['spoiler_content'])): ?>
                                                        <div class="text-xs text-orange-600 font-medium mt-1">
                                                            ⚠️ স্পয়লার আছে
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($review['reviewer_name']); ?>
                                            </div>
                                            <?php if ($review['created_by'] === 'user'): ?>
                                                <div class="text-xs text-blue-600">ব্যবহারকারী জমা</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-900">
                                                <?php echo htmlspecialchars($review['categories'] ?: 'কোনো ক্যাটেগরি নেই'); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex text-yellow-400 text-sm mr-2">
                                                    <?php echo getStarRating($review['rating']); ?>
                                                </div>
                                                <span class="text-sm text-gray-600"><?php echo $review['rating']; ?>/5</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <div class="flex items-center space-x-2">
                                                    <span title="ভিউ">👁️ <?php echo number_format($review['view_count']); ?></span>
                                                </div>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span title="লাইক">❤️ <?php echo number_format($review['like_count']); ?></span>
                                                    <span title="মন্তব্য">💬 <?php echo number_format($review['comment_count']); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                <?php 
                                                switch($review['status']) {
                                                    case 'published': echo 'bg-green-100 text-green-800'; break;
                                                    case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                                    case 'draft': echo 'bg-gray-100 text-gray-800'; break;
                                                }
                                                ?>">
                                                <?php 
                                                switch($review['status']) {
                                                    case 'published': echo 'প্রকাশিত'; break;
                                                    case 'pending': echo 'অপেক্ষমাণ'; break;
                                                    case 'draft': echo 'ড্রাফট'; break;
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('d M Y', strtotime($review['created_at'])); ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="action-buttons">
                                                <a href="edit.php?id=<?php echo $review['id']; ?>" 
                                                   class="btn text-blue-600 hover:text-blue-700" title="সম্পাদনা">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <a href="../../review/<?php echo $review['slug']; ?>" 
                                                   target="_blank"
                                                   class="btn text-gray-600 hover:text-gray-700" title="দেখুন">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                </a>
                                                <a href="delete.php?id=<?php echo $review['id']; ?>" 
                                                   onclick="return showDeleteConfirmation('আপনি কি নিশ্চিত যে এই রিভিউটি মুছে ফেলতে চান?')"
                                                   class="btn text-red-600 hover:text-red-700" title="মুছুন">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Mobile Actions -->
                    <div class="mobile-actions md:hidden">
                        <div class="flex items-center space-x-2">
                            <select name="bulk_action_mobile" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="">বাল্ক অ্যাকশন</option>
                                <option value="publish">প্রকাশ করুন</option>
                                <option value="draft">ড্রাফট করুন</option>
                                <option value="delete">মুছে ফেলুন</option>
                            </select>
                            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                প্রয়োগ
                            </button>
                        </div>
                    </div>
                    
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                </form>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="px-4 md:px-6 py-3 border-t border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                            <div class="text-sm text-gray-700 text-center md:text-left">
                                <?php 
                                $start = $offset + 1;
                                $end = min($offset + $per_page, $total_reviews);
                                echo "দেখাচ্ছে $start থেকে $end, মোট $total_reviews টি";
                                ?>
                            </div>
                            
                            <div class="flex justify-center space-x-1">
                                <?php 
                                // Show fewer pages on mobile
                                $show_pages = 5;
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $start_page + $show_pages - 1);
                                
                                if ($page > 1): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                                       class="px-3 py-2 text-sm rounded-lg transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200">‹</a>
                                <?php endif; ?>
                                
                                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                       class="px-3 py-2 text-sm rounded-lg transition-colors <?php echo $i === $page ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                                       class="px-3 py-2 text-sm rounded-lg transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200">›</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">কোনো রিভিউ পাওয়া যায়নি</h3>
                    <p class="text-gray-500 mb-4">আপনার প্রথম রিভিউ তৈরি করুন।</p>
                    <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        নতুন রিভিউ যোগ করুন
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Mobile menu functionality
document.getElementById('mobile-menu-btn').addEventListener('click', function() {
    const sidebar = document.querySelector('.admin-sidebar');
    const overlay = document.getElementById('mobile-overlay');
    
    sidebar.classList.add('mobile-open');
    overlay.classList.add('active');
});

function closeMobileMenu() {
    const sidebar = document.querySelector('.admin-sidebar');
    const overlay = document.getElementById('mobile-overlay');
    
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
}

// Select all functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.review-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Sync mobile bulk action with main form
document.querySelector('select[name="bulk_action_mobile"]').addEventListener('change', function() {
    document.querySelector('select[name="bulk_action"]').value = this.value;
});

// Enhanced bulk form submission
document.getElementById('bulk-form').addEventListener('submit', function(e) {
    const action = this.querySelector('select[name="bulk_action"]').value || 
                   this.querySelector('select[name="bulk_action_mobile"]').value;
    const checkedBoxes = this.querySelectorAll('.review-checkbox:checked');
    
    if (!action) {
        e.preventDefault();
        showCustomAlert('অনুগ্রহ করে একটি অ্যাকশন নির্বাচন করুন।');
        return;
    }
    
    if (checkedBoxes.length === 0) {
        e.preventDefault();
        showCustomAlert('অনুগ্রহ করে অন্তত একটি রিভিউ নির্বাচন করুন।');
        return;
    }
    
    if (action === 'delete') {
        if (!showDeleteConfirmation(`আপনি কি নিশ্চিত যে ${checkedBoxes.length}টি রিভিউ মুছে ফেলতে চান?`)) {
            e.preventDefault();
            return;
        }
    }
    
    // Update mobile bulk action value
    this.querySelector('select[name="bulk_action"]').value = action;
});

// Custom alert function
function showCustomAlert(message) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-lg max-w-sm w-full mx-4 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">বিজ্ঞপ্তি</h3>
            <p class="text-gray-700 mb-4">${message}</p>
            <div class="flex justify-end">
                <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">ঠিক আছে</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

// Custom confirm function
function showDeleteConfirmation(message) {
    return new Promise((resolve) => {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-lg max-w-sm w-full mx-4 p-6">
                <h3 class="text-lg font-bold text-red-900 mb-4">নিশ্চিতকরণ</h3>
                <p class="text-gray-700 mb-4">${message}</p>
                <div class="flex justify-end space-x-3">
                    <button onclick="this.closest('.fixed').remove(); resolve(false)" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">বাতিল</button>
                    <button onclick="this.closest('.fixed').remove(); resolve(true)" class="px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded">মুছে ফেলুন</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Make resolve available to onclick handlers
        window.resolve = resolve;
    });
}

// Fix for mobile scrolling
if (window.innerWidth <= 768) {
    // Prevent zoom on input focus
    document.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('focus', function() {
            const viewport = document.querySelector('meta[name="viewport"]');
            if (viewport) {
                viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
            }
        });
        
        element.addEventListener('blur', function() {
            const viewport = document.querySelector('meta[name="viewport"]');
            if (viewport) {
                viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, user-scalable=yes');
            }
        });
    });
}
</script>

<?php include '../includes/footer.php'; ?>