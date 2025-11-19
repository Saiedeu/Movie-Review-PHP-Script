<?php
/**
 * Enhanced Site Settings with Design & Integration Options
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
            'primary_color' => sanitize($_POST['primary_color'] ?? '#3B82F6'),
            'secondary_color' => sanitize($_POST['secondary_color'] ?? '#10B981'),
            'body_color' => sanitize($_POST['body_color'] ?? '#F9FAFB'),
            'text_color' => sanitize($_POST['text_color'] ?? '#1F2937'),
            'header_color' => sanitize($_POST['header_color'] ?? '#FFFFFF'),
            'footer_color' => sanitize($_POST['footer_color'] ?? '#1F2937'),
            'footer_text' => sanitize($_POST['footer_text'] ?? ''),
            'custom_css' => $_POST['custom_css'] ?? '', // Don't sanitize CSS
            'recaptcha_site_key' => sanitize($_POST['recaptcha_site_key'] ?? ''),
            'recaptcha_secret_key' => sanitize($_POST['recaptcha_secret_key'] ?? ''),
            'cloudflare_zone_id' => sanitize($_POST['cloudflare_zone_id'] ?? ''),
            'cloudflare_api_token' => sanitize($_POST['cloudflare_api_token'] ?? ''),
            'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0,
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

<!-- Page Header -->
<div class="bg-white shadow-sm border-b border-gray-200">
    <div class="px-6 py-4">
        <h1 class="text-2xl font-bold text-gray-900">সাইট সেটিংস</h1>
        <p class="text-gray-600">ওয়েবসাইটের মূল সেটিংস এবং ডিজাইন কাস্টমাইজেশন</p>
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
    
    <!-- Live Preview Panel -->
    <div class="mb-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">লাইভ প্রিভিউ</h3>
        <div id="live-preview" class="border rounded-lg p-4" style="background-color: <?php echo $current_settings['body_color'] ?? '#F9FAFB'; ?>; color: <?php echo $current_settings['text_color'] ?? '#1F2937'; ?>;">
            <div id="preview-header" class="p-4 rounded-lg mb-4" style="background-color: <?php echo $current_settings['header_color'] ?? '#FFFFFF'; ?>;">
                <div class="flex items-center justify-between">
                    <h2 style="color: <?php echo $current_settings['text_color'] ?? '#1F2937'; ?>;">সাইটের হেডার</h2>
                    <button id="preview-btn-primary" class="px-4 py-2 rounded-lg text-white" style="background-color: <?php echo $current_settings['primary_color'] ?? '#3B82F6'; ?>;">প্রাইমারি বাটন</button>
                </div>
            </div>
            <div class="p-4">
                <h3>সাইটের কন্টেন্ট এরিয়া</h3>
                <p>এটি একটি স্যাম্পল টেক্সট যা দেখাচ্ছে আপনার সাইট কেমন দেখাবে।</p>
                <button id="preview-btn-secondary" class="px-4 py-2 rounded-lg text-white mt-2" style="background-color: <?php echo $current_settings['secondary_color'] ?? '#10B981'; ?>;">সেকেন্ডারি বাটন</button>
            </div>
            <div id="preview-footer" class="p-4 rounded-lg mt-4" style="background-color: <?php echo $current_settings['footer_color'] ?? '#1F2937'; ?>; color: white;">
                <p>সাইটের ফুটার এরিয়া</p>
            </div>
        </div>
    </div>
    
    <!-- Settings Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6">
            <form method="POST" enctype="multipart/form-data" class="space-y-8">
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
                        
                        <!-- Maintenance Mode -->
                        <div class="flex items-center justify-center">
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" 
                                       name="maintenance_mode" 
                                       <?php echo ($current_settings['maintenance_mode'] ?? 0) ? 'checked' : ''; ?>
                                       class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <span class="text-sm font-medium text-gray-700">
                                    মেইনটেনেন্স মোড
                                </span>
                            </label>
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
                
                <!-- Design Colors with Enhanced Color Picker -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">ডিজাইন ও রঙ</h3>
                    
                    <!-- Color Palette Presets -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">প্রিসেট রঙের থিম</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <button type="button" onclick="applyColorTheme('blue')" class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50">
                                <div class="flex space-x-1">
                                    <div class="w-4 h-4 rounded" style="background-color: #3B82F6;"></div>
                                    <div class="w-4 h-4 rounded" style="background-color: #10B981;"></div>
                                </div>
                                <span class="text-sm">নীল থিম</span>
                            </button>
                            <button type="button" onclick="applyColorTheme('green')" class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50">
                                <div class="flex space-x-1">
                                    <div class="w-4 h-4 rounded" style="background-color: #059669;"></div>
                                    <div class="w-4 h-4 rounded" style="background-color: #DC2626;"></div>
                                </div>
                                <span class="text-sm">সবুজ থিম</span>
                            </button>
                            <button type="button" onclick="applyColorTheme('purple')" class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50">
                                <div class="flex space-x-1">
                                    <div class="w-4 h-4 rounded" style="background-color: #7C3AED;"></div>
                                    <div class="w-4 h-4 rounded" style="background-color: #F59E0B;"></div>
                                </div>
                                <span class="text-sm">বেগুনি থিম</span>
                            </button>
                            <button type="button" onclick="applyColorTheme('dark')" class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50">
                                <div class="flex space-x-1">
                                    <div class="w-4 h-4 rounded" style="background-color: #1F2937;"></div>
                                    <div class="w-4 h-4 rounded" style="background-color: #6B7280;"></div>
                                </div>
                                <span class="text-sm">ডার্ক থিম</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Primary Color -->
                        <div class="color-input-group">
                            <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-2">
                                প্রাইমারি কালার
                            </label>
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <input type="color" 
                                           id="primary_color" 
                                           name="primary_color" 
                                           value="<?php echo htmlspecialchars($current_settings['primary_color'] ?? '#3B82F6'); ?>"
                                           class="w-16 h-16 border border-gray-300 rounded-lg cursor-pointer">
                                    <div class="absolute inset-0 rounded-lg border-2 border-white shadow-sm pointer-events-none"></div>
                                </div>
                                <div class="flex-1">
                                    <input type="text" 
                                           id="primary_color_text" 
                                           value="<?php echo htmlspecialchars($current_settings['primary_color'] ?? '#3B82F6'); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono"
                                           placeholder="#3B82F6">
                                    <p class="text-xs text-gray-500 mt-1">বাটন, লিংক</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Secondary Color -->
                        <div class="color-input-group">
                            <label for="secondary_color" class="block text-sm font-medium text-gray-700 mb-2">
                                সেকেন্ডারি কালার
                            </label>
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <input type="color" 
                                           id="secondary_color" 
                                           name="secondary_color" 
                                           value="<?php echo htmlspecialchars($current_settings['secondary_color'] ?? '#10B981'); ?>"
                                           class="w-16 h-16 border border-gray-300 rounded-lg cursor-pointer">
                                    <div class="absolute inset-0 rounded-lg border-2 border-white shadow-sm pointer-events-none"></div>
                                </div>
                                <div class="flex-1">
                                    <input type="text" 
                                           id="secondary_color_text" 
                                           value="<?php echo htmlspecialchars($current_settings['secondary_color'] ?? '#10B981'); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono"
                                           placeholder="#10B981">
                                    <p class="text-xs text-gray-500 mt-1">অ্যাকসেন্ট, হাইলাইট</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Body Color -->
                        <div class="color-input-group">
                            <label for="body_color" class="block text-sm font-medium text-gray-700 mb-2">
                                বডি ব্যাকগ্রাউন্ড
                            </label>
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <input type="color" 
                                           id="body_color" 
                                           name="body_color" 
                                           value="<?php echo htmlspecialchars($current_settings['body_color'] ?? '#F9FAFB'); ?>"
                                           class="w-16 h-16 border border-gray-300 rounded-lg cursor-pointer">
                                    <div class="absolute inset-0 rounded-lg border-2 border-white shadow-sm pointer-events-none"></div>
                                </div>
                                <div class="flex-1">
                                    <input type="text" 
                                           id="body_color_text" 
                                           value="<?php echo htmlspecialchars($current_settings['body_color'] ?? '#F9FAFB'); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono"
                                           placeholder="#F9FAFB">
                                    <p class="text-xs text-gray-500 mt-1">পেজ ব্যাকগ্রাউন্ড</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Text Color -->
                        <div class="color-input-group">
                            <label for="text_color" class="block text-sm font-medium text-gray-700 mb-2">
                                টেক্সট কালার
                            </label>
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <input type="color" 
                                           id="text_color" 
                                           name="text_color" 
                                           value="<?php echo htmlspecialchars($current_settings['text_color'] ?? '#1F2937'); ?>"
                                           class="w-16 h-16 border border-gray-300 rounded-lg cursor-pointer">
                                    <div class="absolute inset-0 rounded-lg border-2 border-white shadow-sm pointer-events-none"></div>
                                </div>
                                <div class="flex-1">
                                    <input type="text" 
                                           id="text_color_text" 
                                           value="<?php echo htmlspecialchars($current_settings['text_color'] ?? '#1F2937'); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono"
                                           placeholder="#1F2937">
                                    <p class="text-xs text-gray-500 mt-1">মূল টেক্সট</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Header Color -->
                        <div class="color-input-group">
                            <label for="header_color" class="block text-sm font-medium text-gray-700 mb-2">
                                হেডার কালার
                            </label>
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <input type="color" 
                                           id="header_color" 
                                           name="header_color" 
                                           value="<?php echo htmlspecialchars($current_settings['header_color'] ?? '#FFFFFF'); ?>"
                                           class="w-16 h-16 border border-gray-300 rounded-lg cursor-pointer">
                                    <div class="absolute inset-0 rounded-lg border-2 border-white shadow-sm pointer-events-none"></div>
                                </div>
                                <div class="flex-1">
                                    <input type="text" 
                                           id="header_color_text" 
                                           value="<?php echo htmlspecialchars($current_settings['header_color'] ?? '#FFFFFF'); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono"
                                           placeholder="#FFFFFF">
                                    <p class="text-xs text-gray-500 mt-1">নেভিগেশন এরিয়া</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Footer Color -->
                        <div class="color-input-group">
                            <label for="footer_color" class="block text-sm font-medium text-gray-700 mb-2">
                                ফুটার কালার
                            </label>
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <input type="color" 
                                           id="footer_color" 
                                           name="footer_color" 
                                           value="<?php echo htmlspecialchars($current_settings['footer_color'] ?? '#1F2937'); ?>"
                                           class="w-16 h-16 border border-gray-300 rounded-lg cursor-pointer">
                                    <div class="absolute inset-0 rounded-lg border-2 border-white shadow-sm pointer-events-none"></div>
                                </div>
                                <div class="flex-1">
                                    <input type="text" 
                                           id="footer_color_text" 
                                           value="<?php echo htmlspecialchars($current_settings['footer_color'] ?? '#1F2937'); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono"
                                           placeholder="#1F2937">
                                    <p class="text-xs text-gray-500 mt-1">ফুটার এরিয়া</p>
                                </div>
                            </div>
                        </div>
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
                
                <!-- Custom CSS -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">কাস্টম CSS</h3>
                    
                    <div>
                        <label for="custom_css" class="block text-sm font-medium text-gray-700 mb-2">
                            অতিরিক্ত CSS কোড
                        </label>
                        <textarea id="custom_css" 
                                  name="custom_css" 
                                  rows="10" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                                  placeholder="/* আপনার কাস্টম CSS কোড এখানে লিখুন */
