<?php
/**
 * MSR Homepage - Design 2: Entertainment Hub
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
/* Entertainment Hub Animations */
@keyframes neonGlow {
    0%, 100% { text-shadow: 0 0 5px #ff6b6b, 0 0 10px #ff6b6b, 0 0 15px #ff6b6b; }
    50% { text-shadow: 0 0 10px #4ecdc4, 0 0 20px #4ecdc4, 0 0 30px #4ecdc4; }
}

@keyframes floatingBubbles {
    0% { transform: translateY(100vh) scale(0); opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { transform: translateY(-100px) scale(1); opacity: 0; }
}

@keyframes rotateIcon {
    0% { transform: rotate(0deg) scale(1); }
    25% { transform: rotate(90deg) scale(1.2); }
    50% { transform: rotate(180deg) scale(1); }
    75% { transform: rotate(270deg) scale(1.2); }
    100% { transform: rotate(360deg) scale(1); }
}

@keyframes pulseGlow {
    0%, 100% { box-shadow: 0 0 20px rgba(255, 107, 107, 0.5); }
    50% { box-shadow: 0 0 40px rgba(78, 205, 196, 0.8); }
}

@keyframes marqueeScroll {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}

@keyframes theaterCurtain {
    0% { transform: scaleY(1); }
    100% { transform: scaleY(0); transform-origin: top; }
}

.entertainment-bg {
    background: 
        radial-gradient(circle at 20% 50%, rgba(255, 107, 107, 0.2) 0%, transparent 50%),
        radial-gradient(circle at 80% 50%, rgba(78, 205, 196, 0.2) 0%, transparent 50%),
        radial-gradient(circle at 40% 20%, rgba(120, 119, 241, 0.3) 0%, transparent 50%),
        linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
}

.neon-text {
    animation: neonGlow 3s ease-in-out infinite;
}

.floating-bubble {
    position: absolute;
    font-size: 2rem;
    animation: floatingBubbles 8s linear infinite;
}

.rotating-icon {
    animation: rotateIcon 4s ease-in-out infinite;
}

.pulse-glow {
    animation: pulseGlow 2s ease-in-out infinite;
}

.marquee-container {
    overflow: hidden;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
}

.marquee-content {
    display: flex;
    animation: marqueeScroll 20s linear infinite;
    white-space: nowrap;
}

.theater-screen {
    background: 
        linear-gradient(45deg, #000 25%, transparent 25%, transparent 75%, #000 75%, #000),
        linear-gradient(45deg, #000 25%, transparent 25%, transparent 75%, #000 75%, #000);
    background-size: 4px 4px;
    background-position: 0 0, 2px 2px;
}

.movie-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.interactive-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.interactive-card:hover {
    transform: translateY(-10px) scale(1.05);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.glow-border {
    border: 2px solid transparent;
    background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4) border-box;
    -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: destination-out;
    mask-composite: exclude;
}

/* Emoji animations */
.emoji-float-1 { animation: floatingBubbles 6s ease-in-out infinite; animation-delay: 0s; }
.emoji-float-2 { animation: floatingBubbles 8s ease-in-out infinite; animation-delay: 2s; }
.emoji-float-3 { animation: floatingBubbles 7s ease-in-out infinite; animation-delay: 4s; }
.emoji-float-4 { animation: floatingBubbles 9s ease-in-out infinite; animation-delay: 1s; }
.emoji-float-5 { animation: floatingBubbles 5s ease-in-out infinite; animation-delay: 3s; }
.emoji-float-6 { animation: floatingBubbles 10s ease-in-out infinite; animation-delay: 5s; }
</style>

<!-- Hero Section - Entertainment Hub -->
<section class="relative min-h-screen entertainment-bg overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 pointer-events-none">
        <!-- Floating emoji bubbles -->
        <div class="floating-bubble emoji-float-1" style="left: 10%; animation-delay: 0s;">🍿</div>
        <div class="floating-bubble emoji-float-2" style="left: 20%; animation-delay: 1s;">🎬</div>
        <div class="floating-bubble emoji-float-3" style="left: 70%; animation-delay: 2s;">🎥</div>
        <div class="floating-bubble emoji-float-4" style="left: 80%; animation-delay: 3s;">📺</div>
        <div class="floating-bubble emoji-float-5" style="left: 30%; animation-delay: 4s;">🎞️</div>
        <div class="floating-bubble emoji-float-6" style="left: 60%; animation-delay: 5s;">🎭</div>
        <div class="floating-bubble emoji-float-1" style="left: 40%; animation-delay: 6s;">🍟</div>
        <div class="floating-bubble emoji-float-2" style="left: 90%; animation-delay: 7s;">🥤</div>
    </div>
    
    <!-- Top Marquee -->
    <div class="marquee-container py-3 text-white text-sm font-bold">
        <div class="marquee-content">
            <span class="mx-8">🔥 ট্রেন্ডিং: স্কুইড গেম সিজন ২</span>
            <span class="mx-8">⭐ রেটেড: প্যারাসাইট - ৫/৫ স্টার</span>
            <span class="mx-8">🆕 নতুন: অক্টোপাস গেম রিভিউ</span>
            <span class="mx-8">🎬 এক্সক্লুসিভ: হলিউড vs বলিউড</span>
            <span class="mx-8">📺 K-Drama: সেরা কোরিয়ান সিরিয়াল</span>
            <span class="mx-8">🍿 প্রিমিয়াম: VIP রিভিউ অ্যাক্সেস</span>
        </div>
    </div>
    
    <div class="container mx-auto px-4 py-16 relative z-10">
        <!-- Main Content Grid -->
        <div class="grid lg:grid-cols-12 gap-8 items-center min-h-screen">
            
            <!-- Left Content - 7 columns -->
            <div class="lg:col-span-7 space-y-8">
                <!-- Hero Badge -->
                <div class="inline-flex items-center bg-gradient-to-r from-red-500/20 to-blue-500/20 backdrop-blur-lg rounded-full px-8 py-4 text-white border border-white/20 glow-border">
                    <span class="text-3xl mr-4 rotating-icon">🎪</span>
                    <div class="flex items-center">
                        <span class="w-3 h-3 bg-red-500 rounded-full mr-3 animate-pulse"></span>
                        <span class="font-bold">ENTERTAINMENT HUB</span>
                        <span class="ml-4 bg-yellow-400 text-black px-3 py-1 rounded-full text-xs font-black">LIVE</span>
                    </div>
                </div>
                
                <!-- Main Headline -->
                <div class="space-y-6">
                    <h1 class="text-6xl lg:text-8xl font-black leading-none">
                        <span class="block text-white">আপনার</span>
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-red-400 via-yellow-400 to-pink-400 neon-text">এনটারটেইনমেন্ট</span>
                        <span class="block text-white/80 text-4xl lg:text-6xl">🎬 গাইড</span>
                    </h1>
                    
                    <div class="flex items-center space-x-4 text-white/90">
                        <span class="text-4xl">🍿</span>
                        <p class="text-2xl lg:text-3xl font-bold">
                            পপকর্ন রেডি? চলুন শুরু করি!
                        </p>
                    </div>
                </div>
                
                <!-- Features Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 interactive-card">
                        <div class="text-4xl mb-3">🎭</div>
                        <h3 class="text-white font-bold text-lg mb-2">সিনেমা রিভিউ</h3>
                        <p class="text-white/70 text-sm">বিশ্বের সেরা সিনেমাগুলোর বিস্তারিত রিভিউ</p>
                    </div>
                    
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 interactive-card">
                        <div class="text-4xl mb-3">📺</div>
                        <h3 class="text-white font-bold text-lg mb-2">সিরিয়াল গাইড</h3>
                        <p class="text-white/70 text-sm">ট্রেন্ডিং সিরিয়াল ও ওয়েব সিরিজ</p>
                    </div>
                    
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 interactive-card">
                        <div class="text-4xl mb-3">⭐</div>
                        <h3 class="text-white font-bold text-lg mb-2">রেটিং সিস্টেম</h3>
                        <p class="text-white/70 text-sm">নির্ভরযোগ্য ও নিরপেক্ষ রেটিং</p>
                    </div>
                    
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 interactive-card">
                        <div class="text-4xl mb-3">🌟</div>
                        <h3 class="text-white font-bold text-lg mb-2">এক্সক্লুসিভ</h3>
                        <p class="text-white/70 text-sm">বিশেষ রিভিউ ও বিশ্লেষণ</p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/reviews" class="group relative bg-gradient-to-r from-red-500 to-pink-500 text-white px-10 py-5 rounded-2xl font-black text-xl overflow-hidden pulse-glow">
                        <span class="relative z-10 flex items-center justify-center">
                            🚀 এক্সপ্লোর করুন
                            <svg class="w-6 h-6 ml-3 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </span>
                    </a>
                    
                    <a href="/categories" class="group bg-white/10 backdrop-blur-lg border-2 border-white/30 text-white px-10 py-5 rounded-2xl font-black text-xl hover:bg-white/20 transition-all">
                        🎪 ক্যাটেগরি ব্রাউজ করুন
                    </a>
                </div>
                
                <!-- Quick Stats -->
                <div class="flex justify-between items-center bg-black/30 backdrop-blur-lg rounded-2xl p-6 border border-white/10">
                    <div class="text-center">
                        <div class="text-3xl font-black text-yellow-400 flex items-center justify-center">
                            🎬 <?php echo $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'"); ?>+
                        </div>
                        <div class="text-white/70 text-sm">রিভিউ</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-pink-400 flex items-center justify-center">
                            👥 1M+
                        </div>
                        <div class="text-white/70 text-sm">ভিজিটর</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-green-400 flex items-center justify-center">
                            ⭐ 4.9
                        </div>
                        <div class="text-white/70 text-sm">রেটিং</div>
                    </div>
                </div>
            </div>
            
            <!-- Right Content - 5 columns -->
            <div class="lg:col-span-5">
                <div class="relative">
                    <!-- Theater Screen -->
                    <div class="relative bg-black rounded-3xl p-8 theater-screen border-4 border-yellow-400/50">
                        <div class="bg-gray-900 rounded-2xl overflow-hidden">
                            <!-- Featured Content Carousel -->
                            <?php if (!empty($featured_reviews)): ?>
                                <div class="relative">
                                    <!-- Main Featured Item -->
                                    <?php $featured = $featured_reviews[0]; ?>
                                    <div class="relative h-80">
                                        <?php if ($featured['poster_image']): ?>
                                            <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $featured['poster_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($featured['title']); ?>" 
                                                 class="w-full h-full object-cover">
                                        <?php endif; ?>
                                        
                                        <!-- Overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
                                        
                                        <!-- Live indicator -->
                                        <div class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                                            🔴 FEATURED
                                        </div>
                                        
                                        <!-- Rating -->
                                        <div class="absolute top-4 right-4 bg-black/70 text-white px-3 py-2 rounded-full font-bold">
                                            ⭐ <?php echo $featured['rating']; ?>/5
                                        </div>
                                        
                                        <!-- Play Button -->
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:scale-110 transition-transform cursor-pointer group">
                                                <div class="w-16 h-16 bg-red-500 rounded-full flex items-center justify-center group-hover:bg-red-600 transition-colors">
                                                    <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M8 5v14l11-7z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Bottom Info -->
                                        <div class="absolute bottom-0 left-0 right-0 p-6">
                                            <h3 class="text-white text-2xl font-bold mb-2">
                                                <?php echo htmlspecialchars($featured['title']); ?>
                                            </h3>
                                            <div class="flex items-center space-x-4 text-white/80 text-sm">
                                                <span>📅 <?php echo $featured['year']; ?></span>
                                                <span>🌍 <?php echo $featured['language']; ?></span>
                                                <span>🎭 <?php echo $featured['category_name_bn'] ?: $featured['category_name']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Quick Action Bar -->
                                    <div class="bg-gray-800 p-4 flex justify-between items-center">
                                        <div class="flex space-x-3">
                                            <button class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white hover:bg-red-600 transition-colors">
                                                ❤️
                                            </button>
                                            <button class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white hover:bg-blue-600 transition-colors">
                                                🔖
                                            </button>
                                            <button class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white hover:bg-green-600 transition-colors">
                                                ↗️
                                            </button>
                                        </div>
                                        <a href="/review/<?php echo $featured['slug']; ?>" 
                                           class="bg-yellow-400 text-black px-6 py-2 rounded-full font-bold hover:bg-yellow-300 transition-colors">
                                            🍿 রিভিউ পড়ুন
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Theater Elements -->
                        <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 flex space-x-4">
                            <div class="w-6 h-6 bg-yellow-400 rounded-full animate-pulse"></div>
                            <div class="w-6 h-6 bg-red-400 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                            <div class="w-6 h-6 bg-green-400 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
                        </div>
                    </div>
                    
                    <!-- Floating Quick Access -->
                    <div class="absolute -bottom-4 -left-4 bg-white/10 backdrop-blur-lg rounded-2xl p-4 border border-white/20">
                        <div class="text-white text-center">
                            <div class="text-2xl mb-2">🎪</div>
                            <div class="text-sm font-bold">Quick Access</div>
                        </div>
                    </div>
                    
                    <div class="absolute -top-4 -right-4 bg-white/10 backdrop-blur-lg rounded-2xl p-4 border border-white/20">
                        <div class="text-white text-center">
                            <div class="text-2xl mb-2">🔥</div>
                            <div class="text-sm font-bold">Trending</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-center">
        <div class="text-white/60 space-y-2">
            <div class="text-3xl animate-bounce">🍿</div>
            <div class="text-sm font-medium">Scroll for More</div>
            <div class="w-1 h-8 bg-white/30 rounded-full mx-auto animate-pulse"></div>
        </div>
    </div>
</section>

<!-- Rest of the content continues... -->