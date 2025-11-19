<?php
/**
 * About Page
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config/config.php';

// Get database instance
$db = Database::getInstance();

// Get some stats
$total_reviews = $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'published'");
$total_categories = $db->count("SELECT COUNT(*) FROM categories WHERE status = 'active'");
$total_views = $db->count("SELECT SUM(view_count) FROM reviews WHERE status = 'published'") ?: 0;

// SEO Meta Data
$seo_title = 'আমাদের সম্পর্কে - ' . getSiteSetting('site_name', SITE_NAME);
$seo_description = 'MSR সম্পর্কে জানুন। আমাদের লক্ষ্য, উদ্দেশ্য এবং কীভাবে আমরা সেরা সিনেমা ও সিরিয়াল রিভিউ প্রদান করি।';
$seo_keywords = 'about us, আমাদের সম্পর্কে, MSR, movie review site';

// Breadcrumbs
$breadcrumbs = [
    ['name' => 'হোম', 'url' => '/'],
    ['name' => 'আমাদের সম্পর্কে', 'url' => '/about']
];

// Include header
include 'includes/header.php';
?>

<!-- Page Header -->
<section class="py-16 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600">
    <div class="container mx-auto px-4 text-center text-white">
        <!-- Breadcrumb -->
        <nav class="flex justify-center items-center space-x-2 text-sm text-white/80 mb-6">
            <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                <?php if ($index === count($breadcrumbs) - 1): ?>
                    <span><?php echo htmlspecialchars($breadcrumb['name']); ?></span>
                <?php else: ?>
                    <a href="<?php echo $breadcrumb['url']; ?>" class="hover:text-white transition-colors">
                        <?php echo htmlspecialchars($breadcrumb['name']); ?>
                    </a>
                    <span>→</span>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
        
        <h1 class="text-4xl lg:text-5xl font-bold mb-4">আমাদের সম্পর্কে</h1>
        <p class="text-xl lg:text-2xl text-white/90 max-w-3xl mx-auto">
            বাংলা ভাষায় সেরা সিনেমা ও সিরিয়াল রিভিউর নির্ভরযোগ্য প্ল্যাটফর্ম
        </p>
    </div>
</section>

<!-- About Content -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Main Story -->
            <div class="prose prose-lg max-w-none mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">আমাদের গল্প</h2>
                
                <p class="text-lg text-gray-700 leading-relaxed mb-6">
                    <strong>MSR (Movie & Series Review)</strong> হলো বাংলা ভাষায় সিনেমা ও সিরিয়াল রিভিউর একটি নির্ভরযোগ্য প্ল্যাটফর্ম। 
                    আমাদের উদ্দেশ্য হলো দর্শকদের সঠিক গাইডেন্স প্রদান করা যাতে তারা তাদের সময় ও অর্থের সদ্ব্যবহার করতে পারেন।
                </p>
                
                <p class="text-lg text-gray-700 leading-relaxed mb-6">
                    আমরা বিশ্বাস করি যে প্রতিটি সিনেমা ও সিরিয়ালের নিজস্ব মূল্য রয়েছে। আমাদের লক্ষ্য কোনো কন্টেন্টকে 
                    ভালো বা খারাপ বলা নয়, বরং তার শক্তিমত্তা ও দুর্বলতা নিরপেক্ষভাবে তুলে ধরা। আমরা প্রতিটি রিভিউতে 
                    কাহিনী, অভিনয়, পরিচালনা, সিনেমাটোগ্রাফি, এবং সামগ্রিক প্রভাব নিয়ে বিস্তারিত আলোচনা করি।
                </p>
                
                <p class="text-lg text-gray-700 leading-relaxed mb-8">
                    আমাদের টিম বিভিন্ন ব্যাকগ্রাউন্ডের সিনেমাপ্রেমী ও বিশেষজ্ঞদের নিয়ে গঠিত। আমরা বাংলা, হিন্দি, ইংরেজি, 
                    কোরিয়ান, জাপানি সহ বিভিন্ন ভাষার কন্টেন্ট রিভিউ করি। আমাদের প্রতিটি রিভিউ সতর্কতার সাথে তৈরি 
                    করা হয় যাতে স্পয়লার এড়িয়ে দর্শকদের সঠিক ধারণা দেওয়া যায়।
                </p>
            </div>
            
            <!-- Mission & Vision -->
            <div class="grid md:grid-cols-2 gap-8 mb-12">
                <div class="bg-blue-50 rounded-2xl p-8">
                    <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">আমাদের উদ্দেশ্য</h3>
                    <p class="text-gray-700 leading-relaxed">
                        বাংলা ভাষায় সিনেমা ও সিরিয়াল রিভিউর ক্ষেত্রে সর্বোচ্চ মানের কন্টেন্ট প্রদান করা। 
                        দর্শকদের সঠিক গাইডেন্স দিয়ে তাদের বিনোদনের মান উন্নত করা।
                    </p>
                </div>
                
                <div class="bg-green-50 rounded-2xl p-8">
                    <div class="w-16 h-16 bg-green-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">আমাদের দৃষ্টিভঙ্গি</h3>
                    <p class="text-gray-700 leading-relaxed">
                        বাংলাদেশের সেরা সিনেমা ও সিরিয়াল রিভিউ প্ল্যাটফর্ম হিসেবে প্রতিষ্ঠিত হওয়া। 
                        বিনোদন শিল্পের উন্নয়নে অবদান রাখা।
                    </p>
                </div>
            </div>
            
            <!-- What We Do -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">আমরা যা করি</h2>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">বিস্তারিত রিভিউ</h3>
                        <p class="text-gray-600">
                            প্রতিটি সিনেমা ও সিরিয়ালের গভীর বিশ্লেষণ, কাহিনী, অভিনয়, পরিচালনা সবকিছু নিয়ে আলোচনা।
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">রেটিং সিস্টেম</h3>
                        <p class="text-gray-600">
                            ৫ স্টার রেটিং সিস্টেমের মাধ্যমে দ্রুত ধারণা পাওয়া। স্বচ্ছ ও নিরপেক্ষ মূল্যায়ন।
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">কমিউনিটি</h3>
                        <p class="text-gray-600">
                            ব্যবহারকারীদের নিজস্ব রিভিউ জমা দেওয়ার সুবিধা। একসাথে সিনেমাপ্রেমীদের কমিউনিটি গড়া।
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">আমাদের পরিসংখ্যান</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-4xl font-bold text-blue-600 mb-2">
                    <?php echo number_format($total_reviews); ?>+
                </div>
                <div class="text-gray-600">রিভিউ প্রকাশিত</div>
            </div>
            
            <div class="text-center">
                <div class="text-4xl font-bold text-green-600 mb-2">
                    <?php echo number_format($total_categories); ?>+
                </div>
                <div class="text-gray-600">ক্যাটেগরি</div>
            </div>
            
            <div class="text-center">
                <div class="text-4xl font-bold text-purple-600 mb-2">
                    <?php echo number_format($total_views); ?>+
                </div>
                <div class="text-gray-600">মোট ভিউ</div>
            </div>
            
            <div class="text-center">
                <div class="text-4xl font-bold text-red-600 mb-2">
                    ২০২৫
                </div>
                <div class="text-gray-600">প্রতিষ্ঠিত</div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">আমাদের টিম</h2>
            
            <div class="bg-gray-50 rounded-2xl p-8">
                <div class="w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-white font-bold text-2xl">MA</span>
                </div>
                
                <h3 class="text-2xl font-bold text-gray-900 mb-2">মাসুম আহমেদ</h3>
                <p class="text-blue-600 font-medium mb-4">প্রতিষ্ঠাতা ও প্রধান সম্পাদক</p>
                
                <p class="text-gray-700 leading-relaxed mb-6">
                    সিনেমা ও প্রযুক্তিপ্রেমী। ১০+ বছরের অভিজ্ঞতা নিয়ে MSR প্রতিষ্ঠা করেছেন। 
                    তার লক্ষ্য বাংলা ভাষায় মানসম্পন্ন রিভিউ কন্টেন্ট তৈরি করা।
                </p>
                
                <div class="flex justify-center space-x-4">
                    <a href="mailto:msa.masum.bd@gmail.com" 
                       class="text-blue-600 hover:text-blue-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-purple-600">
    <div class="container mx-auto px-4 text-center">
        <div class="max-w-3xl mx-auto text-white">
            <h2 class="text-3xl lg:text-4xl font-bold mb-6">
                আমাদের সাথে যুক্ত হন
            </h2>
            <p class="text-xl text-white/90 mb-8">
                আপনিও সিনেমাপ্রেমী? আমাদের কমিউনিটির অংশ হয়ে আপনার প্রিয় সিনেমা ও সিরিয়ালের রিভিউ শেয়ার করুন।
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/submit-review" 
                   class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-xl font-semibold text-lg transition-colors">
                    রিভিউ জমা দিন
                </a>
                <a href="/contact" 
                   class="border-2 border-white text-white hover:bg-white hover:text-blue-600 px-8 py-4 rounded-xl font-semibold text-lg transition-colors">
                    যোগাযোগ করুন
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>