<?php
/**
 * Delete Review - Admin
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../../config/config.php';

// Require authentication
requireAuth();

// Get database instance
$db = Database::getInstance();

// Get review ID
$review_id = intval($_GET['id'] ?? 0);

// Get review data
$review = $db->fetchOne("SELECT * FROM reviews WHERE id = ?", [$review_id]);

if (!$review) {
    $_SESSION['error_message'] = 'রিভিউ পাওয়া যায়নি।';
    header('Location: index.php');
    exit;
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        // Delete poster image if exists
        if ($review['poster_image']) {
            deleteFile('reviews', $review['poster_image']);
        }
        
        // Delete review categories
        $db->query("DELETE FROM review_categories WHERE review_id = ?", [$review_id]);
        
        // Delete review
        $deleted = $db->query("DELETE FROM reviews WHERE id = ?", [$review_id]);
        
        if ($deleted) {
            $_SESSION['success_message'] = 'রিভিউ সফলভাবে মুছে ফেলা হয়েছে।';
        } else {
            $_SESSION['error_message'] = 'রিভিউ মুছতে সমস্যা হয়েছে।';
        }
    } else {
        $_SESSION['error_message'] = 'নিরাপত্তা টোকেন যাচাই করা যায়নি।';
    }
    
    header('Location: index.php');
    exit;
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">রিভিউ মুছে ফেলুন</h1>
                    <p class="text-gray-600">নিশ্চিত করুন যে আপনি এই রিভিউটি মুছে ফেলতে চান</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <div class="max-w-2xl mx-auto">
            <!-- Warning Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">সতর্কতা!</h3>
                        <p class="text-gray-600">এই কাজটি পূর্বাবস্থায় ফেরানো যাবে না।</p>
                    </div>
                </div>
                
                <!-- Review Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-16 h-20 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                            <?php if ($review['poster_image']): ?>
                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                     class="w-full h-full object-cover">
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">
                                <?php echo htmlspecialchars($review['title']); ?>
                            </h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <p><strong>লেখক:</strong> <?php echo htmlspecialchars($review['reviewer_name']); ?></p>
                                <p><strong>ধরণ:</strong> <?php echo $review['type'] === 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?></p>
                                <p><strong>বছর:</strong> <?php echo $review['year']; ?></p>
                                <p><strong>রেটিং:</strong> <?php echo $review['rating']; ?>/5</p>
                                <p><strong>ভিউ:</strong> <?php echo number_format($review['view_count']); ?></p>
                                <p><strong>তৈরি:</strong> <?php echo date('d M Y', strtotime($review['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Warning List -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-3">নিম্নলিখিত তথ্য স্থায়ীভাবে মুছে যাবে:</h4>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        <li>রিভিউর সমস্ত কন্টেন্ট ও তথ্য</li>
                        <li>পোস্টার ছবি (যদি থাকে)</li>
                        <li>ক্যাটেগরি সংযোগ</li>
                        <li>ভিউ কাউন্ট ও পরিসংখ্যান</li>
                        <li>সকল মেটাডেটা ও SEO তথ্য</li>
                    </ul>
                </div>
                
                <!-- Confirmation Form -->
                <form method="POST" class="space-y-4">
                    <div class="flex items-center space-x-3 p-4 bg-red-50 rounded-lg">
                        <input type="checkbox" 
                               id="confirm" 
                               required
                               class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <label for="confirm" class="text-sm text-gray-700">
                            আমি নিশ্চিত যে এই রিভিউটি স্থায়ীভাবে মুছে ফেলতে চাই
                        </label>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a href="index.php" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium transition-colors">
                            বাতিল করুন
                        </a>
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            মুছে ফেলুন
                        </button>
                    </div>
                    
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>