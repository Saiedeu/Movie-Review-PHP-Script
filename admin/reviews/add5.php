<?php
/**
 * Enhanced Add New Review - Admin with Spoiler Support, Custom URL and Download Links
 * Fixed Version with Proper TinyMCE for Spoiler Content
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../../config/config.php';

// Require authentication
requireAuth();

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
        $error = 'নিরাপত্তা টোকেন যাচাই করা যায়নি।';
    } else {
        // Get form data
        $title = sanitize($_POST['title'] ?? '');
        $custom_url = sanitize($_POST['custom_url'] ?? '');
        $reviewer_name = sanitize($_POST['reviewer_name'] ?? '');
        $year = sanitize($_POST['year'] ?? '');
        $type = sanitize($_POST['type'] ?? 'movie');
        $language = sanitize($_POST['language'] ?? '');
        $director = sanitize($_POST['director'] ?? '');
        $cast = sanitize($_POST['cast'] ?? '');
        $rating = floatval($_POST['rating'] ?? 0);
        $content = $_POST['content'] ?? '';
        $spoiler_content = $_POST['spoiler_content'] ?? '';
        $excerpt = sanitize($_POST['excerpt'] ?? '');
        $trailer_url = sanitize($_POST['trailer_url'] ?? '');
        $download_movie_url = sanitize($_POST['download_movie_url'] ?? '');
        $download_subtitle_url = sanitize($_POST['download_subtitle_url'] ?? '');
        $featured = isset($_POST['featured']) ? 1 : 0;
        $status = sanitize($_POST['status'] ?? 'published');
        $category_ids = $_POST['categories'] ?? [];
        
        // SEO fields
        $meta_title = sanitize($_POST['meta_title'] ?? '');
        $meta_description = sanitize($_POST['meta_description'] ?? '');
        $meta_keywords = sanitize($_POST['meta_keywords'] ?? '');
        
        // Validate required fields
        if (empty($title) || empty($reviewer_name) || empty($content)) {
            $error = 'শিরোনাম, লেখক নাম এবং রিভিউ কন্টেন্ট প্রয়োজন।';
        } elseif ($rating < 0 || $rating > 5) {
            $error = 'রেটিং ০ থেকে ৫ এর মধ্যে হতে হবে।';
        } else {
            // Generate or use custom slug
            if (!empty($custom_url)) {
                // Validate custom URL format
                if (!preg_match('/^[a-z0-9-]+$/', $custom_url)) {
                    $error = 'কাস্টম URL শুধুমাত্র ছোট হাতের অক্ষর, সংখ্যা এবং হাইফেন (-) থাকতে পারে।';
                } elseif (strlen($custom_url) < 3) {
                    $error = 'কাস্টম URL কমপক্ষে ৩ অক্ষরের হতে হবে।';
                } else {
                    $slug = $custom_url;
                }
            } else {
                // Generate slug from title
                $slug = generateSlug($title);
            }
            
            if (empty($error)) {
                // Check if slug exists
                $existing = $db->fetchOne("SELECT id FROM reviews WHERE slug = ?", [$slug]);
                if ($existing) {
                    if (!empty($custom_url)) {
                        $error = 'এই URL ইতিমধ্যে ব্যবহৃত হয়েছে। অন্য URL ব্যবহার করুন।';
                    } else {
                        $slug .= '-' . time();
                    }
                }
            }
            
            // Handle poster image upload
            $poster_image = '';
            if (empty($error) && isset($_FILES['poster_image']) && $_FILES['poster_image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = uploadFile($_FILES['poster_image'], 'reviews');
                if ($upload_result['success']) {
                    $poster_image = $upload_result['filename'];
                } else {
                    $error = $upload_result['message'];
                }
            }
            
            if (empty($error)) {
                // Generate excerpt if empty
                if (empty($excerpt)) {
                    $excerpt = truncateText(strip_tags($content), 200);
                }
                
                // Start transaction
                $db->beginTransaction();
                
                try {
                    // Check if download columns exist in database
                    $columns_exist = false;
                    try {
                        $test_query = $db->fetchOne("SHOW COLUMNS FROM reviews LIKE 'download_movie_url'");
                        $columns_exist = ($test_query !== false);
                    } catch (Exception $e) {
                        $columns_exist = false;
                    }
                    
                    if ($columns_exist) {
                        // Insert review with download URLs
                        $review_id = $db->query("
                            INSERT INTO reviews (
                                title, slug, reviewer_name, reviewer_email, year, type, language, director, cast, 
                                rating, content, spoiler_content, excerpt, poster_image, trailer_url, 
                                download_movie_url, download_subtitle_url, featured, 
                                status, created_by, meta_title, meta_description, meta_keywords, 
                                created_at
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'admin', ?, ?, ?, NOW())
                        ", [
                            $title, $slug, $reviewer_name, 'admin@msr.com', $year, $type, $language, $director, $cast,
                            $rating, $content, $spoiler_content, $excerpt, $poster_image, $trailer_url,
                            $download_movie_url, $download_subtitle_url, $featured,
                            $status, $meta_title, $meta_description, $meta_keywords
                        ]);
                    } else {
                        // Insert review without download URLs (for compatibility)
                        $review_id = $db->query("
                            INSERT INTO reviews (
                                title, slug, reviewer_name, reviewer_email, year, type, language, director, cast, 
                                rating, content, spoiler_content, excerpt, poster_image, trailer_url, featured, 
                                status, created_by, meta_title, meta_description, meta_keywords, 
                                created_at
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'admin', ?, ?, ?, NOW())
                        ", [
                            $title, $slug, $reviewer_name, 'admin@msr.com', $year, $type, $language, $director, $cast,
                            $rating, $content, $spoiler_content, $excerpt, $poster_image, $trailer_url, $featured,
                            $status, $meta_title, $meta_description, $meta_keywords
                        ]);
                    }
                    
                    if ($review_id) {
                        $review_id_value = $db->lastInsertId();
                        
                        // Insert categories
                        if (!empty($category_ids)) {
                            foreach ($category_ids as $category_id) {
                                $db->query("INSERT INTO review_categories (review_id, category_id) VALUES (?, ?)", [$review_id_value, $category_id]);
                            }
                        }
                        
                        // Commit transaction
                        $db->commit();
                        
                        $_SESSION['success_message'] = 'রিভিউ সফলভাবে যোগ করা হয়েছে।';
                        header('Location: index.php');
                        exit;
                    } else {
                        throw new Exception('রিভিউ যোগ করতে সমস্যা হয়েছে।');
                    }
                } catch (Exception $e) {
                    // Rollback transaction
                    $db->rollback();
                    
                    // Delete uploaded file if exists
                    if ($poster_image) {
                        deleteFile('reviews', $poster_image);
                    }
                    
                    $error = 'রিভিউ যোগ করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।';
                }
            }
        }
    }
}

// Include admin header
include '../includes/header.php';
?>

<style>
/* Mobile Admin Panel Fix */
@media (max-width: 768px) {
    .admin-content {
        height: auto !important;
        min-height: 100vh;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding-top: 70px;
    }
    
    .mobile-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: white;
        border-bottom: 1px solid #e5e7eb;
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 16px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .sidebar-content {
        position: sticky;
        top: 20px;
    }
    
    /* Fix TinyMCE mobile issues */
    .tox-tinymce {
        min-height: 200px !important;
    }
    
    .tox-edit-area {
        min-height: 150px !important;
    }
    
    /* Fix mobile keyboard zoom */
    input, textarea, select {
        font-size: 16px !important;
        transform-origin: left top;
        transform: scale(1);
    }
}

