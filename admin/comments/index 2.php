<?php
/**
 * Admin Comments Management
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../../config/config.php';

// Check authentication
requireAuth();

// Initialize database connection
$db = Database::getInstance();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_status') {
        $comment_id = intval($_POST['comment_id'] ?? 0);
        $status = sanitize($_POST['status'] ?? '');
        
        if ($comment_id > 0 && in_array($status, ['approved', 'pending', 'spam'])) {
            $updated = $db->query("UPDATE comments SET status = ? WHERE id = ?", [$status, $comment_id]);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $updated ? true : false,
                'message' => $updated ? 'স্ট্যাটাস আপডেট করা হয়েছে।' : 'আপডেট করতে সমস্যা হয়েছে।'
            ]);
            exit;
        }
    }
    
    if ($action === 'delete_comment') {
        $comment_id = intval($_POST['comment_id'] ?? 0);
        
        if ($comment_id > 0) {
            // Delete comment and its replies
            $db->beginTransaction();
            try {
                $db->query("DELETE FROM comments WHERE parent_id = ?", [$comment_id]);
                $deleted = $db->query("DELETE FROM comments WHERE id = ?", [$comment_id]);
                
                if ($deleted) {
                    $db->commit();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'মন্তব্য মুছে ফেলা হয়েছে।']);
                } else {
                    throw new Exception('Delete failed');
                }
            } catch (Exception $e) {
                $db->rollback();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'মুছতে সমস্যা হয়েছে।']);
            }
            exit;
        }
    }
    
    if ($action === 'bulk_action') {
        $comment_ids = array_map('intval', $_POST['comment_ids'] ?? []);
        $bulk_action = sanitize($_POST['bulk_action'] ?? '');
        
        if (!empty($comment_ids) && in_array($bulk_action, ['approve', 'pending', 'spam', 'delete'])) {
            $placeholders = str_repeat('?,', count($comment_ids) - 1) . '?';
            
            try {
                if ($bulk_action === 'delete') {
                    $deleted = $db->query("DELETE FROM comments WHERE id IN ($placeholders)", $comment_ids);
                    $message = $deleted ? count($comment_ids) . 'টি মন্তব্য মুছে ফেলা হয়েছে।' : 'মুছতে সমস্যা হয়েছে।';
                } else {
                    $status = $bulk_action === 'approve' ? 'approved' : $bulk_action;
                    $updated = $db->query("UPDATE comments SET status = ? WHERE id IN ($placeholders)", array_merge([$status], $comment_ids));
                    $message = $updated ? count($comment_ids) . 'টি মন্তব্যের স্ট্যাটাস আপডেট করা হয়েছে।' : 'আপডেট করতে সমস্যা হয়েছে।';
                }
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $message]);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'অপারেশন সম্পন্ন করতে সমস্যা হয়েছে।']);
            }
            exit;
        }
    }
}

// Get filter parameters
$status_filter = sanitize($_GET['status'] ?? 'all');
$search = sanitize($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build query conditions
$where_conditions = [];
$params = [];

if ($status_filter !== 'all') {
    $where_conditions[] = "c.status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $where_conditions[] = "(c.name LIKE ? OR c.email LIKE ? OR c.comment LIKE ? OR r.title LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$total_comments = $db->count("
    SELECT COUNT(*) 
    FROM comments c 
    LEFT JOIN reviews r ON c.review_id = r.id 
    $where_clause
", $params);

// Get comments
$comments = $db->fetchAll("
    SELECT c.*, r.title as review_title, r.slug as review_slug,
           (SELECT COUNT(*) FROM comments replies WHERE replies.parent_id = c.id) as reply_count
    FROM comments c 
    LEFT JOIN reviews r ON c.review_id = r.id 
    $where_clause
    ORDER BY c.created_at DESC 
    LIMIT $per_page OFFSET $offset
", $params);

// Get status counts
$status_counts = [
    'all' => $db->count("SELECT COUNT(*) FROM comments"),
    'pending' => $db->count("SELECT COUNT(*) FROM comments WHERE status = 'pending'"),
    'approved' => $db->count("SELECT COUNT(*) FROM comments WHERE status = 'approved'"),
    'spam' => $db->count("SELECT COUNT(*) FROM comments WHERE status = 'spam'")
];

$total_pages = ceil($total_comments / $per_page);
?>

include '../includes/header.php';
?>


<!DOCTYPE html>
<html lang="bn" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>মন্তব্য ব্যবস্থাপনা - <?php echo SITE_NAME; ?></title>
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; }
        .status-pending { background-color: #fef3c7; color: #d97706; }
        .status-approved { background-color: #d1fae5; color: #059669; }
        .status-spam { background-color: #fee2e2; color: #dc2626; }
    </style>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">মন্তব্য ব্যবস্থাপনা</h1>
                        <p class="text-gray-600">ব্যবহারকারীর মন্তব্য দেখুন ও ব্যবস্থাপনা করুন</p>
                    </div>
                    <a href="../" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>ড্যাশবোর্ড
                    </a>
                </div>
            </div>
        </header>

        <div class="p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-comments text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">মোট মন্তব্য</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo number_format($status_counts['all']); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">অপেক্ষমাণ</p>
                            <p class="text-2xl font-bold text-yellow-600"><?php echo number_format($status_counts['pending']); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">অনুমোদিত</p>
                            <p class="text-2xl font-bold text-green-600"><?php echo number_format($status_counts['approved']); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-ban text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">স্প্যাম</p>
                            <p class="text-2xl font-bold text-red-600"><?php echo number_format($status_counts['spam']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
                <form method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">স্ট্যাটাস ফিল্টার</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                                <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>সব মন্তব্য</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>অপেক্ষমাণ (<?php echo $status_counts['pending']; ?>)</option>
                                <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>অনুমোদিত (<?php echo $status_counts['approved']; ?>)</option>
                                <option value="spam" <?php echo $status_filter === 'spam' ? 'selected' : ''; ?>>স্প্যাম (<?php echo $status_counts['spam']; ?>)</option>
                            </select>
                        </div>
                        
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">অনুসন্ধান</label>
                            <input type="text" 
                                   name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   placeholder="নাম, ইমেইল বা মন্তব্য অনুসন্ধান..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                        </div>
                        
                        <!-- Submit -->
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-base">
                                <i class="fas fa-search mr-2"></i>ফিল্টার
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Bulk Actions -->
            <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
                <form id="bulk-form">
                    <div class="flex items-center space-x-4">
                        <select id="bulk-action" class="px-3 py-2 border border-gray-300 rounded-lg text-base">
                            <option value="">বাল্ক অ্যাকশন নির্বাচন করুন</option>
                            <option value="approve">অনুমোদন করুন</option>
                            <option value="pending">অপেক্ষমাণ করুন</option>
                            <option value="spam">স্প্যাম মার্ক করুন</option>
                            <option value="delete">মুছে ফেলুন</option>
                        </select>
                        <button type="button" onclick="performBulkAction()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-base">
                            প্রয়োগ করুন
                        </button>
                        <span id="selected-count" class="text-sm text-gray-600"></span>
                    </div>
                </form>
            </div>

            <!-- Comments Table -->
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <?php if (!empty($comments)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" id="select-all" class="rounded">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">মন্তব্যকারী</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">মন্তব্য</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">রিভিউ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">স্ট্যাটাস</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">তারিখ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">অ্যাকশন</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($comments as $comment): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" class="comment-checkbox rounded" value="<?php echo $comment['id']; ?>">
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($comment['name']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($comment['email']); ?>
                                                </div>
                                                <?php if ($comment['reply_count'] > 0): ?>
                                                    <div class="text-xs text-blue-600 mt-1">
                                                        <i class="fas fa-reply mr-1"></i><?php echo $comment['reply_count']; ?>টি উত্তর
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs">
                                                <?php echo htmlspecialchars(truncateText($comment['comment'], 100)); ?>
                                            </div>
                                            <?php if ($comment['parent_id']): ?>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    <i class="fas fa-reply mr-1"></i>উত্তর
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <div class="text-sm">
                                                <a href="/review/<?php echo $comment['review_slug']; ?>" 
                                                   target="_blank"
                                                   class="text-blue-600 hover:text-blue-800">
                                                    <?php echo htmlspecialchars(truncateText($comment['review_title'], 50)); ?>
                                                </a>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium status-<?php echo $comment['status']; ?>">
                                                <?php 
                                                switch($comment['status']) {
                                                    case 'approved': echo 'অনুমোদিত'; break;
                                                    case 'pending': echo 'অপেক্ষমাণ'; break;
                                                    case 'spam': echo 'স্প্যাম'; break;
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <?php echo date('d M Y, H:i', strtotime($comment['created_at'])); ?>
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-2">
                                                <!-- Status Change Dropdown -->
                                                <select onchange="updateStatus(<?php echo $comment['id']; ?>, this.value)" 
                                                        class="text-xs border border-gray-300 rounded px-2 py-1">
                                                    <option value="">স্ট্যাটাস পরিবর্তন</option>
                                                    <option value="approved" <?php echo $comment['status'] === 'approved' ? 'selected' : ''; ?>>অনুমোদন</option>
                                                    <option value="pending" <?php echo $comment['status'] === 'pending' ? 'selected' : ''; ?>>অপেক্ষমাণ</option>
                                                    <option value="spam" <?php echo $comment['status'] === 'spam' ? 'selected' : ''; ?>>স্প্যাম</option>
                                                </select>
                                                
                                                <!-- View Comment Modal -->
                                                <button onclick="viewComment(<?php echo $comment['id']; ?>)" 
                                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <!-- Delete -->
                                                <button onclick="deleteComment(<?php echo $comment['id']; ?>)" 
                                                        class="text-red-600 hover:text-red-800 text-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    <span><?php echo ($offset + 1); ?> থেকে <?php echo min($offset + $per_page, $total_comments); ?></span>
                                    <span class="font-medium"><?php echo number_format($total_comments); ?></span> এর মধ্যে
                                </div>
                                
                                <div class="flex space-x-2">
                                    <?php if ($page > 1): ?>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                                           class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-500 hover:bg-gray-50">
                                            পূর্ববর্তী
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                           class="px-3 py-2 border rounded-md text-sm <?php echo $i === $page ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-500 border-gray-300 hover:bg-gray-50'; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                                           class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-500 hover:bg-gray-50">
                                            পরবর্তী
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fas fa-comments text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">কোনো মন্তব্য পাওয়া যায়নি</h3>
                        <p class="text-gray-500">নির্বাচিত ফিল্টারে কোনো মন্তব্য নেই।</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Comment View Modal -->
    <div id="comment-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">মন্তব্যের বিস্তারিত</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="modal-content"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Select all functionality
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.comment-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Individual checkbox change
        document.querySelectorAll('.comment-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        function updateSelectedCount() {
            const selected = document.querySelectorAll('.comment-checkbox:checked').length;
            document.getElementById('selected-count').textContent = 
                selected > 0 ? `${selected}টি নির্বাচিত` : '';
        }

        // Update comment status
        function updateStatus(commentId, status) {
            if (!status) return;
            
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_status&comment_id=${commentId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('আপডেট করতে সমস্যা হয়েছে।', 'error');
            });
        }

        // Delete comment
        function deleteComment(commentId) {
            if (!confirm('আপনি কি নিশ্চিত যে এই মন্তব্যটি মুছে ফেলতে চান?')) {
                return;
            }

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete_comment&comment_id=${commentId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('মুছতে সমস্যা হয়েছে।', 'error');
            });
        }

        // Bulk actions
        function performBulkAction() {
            const action = document.getElementById('bulk-action').value;
            const selectedIds = Array.from(document.querySelectorAll('.comment-checkbox:checked'))
                .map(cb => cb.value);

            if (!action || selectedIds.length === 0) {
                showAlert('অ্যাকশন এবং মন্তব্য নির্বাচন করুন।', 'warning');
                return;
            }

            if (action === 'delete' && !confirm(`আপনি কি নিশ্চিত যে ${selectedIds.length}টি মন্তব্য মুছে ফেলতে চান?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'bulk_action');
            formData.append('bulk_action', action);
            selectedIds.forEach(id => formData.append('comment_ids[]', id));

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('অপারেশন সম্পন্ন করতে সমস্যা হয়েছে।', 'error');
            });
        }

        // View comment modal
        function viewComment(commentId) {
            // This would fetch and display full comment details
            // For now, showing a placeholder
            document.getElementById('modal-content').innerHTML = `
                <div class="space-y-4">
                    <p class="text-gray-600">মন্তব্য ID: ${commentId}</p>
                    <p>এখানে মন্তব্যের পূর্ণ বিস্তারিত দেখানো হবে...</p>
                </div>
            `;
            document.getElementById('comment-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('comment-modal').classList.add('hidden');
        }

        // Alert function
        function showAlert(message, type = 'info') {
            const bgColor = type === 'success' ? 'bg-green-500' : 
                           type === 'error' ? 'bg-red-500' : 
                           type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
            
            const alert = document.createElement('div');
            alert.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
            alert.textContent = message;
            
            document.body.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 3000);
        }

        // Initialize
        updateSelectedCount();
    </script>
</body>
</html>