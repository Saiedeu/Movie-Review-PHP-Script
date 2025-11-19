<?php
/**
 * Terms of Service Page
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config/config.php';

// SEO Meta Data
$seo_title = 'ব্যবহারের শর্তাবলী - ' . getSiteSetting('site_name', SITE_NAME);
$seo_description = 'Cine Review এর ব্যবহারের শর্তাবলী ও নিয়মাবলী। রিভিউ লেখার গাইডলাইন এবং সাইট ব্যবহারের নীতিমালা।';
$seo_keywords = 'terms of service, ব্যবহারের শর্তাবলী, review guidelines, রিভিউ গাইডলাইন';

// Breadcrumbs
$breadcrumbs = [
    ['name' => 'হোম', 'url' => '/'],
    ['name' => 'ব্যবহারের শর্তাবলী', 'url' => '/terms']
];

// Include header
include 'includes/header.php';
?>

<!-- Page Header -->
<section class="py-16 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600">
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
        
        <h1 class="text-4xl lg:text-5xl font-bold mb-4">ব্যবহারের শর্তাবলী</h1>
        <p class="text-xl lg:text-2xl text-white/90 max-w-3xl mx-auto">
            Cine Review ব্যবহারের নিয়মাবলী ও রিভিউ লেখার গাইডলাইন
        </p>
    </div>
</section>

<!-- Terms Content -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Last Updated -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-lg mb-8">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-blue-900">সর্বশেষ আপডেট</h3>
                        <p class="text-blue-700">জানুয়ারি ২০২৫</p>
                    </div>
                </div>
            </div>

            <!-- Introduction -->
            <div class="prose prose-lg max-w-none mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">ভূমিকা</h2>
                
                <p class="text-lg text-gray-700 leading-relaxed mb-6">
                    Cine Review একটি সিনেমা ও সিরিয়াল রিভিউ প্ল্যাটফর্ম। এই সাইট ব্যবহার করার মাধ্যমে আপনি 
                    নিম্নলিখিত শর্তাবলী মেনে নিতে সম্মত হচ্ছেন। এই শর্তাবলী পড়ুন এবং বুঝে নিন।
                </p>
                
                <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-8">
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20a2 2 0 01-2-2v-2a2 2 0 012-2h2.262M7 20H2v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM9 9a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-bold text-green-900 mb-2">আমাদের কমিউনিটিতে যোগ দিন</h3>
                            <p class="text-green-800">
                                দেখেছেন কোনো দুর্দান্ত সিনেমা বা সিরিয়াল? আপনার অভিজ্ঞতা অন্যদের সাথে শেয়ার করুন এবং 
                                আমাদের কমিউনিটির অংশ হয়ে উঠুন।
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Guidelines -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">রিভিউ লেখার গাইডলাইন</h2>
                
                <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-2xl p-8 mb-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6">
                                <svg class="w-6 h-6 inline-block mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                                মূল নিয়মাবলী
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm">১</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">সিনেমা/সিরিয়ালের নাম</h4>
                                        <p class="text-gray-700 text-sm">সিনেমা বা সিরিয়ালের নাম স্পষ্ট করে লিখুন</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm">২</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">ন্যূনতম শব্দ সংখ্যা</h4>
                                        <p class="text-gray-700 text-sm">আপনার রিভিউ অন্তত <strong>২০০ শব্দের</strong> হতে হবে</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm">৩</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">পোস্টার ইমেজ</h4>
                                        <p class="text-gray-700 text-sm">পোস্টার ইমেজ <strong>১৯২০x১০৮০</strong> রেজোলিউশন হলে ভালো হয়</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6">
                                <svg class="w-6 h-6 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                ফরম্যাটিং নিয়ম
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm">৪</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">টেক্সট ফরম্যাটিং</h4>
                                        <p class="text-gray-700 text-sm">লিস্ট, বোল্ড/ইতালিক ব্যবহার করুন</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm">৫</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">ভাষা নীতি</h4>
                                        <p class="text-gray-700 text-sm">অশ্লীল বা আপত্তিজনক ভাষা ব্যবহার করবেন না</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm">৬</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">প্রকাশনা</h4>
                                        <p class="text-gray-700 text-sm">আপনার রিভিউ অনুমোদনের পর প্রকাশিত হবে</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Spoiler Guidelines -->
                <div class="bg-red-50 border border-red-200 rounded-xl p-8 mb-8">
                    <h3 class="text-2xl font-bold text-red-900 mb-6">
                        <svg class="w-8 h-8 inline-block mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        স্পয়লার নীতিমালা
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="font-bold text-red-900 mb-4">স্পয়লার কী?</h4>
                            <p class="text-red-800 mb-4">
                                যদি আপনার রিভিউতে গল্পের গুরুত্বপূর্ণ তথ্য বা সারপ্রাইজ থাকে যা অন্যদের দেখার আগ্রহ নষ্ট করতে পারে, 
                                তাহলে সেগুলো স্পয়লার।
                            </p>
                            
                            <div class="bg-white rounded-lg p-4">
                                <h5 class="font-semibold text-red-900 mb-2">স্পয়লারের উদাহরণ:</h5>
                                <ul class="text-red-800 text-sm space-y-1">
                                    <li>• গল্পের শেষ</li>
                                    <li>• চরিত্রের মৃত্যু</li>
                                    <li>• অপ্রত্যাশিত ঘটনা</li>
                                    <li>• প্লট টুইস্ট</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-bold text-red-900 mb-4">স্পয়লার কীভাবে লিখবেন?</h4>
                            <div class="space-y-4">
                                <div class="bg-white rounded-lg p-4 border-l-4 border-orange-500">
                                    <p class="text-red-800 mb-2">
                                        স্পয়লার থাকলে স্পয়লার বক্সে আলাদা করে লিখুন। এটি একটি বিশেষ "স্পয়লার দেখান" বাটনের ভিতরে লুকানো থাকবে।
                                    </p>
                                </div>
                                
                                <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                                    <p class="text-red-800">
                                        <strong>বিশেষ দ্রষ্টব্য:</strong> যদি কোনো গুরুত্বপূর্ণ তথ্য অসম্পূর্ণ থাকে, তাহলে আমাদের সম্পাদনা টিম 
                                        সেটি সংযোজন করে প্রকাশ করবে।
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Submission Rules -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">কন্টেন্ট জমা দেওয়ার নিয়ম</h2>
                
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div class="bg-green-50 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-green-900 mb-4">
                                <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                গ্রহণযোগ্য কন্টেন্ট
                            </h3>
                            <ul class="text-green-800 space-y-2">
                                <li>• সিনেমা রিভিউ</li>
                                <li>• টিভি সিরিয়াল রিভিউ</li>
                                <li>• ওয়েব সিরিজ রিভিউ</li>
                                <li>• চরিত্র বিশ্লেষণ</li>
                                <li>• পরিচালনা পর্যালোচনা</li>
                                <li>• অভিনয় মূল্যায়ন</li>
                            </ul>
                        </div>
                        
                        <div class="bg-blue-50 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-blue-900 mb-4">
                                <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                মান নিয়ন্ত্রণ
                            </h3>
                            <ul class="text-blue-800 space-y-2">
                                <li>• সব রিভিউ মডারেশনের মধ্য দিয়ে যাবে</li>
                                <li>• ব্যাকরণ ও বানান চেক করা হবে</li>
                                <li>• প্রয়োজনে সম্পাদনা করা হবে</li>
                                <li>• অনুপযুক্ত কন্টেন্ট প্রত্যাখ্যান করা হবে</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="bg-red-50 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-red-900 mb-4">
                                <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                </svg>
                                নিষিদ্ধ কন্টেন্ট
                            </h3>
                            <ul class="text-red-800 space-y-2">
                                <li>• অশ্লীল বা আপত্তিজনক ভাষা</li>
                                <li>• ব্যক্তিগত আক্রমণ</li>
                                <li>• ধর্মীয় বা রাজনৈতিক বিদ্বেষ</li>
                                <li>• কপিরাইট লঙ্ঘন</li>
                                <li>• ভুয়া তথ্য</li>
                                <li>• স্প্যাম কন্টেন্ট</li>
                            </ul>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-yellow-900 mb-4">
                                <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                প্রকাশনার সময়সীমা
                            </h3>
                            <ul class="text-yellow-800 space-y-2">
                                <li>• রিভিউ জমা দেওয়ার ২৪-৪৮ ঘন্টার মধ্যে পর্যালোচনা</li>
                                <li>• অনুমোদিত হলে ৭২ ঘন্টার মধ্যে প্রকাশ</li>
                                <li>• প্রত্যাখ্যাত হলে কারণ সহ জানানো হবে</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Rights and Responsibilities -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">ব্যবহারকারীর অধিকার ও দায়িত্ব</h2>
                
                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">আপনার অধিকার</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-green-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-gray-900">কৃতিত্ব পাওয়ার অধিকার</h4>
                                    <p class="text-gray-700 text-sm">আপনার লেখা রিভিউতে আপনার নাম উল্লেখ থাকবে</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-green-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-gray-900">সম্পাদনা অবগতি</h4>
                                    <p class="text-gray-700 text-sm">আপনার রিভিউ সম্পাদনা করা হলে আপনাকে জানানো হবে</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-green-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-gray-900">প্রত্যাহার অধিকার</h4>
                                    <p class="text-gray-700 text-sm">যে কোনো সময় আপনার কন্টেন্ট প্রত্যাহারের অনুরোধ করতে পারেন</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-green-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-gray-900">ফিডব্যাক পাওয়া</h4>
                                    <p class="text-gray-700 text-sm">আপনার রিভিউ সম্পর্কে গঠনমূলক ফিডব্যাক পাবেন</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">আপনার দায়িত্ব</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-blue-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-gray-900">সঠিক তথ্য প্রদান</h4>
                                    <p class="text-gray-700 text-sm">সিনেমা/সিরিয়াল সম্পর্কে সঠিক ও যাচাইকৃত তথ্য দিন</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-blue-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-gray-900">মৌলিক কন্টেন্ট</h4>
                                    <p class="text-gray-700 text-sm">আপনার নিজের মতামত ও বিশ্লেষণ প্রদান করুন</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-blue-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-gray-900">শালীন ভাষা ব্যবহার</h4>
                                    <p class="text-gray-700 text-sm">সম্মানজনক ও শালীন ভাষায় রিভিউ লিখুন</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-blue-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-gray-900">কপিরাইট সম্মান</h4>
                                    <p class="text-gray-700 text-sm">অন্যের কন্টেন্ট কপি না করে নিজের মতামত দিন</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright and Intellectual Property -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">কপিরাইট ও বৌদ্ধিক সম্পদ</h2>
                
                <div class="bg-gray-50 rounded-xl p-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4">আপনার কন্টেন্ট</h3>
                            <ul class="text-gray-700 space-y-3">
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-green-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>আপনি আপনার লেখা রিভিউর মূল লেখক থাকবেন</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-green-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>Cine Review প্ল্যাটফর্মে প্রকাশের লাইসেন্স প্রদান করছেন</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-green-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>আমরা সম্পাদনা ও উন্নতির অধিকার রাখি</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4">আমাদের কন্টেন্ট</h3>
                            <ul class="text-gray-700 space-y-3">
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>সাইটের ডিজাইন ও কাঠামো আমাদের সম্পত্তি</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>সিনেমা পোস্টার ও ছবি মূল নির্মাতাদের সম্পত্তি</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>ন্যায্য ব্যবহারের অধীনে শিক্ষামূলক উদ্দেশ্যে ব্যবহৃত</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Site Usage Terms -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">সাইট ব্যবহারের নিয়ম</h2>
                
                <div class="space-y-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <h3 class="text-xl font-bold text-blue-900 mb-4">অনুমোদিত ব্যবহার</h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <ul class="text-blue-800 space-y-2">
                                <li>• রিভিউ পড়া ও শেয়ার করা</li>
                                <li>• নিজের রিভিউ জমা দেওয়া</li>
                                <li>• সিনেমা ও সিরিয়াল খোঁজা</li>
                                <li>• শিক্ষামূলক উদ্দেশ্যে ব্যবহার</li>
                            </ul>
                            <ul class="text-blue-800 space-y-2">
                                <li>• ব্যক্তিগত গবেষণা</li>
                                <li>• সামাজিক মাধ্যমে শেয়ার</li>
                                <li>• রেফারেন্স হিসেবে ব্যবহার</li>
                                <li>• বন্ধুদের সাথে আলোচনা</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                        <h3 class="text-xl font-bold text-red-900 mb-4">নিষিদ্ধ কার্যকলাপ</h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <ul class="text-red-800 space-y-2">
                                <li>• সাইটের কন্টেন্ট কপি করা</li>
                                <li>• বাণিজ্যিক উদ্দেশ্যে ব্যবহার</li>
                                <li>• স্প্যাম বা ভাইরাস পাঠানো</li>
                                <li>• হ্যাকিং বা আক্রমণ</li>
                            </ul>
                            <ul class="text-red-800 space-y-2">
                                <li>• ভুয়া তথ্য প্রচার</li>
                                <li>• অন্যের পরিচয়ে ছদ্মবেশ</li>
                                <li>• সাইটের ক্ষতি করা</li>
                                <li>• আইন বিরোধী কাজ</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disclaimer -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">দাবিত্যাগ</h2>
                
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-8">
                    <div class="space-y-4 text-gray-700">
                        <p class="flex items-start space-x-3">
                            <span class="w-2 h-2 bg-gray-600 rounded-full mt-2 flex-shrink-0"></span>
                            <span>
                                <strong>মতামতের দায়ভার:</strong> এই সাইটের সব রিভিউ ও মতামত লেখকদের ব্যক্তিগত মতামত। 
                                Cine Review প্রশাসন এই মতামতের সাথে সরাসরি একমত নাও হতে পারে।
                            </span>
                        </p>
                        <p class="flex items-start space-x-3">
                            <span class="w-2 h-2 bg-gray-600 rounded-full mt-2 flex-shrink-0"></span>
                            <span>
                                <strong>তথ্যের নির্ভরযোগ্যতা:</strong> আমরা যথাসাধ্য চেষ্টা করি সঠিক তথ্য প্রকাশ করতে, 
                                তবে কোনো ভুল তথ্যের জন্য আমরা সম্পূর্ণ দায়বদ্ধ নই।
                            </span>
                        </p>
                        <p class="flex items-start space-x-3">
                            <span class="w-2 h-2 bg-gray-600 rounded-full mt-2 flex-shrink-0"></span>
                            <span>
                                <strong>সিদ্ধান্তের দায়:</strong> কোনো সিনেমা বা সিরিয়াল দেখার সিদ্ধান্ত সম্পূর্ণভাবে দর্শকের। 
                                আমাদের রিভিউ শুধুমাত্র গাইডলাইন হিসেবে ব্যবহার করুন।
                            </span>
                        </p>
                        <p class="flex items-start space-x-3">
                            <span class="w-2 h-2 bg-gray-600 rounded-full mt-2 flex-shrink-0"></span>
                            <span>
                                <strong>বাহ্যিক লিংক:</strong> আমাদের সাইটে অন্য ওয়েবসাইটের লিংক থাকতে পারে। 
                                সেই সাইটগুলোর কন্টেন্টের জন্য আমরা দায়ী নই।
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">যোগাযোগ</h2>
                
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6">শর্তাবলী সংক্রান্ত প্রশ্ন</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">মাসুম আহমেদ</h4>
                                        <p class="text-gray-600">প্রতিষ্ঠাতা ও সম্পাদক</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">ইমেইল</h4>
                                        <p class="text-gray-600">info.saidur_bd@aol.com</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">ফোন</h4>
                                        <p class="text-gray-600">+974-66489944</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6">কখন যোগাযোগ করবেন</h3>
                            <ul class="text-gray-700 space-y-3">
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>শর্তাবলী সম্পর্কে প্রশ্ন</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>রিভিউ জমা দিতে সমস্যা</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>কন্টেন্ট অপসারণের অনুরোধ</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>কপিরাইট সংক্রান্ত বিষয়</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>সাধারণ পরামর্শ ও ফিডব্যাক</span>
                                </li>
                            </ul>
                            
                            <div class="bg-white rounded-lg p-4 mt-6">
                                <p class="text-sm text-gray-600">
                                    <strong>উত্তরের সময়:</strong> আমরা ২৪-৪৮ ঘন্টার মধ্যে আপনার প্রশ্নের উত্তর দেওয়ার চেষ্টা করি।
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms Updates -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">শর্তাবলী আপডেট</h2>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                    <div class="flex items-start space-x-4">
                        <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold text-yellow-900 mb-2">শর্তাবলী পরিবর্তন</h3>
                            <p class="text-yellow-800 mb-4">
                                আমরা প্রয়োজন অনুযায়ী এই শর্তাবলী পরিবর্তন করার অধিকার রাখি। কোনো পরিবর্তন হলে 
                                এই পাতায় নতুন তারিখ সহ আপডেট প্রকাশ করা হবে।
                            </p>
                            <p class="text-yellow-800">
                                <strong>গুরুত্বপূর্ণ:</strong> এই সাইট ব্যবহার অব্যাহত রাখার মাধ্যমে আপনি আপডেটেড শর্তাবলী মেনে নিতে সম্মত হচ্ছেন।
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>