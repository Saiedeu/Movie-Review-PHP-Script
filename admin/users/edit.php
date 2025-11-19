<?php
/**
 * Edit User
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

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'নিরাপত্তা টোকেন যাচাই করা যায়নি।';
    } else {
        // Get form data
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $role = sanitize($_POST['role'] ?? 'editor');
        $status = sanitize($_POST['status'] ?? 'active');
        
        // Validate required fields
        if (empty($username)) {
            $error = 'ইউজারনেম আবশ্যক।';
        } elseif (empty($email)) {
            $error = 'ইমেইল আবশ্যক।';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'সঠিক ইমেইল ঠিকানা দিন।';
        } elseif (!empty($password) && strlen($password) < 6) {
            $error = 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।';
        } elseif (!empty($password) && $password !== $confirm_password) {
            $error = 'পাসওয়ার্ড মিলছে না।';
        } else {
            // Check if username already exists (excluding current user)
            $existing_username = $db->fetchOne("SELECT id FROM users WHERE username = ? AND id != ?", [$username, $id]);
            if ($existing_username) {
                $error = 'এই ইউজারনেম ইতিমধ্যে ব্যবহৃত হয়েছে।';
            } else {
                // Check if email already exists (excluding current user)
                $existing_email = $db->fetchOne("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $id]);
                if ($existing_email) {
                    $error = 'এই ইমেইল ইতিমধ্যে ব্যবহৃত হয়েছে।';
                } else {
                    // Prepare update query
                    if (!empty($password)) {
                        // Update with password
                        $hashed_password = hashPassword($password);
                        $result = $db->query("
                            UPDATE users 
                            SET username = ?, email = ?, password = ?, role = ?, status = ?, updated_at = NOW()
                            WHERE id = ?
                        ", [$username, $email, $hashed_password, $role, $status, $id]);
                    } else {
                        // Update without password
                        $result = $db->query("
                            UPDATE users 
                            SET username = ?, email = ?, role = ?, status = ?, updated_at = NOW()
                            WHERE id = ?
                        ", [$username, $email, $role, $status, $id]);
                    }
                    
                    if ($result) {
                        $success = 'ব্যবহারকারী সফলভাবে আপডেট করা হয়েছে।';
                        // Refresh user data
                        $user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
                    } else {
                        $error = 'ব্যবহারকারী আপডেট করতে সমস্যা হয়েছে।';
                    }
                }
            }
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
                    <h1 class="text-2xl font-bold text-gray-900">ব্যবহারকারী সম্পাদনা</h1>
                    <p class="text-gray-600">"<?php echo htmlspecialchars($user['username']); ?>" সম্পাদনা করুন</p>
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
        
        <?php if ($success): ?>
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 max-w-2xl">
            <div class="p-6">
                <form method="POST" class="space-y-6">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            ইউজারনেম <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               value="<?php echo htmlspecialchars($user['username']); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="ইউজারনেম (ইংরেজি অক্ষর ও সংখ্যা)"
                               required>
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            ইমেইল ঠিকানা <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="user@example.com"
                               required>
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            নতুন পাসওয়ার্ড
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="নতুন পাসওয়ার্ড (খালি রাখুন যদি পরিবর্তন না করতে চান)">
                        <p class="text-sm text-gray-500 mt-1">পাসওয়ার্ড পরিবর্তন করতে চাইলে এখানে নতুন পাসওয়ার্ড দিন</p>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            পাসওয়ার্ড নিশ্চিত করুন
                        </label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="পাসওয়ার্ড আবার লিখুন">
                    </div>
                    
                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            ভূমিকা
                        </label>
                        <select id="role" 
                                name="role" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                <?php echo $user['id'] === getCurrentAdmin()['id'] ? 'disabled' : ''; ?>>
                            <option value="editor" <?php echo $user['role'] === 'editor' ? 'selected' : ''; ?>>এডিটর</option>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>অ্যাডমিন</option>
                        </select>
                        <?php if ($user['id'] === getCurrentAdmin()['id']): ?>
                            <p class="text-sm text-yellow-600 mt-1">আপনি নিজের ভূমিকা পরিবর্তন করতে পারবেন না।</p>
                            <input type="hidden" name="role" value="<?php echo $user['role']; ?>">
                        <?php endif; ?>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            স্ট্যাটাস
                        </label>
                        <select id="status" 
                                name="status" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                <?php echo $user['id'] === getCurrentAdmin()['id'] ? 'disabled' : ''; ?>>
                            <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>সক্রিয়</option>
                            <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>নিষ্ক্রিয়</option>
                        </select>
                        <?php if ($user['id'] === getCurrentAdmin()['id']): ?>
                            <p class="text-sm text-yellow-600 mt-1">আপনি নিজের স্ট্যাটাস পরিবর্তন করতে পারবেন না।</p>
                            <input type="hidden" name="status" value="<?php echo $user['status']; ?>">
                        <?php endif; ?>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4 pt-4">
                        <a href="index.php" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            বাতিল
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            পরিবর্তন সংরক্ষণ করুন
                        </button>
                    </div>
                    
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password && password !== confirmPassword) {
        this.setCustomValidity('পাসওয়ার্ড মিলছে না');
    } else {
        this.setCustomValidity('');
    }
});

// Username validation
document.getElementById('username').addEventListener('input', function() {
    const username = this.value;
    const pattern = /^[a-zA-Z0-9_]+$/;
    
    if (username && !pattern.test(username)) {
        this.setCustomValidity('শুধু ইংরেজি অক্ষর, সংখ্যা এবং আন্ডারস্কোর ব্যবহার করুন');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php include '../includes/footer.php'; ?>