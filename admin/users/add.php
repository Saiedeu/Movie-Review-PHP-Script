<?php
/**
 * Add User
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../../config/config.php';

// Require authentication
requireAuth();

// Get database instance
$db = Database::getInstance();

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
        } elseif (empty($password)) {
            $error = 'পাসওয়ার্ড আবশ্যক।';
        } elseif (strlen($password) < 6) {
            $error = 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।';
        } elseif ($password !== $confirm_password) {
            $error = 'পাসওয়ার্ড মিলছে না।';
        } else {
            // Check if username already exists
            $existing_username = $db->fetchOne("SELECT id FROM users WHERE username = ?", [$username]);
            if ($existing_username) {
                $error = 'এই ইউজারনেম ইতিমধ্যে ব্যবহৃত হয়েছে।';
            } else {
                // Check if email already exists
                $existing_email = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
                if ($existing_email) {
                    $error = 'এই ইমেইল ইতিমধ্যে ব্যবহৃত হয়েছে।';
                } else {
                    // Hash password
                    $hashed_password = hashPassword($password);
                    
                    // Insert user
                    $result = $db->query("
                        INSERT INTO users (username, email, password, role, status) 
                        VALUES (?, ?, ?, ?, ?)
                    ", [$username, $email, $hashed_password, $role, $status]);
                    
                    if ($result) {
                        $success = 'ব্যবহারকারী সফলভাবে যোগ করা হয়েছে।';
                        // Clear form
                        $_POST = [];
                    } else {
                        $error = 'ব্যবহারকারী যোগ করতে সমস্যা হয়েছে।';
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
                    <h1 class="text-2xl font-bold text-gray-900">নতুন ব্যবহারকারী যোগ করুন</h1>
                    <p class="text-gray-600">একটি নতুন ব্যবহারকারী অ্যাকাউন্ট তৈরি করুন</p>
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
                <a href="index.php" class="ml-4 underline">ব্যবহারকারী তালিকায় ফিরে যান</a>
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
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="ইউজারনেম (ইংরেজি অক্ষর ও সংখ্যা)"
                               required>
                        <p class="text-sm text-gray-500 mt-1">শুধু ইংরেজি অক্ষর, সংখ্যা এবং আন্ডারস্কোর ব্যবহার করুন</p>
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            ইমেইল ঠিকানা <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="user@example.com"
                               required>
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            পাসওয়ার্ড <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="কমপক্ষে ৬ অক্ষর"
                               required>
                        <p class="text-sm text-gray-500 mt-1">কমপক্ষে ৬ অক্ষরের পাসওয়ার্ড দিন</p>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            পাসওয়ার্ড নিশ্চিত করুন <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="পাসওয়ার্ড আবার লিখুন"
                               required>
                    </div>
                    
                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            ভূমিকা
                        </label>
                        <select id="role" 
                                name="role" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="editor" <?php echo ($_POST['role'] ?? 'editor') === 'editor' ? 'selected' : ''; ?>>এডিটর</option>
                            <option value="admin" <?php echo ($_POST['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>অ্যাডমিন</option>
                        </select>
                        <p class="text-sm text-gray-500 mt-1">
                            এডিটর: রিভিউ যোগ/সম্পাদনা করতে পারে<br>
                            অ্যাডমিন: সব অনুমতি রয়েছে
                        </p>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            স্ট্যাটাস
                        </label>
                        <select id="status" 
                                name="status" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="active" <?php echo ($_POST['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>সক্রিয়</option>
                            <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>নিষ্ক্রিয়</option>
                        </select>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4 pt-4">
                        <a href="index.php" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            বাতিল
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            ব্যবহারকারী যোগ করুন
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
    
    if (password !== confirmPassword) {
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