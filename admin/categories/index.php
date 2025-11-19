<?php
/**
 * Categories Management
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
$message = '';
$message_type = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    
    if ($action === 'toggle_status') {
        $category = $db->fetchOne("SELECT status FROM categories WHERE id = ?", [$id]);
        if ($category) {
            $new_status = $category['status'] === 'active' ? 'inactive' : 'active';
            $db->query("UPDATE categories SET status = ? WHERE id = ?", [$new_status, $id]);
            $message = 'ক্যাটেগরি স্ট্যাটাস আপডেট করা হয়েছে।';
            $message_type = 'success';
        }
    }
}

// Get all categories
$categories = $db->fetchAll("
    SELECT c.*, 
           COUNT(rc.review_id) as review_count 
    FROM categories c 
    LEFT JOIN review_categories rc ON c.id = rc.category_id 
    LEFT JOIN reviews r ON rc.review_id = r.id AND r.status = 'published'
    GROUP BY c.id 
    ORDER BY c.sort_order ASC, c.name ASC
");

// Include admin header
include '../includes/header.php';
?>

<div class="flex-1 bg-gray-100">
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">ক্যাটেগরি ব্যবস্থাপনা</h1>
                    <p class="text-gray-600">সব ক্যাটেগরি দেখুন এবং ব্যবস্থাপনা করুন</p>
                </div>
                <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    নতুন ক্যাটেগরি
                </a>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <!-- Messages -->
        <?php if ($message): ?>
            <div class="mb-6 bg-<?php echo $message_type === 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $message_type === 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $message_type === 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded-lg">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Categories List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">আইকন</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">নাম</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">বাংলা নাম</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">স্লাগ</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">রিভিউ</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">ক্রম</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">স্ট্যাটাস</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-4 px-4">
                                            <?php if ($category['icon']): ?>
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: <?php echo $category['color']; ?>; color: white;">
                                                    <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
                                                </div>
                                            <?php else: ?>
                                                <div class="w-8 h-8 bg-gray-300 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-folder"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-medium text-gray-900">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="text-gray-700">
                                                <?php echo htmlspecialchars($category['name_bn'] ?: '-'); ?>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <code class="bg-gray-100 px-2 py-1 rounded text-sm">
                                                <?php echo htmlspecialchars($category['slug']); ?>
                                            </code>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm">
                                                <?php echo number_format($category['review_count']); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="text-gray-600">
                                                <?php echo $category['sort_order']; ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <a href="?action=toggle_status&id=<?php echo $category['id']; ?>" 
                                               class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                               <?php echo $category['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>"
                                               onclick="return confirm('স্ট্যাটাস পরিবর্তন করতে চান?')">
                                                <?php echo $category['status'] === 'active' ? 'সক্রিয়' : 'নিষ্ক্রিয়'; ?>
                                            </a>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center space-x-2">
                                                <a href="edit.php?id=<?php echo $category['id']; ?>" 
                                                   class="text-blue-600 hover:text-blue-700 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <a href="delete.php?id=<?php echo $category['id']; ?>" 
                                                   class="text-red-600 hover:text-red-700 transition-colors"
                                                   onclick="return confirm('এই ক্যাটেগরি মুছে ফেলতে চান? এটি পূর্বাবস্থায় ফেরানো যাবে না।')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-gray-500">
                                        কোনো ক্যাটেগরি পাওয়া যায়নি
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>