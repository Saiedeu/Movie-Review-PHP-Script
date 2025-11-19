<?php
/**
 * Site Settings
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
        $settings = [
            'site_name' => sanitize($_POST['site_name'] ?? ''),
            'site_description' => sanitize($_POST['site_description'] ?? ''),
            'theme_color' => sanitize($_POST['theme_color'] ?? '#3B82F6'),
            'footer_text' => sanitize($_POST['footer_text'] ?? ''),
        ];
        
        // Handle logo upload
        if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadFile($_FILES['site_logo'], 'logo');
            if ($upload_result['success']) {
                // Delete old logo
                $old_logo = getSiteSetting('site_logo');
                if ($old_logo) {
                    deleteFile('logo', $old_logo);
                }
                $settings['site_logo'] = $upload_result['filename'];
            } else {
                $error = $upload_result['message'];
            }
        }
        
        // Handle favicon upload
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadFile($_FILES['favicon'], 'seo');
            if ($upload_result['success']) {
                // Delete old favicon
                $old_favicon = getSiteSetting('favicon');
                if ($old_favicon) {
                    deleteFile('seo', $old_favicon);
                }
                $settings['favicon'] = $upload_result['filename'];
            } else {
                $error = $upload_result['message'];
            }
        }
        
        if (empty($error)) {
            // Update settings
            foreach ($settings as $key => $value) {
                $existing = $db->fetchOne("SELECT id FROM site_settings WHERE setting_key = ?", [$key]);
                if ($existing) {
                    $db->query("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
                } else {
                    $db->query("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
                }
            }
            
            $success = 'সাইট সেটিংস সফলভাবে আপডেট করা হয়েছে।';
        }
    }
}

// Get current settings
$current_settings = [];
$settings_result = $db->fetchAll("SELECT setting_key, setting_value FROM site_settings");
foreach ($settings_result as $setting) {
    $current_settings[$setting['setting_key']] = $setting['setting_value'];
}

// Include admin header
include '../includes/header.php';
?>

<div class="flex-1 bg-gray-100">
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-900">সাইট সেটিংস</h1>
            <p class="text-gray-600">ওয়েবসাইটের মূল সেটিংস পরিবর্তন করুন</p>
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
        
        <!-- Settings Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">মূল তথ্য</h3>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Site Name -->
                            <div>
                                <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    সাইটের নাম <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="site_name" 
                                       name="site_name" 
                                       value="<?php echo htmlspecialchars($current_settings['site_name'] ?? SITE_NAME); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                            </div>
                            
                            <!-- Theme Color -->
                            <div>
                                <label for="theme_color" class="block text-sm font-medium text-gray-700 mb-2">
                                    থিম রঙ
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" 
                                           id="theme_color" 
                                           name="theme_color" 
                                           value="<?php echo htmlspecialchars($current_settings['theme_color'] ?? '#3B82F6'); ?>"
                                           class="w-12 h-12 border border-gray-300 rounded-lg">
                                    <input type="text" 
                                           id="theme_color_text" 
                                           value="<?php echo htmlspecialchars($current_settings['theme_color'] ?? '#3B82F6'); ?>"
                                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           onchange="document.getElementById('theme_color').value = this.value">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Site Description -->
                        <div class="mt-6">
                            <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">
                                সাইটের বিবরণ
                            </label>
                            <textarea id="site_description" 
                                      name="site_description" 
                                      rows="3" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="সাইট সম্পর্কে সংক্ষিপ্ত বিবরণ"><?php echo htmlspecialchars($current_settings['site_description'] ?? SITE_DESCRIPTION); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Logo & Images -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">লোগো ও ছবি</h3>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Site Logo -->
                            <div>
                                <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-2">
                                    সাইট লোগো
                                </label>
                                
                                <?php if (!empty($current_settings['site_logo'])): ?>
                                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                        <img src="<?php echo UPLOADS_URL; ?>/logo/<?php echo $current_settings['site_logo']; ?>" 
                                             alt="Current Logo" 
                                             class="h-16 w-auto">
                                        <p class="text-sm text-gray-600 mt-2">বর্তমান লোগো</p>
                                    </div>
                                <?php endif; ?>
                                
                                <input type="file" 
                                       id="site_logo" 
                                       name="site_logo" 
                                       accept="image/*" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-sm text-gray-500 mt-1">PNG, JPG বা WEBP ফর্ম্যাট, সর্বোচ্চ ৫MB</p>
                            </div>
                            
                            <!-- Favicon -->
                            <div>
                                <label for="favicon" class="block text-sm font-medium text-gray-700 mb-2">
                                    ফেভিকন
                                </label>
                                
                                <?php if (!empty($current_settings['favicon'])): ?>
                                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                        <img src="<?php echo UPLOADS_URL; ?>/seo/<?php echo $current_settings['favicon']; ?>" 
                                             alt="Current Favicon" 
                                             class="h-8 w-8">
                                        <p class="text-sm text-gray-600 mt-2">বর্তমান ফেভিকন</p>
                                    </div>
                                <?php endif; ?>
                                
                                <input type="file" 
                                       id="favicon" 
                                       name="favicon" 
                                       accept="image/*" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-sm text-gray-500 mt-1">ICO, PNG বা JPG ফর্ম্যাট, 32x32 পিক্সেল</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">ফুটার</h3>
                        
                        <div>
                            <label for="footer_text" class="block text-sm font-medium text-gray-700 mb-2">
                                ফুটার টেক্সট
                            </label>
                            <textarea id="footer_text" 
                                      name="footer_text" 
                                      rows="2" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="কপিরাইট টেক্সট"><?php echo htmlspecialchars($current_settings['footer_text'] ?? '© ২০২৫ MSR - Movie & Series Review. সকল অধিকার সংরক্ষিত।'); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-end pt-6 border-t border-gray-200">
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            সেটিংস সংরক্ষণ করুন
                        </button>
                    </div>
                    
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Color picker sync
document.getElementById('theme_color').addEventListener('change', function() {
    document.getElementById('theme_color_text').value = this.value;
});

document.getElementById('theme_color_text').addEventListener('input', function() {
    if (/^#[0-9A-F]{6}$/i.test(this.value)) {
        document.getElementById('theme_color').value = this.value;
    }
});
</script>

<?php include '../includes/footer.php'; ?>