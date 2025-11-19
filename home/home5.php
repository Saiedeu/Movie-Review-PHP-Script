<?php
/**
 * MSR Homepage - Design 5: Clean Modern Grid Layout
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

// Get top rated reviews
$top_rated = $db->fetchAll("
    SELECT r.*, c.name as category_name, c.name_bn as category_name_bn
    FROM reviews r 
    LEFT JOIN review_categories rc ON r.id = rc.review_id
    LEFT JOIN categories c ON rc.category_id = c.id
    WHERE r.status = 'published' 
    ORDER BY r.rating DESC 
    LIMIT 6
");

// Get categories with counts
$categories = $db->fetchAll("
    SELECT c.*, COUNT(r.id) as review_count 
    FROM categories c 
    LEFT JOIN review_categories rc ON c.id = rc.category_id 
    LEFT JOIN reviews r ON rc.review_id = r.id AND r.status = 'published'
    WHERE c.status = 'active'
    GROUP BY c.id 
    ORDER BY review_count DESC 
    LIMIT 6
");

// Get coming soon / recent additions
$coming_soon = [
    ['title' => 'স্কুইড গেম সিজন ২', 'genre' => 'থ্রিলার', 'coming' => 'ডিসেম্বর ২০২৪'],
    ['title' => 'অ্যাভাটার ৩', 'genre' => 'সাই-ফাই', 'coming' => 'মার্চ ২০২৫'],
    ['title' => 'জন উইক ৫', 'genre' => 'অ্যাকশন', 'coming' => 'এপ্রিল ২০২৫'],
    ['title' => 'মিশন ইম্পসিবল ৮', 'genre' => 'অ্যাকশন', 'coming' => 'জুন ২০২৫']
];

// SEO Meta Data
$seo_title = getSiteSetting('site_name', SITE_NAME);
$seo_description = getSiteSetting('site_description', SITE_DESCRIPTION);
$seo_keywords = getSiteSetting('site_keywords', SITE_KEYWORDS);
?>

<!DOCTYPE html>
<html lang="bn" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $seo_title; ?> - আধুনিক সিনেমা হাব</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo $seo_description; ?>">
    <meta name="keywords" content="<?php echo $seo_keywords; ?>">
    <meta name="author" content="MSR Team">
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Configuration -->
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
                        'bangla': ['Hind Siliguri', 'sans-serif'],
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
<body class="bg-white dark:bg-cinema text-gray-900 dark:text-white font-bangla">

    <!-- Top Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/90 dark:bg-cinema/90 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Brand -->
                <div class="flex items-center space-x-3">
                    <!-- Premium Movie Icon -->
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-2xl flex items-center justify-center shadow-lg">
                            <span class="text-2xl">🍿</span>
                        </div>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-accent rounded-full animate-pulse"></div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            MSR Reviews
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">আধুনিক সিনেমা হাব</p>
                    </div>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="#home" class="text-primary font-semibold">হোম</a>
                    <a href="/reviews" class="hover:text-primary transition-colors">রিভিউ</a>
                    <a href="/categories" class="hover:text-primary transition-colors">ক্যাটেগরি</a>
                    <a href="/about" class="hover:text-primary transition-colors">সম্পর্কে</a>
                    <a href="/contact" class="hover:text-primary transition-colors">যোগাযোগ</a>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-4">
                    <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <button class="bg-gradient-to-r from-primary to-secondary text-white px-6 py-2 rounded-xl font-semibold hover:shadow-lg transition-all">
                        যোগদান করুন
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
                                        সর্বশেষ মুভি ও সিরিয়াল এক্সপ্লোর করুন, বিশেষজ্ঞ রিভিউ পড়ুন এবং 
                                        আপনার পরবর্তী প্রিয় ফিল্ম খুঁজে নিন আমাদের আধুনিক সিনেমা হাবে।
                                    </p>
                                    
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <button class="group bg-primary text-white px-8 py-4 rounded-2xl font-semibold text-lg hover:bg-primary/90 transition-all flex items-center justify-center">
                                            <span class="text-xl mr-2">🎬</span>
                                            এখনই দেখুন
                                        </button>
                                        <button class="border-2 border-primary text-primary dark:text-white hover:bg-primary hover:text-white px-8 py-4 rounded-2xl font-semibold text-lg transition-all">
                                            লাইব্রেরি ব্রাউজ করুন
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Featured Review Card -->
                                <?php if (!empty($featured_reviews)): ?>
                                    <div class="relative">
                                        <?php $hero_review = $featured_reviews[0]; ?>
                                        <div class="card-3d bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-2xl">
                                            <div class="relative h-80">
                                                <?php if ($hero_review['poster_image']): ?>
                                                    <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $hero_review['poster_image']; ?>" 
                                                         alt="<?php echo htmlspecialchars($hero_review['title']); ?>"
                                                         class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-400 flex items-center justify-center">
                                                        <span class="text-6xl">🎬</span>
                                                    </div>
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
                                                    ⭐ <?php echo $hero_review['rating']; ?>
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
                                                
                                                <a href="/review/<?php echo $hero_review['slug']; ?>" class="w-full bg-gradient-to-r from-primary to-secondary text-white py-3 rounded-xl font-semibold hover:shadow-lg transition-all text-center block">
                                                    রিভিউ পড়ুন
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Featured Reviews Grid -->
                    <section>
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-3xl font-bold">ফিচার্ড রিভিউ</h2>
                            <a href="/reviews" class="text-primary hover:text-primary/80 font-semibold flex items-center">
                                সব দেখুন
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                        
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach (array_slice($featured_reviews, 1, 6) as $review): ?>
                                <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 group">
                                    <div class="relative h-48 overflow-hidden">
                                        <?php if ($review['poster_image']): ?>
                                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($review['title']); ?>"
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        <?php else: ?>
                                            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-400 flex items-center justify-center">
                                                <span class="text-4xl">🎬</span>
                                            </div>
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
                                            ⭐ <?php echo $review['rating']; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="bg-primary/10 text-primary px-2 py-1 rounded-full text-xs font-medium">
                                                <?php echo $review['category_name_bn'] ?: $review['category_name']; ?>
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

                    <!-- Categories Grid -->
                    <section>
                        <h2 class="text-3xl font-bold mb-8">ঘরানা অনুযায়ী ব্রাউজ করুন</h2>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <?php 
                            $category_colors = [
                                'from-red-500 to-orange-500',
                                'from-blue-500 to-purple-500',
                                'from-yellow-500 to-pink-500',
                                'from-purple-500 to-red-500',
                                'from-cyan-500 to-blue-500',
                                'from-pink-500 to-rose-500'
                            ];
                            foreach ($categories as $index => $category): 
                                $color = $category_colors[$index % count($category_colors)];
                            ?>
                                <a href="/category/<?php echo $category['slug']; ?>" class="group relative bg-gradient-to-br <?php echo $color; ?> rounded-2xl p-6 text-white cursor-pointer hover:scale-105 transition-transform">
                                    <div class="relative z-10">
                                        <h3 class="text-xl font-bold mb-2"><?php echo $category['name_bn'] ?: $category['name']; ?></h3>
                                        <p class="text-white/80 text-sm"><?php echo number_format($category['review_count']); ?> টি রিভিউ</p>
                                    </div>
                                    
                                    <!-- Background Icon -->
                                    <div class="absolute bottom-2 right-2 opacity-20">
                                        <span class="text-4xl">
                                            <?php 
                                            $icons = ['🎬', '🎭', '😂', '😱', '🚀', '💕'];
                                            echo $icons[$index % count($icons)];
                                            ?>
                                        </span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>

                </div>

                <!-- Sidebar -->
                <aside class="sidebar-sticky space-y-8">
                    
                    <!-- Search Widget -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                        <h3 class="text-xl font-bold mb-4">দ্রুত সার্চ</h3>
                        <div class="relative">
                            <input type="text" 
                                   placeholder="মুভি খুঁজুন..." 
                                   class="w-full px-4 py-3 text-base bg-gray-100 dark:bg-gray-700 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary">
                            <button class="absolute right-2 top-1/2 transform -translate-y-1/2 p-2 text-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Top Rated Reviews -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold">টপ রেটেড</h3>
                            <span class="bg-accent/10 text-accent px-2 py-1 rounded-full text-xs font-semibold">HOT</span>
                        </div>
                        
                        <div class="space-y-3">
                            <?php foreach (array_slice($top_rated, 0, 5) as $index => $review): ?>
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
                                            <span class="text-accent font-semibold">⭐ <?php echo $review['rating']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Coming Soon -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center space-x-2 mb-4">
                            <h3 class="text-xl font-bold">শীঘ্রই আসছে</h3>
                            <div class="w-2 h-2 bg-secondary rounded-full animate-pulse"></div>
                        </div>
                        
                        <div class="space-y-4">
                            <?php foreach ($coming_soon as $release): ?>
                                <div class="border-l-4 border-primary pl-4">
                                    <h4 class="font-semibold"><?php echo $release['title']; ?></h4>
                                    <div class="flex items-center justify-between text-sm text-gray-500 mt-1">
                                        <span><?php echo $release['genre']; ?></span>
                                        <span class="bg-primary/10 text-primary px-2 py-1 rounded-full text-xs font-medium">
                                            <?php echo $release['coming']; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Newsletter Signup -->
                    <div class="bg-gradient-to-br from-primary to-secondary rounded-2xl p-6 text-white">
                        <h3 class="text-xl font-bold mb-3">আপডেট পান</h3>
                        <p class="text-white/80 text-sm mb-4">সর্বশেষ মুভি রিভিউ এবং সুপারিশ পেতে সাবস্ক্রাইব করুন।</p>
                        
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

    <!-- Footer -->
    <footer class="bg-gray-100 dark:bg-gray-900 py-16 mt-20">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-2xl flex items-center justify-center">
                            <span class="text-xl">🍿</span>
                        </div>
                        <span class="text-xl font-bold">MSR Reviews</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">বাংলা ভাষায় প্রিমিয়াম সিনেমা এক্সপেরিয়েন্স এবং মুভি রিভিউর আধুনিক গন্তব্য।</p>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">দ্রুত লিংক</h4>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li><a href="/reviews" class="hover:text-primary transition-colors">রিভিউ</a></li>
                        <li><a href="/categories" class="hover:text-primary transition-colors">ক্যাটেগরি</a></li>
                        <li><a href="/about" class="hover:text-primary transition-colors">সম্পর্কে</a></li>
                        <li><a href="/contact" class="hover:text-primary transition-colors">যোগাযোগ</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">সাপোর্ট</h4>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li><a href="#" class="hover:text-primary transition-colors">হেল্প সেন্টার</a></li>
                        <li><a href="/contact" class="hover:text-primary transition-colors">যোগাযোগ</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">প্রাইভেসি</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">শর্তাবলী</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">কানেক্ট</h4>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 bg-primary/20 rounded-xl flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-primary/20 rounded-xl flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-primary/20 rounded-xl flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.719-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.347-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-200 dark:border-gray-800 mt-12 pt-8 text-center text-gray-600 dark:text-gray-400">
                <p>&copy; ২০২৪ MSR Reviews. সকল অধিকার সংরক্ষিত। মুভি প্রেমীদের জন্য ❤️ দিয়ে তৈরি।</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

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

        // Search functionality
        const searchInput = document.querySelector('input[type="text"]');
        searchInput?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                window.location.href = '/search?q=' + encodeURIComponent(this.value);
            }
        });
    </script>
</body>
</html>