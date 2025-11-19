<?php
/**
 * Contact Page
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config/config.php';

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'নিরাপত্তা টোকেন যাচাই করা যায়নি।';
    } else {
        // Get form data
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        $message = sanitize($_POST['message'] ?? '');
        
        // Validate required fields
        if (empty($name)) {
            $error = 'নাম আবশ্যক।';
        } elseif (empty($email)) {
            $error = 'ইমেইল আবশ্যক।';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'সঠিক ইমেইল ঠিকানা দিন।';
        } elseif (empty($message)) {
            $error = 'বার্তা আবশ্যক।';
        } else {
            // Process contact form (save to database, send email, etc.)
            $db = Database::getInstance();
            
            $result = $db->query("
                INSERT INTO contact_messages (name, email, subject, message, ip_address, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ", [$name, $email, $subject, $message, getClientIP()]);
            
            if ($result) {
                $success = 'আপনার বার্তা সফলভাবে পাঠানো হয়েছে। আমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।';
                // Clear form
                $_POST = [];
            } else {
                $error = 'বার্তা পাঠাতে সমস্যা হয়েছে। দয়া করে আবার চেষ্টা করুন।';
            }
        }
    }
}

// SEO Meta Data
$seo_title = 'যোগাযোগ - ' . getSiteSetting('site_name', SITE_NAME);
$seo_description = 'আমাদের সাথে যোগাযোগ করুন। আপনার মতামত, পরামর্শ বা যেকোনো প্রশ্ন জানান।';
$seo_keywords = 'contact, যোগাযোগ, feedback, পরামর্শ';

// Breadcrumbs
$breadcrumbs = [
    ['name' => 'হোম', 'url' => '/'],
    ['name' => 'যোগাযোগ', 'url' => '/contact']
];

// Include header
include 'includes/header.php';
?>

<!-- Page Header -->
<section class="py-16 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600">
    <div class="container mx-auto px-4 text-center text-white">
        <!-- Breadcrumb -->
        <nav class="flex justify-center items-center space-x-2 text-sm text-white/80 mb-6">
            <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                <?php if ($index === count($breadcrumbs) - 1): ?>
                    <span><?php echo htmlspecialchars($breadcrumb['name']); ?></span>
                <?php else: ?>
                    <a href="<?php echo $breadcrumb['url']; ?>" class="hover:text-white transition-colors">
                        <?php echo htmlspecialchars($breadcrumb['name']); ?>
                    </a>
                    <span>→</span>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
        
        <h1 class="text-4xl lg:text-5xl font-bold mb-4">যোগাযোগ করুন</h1>
        <p class="text-xl lg:text-2xl text-white/90 max-w-3xl mx-auto">
            আপনার মতামত, পরামর্শ বা যেকোনো প্রশ্ন আমাদের জানান
        </p>
    </div>
</section>

<!-- Contact Content -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">আমাদের লিখুন</h2>
                    
                    <!-- Messages -->
                    <?php if ($success): ?>
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <?php echo $success; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <?php echo $error; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="space-y-6">
                        <!-- Name & Email -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    আপনার নাম <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="আপনার পূর্ণ নাম"
                                       required>
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    ইমেইল ঠিকানা <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="your@email.com"
                                       required>
                            </div>
                        </div>
                        
                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                বিষয়
                            </label>
                            <select id="subject" 
                                    name="subject" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">বিষয় নির্বাচন করুন</option>
                                <option value="সাধারণ অনুসন্ধান" <?php echo ($_POST['subject'] ?? '') === 'সাধারণ অনুসন্ধান' ? 'selected' : ''; ?>>সাধারণ অনুসন্ধান</option>
                                <option value="রিভিউ সংক্রান্ত" <?php echo ($_POST['subject'] ?? '') === 'রিভিউ সংক্রান্ত' ? 'selected' : ''; ?>>রিভিউ সংক্রান্ত</option>
                                <option value="পরামর্শ" <?php echo ($_POST['subject'] ?? '') === 'পরামর্শ' ? 'selected' : ''; ?>>পরামর্শ</option>
                                <option value="প্রযুক্তিগত সমস্যা" <?php echo ($_POST['subject'] ?? '') === 'প্রযুক্তিগত সমস্যা' ? 'selected' : ''; ?>>প্রযুক্তিগত সমস্যা</option>
                                <option value="সহযোগিতা" <?php echo ($_POST['subject'] ?? '') === 'সহযোগিতা' ? 'selected' : ''; ?>>সহযোগিতা</option>
                                <option value="অন্যান্য" <?php echo ($_POST['subject'] ?? '') === 'অন্যান্য' ? 'selected' : ''; ?>>অন্যান্য</option>
                            </select>
                        </div>
                        
                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                আপনার বার্তা <span class="text-red-500">*</span>
                            </label>
                            <textarea id="message" 
                                      name="message" 
                                      rows="6" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="আপনার বার্তা বিস্তারিত লিখুন..."
                                      required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>
                        
                        <!-- Submit Button -->
                        <div>
                            <button type="submit" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                বার্তা পাঠান
                            </button>
                        </div>
                        
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    </form>
                </div>
                
                <!-- Contact Information -->
                <div class="space-y-8">
                    <!-- Contact Details -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">যোগাযোগের তথ্য</h2>
                        
                        <div class="space-y-6">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-1">ইমেইল</h3>
                                    <p class="text-gray-600">info.saidur_bd@aol.com</p>
                                    <p class="text-sm text-gray-500">২৪ ঘন্টার মধ্যে উত্তর পাবেন</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-1">ফোন</h3>
                                    <p class="text-gray-600">+974-66489944</p>
                                    <p class="text-sm text-gray-500">সকাল ৯টা - রাত ৯টা</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-1">ঠিকানা</h3>
                                    <p class="text-gray-600">সিলেট, বাংলাদেশ</p>
                                    <p class="text-sm text-gray-500">অনলাইন ভিত্তিক সেবা</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">সাধারণ প্রশ্ন</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">কীভাবে রিভিউ জমা দিতে পারি?</h3>
                                <p class="text-gray-600 text-sm">আমাদের 'রিভিউ জমা দিন' পেজে গিয়ে ফর্ম পূরণ করুন। আমরা পর্যালোচনা করে প্রকাশ করব।</p>
                            </div>
                            
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">রিভিউ প্রকাশ হতে কতদিন লাগে?</h3>
                                <p class="text-gray-600 text-sm">সাধারণত ২-৩ ঘন্টার মধ্যে আমরা রিভিউ পর্যালোচনা করে প্রকাশ করি।</p>
                            </div>
                            
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">আমার পরামর্শ কীভাবে দিতে পারি?</h3>
                                <p class="text-gray-600 text-sm">এই যোগাযোগ ফর্মের মাধ্যমে বা ইমেইলে সরাসরি আমাদের পরামর্শ দিতে পারেন।</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>