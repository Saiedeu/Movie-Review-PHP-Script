<?php
/**
 * MSR Homepage - Design 1: Modern Minimalist
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
    LIMIT 8
");

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

// Get popular reviews
$popular_reviews = $db->fetchAll("
    SELECT r.*, c.name as category_name, c.name_bn as category_name_bn
    FROM reviews r 
    LEFT JOIN review_categories rc ON r.id = rc.review_id
    LEFT JOIN categories c ON rc.category_id = c.id
    WHERE r.status = 'published' 
    ORDER BY r.view_count DESC 
    LIMIT 6
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

// Include header
include 'includes/header.php';
?>

<style>
/* Custom animations for home1 */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
}

.animate-fade-in-left {
    animation: fadeInLeft 0.8s ease-out forwards;
}

.animate-fade-in-right {
    animation: fadeInRight 0.8s ease-out forwards;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.section-spacing {
    padding: 5rem 0;
}

.container-custom {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1rem;
}

@media (min-width: 768px) {
    .container-custom {
        padding: 0 2rem;
    }
}
</style>

<!-- Hero Section - Minimalist -->
<section class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 via-white to-gray-100 overflow-hidden">
    <!-- Subtle background shapes -->
    <div class="absolute inset-0">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-100 rounded-full opacity-30 animate-float" style="animation-delay: 0s;"></div>
        <div class="absolute top-3/4 right-1/4 w-80 h-80 bg-purple-100 rounded-full opacity-30 animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-pink-100 rounded-full opacity-30 animate-float" style="animation-delay: 4s;"></div>
    </div>
    
    <div class="container-custom relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <!-- Content Side -->
            <div class="text-center lg:text-left space-y-8 animate-fade-in-left">
                <!-- Badge -->
                <div class="inline-flex items-center bg-white shadow-lg rounded-full px-6 py-3 text-sm font-medium text-gray-700">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3 animate-pulse"></div>
                    বিশ্বস্ত রিভিউ প্ল্যাটফর্ম
                </div>
                
                <!-- Main Heading -->
                <h1 class="text-5xl lg:text-7xl font-black leading-tight">
                    <span class="text-gray-900">সেরা</span>
                    <br>
                    <span class="gradient-text">সিনেমা ও সিরিয়াল</span>
                    <br>
                    <span class="text-gray-700">রিভিউ</span>
                </h1>
                
                <!-- Subtitle -->
                <p class="text-xl lg:text-2xl text-gray-600 leading-relaxed max-w-2xl">
                    বাংলা ভাষায় বিশ্বের সেরা সিনেমা ও সিরিয়ালের গভীর বিশ্লেষণ, 
                    নিরপেক্ষ রিভিউ এবং দর্শক গাইড।
                </p>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="/reviews" class="group bg-black text-white px-8 py-4 rounded-2xl font-semibold text-lg hover:bg-gray-800 transition-all duration-300 flex items-center justify-center">
                        সকল রিভিউ দেখুন
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                    <a href="/submit-review" class="group border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-2xl font-semibold text-lg hover:border-gray-400 hover:bg-gray-50 transition-all duration-300 flex items-center justify-center">
                        রিভিউ লিখুন
                        <svg class="w-5 h-5 ml-2 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-8 pt-8 max-w-md mx-auto lg:mx-0">
                    <div class="text-center lg:text-left">
                        <div class="text-3xl font-bold text-gray-900">
                            <?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?>+
                        </div>
                        <div class="text-gray-500 text-sm font-medium">রিভিউ</div>
                    </div>
                    <div class="text-center lg:text-left">
                        <div class="text-3xl font-bold text-gray-900">
                            <?php echo $db->count("SELECT COUNT(*) FROM categories WHERE status = 'active'"); ?>+
                        </div>
                        <div class="text-gray-500 text-sm font-medium">ক্যাটেগরি</div>
                    </div>
                    <div class="text-center lg:text-left">
                        <div class="text-3xl font-bold text-gray-900">
                            <?php echo number_format($db->count("SELECT SUM(view_count) FROM reviews WHERE status = 'published'")); ?>+
                        </div>
                        <div class="text-gray-500 text-sm font-medium">ভিউ</div>
                    </div>
                </div>
            </div>
            
            <!-- Visual Side -->
            <div class="relative animate-fade-in-right">
                <div class="relative max-w-lg mx-auto">
                    <!-- Main featured review card -->
                    <?php if (!empty($featured_reviews)): ?>
                        <?php $main_review = $featured_reviews[0]; ?>
                        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden hover-lift">
                            <div class="relative h-80">
                                <?php if ($main_review['poster_image']): ?>
                                    <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $main_review['poster_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($main_review['title']); ?>" 
                                         class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                        <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Rating overlay -->
                                <div class="absolute top-6 right-6 bg-black/50 backdrop-blur-sm text-white px-4 py-2 rounded-full font-semibold">
                                    ★ <?php echo $main_review['rating']; ?>/5
                                </div>
                            </div>
                            
                            <div class="p-8">
                                <div class="flex items-center space-x-2 mb-4">
                                    <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full font-medium">
                                        <?php echo $main_review['category_name_bn'] ?: $main_review['category_name']; ?>
                                    </span>
                                    <span class="text-gray-400">•</span>
                                    <span class="text-gray-600 text-sm"><?php echo $main_review['year']; ?></span>
                                </div>
                                
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">
                                    <?php echo htmlspecialchars($main_review['title']); ?>
                                </h3>
                                
                                <p class="text-gray-600 leading-relaxed mb-6">
                                    <?php echo htmlspecialchars(substr($main_review['excerpt'], 0, 120)); ?>...
                                </p>
                                
                                <a href="/review/<?php echo $main_review['slug']; ?>" 
                                   class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold transition-colors">
                                    সম্পূর্ণ রিভিউ পড়ুন
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Floating mini cards -->
                    <?php if (count($featured_reviews) > 1): ?>
                        <div class="absolute -top-8 -left-12 bg-white rounded-2xl shadow-xl p-4 hover-lift" style="animation-delay: 1s;">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-16 bg-gray-200 rounded-lg overflow-hidden">
                                    <?php if ($featured_reviews[1]['poster_image']): ?>
                                        <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $featured_reviews[1]['poster_image']; ?>" 
                                             alt="<?php echo htmlspecialchars($featured_reviews[1]['title']); ?>" 
                                             class="w-full h-full object-cover">
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-sm">
                                        <?php echo htmlspecialchars(substr($featured_reviews[1]['title'], 0, 20)); ?>...
                                    </h4>
                                    <div class="flex text-yellow-400 text-xs">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?php echo $i <= $featured_reviews[1]['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (count($featured_reviews) > 2): ?>
                        <div class="absolute -bottom-8 -right-12 bg-white rounded-2xl shadow-xl p-4 hover-lift" style="animation-delay: 2s;">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-16 bg-gray-200 rounded-lg overflow-hidden">
                                    <?php if ($featured_reviews[2]['poster_image']): ?>
                                        <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $featured_reviews[2]['poster_image']; ?>" 
                                             alt="<?php echo htmlspecialchars($featured_reviews[2]['title']); ?>" 
                                             class="w-full h-full object-cover">
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-sm">
                                        <?php echo htmlspecialchars(substr($featured_reviews[2]['title'], 0, 20)); ?>...
                                    </h4>
                                    <div class="flex text-yellow-400 text-xs">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?php echo $i <= $featured_reviews[2]['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <div class="w-8 h-12 border-2 border-gray-400 rounded-full flex justify-center">
            <div class="w-1 h-3 bg-gray-400 rounded-full mt-2 animate-pulse"></div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="section-spacing bg-white">
    <div class="container-custom">
        <div class="max-w-4xl mx-auto text-center">
            <!-- Search Form -->
            <div class="relative mb-8">
                <form action="/search" method="GET" class="relative">
                    <input type="text" 
                           name="q" 
                           placeholder="সিনেমা বা সিরিয়ালের নাম লিখুন..." 
                           class="w-full px-8 py-6 text-xl border-2 border-gray-200 rounded-3xl focus:outline-none focus:border-blue-500 transition-all duration-300 shadow-lg">
                    <button type="submit" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-black text-white p-4 rounded-2xl hover:bg-gray-800 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>
            
            <!-- Filter Pills -->
            <div class="flex flex-wrap justify-center gap-3">
                <a href="/reviews" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-full font-medium transition-all">সকল</a>
                <a href="/reviews?type=movie" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-full font-medium transition-all">সিনেমা</a>
                <a href="/reviews?type=series" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-full font-medium transition-all">সিরিয়াল</a>
                <a href="/reviews?language=bangla" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-full font-medium transition-all">বাংলা</a>
                <a href="/reviews?language=korean" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-full font-medium transition-all">কোরিয়ান</a>
                <a href="/reviews?language=hollywood" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-full font-medium transition-all">হলিউড</a>
            </div>
        </div>
    </div>
</section>

<!-- Latest Reviews Section -->
<?php if (!empty($latest_reviews)): ?>
<section class="section-spacing bg-gray-50">
    <div class="container-custom">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-black text-gray-900 mb-6">সর্বশেষ রিভিউ</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                আমাদের সর্বশেষ যোগ করা সিনেমা ও সিরিয়াল রিভিউগুলো দেখুন
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach (array_slice($latest_reviews, 0, 8) as $index => $review): ?>
                <div class="bg-white rounded-3xl overflow-hidden shadow-lg hover-lift group" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                    <div class="relative h-64 overflow-hidden">
                        <?php if ($review['poster_image']): ?>
                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                 alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>
                                </svg>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Rating badge -->
                        <div class="absolute top-4 right-4 bg-black/70 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm font-semibold">
                            ★ <?php echo $review['rating']; ?>/5
                        </div>
                        
                        <!-- Type badge -->
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-gray-900 px-3 py-1 rounded-full text-xs font-medium">
                            <?php echo $review['type'] == 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="font-bold text-gray-900 text-lg mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">
                            <a href="/review/<?php echo $review['slug']; ?>">
                                <?php echo htmlspecialchars($review['title']); ?>
                            </a>
                        </h3>
                        
                        <div class="flex items-center text-gray-500 text-sm mb-3">
                            <span><?php echo $review['year']; ?></span>
                            <span class="mx-2">•</span>
                            <span><?php echo $review['language']; ?></span>
                        </div>
                        
                        <p class="text-gray-600 text-sm leading-relaxed">
                            <?php echo htmlspecialchars(substr($review['excerpt'], 0, 80)); ?>...
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="/reviews" class="inline-flex items-center bg-black text-white px-8 py-4 rounded-2xl font-semibold text-lg hover:bg-gray-800 transition-colors">
                সব রিভিউ দেখুন
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Categories Section -->
<?php if (!empty($categories)): ?>
<section class="section-spacing bg-white">
    <div class="container-custom">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-black text-gray-900 mb-6">ক্যাটেগরি</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                আপনার পছন্দের ধরণের কন্টেন্ট খুঁজে নিন
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
            <?php foreach ($categories as $index => $category): ?>
                <a href="/category/<?php echo $category['slug']; ?>" 
                   class="group bg-gray-50 hover:bg-gray-100 rounded-3xl p-8 text-center transition-all duration-300 hover-lift" 
                   style="animation-delay: <?php echo $index * 0.1; ?>s;">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        <?php if ($category['icon']): ?>
                            <i class="<?php echo $category['icon']; ?> text-2xl text-white"></i>
                        <?php else: ?>
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <h3 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors mb-2">
                        <?php echo $category['name_bn'] ?: $category['name']; ?>
                    </h3>
                    <p class="text-gray-500 text-sm">
                        <?php echo $db->count("SELECT COUNT(*) FROM review_categories rc JOIN reviews r ON rc.review_id = r.id WHERE rc.category_id = ? AND r.status = 'published'", [$category['id']]); ?> টি রিভিউ
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Popular Reviews Section -->
<?php if (!empty($popular_reviews)): ?>
<section class="section-spacing bg-gray-50">
    <div class="container-custom">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-black text-gray-900 mb-6">জনপ্রিয় রিভিউ</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                সবচেয়ে বেশি পড়া হয়েছে এমন রিভিউগুলো
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach (array_slice($popular_reviews, 0, 6) as $index => $review): ?>
                <div class="bg-white rounded-3xl overflow-hidden shadow-lg hover-lift group" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                    <div class="flex">
                        <div class="w-24 h-32 bg-gray-200 flex-shrink-0 overflow-hidden">
                            <?php if ($review['poster_image']): ?>
                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex-1 p-6 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="bg-red-100 text-red-800 text-xs px-3 py-1 rounded-full font-medium">
                                        #{<?php echo $index + 1; ?>} জনপ্রিয়
                                    </span>
                                    <div class="flex text-yellow-400 text-sm">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                
                                <h3 class="font-bold text-gray-900 text-lg mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">
                                    <a href="/review/<?php echo $review['slug']; ?>">
                                        <?php echo htmlspecialchars($review['title']); ?>
                                    </a>
                                </h3>
                                
                                <div class="flex items-center text-gray-500 text-sm">
                                    <span><?php echo number_format($review['view_count']); ?> ভিউ</span>
                                    <span class="mx-2">•</span>
                                    <span><?php echo $review['year']; ?></span>
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

<!-- Call to Action Section -->
<section class="section-spacing bg-gradient-to-r from-gray-900 via-black to-gray-900">
    <div class="container-custom text-center">
        <div class="max-w-4xl mx-auto text-white space-y-8">
            <h2 class="text-4xl lg:text-6xl font-black leading-tight">
                আপনার মতামত <span class="gradient-text">শেয়ার করুন</span>
            </h2>
            <p class="text-xl lg:text-2xl text-gray-300 leading-relaxed">
                দেখেছেন কোনো দুর্দান্ত সিনেমা বা সিরিয়াল? আপনার অভিজ্ঞতা অন্যদের সাথে শেয়ার করুন।
            </p>
            
            <div class="flex flex-col sm:flex-row gap-6 justify-center">
                <a href="/submit-review" 
                   class="bg-white text-black px-8 py-4 rounded-2xl font-bold text-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-105">
                    এখনই রিভিউ জমা দিন
                </a>
                <a href="/about" 
                   class="border-2 border-white/30 hover:border-white text-white hover:bg-white/10 px-8 py-4 rounded-2xl font-bold text-lg transition-all duration-300">
                    আরো জানুন
                </a>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>