<?php
/**
 * Delete Category
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../../config/config.php';

// Require authentication
requireAuth();

// Get database instance
$db = Database::getInstance();

// Get category ID
$id = intval($_GET['id'] ?? 0);

// Get category data
$category = $db->fetchOne("SELECT * FROM categories WHERE id = ?", [$id]);

if (!$category) {
    header('Location: index.php?error=category_not_found');
    exit;
}

// Check if category has reviews
$review_count = $db->count("SELECT COUNT(*) FROM review_categories WHERE category_id = ?", [$id]);

$error = '';
$success = '';

// Handle confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'নিরাপত্তা টোকেন যাচাই করা যায়নি।';
    } elseif ($review_count > 0) {
        $error = 'এই ক্যাটেগরিতে রিভিউ রয়েছে। প্রথমে সব রিভিউ অন্য ক্যাটেগরিতে সরান।';
    } else {
        // Delete category
        $result = $db->query("DELETE FROM categories WHERE id = ?", [$id]);
        
        if ($result) {
            header('Location: index.php?success=category_deleted');
            exit;
        } else {
            $error = 'ক্যাটেগরি মুছতে সমস্যা হয়েছে।';
        }
    }
}

// Include admin header
include '../includes/header.php';
?>

<div class="flex-1 bg-gray-100">
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="index.php" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">ক্যাটেগরি মুছুন</h1>
                    <p class="text-gray-600">ক্যাটেগরি মুছে ফেলার নিশ্চিতকরণ</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <!-- Messages -->
        <?php if ($error): ?>
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <!-- Confirmation Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            আপনি কি নিশ্চিত যে এই ক্যাটেগরি মুছে ফেলতে চান?
                        </h3>
                        
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex items-center space-x-4">
                                <?php if ($category['icon']): ?>
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: <?php echo $category['color']; ?>; color: white;">
                                        <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h4 class="font-medium text-gray-900">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                        <?php if ($category['name_bn']): ?>
                                            (<?php echo htmlspecialchars($category['name_bn']); ?>)
                                        <?php endif; ?>
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        স্লাগ: <?php echo htmlspecialchars($category['slug']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($review_count > 0): ?>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <p class="text-yellow-800">
                                        <strong>সতর্কতা:</strong> এই ক্যাটেগরিতে <?php echo number_format($review_count); ?>টি রিভিউ রয়েছে। 
                                        ক্যাটেগরি মুছার আগে সব রিভিউ অন্য ক্যাটেগরিতে সরিয়ে নিন।
                                    </p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                                <p class="text-red-800">
                                    <strong>সতর্কতা:</strong> এই অ্যাকশনটি পূর্বাবস্থায় ফেরানো যাবে না। 
                                    ক্যাটেগরি স্থায়ীভাবে মুছে যাবে।
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex justify-end space-x-4">
                            <a href="index.php" 
                               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                বাতিল
                            </a>
                            
                            <?php if ($review_count === 0): ?>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <button type="submit" 
                                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                                            onclick="return confirm('আপনি কি সত্যিই এই ক্যাটেগরি মুছে ফেলতে চান?')">
                                        হ্যাঁ, মুছে ফেলুন
                                    </button>
                                </form>
                            <?php else: ?>
                                <button disabled 
                                        class="px-6 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed">
                                    মুছা যাবে না
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>