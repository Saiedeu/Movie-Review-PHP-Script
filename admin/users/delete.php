<?php
/**
 * Delete User
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../../config/config.php';

// Require authentication
requireAuth();

// Get database instance
$db = Database::getInstance();

// Get user ID
$id = intval($_GET['id'] ?? 0);

// Get user data
$user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);

if (!$user) {
    header('Location: index.php?error=user_not_found');
    exit;
}

// Prevent admin from deleting themselves
if ($user['id'] === getCurrentAdmin()['id']) {
    header('Location: index.php?error=cannot_delete_self');
    exit;
}

// Check if user has reviews
$review_count = $db->count("SELECT COUNT(*) FROM reviews WHERE reviewer_email = ?", [$user['email']]);

$error = '';
$success = '';

// Handle confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'নিরাপত্তা টোকেন যাচাই করা যায়নি।';
    } else {
        // Delete user
        $result = $db->query("DELETE FROM users WHERE id = ?", [$id]);
        
        if ($result) {
            header('Location: index.php?success=user_deleted');
            exit;
        } else {
            $error = 'ব্যবহারকারী মুছতে সমস্যা হয়েছে।';
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
                    <h1 class="text-2xl font-bold text-gray-900">ব্যবহারকারী মুছুন</h1>
                    <p class="text-gray-600">ব্যবহারকারী মুছে ফেলার নিশ্চিতকরণ</p>
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
                            আপনি কি নিশ্চিত যে এই ব্যবহারকারী মুছে ফেলতে চান?
                        </h3>
                        
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-medium">
                                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        ভূমিকা: <?php echo $user['role'] === 'admin' ? 'অ্যাডমিন' : 'এডিটর'; ?> | 
                                        যোগদান: <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
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
                                        <strong>তথ্য:</strong> এই ব্যবহারকারীর নামে <?php echo number_format($review_count); ?>টি রিভিউ রয়েছে। 
                                        ব্যবহারকারী মুছে ফেললেও রিভিউগুলো থাকবে।
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <p class="text-red-800">
                                <strong>সতর্কতা:</strong> এই অ্যাকশনটি পূর্বাবস্থায় ফেরানো যাবে না। 
                                ব্যবহারকারী অ্যাকাউন্ট স্থায়ীভাবে মুছে যাবে।
                            </p>
                        </div>
                        
                        <div class="flex justify-end space-x-4">
                            <a href="index.php" 
                               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                বাতিল
                            </a>
                            
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <button type="submit" 
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                                        onclick="return confirm('আপনি কি সত্যিই এই ব্যবহারকারী মুছে ফেলতে চান?')">
                                    হ্যাঁ, মুছে ফেলুন
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>