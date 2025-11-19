<?php
/**
 * Privacy Policy Page
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config/config.php';

// SEO Meta Data
$seo_title = 'গোপনীয়তা নীতি - ' . getSiteSetting('site_name', SITE_NAME);
$seo_description = 'Cine Review এর গোপনীয়তা নীতি। আমরা কীভাবে কন্টেন্ট তৈরি করি এবং কী ধরনের পাবলিক তথ্য ব্যবহার করি সে সম্পর্কে জানুন।';
$seo_keywords = 'privacy policy, গোপনীয়তা নীতি, content policy, কন্টেন্ট নীতি';

// Breadcrumbs
$breadcrumbs = [
    ['name' => 'হোম', 'url' => '/'],
    ['name' => 'গোপনীয়তা নীতি', 'url' => '/privacy']
];

// Include header
include 'includes/header.php';
?>

<!-- Page Header -->
<section class="py-16 bg-gradient-to-r from-green-600 via-blue-600 to-purple-600">
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
        
        <h1 class="text-4xl lg:text-5xl font-bold mb-4">গোপনীয়তা নীতি</h1>
        <p class="text-xl lg:text-2xl text-white/90 max-w-3xl mx-auto">
            আমাদের কন্টেন্ট নীতি ও তথ্য ব্যবহারের নিয়মাবলী
        </p>
    </div>
</section>

<!-- Privacy Policy Content -->
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
                    <strong>Cine Review</strong> একটি সিনেমা ও সিরিয়াল রিভিউ প্ল্যাটফর্ম যেখানে আমরা চলচ্চিত্র ও নাটক সিরিয়াল সম্পর্কে রিভিউ, 
                    চরিত্র বিশ্লেষণ, পরিচালনা পর্যালোচনা ইত্যাদি প্রকাশ করি। আমরা সিনেমা সংশ্লিষ্ট ফেসবুক গ্রুপ থেকে ভালো রিভিউ সংগ্রহ করে 
                    সেগুলোতে আরও বিস্তারিত তথ্য, ফরম্যাটিং (H1, H2 ট্যাগ) ও বিশ্লেষণ যোগ করে সম্পূর্ণ ব্লগ পোস্ট তৈরি করি এবং 
                    যথাযথ লেখকের কৃতিত্ব সহ প্রকাশ করি।
                </p>
                
                <p class="text-lg text-gray-700 leading-relaxed mb-8">
                    <strong>গুরুত্বপূর্ণ:</strong> আমরা ব্যবহারকারীদের কোনো ব্যক্তিগত তথ্য সংগ্রহ করি না। এই নীতিতে আমরা ব্যাখ্যা করেছি যে আমরা কীভাবে 
                    কন্টেন্ট তৈরি করি এবং কী ধরনের পাবলিক তথ্য ব্যবহার করি।
                </p>
            </div>

            <!-- No Data Collection -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">আমরা কোনো ব্যক্তিগত তথ্য সংগ্রহ করি না</h2>
                
                <div class="bg-green-50 border border-green-200 rounded-xl p-8 mb-8">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-green-900 mb-4">গোপনীয়তার নিশ্চয়তা</h3>
                            <p class="text-green-800 mb-4">
                                Cine Review একটি কন্টেন্ট পাবলিশিং সাইট যেখানে কোনো ব্যবহারকারী রেজিস্ট্রেশন, লগইন সিস্টেম বা 
                                ব্যক্তিগত তথ্য সংগ্রহের ব্যবস্থা নেই।
                            </p>
                            <ul class="text-green-800 space-y-2">
                                <li>• কোনো ইমেইল ঠিকানা সংগ্রহ করি না</li>
                                <li>• কোনো ব্যক্তিগত তথ্য সংরক্ষণ করি না</li>
                                <li>• কোনো ব্যবহারকারী ট্র্যাকিং করি না</li>
                                <li>• কোনো অ্যাকাউন্ট সিস্টেম নেই</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Creation Process -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">আমাদের কন্টেন্ট তৈরির প্রক্রিয়া</h2>
                
                <div class="space-y-8">
                    <!-- Facebook Review Process -->
                    <div class="bg-blue-50 rounded-xl p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">
                            <svg class="w-8 h-8 inline-block mr-3 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            ফেসবুক গ্রুপ থেকে রিভিউ সংগ্রহ
                        </h3>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-bold text-gray-900 mb-3">আমরা যা করি:</h4>
                                <ul class="text-gray-700 space-y-2">
                                    <li class="flex items-start space-x-3">
                                        <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                        <span>সিনেমা সংক্রান্ত ফেসবুক গ্রুপে ভালো রিভিউ খুঁজি</span>
                                    </li>
                                    <li class="flex items-start space-x-3">
                                        <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                        <span>রিভিউ কপি করে আরও বিস্তারিত তথ্য যোগ করি</span>
                                    </li>
                                    <li class="flex items-start space-x-3">
                                        <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                        <span>H1, H2 ট্যাগ দিয়ে সুন্দর ফরম্যাটিং করি</span>
                                    </li>
                                    <li class="flex items-start space-x-3">
                                        <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                        <span>সম্পূর্ণ ব্লগ পোস্ট আকারে প্রকাশ করি</span>
                                    </li>
                                </ul>
                            </div>
                            
                            <div>
                                <h4 class="font-bold text-gray-900 mb-3">কৃতিত্ব প্রদান:</h4>
                                <ul class="text-gray-700 space-y-2">
                                    <li class="flex items-start space-x-3">
                                        <span class="w-2 h-2 bg-green-600 rounded-full mt-2 flex-shrink-0"></span>
                                        <span>ফেসবুক থেকে শুধুমাত্র রিভিউয়ারের নাম নিই</span>
                                    </li>
                                    <li class="flex items-start space-x-3">
                                        <span class="w-2 h-2 bg-green-600 rounded-full mt-2 flex-shrink-0"></span>
                                        <span>রিভিউ পোস্টে লেখকের নাম স্পষ্টভাবে উল্লেখ করি</span>
                                    </li>
                                    <li class="flex items-start space-x-3">
                                        <span class="w-2 h-2 bg-green-600 rounded-full mt-2 flex-shrink-0"></span>
                                        <span>মূল লেখকের যথাযথ সম্মান প্রদান করি</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Movie Data Collection -->
                    <div class="bg-purple-50 rounded-xl p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">
                            <svg class="w-8 h-8 inline-block mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            সিনেমা ও সিরিয়াল তথ্য সংগ্রহ
                        </h3>
                        
                        <p class="text-gray-700 mb-6">
                            আমরা সিনেমা ও সিরিয়াল সম্পর্কিত সব তথ্য পাবলিক সোর্স থেকে সংগ্রহ করি:
                        </p>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-bold text-gray-900 mb-3">প্রধান উৎস:</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                            <span class="text-orange-600 font-bold text-sm">W</span>
                                        </div>
                                        <span class="text-gray-700">Wikipedia</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                            <span class="text-yellow-600 font-bold text-sm">I</span>
                                        </div>
                                        <span class="text-gray-700">IMDb</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="font-bold text-gray-900 mb-3">যে তথ্য সংগ্রহ করি:</h4>
                                <ul class="text-gray-700 space-y-1 text-sm">
                                    <li>• চলচ্চিত্রের নাম</li>
                                    <li>• কাস্ট ও অভিনেতা-অভিনেত্রী</li>
                                    <li>• পরিচালকের নাম</li>
                                    <li>• জেনার (ধরন)</li>
                                    <li>• মুক্তির তারিখ</li>
                                    <li>• ভাষা</li>
                                    <li>• দেশ</li>
                                    <li>• রানটাইম</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Website Analytics -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">ওয়েবসাইট পরিসংখ্যান</h2>
                
                <div class="bg-gray-50 rounded-xl p-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4">যা আমরা ট্র্যাক করি:</h3>
                            <ul class="text-gray-700 space-y-2">
                                <li class="flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <span>পেজ ভিউ সংখ্যা</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>পেজে কত সময় কাটানো হয়</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    <span>জনপ্রিয় কন্টেন্ট</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4">উদ্দেশ্য:</h3>
                            <p class="text-gray-700 mb-4">
                                এই তথ্য আমাদের সাইটের কার্যকারিতা উন্নত করতে এবং দর্শকদের পছন্দ অনুযায়ী 
                                আরও ভালো কন্টেন্ট তৈরি করতে সাহায্য করে।
                            </p>
                            <p class="text-gray-600 text-sm">
                                এই সব তথ্য সম্পূর্ণ বেনামী এবং কোনো ব্যক্তিগত পরিচয় সংযুক্ত নয়।
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright and Fair Use -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">কপিরাইট ও ন্যায্য ব্যবহার</h2>
                
                <div class="space-y-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                        <h3 class="text-xl font-bold text-yellow-900 mb-4">আমাদের নীতি</h3>
                        <ul class="text-yellow-800 space-y-3">
                            <li class="flex items-start space-x-3">
                                <span class="w-2 h-2 bg-yellow-600 rounded-full mt-2 flex-shrink-0"></span>
                                <span>সব কন্টেন্ট শিক্ষামূলক ও পর্যালোচনার উদ্দেশ্যে ব্যবহৃত</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="w-2 h-2 bg-yellow-600 rounded-full mt-2 flex-shrink-0"></span>
                                <span>মূল লেখকের সম্পূর্ণ কৃতিত্ব প্রদান</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="w-2 h-2 bg-yellow-600 rounded-full mt-2 flex-shrink-0"></span>
                                <span>যে কোনো কপিরাইট সমস্যায় তাৎক্ষণিক সমাধান</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="bg-blue-50 rounded-xl p-6">
                        <h3 class="text-xl font-bold text-blue-900 mb-4">আপনার অধিকার</h3>
                        <p class="text-blue-800 mb-4">
                            যদি আপনার কোনো কন্টেন্ট আমাদের সাইটে ব্যবহৃত হয়ে থাকে এবং আপনি চান যে সেটি সরিয়ে ফেলা হোক, 
                            তাহলে আমাদের সাথে যোগাযোগ করুন। আমরা ২৪ ঘন্টার মধ্যে সেটি সরিয়ে ফেলব।
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
                            <h3 class="text-xl font-bold text-gray-900 mb-6">আমাদের সাথে যোগাযোগ করুন</h3>
                            
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
                            <h3 class="text-xl font-bold text-gray-900 mb-6">যোগাযোগের কারণ</h3>
                            <ul class="text-gray-700 space-y-3">
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>কপিরাইট সংক্রান্ত বিষয়</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>কন্টেন্ট অপসারণের অনুরোধ</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>সাধারণ প্রশ্ন ও পরামর্শ</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>সহযোগিতার প্রস্তাব</span>
                                </li>
                            </ul>
                            
                            <div class="bg-white rounded-lg p-4 mt-6">
                                <p class="text-sm text-gray-600">
                                    <strong>প্রতিক্রিয়ার সময়:</strong> আমরা সর্বোচ্চ ২ৄ ঘন্টার মধ্যে আপনার বার্তার উত্তর দেওয়ার চেষ্টা করি।
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disclaimer -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">দাবিত্যাগ</h2>
                
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-8">
                    <div class="space-y-4 text-gray-700">
                        <p>
                            • এই ওয়েবসাইটের সব রিভিউ ও মতামত লেখকদের ব্যক্তিগত মতামত, Cine Review এর সাথে সেগুলোর সরাসরি সম্পর্ক নেই।
                        </p>
                        <p>
                            • আমরা যথাসাধ্য চেষ্টা করি সঠিক তথ্য প্রকাশ করতে, তবে কোনো ভুল তথ্যের জন্য আমরা দায়বদ্ধ নই।
                        </p>
                        <p>
                            • যে কোনো কপিরাইট বা আইনি সমস্যার ক্ষেত্রে আমরা সঙ্গে সঙ্গে প্রয়োজনীয় ব্যবস্থা নেব।
                        </p>
                        <p>
                            • এই সাইট ব্যবহার করার মাধ্যমে আপনি এই শর্তাবলী মেনে নিতে সম্মত হচ্ছেন।
                        </p>
                    </div>
                </div>
            </div>

            <!-- Policy Updates -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">নীতি আপডেট</h2>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                    <div class="flex items-start space-x-4">
                        <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold text-yellow-900 mb-2">নীতি পরিবর্তন</h3>
                            <p class="text-yellow-800 mb-4">
                                আমরা সময়ে সময়ে এই গোপনীয়তা নীতি আপডেট করতে পারি। কোনো পরিবর্তন হলে আমরা 
                                এই পাতায় নতুন তারিখ সহ আপডেট প্রকাশ করব।
                            </p>
                            <p class="text-yellow-800">
                                নিয়মিত এই পাতা চেক করার পরামর্শ দিচ্ছি যাতে আপডেট সম্পর্কে অবগত থাকতে পারেন।
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>