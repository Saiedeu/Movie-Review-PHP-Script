<?php
/**
 * MSR Homepage - Design 4: Vibrant & Colorful
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
/* Vibrant Theme Animations */
@keyframes rainbowGlow {
    0% { filter: hue-rotate(0deg); }
    100% { filter: hue-rotate(360deg); }
}

@keyframes popIn {
    0% { opacity: 0; transform: scale(0.5) rotate(-10deg); }
    50% { opacity: 1; transform: scale(1.1) rotate(5deg); }
    100% { opacity: 1; transform: scale(1) rotate(0deg); }
}

@keyframes wiggle {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(5deg); }
    75% { transform: rotate(-5deg); }
}

@keyframes colorPulse {
    0%, 100% { background-color: #FF6B6B; }
    25% { background-color: #4ECDC4; }
    50% { background-color: #45B7D1; }
    75% { background-color: #FFA726; }
}

@keyframes slideUp {
    0% { opacity: 0; transform: translateY(30px); }
    100% { opacity: 1; transform: translateY(0); }
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% { transform: translateY(0); }
    40%, 43% { transform: translateY(-20px); }
    70% { transform: translateY(-10px); }
    90% { transform: translateY(-4px); }
}

.vibrant-bg {
    background: 
        linear-gradient(135deg, 
            rgba(255, 255, 255, 0.95) 0%, 
            rgba(248, 250, 252, 0.98) 25%,
            rgba(240, 249, 255, 0.95) 50%,
            rgba(254, 240, 138, 0.1) 75%,
            rgba(252, 231, 243, 0.1) 100%
        ),
        radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(236, 72, 153, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 50% 90%, rgba(34, 197, 94, 0.1) 0%, transparent 50%);
}

.rainbow-text {
    background: linear-gradient(45deg, #FF6B6B, #4ECDC4, #45B7D1, #FFA726, #FF6B6B);
    background-size: 400% 400%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: rainbowGlow 3s ease infinite;
}

.pop-in {
    animation: popIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

.wiggle-hover:hover {
    animation: wiggle 0.5s ease-in-out;
}

.color-pulse {
    animation: colorPulse 4s ease-in-out infinite;
}

.slide-up {
    animation: slideUp 0.8s ease-out forwards;
}

.bounce-animation {
    animation: bounce 2s ease infinite;
}

.colorful-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: 
        0 25px 45px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(255, 255, 255, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
}

.gradient-border {
    background: linear-gradient(45deg, #FF6B6B, #4ECDC4, #45B7D1, #FFA726);
    background-size: 400% 400%;
    animation: rainbowGlow 3s ease infinite;
    padding: 3px;
    border-radius: 1.5rem;
}

.gradient-border-inner {
    background: white;
    border-radius: 1.25rem;
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
    font-size: 2.5rem;
    animation: popIn 0.6s ease-out forwards;
}

.floating-icon:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
.floating-icon:nth-child(2) { top: 20%; right: 20%; animation-delay: 0.5s; }
.floating-icon:nth-child(3) { top: 60%; left: 5%; animation-delay: 1s; }
.floating-icon:nth-child(4) { top: 70%; right: 15%; animation-delay: 1.5s; }
.floating-icon:nth-child(5) { top: 40%; right: 80%; animation-delay: 2s; }
.floating-icon:nth-child(6) { top: 80%; left: 70%; animation-delay: 2.5s; }

.neon-button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 
        0 10px 30px rgba(102, 126, 234, 0.4),
        0 0 20px rgba(118, 75, 162, 0.3);
    transition: all 0.3s ease;
}

.neon-button:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 
        0 15px 40px rgba(102, 126, 234, 0.5),
        0 0 30px rgba(118, 75, 162, 0.4);
}

.fun-stats {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    background-size: 300% 300%;
    animation: rainbowGlow 4s ease infinite;
}

@media (max-width: 768px) {
    .floating-icon {
        font-size: 2rem;
    }
}
</style>

<!-- Hero Section - Vibrant & Colorful -->
<section class="relative min-h-screen flex items-center vibrant-bg overflow-hidden">
    <!-- Animated background elements -->
    <div class="floating-elements">
        <div class="floating-icon">🍿</div>
        <div class="floating-icon">🎬</div>
        <div class="floating-icon">📺</div>
        <div class="floating-icon">🎞️</div>
        <div class="floating-icon">🎭</div>
        <div class="floating-icon">⭐</div>
        <div class="floating-icon">🎪</div>
        <div class="floating-icon">🎨</div>
    </div>
    
    <!-- Colorful floating shapes -->
    <div class="absolute top-20 left-1/4 w-20 h-20 rounded-full color-pulse opacity-60"></div>
    <div class="absolute top-1/3 right-1/4 w-16 h-16 bg-gradient-to-r from-pink-400 to-purple-500 rounded-full opacity-40 animate-pulse"></div>
    <div class="absolute bottom-1/4 left-1/3 w-24 h-24 bg-gradient-to-r from-blue-400 to-cyan-500 rounded-full opacity-50 bounce-animation"></div>
    
    <div class="container mx-auto px-4 py-20 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            
            <!-- Left Content -->
            <div class="text-center lg:text-left space-y-10 slide-up">
                <!-- Fun Badge -->
                <div class="gradient-border inline-block pop-in">
                    <div class="gradient-border-inner px-8 py-4 flex items-center">
                        <span class="text-4xl mr-4 bounce-animation">🎪</span>
                        <div>
                            <div class="font-black text-gray-800 text-lg">FUN ENTERTAINMENT</div>
                            <div class="text-sm text-gray-600">মজার মুভি রিভিউ প্ল্যাটফর্ম</div>
                        </div>
                    </div>
                </div>
                
                <!-- Main Heading -->
                <div class="space-y-6">
                    <h1 class="text-6xl lg:text-8xl font-black leading-tight">
                        <span class="block text-gray-900">মজার</span>
                        <span class="block rainbow-text">বিনোদনের</span>
                        <span class="block text-gray-700 text-5xl lg:text-6xl flex items-center justify-center lg:justify-start">
                            দুনিয়া <span class="ml-4 text-6xl bounce-animation">🌈</span>
                        </span>
                    </h1>
                    
                    <div class="space-y-4">
                        <p class="text-3xl lg:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-violet-500">
                            🍿 খুশির সাথে দেখুন! 🎬
                        </p>
                        
                        <p class="text-xl text-gray-600 leading-relaxed max-w-2xl">
                            রঙিন, মজাদার এবং সহজ ভাষায় সেরা মুভি ও সিরিয়ালের রিভিউ। 
                            আর বিরক্তিকর রিভিউ নয়! <span class="font-bold text-orange-500">মজা করে পড়ুন</span> 
                            এবং <span class="font-bold text-green-500">স্মার্ট চয়েস</span> করুন!
                        </p>
                    </div>
                </div>
                
                <!-- Fun Features -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="colorful-card rounded-3xl p-6 wiggle-hover pop-in" style="animation-delay: 0.5s;">
                        <div class="text-5xl mb-4 bounce-animation">🎯</div>
                        <h3 class="font-black text-gray-800 text-xl mb-2">স্মার্ট রিকমেন্ডেশন</h3>
                        <p class="text-gray-600">AI পাওয়ার্ড সুপারিশ!</p>
                    </div>
                    
                    <div class="colorful-card rounded-3xl p-6 wiggle-hover pop-in" style="animation-delay: 0.7s;">
                        <div class="text-5xl mb-4 bounce-animation" style="animation-delay: 0.5s;">🏆</div>
                        <h3 class="font-black text-gray-800 text-xl mb-2">রেটিং সিস্টেম</h3>
                        <p class="text-gray-600">সততার সাথে রেটিং!</p>
                    </div>
                    
                    <div class="colorful-card rounded-3xl p-6 wiggle-hover pop-in" style="animation-delay: 0.9s;">
                        <div class="text-5xl mb-4 bounce-animation" style="animation-delay: 1s;">🎨</div>
                        <h3 class="font-black text-gray-800 text-xl mb-2">ক্রিয়েটিভ রিভিউ</h3>
                        <p class="text-gray-600">মজাদার স্টাইলে লেখা!</p>
                    </div>
                    
                    <div class="colorful-card rounded-3xl p-6 wiggle-hover pop-in" style="animation-delay: 1.1s;">
                        <div class="text-5xl mb-4 bounce-animation" style="animation-delay: 1.5s;">⚡</div>
                        <h3 class="font-black text-gray-800 text-xl mb-2">দ্রুত আপডেট</h3>
                        <p class="text-gray-600">লেটেস্ট রিলিজ!</p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-6 justify-center lg:justify-start">
                    <a href="/reviews" class="neon-button text-white px-12 py-6 rounded-3xl font-black text-2xl flex items-center justify-center group">
                        🚀 মজা শুরু করুন!
                        <svg class="w-7 h-7 ml-4 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                    
                    <a href="/categories" class="bg-gradient-to-r from-pink-500 to-rose-500 text-white px-12 py-6 rounded-3xl font-black text-2xl hover:shadow-xl hover:scale-105 transition-all flex items-center justify-center group">
                        🎭 ক্যাটেগরি দেখুন
                        <svg class="w-7 h-7 ml-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </a>
                </div>
                
                <!-- Fun Stats -->
                <div class="fun-stats rounded-3xl p-8 text-white">
                    <div class="grid grid-cols-3 gap-6 text-center">
                        <div>
                            <div class="text-4xl font-black mb-2 flex items-center justify-center">
                                🎬 <?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?>+
                            </div>
                            <div class="text-white/90 font-semibold">হ্যাপি রিভিউ</div>
                        </div>
                        <div>
                            <div class="text-4xl font-black mb-2 flex items-center justify-center">
                                😊 98%
                            </div>
                            <div class="text-white/90 font-semibold">স্যাটিসফাইড ইউজার</div>
                        </div>
                        <div>
                            <div class="text-4xl font-black mb-2 flex items-center justify-center">
                                ⭐ 4.9
                            </div>
                            <div class="text-white/90 font-semibold">লাভ রেটিং</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Visual -->
            <div class="relative slide-up" style="animation-delay: 0.3s;">
                <div class="relative max-w-lg mx-auto">
                    <!-- Main Featured Card with Rainbow Border -->
                    <?php if (!empty($featured_reviews)): ?>
                        <?php $featured = $featured_reviews[0]; ?>
                        <div class="gradient-border pop-in">
                            <div class="gradient-border-inner overflow-hidden">
                                <div class="relative h-96">
                                    <?php if ($featured['poster_image']): ?>
                                        <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $featured['poster_image']; ?>" 
                                             alt="<?php echo htmlspecialchars($featured['title']); ?>" 
                                             class="w-full h-full object-cover">
                                    <?php endif; ?>
                                    
                                    <!-- Fun overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-purple-900/50 via-transparent to-pink-500/20"></div>
                                    
                                    <!-- Animated badges -->
                                    <div class="absolute top-6 left-6 bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-4 py-2 rounded-full font-black text-sm bounce-animation">
                                        🔥 HOT PICK
                                    </div>
                                    
                                    <div class="absolute top-6 right-6 bg-gradient-to-r from-green-400 to-blue-500 text-white px-4 py-2 rounded-full font-black wiggle-hover">
                                        ⭐ <?php echo $featured['rating']; ?>/5
                                    </div>
                                    
                                    <!-- Fun play button -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:scale-110 transition-transform cursor-pointer group">
                                            <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-purple-600 rounded-full flex items-center justify-center group-hover:from-purple-600 group-hover:to-pink-500 transition-all bounce-animation">
                                                <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-8 bg-gradient-to-r from-white to-purple-50">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <span class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-full text-sm font-black">
                                            🎭 <?php echo $featured['category_name_bn'] ?: $featured['category_name']; ?>
                                        </span>
                                        <span class="text-gray-400">•</span>
                                        <span class="text-gray-600 font-bold"><?php echo $featured['year']; ?></span>
                                    </div>
                                    
                                    <h3 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 mb-3">
                                        <?php echo htmlspecialchars($featured['title']); ?>
                                    </h3>
                                    
                                    <p class="text-gray-600 leading-relaxed mb-6">
                                        <?php echo htmlspecialchars(substr($featured['excerpt'], 0, 120)); ?>...
                                    </p>
                                    
                                    <div class="flex items-center justify-between">
                                        <a href="/review/<?php echo $featured['slug']; ?>" 
                                           class="bg-gradient-to-r from-orange-400 to-pink-500 text-white px-8 py-4 rounded-2xl font-black hover:shadow-xl hover:scale-105 transition-all flex items-center">
                                            🍿 মজা করে পড়ুন!
                                        </a>
                                        
                                        <div class="flex items-center space-x-3">
                                            <button class="w-12 h-12 bg-gradient-to-r from-red-400 to-pink-500 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform wiggle-hover">
                                                ❤️
                                            </button>
                                            <button class="w-12 h-12 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform wiggle-hover">
                                                🔖
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Fun floating elements -->
                    <div class="absolute -top-8 -left-8 colorful-card rounded-2xl p-4 pop-in" style="animation-delay: 1.5s;">
                        <div class="text-center">
                            <div class="text-3xl mb-2 bounce-animation">🏆</div>
                            <div class="text-gray-700 font-black text-sm">বেস্ট পিক</div>
                        </div>
                    </div>
                    
                    <div class="absolute -bottom-8 -right-8 colorful-card rounded-2xl p-4 pop-in" style="animation-delay: 2s;">
                        <div class="text-center">
                            <div class="text-3xl mb-2 bounce-animation" style="animation-delay: 1s;">🎉</div>
                            <div class="text-gray-700 font-black text-sm">পপুলার</div>
                        </div>
                    </div>
                    
                    <div class="absolute top-1/2 -left-16 transform -translate-y-1/2 colorful-card rounded-2xl p-4 pop-in" style="animation-delay: 2.5s;">
                        <div class="text-center">
                            <div class="text-2xl mb-2 bounce-animation" style="animation-delay: 1.5s;">😍</div>
                            <div class="text-gray-700 font-black text-xs">লাভড</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Fun scroll indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-center">
        <div class="text-gray-600 space-y-3">
            <div class="text-4xl bounce-animation">🍿</div>
            <div class="text-sm font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-500">
                আরো মজা নিচে!
            </div>
            <div class="w-2 h-12 bg-gradient-to-b from-purple-500 to-pink-500 rounded-full mx-auto animate-pulse"></div>
        </div>
    </div>
</section>

<!-- Rest of sections continue with vibrant theme... -->