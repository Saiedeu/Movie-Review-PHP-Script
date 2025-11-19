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
$seo_description = 'Cine Review এর গোপনীয়তা নীতি। আমরা কীভাবে আপনার ব্যক্তিগত তথ্য সংগ্রহ, ব্যবহার এবং সুরক্ষিত রাখি সে সম্পর্কে জানুন।';
$seo_keywords = 'privacy policy, গোপনীয়তা নীতি, data protection, তথ্য সুরক্ষা';

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
            আপনার তথ্যের নিরাপত্তা ও গোপনীয়তা আমাদের অগ্রাধিকার
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
                    চরিত্র বিশ্লেষণ, পরিচালনা পর্যালোচনা ইত্যাদি প্রকাশ করি। আমরা সিনেমা সংশ্লিষ্ট ফেসবুক গ্রুপ থেকেও যথাযথ 
                    লেখকের কৃতিত্ব সহ রিভিউ প্রকাশ করে থাকি।
                </p>
                
                <p class="text-lg text-gray-700 leading-relaxed mb-8">
                    এই গোপনীয়তা নীতিতে আমরা ব্যাখ্যা করেছি যে আমরা কীভাবে আপনার ব্যক্তিগত তথ্য সংগ্রহ, ব্যবহার, সংরক্ষণ 
                    এবং সুরক্ষিত রাখি। আমাদের সেবা ব্যবহার করার মাধ্যমে আপনি এই নীতিতে সম্মত হচ্ছেন।
                </p>
            </div>

            <!-- Information We Collect -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">আমরা যে তথ্য সংগ্রহ করি</h2>
                
                <div class="space-y-8">
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            <svg class="w-6 h-6 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            ব্যক্তিগত তথ্য
                        </h3>
                        <ul class="text-gray-700 space-y-2">
                            <li>• নাম ও ইমেইল ঠিকানা (রেজিস্ট্রেশনের সময়)</li>
                            <li>• প্রোফাইল ছবি (ঐচ্ছিক)</li>
                            <li>• রিভিউ ও মন্তব্যের তথ্য</li>
                            <li>• যোগাযোগের তথ্য</li>
                        </ul>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            <svg class="w-6 h-6 inline-block mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            ব্যবহার সংক্রান্ত তথ্য
                        </h3>
                        <ul class="text-gray-700 space-y-2">
                            <li>• আইপি ঠিকানা ও ব্রাউজার তথ্য</li>
                            <li>• সাইট ব্যবহারের পরিসংখ্যান</li>
                            <li>• পেজ ভিউ ও ক্লিক ডেটা</li>
                            <li>• কুকিজ ও স্থানীয় স্টোরেজ</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- How We Use Information -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">আমরা কীভাবে তথ্য ব্যবহার করি</h2>
                
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6M4 6v13a2 2 0 002 2h12a2 2 0 002-2V6"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">সেবা প্রদান</h3>
                                <p class="text-gray-700">আপনার অ্যাকাউন্ট পরিচালনা এবং ব্যক্তিগতকৃত কন্টেন্ট প্রদান</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">নিরাপত্তা</h3>
                                <p class="text-gray-700">স্প্যাম ও অপব্যবহার রোধ, সাইটের নিরাপত্তা নিশ্চিতকরণ</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">বিশ্লেষণ</h3>
                                <p class="text-gray-700">সাইটের কার্যকারিতা উন্নতি ও ব্যবহারকারীর অভিজ্ঞতা বৃদ্ধি</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">যোগাযোগ</h3>
                                <p class="text-gray-700">আপডেট, নোটিফিকেশন ও গুরুত্বপূর্ণ সংবাদ প্রেরণ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Sharing -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">তথ্য শেয়ারিং</h2>
                
                <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold text-red-900 mb-2">গুরুত্বপূর্ণ ঘোষণা</h3>
                            <p class="text-red-800">
                                আমরা কখনোই আপনার ব্যক্তিগত তথ্য তৃতীয় পক্ষের কাছে বিক্রি, ভাড়া বা বিনিময় করি না।
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">আমরা তথ্য শেয়ার করি যখন:</h3>
                        <ul class="text-gray-700 space-y-3">
                            <li class="flex items-start space-x-3">
                                <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                <span>আইনি প্রয়োজনে বা আদালতের আদেশে</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                <span>সাইটের নিরাপত্তা ও অখণ্ডতা রক্ষার জন্য</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></span>
                                <span>আপনার স্পষ্ট সম্মতি থাকলে</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Data Security -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">তথ্য নিরাপত্তা</h2>
                
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="bg-green-50 rounded-xl p-6 text-center">
                        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">এনক্রিপশন</h3>
                        <p class="text-gray-700 text-sm">সকল ডেটা SSL এনক্রিপশন দিয়ে সুরক্ষিত</p>
                    </div>
                    
                    <div class="bg-blue-50 rounded-xl p-6 text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">সুরক্ষিত সার্ভার</h3>
                        <p class="text-gray-700 text-sm">ফায়ারওয়াল ও নিয়মিত নিরাপত্তা আপডেট</p>
                    </div>
                    
                    <div class="bg-purple-50 rounded-xl p-6 text-center">
                        <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">প্রবেশাধিকার নিয়ন্ত্রণ</h3>
                        <p class="text-gray-700 text-sm">শুধুমাত্র অনুমোদিত কর্মীদের তথ্য প্রবেশাধিকার</p>
                    </div>
                </div>
            </div>

            <!-- Your Rights -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">আপনার অধিকার</h2>
                
                <div class="bg-gray-50 rounded-xl p-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4">আপনি যা করতে পারেন:</h3>
                            <ul class="space-y-3">
                                <li class="flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">আপনার তথ্য দেখা ও সংশোধন</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">অ্যাকাউন্ট ডিলিট করা</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">ইমেইল আনসাবস্ক্রাইব</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">তথ্য কপি চাওয়া</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4">যোগাযোগ করুন:</h3>
                            <p class="text-gray-700 mb-4">
                                আপনার অধিকার ব্যবহারের জন্য আমাদের সাথে যোগাযোগ করুন।
                            </p>
                            <div class="space-y-2 text-sm">
                                <p class="text-gray-600">
                                    <strong>ইমেইল:</strong> info.saidur_bd@aol.com
                                </p>
                                <p class="text-gray-600">
                                    <strong>ফোন:</strong> +974-66489944
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cookies Policy -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">কুকিজ নীতি</h2>
                
                <div class="space-y-6">
                    <p class="text-gray-700 leading-relaxed">
                        আমরা আপনার ব্রাউজিং অভিজ্ঞতা উন্নত করতে কুকিজ ব্যবহার করি। কুকিজ হলো ছোট টেক্সট ফাইল যা 
                        আপনার ডিভাইসে সংরক্ষিত হয়।
                    </p>
                    
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-bold text-gray-900 mb-2">প্রয়োজনীয় কুকিজ</h4>
                            <p class="text-gray-600 text-sm">সাইটের মূল কার্যকারিতার জন্য প্রয়োজনীয়</p>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-bold text-gray-900 mb-2">বিশ্লেষণ কুকিজ</h4>
                            <p class="text-gray-600 text-sm">সাইট ব্যবহারের পরিসংখ্যান সংগ্রহ</p>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-bold text-gray-900 mb-2">পছন্দের কুকিজ</h4>
                            <p class="text-gray-600 text-sm">আপনার সেটিংস ও পছন্দ মনে রাখা</p>
                        </div>
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
                            <h3 class="text-xl font-bold text-gray-900 mb-6">গোপনীয়তা সংক্রান্ত প্রশ্ন</h3>
                            <p class="text-gray-700 mb-4">
                                এই নীতি সম্পর্কে কোনো প্রশ্ন বা উদ্বেগ থাকলে, অথবা আপনার তথ্যের বিষয়ে 
                                কোনো অনুরোধ থাকলে আমাদের সাথে যোগাযোগ করুন।
                            </p>
                            <p class="text-gray-700 text-sm">
                                আমরা সর্বোচ্চ ৪৮ ঘন্টার মধ্যে আপনার প্রশ্নের উত্তর দেওয়ার চেষ্টা করি।
                            </p>
                        </div>
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
                            <h3 class="font-bold text-yellow-900 mb-2">গুরুত্বপূর্ণ তথ্য</h3>
                            <p class="text-yellow-800 mb-4">
                                আমরা সময়ে সময়ে এই গোপনীয়তা নীতি আপডেট করতে পারি। কোনো পরিবর্তন হলে আমরা 
                                এই পাতায় নতুন তারিখ সহ আপডেট প্রকাশ করব।
                            </p>
                            <p class="text-yellow-800">
                                গুরুত্বপূর্ণ পরিবর্তনের ক্ষেত্রে আমরা ইমেইলের মাধ্যমে আপনাকে জানিয়ে দেব।
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>