<?php
/**
 * Enhanced Submit Review Page - User Submission with TinyMCE
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config/config.php';

// Get database instance
$db = Database::getInstance();

// Get categories for dropdown
$categories = $db->fetchAll("SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC");

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'নিরাপত্তা টোকেন যাচাই করা যায়নি। দয়া করে আবার চেষ্টা করুন।';
    } else {
        // Get form data
        $title = sanitize($_POST['title'] ?? '');
        $reviewer_name = sanitize($_POST['reviewer_name'] ?? '');
        $reviewer_email = sanitize($_POST['reviewer_email'] ?? '');
        $year = sanitize($_POST['year'] ?? '');
        $type = sanitize($_POST['type'] ?? 'movie');
        $language = sanitize($_POST['language'] ?? '');
        $director = sanitize($_POST['director'] ?? '');
        $cast = sanitize($_POST['cast'] ?? '');
        $rating = floatval($_POST['rating'] ?? 0);
        $content = $_POST['content'] ?? '';
        $spoiler_content = $_POST['spoiler_content'] ?? '';  // New spoiler field
        $category_ids = $_POST['categories'] ?? [];
        
        // Validate required fields
        if (empty($title) || empty($reviewer_name) || empty($reviewer_email) || empty($content)) {
            $error = 'সব প্রয়োজনীয় ফিল্ড পূরণ করুন।';
        } elseif (!filter_var($reviewer_email, FILTER_VALIDATE_EMAIL)) {
            $error = 'সঠিক ইমেইল ঠিকানা দিন।';
        } elseif ($rating < 1 || $rating > 5) {
            $error = 'রেটিং ১ থেকে ৫ এর মধ্যে হতে হবে।';
        } else {
            // Generate slug
            $slug = generateSlug($title);
            
            // Check if slug exists
            $existing = $db->fetchOne("SELECT id FROM reviews WHERE slug = ?", [$slug]);
            if ($existing) {
                $slug .= '-' . time();
            }
            
            // Handle poster image upload
            $poster_image = '';
            if (isset($_FILES['poster_image']) && $_FILES['poster_image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = uploadFile($_FILES['poster_image'], 'reviews');
                if ($upload_result['success']) {
                    $poster_image = $upload_result['filename'];
                } else {
                    $error = $upload_result['message'];
                }
            }
            
            if (empty($error)) {
                // Insert review with spoiler content
                $review_id = $db->query("
                    INSERT INTO reviews (
                        title, slug, reviewer_name, reviewer_email, year, type, language, 
                        director, cast, rating, content, spoiler_content, poster_image, 
                        status, created_by, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'user', NOW())
                ", [
                    $title, $slug, $reviewer_name, $reviewer_email, $year, $type, $language,
                    $director, $cast, $rating, $content, $spoiler_content, $poster_image
                ]);
                
                if ($review_id) {
                    $review_id = $db->lastInsertId();
                    
                    // Insert categories
                    if (!empty($category_ids)) {
                        foreach ($category_ids as $category_id) {
                            $db->query("INSERT INTO review_categories (review_id, category_id) VALUES (?, ?)", [$review_id, $category_id]);
                        }
                    }
                    
                    $success = 'আপনার রিভিউ সফলভাবে জমা দেওয়া হয়েছে! অ্যাডমিন অনুমোদনের পর এটি প্রকাশিত হবে।';
                    
                    // Clear form data
                    $_POST = [];
                } else {
                    $error = 'রিভিউ জমা দিতে সমস্যা হয়েছে। দয়া করে আবার চেষ্টা করুন।';
                }
            }
        }
    }
}

// SEO Meta Data
$seo_title = 'আপনার রিভিউ জমা দিন - ' . getSiteSetting('site_name', SITE_NAME);
$seo_description = 'আপনার দেখা সিনেমা বা সিরিয়ালের রিভিউ লিখুন এবং অন্যদের সাথে শেয়ার করুন। আমাদের কমিউনিটির অংশ হয়ে উঠুন।';
$seo_keywords = 'রিভিউ জমা দিন, সিনেমা রিভিউ লিখুন, সিরিয়াল রিভিউ, user review';

// Include header
include 'includes/header.php';
?>

<!-- TinyMCE Script -->
<script src="https://cdn.tiny.cloud/1/hhiyirqkh3fnrmgjs7nq6tpk6nqb62m3vww7smgrz7kjfv6v/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<style>
/* Mobile Admin Panel Fix */
@media (max-width: 768px) {
    body {
        overflow-x: hidden;
    }
    .admin-content {
        height: auto !important;
        min-height: 100vh;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
}

/* Form styles */
.form-group {
    margin-bottom: 1.5rem;
}

.spoiler-input {
    border: 2px dashed #fbbf24;
    background-color: #fef3c7;
}

.spoiler-input:focus {
    border-color: #f59e0b;
    background-color: #fde68a;
}

/* TinyMCE custom styles */
.tox-tinymce {
    border-radius: 0.5rem !important;
    border: 1px solid #d1d5db !important;
}

.tox-editor-header {
    border-top-left-radius: 0.5rem !important;
    border-top-right-radius: 0.5rem !important;
}

.tox-edit-area {
    border-bottom-left-radius: 0.5rem !important;
    border-bottom-right-radius: 0.5rem !important;
}

.tox-toolbar__group {
    border: none !important;
}

/* Spoiler editor styling */
.spoiler-editor .tox-tinymce {
    border: 2px dashed #fbbf24 !important;
    background-color: #fef3c7;
}

.spoiler-editor .tox-editor-header {
    background-color: #fde68a !important;
}

.spoiler-editor .tox-toolbar {
    background-color: #fde68a !important;
}

/* Mobile responsive for TinyMCE */
@media (max-width: 768px) {
    .tox-toolbar__group {
        flex-wrap: wrap;
    }
    
    .tox-toolbar {
        padding: 8px 4px !important;
    }
    
    .tox-tbtn {
        margin: 1px !important;
    }
}
</style>

<!-- Page Header -->
<section class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 py-16">
    <div class="container mx-auto px-4 text-center">
        <div class="max-w-3xl mx-auto text-white">
            <h1 class="text-4xl lg:text-5xl font-bold mb-6">আপনার রিভিউ জমা দিন</h1>
            <p class="text-xl lg:text-2xl text-white/90 leading-relaxed">
                দেখেছেন কোনো দুর্দান্ত সিনেমা বা সিরিয়াল? আপনার অভিজ্ঞতা অন্যদের সাথে শেয়ার করুন এবং 
                আমাদের কমিউনিটির অংশ হয়ে উঠুন।
            </p>
        </div>
    </div>
</section>

<!-- Submit Form -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Success Message -->
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-8">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-bold">সফল!</h4>
                            <p><?php echo $success; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-8">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-bold">ত্রুটি!</h4>
                            <p><?php echo $error; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Guidelines -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-bold text-blue-900 mb-4">
                    <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    রিভিউ লেখার গাইডলাইন
                </h3>
                <ul class="text-blue-800 space-y-2">
                    <li class="flex items-start">
                        <span class="text-blue-600 mr-2">•</span>
                        <span>আপনার রিভিউ অন্তত ২০০ শব্দের হতে হবে</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-600 mr-2">•</span>
                        <span>স্পয়লার থাকলে স্পয়লার বক্সে আলাদা করে লিখুন</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-600 mr-2">•</span>
                        <span>টেক্সট ফরম্যাটিং, লিস্ট, বোল্ড/ইতালিক ব্যবহার করুন</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-600 mr-2">•</span>
                        <span>অশ্লীল বা আপত্তিজনক ভাষা ব্যবহার করবেন না</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-600 mr-2">•</span>
                        <span>আপনার রিভিউ অনুমোদনের পর প্রকাশিত হবে</span>
                    </li>
                </ul>
            </div>
            
            <!-- Form -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                <div class="p-8">
                    <form method="POST" enctype="multipart/form-data" class="space-y-8">
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                মূল তথ্য
                            </h3>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Title -->
                                <div class="md:col-span-2 form-group">
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                        সিনেমা/সিরিয়ালের নাম <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
                                           placeholder="উদাহরণ: অভিমান, প্যারাসাইট, স্কুইড গেম"
                                           required>
                                </div>
                                
                                <!-- Type -->
                                <div class="form-group">
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                        ধরণ <span class="text-red-500">*</span>
                                    </label>
                                    <select id="type" 
                                            name="type" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
                                            required>
                                        <option value="movie" <?php echo ($_POST['type'] ?? '') === 'movie' ? 'selected' : ''; ?>>সিনেমা</option>
                                        <option value="series" <?php echo ($_POST['type'] ?? '') === 'series' ? 'selected' : ''; ?>>সিরিয়াল/ড্রামা</option>
                                    </select>
                                </div>
                                
                                <!-- Year -->
                                <div class="form-group">
                                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                                        বছর
                                    </label>
                                    <input type="number" 
                                           id="year" 
                                           name="year" 
                                           value="<?php echo htmlspecialchars($_POST['year'] ?? date('Y')); ?>"
                                           min="1900" 
                                           max="<?php echo date('Y') + 1; ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
                                           placeholder="<?php echo date('Y'); ?>">
                                </div>
                                
                                <!-- Language -->
                                <div class="form-group">
                                    <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                                        ভাষা
                                    </label>
                                    <select id="language" 
                                            name="language" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base">
                                        <option value="">নির্বাচন করুন</option>
                                        <option value="বাংলা" <?php echo ($_POST['language'] ?? '') === 'বাংলা' ? 'selected' : ''; ?>>বাংলা</option>
                                        <option value="হিন্দি" <?php echo ($_POST['language'] ?? '') === 'হিন্দি' ? 'selected' : ''; ?>>হিন্দি</option>
                                        <option value="ইংরেজি" <?php echo ($_POST['language'] ?? '') === 'ইংরেজি' ? 'selected' : ''; ?>>ইংরেজি</option>
                                        <option value="কোরিয়ান" <?php echo ($_POST['language'] ?? '') === 'কোরিয়ান' ? 'selected' : ''; ?>>কোরিয়ান</option>
                                        <option value="জাপানি" <?php echo ($_POST['language'] ?? '') === 'জাপানি' ? 'selected' : ''; ?>>জাপানি</option>
                                        <option value="চাইনিজ" <?php echo ($_POST['language'] ?? '') === 'চাইনিজ' ? 'selected' : ''; ?>>চাইনিজ</option>
                                        <option value="অন্যান্য" <?php echo ($_POST['language'] ?? '') === 'অন্যান্য' ? 'selected' : ''; ?>>অন্যান্য</option>
                                    </select>
                                </div>
                                
                                <!-- Rating -->
                                <div class="form-group">
                                    <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">
                                        আপনার রেটিং <span class="text-red-500">*</span>
                                    </label>
                                    <select id="rating" 
                                            name="rating" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
                                            required>
                                        <option value="">রেটিং দিন</option>
                                        <option value="5" <?php echo ($_POST['rating'] ?? '') == '5' ? 'selected' : ''; ?>>৫ - চমৎকার</option>
                                        <option value="4" <?php echo ($_POST['rating'] ?? '') == '4' ? 'selected' : ''; ?>>৪ - ভালো</option>
                                        <option value="3" <?php echo ($_POST['rating'] ?? '') == '3' ? 'selected' : ''; ?>>৩ - গড়</option>
                                        <option value="2" <?php echo ($_POST['rating'] ?? '') == '2' ? 'selected' : ''; ?>>২ - খারাপ</option>
                                        <option value="1" <?php echo ($_POST['rating'] ?? '') == '1' ? 'selected' : ''; ?>>১ - অত্যন্ত খারাপ</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cast & Crew -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                কাস্ট ও ক্রু
                            </h3>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Director -->
                                <div class="form-group">
                                    <label for="director" class="block text-sm font-medium text-gray-700 mb-2">
                                        পরিচালক
                                    </label>
                                    <input type="text" 
                                           id="director" 
                                           name="director" 
                                           value="<?php echo htmlspecialchars($_POST['director'] ?? ''); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
                                           placeholder="পরিচালকের নাম">
                                </div>
                                
                                <!-- Cast -->
                                <div class="form-group">
                                    <label for="cast" class="block text-sm font-medium text-gray-700 mb-2">
                                        প্রধান অভিনেতা/অভিনেত্রী
                                    </label>
                                    <input type="text" 
                                           id="cast" 
                                           name="cast" 
                                           value="<?php echo htmlspecialchars($_POST['cast'] ?? ''); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
                                           placeholder="কমা দিয়ে আলাদা করুন">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Categories -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                ক্যাটেগরি
                            </h3>
                            
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <?php foreach ($categories as $category): ?>
                                    <label class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                                        <input type="checkbox" 
                                               name="categories[]" 
                                               value="<?php echo $category['id']; ?>"
                                               <?php echo in_array($category['id'], $_POST['categories'] ?? []) ? 'checked' : ''; ?>
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm font-medium text-gray-700">
                                            <?php echo $category['name_bn'] ?: $category['name']; ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Poster Image -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                পোস্টার ছবি
                            </h3>
                            
                            <div class="form-group">
                                <label for="poster_image" class="block text-sm font-medium text-gray-700 mb-2">
                                    পোস্টার আপলোড করুন
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="poster_image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>ফাইল আপলোড করুন</span>
                                                <input id="poster_image" name="poster_image" type="file" accept="image/*" class="sr-only">
                                            </label>
                                            <p class="pl-1">অথবা ড্র্যাগ করে ছাড়ুন</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, WEBP সর্বোচ্চ ৫MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Review Content -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                আপনার রিভিউ
                            </h3>
                            
                            <div class="space-y-6">
                                <!-- Main Review Content -->
                                <div class="form-group">
                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                        রিভিউ লিখুন <span class="text-red-500">*</span>
                                    </label>
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-3">
                                        <p class="text-sm text-blue-800">
                                            <strong>টিপস:</strong> টেক্সট এডিটরে বোল্ড, ইতালিক, লিস্ট, হেডিং ব্যবহার করে আপনার রিভিউকে আকর্ষণীয় করুন। 
                                            কাহিনী, অভিনয়, পরিচালনা, ভিজুয়াল এফেক্ট সম্পর্কে বিস্তারিত লিখুন।
                                        </p>
                                    </div>
                                    <textarea id="content" 
                                              name="content" 
                                              class="w-full"
                                              required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                                    <div class="mt-2 text-sm text-gray-500">
                                        ন্যূনতম ২০০ শব্দ প্রয়োজন। <span id="word-count">0</span> শব্দ
                                    </div>
                                </div>
                                
                                <!-- Spoiler Content -->
                                <div class="form-group spoiler-editor">
                                    <label for="spoiler_content" class="block text-sm font-medium text-gray-700 mb-2">
                                        <span class="text-yellow-600">⚠️</span> স্পয়লার কন্টেন্ট (ঐচ্ছিক)
                                    </label>
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-3">
                                        <p class="text-sm text-yellow-800">
                                            <strong>স্পয়লার এলার্ট:</strong> যদি আপনার রিভিউতে গল্পের গুরুত্বপূর্ণ তথ্য বা সারপ্রাইজ থাকে যা অন্যদের দেখার আগ্রহ নষ্ট করতে পারে, 
                                            তাহলে সেগুলো এই এডিটরে আলাদা করে লিখুন। এটি একটি বিশেষ "স্পয়লার দেখান" বাটনের ভিতরে লুকানো থাকবে।
                                        </p>
                                    </div>
                                    <textarea id="spoiler_content" 
                                              name="spoiler_content" 
                                              class="w-full"><?php echo htmlspecialchars($_POST['spoiler_content'] ?? ''); ?></textarea>
                                    <div class="mt-2 text-sm text-yellow-600">
                                        এই অংশটি পাঠকরা শুধুমাত্র "স্পয়লার দেখান" বাটনে ক্লিক করার পর দেখতে পাবেন।
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reviewer Information -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                আপনার তথ্য
                            </h3>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div class="form-group">
                                    <label for="reviewer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        আপনার নাম <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="reviewer_name" 
                                           name="reviewer_name" 
                                           value="<?php echo htmlspecialchars($_POST['reviewer_name'] ?? ''); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
                                           placeholder="আপনার পূর্ণ নাম"
                                           required>
                                </div>
                                
                                <!-- Email -->
                                <div class="form-group">
                                    <label for="reviewer_email" class="block text-sm font-medium text-gray-700 mb-2">
                                        ইমেইল ঠিকানা <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                           id="reviewer_email" 
                                           name="reviewer_email" 
                                           value="<?php echo htmlspecialchars($_POST['reviewer_email'] ?? ''); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
                                           placeholder="your@email.com"
                                           required>
                                    <p class="mt-1 text-sm text-gray-500">আপনার ইমেইল প্রকাশ করা হবে না</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit -->
                        <div class="flex justify-center pt-6">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-105">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                রিভিউ জমা দিন
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Initialize TinyMCE
document.addEventListener('DOMContentLoaded', function() {
    // Common TinyMCE configuration
    const commonConfig = {
        height: 400,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
        'bold italic underline strikethrough | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | help',
        content_style: `
            body { 
                font-family: 'Hind Siliguri', sans-serif; 
                font-size: 16px; 
                line-height: 1.6; 
                color: #374151;
                padding: 10px;
            }
            h1, h2, h3, h4, h5, h6 { 
                color: #1f2937; 
                margin-top: 1.5em; 
                margin-bottom: 0.5em;
            }
            p { margin-bottom: 1em; }
            ul, ol { margin: 1em 0; padding-left: 2em; }
            li { margin-bottom: 0.5em; }
            blockquote { 
                border-left: 4px solid #3b82f6; 
                margin: 1.5em 0; 
                padding: 0.5em 0 0.5em 1em; 
                background: #f8fafc; 
            }
        `,
        setup: function(editor) {
            editor.on('change', function() {
                // Update word count for main content
                if (editor.id === 'content') {
                    updateWordCount();
                }
            });
        },
        mobile: {
            theme: 'silver',
            plugins: ['autosave', 'lists', 'autolink'],
            toolbar: 'undo bold italic | bullist numlist'
        }
    };

    // Initialize main content editor
    tinymce.init({
        ...commonConfig,
        selector: '#content',
        placeholder: 'আপনার রিভিউ বিস্তারিত লিখুন। কাহিনী, অভিনয়, পরিচালনা, সিনেমাটোগ্রাফি সম্পর্কে আপনার মতামত দিন। স্পয়লার এড়িয়ে চলুন।'
    });

    // Initialize spoiler content editor
    tinymce.init({
        ...commonConfig,
        selector: '#spoiler_content',
        height: 300,
        placeholder: 'এখানে স্পয়লার সংক্রান্ত আলোচনা লিখুন। যেমন: গল্পের মোড়, চরিত্রের ভাগ্য, সারপ্রাইজ টুইস্ট ইত্যাদি...',
        content_style: commonConfig.content_style + `
            body { 
                background-color: #fef3c7; 
                border: 2px dashed #f59e0b; 
                border-radius: 8px;
            }
        `
    });
});

// Word count function
function updateWordCount() {
    const content = tinymce.get('content').getContent({format: 'text'});
    const words = content.trim().split(/\s+/).filter(word => word.length > 0);
    document.getElementById('word-count').textContent = words.length;
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    // Get content from TinyMCE
    const content = tinymce.get('content').getContent({format: 'text'});
    const words = content.trim().split(/\s+/).filter(word => word.length > 0);
    
    if (words.length < 200) {
        e.preventDefault();
        showCustomAlert('রিভিউ অন্তত ২০০ শব্দের হতে হবে। বর্তমানে ' + words.length + ' শব্দ আছে।');
        return false;
    }
    
    // Update textarea values before submit
    document.getElementById('content').value = tinymce.get('content').getContent();
    document.getElementById('spoiler_content').value = tinymce.get('spoiler_content').getContent();
    
    return true;
});

// Custom alert function
function showCustomAlert(message) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-lg max-w-sm w-full mx-4 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">বিজ্ঞপ্তি</h3>
            <p class="text-gray-700 mb-4">${message}</p>
            <div class="flex justify-end">
                <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">ঠিক আছে</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

// Prevent form submission on Enter in TinyMCE
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.target.closest('.tox-edit-area')) {
        e.stopPropagation();
    }
});
</script>

<?php include 'includes/footer.php'; ?>