<?php
/**
 * MSR Homepage - Design 1 (Premium Dark Theme)
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

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $seo_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            900: '#1e3a8a',
                        },
                        accent: {
                            400: '#f59e0b',
                            500: '#d97706',
                            600: '#b45309',
                        },
                        dark: {
                            800: '#1f2937',
                            900: '#111827',
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes glow {
            from { box-shadow: 0 0 20px #3b82f6; }
            to { box-shadow: 0 0 30px #3b82f6, 0 0 40px #3b82f6; }
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .poster-aspect {
            aspect-ratio: 1920 / 1280;
        }
    </style>
</head>

<!-- Navigation -->
<nav class="fixed top-0 w-full z-50 bg-gray-900/95 backdrop-blur-md border-b border-gray-800">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            <div class="flex items-center space-x-8">
                <div class="text-3xl font-bold bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                    MSR
                </div>
                <div class="hidden lg:flex space-x-8">
                    <a href="/" class="text-white hover:text-blue-400 font-medium transition-all duration-300 border-b-2 border-blue-500">হোম</a>
                    <a href="/reviews" class="text-gray-300 hover:text-blue-400 font-medium transition-all duration-300 hover:border-b-2 hover:border-blue-500">রিভিউ</a>
                    <a href="/categories" class="text-gray-300 hover:text-blue-400 font-medium transition-all duration-300 hover:border-b-2 hover:border-blue-500">ক্যাটেগরি</a>
                    <a href="/submit-review" class="text-gray-300 hover:text-blue-400 font-medium transition-all duration-300 hover:border-b-2 hover:border-blue-500">জমা দিন</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative hidden md:block">
                    <input type="text" placeholder="সার্চ করুন..." 
                           class="bg-gray-800 text-white px-6 py-3 pl-12 rounded-full w-80 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <button class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-3 rounded-full font-semibold transition-all duration-300 hover:scale-105">
                    লগইন
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="relative min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full opacity-20 animate-float"></div>
        <div class="absolute top-40 right-20 w-24 h-24 bg-gradient-to-r from-pink-500 to-red-500 rounded-full opacity-20 animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-40 left-1/4 w-20 h-20 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-full opacity-20 animate-float" style="animation-delay: 4s;"></div>
        <div class="absolute bottom-20 right-1/3 w-16 h-16 bg-gradient-to-r from-green-500 to-blue-500 rounded-full opacity-20 animate-float" style="animation-delay: 1s;"></div>
    </div>

    <div class="container mx-auto px-4 pt-32 pb-20 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <!-- Left Content -->
            <div class="text-white space-y-8">
                <div class="space-y-6">
                    <div class="inline-flex items-center glass-effect rounded-full px-6 py-3 text-sm font-medium">
                        <div class="w-3 h-3 bg-green-400 rounded-full mr-3 animate-pulse"></div>
                        🇧🇩 বাংলাদেশের প্রিমিয়াম রিভিউ প্ল্যাটফর্ম
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-bold leading-tight">
                        <span class="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                            সিনেমা ও সিরিয়ালের
                        </span>
                        <br>
                        <span class="text-white">বিশেষজ্ঞ রিভিউ</span>
                    </h1>
                    
                    <p class="text-xl lg:text-2xl text-gray-300 leading-relaxed">
                        বিশ্বমানের কন্টেন্টের গভীর বিশ্লেষণ, প্রফেশনাল রেটিং এবং 
                        <span class="text-blue-400 font-semibold">এক্সক্লুসিভ</span> রিভিউ পড়ুন
                    </p>
                </div>
                
                <!-- Enhanced Search -->
                <div class="relative">
                    <form action="/search" method="GET" class="relative">
                        <input type="text" name="q" placeholder="আপনার পছন্দের সিনেমা বা সিরিয়াল খুঁজুন..." 
                               class="w-full px-8 py-5 pr-16 text-lg bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white/20 transition-all text-white placeholder-gray-300">
                        <button type="submit" 
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white p-3 rounded-xl transition-all hover:scale-105">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/reviews" 
                       class="group bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-4 rounded-2xl font-semibold text-lg transition-all duration-300 hover:scale-105 hover:shadow-2xl text-center relative overflow-hidden">
                        <span class="relative z-10">🎬 সব রিভিউ দেখুন</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-purple-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                    <a href="/submit-review" 
                       class="group glass-effect hover:bg-white/20 text-white px-8 py-4 rounded-2xl font-semibold text-lg transition-all duration-300 hover:scale-105 text-center">
                        <span class="group-hover:text-blue-300 transition-colors">✍️ রিভিউ লিখুন</span>
                    </a>
                </div>
                
                <!-- Premium Stats -->
                <div class="grid grid-cols-3 gap-8 pt-8">
                    <div class="text-center group">
                        <div class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent group-hover:scale-110 transition-transform duration-300">
                            <?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?>+
                        </div>
                        <div class="text-gray-400 text-sm mt-2">প্রিমিয়াম রিভিউ</div>
                    </div>
                    <div class="text-center group">
                        <div class="text-4xl font-bold bg-gradient-to-r from-pink-400 to-red-500 bg-clip-text text-transparent group-hover:scale-110 transition-transform duration-300">
                            <?php echo $db->count("SELECT COUNT(*) FROM categories WHERE status = 'active'"); ?>+
                        </div>
                        <div class="text-gray-400 text-sm mt-2">ক্যাটেগরি</div>
                    </div>
                    <div class="text-center group">
                        <div class="text-4xl font-bold bg-gradient-to-r from-green-400 to-blue-500 bg-clip-text text-transparent group-hover:scale-110 transition-transform duration-300">
                            <?php echo number_format($db->count("SELECT SUM(view_count) FROM reviews WHERE status = 'published'") / 1000, 1); ?>K+
                        </div>
                        <div class="text-gray-400 text-sm mt-2">ভিউয়ার</div>
                    </div>
                </div>
            </div>
            
            <!-- Right Content - Featured Reviews Showcase -->
            <div class="relative">
                <?php if (!empty($featured_reviews)): ?>
                    <div class="relative space-y-6">
                        <?php foreach (array_slice($featured_reviews, 0, 3) as $index => $review): ?>
                            <div class="group relative glass-effect rounded-3xl p-6 hover:bg-white/20 transition-all duration-500 transform hover:scale-105" 
                                 style="animation: fadeInUp 1s ease-out <?php echo $index * 0.3; ?>s both;">
                                <div class="flex space-x-6">
                                    <!-- Poster -->
                                    <div class="relative w-24 h-36 flex-shrink-0 rounded-xl overflow-hidden">
                                        <?php if ($review['poster_image']): ?>
                                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        <?php endif; ?>
                                        
                                        <!-- Glow Effect -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-blue-600/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        
                                        <!-- Rating Badge -->
                                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-black font-bold text-xs">
                                            <?php echo $review['rating']; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="flex-1 space-y-3">
                                        <div class="flex items-center space-x-3">
                                            <span class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-3 py-1 rounded-full text-xs font-medium">
                                                <?php echo $review['type'] == 'movie' ? '🎬 সিনেমা' : '📺 সিরিয়াল'; ?>
                                            </span>
                                            <span class="text-gray-400 text-sm"><?php echo $review['year']; ?></span>
                                        </div>
                                        
                                        <h3 class="text-white font-bold text-lg leading-tight group-hover:text-blue-300 transition-colors">
                                            <a href="/review/<?php echo $review['slug']; ?>">
                                                <?php echo htmlspecialchars($review['title']); ?>
                                            </a>
                                        </h3>
                                        
                                        <div class="flex items-center space-x-4">
                                            <div class="flex text-yellow-400 text-sm">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <span class="<?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-600'; ?> transition-colors duration-200">★</span>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="text-gray-300 text-sm"><?php echo $review['rating']; ?>/5</span>
                                        </div>
                                        
                                        <p class="text-gray-300 text-sm leading-relaxed">
                                            <?php echo htmlspecialchars(substr($review['excerpt'], 0, 80)); ?>...
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Hover Effect -->
                                <div class="absolute inset-0 border-2 border-transparent group-hover:border-blue-500/50 rounded-3xl transition-all duration-300"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Premium Categories -->
<?php if (!empty($categories)): ?>
<section class="py-20 bg-gray-900 relative">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <div class="inline-flex items-center glass-effect text-blue-400 px-6 py-3 rounded-full text-sm font-medium mb-6">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                </svg>
                প্রিমিয়াম ক্যাটেগরি
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6">
                আপনার <span class="bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">পছন্দের</span> জনরা
            </h2>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                বিশেষভাবে কিউরেটেড ক্যাটেগরি থেকে আপনার পছন্দের কন্টেন্ট খুঁজে নিন
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-6">
            <?php foreach ($categories as $index => $category): ?>
                <a href="/category/<?php echo $category['slug']; ?>" 
                   class="group relative bg-gradient-to-br from-gray-800 to-gray-900 hover:from-blue-600/20 hover:to-purple-600/20 p-6 rounded-2xl text-center transition-all duration-500 hover:scale-110 hover:shadow-2xl"
                   style="animation: fadeInUp 0.6s ease-out <?php echo $index * 0.1; ?>s both;">
                    
                    <!-- Icon -->
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <?php if ($category['icon']): ?>
                            <i class="<?php echo $category['icon']; ?> text-white text-2xl"></i>
                        <?php else: ?>
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Content -->
                    <h3 class="text-white font-semibold text-sm mb-2 group-hover:text-blue-300 transition-colors">
                        <?php echo $category['name_bn'] ?: $category['name']; ?>
                    </h3>
                    <p class="text-gray-400 text-xs">
                        <?php echo $db->count("SELECT COUNT(*) FROM review_categories rc JOIN reviews r ON rc.review_id = r.id WHERE rc.category_id = ? AND r.status = 'published'", [$category['id']]); ?> রিভিউ
                    </p>
                    
                    <!-- Hover Border -->
                    <div class="absolute inset-0 border-2 border-transparent group-hover:border-blue-500/50 rounded-2xl transition-all duration-300"></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Reviews with Premium Layout -->
<?php if (!empty($latest_reviews)): ?>
<section class="py-20 bg-black relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-16">
            <div class="inline-flex items-center glass-effect text-green-400 px-6 py-3 rounded-full text-sm font-medium mb-6">
                <div class="w-3 h-3 bg-green-400 rounded-full mr-3 animate-pulse"></div>
                সর্বশেষ আপডেট
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6">
                <span class="bg-gradient-to-r from-green-400 to-blue-500 bg-clip-text text-transparent">নতুন</span> রিভিউগুলো
            </h2>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($latest_reviews as $index => $review): ?>
                <div class="group relative glass-effect rounded-3xl overflow-hidden hover:bg-white/10 transition-all duration-500 hover:scale-105"
                     style="animation: fadeInUp 0.8s ease-out <?php echo $index * 0.2; ?>s both;">
                    
                    <!-- Poster with proper 1920x1280 aspect ratio -->
                    <div class="relative poster-aspect overflow-hidden">
                        <?php if ($review['poster_image']): ?>
                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                 alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <?php endif; ?>
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                        
                        <!-- Type Badge -->
                        <div class="absolute top-6 left-6 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-medium">
                            <?php echo $review['type'] == 'movie' ? '🎬 সিনেমা' : '📺 সিরিয়াল'; ?>
                        </div>
                        
                        <!-- Rating Badge -->
                        <div class="absolute top-6 right-6 bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-3 py-2 rounded-full font-bold text-sm">
                            ★ <?php echo $review['rating']; ?>/5
                        </div>
                        
                        <!-- Play Overlay -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-blue-400 text-sm font-medium">
                                <?php echo $review['category_name_bn'] ?: $review['category_name']; ?>
                            </span>
                            <span class="text-gray-400 text-sm"><?php echo $review['year']; ?></span>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-white mb-3 group-hover:text-blue-300 transition-colors leading-tight">
                            <a href="/review/<?php echo $review['slug']; ?>">
                                <?php echo htmlspecialchars($review['title']); ?>
                            </a>
                        </h3>
                        
                        <p class="text-gray-300 text-sm leading-relaxed mb-6">
                            <?php echo htmlspecialchars(substr($review['excerpt'], 0, 120)); ?>...
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex text-yellow-400">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="<?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-600'; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-gray-400 text-sm"><?php echo date('M d', strtotime($review['created_at'])); ?></span>
                            </div>
                            
                            <a href="/review/<?php echo $review['slug']; ?>" 
                               class="inline-flex items-center text-blue-400 hover:text-blue-300 font-medium transition-colors">
                                পড়ুন
                                <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Popular & CTA Section -->
<section class="py-20 bg-gradient-to-br from-gray-900 via-black to-gray-900">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <!-- Popular Reviews -->
            <?php if (!empty($popular_reviews)): ?>
            <div class="space-y-8">
                <div>
                    <div class="inline-flex items-center glass-effect text-red-400 px-6 py-3 rounded-full text-sm font-medium mb-6">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        ট্রেন্ডিং রিভিউ
                    </div>
                    <h2 class="text-4xl font-bold text-white mb-8">
                        <span class="bg-gradient-to-r from-red-400 to-pink-500 bg-clip-text text-transparent">জনপ্রিয়</span> রিভিউগুলো
                    </h2>
                </div>
                
                <div class="space-y-4">
                    <?php foreach ($popular_reviews as $index => $review): ?>
                        <div class="group glass-effect rounded-2xl p-6 hover:bg-white/20 transition-all duration-300">
                            <div class="flex items-center space-x-4">
                                <!-- Rank -->
                                <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                                    <?php echo $index + 1; ?>
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1">
                                    <h4 class="font-bold text-white text-lg group-hover:text-blue-300 transition-colors">
                                        <a href="/review/<?php echo $review['slug']; ?>">
                                            <?php echo htmlspecialchars($review['title']); ?>
                                        </a>
                                    </h4>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-gray-400 text-sm">
                                            👁️ <?php echo number_format($review['view_count']); ?> ভিউ
                                        </span>
                                        <div class="flex text-yellow-400">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="<?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-600'; ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Premium CTA -->
            <div class="relative">
                <div class="glass-effect rounded-3xl p-12 text-center relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
                    
                    <div class="relative z-10 space-y-6">
                        <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </div>
                        
                        <h3 class="text-3xl font-bold text-white mb-4">
                            <span class="bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                                আপনার রিভিউ
                            </span>
                            <br>শেয়ার করুন
                        </h3>
                        
                        <p class="text-gray-300 text-lg leading-relaxed mb-8">
                            আমাদের প্রিমিয়াম কমিউনিটির অংশ হয়ে আপনার মতামত শেয়ার করুন। 
                            পেশাদার রিভিউয়ার হিসেবে নিজেকে প্রতিষ্ঠিত করুন।
                        </p>
                        
                        <div class="space-y-4">
                            <a href="/submit-review" 
                               class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-10 py-4 rounded-2xl font-bold text-lg transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                                🚀 এখনই শুরু করুন
                            </a>
                            
                            <div class="flex items-center justify-center space-x-6 text-sm text-gray-400">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    সম্পূর্ণ বিনামূল্যে
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    সহজ প্রক্রিয়া
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    তাৎক্ষণিক প্রকাশ
                                </span>
                            </div>
                        </div>
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