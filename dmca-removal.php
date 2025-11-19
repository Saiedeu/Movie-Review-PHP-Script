<?php
/**
 * DMCA Removal Request Page
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config/config.php';

// SEO Meta Data
$seo_title = 'DMCA অপসারণ অনুরোধ - ' . getSiteSetting('site_name', SITE_NAME);
$seo_description = 'কপিরাইট লঙ্ঘনের কারণে কন্টেন্ট অপসারণের জন্য DMCA অনুরোধ জমা দিন। আমরা ২৪ ঘন্টার মধ্যে ব্যবস্থা নেব।';
$seo_keywords = 'DMCA removal, কপিরাইট অপসারণ, content removal, কন্টেন্ট অপসারণ';

// Breadcrumbs
$breadcrumbs = [
    ['name' => 'হোম', 'url' => '/'],
    ['name' => 'DMCA অপসারণ', 'url' => '/dmca-removal']
];

// Include header
include 'includes/header.php';
?>

<!-- Page Header -->
<section class="py-16 bg-gradient-to-r from-red-600 via-orange-600 to-pink-600">
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
        
        <h1 class="text-4xl lg:text-5xl font-bold mb-4">DMCA অপসারণ অনুরোধ</h1>
        <p class="text-xl lg:text-2xl text-white/90 max-w-3xl mx-auto">
            কপিরাইট লঙ্ঘনের কারণে কন্টেন্ট অপসারণের জন্য অনুরোধ জমা দিন
        </p>
    </div>
</section>

<!-- DMCA Content -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            
            <!-- Important Notice -->
            <div class="bg-red-50 border border-red-200 rounded-xl p-8 mb-12">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-red-900 mb-4">গুরুত্বপূর্ণ বিজ্ঞপ্তি</h2>
                        <div class="text-red-800 space-y-3">
                            <p>
                                আমরা কপিরাইট আইনের প্রতি সম্মান প্রদর্শন করি এবং Digital Millennium Copyright Act (DMCA) 
                                অনুসরণ করি। যদি আপনি মনে করেন যে আমাদের সাইটে আপনার কপিরাইট লঙ্ঘন করা হয়েছে, 
                                তাহলে নিচের ফর্মটি পূরণ করুন।
                            </p>
                            <p class="font-semibold">
                                আমরা ২৪ ঘন্টার মধ্যে আপনার অনুরোধ পর্যালোচনা করে প্রয়োজনীয় ব্যবস্থা নেব।
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DMCA Information -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">DMCA কী?</h2>
                
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-blue-50 rounded-xl p-6">
                        <h3 class="text-xl font-bold text-blue-900 mb-4">
                            <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            DMCA কী?
                        </h3>
                        <p class="text-blue-800 text-sm leading-relaxed">
                            Digital Millennium Copyright Act (DMCA) হলো একটি আমেরিকান কপিরাইট আইন যা ইন্টারনেটে 
                            কপিরাইট সুরক্ষা প্রদান করে। এটি কপিরাইট মালিকদের তাদের কন্টেন্ট অবৈধভাবে ব্যবহার হলে 
                            সেটি অপসারণের অনুরোধ করার অধিকার দেয়।
                        </p>
                    </div>
                    
                    <div class="bg-green-50 rounded-xl p-6">
                        <h3 class="text-xl font-bold text-green-900 mb-4">
                            <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            আমাদের নীতি
                        </h3>
                        <p class="text-green-800 text-sm leading-relaxed">
                            আমরা সব বৈধ DMCA অনুরোধের প্রতি দ্রুত সাড়া দেই। যদি আপনার কপিরাইট লঙ্ঘন প্রমাণিত হয়, 
                            আমরা তাৎক্ষণিক সেই কন্টেন্ট অপসারণ করি। আমাদের লক্ষ্য সব পক্ষের অধিকার রক্ষা করা।
                        </p>
                    </div>
                </div>
            </div>

            <!-- DMCA Removal Form -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">অপসারণ অনুরোধ ফর্ম</h2>
                
                <div class="bg-gray-50 rounded-2xl p-8">
                    <form id="dmcaForm" class="space-y-6">
                        <!-- Movie/Series Name -->
                        <div>
                            <label for="movieName" class="block text-lg font-semibold text-gray-900 mb-3">
                                <svg class="w-5 h-5 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>
                                </svg>
                                সিনেমা/সিরিয়ালের নাম <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="movieName" 
                                   name="movieName" 
                                   required
                                   placeholder="যেমন: Breaking Bad"
                                   class="w-full px-4 py-3 text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <p class="text-sm text-gray-600 mt-2">সেই সিনেমা বা সিরিয়ালের নাম লিখুন যার কপিরাইট লঙ্ঘন হয়েছে</p>
                        </div>

                        <!-- URL -->
                        <div>
                            <label for="contentUrl" class="block text-lg font-semibold text-gray-900 mb-3">
                                <svg class="w-5 h-5 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                কন্টেন্টের URL <span class="text-red-500">*</span>
                            </label>
                            <input type="url" 
                                   id="contentUrl" 
                                   name="contentUrl" 
                                   required
                                   placeholder="https://example.com/movie-review"
                                   class="w-full px-4 py-3 text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <p class="text-sm text-gray-600 mt-2">আমাদের সাইটের যে পেজে কপিরাইট লঙ্ঘন হয়েছে সেই পেজের লিংক দিন</p>
                        </div>

                        <!-- Reason -->
                        <div>
                            <label for="reason" class="block text-lg font-semibold text-gray-900 mb-3">
                                <svg class="w-5 h-5 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                অপসারণের কারণ <span class="text-red-500">*</span>
                            </label>
                            <select id="reason" 
                                    name="reason" 
                                    required
                                    class="w-full px-4 py-3 text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                <option value="">কারণ নির্বাচন করুন</option>
                                <option value="copyright">কপিরাইট লঙ্ঘন</option>
                                <option value="trademark">ট্রেডমার্ক লঙ্ঘন</option>
                                <option value="privacy">গোপনীয়তা লঙ্ঘন</option>
                                <option value="defamation">মানহানি</option>
                                <option value="unauthorized_content">অনুমতিহীন কন্টেন্ট</option>
                                <option value="other">অন্যান্য</option>
                            </select>
                        </div>

                        <!-- Message to Admin -->
                        <div>
                            <label for="adminMessage" class="block text-lg font-semibold text-gray-900 mb-3">
                                <svg class="w-5 h-5 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-3 3z"></path>
                                </svg>
                                প্রশাসকের জন্য বার্তা <span class="text-red-500">*</span>
                            </label>
                            <textarea id="adminMessage" 
                                      name="adminMessage" 
                                      required
                                      rows="5"
                                      placeholder="যেমন: Please remove this content as it violates my copyright. I am the original creator of this content."
                                      class="w-full px-4 py-3 text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"></textarea>
                            <p class="text-sm text-gray-600 mt-2">বিস্তারিত বর্ণনা দিন কেন এই কন্টেন্ট অপসারণ করা উচিত</p>
                        </div>

                        <!-- Legal Declaration -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                            <div class="flex items-start space-x-3">
                                <input type="checkbox" 
                                       id="legalDeclaration" 
                                       name="legalDeclaration" 
                                       required
                                       class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="legalDeclaration" class="text-sm text-yellow-800">
                                    <strong>আইনি ঘোষণা:</strong> আমি নিশ্চিত করছি যে এই অনুরোধে প্রদত্ত তথ্য সত্য এবং সঠিক। 
                                    আমি এই কন্টেন্টের কপিরাইট মালিক অথবা তার পক্ষে কাজ করার অধিকার রাখি। 
                                    আমি বুঝতে পারছি যে মিথ্যা দাবি আইনি পরিণতি বয়ে আনতে পারে।
                                </label>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="bg-blue-50 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-blue-900 mb-4">যোগাযোগের তথ্য</h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label for="senderName" class="block text-sm font-medium text-blue-900 mb-2">আপনার নাম</label>
                                    <input type="text" 
                                           id="senderName" 
                                           name="senderName" 
                                           placeholder="পূর্ণ নাম"
                                           class="w-full px-3 py-2 text-base border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="senderEmail" class="block text-sm font-medium text-blue-900 mb-2">ইমেইল ঠিকানা</label>
                                    <input type="email" 
                                           id="senderEmail" 
                                           name="senderEmail" 
                                           placeholder="your@email.com"
                                           class="w-full px-3 py-2 text-base border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            <p class="text-sm text-blue-700 mt-2">যোগাযোগের তথ্য ঐচ্ছিক, তবে দ্রুত সমাধানের জন্য প্রদান করুন</p>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center pt-6">
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-12 rounded-xl text-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <svg class="w-6 h-6 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                WhatsApp এ অনুরোধ পাঠান
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Process Information -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">প্রক্রিয়া কীভাবে কাজ করে</h2>
                
                <div class="grid md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-white font-bold text-xl">১</span>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">ফর্ম পূরণ</h3>
                        <p class="text-gray-600 text-sm">উপরের ফর্মটি সঠিক তথ্য দিয়ে পূরণ করুন</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-white font-bold text-xl">২</span>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">WhatsApp পাঠান</h3>
                        <p class="text-gray-600 text-sm">ফর্ম সাবমিট করলে WhatsApp খুলে যাবে</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-white font-bold text-xl">৩</span>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">পর্যালোচনা</h3>
                        <p class="text-gray-600 text-sm">আমরা ২৪ ঘন্টার মধ্যে পর্যালোচনা করি</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-white font-bold text-xl">৪</span>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">ব্যবস্থা নেওয়া</h3>
                        <p class="text-gray-600 text-sm">বৈধ হলে কন্টেন্ট অপসারণ করি</p>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">অন্যান্য যোগাযোগ মাধ্যম</h2>
                
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.382"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">WhatsApp</h3>
                        <p class="text-gray-600 text-sm mb-3">দ্রুততম সমাধানের জন্য</p>
                        <a href="https://wa.me/+974-66489944" target="_blank" 
                           class="text-green-600 hover:text-green-700 font-medium">+974-66489944</a>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">ইমেইল</h3>
                        <p class="text-gray-600 text-sm mb-3">বিস্তারিত অনুরোধের জন্য</p>
                        <a href="mailto:info.saidur_bd@aol.com" 
                           class="text-blue-600 hover:text-blue-700 font-medium">info.saidur_bd@aol.com</a>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">প্রতিক্রিয়ার সময়</h3>
                        <p class="text-gray-600 text-sm mb-3">সর্বোচ্চ ২৪ ঘন্টা</p>
                        <p class="text-purple-600 font-medium">দ্রুত সেবা</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Form Submission Script -->
<script>
document.getElementById('dmcaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form values
    const movieName = document.getElementById('movieName').value.trim();
    const contentUrl = document.getElementById('contentUrl').value.trim();
    const reason = document.getElementById('reason').value;
    const adminMessage = document.getElementById('adminMessage').value.trim();
    const senderName = document.getElementById('senderName').value.trim();
    const senderEmail = document.getElementById('senderEmail').value.trim();
    const legalDeclaration = document.getElementById('legalDeclaration').checked;
    
    // Validate required fields
    if (!movieName || !contentUrl || !reason || !adminMessage || !legalDeclaration) {
        showCustomAlert('দয়া করে সব প্রয়োজনীয় ক্ষেত্র পূরণ করুন এবং আইনি ঘোষণায় চেকমার্ক দিন।');
        return;
    }
    
    // Validate URL format
    try {
        new URL(contentUrl);
    } catch (_) {
        showCustomAlert('দয়া করে একটি সঠিক URL প্রদান করুন।');
        return;
    }
    
    // Format the WhatsApp message
    let whatsappMessage = `*DMCA অপসারণ অনুরোধ*\n\n`;
    whatsappMessage += `*সিনেমা/সিরিয়ালের নাম:* ${movieName}\n`;
    whatsappMessage += `*URL:* ${contentUrl}\n`;
    whatsappMessage += `*কারণ:* ${getReasonText(reason)}\n`;
    whatsappMessage += `*প্রশাসকের জন্য বার্তা:* ${adminMessage}\n\n`;
    
    if (senderName) {
        whatsappMessage += `*নাম:* ${senderName}\n`;
    }
    if (senderEmail) {
        whatsappMessage += `*ইমেইল:* ${senderEmail}\n`;
    }
    
    whatsappMessage += `\n*আইনি ঘোষণা:* আমি নিশ্চিত করছি যে প্রদত্ত তথ্য সত্য এবং আমার এই অনুরোধ করার অধিকার রয়েছে।`;
    
    // Encode the message for WhatsApp URL
    const encodedMessage = encodeURIComponent(whatsappMessage);
    
    // Create WhatsApp URL
    const whatsappUrl = `https://wa.me/+974-66489944?text=${encodedMessage}`;
    
    // Open WhatsApp
    window.open(whatsappUrl, '_blank');
    
    // Show success message
    showCustomAlert('WhatsApp খোলা হচ্ছে... দয়া করে আপনার বার্তা পাঠান।', 'success');
});

function getReasonText(reason) {
    const reasons = {
        'copyright': 'কপিরাইট লঙ্ঘন',
        'trademark': 'ট্রেডমার্ক লঙ্ঘন',
        'privacy': 'গোপনীয়তা লঙ্ঘন',
        'defamation': 'মানহানি',
        'unauthorized_content': 'অনুমতিহীন কন্টেন্ট',
        'other': 'অন্যান্য'
    };
    return reasons[reason] || reason;
}

function showCustomAlert(message, type = 'error') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${
        type === 'success' ? 'bg-green-100 border border-green-500 text-green-800' : 'bg-red-100 border border-red-500 text-red-800'
    }`;
    
    alertDiv.innerHTML = `
        <div class="flex items-start space-x-3">
            <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>'
                }
            </svg>
            <div class="flex-1">
                <p>${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>

<?php include 'includes/footer.php'; ?>