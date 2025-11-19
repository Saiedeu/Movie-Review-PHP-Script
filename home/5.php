<?php
/**
 * MSR Homepage - Design 3: Premium Light & Clean
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

// Include header
include 'includes/header.php';
?>

<style>
/* Premium Light Theme Animations */
@keyframes gentleFloat {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    25% { transform: translateY(-15px) rotate(2deg); }
    50% { transform: translateY(-5px) rotate(-1deg); }
    75% { transform: translateY(-10px) rotate(1deg); }
}

@keyframes sparkle {
    0%, 100% { opacity: 0; transform: scale(0) rotate(0deg); }
    50% { opacity: 1; transform: scale(1) rotate(180deg); }
}

@keyframes slideInFromRight {
    0% { opacity: 0; transform: translateX(50px); }
    100% { opacity: 1; transform: translateX(0); }
}

@keyframes slideInFromLeft {
    0% { opacity: 0; transform: translateX(-50px); }
    100% { opacity: 1; transform: translateX(0); }
}

@keyframes bounceIn {
    0% { opacity: 0; transform: scale(0.3); }
    50% { opacity: 1; transform: scale(1.1); }
    100% { opacity: 1; transform: scale(1); }
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.premium-bg {
    background: 
        linear-gradient(135deg, rgba(255, 250, 250, 0.9) 0%, rgba(245, 247, 250, 0.95) 100%),
        radial-gradient(circle at 20% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(168, 85, 247, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(34, 197, 94, 0.05) 0%, transparent 50%);
    background-size: 100% 100%, 800px 800px, 600px 600px, 1000px 1000px;
    background-attachment: fixed;
}

.movie-icon-float {
    animation: gentleFloat 4s ease-in-out infinite;
}

.sparkle-effect {
    animation: sparkle 2s ease-in-out infinite;
}

.slide-in-right {
    animation: slideInFromRight 0.8s ease-out forwards;
}

.slide-in-left {
    animation: slideInFromLeft 0.8s ease-out forwards;
}

.bounce-in {
    animation: bounceIn 0.6s ease-out forwards;
}

.gradient-text-premium {
    background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 50%, #EF4444 100%);
    background-size: 300% 300%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: gradientShift 3s ease infinite;
}

.glass-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.hover-lift {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-lift:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.premium-button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
}

.premium-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
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
    font-size: 3rem;
    opacity: 0.6;
    color: #3B82F6;
}

.floating-icon:nth-child(1) { top: 15%; left: 10%; animation: gentleFloat 3s ease-in-out infinite; animation-delay: 0s; }
.floating-icon:nth-child(2) { top: 25%; right: 15%; animation: gentleFloat 4s ease-in-out infinite; animation-delay: 1s; }
.floating-icon:nth-child(3) { top: 65%; left: 8%; animation: gentleFloat 5s ease-in-out infinite; animation-delay: 2s; }
.floating-icon:nth-child(4) { top: 75%; right: 12%; animation: gentleFloat 3.5s ease-in-out infinite; animation-delay: 3s; }
.floating-icon:nth-child(5) { top: 45%; right: 80%; animation: gentleFloat 4.5s ease-in-out infinite; animation-delay: 4s; }
.floating-icon:nth-child(6) { top: 85%; left: 75%; animation: gentleFloat 3.8s ease-in-out infinite; animation-delay: 5s; }

.section-spacing {
    padding: 4rem 0;
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
</style>

<!-- Hero Section - Premium Light Design -->
<section class="relative min-h-screen flex items-center premium-bg overflow-hidden">
    <!-- Floating movie icons -->
    <div class="floating-elements">
        <div class="floating-icon">🍿</div>
        <div class="floating-icon">🎬</div>
        <div class="floating-icon">📺</div>
        <div class="floating-icon">🎞️</div>
        <div class="floating-icon">🎭</div>
        <div class="floating-icon">⭐</div>
    </div>
    
    <!-- Sparkle effects -->
    <div class="absolute top-20 left-1/4 w-4 h-4 bg-yellow-400 rounded-full sparkle-effect"></div>
    <div class="absolute top-1/3 right-1/4 w-3 h-3 bg-pink-400 rounded-full sparkle-effect" style="animation-delay: 1s;"></div>
    <div class="absolute bottom-1/4 left-1/3 w-5 h-5 bg-blue-400 rounded-full sparkle-effect" style="animation-delay: 2s;"></div>
    
    <div class="container-custom relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            
            <!-- Left Content -->
            <div class="text-center lg:text-left space-y-8 slide-in-left">
                <!-- Premium Badge -->
                <div class="inline-flex items-center glass-card rounded-full px-8 py-4 text-gray-700 font-semibold border border-blue-200">
                    <span class="text-3xl mr-4 movie-icon-float">🎪</span>
                    <span class="flex items-center">
                        <span class="w-3 h-3 bg-green-500 rounded-full mr-3 animate-pulse"></span>
                        প্রিমিয়াম মুভি রিভিউ প্ল্যাটফর্ম
                    </span>
                </div>
                
                <!-- Main Heading -->
                <div class="space-y-6">
                    <h1 class="text-6xl lg:text-8xl font-black leading-tight">
                        <span class="block text-gray-900">আপনার</span>
                        <span class="block gradient-text-premium">বিনোদনের</span>
                        <span class="block text-gray-700 text-5xl lg:text-6xl">সেরা গাইড 🎬</span>
                    </h1>
                    
                    <p class="text-2xl lg:text-3xl text-gray-600 leading-relaxed">
                        🍿 <span class="font-bold text-blue-600">স্মার্ট চয়েস</span> করুন প্রতিটি মুভি ও সিরিয়ালের জন্য
                    </p>
                    
                    <p class="text-xl text-gray-500 leading-relaxed max-w-2xl">
                        বিশেষজ্ঞ রিভিউ, নির্ভরযোগ্য রেটিং এবং ব্যক্তিগত সুপারিশ - 
                        সব একসাথে পাবেন আমাদের প্ল্যাটফর্মে। 
                        <span class="text-purple-600 font-semibold">আর নষ্ট করবেন না সময়!</span>
                    </p>
                </div>
                
                <!-- Feature Highlights -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="glass-card rounded-2xl p-6 hover-lift">
                        <div class="text-4xl mb-3">⭐</div>
                        <h3 class="font-bold text-gray-800 text-lg mb-2">নির্ভরযোগ্য রেটিং</h3>
                        <p class="text-gray-600 text-sm">বিশেষজ্ঞ ও দর্শকদের রেটিং</p>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-6 hover-lift">
                        <div class="text-4xl mb-3">🎯</div>
                        <h3 class="font-bold text-gray-800 text-lg mb-2">স্মার্ট সুপারিশ</h3>
                        <p class="text-gray-600 text-sm">আপনার পছন্দ অনুযায়ী</p>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-6 hover-lift">
                        <div class="text-4xl mb-3">🔍</div>
                        <h3 class="font-bold text-gray-800 text-lg mb-2">বিস্তারিত রিভিউ</h3>
                        <p class="text-gray-600 text-sm">গভীর বিশ্লেষণ ও আলোচনা</p>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-6 hover-lift">
                        <div class="text-4xl mb-3">💎</div>
                        <h3 class="font-bold text-gray-800 text-lg mb-2">প্রিমিয়াম কন্টেন্ট</h3>
                        <p class="text-gray-600 text-sm">এক্সক্লুসিভ রিভিউ ও গাইড</p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-6 justify-center lg:justify-start">
                    <a href="/reviews" class="premium-button text-white px-10 py-5 rounded-2xl font-bold text-xl flex items-center justify-center group">
                        🚀 এক্সপ্লোর শুরু করুন
                        <svg class="w-6 h-6 ml-3 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                    
                    <a href="/categories" class="bg-white text-gray-700 border-2 border-gray-200 px-10 py-5 rounded-2xl font-bold text-xl hover:border-blue-300 hover:text-blue-600 transition-all flex items-center justify-center group">
                        🎭 ক্যাটেগরি দেখুন
                        <svg class="w-6 h-6 ml-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </a>
                </div>
                
                <!-- Trust Indicators -->
                <div class="flex items-center justify-center lg:justify-start space-x-8 pt-8">
                    <div class="text-center">
                        <div class="text-3xl font-black text-blue-600 mb-1">
                            🎬 <?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?>+
                        </div>
                        <div class="text-gray-500 text-sm font-medium">রিভিউ</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-purple-600 mb-1">
                            👥 50K+
                        </div>
                        <div class="text-gray-500 text-sm font-medium">ইউজার</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-green-600 mb-1">
                            ⭐ 4.9
                        </div>
                        <div class="text-gray-500 text-sm font-medium">রেটিং</div>
                    </div>
                </div>
            </div>
            
            <!-- Right Visual -->
            <div class="relative slide-in-right">
                <div class="relative max-w-lg mx-auto">
                    <!-- Main Featured Card -->
                    <?php if (!empty($featured_reviews)): ?>
                        <?php $featured = $featured_reviews[0]; ?>
                        <div class="glass-card rounded-3xl overflow-hidden hover-lift">
                            <div class="relative h-96">
                                <?php if ($featured['poster_image']): ?>
                                    <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $featured['poster_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($featured['title']); ?>" 
                                         class="w-full h-full object-cover">
                                <?php endif; ?>
                                
                                <!-- Gradient overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                                
                                <!-- Featured badge -->
                                <div class="absolute top-6 left-6 bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-4 py-2 rounded-full font-bold text-sm">
                                    ⭐ FEATURED
                                </div>
                                
                                <!-- Rating -->
                                <div class="absolute top-6 right-6 bg-white/90 text-gray-900 px-4 py-2 rounded-full font-bold">
                                    🏆 <?php echo $featured['rating']; ?>/5
                                </div>
                                
                                <!-- Play button -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:scale-110 transition-transform cursor-pointer group">
                                        <div class="w-14 h-14 bg-blue-500 rounded-full flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                                            <svg class="w-7 h-7 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-8">
                                <div class="flex items-center space-x-3 mb-4">
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        🎭 <?php echo $featured['category_name_bn'] ?: $featured['category_name']; ?>
                                    </span>
                                    <span class="text-gray-400">•</span>
                                    <span class="text-gray-600 font-medium"><?php echo $featured['year']; ?></span>
                                </div>
                                
                                <h3 class="text-2xl font-black text-gray-900 mb-3">
                                    <?php echo htmlspecialchars($featured['title']); ?>
                                </h3>
                                
                                <p class="text-gray-600 leading-relaxed mb-6">
                                    <?php echo htmlspecialchars(substr($featured['excerpt'], 0, 120)); ?>...
                                </p>
                                
                                <div class="flex items-center justify-between">
                                    <a href="/review/<?php echo $featured['slug']; ?>" 
                                       class="bg-gradient-to-r from-blue-500 to-purple-500 text-white px-6 py-3 rounded-xl font-bold hover:shadow-lg transition-all">
                                        🍿 রিভিউ পড়ুন
                                    </a>
                                    
                                    <div class="flex items-center space-x-3">
                                        <button class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                                            ❤️
                                        </button>
                                        <button class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                                            🔖
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Floating mini cards -->
                    <div class="absolute -top-8 -left-12 glass-card rounded-2xl p-4 bounce-in" style="animation-delay: 1s;">
                        <div class="text-center">
                            <div class="text-2xl mb-2">🏆</div>
                            <div class="text-gray-700 font-bold text-sm">Top Rated</div>
                        </div>
                    </div>
                    
                    <div class="absolute -bottom-8 -right-12 glass-card rounded-2xl p-4 bounce-in" style="animation-delay: 2s;">
                        <div class="text-center">
                            <div class="text-2xl mb-2">🔥</div>
                            <div class="text-gray-700 font-bold text-sm">Trending</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-center">
        <div class="text-gray-500 space-y-2">
            <div class="text-3xl animate-bounce">🍿</div>
            <div class="text-sm font-medium">আরো দেখুন</div>
            <div class="w-1 h-8 bg-gray-300 rounded-full mx-auto animate-pulse"></div>
        </div>
    </div>
</section>

<!-- Rest of sections with premium light theme... -->