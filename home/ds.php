<?php
/**
 * MSR Homepage - Redesigned
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

<!-- Enhanced Hero Section -->
<section class="relative bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900 min-h-screen flex items-center overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10"></div>
    
    <!-- Enhanced Floating Elements -->
    <div class="absolute top-20 left-10 w-20 h-20 bg-yellow-400 rounded-full opacity-20 animate-pulse"></div>
    <div class="absolute top-40 right-20 w-16 h-16 bg-pink-400 rounded-full opacity-20 animate-bounce" style="animation-delay: 0.5s"></div>
    <div class="absolute bottom-20 left-20 w-12 h-12 bg-green-400 rounded-full opacity-20 animate-ping" style="animation-delay: 1s"></div>
    <div class="absolute top-1/3 right-1/4 w-10 h-10 bg-blue-400 rounded-full opacity-30 animate-pulse" style="animation-delay: 1.5s"></div>
    <div class="absolute bottom-1/4 right-1/3 w-14 h-14 bg-purple-400 rounded-full opacity-25 animate-bounce" style="animation-delay: 2s"></div>
    
    <!-- Background Grid Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Enhanced Content -->
            <div class="text-white space-y-8">
                <div class="inline-flex items-center bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 text-sm border border-white/20">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                    আপনার বিশ্বস্ত রিভিউ প্ল্যাটফর্ম
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-bold leading-tight">
                    <span class="bg-gradient-to-r from-yellow-400 to-pink-400 bg-clip-text text-transparent animate-gradient">
                        সেরা সিনেমা ও সিরিয়াল
                    </span>
                    <br>
                    <span class="text-white">রিভিউ</span>
                </h1>
                
                <p class="text-xl lg:text-2xl text-gray-300 leading-relaxed">
                    বাংলা ভাষায় বিশ্বের সেরা সিনেমা ও সিরিয়ালের বিস্তারিত আলোচনা, রেটিং এবং থিমেটিক বিশ্লেষণ
                </p>
                
                <!-- Enhanced Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/reviews" class="group bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 transform hover:scale-105 text-center shadow-lg hover:shadow-xl flex items-center justify-center">
                        <span>সকল রিভিউ দেখুন</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="/submit-review" class="group border-2 border-white/30 hover:border-white/60 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 hover:bg-white/10 text-center flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>আপনার রিভিউ জমা দিন</span>
                    </a>
                </div>
                
                <!-- Enhanced Stats -->
                <div class="grid grid-cols-3 gap-8 pt-8">
                    <div class="text-center group">
                        <div class="text-3xl font-bold text-yellow-400 group-hover:scale-110 transition-transform">
                            <?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?>+
                        </div>
                        <div class="text-gray-300 text-sm">রিভিউ</div>
                    </div>
                    <div class="text-center group">
                        <div class="text-3xl font-bold text-pink-400 group-hover:scale-110 transition-transform">
                            <?php echo $db->count("SELECT COUNT(*) FROM categories WHERE status = 'active'"); ?>+
                        </div>
                        <div class="text-gray-300 text-sm">ক্যাটেগরি</div>
                    </div>
                    <div class="text-center group">
                        <div class="text-3xl font-bold text-green-400 group-hover:scale-110 transition-transform">
                            <?php echo number_format($db->count("SELECT SUM(view_count) FROM reviews WHERE status = 'published'")); ?>+
                        </div>
                        <div class="text-gray-300 text-sm">ভিউ</div>
                    </div>
                </div>
            </div>
            
            <!-- Enhanced Featured Reviews Cards -->
            <div class="relative">
                <?php if (!empty($featured_reviews)): ?>
                    <div class="grid gap-6">
                        <?php foreach (array_slice($featured_reviews, 0, 3) as $index => $review): ?>
                            <div class="group bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-lg hover:shadow-xl" style="animation-delay: <?php echo $index * 0.2; ?>s">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-24 bg-gray-300 rounded-lg overflow-hidden flex-shrink-0 relative group-hover:scale-110 transition-transform duration-300">
                                        <?php if ($review['poster_image']): ?>
                                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                                 class="w-full h-full object-cover">
                                        <?php endif; ?>
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-white font-semibold text-lg truncate group-hover:text-yellow-300 transition-colors">
                                            <a href="/review/<?php echo $review['slug']; ?>">
                                                <?php echo htmlspecialchars($review['title']); ?>
                                            </a>
                                        </h3>
                                        <p class="text-gray-300 text-sm"><?php echo $review['year']; ?></p>
                                        <div class="flex items-center mt-2">
                                            <div class="flex text-yellow-400 text-sm">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <span class="<?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-500'; ?>">★</span>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="text-gray-300 text-sm ml-2"><?php echo $review['rating']; ?>/5</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Enhanced Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce">
        <div class="flex flex-col items-center">
            <span class="text-sm mb-2 opacity-70">স্ক্রোল করুন</span>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </div>
</section>

<!-- Enhanced Search Section -->
<section class="py-16 bg-gradient-to-br from-gray-50 to-blue-50 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-200 rounded-full -translate-y-32 translate-x-32 opacity-20"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-200 rounded-full translate-y-24 -translate-x-24 opacity-20"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">আপনার পছন্দের কন্টেন্ট খুঁজুন</h2>
            <p class="text-gray-600 text-lg mb-8">হাজারো সিনেমা ও সিরিয়ালের বিশদ রিভিউ এক ক্লিকেই</p>
            
            <!-- Enhanced Search Form -->
            <div class="relative mb-8">
                <form action="/search" method="GET" class="flex shadow-lg rounded-xl overflow-hidden">
                    <input type="text" 
                           name="q" 
                           placeholder="সিনেমা বা সিরিয়ালের নাম লিখুন..." 
                           class="flex-1 px-6 py-4 text-lg border-0 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <button type="submit" 
                            class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-4 transition-all flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>খুঁজুন</span>
                    </button>
                </form>
            </div>
            
            <!-- Enhanced Filter Buttons -->
            <div class="flex flex-wrap justify-center gap-3">
                <a href="/reviews" class="bg-white border border-gray-200 hover:border-blue-500 text-gray-700 hover:text-blue-600 px-6 py-2 rounded-full transition-all shadow-sm hover:shadow-md">সকল</a>
                <a href="/reviews?type=movie" class="bg-white border border-gray-200 hover:border-blue-500 text-gray-700 hover:text-blue-600 px-6 py-2 rounded-full transition-all shadow-sm hover:shadow-md">সিনেমা</a>
                <a href="/reviews?type=series" class="bg-white border border-gray-200 hover:border-blue-500 text-gray-700 hover:text-blue-600 px-6 py-2 rounded-full transition-all shadow-sm hover:shadow-md">সিরিয়াল</a>
                <a href="/reviews?language=bangla" class="bg-white border border-gray-200 hover:border-blue-500 text-gray-700 hover:text-blue-600 px-6 py-2 rounded-full transition-all shadow-sm hover:shadow-md">বাংলা</a>
                <a href="/reviews?language=korean" class="bg-white border border-gray-200 hover:border-blue-500 text-gray-700 hover:text-blue-600 px-6 py-2 rounded-full transition-all shadow-sm hover:shadow-md">কোরিয়ান</a>
                <a href="/reviews?language=hollywood" class="bg-white border border-gray-200 hover:border-blue-500 text-gray-700 hover:text-blue-600 px-6 py-2 rounded-full transition-all shadow-sm hover:shadow-md">হলিউড</a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Reviews Section with Enhanced Design -->
<?php if (!empty($featured_reviews)): ?>
<section class="py-16 bg-white relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-yellow-50 rounded-full -translate-y-48 translate-x-48"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-12">
            <div class="inline-flex items-center bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full text-sm font-medium mb-4 shadow-sm">
                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                সপ্তাহের পছন্দ
            </div>
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">এই সপ্তাহের সেরা রিভিউ</h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">আমাদের বিশেষজ্ঞ দলের নির্বাচিত সেরা কন্টেন্ট যা আপনার মুভি ও সিরিয়াল অভিজ্ঞতাকে সমৃদ্ধ করবে</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($featured_reviews as $review): ?>
                <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-gray-100 hover:border-blue-200 relative">
                    <!-- Poster -->
                    <div class="relative h-80 bg-gray-200 overflow-hidden">
                        <?php if ($review['poster_image']): ?>
                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                 alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <?php endif; ?>
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-300"></div>
                        
                        <!-- Play Button -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Rating Badge -->
                        <div class="absolute top-4 right-4 bg-black/70 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm font-medium transform group-hover:scale-110 transition-transform">
                            ★ <?php echo $review['rating']; ?>/5
                        </div>
                        
                        <!-- Category Badge -->
                        <div class="absolute top-4 left-4 bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-medium transform group-hover:scale-110 transition-transform">
                            <?php echo $review['category_name_bn'] ?: $review['category_name']; ?>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6 relative">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">
                            <a href="/review/<?php echo $review['slug']; ?>" class="hover:underline">
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
                        
                        <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">
                            <?php echo htmlspecialchars(substr($review['excerpt'], 0, 120)); ?>...
                        </p>
                        
                        <div class="flex justify-between items-center">
                            <a href="/review/<?php echo $review['slug']; ?>" 
                               class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium transition-colors group-hover:underline">
                                সম্পূর্ণ রিভিউ পড়ুন
                                <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <span class="text-gray-400 text-sm">
                                <?php echo number_format($review['view_count']); ?> ভিউ
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Reviews Section with Enhanced Design -->
<?php if (!empty($latest_reviews)): ?>
<section class="py-16 bg-gradient-to-br from-gray-50 to-indigo-50 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-green-100 rounded-full -translate-x-32 -translate-y-32 opacity-40"></div>
    <div class="absolute bottom-0 right-0 w-80 h-80 bg-blue-100 rounded-full translate-x-40 translate-y-40 opacity-30"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="flex justify-between items-center mb-12">
            <div>
                <div class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-medium mb-4 shadow-sm">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    সাম্প্রতিক রিভিউ
                </div>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">সর্বশেষ যোগ করা সিনেমা ও সিরিয়াল রিভিউ</h2>
            </div>
            <a href="/reviews" class="hidden md:inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                সব দেখুন
                <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($latest_reviews as $review): ?>
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group border border-gray-100 hover:border-blue-200">
                    <div class="flex h-32">
                        <!-- Poster -->
                        <div class="w-24 h-32 bg-gray-200 flex-shrink-0 overflow-hidden relative">
                            <?php if ($review['poster_image']): ?>
                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-r from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">
                                        <?php echo $review['type'] == 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?>
                                    </span>
                                    <div class="flex text-yellow-400 text-sm">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                
                                <h3 class="font-bold text-gray-900 text-lg mb-1 group-hover:text-blue-600 transition-colors line-clamp-2">
                                    <a href="/review/<?php echo $review['slug']; ?>">
                                        <?php echo htmlspecialchars($review['title']); ?>
                                    </a>
                                </h3>
                                
                                <p class="text-gray-500 text-sm">
                                    <?php echo $review['year']; ?> | <?php echo $review['language']; ?>
                                </p>
                            </div>
                            
                            <p class="text-gray-600 text-sm mt-2 line-clamp-2">
                                <?php echo htmlspecialchars(substr($review['excerpt'], 0, 80)); ?>...
                            </p>
                        </div>
                    </div>
                    
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 group-hover:bg-blue-50 transition-colors">
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <?php echo date('d M Y', strtotime($review['created_at'])); ?>
                            </span>
                            <a href="/review/<?php echo $review['slug']; ?>" class="text-blue-600 hover:text-blue-700 font-medium flex items-center group-hover:underline">
                                পড়ুন
                                <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-8 md:hidden">
            <a href="/reviews" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                সব দেখুন
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Popular Reviews Section with Enhanced Design -->
<?php if (!empty($popular_reviews)): ?>
<section class="py-16 bg-white relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-red-50 to-transparent"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-12">
            <div class="inline-flex items-center bg-red-100 text-red-800 px-4 py-2 rounded-full text-sm font-medium mb-4 shadow-sm">
                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                জনপ্রিয় রিভিউ
            </div>
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">সবচেয়ে বেশি পড়া রিভিউগুলো</h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">আমাদের পাঠকরা কোন রিভিউগুলো সবচেয়ে বেশি পড়ছেন তা দেখে নিন</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($popular_reviews as $index => $review): ?>
                <div class="group relative">
                    <div class="flex items-center space-x-4 bg-white p-4 rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <!-- Ranking Number -->
                        <div class="w-8 h-8 bg-gradient-to-br 
                            <?php 
                                if ($index == 0) echo 'from-yellow-500 to-yellow-600';
                                elseif ($index == 1) echo 'from-gray-400 to-gray-500';
                                elseif ($index == 2) echo 'from-amber-600 to-amber-700';
                                else echo 'from-blue-500 to-purple-600';
                            ?> 
                            text-white rounded-full flex items-center justify-center font-bold text-sm transform group-hover:scale-110 transition-transform">
                            <?php echo $index + 1; ?>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors truncate">
                                <a href="/review/<?php echo $review['slug']; ?>">
                                    <?php echo htmlspecialchars($review['title']); ?>
                                </a>
                            </h4>
                            <div class="flex items-center justify-between text-sm text-gray-500 mt-1">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <?php echo number_format($review['view_count']); ?> ভিউ
                                </span>
                                <div class="flex text-yellow-400">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="<?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?> text-xs">★</span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Categories Section with Enhanced Design -->
<?php if (!empty($categories)): ?>
<section class="py-16 bg-gradient-to-br from-purple-50 to-indigo-100 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-purple-200 rounded-full -translate-y-32 translate-x-32 opacity-30"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-indigo-200 rounded-full translate-y-24 -translate-x-24 opacity-40"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-12">
            <div class="inline-flex items-center bg-purple-100 text-purple-800 px-4 py-2 rounded-full text-sm font-medium mb-4 shadow-sm">
                <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                ক্যাটেগরি
            </div>
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">জনরা অনুযায়ী ব্রাউজ করুন</h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">আপনার পছন্দের ধরণের কন্টেন্ট খুঁজে নিন</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($categories as $category): ?>
                <a href="/category/<?php echo $category['slug']; ?>" 
                   class="group bg-white rounded-xl p-6 text-center hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-200 hover:border-purple-300 relative overflow-hidden">
                    <!-- Hover Effect Background -->
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-blue-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                            <?php if ($category['icon']): ?>
                                <i class="<?php echo $category['icon']; ?> text-2xl text-purple-600"></i>
                            <?php else: ?>
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">
                            <?php echo $category['name_bn'] ?: $category['name']; ?>
                        </h3>
                        <p class="text-gray-500 text-sm mt-1">
                            <?php echo $db->count("SELECT COUNT(*) FROM review_categories rc JOIN reviews r ON rc.review_id = r.id WHERE rc.category_id = ? AND r.status = 'published'", [$category['id']]); ?> রিভিউ
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Newsletter Section with Enhanced Design -->
<section class="py-16 bg-gradient-to-br from-blue-900 to-purple-900 text-white relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full bg-black bg-opacity-20"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600 rounded-full -translate-y-48 translate-x-48 opacity-20"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-purple-600 rounded-full translate-y-40 -translate-x-40 opacity-20"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">সর্বশেষ আপডেট পান</h2>
            <p class="text-xl text-blue-100 mb-8">সাবস্ক্রাইব করুন এবং নতুন রিভিউ ও আপডেট সরাসরি আপনার ইমেইলে পান</p>
            
            <form class="max-w-2xl mx-auto flex flex-col sm:flex-row gap-4">
                <input type="email" 
                       placeholder="আপনার ইমেইল ঠিকানা" 
                       class="flex-1 px-6 py-4 rounded-xl border-0 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg shadow-lg">
                <button type="submit" 
                        class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    সাবস্ক্রাইব করুন
                </button>
            </form>
            
            <p class="text-blue-200 text-sm mt-4">আপনার ইমেইল সুরক্ষিত থাকবে। আমরা স্প্যাম করি না</p>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>