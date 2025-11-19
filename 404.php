<?php
/**
 * 404 Error Page
 */

// Define access constant only if not already defined
if (!defined('MSR_ACCESS')) {
    define('MSR_ACCESS', true);
}

// Include configuration only if not already included
if (!class_exists('Database')) {
    require_once 'config/config.php';
}

// Set 404 header
if (!headers_sent()) {
    header('HTTP/1.0 404 Not Found');
}

// Rest of your 404.php content remains the same...

// Get database instance for suggestions
$db = Database::getInstance();

// Get some popular reviews for suggestions
$popular_reviews = $db->fetchAll("
    SELECT title, slug, poster_image 
    FROM reviews 
    WHERE status = 'published' 
    ORDER BY view_count DESC 
    LIMIT 6
");

// Get random categories
$categories = $db->fetchAll("
    SELECT name, name_bn, slug, icon, color 
    FROM categories 
    WHERE status = 'active' 
    ORDER BY RAND() 
    LIMIT 4
");

// SEO Meta Data
$seo_title = 'পেজ পাওয়া যায়নি (404) - ' . getSiteSetting('site_name', SITE_NAME);
$seo_description = 'দুঃখিত, আপনি যে পেজটি খুঁজছেন সেটি পাওয়া যায়নি। আমাদের অন্যান্য কন্টেন্ট দেখুন।';
$seo_keywords = '404, page not found, পেজ পাওয়া যায়নি';

// Include header only if not already included
if (!isset($header_included)) {
    include 'includes/header.php';
    $header_included = true;
}
?>

<!-- 404 Error Section -->
<section class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 flex items-center justify-center py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <!-- 404 Illustration -->
            <div class="mb-12">
                <div class="relative">
                    <!-- Large 404 Text -->
                    <h1 class="text-[120px] md:text-[200px] font-bold text-gray-200 leading-none select-none">
                        404
                    </h1>
                    
                    <!-- Centered Icon -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-24 h-24 md:w-32 md:h-32 bg-blue-600 rounded-full flex items-center justify-center animate-bounce">
                            <svg class="w-12 h-12 md:w-16 md:h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Error Message -->
            <div class="mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    উফ! পেজটি পাওয়া যায়নি
                </h2>
                <p class="text-lg md:text-xl text-gray-600 mb-6">
                    আপনি যে পেজটি খুঁজছেন সেটি সরানো হয়েছে, মুছে ফেলা হয়েছে বা কখনো ছিলই না।
                </p>
                <p class="text-gray-500">
                    চিন্তা নেই! আমাদের অন্যান্য দুর্দান্ত কন্টেন্ট দেখুন।
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
                <a href="/" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-colors transform hover:scale-105">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    হোমে ফিরুন
                </a>
                
                <a href="/search" 
                   class="border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white px-8 py-4 rounded-xl font-semibold text-lg transition-colors transform hover:scale-105">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    খুঁজুন
                </a>
                
                <button onclick="window.history.back()" 
                        class="border-2 border-gray-300 text-gray-700 hover:bg-gray-50 px-8 py-4 rounded-xl font-semibold text-lg transition-colors transform hover:scale-105">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    পেছনে যান
                </button>
            </div>
        </div>
        
        <!-- Suggestions -->
        <div class="max-w-6xl mx-auto">
            <!-- Popular Reviews -->
            <?php if (!empty($popular_reviews)): ?>
                <div class="mb-16">
                    <h3 class="text-2xl font-bold text-gray-900 text-center mb-8">
                        জনপ্রিয় রিভিউ দেখুন
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                        <?php foreach ($popular_reviews as $review): ?>
                            <div class="group">
                                <a href="/review/<?php echo $review['slug']; ?>" 
                                   class="block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden transform hover:-translate-y-2">
                                    
                                    <div class="aspect-[2/3] bg-gray-200 overflow-hidden">
                                        <?php if ($review['poster_image']): ?>
                                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                                 loading="lazy">
                                        <?php else: ?>
                                            <div class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="p-3">
                                        <h4 class="font-medium text-gray-900 text-sm line-clamp-2 group-hover:text-blue-600 transition-colors">
                                            <?php echo htmlspecialchars($review['title']); ?>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Categories -->
            <?php if (!empty($categories)): ?>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 text-center mb-8">
                        ক্যাটেগরি অনুযায়ী ব্রাউজ করুন
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <?php foreach ($categories as $category): ?>
                            <div class="group">
                                <a href="/category/<?php echo $category['slug']; ?>" 
                                   class="block bg-white rounded-xl p-6 text-center hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                                    
                                    <!-- Icon -->
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center transition-all duration-300 group-hover:scale-110" 
                                         style="background-color: <?php echo $category['color']; ?>20;">
                                        <?php if ($category['icon']): ?>
                                            <i class="<?php echo $category['icon']; ?> text-2xl" style="color: <?php echo $category['color']; ?>"></i>
                                        <?php else: ?>
                                            <svg class="w-6 h-6" style="color: <?php echo $category['color']; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Name -->
                                    <h4 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                        <?php echo htmlspecialchars($category['name_bn'] ?: $category['name']); ?>
                                    </h4>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Help Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-8">
                এখনও খুঁজে পাচ্ছেন না?
            </h3>
            
            <div class="bg-gray-50 rounded-2xl p-8">
                <p class="text-gray-700 mb-6">
                    আপনি যদি মনে করেন এটি একটি ভুল, অথবা আপনার সাহায্য প্রয়োজন, 
                    তাহলে আমাদের সাথে যোগাযোগ করুন।
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/contact" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        যোগাযোগ করুন
                    </a>
                    <a href="/submit-review" 
                       class="border border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-3 rounded-lg font-medium transition-colors">
                        রিভিউ জমা দিন
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?php 
// Include footer only if not already included and if we're not inside another page
if (!isset($footer_included) && !isset($included_in_page)) {
    include 'includes/footer.php';
    $footer_included = true;
}
?>