.spoiler-input-container {
    background: linear-gradient(135deg, #fef3c7, #fed7aa);
    border: 2px dashed #f59e0b;
    border-radius: 12px;
    padding: 20px;
    margin: 16px 0;
}

.spoiler-warning {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border: 1px solid #f87171;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 16px;
}

.form-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    padding: 24px;
    margin-bottom: 24px;
}

.form-section h3 {
    color: #1f2937;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f3f4f6;
}

.url-preview {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
    color: #64748b;
    margin-top: 8px;
}

.download-section {
    background: linear-gradient(135deg, #e0f2fe, #f3e5f5);
    border: 2px dashed #8e24aa;
    border-radius: 12px;
    padding: 20px;
}

.download-section h3 {
    color: #8e24aa;
    border-bottom-color: #e1bee7;
}

/* Image Upload Progress */
.image-upload-progress {
    background: #f0f9ff;
    border: 1px solid #0ea5e9;
    border-radius: 6px;
    padding: 12px;
    margin: 8px 0;
    display: none;
}

.image-upload-progress.active {
    display: block;
}

.upload-progress-bar {
    width: 100%;
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
}

.upload-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #0ea5e9, #06b6d4);
    transition: width 0.3s ease;
    width: 0%;
}

/* TinyMCE Custom Styling */
.tox .tox-toolbar-overlord {
    background: #f8fafc !important;
}

