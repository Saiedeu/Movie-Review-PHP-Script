<?php
/**
 * MSR Homepage - Complete Redesign with All Features
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config/config.php';

// Get database instance
$db = Database::getInstance();

// Get featured reviews
$featured_reviews = $db->fetchAll("
    SELECT r.*, c.name as category_name, c.name_bn as category_name_bn
    FROM reviews r 
    LEFT JOIN review_categories rc ON r.id = rc.review_id
    LEFT JOIN categories c ON rc.category_id = c.id
    WHERE r.status = 'published' AND r.featured = 1 
    ORDER BY r.created_at DESC 
    LIMIT 6
");

// Get latest reviews
$latest_reviews = $db->fetchAll("
    SELECT r.*, c.name as category_name, c.name_bn as category_name_bn
    FROM reviews r 
    LEFT JOIN review_categories rc ON r.id = rc.review_id
    LEFT JOIN categories c ON rc.category_id = c.id
    WHERE r.status = 'published' 
    ORDER BY r.created_at DESC 
    LIMIT 8
");

// Get most read reviews
$most_read_reviews = $db->fetchAll("
    SELECT r.*, c.name as category_name, c.name_bn as category_name_bn
    FROM reviews r 
    LEFT JOIN review_categories rc ON r.id = rc.review_id
    LEFT JOIN categories c ON rc.category_id = c.id
    WHERE r.status = 'published' 
    ORDER BY r.view_count DESC 
    LIMIT 8
");

// Get cinema reviews
$cinema_reviews = $db->fetchAll("
    SELECT r.*, c.name as category_name, c.name_bn as category_name_bn
    FROM reviews r 
    LEFT JOIN review_categories rc ON r.id = rc.review_id
    LEFT JOIN categories c ON rc.category_id = c.id
    WHERE r.status = 'published' AND r.type = 'movie'
    ORDER BY r.created_at DESC 
    LIMIT 6
");

// Get serial/drama reviews
$serial_reviews = $db->fetchAll("
    SELECT r.*, c.name as category_name, c.name_bn as category_name_bn
    FROM reviews r 
    LEFT JOIN review_categories rc ON r.id = rc.review_id
    LEFT JOIN categories c ON rc.category_id = c.id
    WHERE r.status = 'published' AND r.type = 'series'
    ORDER BY r.created_at DESC 
    LIMIT 6
");

// Get categories
$categories = $db->fetchAll("
    SELECT * FROM categories 
    WHERE status = 'active' 
    ORDER BY sort_order ASC 
    LIMIT 12
");

// SEO Meta Data
$seo_title = getSiteSetting('site_name', SITE_NAME);
$seo_description = getSiteSetting('site_description', SITE_DESCRIPTION);
$seo_keywords = getSiteSetting('site_keywords', SITE_KEYWORDS);
$seo_image = getSiteSetting('og_image', SITE_URL . '/assets/images/og-default.jpg');

// Include header
include 'includes/header.php';
?>

<!-- Enhanced Hero Section with Cinema Elements -->
<section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-gray-900 via-purple-900 to-blue-900">
    <!-- Animated Background -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 w-80 h-80 bg-yellow-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
        <div class="absolute -bottom-40 -right-40 w-80 h-80 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-bounce delay-1000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse delay-500"></div>
    </div>
    
    <!-- Cinema Elements -->
    <div class="absolute top-1/4 left-1/6 w-16 h-24 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-lg transform rotate-12 opacity-70 shadow-2xl animate-float">
        <div class="w-full h-full bg-gradient-to-br from-yellow-300 to-orange-400 rounded-lg flex items-center justify-center">
            <div class="w-8 h-10 bg-red-500 rounded-sm transform rotate-45"></div>
        </div>
    </div>
    
    <!-- Popcorn Basket -->
    <div class="absolute top-1/3 right-1/5 w-20 h-16 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg transform -rotate-6 opacity-80 shadow-2xl animate-float-delayed">
        <div class="w-full h-full relative">
            <!-- Popcorn pieces -->
            <div class="absolute -top-2 -left-1 w-3 h-3 bg-white rounded-full transform rotate-45 opacity-90"></div>
            <div class="absolute -top-1 right-0 w-2 h-2 bg-white rounded-full transform -rotate-12 opacity-80"></div>
            <div class="absolute top-0 left-2 w-2 h-2 bg-white rounded-full transform rotate-45 opacity-70"></div>
        </div>
    </div>
    
    <!-- Film Reel -->
    <div class="absolute bottom-1/4 left-1/3 w-14 h-14 bg-gradient-to-br from-gray-600 to-gray-800 rounded-full opacity-60 shadow-2xl animate-float-slow">
        <div class="absolute inset-2 bg-gray-900 rounded-full"></div>
        <div class="absolute inset-1 bg-gray-700 rounded-full"></div>
        <!-- Reel holes -->
        <div class="absolute top-1 left-2 w-1 h-1 bg-gray-400 rounded-full"></div>
        <div class="absolute top-2 right-1 w-1 h-1 bg-gray-400 rounded-full"></div>
        <div class="absolute bottom-2 left-1 w-1 h-1 bg-gray-400 rounded-full"></div>
        <div class="absolute bottom-1 right-2 w-1 h-1 bg-gray-400 rounded-full"></div>
    </div>
    
    <!-- Clapperboard -->
    <div class="absolute top-1/5 right-1/4 w-12 h-16 bg-gradient-to-br from-black to-gray-800 transform rotate-45 opacity-70 shadow-2xl animate-float-delayed-2">
        <div class="absolute top-2 left-2 right-2 h-4 bg-white"></div>
        <div class="absolute top-6 left-2 right-2 h-1 bg-white"></div>
    </div>
    
    <!-- Movie Ticket -->
    <div class="absolute bottom-1/3 right-1/6 w-16 h-8 bg-gradient-to-br from-green-400 to-teal-600 transform -rotate-12 opacity-60 shadow-2xl animate-float-slow-2">
        <div class="absolute top-1 left-1 right-1 h-1 bg-white/30 rounded-full"></div>
        <div class="absolute bottom-1 left-1 right-1 h-1 bg-white/30 rounded-full"></div>
    </div>
    
    <!-- Camera Icon -->
    <div class="absolute top-2/3 left-1/5 w-12 h-10 bg-gradient-to-br from-gray-400 to-gray-600 rounded-lg transform rotate-6 opacity-50 shadow-2xl animate-float">
        <div class="absolute top-1 left-2 w-8 h-6 bg-gray-800 rounded-sm"></div>
        <div class="absolute -right-1 top-1/2 transform -translate-y-1/2 w-2 h-2 bg-gray-300 rounded-full"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Content -->
            <div class="text-white space-y-8">
                <div class="inline-flex items-center bg-white/20 backdrop-blur-md rounded-full px-6 py-3 text-lg border border-white/30 shadow-lg">
                    <span class="w-3 h-3 bg-green-400 rounded-full mr-3 animate-pulse"></span>
                    বাংলাদেশের সেরা রিভিউ প্ল্যাটফর্ম
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-black leading-tight">
                    <span class="bg-gradient-to-r from-yellow-400 via-pink-500 to-purple-600 bg-clip-text text-transparent animate-gradient-x">
                        সিনেমা ও সিরিয়াল
                    </span>
                    <br>
                    <span class="text-white drop-shadow-2xl">রিভিউ এক্সপ্লোর করুন</span>
                </h1>
                
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
                
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-8 pt-8">
                    <div class="text-center group">
                        <div class="text-3xl font-black text-yellow-400 group-hover:scale-110 transition-transform">
                            <?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?>+
                        </div>
                        <div class="text-gray-300 text-sm font-medium">রিভিউ</div>
                    </div>
                    <div class="text-center group">
                        <div class="text-3xl font-black text-pink-400 group-hover:scale-110 transition-transform">
                            <?php echo $db->count("SELECT COUNT(*) FROM categories WHERE status = 'active'"); ?>+
                        </div>
                        <div class="text-gray-300 text-sm font-medium">ক্যাটেগরি</div>
                    </div>
                    <div class="text-center group">
                        <div class="text-3xl font-black text-green-400 group-hover:scale-110 transition-transform">
                            <?php echo number_format($db->count("SELECT SUM(view_count) FROM reviews WHERE status = 'published'")); ?>+
                        </div>
                        <div class="text-gray-300 text-sm font-medium">ভিউ</div>
                    </div>
                </div>
            </div>
            
            <!-- Featured Reviews Carousel -->
            <div class="relative">
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-white">ফিচার্ড রিভিউ</h3>
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

<!-- Quick Access Bar -->
<section class="py-4 bg-white/95 backdrop-blur-md shadow-lg sticky top-0 z-40 border-b border-gray-200">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                <a href="#featured" class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-medium transition-all hover:scale-105">ফিচার্ড</a>
                <a href="#latest" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-full text-sm font-medium transition-all hover:scale-105">সর্বশেষ</a>
                <a href="#popular" class="bg-red-100 text-red-700 px-4 py-2 rounded-full text-sm font-medium transition-all hover:scale-105">জনপ্রিয়</a>
                <a href="#cinema" class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-medium transition-all hover:scale-105">সিনেমা</a>
                <a href="#serial" class="bg-purple-100 text-purple-700 px-4 py-2 rounded-full text-sm font-medium transition-all hover:scale-105">সিরিয়াল</a>
                <a href="#categories" class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-full text-sm font-medium transition-all hover:scale-105">ক্যাটেগরি</a>
            </div>
            
            <div class="flex-1 max-w-md">
                <form action="/search" method="GET" class="relative">
                    <input type="text" name="q" placeholder="সিনেমা বা সিরিয়াল খুঁজুন..." 
                           class="w-full px-4 py-2 bg-gray-100 border-0 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm pr-10">
                    <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Featured Reviews Section -->
<section id="featured" class="py-16 bg-gradient-to-br from-gray-50 to-white">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="inline-flex items-center bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-medium mb-2">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    বিশেষভাবে নির্বাচিত
                </div>
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900">ফিচার্ড রিভিউ</h2>
            </div>
            <a href="/reviews?featured=1" class="hidden md:flex items-center text-blue-600 hover:text-blue-700 font-medium">
                সব দেখুন
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($featured_reviews)): ?>
                <?php foreach ($featured_reviews as $review): ?>
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-gray-100 hover:border-blue-200 transform hover:-translate-y-1">
                        <div class="relative h-64 bg-gray-800 overflow-hidden">
                            <?php if ($review['poster_image']): ?>
                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                     class="w-full h-full object-cover hover:scale-110 transition-transform duration-700">
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            <div class="absolute top-4 left-4 bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold">
                                <?php echo $review['category_name_bn'] ?: $review['category_name']; ?>
                            </div>
                            <div class="absolute top-4 right-4 bg-black/70 text-white px-3 py-1 rounded-full text-xs font-bold">
                                ★ <?php echo $review['rating']; ?>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <h3 class="text-xl font-black text-gray-900 mb-2 hover:text-blue-600 transition-colors line-clamp-2">
                                <a href="/review/<?php echo $review['slug']; ?>">
                                    <?php echo htmlspecialchars($review['title']); ?>
                                </a>
                            </h3>
                            
                            <div class="flex items-center text-gray-500 text-sm mb-3">
                                <span><?php echo $review['year']; ?></span>
                                <span class="mx-2">•</span>
                                <span><?php echo $review['language']; ?></span>
                                <span class="mx-2">•</span>
                                <span><?php echo $review['type'] == 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?></span>
                            </div>
                            
                            <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-2">
                                <?php echo htmlspecialchars(substr($review['excerpt'], 0, 100)); ?>...
                            </p>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 text-sm">
                                    <?php echo number_format($review['view_count']); ?> ভিউ
                                </span>
                                <a href="/review/<?php echo $review['slug']; ?>" class="text-blue-600 hover:text-blue-700 font-medium text-sm flex items-center">
                                    পড়ুন
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Latest Reviews Section -->
<section id="latest" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-medium mb-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    সাম্প্রতিক
                </div>
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900">সর্বশেষ রিভিউ</h2>
            </div>
            <a href="/reviews" class="hidden md:flex items-center text-blue-600 hover:text-blue-700 font-medium">
                সব দেখুন
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php if (!empty($latest_reviews)): ?>
                <?php foreach ($latest_reviews as $review): ?>
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-200">
                        <div class="relative h-48 bg-gray-800 overflow-hidden">
                            <?php if ($review['poster_image']): ?>
                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                     class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-3 left-3 right-3">
                                <div class="flex justify-between items-center text-white text-xs">
                                    <span class="bg-blue-600 px-2 py-1 rounded-full"><?php echo $review['type'] == 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?></span>
                                    <div class="flex items-center bg-black/50 px-2 py-1 rounded-full">
                                        <svg class="w-3 h-3 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <span><?php echo $review['rating']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 mb-2 hover:text-blue-600 transition-colors line-clamp-2 text-sm">
                                <a href="/review/<?php echo $review['slug']; ?>">
                                    <?php echo htmlspecialchars($review['title']); ?>
                                </a>
                            </h3>
                            
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span><?php echo $review['year']; ?></span>
                                <span><?php echo date('d M', strtotime($review['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Most Read Reviews Section -->
<section id="popular" class="py-16 bg-gradient-to-br from-red-50 to-orange-50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="inline-flex items-center bg-red-100 text-red-800 px-4 py-2 rounded-full text-sm font-medium mb-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    জনপ্রিয়
                </div>
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900">সবচেয়ে বেশি পড়া রিভিউ</h2>
            </div>
            <a href="/reviews?sort=popular" class="hidden md:flex items-center text-blue-600 hover:text-blue-700 font-medium">
                সব দেখুন
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php if (!empty($most_read_reviews)): ?>
                <?php foreach ($most_read_reviews as $index => $review): ?>
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-red-200 transform hover:-translate-y-1">
                        <div class="relative">
                            <div class="absolute top-4 left-4 z-10">
                                <div class="w-8 h-8 bg-gradient-to-br 
                                    <?php 
                                        if ($index == 0) echo 'from-yellow-500 to-yellow-600';
                                        elseif ($index == 1) echo 'from-gray-400 to-gray-500';
                                        elseif ($index == 2) echo 'from-amber-600 to-amber-700';
                                        else echo 'from-red-500 to-pink-600';
                                    ?> 
                                    text-white rounded-full flex items-center justify-center font-bold text-sm">
                                    <?php echo $index + 1; ?>
                                </div>
                            </div>
                            
                            <div class="h-48 bg-gray-800 overflow-hidden">
                                <?php if ($review['poster_image']): ?>
                                    <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                         class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <h3 class="font-bold text-gray-900 mb-2 hover:text-red-600 transition-colors line-clamp-2">
                                <a href="/review/<?php echo $review['slug']; ?>">
                                    <?php echo htmlspecialchars($review['title']); ?>
                                </a>
                            </h3>
                            
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <?php echo number_format($review['view_count']); ?> ভিউ
                                </div>
                                <div class="flex items-center text-yellow-500">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="ml-1 text-gray-700 font-medium"><?php echo $review['rating']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Cinema Reviews Section -->
<section id="cinema" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-medium mb-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
                    </svg>
                    সিনেমা রিভিউ
                </div>
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900">সিনেমা রিভিউ</h2>
            </div>
            <a href="/reviews?type=movie" class="hidden md:flex items-center text-blue-600 hover:text-blue-700 font-medium">
                সব দেখুন
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($cinema_reviews)): ?>
                <?php foreach ($cinema_reviews as $review): ?>
                    <div class="group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-green-200">
                        <div class="flex h-32">
                            <div class="w-24 h-32 bg-gray-800 flex-shrink-0 overflow-hidden">
                                <?php if ($review['poster_image']): ?>
                                    <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex-1 p-4 flex flex-col justify-between">
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-1 group-hover:text-green-600 transition-colors line-clamp-2 text-sm">
                                        <a href="/review/<?php echo $review['slug']; ?>">
                                            <?php echo htmlspecialchars($review['title']); ?>
                                        </a>
                                    </h3>
                                    <p class="text-gray-500 text-xs">
                                        <?php echo $review['year']; ?> | <?php echo $review['language']; ?>
                                    </p>
                                </div>
                                
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex text-yellow-400 text-xs">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-gray-500 text-xs"><?php echo number_format($review['view_count']); ?> ভিউ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Serial/Drama Reviews Section -->
<section id="serial" class="py-16 bg-gradient-to-br from-purple-50 to-pink-50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="inline-flex items-center bg-purple-100 text-purple-800 px-4 py-2 rounded-full text-sm font-medium mb-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    সিরিয়াল রিভিউ
                </div>
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900">সিরিয়াল ও ড্রামা রিভিউ</h2>
            </div>
            <a href="/reviews?type=series" class="hidden md:flex items-center text-blue-600 hover:text-blue-700 font-medium">
                সব দেখুন
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($serial_reviews)): ?>
                <?php foreach ($serial_reviews as $review): ?>
                    <div class="group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-purple-200">
                        <div class="flex h-32">
                            <div class="w-24 h-32 bg-gray-800 flex-shrink-0 overflow-hidden">
                                <?php if ($review['poster_image']): ?>
                                    <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex-1 p-4 flex flex-col justify-between">
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-1 group-hover:text-purple-600 transition-colors line-clamp-2 text-sm">
                                        <a href="/review/<?php echo $review['slug']; ?>">
                                            <?php echo htmlspecialchars($review['title']); ?>
                                        </a>
                                    </h3>
                                    <p class="text-gray-500 text-xs">
                                        <?php echo $review['year']; ?> | <?php echo $review['language']; ?>
                                    </p>
                                </div>
                                
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex text-yellow-400 text-xs">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-gray-500 text-xs"><?php echo number_format($review['view_count']); ?> ভিউ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section id="categories" class="py-16 bg-gradient-to-br from-indigo-50 to-blue-100">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <div class="inline-flex items-center bg-indigo-100 text-indigo-800 px-4 py-2 rounded-full text-sm font-medium mb-4">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                ক্যাটেগরি
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 mb-4">জনরা অনুযায়ী ব্রাউজ করুন</h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">আপনার পছন্দের ধরণের কন্টেন্ট খুঁজে নিন</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <a href="/category/<?php echo $category['slug']; ?>" 
                       class="group bg-white rounded-xl p-4 text-center hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-gray-200 hover:border-indigo-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                            <?php if ($category['icon']): ?>
                                <i class="<?php echo $category['icon']; ?> text-xl text-indigo-600"></i>
                            <?php else: ?>
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors text-sm">
                            <?php echo $category['name_bn'] ?: $category['name']; ?>
                        </h3>
                        <p class="text-gray-500 text-xs mt-1">
                            <?php echo $db->count("SELECT COUNT(*) FROM review_categories rc JOIN reviews r ON rc.review_id = r.id WHERE rc.category_id = ? AND r.status = 'published'", [$category['id']]); ?> রিভিউ
                        </p>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="text-center mt-8">
            <a href="/categories" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                সব ক্যাটেগরি দেখুন
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Submit Review Section -->
<section id="submit-review" class="py-16 bg-gradient-to-br from-blue-900 to-purple-900 text-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl lg:text-5xl font-black mb-6">আপনার রিভিউ জমা দিন</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                আপনার প্রিয় সিনেমা বা সিরিয়াল সম্পর্কে রিভিউ লিখুন এবং হাজারো দর্শকের সাথে শেয়ার করুন
            </p>
            
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="w-16 h-16 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">রিভিউ লিখুন</h3>
                    <p class="text-blue-200 text-sm">আপনার অভিজ্ঞতা এবং মতামত শেয়ার করুন</p>
                </div>
                
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">রেটিং দিন</h3>
                    <p class="text-blue-200 text-sm">১-৫ স্টার রেটিং সিস্টেম</p>
                </div>
                
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="w-16 h-16 bg-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">শেয়ার করুন</h3>
                    <p class="text-blue-200 text-sm">সামাজিক মিডিয়াতে শেয়ার করুন</p>
                </div>
            </div>
            
            <a href="/submit-review" class="inline-flex items-center bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                এখনই রিভিউ জমা দিন
            </a>
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

@keyframes float-delayed-2 {
    0%, 100% { transform: translateY(0) rotate(45deg); }
    50% { transform: translateY(-12px) rotate(45deg); }
}

@keyframes float-slow-2 {
    0%, 100% { transform: translateY(0) rotate(-12deg); }
    50% { transform: translateY(-8px) rotate(-12deg); }
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

.animate-float-delayed-2 {
    animation: float-delayed-2 9s ease-in-out infinite;
}

.animate-float-slow-2 {
    animation: float-slow-2 10s ease-in-out infinite;
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
</style>