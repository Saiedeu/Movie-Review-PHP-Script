<?php
/**
 * MSR Homepage - Design 1: Cinematic Minimalism
 * Modern floating elements with movie-themed graphics
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config.php';
require_once 'functions.php';

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
$seo_description = getSiteSetting('site_description', 'বাংলা ভাষায় সিনেমা ও সিরিয়াল রিভিউ');

// Include header
include 'includes/header.php';
?>

<style>
/* Cinematic Minimalism Styles */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gold-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 4%, #4facfe 100%);
    --dark-gradient: linear-gradient(135deg, #0c0c0c 0%, #1a1a2e 100%);
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-20px) rotate(5deg); }
    66% { transform: translateY(-10px) rotate(-5deg); }
}

@keyframes floatReverse {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(20px) rotate(-5deg); }
    66% { transform: translateY(10px) rotate(5deg); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.7; }
    50% { transform: scale(1.1); opacity: 1; }
}

@keyframes slideInUp {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes slideInLeft {
    from { transform: translateX(-50px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideInRight {
    from { transform: translateX(50px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.animate-float { animation: float 6s ease-in-out infinite; }
.animate-float-reverse { animation: floatReverse 8s ease-in-out infinite; }
.animate-pulse-soft { animation: pulse 4s ease-in-out infinite; }
.animate-slide-up { animation: slideInUp 0.8s ease-out forwards; }
.animate-slide-left { animation: slideInLeft 0.8s ease-out forwards; }
.animate-slide-right { animation: slideInRight 0.8s ease-out forwards; }

.glass-morphism {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.text-gradient {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.text-gold {
    background: var(--gold-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.btn-premium {
    background: var(--primary-gradient);
    border: none;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.btn-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.btn-premium::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-premium:hover::before {
    left: 100%;
}

.movie-card {
    position: relative;
    overflow: hidden;
    border-radius: 20px;
    transition: all 0.4s ease;
}

.movie-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 25px 50px rgba(0,0,0,0.2);
}

.movie-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 1;
}

.movie-card:hover::before {
    opacity: 1;
}

.cinema-icon {
    width: 60px;
    height: 60px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.5;
    filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
}

.floating-element {
    position: absolute;
    pointer-events: none;
    user-select: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title { font-size: 3rem !important; }
    .hero-subtitle { font-size: 1.2rem !important; }
    .floating-element { display: none; }
}
</style>

<!-- Hero Section - Cinematic Minimalism -->
<section class="relative min-h-screen flex items-center bg-gradient-to-br from-gray-50 via-white to-gray-100 overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <!-- Floating Movie Elements -->
        <div class="floating-element top-20 left-10 animate-float">
            <!-- Popcorn SVG -->
            <svg class="w-16 h-16 text-red-400 opacity-60" viewBox="0 0 100 100" fill="currentColor">
                <path d="M25 35c0-5.5 4.5-10 10-10s10 4.5 10 10M35 25c0-5.5 4.5-10 10-10s10 4.5 10 10M45 35c0-5.5 4.5-10 10-10s10 4.5 10 10M55 25c0-5.5 4.5-10 10-10s10 4.5 10 10M65 35c0-5.5 4.5-10 10-10s10 4.5 10 10"/>
                <rect x="20" y="35" width="60" height="45" rx="8" fill="currentColor"/>
                <rect x="22" y="75" width="56" height="8" rx="4" fill="#ff6b6b"/>
            </svg>
        </div>
        
        <div class="floating-element top-32 right-20 animate-float-reverse">
            <!-- Film Reel SVG -->
            <svg class="w-20 h-20 text-blue-500 opacity-50" viewBox="0 0 100 100" fill="currentColor">
                <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="3"/>
                <circle cx="50" cy="50" r="15" fill="currentColor"/>
                <circle cx="30" cy="30" r="8" fill="none" stroke="currentColor" stroke-width="2"/>
                <circle cx="70" cy="30" r="8" fill="none" stroke="currentColor" stroke-width="2"/>
                <circle cx="30" cy="70" r="8" fill="none" stroke="currentColor" stroke-width="2"/>
                <circle cx="70" cy="70" r="8" fill="none" stroke="currentColor" stroke-width="2"/>
            </svg>
        </div>
        
        <div class="floating-element bottom-32 left-32 animate-pulse-soft">
            <!-- Camera SVG -->
            <svg class="w-14 h-14 text-purple-500 opacity-40" viewBox="0 0 100 100" fill="currentColor">
                <rect x="15" y="35" width="70" height="45" rx="8" fill="currentColor"/>
                <rect x="25" y="25" width="20" height="15" rx="4" fill="currentColor"/>
                <circle cx="50" cy="57" r="15" fill="none" stroke="white" stroke-width="3"/>
                <circle cx="50" cy="57" r="10" fill="white"/>
                <rect x="70" y="40" width="8" height="5" rx="2" fill="white"/>
            </svg>
        </div>
        
        <div class="floating-element top-64 left-1/3 animate-float">
            <!-- Movie Ticket SVG -->
            <svg class="w-12 h-20 text-yellow-500 opacity-50" viewBox="0 0 60 100" fill="currentColor">
                <rect x="5" y="10" width="50" height="80" rx="8" fill="currentColor"/>
                <circle cx="30" cy="35" r="3" fill="white"/>
                <circle cx="30" cy="50" r="3" fill="white"/>
                <circle cx="30" cy="65" r="3" fill="white"/>
                <rect x="15" y="75" width="30" height="2" rx="1" fill="white"/>
                <rect x="15" y="80" width="20" height="2" rx="1" fill="white"/>
            </svg>
        </div>
        
        <div class="floating-element bottom-40 right-32 animate-float-reverse">
            <!-- Clapperboard SVG -->
            <svg class="w-16 h-12 text-gray-600 opacity-40" viewBox="0 0 100 75" fill="currentColor">
                <rect x="10" y="25" width="80" height="40" rx="5" fill="currentColor"/>
                <rect x="10" y="10" width="80" height="20" rx="5" fill="currentColor"/>
                <rect x="15" y="12" width="15" height="16" fill="white"/>
                <rect x="35" y="12" width="15" height="16" fill="black"/>
                <rect x="55" y="12" width="15" height="16" fill="white"/>
                <rect x="75" y="12" width="15" height="16" fill="black"/>
            </svg>
        </div>
        
        <!-- Geometric Shapes -->
        <div class="absolute top-1/4 right-1/4 w-32 h-32 bg-gradient-to-br from-pink-200 to-purple-200 rounded-full opacity-20 animate-pulse-soft"></div>
        <div class="absolute bottom-1/3 left-1/5 w-24 h-24 bg-gradient-to-br from-blue-200 to-indigo-200 rounded-xl opacity-30 animate-float transform rotate-45"></div>
        <div class="absolute top-1/2 right-1/3 w-16 h-16 bg-gradient-to-br from-yellow-200 to-orange-200 rounded-full opacity-25 animate-float-reverse"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <!-- Content Side -->
            <div class="space-y-8 animate-slide-left">
                <!-- Premium Badge -->
                <div class="inline-flex items-center glass-morphism rounded-full px-6 py-3 text-sm font-semibold text-gray-700 shadow-lg">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3 animate-pulse"></div>
                    বিশ্বস্ত সিনেমা ও সিরিয়াল রিভিউ প্ল্যাটফর্ম
                </div>
                
                <!-- Main Heading -->
                <h1 class="hero-title text-6xl lg:text-8xl font-black leading-tight">
                    <span class="text-gray-900">সেরা</span>
                    <br>
                    <span class="text-gradient">সিনেমা ও</span>
                    <br>
                    <span class="text-gold">সিরিয়াল রিভিউ</span>
                </h1>
                
                <!-- Subtitle -->
                <p class="hero-subtitle text-xl lg:text-2xl text-gray-600 leading-relaxed max-w-2xl">
                    বাংলা ভাষায় বিশ্বের সেরা সিনেমা ও সিরিয়ালের গভীর বিশ্লেষণ, 
                    নিরপেক্ষ রিভিউ এবং সিনেমাপ্রেমীদের জন্য বিশেষ গাইড।
                </p>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button class="btn-premium text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-lg transform transition-all duration-300">
                        <span class="relative z-10 flex items-center justify-center">
                            সকল রিভিউ দেখুন
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-5 3a9 9 0 110-18 9 9 0 010 18z"></path>
                            </svg>
                        </span>
                    </button>
                    
                    <button class="glass-morphism border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-2xl font-bold text-lg hover:bg-white/30 transition-all duration-300 transform hover:scale-105">
                        <span class="flex items-center justify-center">
                            রিভিউ লিখুন
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
                
                <!-- Live Stats -->
                <div class="grid grid-cols-3 gap-6 pt-8">
                    <div class="text-center glass-morphism rounded-2xl p-4">
                        <div class="text-3xl font-black text-gradient">
                            <?php echo $db->count('reviews', 'status = ?', ['published']) ?: '150'; ?>+
                        </div>
                        <div class="text-gray-600 text-sm font-medium">সিনেমা রিভিউ</div>
                    </div>
                    <div class="text-center glass-morphism rounded-2xl p-4">
                        <div class="text-3xl font-black text-gradient">
                            <?php echo $db->count('categories', 'status = ?', ['active']) ?: '25'; ?>+
                        </div>
                        <div class="text-gray-600 text-sm font-medium">ক্যাটেগরি</div>
                    </div>
                    <div class="text-center glass-morphism rounded-2xl p-4">
                        <div class="text-3xl font-black text-gradient">
                            <?php echo number_format($db->fetch("SELECT SUM(view_count) as total FROM reviews")['total'] ?? 50000); ?>+
                        </div>
                        <div class="text-gray-600 text-sm font-medium">মোট ভিউ</div>
                    </div>
                </div>
            </div>
            
            <!-- Visual Side -->
            <div class="relative animate-slide-right">
                <div class="relative max-w-lg mx-auto">
                    <!-- Main Featured Review Card -->
                    <?php if (!empty($featured_reviews)): ?>
                        <?php $main_review = $featured_reviews[0]; ?>
                        <div class="movie-card bg-white shadow-2xl overflow-hidden relative z-20">
                            <div class="relative h-96 overflow-hidden">
                                <?php if ($main_review['poster_image']): ?>
                                    <img src="<?php echo REVIEW_UPLOAD_URL . '/' . $main_review['poster_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($main_review['title']); ?>" 
                                         class="w-full h-full object-cover transition-transform duration-700 hover:scale-110">
                                <?php else: ?>
                                    <div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-600 flex items-center justify-center">
                                        <svg class="cinema-icon text-white opacity-50" viewBox="0 0 24 24">
                                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                            <line x1="8" y1="21" x2="16" y2="21"/>
                                            <line x1="12" y1="17" x2="12" y2="21"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Premium Rating Badge -->
                                <div class="absolute top-6 right-6 glass-morphism text-white px-4 py-2 rounded-full font-bold backdrop-blur-md">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <?php echo formatRating($main_review['rating']); ?>
                                    </span>
                                </div>
                                
                                <!-- Category Badge -->
                                <div class="absolute top-6 left-6 bg-black/50 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium">
                                    <?php echo $main_review['category_name_bn'] ?: $main_review['category_name']; ?>
                                </div>
                                
                                <!-- Play Button Overlay -->
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300 bg-black/20">
                                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center transform transition-transform hover:scale-110">
                                        <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-8 relative z-10">
                                <div class="flex items-center space-x-3 mb-4">
                                    <span class="bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm px-4 py-2 rounded-full font-medium">
                                        <?php echo $main_review['content_type'] === 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?>
                                    </span>
                                    <span class="text-gray-400">•</span>
                                    <span class="text-gray-600 font-medium"><?php echo $main_review['year']; ?></span>
                                </div>
                                
                                <h3 class="text-2xl font-black text-gray-900 mb-4 leading-tight">
                                    <?php echo htmlspecialchars($main_review['title']); ?>
                                </h3>
                                
                                <p class="text-gray-600 leading-relaxed mb-6 text-lg">
                                    <?php echo htmlspecialchars(generateExcerpt($main_review['summary'] ?: $main_review['review_body'], 120)); ?>...
                                </p>
                                
                                <a href="/review/<?php echo $main_review['slug']; ?>" 
                                   class="inline-flex items-center text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 font-bold text-lg hover:from-purple-600 hover:to-blue-600 transition-all duration-300">
                                    সম্পূর্ণ রিভিউ পড়ুন
                                    <svg class="w-5 h-5 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Floating Mini Cards -->
                    <?php if (count($featured_reviews) > 1): ?>
                        <div class="absolute -top-12 -left-16 movie-card glass-morphism p-4 z-10" style="animation-delay: 1s;">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-16 bg-gray-200 rounded-xl overflow-hidden">
                                    <?php if ($featured_reviews[1]['poster_image']): ?>
                                        <img src="<?php echo REVIEW_UPLOAD_URL . '/' . $featured_reviews[1]['poster_image']; ?>" 
                                             alt="<?php echo htmlspecialchars($featured_reviews[1]['title']); ?>" 
                                             class="w-full h-full object-cover">
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm leading-tight">
                                        <?php echo htmlspecialchars(truncateText($featured_reviews[1]['title'], 20)); ?>
                                    </h4>
                                    <div class="flex text-yellow-400 text-xs mt-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?php echo $i <= $featured_reviews[1]['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (count($featured_reviews) > 2): ?>
                        <div class="absolute -bottom-12 -right-16 movie-card glass-morphism p-4 z-10" style="animation-delay: 2s;">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-16 bg-gray-200 rounded-xl overflow-hidden">
                                    <?php if ($featured_reviews[2]['poster_image']): ?>
                                        <img src="<?php echo REVIEW_UPLOAD_URL . '/' . $featured_reviews[2]['poster_image']; ?>" 
                                             alt="<?php echo htmlspecialchars($featured_reviews[2]['title']); ?>" 
                                             class="w-full h-full object-cover">
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm leading-tight">
                                        <?php echo htmlspecialchars(truncateText($featured_reviews[2]['title'], 20)); ?>
                                    </h4>
                                    <div class="flex text-yellow-400 text-xs mt-1">
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
    
    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <div class="w-8 h-12 border-2 border-gray-400 rounded-full flex justify-center backdrop-blur-sm bg-white/20">
            <div class="w-1 h-3 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full mt-2 animate-pulse"></div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="py-20 bg-white relative overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-10 left-10 w-20 h-20 bg-blue-100 rounded-full opacity-50 animate-float"></div>
        <div class="absolute bottom-10 right-10 w-16 h-16 bg-purple-100 rounded-full opacity-50 animate-float-reverse"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <div class="animate-slide-up">
                <h2 class="text-4xl lg:text-5xl font-black text-gray-900 mb-6 leading-tight">
                    আপনার <span class="text-gradient">স্বপ্নের</span> সিনেমা খুঁজুন
                </h2>
                <p class="text-xl text-gray-600 mb-12 leading-relaxed">
                    হাজার হাজার সিনেমা ও সিরিয়ালের মধ্য থেকে আপনার পছন্দের কন্টেন্ট খুঁজে নিন
                </p>
            </div>
            
            <!-- Premium Search Form -->
            <div class="relative mb-12 animate-slide-up" style="animation-delay: 0.2s;">
                <form action="/search" method="GET" class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-300"></div>
                    <div class="relative bg-white rounded-3xl p-2 shadow-2xl">
                        <div class="flex items-center">
                            <div class="flex-1 flex items-center px-6">
                                <svg class="w-6 h-6 text-gray-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" 
                                       name="q" 
                                       placeholder="সিনেমা বা সিরিয়ালের নাম লিখুন..." 
                                       class="flex-1 text-xl py-4 bg-transparent border-none outline-none text-gray-700 placeholder-gray-400">
                            </div>
                            <button type="submit" 
                                    class="btn-premium text-white px-8 py-4 rounded-2xl font-bold text-lg mr-2">
                                <span class="relative z-10">খুঁজুন</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Filter Pills -->
            <div class="flex flex-wrap justify-center gap-4 animate-slide-up" style="animation-delay: 0.4s;">
                <a href="/reviews" class="glass-morphism hover:bg-white/40 text-gray-700 px-6 py-3 rounded-full font-semibold transition-all transform hover:scale-105">সকল</a>
                <a href="/reviews?type=movie" class="glass-morphism hover:bg-white/40 text-gray-700 px-6 py-3 rounded-full font-semibold transition-all transform hover:scale-105">সিনেমা</a>
                <a href="/reviews?type=series" class="glass-morphism hover:bg-white/40 text-gray-700 px-6 py-3 rounded-full font-semibold transition-all transform hover:scale-105">সিরিয়াল</a>
                <a href="/reviews?language=bangla" class="glass-morphism hover:bg-white/40 text-gray-700 px-6 py-3 rounded-full font-semibold transition-all transform hover:scale-105">বাংলা</a>
                <a href="/reviews?language=korean" class="glass-morphism hover:bg-white/40 text-gray-700 px-6 py-3 rounded-full font-semibold transition-all transform hover:scale-105">কোরিয়ান</a>
                <a href="/reviews?language=hollywood" class="glass-morphism hover:bg-white/40 text-gray-700 px-6 py-3 rounded-full font-semibold transition-all transform hover:scale-105">হলিউড</a>
            </div>
        </div>
    </div>
</section>

<!-- Latest Reviews Section -->
<?php if (!empty($latest_reviews)): ?>
<section class="py-20 bg-gradient-to-br from-gray-50 to-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-black text-gray-900 mb-6">সর্বশেষ রিভিউ</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                আমাদের সর্বশেষ যোগ করা সিনেমা ও সিরিয়াল রিভিউগুলো দেখুন
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach (array_slice($latest_reviews, 0, 8) as $index => $review): ?>
                <div class="movie-card bg-white shadow-xl overflow-hidden group" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                    <div class="relative h-64 overflow-hidden">
                        <?php if ($review['poster_image']): ?>
                            <img src="<?php echo REVIEW_UPLOAD_URL . '/' . $review['poster_image']; ?>" 
                                 alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-400 flex items-center justify-center">
                                <svg class="cinema-icon text-gray-500" viewBox="0 0 24 24">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                    <line x1="8" y1="21" x2="16" y2="21"/>
                                    <line x1="12" y1="17" x2="12" y2="21"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Premium Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <!-- Rating Badge -->
                        <div class="absolute top-4 right-4 glass-morphism text-white px-3 py-1 rounded-full text-sm font-bold">
                            <span class="flex items-center">
                                <svg class="w-3 h-3 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <?php echo formatRating($review['rating']); ?>
                            </span>
                        </div>
                        
                        <!-- Type Badge -->
                        <div class="absolute top-4 left-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-3 py-1 rounded-full text-xs font-bold">
                            <?php echo $review['content_type'] === 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?>
                        </div>
                        
                        <!-- Play Button -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="w-16 h-16 glass-morphism rounded-full flex items-center justify-center transform transition-transform hover:scale-110">
                                <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 relative z-10">
                        <h3 class="font-black text-gray-900 text-lg mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">
                            <a href="/review/<?php echo $review['slug']; ?>">
                                <?php echo htmlspecialchars($review['title']); ?>
                            </a>
                        </h3>
                        
                        <div class="flex items-center text-gray-500 text-sm mb-3 font-medium">
                            <span><?php echo $review['year']; ?></span>
                            <span class="mx-2">•</span>
                            <span><?php echo $review['language']; ?></span>
                        </div>
                        
                        <p class="text-gray-600 text-sm leading-relaxed">
                            <?php echo htmlspecialchars(generateExcerpt($review['summary'] ?: $review['review_body'], 80)); ?>...
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <button class="btn-premium text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-lg">
                <span class="relative z-10 flex items-center justify-center">
                    সব রিভিউ দেখুন
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </span>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Categories Section -->
<?php if (!empty($categories)): ?>
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-black text-gray-900 mb-6">ক্যাটেগরি</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                আপনার পছন্দের ধরণের কন্টেন্ট খুঁজে নিন
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
            <?php foreach ($categories as $index => $category): ?>
                <a href="/category/<?php echo $category['slug']; ?>" 
                   class="group glass-morphism hover:bg-white/60 rounded-3xl p-8 text-center transition-all duration-300 transform hover:scale-105 hover:shadow-2xl" 
                   style="animation-delay: <?php echo $index * 0.1; ?>s;">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <?php if ($category['icon']): ?>
                            <i class="<?php echo $category['icon']; ?> text-2xl text-white"></i>
                        <?php else: ?>
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <h3 class="font-black text-gray-900 group-hover:text-blue-600 transition-colors mb-2 text-lg">
                        <?php echo $category['name_bn'] ?: $category['name']; ?>
                    </h3>
                    <p class="text-gray-500 text-sm font-medium">
                        <?php echo $db->count("SELECT COUNT(*) FROM review_categories rc JOIN reviews r ON rc.review_id = r.id WHERE rc.category_id = ? AND r.status = 'published'", [$category['id']]) ?: '0'; ?> টি রিভিউ
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Call to Action Section -->
<section class="py-20 bg-gradient-to-r from-gray-900 via-black to-gray-900 relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0">
        <div class="absolute top-20 left-20 w-32 h-32 bg-blue-500 rounded-full opacity-10 animate-float"></div>
        <div class="absolute bottom-20 right-20 w-24 h-24 bg-purple-500 rounded-full opacity-10 animate-float-reverse"></div>
        <div class="absolute top-1/2 left-1/2 w-40 h-40 bg-pink-500 rounded-full opacity-5 animate-pulse-soft transform -translate-x-1/2 -translate-y-1/2"></div>
    </div>
    
    <div class="container mx-auto px-4 text-center relative z-10">
        <div class="max-w-4xl mx-auto text-white space-y-8">
            <h2 class="text-4xl lg:text-6xl font-black leading-tight">
                আপনার মতামত <span class="text-gradient">শেয়ার করুন</span>
            </h2>
            <p class="text-xl lg:text-2xl text-gray-300 leading-relaxed">
                দেখেছেন কোনো দুর্দান্ত সিনেমা বা সিরিয়াল? আপনার অভিজ্ঞতা অন্যদের সাথে শেয়ার করুন 
                এবং আমাদের সিনেমাপ্রেমী কমিউনিটির অংশ হয়ে উঠুন।
            </p>
            
            <div class="flex flex-col sm:flex-row gap-6 justify-center">
                <button class="bg-white text-black px-8 py-4 rounded-2xl font-black text-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-xl">
                    এখনই রিভিউ জমা দিন
                </button>
                <button class="glass-morphism border-2 border-white/30 hover:border-white text-white hover:bg-white/10 px-8 py-4 rounded-2xl font-black text-lg transition-all duration-300 transform hover:scale-105">
                    আরো জানুন
                </button>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>