.custom-style {
    background-color: #f0f0f0;
    padding: 20px;
}"><?php echo htmlspecialchars($current_settings['custom_css'] ?? ''); ?></textarea>
                        <p class="text-sm text-gray-500 mt-1">সাবধানে CSS কোড লিখুন। ভুল কোড সাইটের ডিজাইন নষ্ট করতে পারে।</p>
                    </div>
                </div>
                
                <!-- Security & Integration -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">নিরাপত্তা ও ইন্টিগ্রেশন</h3>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Google reCAPTCHA -->
                        <div>
                            <h4 class="text-md font-medium text-gray-800 mb-3">Google reCAPTCHA</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="recaptcha_site_key" class="block text-sm font-medium text-gray-700 mb-2">
                                        Site Key
                                    </label>
                                    <input type="text" 
                                           id="recaptcha_site_key" 
                                           name="recaptcha_site_key" 
                                           value="<?php echo htmlspecialchars($current_settings['recaptcha_site_key'] ?? ''); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="6LcXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">
                                </div>
                                
                                <div>
                                    <label for="recaptcha_secret_key" class="block text-sm font-medium text-gray-700 mb-2">
                                        Secret Key
                                    </label>
                                    <input type="password" 
                                           id="recaptcha_secret_key" 
                                           name="recaptcha_secret_key" 
                                           value="<?php echo htmlspecialchars($current_settings['recaptcha_secret_key'] ?? ''); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="6LcXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cloudflare -->
                        <div>
                            <h4 class="text-md font-medium text-gray-800 mb-3">Cloudflare</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="cloudflare_zone_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Zone ID
                                    </label>
                                    <input type="text" 
                                           id="cloudflare_zone_id" 
                                           name="cloudflare_zone_id" 
                                           value="<?php echo htmlspecialchars($current_settings['cloudflare_zone_id'] ?? ''); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="a1b2c3d4e5f6g7h8i9j0...">
                                </div>
                                
                                <div>
                                    <label for="cloudflare_api_token" class="block text-sm font-medium text-gray-700 mb-2">
                                        API Token
                                    </label>
                                    <input type="password" 
                                           id="cloudflare_api_token" 
                                           name="cloudflare_api_token" 
                                           value="<?php echo htmlspecialchars($current_settings['cloudflare_api_token'] ?? ''); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="ক্যাশ পার্জের জন্য">
                                </div>
                            </div>
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

