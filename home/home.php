<?php
/**
 * MSR Homepage - Design 1: Modern Clean Layout
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
                    fontFamily: {
                        'display': ['Inter', 'sans-serif']
                    }
                }
            }
        }

        // Dark mode detection
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
        
        .grid-container {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }
        
        .hero-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            min-height: 100vh;
        }
        
        .sidebar-sticky {
            position: sticky;
            top: 6rem;
            height: fit-content;
        }
        
        .card-3d {
            transform-style: preserve-3d;
            transition: all 0.5s ease;
        }
        
        .card-3d:hover {
            transform: rotateY(5deg) rotateX(5deg) translateZ(20px);
        }
        
        .glass-morphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .poster-aspect {
            aspect-ratio: 1920 / 1280;
        }
        
        @media (max-width: 768px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .sidebar-sticky {
                position: static;
            }
        }
    </style>
</head>
<body class="bg-white dark:bg-cinema text-gray-900 dark:text-white font-display">

    <!-- Top Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/90 dark:bg-cinema/90 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Brand -->
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18,4V3A1,1 0 0,0 17,2H4A1,1 0 0,0 3,3V18L6,16V4H18M21,6H7A1,1 0 0,0 6,7V21A1,1 0 0,0 7,22H21A1,1 0 0,0 22,21V7A1,1 0 0,0 21,6M20,20H8V8H20V20M14.5,10.5V19.5L18.5,15L14.5,10.5Z"/>
                            </svg>
                        </div>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-accent rounded-full animate-pulse"></div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            MSR
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Movie & Series Review</p>
                    </div>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="/" class="text-primary font-semibold">হোম</a>
                    <a href="/reviews" class="hover:text-primary transition-colors">রিভিউ</a>
                    <a href="/categories" class="hover:text-primary transition-colors">ক্যাটেগরি</a>
                    <a href="/submit-review" class="hover:text-primary transition-colors">জমা দিন</a>
                    <a href="/about" class="hover:text-primary transition-colors">আমাদের সম্পর্কে</a>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-4">
                    <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <button class="bg-gradient-to-r from-primary to-secondary text-white px-6 py-2 rounded-xl font-semibold hover:shadow-lg transition-all">
                        যোগ দিন
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Grid Layout -->
    <main class="pt-20">
        <div class="container mx-auto px-4 py-8">
            <div class="hero-grid">
                <!-- Main Content Area -->
                <div class="space-y-12">
                    
                    <!-- Hero Section -->
                    <section class="relative bg-gradient-to-br from-primary/10 via-secondary/10 to-accent/10 dark:from-primary/20 dark:via-secondary/20 dark:to-accent/20 rounded-3xl p-8 lg:p-12 overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-10">
                            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-primary to-transparent"></div>
                            <div class="absolute bottom-0 right-0 w-64 h-64 bg-secondary rounded-full blur-3xl"></div>
                        </div>
                        
                        <div class="relative z-10">
                            <div class="grid lg:grid-cols-2 gap-8 items-center">
                                <!-- Content -->
                                <div class="space-y-6">
                                    <div class="inline-flex items-center bg-white/20 dark:bg-black/20 backdrop-blur-sm rounded-full px-4 py-2 text-sm font-medium">
                                        <div class="w-2 h-2 bg-accent rounded-full mr-2 animate-pulse"></div>
                                        এই সপ্তাহের ফিচার্ড
                                    </div>
                                    
                                    <h1 class="text-4xl lg:text-6xl font-black leading-tight text-shadow">
                                        <span class="bg-gradient-to-r from-primary via-secondary to-accent bg-clip-text text-transparent">
                                            আবিষ্কার করুন
                                        </span>
                                        <br>
                                        <span class="text-gray-900 dark:text-white">অসাধারণ সিনেমা</span>
                                    </h1>
                                    
                                    <p class="text-lg lg:text-xl text-gray-600 dark:text-gray-300 leading-relaxed">
                                        সর্বশেষ সিনেমা ও সিরিয়াল এক্সপ্লোর করুন, বিশেষজ্ঞ রিভিউ পড়ুন, এবং আপনার 
                                        পরবর্তী প্রিয় কন্টেন্ট আবিষ্কার করুন।
                                    </p>
                                    
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <a href="/reviews" class="group bg-primary text-white px-8 py-4 rounded-2xl font-semibold text-lg hover:bg-primary/90 transition-all flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                            এখনই দেখুন
                                        </a>
                                        <a href="/categories" class="border-2 border-primary text-primary dark:text-white hover:bg-primary hover:text-white px-8 py-4 rounded-2xl font-semibold text-lg transition-all">
                                            লাইব্রেরি ব্রাউজ করুন
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Featured Review Card -->
                                <?php if (!empty($featured_reviews)): ?>
                                    <div class="relative">
                                        <?php $hero_review = $featured_reviews[0]; ?>
                                        <div class="card-3d bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-2xl">
                                            <div class="relative poster-aspect">
                                                <?php if ($hero_review['poster_image']): ?>
                                                    <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $hero_review['poster_image']; ?>" 
                                                         alt="<?php echo htmlspecialchars($hero_review['title']); ?>"
                                                         class="w-full h-full object-cover">
                                                <?php endif; ?>
                                                
                                                <!-- Overlay -->
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                                
                                                <!-- Play Button -->
                                                <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M8 5v14l11-7z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                
                                                <!-- Rating -->
                                                <div class="absolute top-4 right-4 bg-black/70 backdrop-blur-sm text-white px-3 py-1 rounded-full font-semibold text-sm">
                                                    ★ <?php echo $hero_review['rating']; ?>/5
                                                </div>
                                            </div>
                                            
                                            <div class="p-6">
                                                <div class="flex items-center space-x-2 mb-3">
                                                    <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-medium">
                                                        <?php echo $hero_review['category_name_bn'] ?: $hero_review['category_name']; ?>
                                                    </span>
                                                    <span class="text-gray-500"><?php echo $hero_review['year']; ?></span>
                                                </div>
                                                
                                                <h3 class="text-xl font-bold mb-2">
                                                    <?php echo htmlspecialchars($hero_review['title']); ?>
                                                </h3>
                                                
                                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                                    <?php echo htmlspecialchars(substr($hero_review['excerpt'], 0, 100)); ?>...
                                                </p>
                                                
                                                <a href="/review/<?php echo $hero_review['slug']; ?>" class="w-full bg-gradient-to-r from-primary to-secondary text-white py-3 rounded-xl font-semibold hover:shadow-lg transition-all block text-center">
                                                    সম্পূর্ণ রিভিউ পড়ুন
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Featured Reviews Grid -->
                    <?php if (!empty($latest_reviews)): ?>
                    <section>
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-3xl font-bold">সর্বশেষ রিভিউ</h2>
                            <a href="/reviews" class="text-primary hover:text-primary/80 font-semibold flex items-center">
                                সব দেখুন
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                        
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($latest_reviews as $review): ?>
                                <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 group">
                                    <div class="relative h-48 overflow-hidden">
                                        <?php if ($review['poster_image']): ?>
                                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($review['title']); ?>"
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        <?php endif; ?>
                                        
                                        <!-- Hover Overlay -->
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <!-- Rating Badge -->
                                        <div class="absolute top-3 right-3 bg-black/70 backdrop-blur-sm text-white px-2 py-1 rounded-full text-xs font-semibold">
                                            ★ <?php echo $review['rating']; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="bg-primary/10 text-primary px-2 py-1 rounded-full text-xs font-medium">
                                                <?php echo $review['type'] == 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?>
                                            </span>
                                            <span class="text-gray-500 text-sm"><?php echo $review['year']; ?></span>
                                        </div>
                                        
                                        <h3 class="font-bold text-lg mb-1 group-hover:text-primary transition-colors">
                                            <a href="/review/<?php echo $review['slug']; ?>">
                                                <?php echo htmlspecialchars($review['title']); ?>
                                            </a>
                                        </h3>
                                        
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                                            <?php echo htmlspecialchars(substr($review['excerpt'], 0, 80)); ?>...
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                    <?php endif; ?>

                    <!-- Categories Grid -->
                    <?php if (!empty($categories)): ?>
                    <section>
                        <h2 class="text-3xl font-bold mb-8">জনরা অনুযায়ী ব্রাউজ করুন</h2>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <?php 
                            $colors = [
                                'from-red-500 to-orange-500',
                                'from-blue-500 to-purple-500',
                                'from-yellow-500 to-pink-500',
                                'from-purple-500 to-red-500',
                                'from-cyan-500 to-blue-500',
                                'from-pink-500 to-rose-500',
                                'from-green-500 to-blue-500',
                                'from-indigo-500 to-purple-500'
                            ];
                            ?>
                            <?php foreach ($categories as $index => $category): ?>
                                <a href="/category/<?php echo $category['slug']; ?>" class="group relative bg-gradient-to-br <?php echo $colors[$index % count($colors)]; ?> rounded-2xl p-6 text-white cursor-pointer hover:scale-105 transition-transform">
                                    <div class="relative z-10">
                                        <h3 class="text-xl font-bold mb-2"><?php echo $category['name_bn'] ?: $category['name']; ?></h3>
                                        <p class="text-white/80 text-sm">
                                            <?php echo $db->count("SELECT COUNT(*) FROM review_categories rc JOIN reviews r ON rc.review_id = r.id WHERE rc.category_id = ? AND r.status = 'published'", [$category['id']]); ?> রিভিউ
                                        </p>
                                    </div>
                                    
                                    <!-- Background Icon -->
                                    <div class="absolute bottom-2 right-2 opacity-20">
                                        <?php if ($category['icon']): ?>
                                            <i class="<?php echo $category['icon']; ?> text-3xl"></i>
                                        <?php else: ?>
                                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                    <?php endif; ?>

                </div>

                <!-- Sidebar -->
                <aside class="sidebar-sticky space-y-8">
                    
                    <!-- Search Widget -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                        <h3 class="text-xl font-bold mb-4">দ্রুত অনুসন্ধান</h3>
                        <form action="/search" method="GET" class="relative">
                            <input type="text" 
                                   name="q"
                                   placeholder="সিনেমা বা সিরিয়াল খুঁজুন..." 
                                   class="w-full px-4 py-3 text-base bg-gray-100 dark:bg-gray-700 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary">
                            <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 p-2 text-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Popular Reviews -->
                    <?php if (!empty($popular_reviews)): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold">জনপ্রিয় রিভিউ</h3>
                            <span class="bg-accent/10 text-accent px-2 py-1 rounded-full text-xs font-semibold">HOT</span>
                        </div>
                        
                        <div class="space-y-3">
                            <?php foreach ($popular_reviews as $index => $review): ?>
                                <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <div class="w-8 h-8 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        <?php echo $index + 1; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-sm truncate">
                                            <a href="/review/<?php echo $review['slug']; ?>">
                                                <?php echo htmlspecialchars($review['title']); ?>
                                            </a>
                                        </h4>
                                        <div class="flex items-center space-x-2 text-xs text-gray-500">
                                            <span><?php echo $review['year']; ?></span>
                                            <span>•</span>
                                            <span class="text-accent font-semibold">★ <?php echo $review['rating']; ?></span>
                                            <span>•</span>
                                            <span><?php echo number_format($review['view_count']); ?> ভিউ</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Quick Stats -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center space-x-2 mb-4">
                            <h3 class="text-xl font-bold">পরিসংখ্যান</h3>
                            <div class="w-2 h-2 bg-secondary rounded-full animate-pulse"></div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">মোট রিভিউ</span>
                                <span class="font-bold text-primary"><?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">ক্যাটেগরি</span>
                                <span class="font-bold text-secondary"><?php echo $db->count("SELECT COUNT(*) FROM categories WHERE status = 'active'"); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">মোট ভিউ</span>
                                <span class="font-bold text-accent"><?php echo number_format($db->count("SELECT SUM(view_count) FROM reviews WHERE status = 'published'")); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Newsletter Signup -->
                    <div class="bg-gradient-to-br from-primary to-secondary rounded-2xl p-6 text-white">
                        <h3 class="text-xl font-bold mb-3">আপডেট পান</h3>
                        <p class="text-white/80 text-sm mb-4">সর্বশেষ রিভিউ ও সুপারিশ সরাসরি ইনবক্সে পান।</p>
                        
                        <div class="space-y-3">
                            <input type="email" 
                                   placeholder="আপনার ইমেইল দিন" 
                                   class="w-full px-4 py-3 text-base bg-white/20 border-0 rounded-xl placeholder-white/70 text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                            <button class="w-full bg-white text-primary py-3 rounded-xl font-semibold hover:bg-gray-100 transition-colors">
                                সাবস্ক্রাইব করুন
                            </button>
                        </div>
                    </div>

                </aside>
            </div>
        </div>
    </main>

    <script>
        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards
        document.querySelectorAll('.card-3d, .bg-white, .bg-gray-800').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>

<?php
// Include footer
include 'includes/footer.php';
?>