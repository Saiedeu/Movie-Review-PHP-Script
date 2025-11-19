<?php
/**
 * MSR Homepage - Design 2: Dramatic Split Screen Hero
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
    LIMIT 4
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
                        cinema: '#111827'
                    },
                    animation: {
                        'slide-in-left': 'slideInLeft 1s ease-out forwards',
                        'slide-in-right': 'slideInRight 1s ease-out forwards',
                        'fade-up': 'fadeUp 0.8s ease-out forwards',
                        'bounce-in': 'bounceIn 0.8s ease-out forwards',
                        'zoom-pulse': 'zoomPulse 0.8s ease-out forwards',
                        'float-slow': 'floatSlow 8s ease-in-out infinite',
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
        
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-100px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(100px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        @keyframes zoomPulse {
            0% { opacity: 0; transform: scale(0.8); }
            50% { transform: scale(1.1); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(5deg); }
        }
        
        .hero-split {
            background: linear-gradient(135deg, 
                rgba(93, 92, 222, 0.1) 0%, 
                rgba(236, 72, 153, 0.1) 50%, 
                rgba(245, 158, 11, 0.1) 100%);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .text-stroke {
            -webkit-text-stroke: 2px rgba(93, 92, 222, 0.3);
        }
        
        .hero-image:hover {
            transform: scale(1.05) rotateY(5deg);
        }
    </style>
</head>
<body class="bg-white dark:bg-cinema text-gray-900 dark:text-white">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/95 dark:bg-cinema/95 backdrop-blur-xl border-b border-gray-200/30 dark:border-gray-800/30">
        <div class="container mx-auto px-4 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary via-secondary to-accent rounded-3xl flex items-center justify-center shadow-2xl">
                        <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18,4V3A1,1 0 0,0 17,2H4A1,1 0 0,0 3,3V18L6,16V4H18M21,6H7A1,1 0 0,0 6,7V21A1,1 0 0,0 7,22H21A1,1 0 0,0 22,21V7A1,1 0 0,0 21,6M20,20H8V8H20V20M14.5,10.5V19.5L18.5,15L14.5,10.5Z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">MSR</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Movie & Series Review</p>
                    </div>
                </div>
                
                <div class="hidden lg:flex items-center space-x-10">
                    <a href="/" class="text-primary font-bold text-lg relative">
                        হোম
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-primary rounded-full"></div>
                    </a>
                    <a href="/reviews" class="hover:text-primary transition-colors font-semibold">রিভিউ</a>
                    <a href="/categories" class="hover:text-primary transition-colors font-semibold">ক্যাটেগরি</a>
                    <a href="/submit-review" class="hover:text-primary transition-colors font-semibold">জমা দিন</a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <form action="/search" method="GET" class="relative">
                        <input type="text" name="q" placeholder="সার্চ করুন..." 
                               class="px-6 py-3 pl-12 bg-gray-100/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-2xl border-0 focus:ring-2 focus:ring-primary focus:outline-none transition-all w-80 text-base">
                        <svg class="w-5 h-5 absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-primary text-white p-2 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                    <button class="bg-gradient-to-r from-secondary to-accent text-white px-8 py-3 rounded-2xl font-bold hover:shadow-xl hover:scale-105 transition-all">
                        যোগ দিন
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- DRAMATIC SPLIT SCREEN HERO -->
    <section class="relative min-h-screen pt-24 hero-split overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute top-20 left-20 w-80 h-80 bg-primary/10 rounded-full blur-3xl animate-float-slow"></div>
            <div class="absolute bottom-20 right-20 w-64 h-64 bg-secondary/10 rounded-full blur-3xl animate-float-slow" style="animation-delay: 3s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-accent/5 rounded-full blur-3xl animate-float-slow" style="animation-delay: 6s;"></div>
        </div>

        <?php if (!empty($featured_reviews)): ?>
            <?php $main_review = $featured_reviews[0]; ?>
            
            <div class="container mx-auto px-4 py-16 relative z-10">
                <div class="grid lg:grid-cols-2 gap-16 items-center min-h-[85vh]">
                    
                    <!-- Left Side - Content -->
                    <div class="space-y-10 animate-slide-in-left">
                        <!-- Premium Badge -->
                        <div class="inline-flex items-center glass-card rounded-full px-8 py-4 shadow-2xl">
                            <div class="w-4 h-4 bg-gradient-to-r from-accent to-secondary rounded-full mr-4 animate-pulse"></div>
                            <span class="text-white font-bold text-lg">🏆 প্রিমিয়াম ফিচার্ড রিভিউ</span>
                        </div>
                        
                        <!-- Main Title -->
                        <div class="space-y-8">
                            <h1 class="text-6xl lg:text-8xl font-black leading-none text-stroke">
                                <span class="bg-gradient-to-r from-primary via-secondary to-accent bg-clip-text text-transparent">
                                    <?php echo htmlspecialchars($main_review['title']); ?>
                                </span>
                            </h1>
                            
                            <!-- Movie Info Bar -->
                            <div class="flex flex-wrap items-center gap-6 text-xl">
                                <!-- Rating -->
                                <div class="flex items-center space-x-3 glass-card px-6 py-3 rounded-full">
                                    <div class="flex text-yellow-400 text-2xl">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?php echo $i <= $main_review['rating'] ? 'text-yellow-400' : 'text-gray-400'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-white font-bold text-2xl"><?php echo $main_review['rating']; ?>/5</span>
                                </div>
                                
                                <!-- Year -->
                                <div class="glass-card px-6 py-3 rounded-full">
                                    <span class="text-white font-bold text-xl"><?php echo $main_review['year']; ?></span>
                                </div>
                                
                                <!-- Type -->
                                <div class="bg-gradient-to-r from-primary to-secondary px-6 py-3 rounded-full">
                                    <span class="text-white font-bold">
                                        <?php echo $main_review['type'] == 'movie' ? '🎬 সিনেমা' : '📺 সিরিয়াল'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="glass-card rounded-3xl p-8">
                            <p class="text-2xl text-white leading-relaxed">
                                <?php echo htmlspecialchars(substr($main_review['excerpt'], 0, 250)); ?>...
                            </p>
                        </div>
                        
                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-6">
                            <a href="/review/<?php echo $main_review['slug']; ?>" 
                               class="group bg-gradient-to-r from-primary to-secondary text-white px-12 py-6 rounded-3xl font-black text-2xl hover:shadow-2xl hover:scale-110 transition-all duration-500 text-center flex items-center justify-center">
                                <svg class="w-8 h-8 mr-4 group-hover:scale-125 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                সম্পূর্ণ রিভিউ পড়ুন
                            </a>
                            <a href="/reviews" 
                               class="group glass-card text-white px-12 py-6 rounded-3xl font-black text-2xl hover:shadow-xl hover:scale-110 transition-all duration-300 text-center flex items-center justify-center">
                                <svg class="w-8 h-8 mr-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                আরো এক্সপ্লোর করুন
                            </a>
                        </div>
                    </div>
                    
                    <!-- Right Side - Hero Image -->
                    <div class="animate-slide-in-right">
                        <div class="relative">
                            <!-- Main Hero Image -->
                            <div class="relative hero-image poster-aspect rounded-3xl overflow-hidden shadow-2xl transition-all duration-700 transform">
                                <?php if ($main_review['poster_image']): ?>
                                    <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $main_review['poster_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($main_review['title']); ?>"
                                         class="w-full h-full object-cover">
                                <?php endif; ?>
                                
                                <!-- Dynamic Overlays -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-primary/20 via-transparent to-secondary/20"></div>
                                
                                <!-- Floating Play Button -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-28 h-28 glass-card rounded-full flex items-center justify-center hover:scale-125 transition-all duration-500 shadow-2xl animate-bounce-in" style="animation-delay: 1s;">
                                        <svg class="w-14 h-14 text-white ml-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                </div>
                                
                                <!-- Top Badges -->
                                <div class="absolute top-6 left-6 right-6 flex justify-between items-start">
                                    <div class="bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded-full font-bold shadow-xl">
                                        <?php echo $main_review['category_name_bn'] ?: $main_review['category_name']; ?>
                                    </div>
                                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-4 py-2 rounded-full font-black shadow-xl">
                                        ⭐ FEATURED
                                    </div>
                                </div>
                                
                                <!-- Bottom Info -->
                                <div class="absolute bottom-6 left-6 right-6">
                                    <div class="glass-card rounded-2xl p-6">
                                        <h3 class="text-white text-2xl font-bold mb-2">
                                            <?php echo htmlspecialchars($main_review['title']); ?>
                                        </h3>
                                        <div class="flex items-center justify-between">
                                            <span class="text-white/90"><?php echo $main_review['year']; ?></span>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-yellow-400 font-bold">★ <?php echo $main_review['rating']; ?></span>
                                                <span class="text-white/90">/5</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Floating Side Reviews -->
                            <?php if (count($featured_reviews) > 1): ?>
                                <div class="absolute -left-8 top-1/2 transform -translate-y-1/2 space-y-6 animate-fade-up" style="animation-delay: 1.5s;">
                                    <?php foreach (array_slice($featured_reviews, 1, 2) as $index => $side_review): ?>
                                        <div class="w-32 h-20 glass-card rounded-xl overflow-hidden shadow-xl hover:scale-110 transition-all duration-300" style="transform: rotate(<?php echo $index % 2 ? '' : '-'; ?>8deg);">
                                            <?php if ($side_review['poster_image']): ?>
                                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $side_review['poster_image']; ?>" 
                                                     alt="<?php echo htmlspecialchars($side_review['title']); ?>"
                                                     class="w-full h-full object-cover">
                                            <?php endif; ?>
                                            <div class="absolute inset-0 bg-black/40 flex items-end p-2">
                                                <div class="text-white text-xs font-bold truncate w-full">
                                                    <?php echo htmlspecialchars($side_review['title']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="absolute -right-8 bottom-1/4 space-y-4 animate-zoom-pulse" style="animation-delay: 2s;">
                                    <?php if (isset($featured_reviews[3])): ?>
                                        <div class="w-28 h-40 glass-card rounded-xl overflow-hidden shadow-xl hover:scale-110 transition-all duration-300" style="transform: rotate(12deg);">
                                            <?php if ($featured_reviews[3]['poster_image']): ?>
                                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $featured_reviews[3]['poster_image']; ?>" 
                                                     alt="<?php echo htmlspecialchars($featured_reviews[3]['title']); ?>"
                                                     class="w-full h-full object-cover">
                                            <?php endif; ?>
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-2">
                                                <div class="text-white text-xs font-bold">
                                                    <div class="truncate"><?php echo htmlspecialchars($featured_reviews[3]['title']); ?></div>
                                                    <div class="text-yellow-400">★ <?php echo $featured_reviews[3]['rating']; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Bottom Stats -->
                <div class="flex justify-center items-center space-x-16 pt-16 animate-fade-up" style="animation-delay: 2.5s;">
                    <div class="text-center group cursor-pointer">
                        <div class="text-5xl font-black bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent group-hover:scale-125 transition-transform">
                            <?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?>+
                        </div>
                        <div class="text-white font-bold text-lg mt-2">প্রিমিয়াম রিভিউ</div>
                    </div>
                    <div class="text-center group cursor-pointer">
                        <div class="text-5xl font-black bg-gradient-to-r from-secondary to-accent bg-clip-text text-transparent group-hover:scale-125 transition-transform">
                            <?php echo number_format($db->count("SELECT SUM(view_count) FROM reviews WHERE status = 'published'") / 1000, 1); ?>K+
                        </div>
                        <div class="text-white font-bold text-lg mt-2">স্যাটিসফাইড রিডার</div>
                    </div>
                    <div class="text-center group cursor-pointer">
                        <div class="text-5xl font-black bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent group-hover:scale-125 transition-transform">
                            <?php echo $db->count("SELECT COUNT(*) FROM categories WHERE status = 'active'"); ?>+
                        </div>
                        <div class="text-white font-bold text-lg mt-2">বিভিন্ন ক্যাটেগরি</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- Rest of the content sections similar to home1.php but with different styling... -->
    <section class="py-20 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <!-- Latest Reviews -->
            <?php if (!empty($latest_reviews)): ?>
            <div class="mb-20">
                <div class="text-center mb-16">
                    <h2 class="text-5xl font-black bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-6">
                        সর্বশেষ রিভিউগুলো
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300">আমাদের বিশেষজ্ঞদের সাম্প্রতিক বিশ্লেষণ</p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($latest_reviews as $index => $review): ?>
                        <div class="bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 group hover:-translate-y-4" style="animation: fadeUp 0.8s ease-out <?php echo $index * 0.1; ?>s both;">
                            <div class="relative poster-aspect overflow-hidden">
                                <?php if ($review['poster_image']): ?>
                                    <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($review['title']); ?>"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <?php endif; ?>
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                
                                <div class="absolute top-4 left-4 bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded-full text-sm font-bold">
                                    <?php echo $review['type'] == 'movie' ? '🎬 সিনেমা' : '📺 সিরিয়াল'; ?>
                                </div>
                                
                                <div class="absolute top-4 right-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-3 py-2 rounded-full font-black">
                                    ★ <?php echo $review['rating']; ?>/5
                                </div>
                                
                                <div class="absolute bottom-4 left-4 right-4 text-white">
                                    <h3 class="text-xl font-bold mb-2 group-hover:text-yellow-300 transition-colors">
                                        <?php echo htmlspecialchars($review['title']); ?>
                                    </h3>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm opacity-90"><?php echo $review['year']; ?></span>
                                        <a href="/review/<?php echo $review['slug']; ?>" 
                                           class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-medium hover:bg-white/30 transition-colors">
                                            পড়ুন →
                                        </a>
                                    </div>
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
        // Staggered animations
        const elements = document.querySelectorAll('[style*="animation"]');
        elements.forEach(el => {
            el.style.opacity = '0';
            el.style.animationFillMode = 'forwards';
        });

        // Trigger animations on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, { threshold: 0.1 });

        elements.forEach(el => observer.observe(el));
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?>