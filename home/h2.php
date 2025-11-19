<?php
/**
 * MSR Homepage - Design 1: Cinematic Hero
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
<html lang="bn" class="scroll-smooth">
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
                        primary: '#5D5CDE',
                        secondary: '#EC4899',
                        accent: '#F59E0B',
                        cinema: '#111827',
                        light: '#F8FAFC'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'slide-up': 'slideUp 0.8s ease-out forwards',
                        'fade-in': 'fadeIn 1s ease-out forwards',
                        'zoom-in': 'zoomIn 0.8s ease-out forwards',
                    }
                }
            }
        }

        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            if (event.matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        
        body { font-family: 'Inter', sans-serif; }
        
        .poster-aspect { aspect-ratio: 16 / 9; }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        @keyframes glow {
            from { box-shadow: 0 0 20px rgba(93, 92, 222, 0.3), 0 0 40px rgba(93, 92, 222, 0.1); }
            to { box-shadow: 0 0 30px rgba(93, 92, 222, 0.5), 0 0 60px rgba(93, 92, 222, 0.2); }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .hero-bg {
            background: 
                radial-gradient(circle at 20% 50%, rgba(93, 92, 222, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(236, 72, 153, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(245, 158, 11, 0.2) 0%, transparent 50%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #5D5CDE 0%, #EC4899 50%, #F59E0B 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .hero-card:hover {
            transform: translateY(-10px) rotateX(5deg) rotateY(5deg);
        }
    </style>
</head>
<body class="bg-white dark:bg-cinema text-gray-900 dark:text-white">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/90 dark:bg-cinema/90 backdrop-blur-xl border-b border-gray-200/50 dark:border-gray-800/50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary via-secondary to-accent rounded-2xl flex items-center justify-center shadow-2xl animate-glow">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18,4V3A1,1 0 0,0 17,2H4A1,1 0 0,0 3,3V18L6,16V4H18M21,6H7A1,1 0 0,0 6,7V21A1,1 0 0,0 7,22H21A1,1 0 0,0 22,21V7A1,1 0 0,0 21,6M20,20H8V8H20V20M14.5,10.5V19.5L18.5,15L14.5,10.5Z"/>
                            </svg>
                        </div>
                        <div class="absolute -top-2 -right-2 w-5 h-5 bg-accent rounded-full animate-pulse"></div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gradient">MSR</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Premium Cinema Reviews</p>
                    </div>
                </div>
                
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="/" class="text-primary font-bold relative group">
                        হোম
                        <div class="absolute -bottom-1 left-0 w-full h-0.5 bg-primary transform scale-x-100 group-hover:scale-x-110 transition-transform"></div>
                    </a>
                    <a href="/reviews" class="hover:text-primary transition-colors font-medium group">রিভিউ</a>
                    <a href="/categories" class="hover:text-primary transition-colors font-medium">ক্যাটেগরি</a>
                    <a href="/submit-review" class="hover:text-primary transition-colors font-medium">জমা দিন</a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="সার্চ করুন..." 
                               class="px-6 py-3 pl-12 bg-gray-100/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl border-0 focus:ring-2 focus:ring-primary focus:outline-none transition-all w-80">
                        <svg class="w-5 h-5 absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button class="bg-gradient-to-r from-primary to-secondary text-white px-8 py-3 rounded-2xl font-bold hover:shadow-xl hover:scale-105 transition-all duration-300">
                        যোগ দিন
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- EPIC HERO SECTION -->
    <section class="relative min-h-screen pt-24 pb-16 overflow-hidden hero-bg">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute top-20 left-20 w-64 h-64 bg-primary/20 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-20 right-20 w-48 h-48 bg-secondary/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/4 w-32 h-32 bg-accent/20 rounded-full blur-2xl animate-float" style="animation-delay: 4s;"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <?php if (!empty($featured_reviews)): ?>
                <?php $hero_review = $featured_reviews[0]; ?>
                
                <!-- Hero Content Grid -->
                <div class="grid lg:grid-cols-5 gap-12 items-center min-h-[80vh]">
                    
                    <!-- Left Content -->
                    <div class="lg:col-span-3 space-y-10 animate-slide-up">
                        <!-- Badge -->
                        <div class="inline-flex items-center glass-effect rounded-full px-8 py-4 text-white font-semibold shadow-2xl">
                            <div class="w-3 h-3 bg-accent rounded-full mr-3 animate-pulse"></div>
                            🏆 এক্সক্লুসিভ ফিচার্ড রিভিউ
                        </div>
                        
                        <!-- Main Title -->
                        <div class="space-y-6">
                            <h1 class="text-6xl lg:text-8xl font-black leading-none">
                                <span class="text-gradient">
                                    <?php echo htmlspecialchars($hero_review['title']); ?>
                                </span>
                            </h1>
                            
                            <div class="flex items-center space-x-6 text-xl">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center">
                                        <span class="text-black font-bold text-sm">★</span>
                                    </div>
                                    <span class="font-bold text-2xl"><?php echo $hero_review['rating']; ?>/5</span>
                                </div>
                                <span class="text-gray-400">•</span>
                                <span class="font-semibold"><?php echo $hero_review['year']; ?></span>
                                <span class="text-gray-400">•</span>
                                <span class="bg-primary/20 text-primary px-4 py-2 rounded-full font-semibold">
                                    <?php echo $hero_review['type'] == 'movie' ? '🎬 সিনেমা' : '📺 সিরিয়াল'; ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <p class="text-xl lg:text-2xl text-gray-600 dark:text-gray-300 leading-relaxed max-w-3xl">
                            <?php echo htmlspecialchars(substr($hero_review['excerpt'], 0, 200)); ?>...
                        </p>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-6">
                            <a href="/review/<?php echo $hero_review['slug']; ?>" 
                               class="group bg-gradient-to-r from-primary via-secondary to-accent text-white px-12 py-5 rounded-2xl font-bold text-xl hover:shadow-2xl hover:scale-105 transition-all duration-500 flex items-center justify-center">
                                <svg class="w-6 h-6 mr-3 group-hover:scale-125 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                সম্পূর্ণ রিভিউ পড়ুন
                            </a>
                            <a href="/reviews" 
                               class="group glass-effect text-white px-12 py-5 rounded-2xl font-bold text-xl hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center justify-center">
                                <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                আরো রিভিউ দেখুন
                            </a>
                        </div>
                        
                        <!-- Stats -->
                        <div class="flex items-center space-x-12 pt-8">
                            <div class="text-center group cursor-pointer">
                                <div class="text-4xl font-black text-primary group-hover:scale-125 transition-transform">
                                    <?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?>+
                                </div>
                                <div class="text-gray-500 mt-2 font-medium">মোট রিভিউ</div>
                            </div>
                            <div class="text-center group cursor-pointer">
                                <div class="text-4xl font-black text-secondary group-hover:scale-125 transition-transform">
                                    <?php echo number_format($db->count("SELECT SUM(view_count) FROM reviews WHERE status = 'published'") / 1000, 1); ?>K+
                                </div>
                                <div class="text-gray-500 mt-2 font-medium">হ্যাপি রিডার</div>
                            </div>
                            <div class="text-center group cursor-pointer">
                                <div class="text-4xl font-black text-accent group-hover:scale-125 transition-transform">
                                    <?php echo $db->count("SELECT COUNT(*) FROM categories WHERE status = 'active'"); ?>+
                                </div>
                                <div class="text-gray-500 mt-2 font-medium">ক্যাটেগরি</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Content - Hero Movie Card -->
                    <div class="lg:col-span-2 animate-zoom-in" style="animation-delay: 0.5s;">
                        <div class="relative hero-card transition-all duration-700 transform perspective-1000">
                            <!-- Main Poster -->
                            <div class="relative poster-aspect rounded-3xl overflow-hidden shadow-2xl group">
                                <?php if ($hero_review['poster_image']): ?>
                                    <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $hero_review['poster_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($hero_review['title']); ?>"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                                <?php endif; ?>
                                
                                <!-- Gradient Overlays -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-primary/20 via-transparent to-secondary/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                
                                <!-- Floating Play Button -->
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500">
                                    <div class="w-24 h-24 glass-effect rounded-full flex items-center justify-center group-hover:scale-125 transition-transform duration-300 animate-glow">
                                        <svg class="w-12 h-12 text-white ml-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                </div>
                                
                                <!-- Rating Badge -->
                                <div class="absolute top-6 right-6 bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-4 py-2 rounded-full font-black text-lg shadow-xl">
                                    ⭐ <?php echo $hero_review['rating']; ?>/5
                                </div>
                                
                                <!-- Category Badge -->
                                <div class="absolute top-6 left-6 bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded-full font-bold text-sm shadow-xl">
                                    <?php echo $hero_review['category_name_bn'] ?: $hero_review['category_name']; ?>
                                </div>
                                
                                <!-- Bottom Content -->
                                <div class="absolute bottom-0 left-0 right-0 p-8 transform translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                                    <h3 class="text-2xl font-bold text-white mb-2">
                                        <?php echo htmlspecialchars($hero_review['title']); ?>
                                    </h3>
                                    <p class="text-white/90 mb-4">
                                        <?php echo htmlspecialchars(substr($hero_review['excerpt'], 0, 100)); ?>...
                                    </p>
                                    <a href="/review/<?php echo $hero_review['slug']; ?>" 
                                       class="inline-flex items-center bg-white text-gray-900 px-6 py-3 rounded-xl font-bold hover:bg-gray-100 transition-colors">
                                        পড়ুন
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Floating Side Cards -->
                            <?php if (count($featured_reviews) > 1): ?>
                                <div class="absolute -right-8 top-1/4 space-y-4 animate-fade-in" style="animation-delay: 1s;">
                                    <?php foreach (array_slice($featured_reviews, 1, 2) as $index => $side_review): ?>
                                        <div class="w-24 h-32 bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl hover:scale-110 transition-transform duration-300" style="transform: rotate(<?php echo $index % 2 ? '-' : ''; ?>5deg);">
                                            <?php if ($side_review['poster_image']): ?>
                                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $side_review['poster_image']; ?>" 
                                                     alt="<?php echo htmlspecialchars($side_review['title']); ?>"
                                                     class="w-full h-full object-cover">
                                            <?php endif; ?>
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                            <div class="absolute bottom-2 left-2 right-2">
                                                <div class="text-white text-xs font-bold truncate"><?php echo htmlspecialchars($side_review['title']); ?></div>
                                                <div class="text-yellow-400 text-xs">★ <?php echo $side_review['rating']; ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Rest of content sections... -->
    <section class="py-20 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <!-- Categories -->
            <?php if (!empty($categories)): ?>
            <div class="mb-16">
                <h2 class="text-4xl font-bold text-center mb-12">
                    <span class="text-gradient">জনপ্রিয় ক্যাটেগরি</span>
                </h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                    <?php 
                    $colors = [
                        'from-red-500 to-pink-500',
                        'from-blue-500 to-cyan-500', 
                        'from-green-500 to-emerald-500',
                        'from-yellow-500 to-orange-500',
                        'from-purple-500 to-indigo-500',
                        'from-pink-500 to-rose-500',
                        'from-cyan-500 to-blue-500',
                        'from-orange-500 to-red-500'
                    ];
                    ?>
                    <?php foreach ($categories as $index => $category): ?>
                        <a href="/category/<?php echo $category['slug']; ?>" 
                           class="group bg-gradient-to-br <?php echo $colors[$index % count($colors)]; ?> p-6 rounded-2xl text-white hover:scale-105 hover:rotate-3 transition-all duration-300 shadow-xl">
                            <div class="text-center">
                                <?php if ($category['icon']): ?>
                                    <i class="<?php echo $category['icon']; ?> text-2xl mb-3 group-hover:scale-125 transition-transform"></i>
                                <?php else: ?>
                                    <svg class="w-8 h-8 mx-auto mb-3 group-hover:scale-125 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                <?php endif; ?>
                                <h3 class="font-bold text-sm"><?php echo $category['name_bn'] ?: $category['name']; ?></h3>
                                <p class="text-xs text-white/80">
                                    <?php echo $db->count("SELECT COUNT(*) FROM review_categories rc JOIN reviews r ON rc.review_id = r.id WHERE rc.category_id = ? AND r.status = 'published'", [$category['id']]); ?>
                                </p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Latest Reviews -->
            <?php if (!empty($latest_reviews)): ?>
            <div>
                <div class="flex justify-between items-center mb-12">
                    <h2 class="text-4xl font-bold text-gradient">সর্বশেষ রিভিউ</h2>
                    <a href="/reviews" class="text-primary font-bold hover:text-primary/80 flex items-center text-lg">
                        সব দেখুন
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($latest_reviews as $review): ?>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 group hover:-translate-y-2">
                            <div class="relative poster-aspect overflow-hidden">
                                <?php if ($review['poster_image']): ?>
                                    <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($review['title']); ?>"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <?php endif; ?>
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                
                                <div class="absolute top-4 left-4 bg-primary text-white px-3 py-1 rounded-full text-sm font-medium">
                                    <?php echo $review['type'] == 'movie' ? '🎬' : '📺'; ?> 
                                    <?php echo $review['type'] == 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?>
                                </div>
                                
                                <div class="absolute top-4 right-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-3 py-1 rounded-full text-sm font-bold">
                                    ★ <?php echo $review['rating']; ?>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <div class="flex items-center space-x-2 mb-3">
                                    <span class="text-primary font-medium text-sm">
                                        <?php echo $review['category_name_bn'] ?: $review['category_name']; ?>
                                    </span>
                                    <span class="text-gray-400">•</span>
                                    <span class="text-gray-500 text-sm"><?php echo $review['year']; ?></span>
                                </div>
                                
                                <h3 class="text-xl font-bold mb-3 group-hover:text-primary transition-colors">
                                    <a href="/review/<?php echo $review['slug']; ?>">
                                        <?php echo htmlspecialchars($review['title']); ?>
                                    </a>
                                </h3>
                                
                                <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                                    <?php echo htmlspecialchars(substr($review['excerpt'], 0, 100)); ?>...
                                </p>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 text-sm"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                                    <a href="/review/<?php echo $review['slug']; ?>" 
                                       class="text-primary font-bold hover:text-primary/80 flex items-center">
                                        পড়ুন
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
        // Enhanced animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, observerOptions);

        document.querySelectorAll('[class*="animate-"]').forEach(el => {
            el.style.animationPlayState = 'paused';
            observer.observe(el);
        });
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?>