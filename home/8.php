<?php
/**
 * MSR Homepage - Design 2 (Vibrant Modern Theme)
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
                        neon: {
                            pink: '#ff0080',
                            blue: '#0080ff',
                            green: '#00ff80',
                            yellow: '#ffff00',
                            purple: '#8000ff',
                            cyan: '#00ffff',
                        },
                        dark: {
                            800: '#1a1a2e',
                            900: '#16213e',
                        }
                    },
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                        'pulse-slow': 'pulse 4s infinite',
                        'spin-slow': 'spin 8s linear infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'slide-up': 'slideUp 0.8s ease-out forwards',
                        'neon-pulse': 'neonPulse 2s ease-in-out infinite alternate',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes glow {
            from { box-shadow: 0 0 20px #ff0080, 0 0 30px #ff0080, 0 0 40px #ff0080; }
            to { box-shadow: 0 0 30px #ff0080, 0 0 40px #ff0080, 0 0 50px #ff0080; }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes neonPulse {
            from { text-shadow: 0 0 10px #ff0080, 0 0 20px #ff0080, 0 0 30px #ff0080; }
            to { text-shadow: 0 0 20px #ff0080, 0 0 30px #ff0080, 0 0 40px #ff0080; }
        }
        .neon-border {
            border: 2px solid #ff0080;
            box-shadow: 0 0 20px #ff008050, inset 0 0 20px #ff008020;
        }
        .poster-aspect {
            aspect-ratio: 1920 / 1280;
        }
        .glass-morphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .gradient-text {
            background: linear-gradient(45deg, #ff0080, #00ffff, #ff0080);
            background-size: 200% 200%;
            animation: gradient-shift 3s ease-in-out infinite;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
    </style>
</head>

<!-- Top Bar -->
<div class="bg-gradient-to-r from-neon-pink via-neon-purple to-neon-blue text-white py-2 text-center">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center text-sm">
            <div class="animate-pulse">🔥 আজকের হট রিভিউ দেখুন</div>
            <div class="hidden md:flex items-center space-x-4">
                <span>📅 <?php echo date('F j, Y'); ?></span>
                <span>•</span>
                <span>⭐ প্রিমিয়াম কন্টেন্ট</span>
            </div>
            <div class="animate-bounce">💎 বিশেষ অফার</div>
        </div>
    </div>
</div>

<!-- Navigation -->
<nav class="sticky top-0 z-50 glass-morphism border-b border-white/20">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <div class="flex items-center space-x-4">
                <div class="text-4xl font-black gradient-text">
                    MSR
                </div>
                <div class="hidden lg:block text-white/80 text-sm">
                    Movie & Series Review
                </div>
            </div>
            
            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="/" class="text-white font-semibold hover:text-neon-pink transition-all duration-300 relative group">
                    🏠 হোম
                    <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-neon-pink group-hover:w-full transition-all duration-300"></div>
                </a>
                <a href="/reviews" class="text-white/80 hover:text-neon-cyan font-medium transition-all duration-300 relative group">
                    🎬 রিভিউ
                    <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-neon-cyan group-hover:w-full transition-all duration-300"></div>
                </a>
                <a href="/categories" class="text-white/80 hover:text-neon-green font-medium transition-all duration-300 relative group">
                    📂 ক্যাটেগরি
                    <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-neon-green group-hover:w-full transition-all duration-300"></div>
                </a>
                <a href="/submit-review" class="text-white/80 hover:text-neon-yellow font-medium transition-all duration-300 relative group">
                    ✍️ জমা দিন
                    <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-neon-yellow group-hover:w-full transition-all duration-300"></div>
                </a>
            </div>
            
            <!-- Search & CTA -->
            <div class="flex items-center space-x-4">
                <div class="relative hidden lg:block">
                    <input type="text" placeholder="সার্চ করুন..." 
                           class="bg-white/10 backdrop-blur-sm text-white px-6 py-3 pl-12 rounded-full w-80 focus:outline-none focus:ring-2 focus:ring-neon-pink transition-all placeholder-white/60">
                    <svg class="w-5 h-5 text-white/60 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <button class="bg-gradient-to-r from-neon-pink to-neon-purple hover:from-neon-purple hover:to-neon-pink text-white px-6 py-3 rounded-full font-bold transition-all duration-300 hover:scale-105 hover:shadow-lg">
                    Join Now
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="relative min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-pink-900 overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0">
        <div class="absolute top-20 left-10 w-32 h-32 bg-neon-pink/30 rounded-full animate-float blur-xl"></div>
        <div class="absolute top-40 right-20 w-24 h-24 bg-neon-cyan/30 rounded-full animate-bounce-slow blur-xl"></div>
        <div class="absolute bottom-40 left-1/4 w-20 h-20 bg-neon-green/30 rounded-full animate-pulse-slow blur-xl"></div>
        <div class="absolute bottom-20 right-1/3 w-28 h-28 bg-neon-yellow/30 rounded-full animate-spin-slow blur-xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-neon-purple/20 rounded-full animate-pulse blur-3xl"></div>
    </div>

    <?php if (!empty($featured_reviews) && count($featured_reviews) > 0): ?>
        <?php $main_review = $featured_reviews[0]; ?>
        <!-- Main Hero Content -->
        <div class="container mx-auto px-4 pt-20 pb-16 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <!-- Left Content -->
                <div class="space-y-8 animate-slide-up">
                    <div class="space-y-6">
                        <div class="inline-flex items-center glass-morphism rounded-full px-6 py-3 text-white font-medium neon-border">
                            <div class="w-3 h-3 bg-neon-green rounded-full mr-3 animate-pulse"></div>
                            🚀 বাংলার #1 রিভিউ প্ল্যাটফর্ম
                        </div>
                        
                        <h1 class="text-5xl lg:text-8xl font-black leading-tight">
                            <span class="gradient-text animate-neon-pulse">
                                PREMIUM
                            </span>
                            <br>
                            <span class="text-white">
                                রিভিউ
                            </span>
                        </h1>
                        
                        <p class="text-2xl lg:text-3xl text-white/90 leading-relaxed font-light">
                            বিশ্বমানের <span class="text-neon-cyan font-bold">সিনেমা</span> ও 
                            <span class="text-neon-pink font-bold">সিরিয়ালের</span> 
                            এক্সক্লুসিভ বিশ্লেষণ
                        </p>
                    </div>
                    
                    <!-- Enhanced Search -->
                    <div class="relative">
                        <form action="/search" method="GET" class="relative">
                            <input type="text" name="q" placeholder="কী দেখতে চান? এখানে খুঁজুন..." 
                                   class="w-full px-8 py-6 pr-20 text-xl glass-morphism neon-border rounded-3xl focus:outline-none focus:shadow-lg text-white placeholder-white/60 transition-all duration-300">
                            <button type="submit" 
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-neon-pink to-neon-purple hover:from-neon-purple hover:to-neon-pink text-white p-4 rounded-2xl transition-all duration-300 hover:scale-110 hover:rotate-6">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6">
                        <a href="/reviews" 
                           class="group bg-gradient-to-r from-neon-pink to-neon-purple hover:from-neon-purple hover:to-neon-pink text-white px-10 py-5 rounded-2xl font-bold text-xl transition-all duration-300 hover:scale-110 hover:shadow-2xl text-center relative overflow-hidden">
                            <span class="relative z-10">🎬 সব রিভিউ এক্সপ্লোর করুন</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-neon-cyan to-neon-green opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        </a>
                        <a href="/submit-review" 
                           class="group glass-morphism neon-border hover:bg-white/20 text-white px-10 py-5 rounded-2xl font-bold text-xl transition-all duration-300 hover:scale-110 text-center">
                            <span class="group-hover:text-neon-yellow transition-colors duration-300">✨ আপনার রিভিউ শেয়ার করুন</span>
                        </a>
                    </div>
                    
                    <!-- Vibrant Stats -->
                    <div class="grid grid-cols-3 gap-8 pt-8">
                        <div class="text-center group cursor-pointer">
                            <div class="text-5xl font-black text-neon-pink group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">
                                <?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?>+
                            </div>
                            <div class="text-white/80 text-sm mt-2 group-hover:text-neon-pink transition-colors">এক্সক্লুসিভ রিভিউ</div>
                        </div>
                        <div class="text-center group cursor-pointer">
                            <div class="text-5xl font-black text-neon-cyan group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">
                                <?php echo $db->count("SELECT COUNT(*) FROM categories WHERE status = 'active'"); ?>+
                            </div>
                            <div class="text-white/80 text-sm mt-2 group-hover:text-neon-cyan transition-colors">ক্যাটেগরি</div>
                        </div>
                        <div class="text-center group cursor-pointer">
                            <div class="text-5xl font-black text-neon-green group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">
                                <?php echo number_format($db->count("SELECT SUM(view_count) FROM reviews WHERE status = 'published'") / 1000, 1); ?>K+
                            </div>
                            <div class="text-white/80 text-sm mt-2 group-hover:text-neon-green transition-colors">হ্যাপি ভিউয়ার</div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Content - Featured Review Showcase -->
                <div class="relative animate-slide-up" style="animation-delay: 0.3s;">
                    <div class="relative group">
                        <!-- Main Poster -->
                        <div class="relative poster-aspect rounded-3xl overflow-hidden neon-border shadow-2xl">
                            <?php if ($main_review['poster_image']): ?>
                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $main_review['poster_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($main_review['title']); ?>" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <?php endif; ?>
                            
                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent"></div>
                            
                            <!-- Floating Elements -->
                            <div class="absolute top-6 left-6 bg-gradient-to-r from-neon-pink to-neon-purple text-white px-4 py-2 rounded-full font-bold text-sm animate-bounce">
                                ⭐ FEATURED
                            </div>
                            
                            <div class="absolute top-6 right-6 bg-gradient-to-r from-neon-yellow to-neon-green text-black px-4 py-2 rounded-full font-black text-sm">
                                🏆 <?php echo $main_review['rating']; ?>/5
                            </div>
                            
                            <!-- Play Button -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500">
                                <div class="w-24 h-24 glass-morphism neon-border rounded-full flex items-center justify-center group-hover:scale-125 group-hover:rotate-12 transition-all duration-500">
                                    <svg class="w-12 h-12 text-neon-pink ml-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Content Overlay -->
                            <div class="absolute bottom-0 left-0 right-0 p-8">
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-3">
                                        <span class="bg-gradient-to-r from-neon-cyan to-neon-blue text-white px-3 py-1 rounded-full text-sm font-medium">
                                            <?php echo $main_review['type'] == 'movie' ? '🎬 সিনেমা' : '📺 সিরিয়াল'; ?>
                                        </span>
                                        <span class="text-white/80"><?php echo $main_review['year']; ?></span>
                                    </div>
                                    
                                    <h3 class="text-3xl font-bold text-white group-hover:text-neon-pink transition-colors duration-300">
                                        <a href="/review/<?php echo $main_review['slug']; ?>">
                                            <?php echo htmlspecialchars($main_review['title']); ?>
                                        </a>
                                    </h3>
                                    
                                    <p class="text-white/90 text-lg leading-relaxed">
                                        <?php echo htmlspecialchars(substr($main_review['excerpt'], 0, 150)); ?>...
                                    </p>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex text-neon-yellow text-lg">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="<?php echo $i <= $main_review['rating'] ? 'text-neon-yellow' : 'text-white/30'; ?> animate-pulse" style="animation-delay: <?php echo $i * 0.1; ?>s;">★</span>
                                            <?php endfor; ?>
                                        </div>
                                        <a href="/review/<?php echo $main_review['slug']; ?>" 
                                           class="bg-gradient-to-r from-neon-green to-neon-cyan text-black px-6 py-2 rounded-full font-bold hover:scale-110 transition-transform duration-300">
                                            পড়ুন →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Side Reviews -->
                        <?php if (count($featured_reviews) > 1): ?>
                        <div class="absolute -right-8 top-1/2 transform -translate-y-1/2 space-y-4">
                            <?php foreach (array_slice($featured_reviews, 1, 2) as $index => $review): ?>
                                <div class="w-32 h-48 glass-morphism neon-border rounded-2xl overflow-hidden group hover:scale-110 transition-all duration-300" style="animation-delay: <?php echo 0.6 + ($index * 0.2); ?>s;">
                                    <?php if ($review['poster_image']): ?>
                                        <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                             alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <?php endif; ?>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end p-3">
                                        <div class="text-white text-xs font-bold truncate">
                                            <?php echo htmlspecialchars($review['title']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- Categories with Vibrant Design -->
<?php if (!empty($categories)): ?>
<section class="py-20 bg-gradient-to-br from-black via-purple-900 to-black relative overflow-hidden">
    <!-- Background Animation -->
    <div class="absolute inset-0 opacity-30">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-neon-pink/10 via-transparent to-neon-cyan/10 animate-pulse"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-16">
            <div class="inline-flex items-center glass-morphism text-neon-cyan px-8 py-4 rounded-full text-lg font-bold mb-8 neon-border">
                <svg class="w-6 h-6 mr-3 animate-spin-slow" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                </svg>
                🎯 প্রিমিয়াম ক্যাটেগরি
            </div>
            <h2 class="text-6xl lg:text-8xl font-black text-white mb-8">
                <span class="gradient-text">EXPLORE</span>
                <br>
                <span class="text-neon-green">GENRES</span>
            </h2>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-6">
            <?php foreach ($categories as $index => $category): ?>
                <a href="/category/<?php echo $category['slug']; ?>" 
                   class="group relative glass-morphism hover:bg-white/20 p-6 rounded-3xl text-center transition-all duration-500 hover:scale-125 hover:rotate-6 neon-border hover:shadow-2xl"
                   style="animation: slideUp 0.8s ease-out <?php echo $index * 0.1; ?>s both;">
                    
                    <!-- Icon -->
                    <div class="w-16 h-16 bg-gradient-to-br from-neon-pink via-neon-purple to-neon-cyan rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:animate-bounce relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-neon-green to-neon-yellow opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <?php if ($category['icon']): ?>
                            <i class="<?php echo $category['icon']; ?> text-white text-2xl relative z-10"></i>
                        <?php else: ?>
                            <svg class="w-8 h-8 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Content -->
                    <h3 class="text-white font-bold text-sm mb-2 group-hover:text-neon-pink transition-colors">
                        <?php echo $category['name_bn'] ?: $category['name']; ?>
                    </h3>
                    <p class="text-white/60 text-xs group-hover:text-neon-cyan transition-colors">
                        <?php echo $db->count("SELECT COUNT(*) FROM review_categories rc JOIN reviews r ON rc.review_id = r.id WHERE rc.category_id = ? AND r.status = 'published'", [$category['id']]); ?> রিভিউ
                    </p>
                    
                    <!-- Glow Effect -->
                    <div class="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 animate-glow"></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Reviews with Neon Style -->
<?php if (!empty($latest_reviews)): ?>
<section class="py-20 bg-gradient-to-br from-purple-900 via-black to-blue-900 relative overflow-hidden">
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-16">
            <div class="inline-flex items-center glass-morphism text-neon-green px-8 py-4 rounded-full text-lg font-bold mb-8 neon-border animate-pulse">
                <div class="w-4 h-4 bg-neon-green rounded-full mr-3 animate-bounce"></div>
                🔥 HOT REVIEWS
            </div>
            <h2 class="text-6xl lg:text-8xl font-black text-white mb-8">
                <span class="text-neon-yellow">LATEST</span>
                <br>
                <span class="gradient-text">DROPS</span>
            </h2>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($latest_reviews as $index => $review): ?>
                <div class="group relative glass-morphism rounded-3xl overflow-hidden hover:bg-white/20 transition-all duration-500 hover:scale-105 neon-border"
                     style="animation: slideUp 1s ease-out <?php echo $index * 0.2; ?>s both;">
                    
                    <!-- Poster -->
                    <div class="relative poster-aspect overflow-hidden">
                        <?php if ($review['poster_image']): ?>
                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                 alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <?php endif; ?>
                        
                        <!-- Vibrant Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-purple-900/30 to-transparent"></div>
                        
                        <!-- Type Badge -->
                        <div class="absolute top-6 left-6 bg-gradient-to-r from-neon-pink to-neon-purple text-white px-4 py-2 rounded-full text-sm font-bold animate-bounce">
                            <?php echo $review['type'] == 'movie' ? '🎬' : '📺'; ?> 
                            <?php echo $review['type'] == 'movie' ? 'MOVIE' : 'SERIES'; ?>
                        </div>
                        
                        <!-- Rating Badge -->
                        <div class="absolute top-6 right-6 bg-gradient-to-r from-neon-yellow to-neon-green text-black px-3 py-2 rounded-full font-black text-sm">
                            ⭐ <?php echo $review['rating']; ?>/5
                        </div>
                        
                        <!-- Neon Play Button -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <div class="w-20 h-20 glass-morphism neon-border rounded-full flex items-center justify-center group-hover:scale-125 group-hover:rotate-12 transition-all duration-500 animate-glow">
                                <svg class="w-10 h-10 text-neon-cyan ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-8 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-neon-cyan font-bold text-sm">
                                <?php echo $review['category_name_bn'] ?: $review['category_name']; ?>
                            </span>
                            <span class="text-white/60"><?php echo $review['year']; ?></span>
                        </div>
                        
                        <h3 class="text-2xl font-black text-white group-hover:text-neon-pink transition-colors leading-tight">
                            <a href="/review/<?php echo $review['slug']; ?>">
                                <?php echo htmlspecialchars($review['title']); ?>
                            </a>
                        </h3>
                        
                        <p class="text-white/80 leading-relaxed">
                            <?php echo htmlspecialchars(substr($review['excerpt'], 0, 100)); ?>...
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex text-neon-yellow">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="<?php echo $i <= $review['rating'] ? 'text-neon-yellow' : 'text-white/30'; ?> animate-pulse" style="animation-delay: <?php echo $i * 0.1; ?>s;">★</span>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-white/60 text-sm"><?php echo date('M d', strtotime($review['created_at'])); ?></span>
                            </div>
                            
                            <a href="/review/<?php echo $review['slug']; ?>" 
                               class="bg-gradient-to-r from-neon-green to-neon-cyan text-black px-6 py-2 rounded-full font-bold hover:scale-110 transition-transform duration-300">
                                READ →
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Mega CTA Section -->
<section class="py-20 bg-gradient-to-br from-neon-pink via-neon-purple to-neon-blue relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0">
        <div class="absolute top-20 left-20 w-40 h-40 bg-neon-cyan/20 rounded-full animate-float blur-xl"></div>
        <div class="absolute bottom-20 right-20 w-32 h-32 bg-neon-green/20 rounded-full animate-bounce blur-xl"></div>
        <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-neon-yellow/20 rounded-full animate-spin-slow blur-xl"></div>
    </div>
    
    <div class="container mx-auto px-4 text-center relative z-10">
        <div class="max-w-4xl mx-auto space-y-12">
            <div class="space-y-8">
                <div class="inline-flex items-center glass-morphism text-white px-8 py-4 rounded-full text-lg font-bold mb-8 border border-white/30 animate-bounce">
                    🚀 JOIN THE REVOLUTION
                </div>
                
                <h2 class="text-6xl lg:text-9xl font-black leading-tight">
                    <span class="gradient-text animate-neon-pulse">BE PART OF</span>
                    <br>
                    <span class="text-white">THE FUTURE</span>
                </h2>
                
                <p class="text-2xl lg:text-3xl text-white/90 leading-relaxed font-light max-w-3xl mx-auto">
                    আমাদের <span class="text-neon-cyan font-bold">প্রিমিয়াম</span> কমিউনিটির অংশ হয়ে 
                    <span class="text-neon-pink font-bold">ভবিষ্যতের</span> রিভিউ প্ল্যাটফর্মের সাথে যুক্ত হন
                </p>
            </div>
            
            <!-- Popular Reviews Grid -->
            <?php if (!empty($popular_reviews)): ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <?php foreach ($popular_reviews as $index => $review): ?>
                    <div class="group glass-morphism rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 neon-border">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-gradient-to-r from-neon-pink to-neon-purple text-white rounded-full flex items-center justify-center font-black text-sm">
                                #<?php echo $index + 1; ?>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-white text-sm group-hover:text-neon-cyan transition-colors truncate">
                                    <a href="/review/<?php echo $review['slug']; ?>">
                                        <?php echo htmlspecialchars($review['title']); ?>
                                    </a>
                                </h4>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-white/60 text-xs">
                                        👁️ <?php echo number_format($review['view_count']); ?>
                                    </span>
                                    <div class="flex text-neon-yellow text-xs">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?php echo $i <= $review['rating'] ? 'text-neon-yellow' : 'text-white/30'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Mega CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-8 justify-center">
                <a href="/submit-review" 
                   class="group bg-gradient-to-r from-neon-green to-neon-cyan hover:from-neon-cyan hover:to-neon-green text-black px-12 py-6 rounded-3xl font-black text-2xl transition-all duration-300 hover:scale-110 hover:rotate-3 hover:shadow-2xl relative overflow-hidden">
                    <span class="relative z-10">🔥 START REVIEWING NOW</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-neon-pink to-neon-purple opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                </a>
                <a href="/reviews" 
                   class="group glass-morphism border-2 border-white/30 hover:border-neon-pink text-white px-12 py-6 rounded-3xl font-black text-2xl transition-all duration-300 hover:scale-110 hover:-rotate-3 hover:shadow-2xl">
                    <span class="group-hover:text-neon-pink transition-colors duration-300">✨ EXPLORE REVIEWS</span>
                </a>
            </div>
            
            <!-- Benefits Grid -->
            <div class="grid md:grid-cols-3 gap-8 text-center mt-16">
                <div class="group space-y-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-neon-pink to-neon-purple rounded-2xl flex items-center justify-center mx-auto group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white group-hover:text-neon-pink transition-colors">100% বিনামূল্যে</h3>
                    <p class="text-white/80 text-sm">কোন লুকানো খরচ নেই</p>
                </div>
                <div class="group space-y-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-neon-cyan to-neon-blue rounded-2xl flex items-center justify-center mx-auto group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white group-hover:text-neon-cyan transition-colors">এক্সক্লুসিভ কমিউনিটি</h3>
                    <p class="text-white/80 text-sm">বিশেষজ্ঞদের সাথে যুক্ত হন</p>
                </div>
                <div class="group space-y-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-neon-green to-neon-yellow rounded-2xl flex items-center justify-center mx-auto group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">
                        <svg class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white group-hover:text-neon-green transition-colors">প্রিমিয়াম কন্টেন্ট</h3>
                    <p class="text-white/80 text-sm">সেরা রিভিউ পান প্রতিদিন</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>