.tox .tox-editor-header {
    border-bottom: 1px solid #e2e8f0 !important;
}

.spoiler-editor .tox .tox-toolbar-overlord {
    background: linear-gradient(135deg, #fef3c7, #fed7aa) !important;
}

.spoiler-editor .tox .tox-editor-header {
    border-bottom: 1px solid #f59e0b !important;
}
</style>

<!-- Mobile Header -->
<div class="mobile-header md:hidden">
    <button id="mobile-menu-btn" class="p-2 rounded-lg hover:bg-gray-100">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
    <h1 class="text-lg font-bold">নতুন রিভিউ যোগ করুন</h1>
    <div class="w-8"></div>
</div>

<div class="flex-1 bg-gray-100 admin-content">
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-4 md:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="index.php" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900">নতুন রিভিউ যোগ করুন</h1>
                    <p class="text-gray-600 hidden md:block">একটি নতুন সিনেমা বা সিরিয়াল রিভিউ তৈরি করুন</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="p-4 md:p-6">
        <!-- Error Message -->
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <!-- Image Upload Progress -->
        <div id="image-upload-progress" class="image-upload-progress">
            <div class="flex items-center space-x-3 mb-2">
                <svg class="w-5 h-5 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-blue-700">ছবি আপলোড হচ্ছে...</span>
            </div>
            <div class="upload-progress-bar">
                <div id="upload-progress-fill" class="upload-progress-fill"></div>
            </div>
        </div>
        
        <!-- Form -->
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="grid lg:grid-cols-3 gap-6 form-grid">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h3>মূল তথ্য</h3>
                        
                        <div class="space-y-4">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    শিরোনাম <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="title" 
                                       name="title" 
                                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                       placeholder="সিনেমা বা সিরিয়ালের নাম"
                                       required>
                            </div>
                            
                            <!-- Custom URL -->
                            <div>
                                <label for="custom_url" class="block text-sm font-medium text-gray-700 mb-2">
                                    কাস্টম URL (ঐচ্ছিক)
                                </label>
                                <div class="flex items-center">
                                    <span class="bg-gray-100 border border-r-0 border-gray-300 px-3 py-3 text-sm text-gray-600 rounded-l-lg">
                                        <?php echo SITE_URL; ?>/review.php?slug=
                                    </span>
                                    <input type="text" 
                                           id="custom_url" 
                                           name="custom_url" 
                                           value="<?php echo htmlspecialchars($_POST['custom_url'] ?? ''); ?>"
                                           class="flex-1 px-4 py-3 border border-gray-300 rounded-r-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                           placeholder="my-review-url"
                                           pattern="[a-z0-9-]+"
                                           title="শুধুমাত্র ছোট হাতের অক্ষর, সংখ্যা এবং হাইফেন ব্যবহার করুন">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    খালি রাখলে শিরোনাম থেকে স্বয়ংক্রিয়ভাবে তৈরি হবে। শুধুমাত্র ছোট হাতের অক্ষর, সংখ্যা এবং হাইফেন (-) ব্যবহার করুন।
                                </p>
                                <div id="url-preview" class="url-preview hidden">
                                    <strong>প্রাকদর্শন:</strong> <span id="preview-url"></span>
                                </div>
                            </div>
                            
                            <!-- Reviewer Name -->
                            <div>
                                <label for="reviewer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    লেখক নাম <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="reviewer_name" 
                                       name="reviewer_name" 
                                       value="<?php echo htmlspecialchars($_POST['reviewer_name'] ?? 'MSR Team'); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                       required>
                            </div>
                            
                            <div class="grid md:grid-cols-3 gap-4">
                                <!-- Year -->
                                <div>
                                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">বছর</label>
                                    <input type="number" 
                                           id="year" 
                                           name="year" 
                                           value="<?php echo htmlspecialchars($_POST['year'] ?? date('Y')); ?>"
                                           min="1900" 
                                           max="<?php echo date('Y') + 1; ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                                </div>
                                
                                <!-- Type -->
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">ধরণ</label>
                                    <select id="type" 
                                            name="type" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                                        <option value="movie" <?php echo ($_POST['type'] ?? '') === 'movie' ? 'selected' : ''; ?>>সিনেমা</option>
                                        <option value="series" <?php echo ($_POST['type'] ?? '') === 'series' ? 'selected' : ''; ?>>সিরিয়াল</option>
                                    </select>
                                </div>
                                
                                <!-- Rating -->
                                <div>
                                    <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">রেটিং</label>
                                    <input type="number" 
                                           id="rating" 
                                           name="rating" 
                                           value="<?php echo htmlspecialchars($_POST['rating'] ?? ''); ?>"
                                           min="0" 
                                           max="5" 
                                           step="0.1"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                                </div>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-4">
                                <!-- Language -->
                                <div>
                                    <label for="language" class="block text-sm font-medium text-gray-700 mb-2">ভাষা</label>
                                    <input type="text" 
                                           id="language" 
                                           name="language" 
                                           value="<?php echo htmlspecialchars($_POST['language'] ?? ''); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                           placeholder="বাংলা, ইংরেজি, কোরিয়ান">
                                </div>
                                
                                <!-- Director -->
                                <div>
                                    <label for="director" class="block text-sm font-medium text-gray-700 mb-2">পরিচালক</label>
                                    <input type="text" 
                                           id="director" 
                                           name="director" 
                                           value="<?php echo htmlspecialchars($_POST['director'] ?? ''); ?>"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                                </div>
                            </div>
                            
                            <!-- Cast -->
                            <div>
                                <label for="cast" class="block text-sm font-medium text-gray-700 mb-2">অভিনেতা/অভিনেত্রী</label>
                                <textarea id="cast" 
                                          name="cast" 
                                          rows="2"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                          placeholder="কমা দিয়ে আলাদা করুন"><?php echo htmlspecialchars($_POST['cast'] ?? ''); ?></textarea>
                            </div>
                            
                            <!-- Trailer URL -->
                            <div>
                                <label for="trailer_url" class="block text-sm font-medium text-gray-700 mb-2">ট্রেইলার URL</label>
                                <input type="url" 
                                       id="trailer_url" 
                                       name="trailer_url" 
                                       value="<?php echo htmlspecialchars($_POST['trailer_url'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                       placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="form-section">
                        <h3>রিভিউ কন্টেন্ট</h3>
                        
                        <div class="space-y-6">
                            <!-- Excerpt -->
                            <div>
                                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">সংক্ষিপ্ত বিবরণ</label>
                                <textarea id="excerpt" 
                                          name="excerpt" 
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                          placeholder="রিভিউর সংক্ষিপ্ত বিবরণ..."><?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?></textarea>
                            </div>
                            
                            <!-- Main Content -->
                            <div>
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                    রিভিউ কন্টেন্ট <span class="text-red-500">*</span>
                                </label>
                                <textarea id="content" 
                                          name="content" 
                                          class="main-content-editor w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                          rows="10"
                                          required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                            </div>
                            
                            <!-- Spoiler Content -->
                            <div class="spoiler-input-container">
                                <div class="spoiler-warning">
                                    <div class="flex items-center mb-2">
                                        <span class="text-2xl mr-2">⚠️</span>
                                        <h4 class="text-lg font-bold text-red-800">স্পয়লার সতর্কতা</h4>
                                    </div>
                                    <p class="text-sm text-red-700">
                                        এই অংশে গল্পের গুরুত্বপূর্ণ তথ্য, সারপ্রাইজ বা এন্ডিং সম্পর্কে লিখুন। 
                                        এটি পাঠকদের কাছে "স্পয়লার দেখান" বোতামের পিছনে লুকানো থাকবে।
                                    </p>
                                </div>
                                
                                <div>
                                    <label for="spoiler_content" class="block text-sm font-bold text-orange-800 mb-2">
                                        স্পয়লার কন্টেন্ট (ঐচ্ছিক)
                                    </label>
                                    <textarea id="spoiler_content" 
                                              name="spoiler_content" 
                                              class="spoiler-content-editor w-full px-4 py-3 border border-orange-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-base"
                                              rows="8"><?php echo htmlspecialchars($_POST['spoiler_content'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mt-3 text-xs text-orange-700 bg-orange-50 p-3 rounded-lg">
                                    💡 <strong>টিপস:</strong> স্পয়লার কন্টেন্ট ব্যবহারকারীর ইচ্ছার উপর ভিত্তি করে দেখানো হবে। 
                                    এখানে প্লট টুইস্ট, চরিত্রের পরিণতি, এন্ডিং বা অন্যান্য গুরুত্বপূর্ণ তথ্য যোগ করুন।
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SEO Settings -->
                    <div class="form-section">
                        <h3>SEO সেটিংস</h3>
                        
                        <div class="space-y-4">
                            <!-- Meta Title -->
                            <div>
                                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                                <input type="text" 
                                       id="meta_title" 
                                       name="meta_title" 
                                       value="<?php echo htmlspecialchars($_POST['meta_title'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                       placeholder="খালি রাখলে শিরোনাম ব্যবহার হবে">
                            </div>
                            
                            <!-- Meta Description -->
                            <div>
                                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                                <textarea id="meta_description" 
                                          name="meta_description" 
                                          rows="2"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                          placeholder="খালি রাখলে সংক্ষিপ্ত বিবরণ ব্যবহার হবে"><?php echo htmlspecialchars($_POST['meta_description'] ?? ''); ?></textarea>
                            </div>
                            
                            <!-- Meta Keywords -->
                            <div>
                                <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                                <input type="text" 
                                       id="meta_keywords" 
                                       name="meta_keywords" 
                                       value="<?php echo htmlspecialchars($_POST['meta_keywords'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                       placeholder="কমা দিয়ে আলাদা করুন">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="space-y-6 sidebar-content">
                    <!-- Publish Options -->
                    <div class="form-section">
                        <h3>প্রকাশনা</h3>
                        
                        <div class="space-y-4">
                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">স্ট্যাটাস</label>
                                <select id="status" 
                                        name="status" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                                    <option value="published" <?php echo ($_POST['status'] ?? 'published') === 'published' ? 'selected' : ''; ?>>প্রকাশিত</option>
                                    <option value="draft" <?php echo ($_POST['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>ড্রাফট</option>
                                    <option value="pending" <?php echo ($_POST['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>অপেক্ষমাণ</option>
                                </select>
                            </div>
                            
                            <!-- Featured -->
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" 
                                           name="featured" 
                                           <?php echo isset($_POST['featured']) ? 'checked' : ''; ?>
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">ফিচার্ড রিভিউ</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                রিভিউ সেভ করুন
                            </button>
                        </div>
                    </div>
                    
                    <!-- Poster Image -->
                    <div class="form-section">
                        <h3>পোস্টার ছবি</h3>
                        
                        <div>
                            <input type="file" 
                                   id="poster_image" 
                                   name="poster_image" 
                                   accept="image/*"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                   onchange="previewImage(this, 'poster-preview')">
                            <p class="text-xs text-gray-500 mt-2">JPG, PNG, WEBP সর্বোচ্চ ৫MB</p>
                            
                            <div id="poster-preview" class="mt-4 hidden">
                                <img id="preview-img" src="" alt="Preview" class="w-full h-48 object-cover rounded-lg">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Categories -->
                    <div class="form-section">
                        <h3>ক্যাটেগরি</h3>
                        
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            <?php foreach ($categories as $category): ?>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" 
                                           name="categories[]" 
                                           value="<?php echo $category['id']; ?>"
                                           <?php echo in_array($category['id'], $_POST['categories'] ?? []) ? 'checked' : ''; ?>
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">
                                        <?php echo $category['name_bn'] ?: $category['name']; ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Download Links -->
                    <div class="form-section download-section">
                        <h3>📥 ডাউনলোড লিংক</h3>
                        
                        <div class="space-y-4">
                            <!-- Movie/Series Download URL -->
                            <div>
                                <label for="download_movie_url" class="block text-sm font-medium text-purple-800 mb-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                    </svg>
                                    ডাউনলোড মুভি/সিরিজ URL
                                </label>
                                <input type="url" 
                                       id="download_movie_url" 
                                       name="download_movie_url" 
                                       value="<?php echo htmlspecialchars($_POST['download_movie_url'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-base bg-white/80"
                                       placeholder="https://example.com/download-movie">
                                <p class="text-xs text-purple-700 mt-1">মুভি/সিরিজ ডাউনলোড করার জন্য URL দিন</p>
                            </div>
                            
                            <!-- Subtitle Download URL -->
                            <div>
                                <label for="download_subtitle_url" class="block text-sm font-medium text-purple-800 mb-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h3a1 1 0 011 1v10a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1h2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6"></path>
                                    </svg>
                                    ডাউনলোড সাবটাইটেল URL
                                </label>
                                <input type="url" 
                                       id="download_subtitle_url" 
                                       name="download_subtitle_url" 
                                       value="<?php echo htmlspecialchars($_POST['download_subtitle_url'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-base bg-white/80"
                                       placeholder="https://example.com/download-subtitle">
                                <p class="text-xs text-purple-700 mt-1">সাবটাইটেল ডাউনলোড করার জন্য URL দিন</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        </form>
    </div>
</div>

<!-- TinyMCE Script - Fixed Configuration for Both Editors -->
<script src="https://cdn.tiny.cloud/1/hhiyirqkh3fnrmgjs7nq6tpk6nqb62m3vww7smgrz7kjfv6v/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
// Get current directory path for upload handler
const currentPath = window.location.pathname;
const basePath = currentPath.substring(0, currentPath.lastIndexOf('/')) + '/';

// Initialize TinyMCE for main content editor
tinymce.init({
    selector: '.main-content-editor',
    height: 400,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image media link | code preview | help',
    content_style: `
        body { 
            font-family: 'Arial', sans-serif; 
            font-size: 14px; 
            line-height: 1.6;
            margin: 10px;
        }
        img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 4px;
            margin: 10px 0;
        }
    `,
    menubar: 'edit view insert format tools table help',
    toolbar_mode: 'sliding',
    automatic_uploads: true,
    file_picker_types: 'image',
    images_upload_handler: function (blobInfo, progress) {
        return new Promise(function(resolve, reject) {
            const xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', basePath + 'upload_handler.php');
            
            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    progress(e.loaded / e.total * 100);
                }
            };
            
            xhr.onload = function() {
                if (xhr.status === 403) {
                    reject({message: 'HTTP Error: ' + xhr.status, remove: true});
                    return;
                }
                
                if (xhr.status < 200 || xhr.status >= 300) {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }
                
                let json;
                try {
                    json = JSON.parse(xhr.responseText);
                } catch(e) {
                    reject('Invalid JSON response');
                    return;
                }
                
                if (!json || !json.success || typeof json.location != 'string') {
                    reject(json.message || 'Invalid JSON response');
                    return;
                }
                
                resolve(json.location);
            };
            
            xhr.onerror = function () {
                reject('Image upload failed');
            };
            
            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            
            xhr.send(formData);
        });
    },
    setup: function (editor) {
        editor.on('change keyup', function () {
            editor.save();
        });
        
        editor.on('init', function() {
            console.log('Main content editor initialized');
        });
    }
});

// Initialize TinyMCE for spoiler content editor - Separate configuration
tinymce.init({
    selector: '.spoiler-content-editor',
    height: 300,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image media link | code preview | help',
    content_style: `
        body { 
            font-family: 'Arial', sans-serif; 
            font-size: 14px; 
            line-height: 1.6;
            margin: 10px;
            background: linear-gradient(135deg, #fef3c7, #fed7aa);
            border: 2px dashed #f59e0b;
            border-radius: 8px;
            padding: 10px;
        }
        img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 4px;
            margin: 10px 0;
        }
        p {
            background: rgba(255, 255, 255, 0.7);
            padding: 8px;
            border-radius: 4px;
            margin: 8px 0;
        }
    `,
    menubar: 'edit view insert format tools table help',
    toolbar_mode: 'sliding',
    automatic_uploads: true,
    file_picker_types: 'image',
    images_upload_handler: function (blobInfo, progress) {
        return new Promise(function(resolve, reject) {
            const xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', basePath + 'upload_handler.php');
            
            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    progress(e.loaded / e.total * 100);
                }
            };
            
            xhr.onload = function() {
                if (xhr.status === 403) {
                    reject({message: 'HTTP Error: ' + xhr.status, remove: true});
                    return;
                }
                
                if (xhr.status < 200 || xhr.status >= 300) {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }
                
                let json;
                try {
                    json = JSON.parse(xhr.responseText);
                } catch(e) {
                    reject('Invalid JSON response');
                    return;
                }
                
                if (!json || !json.success || typeof json.location != 'string') {
                    reject(json.message || 'Invalid JSON response');
                    return;
                }
                
                resolve(json.location);
            };
            
            xhr.onerror = function () {
                reject('Image upload failed');
            };
            
            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            
            xhr.send(formData);
        });
    },
    setup: function (editor) {
        editor.on('change keyup', function () {
            editor.save();
        });
        
        editor.on('init', function() {
            console.log('Spoiler content editor initialized');
            // Apply spoiler-specific styling to the editor container
            const container = editor.getContainer();
            if (container) {
                container.classList.add('spoiler-editor');
            }
        });
    }
});

// Mobile menu functionality
document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
    const sidebar = document.querySelector('.admin-sidebar');
    const overlay = document.getElementById('mobile-overlay') || createMobileOverlay();
    
    sidebar?.classList.add('mobile-open');
    overlay.classList.add('active');
});

function createMobileOverlay() {
    const overlay = document.createElement('div');
    overlay.id = 'mobile-overlay';
    overlay.className = 'mobile-overlay';
    overlay.onclick = closeMobileMenu;
    document.body.appendChild(overlay);
    return overlay;
}

function closeMobileMenu() {
    const sidebar = document.querySelector('.admin-sidebar');
    const overlay = document.getElementById('mobile-overlay');
    
    sidebar?.classList.remove('mobile-open');
    overlay?.classList.remove('active');
}

// Image preview function
function previewImage(input, previewId) {
    const file = input.files[0];
    const preview = document.getElementById(previewId);
    const img = document.getElementById('preview-img');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
}

// URL Preview functionality
function updateUrlPreview() {
    const titleInput = document.getElementById('title');
    const customUrlInput = document.getElementById('custom_url');
    const urlPreview = document.getElementById('url-preview');
    const previewUrl = document.getElementById('preview-url');
    
    function showPreview() {
        const customUrl = customUrlInput.value.trim();
        const title = titleInput.value.trim();
        
        let finalUrl = '';
        
        if (customUrl) {
            finalUrl = customUrl;
        } else if (title) {
            // Simple slug generation for preview
            finalUrl = title.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }
        
        if (finalUrl) {
            previewUrl.textContent = '<?php echo SITE_URL; ?>/review.php?slug=' + finalUrl;
            urlPreview.classList.remove('hidden');
        } else {
            urlPreview.classList.add('hidden');
        }
    }
    
    titleInput.addEventListener('input', showPreview);
    customUrlInput.addEventListener('input', showPreview);
    
    // Show preview on page load if fields have values
    showPreview();
}

// Initialize URL preview
document.addEventListener('DOMContentLoaded', updateUrlPreview);

// Custom URL validation
document.getElementById('custom_url').addEventListener('input', function() {
    const value = this.value;
    const sanitized = value.toLowerCase().replace(/[^a-z0-9-]/g, '');
    if (value !== sanitized) {
        this.value = sanitized;
    }
});

// Prevent mobile keyboard zoom
if (window.innerWidth <= 768) {
    document.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('focus', function() {
            const viewport = document.querySelector('meta[name="viewport"]');
            if (viewport) {
                viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
            }
        });
        
        element.addEventListener('blur', function() {
            const viewport = document.querySelector('meta[name="viewport"]');
            if (viewport) {
                viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, user-scalable=yes');
            }
        });
    });
}