<script>
// Color picker sync functions with live preview
const colorInputs = [
    { picker: 'primary_color', text: 'primary_color_text', preview: 'preview-btn-primary' },
    { picker: 'secondary_color', text: 'secondary_color_text', preview: 'preview-btn-secondary' },
    { picker: 'body_color', text: 'body_color_text', preview: 'live-preview' },
    { picker: 'text_color', text: 'text_color_text', preview: 'live-preview' },
    { picker: 'header_color', text: 'header_color_text', preview: 'preview-header' },
    { picker: 'footer_color', text: 'footer_color_text', preview: 'preview-footer' },
];

// Update live preview
function updateLivePreview() {
    const bodyColor = document.getElementById('body_color').value;
    const textColor = document.getElementById('text_color').value;
    const headerColor = document.getElementById('header_color').value;
    const footerColor = document.getElementById('footer_color').value;
    const primaryColor = document.getElementById('primary_color').value;
    const secondaryColor = document.getElementById('secondary_color').value;
    
    const livePreview = document.getElementById('live-preview');
    const previewHeader = document.getElementById('preview-header');
    const previewFooter = document.getElementById('preview-footer');
    const primaryBtn = document.getElementById('preview-btn-primary');
    const secondaryBtn = document.getElementById('preview-btn-secondary');
    
    livePreview.style.backgroundColor = bodyColor;
    livePreview.style.color = textColor;
    previewHeader.style.backgroundColor = headerColor;
    previewHeader.style.color = textColor;
    previewFooter.style.backgroundColor = footerColor;
    primaryBtn.style.backgroundColor = primaryColor;
    secondaryBtn.style.backgroundColor = secondaryColor;
}

