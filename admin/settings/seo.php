<?php
/**
 * Enhanced SEO Settings with Sitemap and Robots.txt
 */

// Define access constant
define('MSR_ACCESS', true);

// Define ROOT_PATH if not already defined
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__DIR__)));
}

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
        // Handle sitemap generation
        if (isset($_POST['generate_sitemap'])) {
            if (generateSitemap()) {
                $success = 'সাইটম্যাপ সফলভাবে তৈরি করা হয়েছে।';
            } else {
                $error = 'সাইটম্যাপ তৈরি করতে সমস্যা হয়েছে।';
            }
        }
        // Handle robots.txt generation
        elseif (isset($_POST['generate_robots'])) {
            if (generateRobotsTxt()) {
                $success = 'Robots.txt ফাইল সফলভাবে তৈরি করা হয়েছে।';
            } else {
                $error = 'Robots.txt ফাইল তৈরি করতে সমস্যা হয়েছে।';
            }
        }
        // Handle regular settings update
        else {
            // Get form data
            $settings = [
                'meta_title' => sanitize($_POST['meta_title'] ?? ''),
                'meta_description' => sanitize($_POST['meta_description'] ?? ''),
                'meta_keywords' => sanitize($_POST['meta_keywords'] ?? ''),
                'google_analytics' => sanitize($_POST['google_analytics'] ?? ''),
                'google_console_verification' => sanitize($_POST['google_console_verification'] ?? ''),
                'facebook_pixel' => sanitize($_POST['facebook_pixel'] ?? ''),
                'bing_webmaster_verification' => sanitize($_POST['bing_webmaster_verification'] ?? ''),
                'auto_sitemap' => isset($_POST['auto_sitemap']) ? 1 : 0,
            ];
            
            // Handle OG image upload
            if (isset($_FILES['og_image']) && $_FILES['og_image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = uploadFile($_FILES['og_image'], 'seo');
                if ($upload_result['success']) {
                    // Delete old OG image
                    $old_og_image = getSeoSetting('og_image');
                    if ($old_og_image) {
                        deleteFile('seo', $old_og_image);
                    }
                    $settings['og_image'] = $upload_result['filename'];
                } else {
                    $error = $upload_result['message'];
                }
            }
            
            if (empty($error)) {
                // Update settings
                foreach ($settings as $key => $value) {
                    $existing = $db->fetchOne("SELECT id FROM seo_settings WHERE setting_key = ?", [$key]);
                    if ($existing) {
                        $db->query("UPDATE seo_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
                    } else {
                        $db->query("INSERT INTO seo_settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
                    }
                }
                
                $success = 'SEO সেটিংস সফলভাবে আপডেট করা হয়েছে।';
            }
        }
    }
}

// Get current settings
$current_settings = [];
$settings_result = $db->fetchAll("SELECT setting_key, setting_value FROM seo_settings");
foreach ($settings_result as $setting) {
    $current_settings[$setting['setting_key']] = $setting['setting_value'];
}

// Check if sitemap exists
$sitemap_exists = file_exists(ROOT_PATH . '/sitemap.xml');
$robots_exists = file_exists(ROOT_PATH . '/robots.txt');

// Include admin header
include '../includes/header.php';
?>

<!-- Page Header -->
<div class="bg-white shadow-sm border-b border-gray-200">
    <div class="px-6 py-4">
        <h1 class="text-2xl font-bold text-gray-900">SEO সেটিংস</h1>
        <p class="text-gray-600">সার্চ ইঞ্জিন অপ্টিমাইজেশন এবং অ্যানালিটিক্স সেটিংস</p>
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
    
    <!-- Quick Actions -->
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <!-- Sitemap Management -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">সাইটম্যাপ ব্যবস্থাপনা</h3>
            
            <?php if ($sitemap_exists): ?>
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-800 text-sm">
                        ✅ সাইটম্যাপ বিদ্যমান: 
                        <a href="<?php echo SITE_URL; ?>/sitemap.xml" target="_blank" class="text-green-600 underline hover:text-green-700">
                            sitemap.xml দেখুন
                        </a>
                    </p>
                    <p class="text-green-600 text-xs mt-1">
                        শেষ আপডেট: <?php echo date('d/m/Y H:i', filemtime(ROOT_PATH . '/sitemap.xml')); ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-800 text-sm">⚠️ কোন সাইটম্যাপ পাওয়া যায়নি</p>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <button type="submit" name="generate_sitemap" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <?php echo $sitemap_exists ? 'সাইটম্যাপ আপডেট করুন' : 'সাইটম্যাপ তৈরি করুন'; ?>
                </button>
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            </form>
        </div>
        
        <!-- Robots.txt Management -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Robots.txt ব্যবস্থাপনা</h3>
            
            <?php if ($robots_exists): ?>
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-800 text-sm">
                        ✅ Robots.txt বিদ্যমান: 
                        <a href="<?php echo SITE_URL; ?>/robots.txt" target="_blank" class="text-green-600 underline hover:text-green-700">
                            robots.txt দেখুন
                        </a>
                    </p>
                    <p class="text-green-600 text-xs mt-1">
                        শেষ আপডেট: <?php echo date('d/m/Y H:i', filemtime(ROOT_PATH . '/robots.txt')); ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-800 text-sm">⚠️ কোন robots.txt পাওয়া যায়নি</p>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <button type="submit" name="generate_robots" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <?php echo $robots_exists ? 'Robots.txt আপডেট করুন' : 'Robots.txt তৈরি করুন'; ?>
                </button>
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            </form>
        </div>
    </div>
    
    <!-- SEO Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6">
            <form method="POST" enctype="multipart/form-data" class="space-y-8">
                <!-- Meta Tags -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">মেটা ট্যাগ</h3>
                    
                    <!-- Meta Title -->
                    <div class="mb-6">
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                            মেটা টাইটেল
                        </label>
                        <input type="text" 
                               id="meta_title" 
                               name="meta_title" 
                               maxlength="60"
                               value="<?php echo htmlspecialchars($current_settings['meta_title'] ?? getSiteSetting('site_name', SITE_NAME)); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="সাইটের SEO বান্ধব টাইটেল (সর্বোচ্চ ৬০ অক্ষর)">
                        <p class="text-sm text-gray-500 mt-1">ব্রাউজার ট্যাব এবং Google সার্চ রেজাল্টে দেখানো হবে</p>
                    </div>
                    
                    <!-- Meta Description -->
                    <div class="mb-6">
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                            মেটা বিবরণ
                        </label>
                        <textarea id="meta_description" 
                                  name="meta_description" 
                                  rows="3" 
                                  maxlength="160"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="সাইটের সংক্ষিপ্ত বিবরণ (সর্বোচ্চ ১৬০ অক্ষর)"><?php echo htmlspecialchars($current_settings['meta_description'] ?? (defined('SITE_DESCRIPTION') ? SITE_DESCRIPTION : '')); ?></textarea>
                        <p class="text-sm text-gray-500 mt-1">Google সার্চ রেজাল্টে দেখানো হবে</p>
                    </div>
                    
                    <!-- Meta Keywords -->
                    <div>
                        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">
                            মেটা কীওয়ার্ড
                        </label>
                        <textarea id="meta_keywords" 
                                  name="meta_keywords" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="কমা দিয়ে আলাদা করুন: movie review, বাংলা মুভি, drama"><?php echo htmlspecialchars($current_settings['meta_keywords'] ?? (defined('SITE_KEYWORDS') ? SITE_KEYWORDS : '')); ?></textarea>
                    </div>
                </div>
                
                <!-- Sitemap Settings -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">সাইটম্যাপ সেটিংস</h3>
                    
                    <div>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" 
                                   name="auto_sitemap" 
                                   <?php echo ($current_settings['auto_sitemap'] ?? 0) ? 'checked' : ''; ?>
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">
                                স্বয়ংক্রিয় সাইটম্যাপ আপডেট
                            </span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1 ml-7">নতুন রিভিউ প্রকাশের সময় সাইটম্যাপ স্বয়ংক্রিয়ভাবে আপডেট হবে</p>
                    </div>
                </div>
                
                <!-- Social Media -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">সোশ্যাল মিডিয়া</h3>
                    
                    <!-- OG Image -->
                    <div>
                        <label for="og_image" class="block text-sm font-medium text-gray-700 mb-2">
                            OpenGraph ছবি
                        </label>
                        
                        <?php if (!empty($current_settings['og_image'])): ?>
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                <img src="<?php echo (defined('UPLOADS_URL') ? UPLOADS_URL : '/uploads'); ?>/seo/<?php echo $current_settings['og_image']; ?>" 
                                     alt="Current OG Image" 
                                     class="h-32 w-auto">
                                <p class="text-sm text-gray-600 mt-2">বর্তমান OG ছবি</p>
                            </div>
                        <?php endif; ?>
                        
                        <input type="file" 
                               id="og_image" 
                               name="og_image" 
                               accept="image/*" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-sm text-gray-500 mt-1">১২০০x৬৩০ পিক্সেল, PNG বা JPG ফর্ম্যাট। Facebook/Twitter শেয়ারের সময় দেখানো হবে।</p>
                    </div>
                </div>
                
                <!-- Analytics -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">অ্যানালিটিক্স</h3>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Google Analytics -->
                        <div>
                            <label for="google_analytics" class="block text-sm font-medium text-gray-700 mb-2">
                                Google Analytics ID
                            </label>
                            <input type="text" 
                                   id="google_analytics" 
                                   name="google_analytics" 
                                   value="<?php echo htmlspecialchars($current_settings['google_analytics'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="G-XXXXXXXXXX">
                            <p class="text-sm text-gray-500 mt-1">Google Analytics 4 থেকে কপি করুন</p>
                        </div>
                        
                        <!-- Facebook Pixel -->
                        <div>
                            <label for="facebook_pixel" class="block text-sm font-medium text-gray-700 mb-2">
                                Facebook Pixel ID
                            </label>
                            <input type="text" 
                                   id="facebook_pixel" 
                                   name="facebook_pixel" 
                                   value="<?php echo htmlspecialchars($current_settings['facebook_pixel'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="123456789012345">
                            <p class="text-sm text-gray-500 mt-1">Facebook Ads Manager থেকে কপি করুন</p>
                        </div>
                    </div>
                </div>
                
                <!-- Webmaster Tools -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">ওয়েবমাস্টার টুলস</h3>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Google Search Console -->
                        <div>
                            <label for="google_console_verification" class="block text-sm font-medium text-gray-700 mb-2">
                                Google Search Console Verification
                            </label>
                            <input type="text" 
                                   id="google_console_verification" 
                                   name="google_console_verification" 
                                   value="<?php echo htmlspecialchars($current_settings['google_console_verification'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="abc123def456...">
                            <p class="text-sm text-gray-500 mt-1">HTML tag থেকে content অংশ কপি করুন</p>
                        </div>
                        
                        <!-- Bing Webmaster -->
                        <div>
                            <label for="bing_webmaster_verification" class="block text-sm font-medium text-gray-700 mb-2">
                                Bing Webmaster Verification
                            </label>
                            <input type="text" 
                                   id="bing_webmaster_verification" 
                                   name="bing_webmaster_verification" 
                                   value="<?php echo htmlspecialchars($current_settings['bing_webmaster_verification'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="123ABC456DEF...">
                            <p class="text-sm text-gray-500 mt-1">Bing Webmaster Tools থেকে কপি করুন</p>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="flex justify-end pt-6 border-t border-gray-200">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        SEO সেটিংস সংরক্ষণ করুন
                    </button>
                </div>
                
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            </form>
        </div>
    </div>
    
    <!-- SEO Tips -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h4 class="text-lg font-semibold text-blue-900 mb-4">SEO টিপস</h4>
        <ul class="text-blue-800 space-y-2">
            <li class="flex items-start">
                <span class="text-blue-600 mr-2">•</span>
                <span>মেটা টাইটেল ৫০-৬০ অক্ষরের মধ্যে রাখুন</span>
            </li>
            <li class="flex items-start">
                <span class="text-blue-600 mr-2">•</span>
                <span>মেটা বিবরণ ১৫০-১৬০ অক্ষরের মধ্যে রাখুন</span>
            </li>
            <li class="flex items-start">
                <span class="text-blue-600 mr-2">•</span>
                <span>কীওয়ার্ড গুলো প্রাসঙ্গিক এবং জনপ্রিয় হতে হবে</span>
            </li>
            <li class="flex items-start">
                <span class="text-blue-600 mr-2">•</span>
                <span>OpenGraph ছবি ১২০০x৬৩০ পিক্সেল হলে সবচেয়ে ভালো দেখায়</span>
            </li>
            <li class="flex items-start">
                <span class="text-blue-600 mr-2">•</span>
                <span>নিয়মিত সাইটম্যাপ আপডেট করুন নতুন কন্টেন্টের জন্য</span>
            </li>
            <li class="flex items-start">
                <span class="text-blue-600 mr-2">•</span>
                <span>Google Analytics দিয়ে ভিজিটর ট্র্যাক করুন</span>
            </li>
        </ul>
    </div>
</div>

<?php include '../includes/footer.php'; ?>