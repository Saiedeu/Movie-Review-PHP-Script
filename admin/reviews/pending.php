<?php
/**
 * Pending Reviews Management - User Submissions
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../../config/config.php';

// Require authentication
requireAuth();

// Get database instance
$db = Database::getInstance();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'approve_review') {
        $review_id = intval($_POST['review_id'] ?? 0);
        
        if ($review_id > 0 && verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $updated = $db->query("UPDATE reviews SET status = 'published', updated_at = NOW() WHERE id = ? AND created_by = 'user'", [$review_id]);
            
            if ($updated) {
                $_SESSION['success_message'] = 'রিভিউ অনুমোদন করা হয়েছে এবং প্রকাশিত হয়েছে।';
            } else {
                $_SESSION['error_message'] = 'রিভিউ অনুমোদন করতে সমস্যা হয়েছে।';
            }
        }
        
        header('Location: pending.php');
        exit;
    }
    
    if ($action === 'reject_review') {
        $review_id = intval($_POST['review_id'] ?? 0);
        $reason = sanitize($_POST['rejection_reason'] ?? '');
        
        if ($review_id > 0 && verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            // Update status to rejected (we can add a new status)
            $updated = $db->query("UPDATE reviews SET status = 'draft', updated_at = NOW() WHERE id = ? AND created_by = 'user'", [$review_id]);
            
            if ($updated) {
                $_SESSION['success_message'] = 'রিভিউ প্রত্যাখ্যান করা হয়েছে।';
            } else {
                $_SESSION['error_message'] = 'রিভিউ প্রত্যাখ্যান করতে সমস্যা হয়েছে।';
            }
        }
        
        header('Location: pending.php');
        exit;
    }
    
    if ($action === 'bulk_action') {
        $bulk_action = $_POST['bulk_action'] ?? '';
        $review_ids = $_POST['review_ids'] ?? [];
        
        if (!empty($review_ids) && verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $placeholders = str_repeat('?,', count($review_ids) - 1) . '?';
            
            switch ($bulk_action) {
                case 'approve':
                    $updated = $db->query("UPDATE reviews SET status = 'published', updated_at = NOW() WHERE id IN ($placeholders) AND created_by = 'user'", $review_ids);
                    $_SESSION['success_message'] = count($review_ids) . 'টি রিভিউ অনুমোদন করা হয়েছে।';
                    break;
                case 'reject':
                    $updated = $db->query("UPDATE reviews SET status = 'draft', updated_at = NOW() WHERE id IN ($placeholders) AND created_by = 'user'", $review_ids);
                    $_SESSION['success_message'] = count($review_ids) . 'টি রিভিউ প্রত্যাখ্যান করা হয়েছে।';
                    break;
                case 'delete':
                    // Delete associated data first
                    $db->query("DELETE FROM review_categories WHERE review_id IN ($placeholders)", $review_ids);
                    $db->query("DELETE FROM comments WHERE review_id IN ($placeholders)", $review_ids);
                    $db->query("DELETE FROM review_likes WHERE review_id IN ($placeholders)", $review_ids);
                    // Delete reviews
                    $db->query("DELETE FROM reviews WHERE id IN ($placeholders) AND created_by = 'user'", $review_ids);
                    $_SESSION['success_message'] = count($review_ids) . 'টি রিভিউ মুছে ফেলা হয়েছে।';
                    break;
            }
        }
        
        header('Location: pending.php');
        exit;
    }
}

// Get filter parameters
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build query conditions for pending user submissions
$where_conditions = ["r.status = 'pending'", "r.created_by = 'user'"];
$params = [];

if ($search) {
    $where_conditions[] = "(r.title LIKE ? OR r.reviewer_name LIKE ? OR r.reviewer_email LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

// Get total count
$total_reviews = $db->count("SELECT COUNT(*) FROM reviews r $where_clause", $params);
$total_pages = ceil($total_reviews / $per_page);

// Get pending reviews
$pending_reviews = $db->fetchAll("
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
", $params);

// Get summary stats
$total_pending = $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'pending' AND created_by = 'user'");
$total_approved_today = $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published' AND created_by = 'user' AND DATE(updated_at) = CURDATE()");

// Include admin header
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="bn" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অপেক্ষমাণ রিভিউ - <?php echo SITE_NAME; ?></title>
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; }
        
        /* Fixed Scrolling */
        html, body {
            height: auto !important;
            overflow-x: hidden;
        }

        @media (max-width: 768px) {
            .admin-content {
                padding-top: 70px;
                padding-left: 16px;
                padding-right: 16px;
                margin-left: 0 !important;
                width: 100%;
                overflow-y: visible;
                height: auto;
                min-height: calc(100vh - 70px);
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
        }

        @media (min-width: 769px) {
            .mobile-header {
                display: none;
            }
            
            .admin-content {
                height: auto;
                overflow-y: visible;
                min-height: 100vh;
            }
        }
        
        .status-pending { background-color: #fef3c7; color: #d97706; }
        .status-approved { background-color: #d1fae5; color: #059669; }
        .user-submission { background-color: #eff6ff; }
        .spoiler-indicator { background-color: #fed7aa; color: #ea580c; }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Mobile Header -->
    <div class="mobile-header">
        <button id="mobile-menu-btn" class="p-2 rounded-lg hover:bg-gray-100">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <h1 class="text-lg font-bold">অপেক্ষমাণ রিভিউ</h1>
        <div class="w-8"></div>
    </div>

    <div class="flex-1 bg-gray-100 admin-content">
        <!-- Page Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 md:px-6 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <a href="index.php" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <div>
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900">অপেক্ষমাণ রিভিউ</h1>
                            <p class="text-gray-600 text-sm md:text-base">ব্যবহারকারীদের জমা দেওয়া রিভিউ পর্যালোচনা করুন</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-orange-600"><?php echo $total_pending; ?></div>
                        <div class="text-sm text-gray-600">অপেক্ষমাণ</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-4 md:p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-orange-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">অপেক্ষমাণ রিভিউ</p>
                            <p class="text-2xl font-bold text-orange-600"><?php echo number_format($total_pending); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">আজ অনুমোদিত</p>
                            <p class="text-2xl font-bold text-green-600"><?php echo number_format($total_approved_today); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">মোট পাওয়া</p>
                            <p class="text-2xl font-bold text-blue-600"><?php echo number_format($total_reviews); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="bg-white rounded-xl shadow-sm border p-4 md:p-6 mb-6">
                <form method="GET" class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="text" 
                               name="search" 
                               value="<?php echo htmlspecialchars($search); ?>"
                               placeholder="রিভিউ শিরোনাম, লেখক নাম বা ইমেইল অনুসন্ধান..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-search mr-2"></i>অনুসন্ধান
                    </button>
                </form>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <!-- Reviews Table -->
            <div class="bg-white rounded-xl shadow-sm border">
                <?php if (!empty($pending_reviews)): ?>
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
                                        <option value="approve">অনুমোদন করুন</option>
                                        <option value="reject">প্রত্যাখ্যান করুন</option>
                                        <option value="delete">মুছে ফেলুন</option>
                                    </select>
                                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
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
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">রিভিউ তথ্য</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">জমাদানকারী</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">রেটিং</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">কন্টেন্ট</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">জমার তারিখ</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">অ্যাকশন</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($pending_reviews as $review): ?>
                                        <tr class="hover:bg-gray-50 user-submission">
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="review_ids[]" value="<?php echo $review['id']; ?>" class="review-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded">
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center space-x-4">
                                                    <div class="w-12 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                                        <?php if ($review['poster_image']): ?>
                                                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                                                 alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                                                 class="w-full h-full object-cover">
                                                        <?php else: ?>
                                                            <div class="w-full h-full flex items-center justify-center bg-gray-300">
                                                                <i class="fas fa-film text-gray-500"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="text-sm font-medium text-gray-900 truncate max-w-xs">
                                                            <?php echo htmlspecialchars($review['title']); ?>
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            <?php echo $review['type'] === 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?> • <?php echo $review['year']; ?>
                                                            <?php if ($review['language']): ?>
                                                                • <?php echo htmlspecialchars($review['language']); ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            <?php if ($review['categories']): ?>
                                                                <span class="bg-gray-100 px-2 py-1 rounded"><?php echo htmlspecialchars($review['categories']); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if (!empty($review['spoiler_content'])): ?>
                                                            <div class="text-xs spoiler-indicator px-2 py-1 rounded-full inline-block mt-1">
                                                                ⚠️ স্পয়লার আছে
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm">
                                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($review['reviewer_name']); ?></div>
                                                    <div class="text-gray-500 text-xs"><?php echo htmlspecialchars($review['reviewer_email']); ?></div>
                                                    <div class="text-blue-600 text-xs mt-1">
                                                        <i class="fas fa-user mr-1"></i>ব্যবহারকারী জমা
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex text-yellow-400 text-sm mr-2">
                                                        <?php echo getStarRating($review['rating']); ?>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-900"><?php echo $review['rating']; ?>/5</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="text-sm text-gray-900">
                                                    <div class="max-w-xs truncate"><?php echo htmlspecialchars(strip_tags(truncateText($review['content'], 100))); ?></div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        <?php echo str_word_count(strip_tags($review['content'])); ?> শব্দ
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div><?php echo date('d M Y', strtotime($review['created_at'])); ?></div>
                                                <div class="text-xs"><?php echo date('H:i', strtotime($review['created_at'])); ?></div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="flex flex-col space-y-2">
                                                    <!-- Approve Button -->
                                                    <form method="POST" class="inline">
                                                        <input type="hidden" name="action" value="approve_review">
                                                        <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                        <button type="submit" 
                                                                onclick="return confirm('এই রিভিউটি অনুমোদন করতে চান?')"
                                                                class="w-full bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs transition-colors">
                                                            <i class="fas fa-check mr-1"></i>অনুমোদন
                                                        </button>
                                                    </form>
                                                    
                                                    <!-- View/Edit -->
                                                    <a href="edit.php?id=<?php echo $review['id']; ?>" 
                                                       class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-3 py-1 rounded text-xs transition-colors">
                                                        <i class="fas fa-edit mr-1"></i>সম্পাদনা
                                                    </a>
                                                    
                                                    <!-- Preview -->
                                                    <button onclick="previewReview(<?php echo $review['id']; ?>)" 
                                                            class="w-full bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-xs transition-colors">
                                                        <i class="fas fa-eye mr-1"></i>প্রিভিউ
                                                    </button>
                                                    
                                                    <!-- Reject -->
                                                    <form method="POST" class="inline">
                                                        <input type="hidden" name="action" value="reject_review">
                                                        <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                        <button type="submit" 
                                                                onclick="return confirm('এই রিভিউটি প্রত্যাখ্যান করতে চান?')"
                                                                class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs transition-colors">
                                                            <i class="fas fa-times mr-1"></i>প্রত্যাখ্যান
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
                                    <?php if ($page > 1): ?>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                                           class="px-3 py-2 text-sm rounded-lg transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200">‹</a>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    $start_page = max(1, $page - 2);
                                    $end_page = min($total_pages, $start_page + 4);
                                    for ($i = $start_page; $i <= $end_page; $i++): 
                                    ?>
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
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-2xl text-orange-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">কোনো অপেক্ষমাণ রিভিউ নেই</h3>
                        <p class="text-gray-500 mb-4">বর্তমানে কোনো ব্যবহারকারী রিভিউ অপেক্ষমাণ নেই।</p>
                        <a href="index.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            সব রিভিউ দেখুন
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">রিভিউ প্রিভিউ</h3>
                        <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="preview-content"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu functionality
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const sidebar = document.querySelector('.admin-sidebar');
            if (sidebar) {
                sidebar.classList.toggle('mobile-open');
            }
        });

        // Select all functionality
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.review-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Bulk form submission
        document.getElementById('bulk-form').addEventListener('submit', function(e) {
            const action = this.querySelector('select[name="bulk_action"]').value;
            const checkedBoxes = this.querySelectorAll('.review-checkbox:checked');
            
            if (!action) {
                e.preventDefault();
                alert('অনুগ্রহ করে একটি অ্যাকশন নির্বাচন করুন।');
                return;
            }
            
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('অনুগ্রহ করে অন্তত একটি রিভিউ নির্বাচন করুন।');
                return;
            }
            
            const actionText = {
                'approve': 'অনুমোদন',
                'reject': 'প্রত্যাখ্যান',
                'delete': 'মুছে ফেলা'
            };
            
            if (!confirm(`আপনি কি নিশ্চিত যে ${checkedBoxes.length}টি রিভিউ ${actionText[action]} করতে চান?`)) {
                e.preventDefault();
                return;
            }
        });

        // Preview functionality
        function previewReview(reviewId) {
            fetch(`../api/preview-review.php?id=${reviewId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('preview-content').innerHTML = data.html;
                        document.getElementById('preview-modal').classList.remove('hidden');
                    } else {
                        alert('প্রিভিউ লোড করতে সমস্যা হয়েছে।');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('প্রিভিউ লোড করতে সমস্যা হয়েছে।');
                });
        }

        function closePreview() {
            document.getElementById('preview-modal').classList.add('hidden');
        }

        // Auto-refresh every 30 seconds to check for new submissions
        setInterval(function() {
            const currentCount = <?php echo $total_pending; ?>;
            
            fetch('?ajax=count')
                .then(response => response.json())
                .then(data => {
                    if (data.count > currentCount) {
                        // Show notification for new submissions
                        showNotification(`${data.count - currentCount}টি নতুন রিভিউ এসেছে!`);
                    }
                })
                .catch(error => console.log('Auto-refresh failed:', error));
        }, 30000);

        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-bell mr-2"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }
    </script>
</body>
</html>