// Form auto-save functionality
let autoSaveTimeout;
function autoSave() {
    clearTimeout(autoSaveTimeout);
    autoSaveTimeout = setTimeout(() => {
        // Get form data
        const formData = new FormData(document.querySelector('form'));
        formData.append('auto_save', '1');
        
        // Save to localStorage as backup
        const formDataObj = {};
        for (let [key, value] of formData.entries()) {
            formDataObj[key] = value;
        }
        localStorage.setItem('review_auto_save', JSON.stringify(formDataObj));
        
        console.log('Form auto-saved to localStorage');
    }, 5000); // Auto-save every 5 seconds
}

// Attach auto-save to form inputs
document.querySelectorAll('input, textarea, select').forEach(element => {
    element.addEventListener('input', autoSave);
    element.addEventListener('change', autoSave);
});

// Load auto-saved data on page load
document.addEventListener('DOMContentLoaded', function() {
    const autoSavedData = localStorage.getItem('review_auto_save');
    if (autoSavedData && confirm('পূর্বে সংরক্ষিত ডেটা পাওয়া গেছে। লোড করবেন?')) {
        const data = JSON.parse(autoSavedData);
        for (let [key, value] of Object.entries(data)) {
            const element = document.querySelector(`[name="${key}"]`);
            if (element) {
                element.value = value;
            }
        }
    }
});

// Clear auto-save data on successful form submission
document.querySelector('form').addEventListener('submit', function() {
    localStorage.removeItem('review_auto_save');
});
</script>

<?php include '../includes/footer.php'; ?>