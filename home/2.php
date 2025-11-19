<?php
/**
 * MSR Homepage - Design 2 (Magazine-Style)
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

<!-- Top Bar -->
<div class="bg-gray-900 text-white py-2">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center text-sm">
            <div class="flex items-center space-x-4">
                <span>📺 সর্বশেষ আপডেট পান</span>
                <span class="hidden md:block">•</span>
                <span class="hidden md:block">দৈনিক নতুন রিভিউ</span>
            </div>
            <div class="flex items-center space-x-4">
                <span><?php echo date('l, F j, Y'); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Header -->
<header class="bg-white shadow-sm border-b-4 border-blue-600">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-6">
            <!-- Logo -->
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900">MSR</h1>
                <p class="text-sm text-gray-600 uppercase tracking-wider">Movie & Series Review</p>
            </div>
            
            <!-- Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="/" class="text-gray-700 hover:text-blue-600 font-medium border-b-2 border-blue-600 pb-1">হোম</a>
                <a href="/reviews" class="text-gray-700 hover:text-blue-600 font-medium hover:border-b-2 hover:border-blue-600 pb-1 transition-all">রিভিউ</a>
                <a href="/categories" class="text-gray-700 hover:text-blue-600 font-medium hover:border-b-2 hover:border-blue-600 pb-1 transition-all">ক্যাটেগরি</a>
                <a href="/submit-review" class="text-gray-700 hover:text-blue-600 font-medium hover:border-b-2 hover:border-blue-600 pb-1 transition-all">জমা দিন</a>
            </nav>
            
            <!-- Search -->
            <div class="relative">
                <input type="text" placeholder="সার্চ..." class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Hero Section -->
<?php if (!empty($featured_reviews) && count($featured_reviews) > 0): ?>
<section class="bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Featured Article -->
            <div class="lg:col-span-2">
                <?php $main_review = $featured_reviews[0]; ?>
                <div class="relative bg-white rounded-lg overflow-hidden shadow-lg group">
                    <div class="aspect-w-16 aspect-h-9">
                        <?php if ($main_review['poster_image']): ?>
                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $main_review['poster_image']; ?>" 
                                 alt="<?php echo htmlspecialchars($main_review['title']); ?>" 
                                 class="w-full h-96 object-cover group-hover:scale-105 transition-transform duration-500">
                        <?php endif; ?>
                    </div>
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                    
                    <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                        <div class="flex items-center space-x-4 mb-4">
                            <span class="bg-red-600 px-3 py-1 rounded text-sm font-medium">ফিচার্ড</span>
                            <span class="text-sm opacity-90"><?php echo $main_review['category_name_bn'] ?: $main_review['category_name']; ?></span>
                            <span class="text-sm opacity-90">•</span>
                            <span class="text-sm opacity-90"><?php echo $main_review['year']; ?></span>
                        </div>
                        
                        <h2 class="text-3xl md:text-4xl font-bold mb-4 leading-tight">
                            <a href="/review/<?php echo $main_review['slug']; ?>" class="hover:text-blue-300 transition-colors">
                                <?php echo htmlspecialchars($main_review['title']); ?>
                            </a>
                        </h2>
                        
                        <p class="text-lg opacity-90 mb-4 leading-relaxed">
                            <?php echo htmlspecialchars(substr($main_review['excerpt'], 0, 200)); ?>...
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex text-yellow-400">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="<?php echo $i <= $main_review['rating'] ? 'text-yellow-400' : 'text-gray-400'; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-sm"><?php echo $main_review['rating']; ?>/5</span>
                            </div>
                            <a href="/review/<?php echo $main_review['slug']; ?>" 
                               class="bg-white text-gray-900 px-6 py-2 rounded-full font-medium hover:bg-gray-200 transition-colors">
                                পড়ুন
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Side Articles -->
            <div class="space-y-6">
                <h3 class="text-xl font-bold text-gray-900 border-b-2 border-blue-600 pb-2">এই সপ্তাহের হাইলাইট</h3>
                
                <?php foreach (array_slice($featured_reviews, 1, 2) as $review): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="flex">
                            <div class="w-32 h-24 bg-gray-200 flex-shrink-0">
                                <?php if ($review['poster_image']): ?>
                                    <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                         class="w-full h-full object-cover">
                                <?php endif; ?>
                            </div>
                            <div class="p-4 flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                        <?php echo $review['type'] == 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?>
                                    </span>
                                    <div class="flex text-yellow-400 text-sm">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <h4 class="font-bold text-gray-900 mb-2 leading-tight hover:text-blue-600 transition-colors">
                                    <a href="/review/<?php echo $review['slug']; ?>">
                                        <?php echo htmlspecialchars($review['title']); ?>
                                    </a>
                                </h4>
                                <p class="text-gray-600 text-sm">
                                    <?php echo htmlspecialchars(substr($review['excerpt'], 0, 80)); ?>...
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Stats Box -->
                <div class="bg-blue-600 text-white rounded-lg p-6 text-center">
                    <h4 class="font-bold mb-4">আমাদের পরিসংখ্যান</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-2xl font-bold"><?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?>+</div>
                            <div>রিভিউ</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold"><?php echo number_format($db->count("SELECT SUM(view_count) FROM reviews WHERE status = 'published'") / 1000, 1); ?>K+</div>
                            <div>ভিউ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Categories Strip -->
<?php if (!empty($categories)): ?>
<section class="bg-white py-6 border-b border-gray-200">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">ক্যাটেগরি অনুযায়ী ব্রাউজ করুন</h3>
            <a href="/categories" class="text-blue-600 hover:text-blue-700 text-sm font-medium">সব দেখুন →</a>
        </div>
        
        <div class="flex space-x-4 overflow-x-auto pb-2">
            <?php foreach ($categories as $category): ?>
                <a href="/category/<?php echo $category['slug']; ?>" 
                   class="flex-shrink-0 bg-gray-100 hover:bg-blue-100 px-4 py-2 rounded-full text-sm font-medium text-gray-700 hover:text-blue-600 transition-all border border-transparent hover:border-blue-200">
                    <?php echo $category['name_bn'] ?: $category['name']; ?>
                    <span class="ml-2 text-xs text-gray-500">
                        (<?php echo $db->count("SELECT COUNT(*) FROM review_categories rc JOIN reviews r ON rc.review_id = r.id WHERE rc.category_id = ? AND r.status = 'published'", [$category['id']]); ?>)
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Main Content Area -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Main Column -->
            <div class="lg:col-span-2 space-y-12">
                <!-- Latest Reviews -->
                <?php if (!empty($latest_reviews)): ?>
                <div>
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 border-b-3 border-blue-600 pb-2">সর্বশেষ রিভিউ</h2>
                        <a href="/reviews" class="text-blue-600 hover:text-blue-700 font-medium">আরও দেখুন →</a>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-8">
                        <?php foreach ($latest_reviews as $review): ?>
                            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow group">
                                <div class="relative">
                                    <div class="aspect-w-16 aspect-h-9">
                                        <?php if ($review['poster_image']): ?>
                                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="absolute top-4 left-4 bg-black/70 text-white px-3 py-1 rounded text-sm">
                                        <?php echo $review['type'] == 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?>
                                    </div>
                                </div>
                                
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-blue-600 text-sm font-medium">
                                            <?php echo $review['category_name_bn'] ?: $review['category_name']; ?>
                                        </span>
                                        <div class="flex text-yellow-400 text-sm">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="<?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    
                                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                        <a href="/review/<?php echo $review['slug']; ?>">
                                            <?php echo htmlspecialchars($review['title']); ?>
                                        </a>
                                    </h3>
                                    
                                    <div class="flex items-center text-gray-500 text-sm mb-3">
                                        <span><?php echo $review['year']; ?></span>
                                        <span class="mx-2">•</span>
                                        <span><?php echo $review['language']; ?></span>
                                        <span class="mx-2">•</span>
                                        <span><?php echo date('M d', strtotime($review['created_at'])); ?></span>
                                    </div>
                                    
                                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                        <?php echo htmlspecialchars(substr($review['excerpt'], 0, 120)); ?>...
                                    </p>
                                    
                                    <a href="/review/<?php echo $review['slug']; ?>" 
                                       class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                                        সম্পূর্ণ পড়ুন
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Popular Reviews -->
                <?php if (!empty($popular_reviews)): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 border-b border-gray-200 pb-2">জনপ্রিয় রিভিউ</h3>
                    
                    <div class="space-y-4">
                        <?php foreach ($popular_reviews as $index => $review): ?>
                            <div class="flex items-start space-x-3 pb-4 <?php echo $index < count($popular_reviews) - 1 ? 'border-b border-gray-100' : ''; ?>">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    <?php echo $index + 1; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 mb-1 hover:text-blue-600 transition-colors">
                                        <a href="/review/<?php echo $review['slug']; ?>">
                                            <?php echo htmlspecialchars($review['title']); ?>
                                        </a>
                                    </h4>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500"><?php echo number_format($review['view_count']); ?> ভিউ</span>
                                        <div class="flex text-yellow-400">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="<?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Newsletter -->
                <div class="bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg p-6 text-white text-center">
                    <h3 class="text-xl font-bold mb-3">সাবস্ক্রাইব করুন</h3>
                    <p class="text-blue-100 text-sm mb-4">সর্বশেষ রিভিউ ও আপডেট সরাসরি ইনবক্সে পান</p>
                    <div class="space-y-3">
                        <input type="email" placeholder="আপনার ইমেইল ঠিকানা" class="w-full px-4 py-2 rounded text-gray-900 text-sm">
                        <button class="w-full bg-white text-blue-600 py-2 rounded font-medium hover:bg-gray-100 transition-colors">
                            সাবস্ক্রাইব করুন
                        </button>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 border-b border-gray-200 pb-2">দ্রুত লিঙ্ক</h3>
                    <div class="space-y-3">
                        <a href="/reviews?type=movie" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 p-2 rounded transition-all">📽️ সিনেমা রিভিউ</a>
                        <a href="/reviews?type=series" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 p-2 rounded transition-all">📺 সিরিয়াল রিভিউ</a>
                        <a href="/reviews?language=bangla" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 p-2 rounded transition-all">🇧🇩 বাংলা কন্টেন্ট</a>
                        <a href="/reviews?language=korean" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 p-2 rounded transition-all">🇰🇷 কোরিয়ান কন্টেন্ট</a>
                        <a href="/submit-review" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 p-2 rounded transition-all">✍️ রিভিউ জমা দিন</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-16 bg-gray-900 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">আমাদের কমিউনিটিতে যোগ দিন</h2>
        <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
            বাংলাদেশের সবচেয়ে বড় সিনেমা ও সিরিয়াল রিভিউ প্ল্যাটফর্মে আপনার মতামত শেয়ার করুন
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/submit-review" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                রিভিউ লিখুন
            </a>
            <a href="/reviews" 
               class="border border-gray-600 hover:border-white text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                সব রিভিউ দেখুন
            </a>
        </div>
    </div>
</section>

<style>
.border-b-3 {
    border-bottom-width: 3px;
}

article:hover img {
    transform: scale(1.05);
}

.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}
</style>

<?php
// Include footer
include 'includes/footer.php';
?>