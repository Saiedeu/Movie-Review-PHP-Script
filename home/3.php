<?php
/**
 * MSR Homepage - Design 1: Cinematic Elegance
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

// Include header
include 'includes/header.php';
?>

<style>
/* Advanced animations for cinematic theme */
@keyframes filmRoll {
    0% { transform: translateX(-100%) rotate(0deg); }
    100% { transform: translateX(100vw) rotate(360deg); }
}

@keyframes popcornFloat {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    25% { transform: translateY(-20px) rotate(90deg); }
    50% { transform: translateY(-10px) rotate(180deg); }
    75% { transform: translateY(-30px) rotate(270deg); }
}

@keyframes cameraFlash {
    0%, 90%, 100% { opacity: 0; transform: scale(1); }
    5%, 85% { opacity: 1; transform: scale(1.2); }
}

@keyframes tvStatic {
    0%, 100% { opacity: 0.1; }
    50% { opacity: 0.3; }
}

@keyframes spotlight {
    0%, 100% { opacity: 0.3; transform: translateX(-50%) scale(1); }
    50% { opacity: 0.6; transform: translateX(-50%) scale(1.2); }
}

@keyframes curtainReveal {
    0% { transform: scaleY(1); }
    100% { transform: scaleY(0); }
}

.film-strip {
    background: linear-gradient(90deg, 
        #1a1a1a 0%, #1a1a1a 10%, 
        transparent 10%, transparent 20%,
        #1a1a1a 20%, #1a1a1a 30%,
        transparent 30%, transparent 40%,
        #1a1a1a 40%, #1a1a1a 50%,
        transparent 50%, transparent 60%,
        #1a1a1a 60%, #1a1a1a 70%,
        transparent 70%, transparent 80%,
        #1a1a1a 80%, #1a1a1a 90%,
        transparent 90%, transparent 100%
    );
    height: 60px;
    animation: filmRoll 20s linear infinite;
}