colorInputs.forEach(({ picker, text, preview }) => {
    // Sync color picker to text input
    document.getElementById(picker).addEventListener('change', function() {
        document.getElementById(text).value = this.value;
        updateLivePreview();
    });
    
    // Sync text input to color picker
    document.getElementById(text).addEventListener('input', function() {
        if (/^#[0-9A-F]{6}$/i.test(this.value)) {
            document.getElementById(picker).value = this.value;
            updateLivePreview();
        }
    });
});

// Color theme presets
function applyColorTheme(theme) {
    const themes = {
        blue: {
            primary: '#3B82F6',
            secondary: '#10B981',
            body: '#F9FAFB',
            text: '#1F2937',
            header: '#FFFFFF',
            footer: '#1F2937'
        },
        green: {
            primary: '#059669',
            secondary: '#DC2626',
            body: '#F0FDF4',
            text: '#1F2937',
            header: '#FFFFFF',
            footer: '#1F2937'
        },
        purple: {
            primary: '#7C3AED',
            secondary: '#F59E0B',
            body: '#FAF5FF',
            text: '#1F2937',
            header: '#FFFFFF',
            footer: '#1F2937'
        },
        dark: {
            primary: '#6366F1',
            secondary: '#10B981',
            body: '#111827',
            text: '#F9FAFB',
            header: '#1F2937',
            footer: '#000000'
        }
    };
    
    const selectedTheme = themes[theme];
    if (!selectedTheme) return;
    
    Object.keys(selectedTheme).forEach(key => {
        const colorInput = document.getElementById(key + '_color');
        const textInput = document.getElementById(key + '_color_text');
        
        if (colorInput && textInput) {
            colorInput.value = selectedTheme[key];
            textInput.value = selectedTheme[key];
        }
    });
    
    updateLivePreview();
}

// Initialize live preview
document.addEventListener('DOMContentLoaded', function() {
    updateLivePreview();
});

// CSS validation
document.getElementById('custom_css').addEventListener('input', function() {
    const css = this.value;
    const isValid = css === '' || /^[\s\S]*$/.test(css); // Basic validation
    
    if (!isValid) {
        this.classList.add('border-red-500');
    } else {
        this.classList.remove('border-red-500');
    }
});
</script>

<?php include '../includes/footer.php'; ?>