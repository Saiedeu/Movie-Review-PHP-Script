<?php
/**
 * MSR Homepage - Design 2: Professional Grid Layout
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
    LIMIT 9
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
                        success: '#10B981',
                        cinema: '#111827',
                        light: '#F8FAFC'
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
        .poster-aspect { aspect-ratio: 1920 / 1280; }
        .masonry { column-count: 3; column-gap: 1.5rem; }
        .masonry-item { break-inside: avoid; margin-bottom: 1.5rem; }
        @media (max-width: 768px) { .masonry { column-count: 1; } }
        @media (min-width: 769px) and (max-width: 1024px) { .masonry { column-count: 2; } }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white">

    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18,4V3A1,1 0 0,0 17,2H4A1,1 0 0,0 3,3V18L6,16V4H18M21,6H7A1,1 0 0,0 6,7V21A1,1 0 0,0 7,22H21A1,1 0 0,0 22,21V7A1,1 0 0,0 21,6M20,20H8V8H20V20M14.5,10.5V19.5L18.5,15L14.5,10.5Z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">MSR</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">বাংলা রিভিউ প্ল্যাটফর্ম</p>
                    </div>
                </div>
                
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="/" class="font-semibold text-primary">হোম</a>
                    <a href="/reviews" class="hover:text-primary transition-colors">রিভিউ</a>
                    <a href="/categories" class="hover:text-primary transition-colors">ক্যাটেগরি</a>
                    <a href="/submit-review" class="hover:text-primary transition-colors">জমা দিন</a>
                </nav>
                
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <input type="text" placeholder="সার্চ..." class="px-4 py-2 text-base bg-gray-100 dark:bg-gray-800 rounded-lg border-0 focus:ring-2 focus:ring-primary focus:outline-none w-64">
                        <svg class="w-4 h-4 absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button class="bg-primary text-white px-6 py-2 rounded-lg font-medium hover:bg-primary/90 transition-colors">যোগ দিন</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <?php if (!empty($featured_reviews)): ?>
    <section class="py-12 bg-gradient-to-br from-primary/5 to-secondary/5 dark:from-primary/10 dark:to-secondary/10">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <div class="inline-flex items-center bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-medium mb-4">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    ফিচার্ড রিভিউ
                </div>
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">সেরা সিনেমা</span>
                    <br><span class="text-gray-900 dark:text-white">ও সিরিয়ালের রিভিউ</span>
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">বিশেষজ্ঞ রিভিউ পড়ুন এবং আপনার পরবর্তী দেখার জন্য নিখুঁত কন্টেন্ট খুঁজে নিন</p>
            </div>

            <!-- Featured Grid -->
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Featured -->
                <div class="lg:col-span-2">
                    <?php $main_featured = $featured_reviews[0]; ?>
                    <div class="relative bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-xl group">
                        <div class="relative h-96">
                            <?php if ($main_featured['poster_image']): ?>
                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $main_featured['poster_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($main_featured['title']); ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            
                            <div class="absolute bottom-0 p-8 text-white">
                                <div class="flex items-center space-x-3 mb-4">
                                    <span class="bg-primary px-3 py-1 rounded-full text-sm font-medium">ফিচার্ড</span>
                                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
                                        <?php echo $main_featured['type'] == 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?>
                                    </span>
                                    <span class="text-accent font-bold">★ <?php echo $main_featured['rating']; ?>/5</span>
                                </div>
                                <h2 class="text-3xl font-bold mb-3"><?php echo htmlspecialchars($main_featured['title']); ?></h2>
                                <p class="text-white/90 text-lg mb-6"><?php echo htmlspecialchars(substr($main_featured['excerpt'], 0, 150)); ?>...</p>
                                <a href="/review/<?php echo $main_featured['slug']; ?>" class="inline-flex items-center bg-white text-gray-900 px-6 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-colors">
                                    সম্পূর্ণ পড়ুন
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Side Featured -->
                <div class="space-y-6">
                    <?php foreach (array_slice($featured_reviews, 1, 3) as $review): ?>
                        <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow group">
                            <div class="flex">
                                <div class="w-32 h-24 flex-shrink-0">
                                    <?php if ($review['poster_image']): ?>
                                        <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                             alt="<?php echo htmlspecialchars($review['title']); ?>"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <?php endif; ?>
                                </div>
                                <div class="p-4 flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="bg-secondary/10 text-secondary px-2 py-1 rounded text-xs font-medium">
                                            <?php echo $review['type'] == 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?>
                                        </span>
                                        <span class="text-accent text-sm font-medium">★ <?php echo $review['rating']; ?></span>
                                    </div>
                                    <h3 class="font-bold mb-1 group-hover:text-primary transition-colors">
                                        <a href="/review/<?php echo $review['slug']; ?>">
                                            <?php echo htmlspecialchars($review['title']); ?>
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm"><?php echo $review['year']; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Main Content -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-4 gap-12">
                
                <!-- Main Content Area -->
                <div class="lg:col-span-3">
                    
                    <!-- Latest Reviews Masonry -->
                    <?php if (!empty($latest_reviews)): ?>
                    <div class="mb-16">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-3xl font-bold">সর্বশেষ রিভিউ</h2>
                            <a href="/reviews" class="text-primary font-medium hover:text-primary/80 flex items-center">
                                সব দেখুন
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                        
                        <div class="masonry">
                            <?php foreach ($latest_reviews as $review): ?>
                                <div class="masonry-item">
                                    <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 group">
                                        <div class="relative h-48">
                                            <?php if ($review['poster_image']): ?>
                                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                                     alt="<?php echo htmlspecialchars($review['title']); ?>"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            <?php endif; ?>
                                            
                                            <div class="absolute top-3 left-3 bg-black/70 text-white px-2 py-1 rounded-full text-xs">
                                                <?php echo $review['type'] == 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?>
                                            </div>
                                            <div class="absolute top-3 right-3 bg-accent text-white px-2 py-1 rounded-full text-xs font-bold">
                                                ★ <?php echo $review['rating']; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="p-5">
                                            <div class="flex items-center space-x-2 mb-3">
                                                <span class="text-primary text-sm font-medium">
                                                    <?php echo $review['category_name_bn'] ?: $review['category_name']; ?>
                                                </span>
                                                <span class="text-gray-400">•</span>
                                                <span class="text-gray-500 text-sm"><?php echo $review['year']; ?></span>
                                            </div>
                                            
                                            <h3 class="text-lg font-bold mb-2 group-hover:text-primary transition-colors">
                                                <a href="/review/<?php echo $review['slug']; ?>">
                                                    <?php echo htmlspecialchars($review['title']); ?>
                                                </a>
                                            </h3>
                                            
                                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 leading-relaxed">
                                                <?php echo htmlspecialchars(substr($review['excerpt'], 0, 120)); ?>...
                                            </p>
                                            
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-500"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                                                <a href="/review/<?php echo $review['slug']; ?>" class="text-primary font-medium hover:text-primary/80">
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

                    <!-- Categories -->
                    <?php if (!empty($categories)): ?>
                    <div>
                        <h2 class="text-3xl font-bold mb-8">জনরা</h2>
                        <div class="grid md:grid-cols-4 gap-4">
                            <?php 
                            $colors = ['bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500', 'bg-cyan-500'];
                            foreach ($categories as $index => $category): 
                            ?>
                                <a href="/category/<?php echo $category['slug']; ?>" 
                                   class="group <?php echo $colors[$index % count($colors)]; ?> text-white rounded-xl p-6 hover:scale-105 transition-transform">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="font-bold"><?php echo $category['name_bn'] ?: $category['name']; ?></h3>
                                        <?php if ($category['icon']): ?>
                                            <i class="<?php echo $category['icon']; ?> text-xl opacity-70"></i>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-white/80 text-sm">
                                        <?php echo $db->count("SELECT COUNT(*) FROM review_categories rc JOIN reviews r ON rc.review_id = r.id WHERE rc.category_id = ? AND r.status = 'published'", [$category['id']]); ?> রিভিউ
                                    </p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="lg:col-span-1 space-y-8">
                    
                    <!-- Popular Reviews -->
                    <?php if (!empty($popular_reviews)): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold">জনপ্রিয় রিভিউ</h3>
                            <span class="bg-accent/10 text-accent px-2 py-1 rounded-full text-xs font-semibold">HOT</span>
                        </div>
                        
                        <div class="space-y-4">
                            <?php foreach (array_slice($popular_reviews, 0, 5) as $index => $review): ?>
                                <div class="flex items-start space-x-3 group">
                                    <div class="w-7 h-7 <?php echo $colors[$index % count($colors)]; ?> rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                        <?php echo $index + 1; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-sm group-hover:text-primary transition-colors">
                                            <a href="/review/<?php echo $review['slug']; ?>">
                                                <?php echo htmlspecialchars($review['title']); ?>
                                            </a>
                                        </h4>
                                        <div class="flex items-center space-x-2 mt-1 text-xs text-gray-500">
                                            <span><?php echo $review['year']; ?></span>
                                            <span>•</span>
                                            <span class="text-accent">★ <?php echo $review['rating']; ?></span>
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
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
                        <h3 class="text-lg font-bold mb-4">পরিসংখ্যান</h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">রিভিউ</span>
                                <span class="font-bold text-primary text-lg"><?php echo number_format($db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'")); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">ক্যাটেগরি</span>
                                <span class="font-bold text-secondary text-lg"><?php echo $db->count("SELECT COUNT(*) FROM categories WHERE status = 'active'"); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">মোট ভিউ</span>
                                <span class="font-bold text-success text-lg"><?php echo number_format($db->count("SELECT SUM(view_count) FROM reviews WHERE status = 'published'")); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="bg-gradient-to-br from-primary to-secondary rounded-xl p-6 text-white text-center">
                        <h3 class="text-lg font-bold mb-3">আপনার রিভিউ শেয়ার করুন</h3>
                        <p class="text-white/90 text-sm mb-4">আমাদের কমিউনিটিতে যোগ দিয়ে আপনার মতামত শেয়ার করুন</p>
                        <a href="/submit-review" class="block bg-white text-primary font-semibold py-3 px-6 rounded-lg hover:bg-gray-100 transition-colors">
                            রিভিউ লিখুন
                        </a>
                    </div>

                </aside>
            </div>
        </div>
    </section>

    <script>
        // Simple fade-in animation
        const elements = document.querySelectorAll('.masonry-item, .bg-white, .bg-gray-800');
        elements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            
            setTimeout(() => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 100);
        });
    </script>
</body>
</html>

<?php
// Include footer
include 'includes/footer.php';
?>