.cinema-bg {
    background: 
        radial-gradient(ellipse at top, rgba(168, 85, 247, 0.1) 0%, transparent 50%),
        radial-gradient(ellipse at bottom left, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
        radial-gradient(ellipse at bottom right, rgba(236, 72, 153, 0.1) 0%, transparent 50%),
        linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
}

.spotlight-effect {
    background: radial-gradient(ellipse 800px 600px at 50% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: spotlight 8s ease-in-out infinite;
}

.movie-icon {
    font-size: 2.5rem;
    animation: popcornFloat 4s ease-in-out infinite;
}

.camera-flash {
    animation: cameraFlash 6s ease-in-out infinite;
}

.tv-static {
    animation: tvStatic 2s ease-in-out infinite;
}

.film-reel {
    width: 120px;
    height: 120px;
    border: 8px solid rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    position: relative;
    animation: rotate 10s linear infinite;
}

.film-reel::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 30px;
    height: 30px;
    border: 3px solid rgba(255, 255, 255, 0.2);
    border-radius: 50%;
}

.film-reel::after {
    content: '';
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    width: 8px;
    height: 8px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    box-shadow: 
        0 25px 0 rgba(255, 255, 255, 0.2),
        0 50px 0 rgba(255, 255, 255, 0.2),
        0 75px 0 rgba(255, 255, 255, 0.2),
        25px 10px 0 rgba(255, 255, 255, 0.2),
        50px 25px 0 rgba(255, 255, 255, 0.2),
        75px 50px 0 rgba(255, 255, 255, 0.2),
        25px 75px 0 rgba(255, 255, 255, 0.2),
        -25px 10px 0 rgba(255, 255, 255, 0.2),
        -50px 25px 0 rgba(255, 255, 255, 0.2),
        -75px 50px 0 rgba(255, 255, 255, 0.2),
        -25px 75px 0 rgba(255, 255, 255, 0.2);
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.gradient-text {
    background: linear-gradient(135deg, #FFD700 0%, #FFA500 50%, #FF6B6B 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.glow-effect {
    box-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
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

.floating-elements {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
}

.floating-icon {
    position: absolute;
    font-size: 2rem;
    opacity: 0.1;
    color: #FFD700;
}

.floating-icon:nth-child(1) { top: 10%; left: 10%; animation: popcornFloat 3s ease-in-out infinite; animation-delay: 0s; }
.floating-icon:nth-child(2) { top: 20%; right: 15%; animation: popcornFloat 4s ease-in-out infinite; animation-delay: 1s; }
.floating-icon:nth-child(3) { top: 60%; left: 5%; animation: popcornFloat 5s ease-in-out infinite; animation-delay: 2s; }
.floating-icon:nth-child(4) { top: 70%; right: 10%; animation: popcornFloat 3.5s ease-in-out infinite; animation-delay: 3s; }
.floating-icon:nth-child(5) { top: 40%; left: 80%; animation: popcornFloat 4.5s ease-in-out infinite; animation-delay: 4s; }
.floating-icon:nth-child(6) { top: 80%; left: 70%; animation: popcornFloat 3.2s ease-in-out infinite; animation-delay: 5s; }
</style>

<!-- Hero Section - Cinematic Theme -->
<section class="relative min-h-screen flex items-center justify-center cinema-bg overflow-hidden">
    <!-- Film strip decoration -->
    <div class="absolute top-0 left-0 w-full film-strip opacity-20"></div>
    <div class="absolute bottom-0 left-0 w-full film-strip opacity-20" style="animation-direction: reverse; animation-delay: -10s;"></div>
    
    <!-- Spotlight effect -->
    <div class="absolute inset-0 spotlight-effect"></div>
    
    <!-- Floating movie-themed icons -->
    <div class="floating-elements">
        <div class="floating-icon">🍿</div>
        <div class="floating-icon">🎥</div>
        <div class="floating-icon">🎬</div>
        <div class="floating-icon">📺</div>
        <div class="floating-icon">🎞️</div>
        <div class="floating-icon">🎭</div>
    </div>
    
    <!-- Movie reel decorations -->
    <div class="absolute top-20 left-10 film-reel opacity-10"></div>
    <div class="absolute bottom-20 right-10 film-reel opacity-10" style="animation-direction: reverse; animation-delay: -5s;"></div>
    
    <!-- Main Content -->
    <div class="container-custom relative z-20">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <!-- Content Side -->
            <div class="text-center lg:text-left space-y-8">
                <!-- Cinema badge -->
                <div class="inline-flex items-center bg-black/30 backdrop-blur-lg rounded-full px-8 py-4 text-white/90 text-sm font-medium border border-white/10 glow-effect">
                    <span class="movie-icon mr-4">🎬</span>
                    <span class="flex items-center">
                        <span class="w-2 h-2 bg-red-500 rounded-full mr-3 animate-pulse"></span>
                        সিনেমা ও সিরিয়াল রিভিউ প্ল্যাটফর্ম
                    </span>
                </div>
                
                <!-- Main Heading with cinematic style -->
                <h1 class="text-6xl lg:text-8xl font-black leading-none text-white mb-6">
                    <span className="block">বিশ্বের</span>
                    <span className="gradient-text block">সেরা বিনোদন</span>
                    <span className="text-gray-300 block text-5xl lg:text-6xl">এক জায়গায়</span>
                </h1>
                
                <!-- Subtitle with typewriter effect -->
                <div class="space-y-4">
                    <p class="text-2xl lg:text-3xl text-white/90 leading-relaxed">
                        🍿 <span class="font-semibold">পপকর্ন খেতে খেতে</span> পড়ুন
                    </p>
                    <p class="text-xl lg:text-2xl text-white/70 leading-relaxed max-w-2xl">
                        বাংলা ভাষায় বিশ্বের সেরা সিনেমা ও সিরিয়ালের বিস্তারিত রিভিউ, রেটিং এবং বিশ্লেষণ।
                        আপনার পরবর্তী দেখার তালিকা তৈরি করুন আমাদের সাথে।
                    </p>
                </div>
                
                <!-- Action Buttons with movie theme -->
                <div class="flex flex-col sm:flex-row gap-6 justify-center lg:justify-start">
                    <a href="/reviews" class="group relative bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 text-white px-10 py-5 rounded-2xl font-bold text-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
                        <span class="relative z-10 flex items-center justify-center">
                            🎭 শুরু করুন
                            <svg class="w-6 h-6 ml-3 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 via-orange-500 to-yellow-400 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </a>
                    
                    <a href="/submit-review" class="group bg-black/40 backdrop-blur-lg border-2 border-white/20 text-white px-10 py-5 rounded-2xl font-bold text-lg hover:border-white/40 hover:bg-black/60 transition-all duration-300 flex items-center justify-center">
                        📝 রিভিউ লিখুন
                        <svg class="w-6 h-6 ml-3 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </a>
                </div>
                
                <!-- Live Stats with movie theme -->
                <div class="grid grid-cols-3 gap-8 pt-8 max-w-md mx-auto lg:mx-0">
                    <div class="text-center lg:text-left group">
                        <div class="text-4xl font-black text-yellow-400 mb-2 group-hover:scale-110 transition-transform">
                            🎬 <?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?>+
                        </div>
                        <div class="text-white/70 text-sm font-medium">রিভিউ</div>
                    </div>
                    <div class="text-center lg:text-left group">
                        <div class="text-4xl font-black text-pink-400 mb-2 group-hover:scale-110 transition-transform">
                            📺 <?php echo $db->count("SELECT COUNT(*) FROM categories WHERE status = 'active'"); ?>+
                        </div>
                        <div class="text-white/70 text-sm font-medium">ক্যাটেগরি</div>
                    </div>
                    <div class="text-center lg:text-left group">
                        <div class="text-4xl font-black text-green-400 mb-2 group-hover:scale-110 transition-transform">
                            👀 <?php echo number_format($db->count("SELECT SUM(view_count) FROM reviews WHERE status = 'published'")); ?>+
                        </div>
                        <div class="text-white/70 text-sm font-medium">ভিউ</div>
                    </div>
                </div>
                
                <!-- Quick access buttons -->
                <div class="flex flex-wrap gap-3 justify-center lg:justify-start pt-6">
                    <a href="/reviews?type=movie" class="bg-white/10 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-white/20 transition-all">
                        🎬 সিনেমা
                    </a>
                    <a href="/reviews?type=series" class="bg-white/10 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-white/20 transition-all">
                        📺 সিরিয়াল
                    </a>
                    <a href="/reviews?language=korean" class="bg-white/10 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-white/20 transition-all">
                        🇰🇷 K-Drama
                    </a>
                    <a href="/reviews?language=bangla" class="bg-white/10 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-white/20 transition-all">
                        🇧🇩 বাংলা
                    </a>
                </div>
            </div>
            
            <!-- Visual Side - Movie Theater Theme -->
            <div class="relative">
                <div class="relative max-w-lg mx-auto">
                    <!-- Theater screen frame -->
                    <div class="relative bg-black rounded-3xl p-6 shadow-2xl border-4 border-yellow-400/30">
                        <!-- Screen content -->
                        <?php if (!empty($featured_reviews)): ?>
                            <?php $main_review = $featured_reviews[0]; ?>
                            <div class="relative rounded-2xl overflow-hidden bg-gray-900">
                                <div class="relative h-80">
                                    <?php if ($main_review['poster_image']): ?>
                                        <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $main_review['poster_image']; ?>" 
                                             alt="<?php echo htmlspecialchars($main_review['title']); ?>" 
                                             class="w-full h-full object-cover">
                                    <?php endif; ?>
                                    
                                    <!-- Play button overlay -->
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                                        <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:scale-110 transition-transform cursor-pointer group">
                                            <svg class="w-10 h-10 text-white ml-1 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Rating overlay -->
                                    <div class="absolute top-4 right-4 bg-black/70 backdrop-blur-sm text-white px-4 py-2 rounded-full font-bold flex items-center">
                                        ⭐ <?php echo $main_review['rating']; ?>/5
                                    </div>
                                    
                                    <!-- Category badge -->
                                    <div class="absolute top-4 left-4 bg-yellow-400 text-black px-4 py-2 rounded-full text-sm font-bold">
                                        🎭 <?php echo $main_review['category_name_bn'] ?: $main_review['category_name']; ?>
                                    </div>
                                </div>
                                
                                <!-- Movie info -->
                                <div class="p-6 bg-gradient-to-t from-black to-gray-900">
                                    <h3 class="text-2xl font-bold text-white mb-2">
                                        <?php echo htmlspecialchars($main_review['title']); ?>
                                    </h3>
                                    <p class="text-gray-300 mb-4">
                                        📅 <?php echo $main_review['year']; ?> • 🌍 <?php echo $main_review['language']; ?>
                                    </p>
                                    <p class="text-gray-400 text-sm mb-4">
                                        <?php echo htmlspecialchars(substr($main_review['excerpt'], 0, 100)); ?>...
                                    </p>
                                    <a href="/review/<?php echo $main_review['slug']; ?>" 
                                       class="inline-flex items-center bg-yellow-400 text-black px-6 py-3 rounded-xl font-bold hover:bg-yellow-300 transition-colors">
                                        🍿 রিভিউ পড়ুন
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Theater lights -->
                        <div class="absolute -top-4 left-8 w-4 h-4 bg-yellow-400 rounded-full opacity-60 animate-pulse"></div>
                        <div class="absolute -top-4 right-8 w-4 h-4 bg-yellow-400 rounded-full opacity-60 animate-pulse" style="animation-delay: 1s;"></div>
                    </div>
                    
                    <!-- Floating movie elements -->
                    <div class="absolute -top-12 -left-8 camera-flash">
                        <div class="text-6xl opacity-30">📹</div>
                    </div>
                    
                    <div class="absolute -bottom-8 -right-8 tv-static">
                        <div class="text-5xl opacity-40">🎞️</div>
                    </div>
                    
                    <div class="absolute top-1/2 -left-16 transform -translate-y-1/2">
                        <div class="text-4xl opacity-20 movie-icon">🍿</div>
                    </div>
                    
                    <div class="absolute top-1/4 -right-12">
                        <div class="text-3xl opacity-25 movie-icon" style="animation-delay: 2s;">🎪</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator with movie theme -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white/60 animate-bounce">
        <div class="flex flex-col items-center">
            <span class="text-2xl mb-2">🍿</span>
            <div class="w-8 h-12 border-2 border-white/30 rounded-full flex justify-center">
                <div class="w-1 h-3 bg-white/30 rounded-full mt-2 animate-pulse"></div>
            </div>
        </div>
    </div>
</section>

<!-- Rest of the content remains the same as previous home1.php -->