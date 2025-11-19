<?php
/**
 * MSR Homepage - Redesigned Layout
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config/config.php';

// Get database instance
$db = Database::getInstance();

// Get latest reviews
$latest_reviews = $db->fetchAll("
    SELECT r.*, c.name as category_name, c.name_bn as category_name_bn
    FROM reviews r 
    LEFT JOIN review_categories rc ON r.id = rc.review_id
    LEFT JOIN categories c ON rc.category_id = c.id
    WHERE r.status = 'published' 
    ORDER BY r.created_at DESC 
    LIMIT 6
");

// Get featured reviews
$featured_reviews = $db->fetchAll("
    SELECT r.*, c.name as category_name, c.name_bn as category_name_bn
    FROM reviews r 
    LEFT JOIN review_categories rc ON r.id = rc.review_id
    LEFT JOIN categories c ON rc.category_id = c.id
    WHERE r.status = 'published' AND r.featured = 1 
    ORDER BY r.created_at DESC 
    LIMIT 3
");

// Get popular reviews
$popular_reviews = $db->fetchAll("
    SELECT r.*, c.name as category_name, c.name_bn as category_name_bn
    FROM reviews r 
    LEFT JOIN review_categories rc ON r.id = rc.review_id
    LEFT JOIN categories c ON rc.category_id = c.id
    WHERE r.status = 'published' 
    ORDER BY r.view_count DESC 
    LIMIT 4
");

// Get categories
$categories = $db->fetchAll("
    SELECT * FROM categories 
    WHERE status = 'active' 
    ORDER BY sort_order ASC 
    LIMIT 8
");

// SEO Meta Data
$seo_title = getSiteSetting('site_name', SITE_NAME);
$seo_description = getSiteSetting('site_description', SITE_DESCRIPTION);
$seo_keywords = getSiteSetting('site_keywords', SITE_KEYWORDS);
$seo_image = getSiteSetting('og_image', SITE_URL . '/assets/images/og-default.jpg');

// Include header
include 'includes/header.php';
?>

<!-- New Hero Section with Split Layout -->
<section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-gray-900 via-purple-900 to-blue-900">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 w-80 h-80 bg-yellow-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
        <div class="absolute -bottom-40 -right-40 w-80 h-80 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-bounce delay-1000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse delay-500"></div>
    </div>
    
    <!-- Floating Movie Elements -->
    <div class="absolute top-1/4 left-1/6 w-16 h-24 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-lg transform rotate-12 opacity-70 shadow-2xl animate-float"></div>
    <div class="absolute top-1/3 right-1/5 w-20 h-28 bg-gradient-to-br from-blue-400 to-purple-600 rounded-lg transform -rotate-6 opacity-80 shadow-2xl animate-float-delayed"></div>
    <div class="absolute bottom-1/4 left-1/3 w-14 h-20 bg-gradient-to-br from-green-400 to-teal-600 rounded-lg transform rotate-45 opacity-60 shadow-2xl animate-float-slow"></div>
    
    <!-- Film Strip Effect -->
    <div class="absolute top-0 left-0 w-full h-4 bg-black opacity-30"></div>
    <div class="absolute bottom-0 left-0 w-full h-4 bg-black opacity-30"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="text-white space-y-8">
                <!-- Badge -->
                <div class="inline-flex items-center bg-white/20 backdrop-blur-md rounded-full px-6 py-3 text-lg border border-white/30 shadow-lg">
                    <span class="w-3 h-3 bg-green-400 rounded-full mr-3 animate-pulse"></span>
                    <span class="font-medium">বাংলাদেশের সেরা রিভিউ প্ল্যাটফর্ম</span>
                </div>
                
                <!-- Main Heading -->
                <h1 class="text-5xl lg:text-7xl font-black leading-tight">
                    <span class="bg-gradient-to-r from-yellow-400 via-pink-500 to-purple-600 bg-clip-text text-transparent animate-gradient-x">
                        সিনেমা ও সিরিয়াল
                    </span>
                    <br>
                    <span class="text-white drop-shadow-2xl">রিভিউ এক্সপ্লোর করুন</span>
                </h1>
                
                <!-- Description -->
                <p class="text-xl lg:text-2xl text-gray-200 leading-relaxed font-light">
                    হাজারো সিনেমা ও সিরিয়ালের গভীর বিশ্লেষণ, রেটিং এবং থিমেটিক আলোচনা। 
                    আপনার পরবর্তী দেখার জন্য সেরা পছন্দ খুঁজে নিন।
                </p>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-6">
                    <a href="/reviews" class="group bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-10 py-5 rounded-2xl font-bold text-xl transition-all duration-500 transform hover:scale-105 text-center shadow-2xl hover:shadow-3xl flex items-center justify-center relative overflow-hidden">
                        <span class="relative z-10 flex items-center">
                            সকল রিভিউ দেখুন
                            <svg class="w-6 h-6 ml-3 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    </a>
                    
                    <a href="#submit-review" class="group border-2 border-white/40 hover:border-white/80 text-white px-10 py-5 rounded-2xl font-bold text-xl transition-all duration-500 hover:bg-white/10 text-center backdrop-blur-sm flex items-center justify-center">
                        <svg class="w-6 h-6 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        রিভিউ জমা দিন
                    </a>
                </div>
                
                <!-- Stats with Icons -->
                <div class="grid grid-cols-3 gap-8 pt-8">
                    <div class="text-center group">
                        <div class="w-16 h-16 bg-yellow-400/20 rounded-2xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="text-3xl font-black text-yellow-400 group-hover:scale-110 transition-transform">
                            <?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?>+
                        </div>
                        <div class="text-gray-300 text-sm font-medium">রিভিউ</div>
                    </div>
                    
                    <div class="text-center group">
                        <div class="w-16 h-16 bg-pink-400/20 rounded-2xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div class="text-3xl font-black text-pink-400 group-hover:scale-110 transition-transform">
                            <?php echo $db->count("SELECT COUNT(*) FROM categories WHERE status = 'active'"); ?>+
                        </div>
                        <div class="text-gray-300 text-sm font-medium">ক্যাটেগরি</div>
                    </div>
                    
                    <div class="text-center group">
                        <div class="w-16 h-16 bg-green-400/20 rounded-2xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <div class="text-3xl font-black text-green-400 group-hover:scale-110 transition-transform">
                            <?php echo number_format($db->count("SELECT SUM(view_count) FROM reviews WHERE status = 'published'")); ?>+
                        </div>
                        <div class="text-gray-300 text-sm font-medium">ভিউ</div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Featured Content Carousel -->
            <div class="relative">
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-white">হট রাইট নাউ</h3>
                        <div class="flex space-x-2">
                            <button class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <?php if (!empty($featured_reviews)): ?>
                            <?php foreach (array_slice($featured_reviews, 0, 3) as $index => $review): ?>
                                <div class="bg-white/5 rounded-2xl p-4 border border-white/10 hover:bg-white/10 transition-all duration-300 group">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-20 h-28 bg-gray-700 rounded-xl overflow-hidden flex-shrink-0 relative">
                                            <?php if ($review['poster_image']): ?>
                                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                                     alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                            <?php endif; ?>
                                            <div class="absolute top-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded-full">
                                                ★ <?php echo $review['rating']; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-white font-bold text-lg group-hover:text-yellow-300 transition-colors truncate">
                                                <?php echo htmlspecialchars($review['title']); ?>
                                            </h4>
                                            <p class="text-gray-300 text-sm"><?php echo $review['year']; ?> • <?php echo $review['category_name_bn'] ?: $review['category_name']; ?></p>
                                            
                                            <div class="flex items-center mt-3">
                                                <div class="flex-1 bg-gray-700 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: <?php echo ($review['rating'] / 5) * 100; ?>%"></div>
                                                </div>
                                                <span class="text-yellow-400 text-sm font-bold ml-3"><?php echo $review['rating']; ?>.0</span>
                                            </div>
                                            
                                            <div class="flex items-center justify-between mt-4">
                                                <span class="text-gray-400 text-sm">
                                                    <?php echo number_format($review['view_count']); ?> ভিউ
                                                </span>
                                                <a href="/review/<?php echo $review['slug']; ?>" class="text-blue-300 hover:text-blue-200 text-sm font-medium flex items-center">
                                                    বিস্তারিত
                                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Floating Action Card -->
                <div class="absolute -bottom-6 -left-6 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl p-6 shadow-2xl transform rotate-3 hover:rotate-0 transition-transform duration-300">
                    <div class="text-center text-white">
                        <div class="text-3xl font-black"><?php echo $db->count("SELECT COUNT(*) FROM users"); ?>+</div>
                        <div class="text-sm font-medium">সক্রিয় রিভিউয়ার</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Enhanced Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2">
        <div class="flex flex-col items-center text-white/70">
            <span class="text-sm mb-2 animate-pulse">নিচে স্ক্রোল করুন</span>
            <div class="w-6 h-10 border-2 border-white/50 rounded-full flex justify-center">
                <div class="w-1 h-3 bg-white/70 rounded-full mt-2 animate-bounce"></div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Search & Filter Bar -->
<section class="py-8 bg-white/95 backdrop-blur-md shadow-lg sticky top-0 z-40 border-b border-gray-200">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
            <!-- Quick Categories -->
            <div class="flex flex-wrap gap-3">
                <span class="text-gray-600 font-medium hidden lg:block">দ্রুত ব্রাউজ:</span>
                <?php if (!empty($categories)): ?>
                    <?php foreach (array_slice($categories, 0, 5) as $category): ?>
                        <a href="/category/<?php echo $category['slug']; ?>" 
                           class="bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-600 px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 hover:scale-105">
                            <?php echo $category['name_bn'] ?: $category['name']; ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Quick Search -->
            <div class="flex-1 max-w-md">
                <form action="/search" method="GET" class="relative">
                    <input type="text" 
                           name="q" 
                           placeholder="সিনেমা বা সিরিয়াল খুঁজুন..." 
                           class="w-full px-6 py-3 bg-gray-100 border-0 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all pr-12">
                    <button type="submit" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Featured Reviews in Grid Layout -->
<?php if (!empty($featured_reviews)): ?>
<section class="py-16 bg-gradient-to-br from-gray-50 to-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <div class="inline-flex items-center bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-full text-sm font-medium mb-4 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                বিশেষভাবে নির্বাচিত
            </div>
            <h2 class="text-4xl lg:text-5xl font-black text-gray-900 mb-4">ফিচার্ড রিভিউ</h2>
            <p class="text-gray-600 text-xl max-w-3xl mx-auto">আমাদের বিশেষজ্ঞ টিম কর্তৃক নির্বাচিত সেরা কন্টেন্ট রিভিউ</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($featured_reviews as $review): ?>
                <div class="group bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 overflow-hidden border border-gray-100 hover:border-blue-200 transform hover:-translate-y-2">
                    <!-- Poster with Overlay -->
                    <div class="relative h-96 bg-gray-800 overflow-hidden">
                        <?php if ($review['poster_image']): ?>
                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                 alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <?php endif; ?>
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                        
                        <!-- Content Overlay -->
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                            <div class="flex items-center justify-between mb-3">
                                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold">
                                    <?php echo $review['category_name_bn'] ?: $review['category_name']; ?>
                                </span>
                                <div class="flex items-center bg-black/50 backdrop-blur-sm px-3 py-1 rounded-full">
                                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="font-bold"><?php echo $review['rating']; ?>.0</span>
                                </div>
                            </div>
                            
                            <h3 class="text-2xl font-black mb-2 group-hover:text-yellow-300 transition-colors">
                                <?php echo htmlspecialchars($review['title']); ?>
                            </h3>
                            
                            <div class="flex items-center text-gray-300 text-sm mb-4">
                                <span><?php echo $review['year']; ?></span>
                                <span class="mx-2">•</span>
                                <span><?php echo $review['language']; ?></span>
                                <span class="mx-2">•</span>
                                <span><?php echo $review['type'] == 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?></span>
                            </div>
                            
                            <p class="text-gray-200 text-sm leading-relaxed mb-4 line-clamp-2">
                                <?php echo htmlspecialchars(substr($review['excerpt'], 0, 120)); ?>...
                            </p>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-gray-300 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <?php echo number_format($review['view_count']); ?> ভিউ
                                </span>
                                <a href="/review/<?php echo $review['slug']; ?>" 
                                   class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 transform hover:scale-105 flex items-center">
                                    পড়ুন
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Submit Review Section (Replaces Newsletter) -->
<section id="submit-review" class="py-16 bg-gradient-to-br from-blue-900 to-purple-900 text-white relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full bg-black bg-opacity-20"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600 rounded-full -translate-y-48 translate-x-48 opacity-20"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-purple-600 rounded-full translate-y-40 -translate-x-40 opacity-20"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Content -->
                <div class="space-y-6">
                    <div class="inline-flex items-center bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 text-sm border border-white/30">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        আপনার মতামত শেয়ার করুন
                    </div>
                    
                    <h2 class="text-4xl lg:text-5xl font-black leading-tight">
                        আপনার রিভিউ 
                        <span class="bg-gradient-to-r from-yellow-400 to-pink-400 bg-clip-text text-transparent">
                            জমা দিন
                        </span>
                    </h2>
                    
                    <p class="text-xl text-blue-100 leading-relaxed">
                        দেখেছেন কোনো অসাধারণ সিনেমা বা সিরিয়াল? আপনার অভিজ্ঞতা অন্যদের সাথে শেয়ার করুন 
                        এবং আমাদের কমিউনিটির অংশ হয়ে উঠুন। আপনার রিভিউ হাজারো দর্শককে সাহায্য করতে পারে।
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-lg">সহজ এবং দ্রুত জমা প্রক্রিয়া</span>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-lg">বিস্তারিত রেটিং সিস্টেম</span>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-lg">সামাজিক মিডিয়াতে শেয়ার করুন</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <a href="/submit-review" 
                           class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 text-center shadow-lg hover:shadow-xl flex items-center justify-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            এখনই রিভিউ জমা দিন
                        </a>
                        
                        <a href="/how-to-submit" 
                           class="border-2 border-white/40 hover:border-white/80 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 hover:bg-white/10 text-center backdrop-blur-sm flex items-center justify-center">
                            কিভাবে জমা দেবেন
                        </a>
                    </div>
                </div>
                
                <!-- Visual Element -->
                <div class="relative">
                    <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-8 border border-white/20">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-yellow-400/20 rounded-2xl p-4 text-center transform rotate-3 hover:rotate-0 transition-transform duration-300">
                                <div class="w-12 h-12 bg-yellow-400/30 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                </div>
                                <div class="text-white font-bold">রেটিং</div>
                                <div class="text-yellow-400 text-sm">১-৫ স্টার</div>
                            </div>
                            
                            <div class="bg-blue-400/20 rounded-2xl p-4 text-center transform -rotate-3 hover:rotate-0 transition-transform duration-300">
                                <div class="w-12 h-12 bg-blue-400/30 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="text-white font-bold">ইমেজ</div>
                                <div class="text-blue-400 text-sm">পোস্টার আপলোড</div>
                            </div>
                            
                            <div class="bg-green-400/20 rounded-2xl p-4 text-center transform -rotate-2 hover:rotate-0 transition-transform duration-300">
                                <div class="w-12 h-12 bg-green-400/30 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                </div>
                                <div class="text-white font-bold">রিভিউ</div>
                                <div class="text-green-400 text-sm">বিস্তারিত লিখুন</div>
                            </div>
                            
                            <div class="bg-pink-400/20 rounded-2xl p-4 text-center transform rotate-2 hover:rotate-0 transition-transform duration-300">
                                <div class="w-12 h-12 bg-pink-400/30 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                    </svg>
                                </div>
                                <div class="text-white font-bold">শেয়ার</div>
                                <div class="text-pink-400 text-sm">সামাজিক মিডিয়া</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Element -->
                    <div class="absolute -top-4 -right-4 bg-gradient-to-br from-yellow-400 to-orange-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                        সহজ ৪ ধাপ
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0) rotate(12deg); }
    50% { transform: translateY(-20px) rotate(12deg); }
}

@keyframes float-delayed {
    0%, 100% { transform: translateY(0) rotate(-6deg); }
    50% { transform: translateY(-15px) rotate(-6deg); }
}

@keyframes float-slow {
    0%, 100% { transform: translateY(0) rotate(45deg); }
    50% { transform: translateY(-10px) rotate(45deg); }
}

@keyframes gradient-x {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 7s ease-in-out infinite;
}

.animate-float-slow {
    animation: float-slow 8s ease-in-out infinite;
}

.animate-gradient-x {
    background-size: 200% 200%;
    animation: gradient-x 3s ease infinite;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>