<?php
/**
 * Enhanced Edit Review - Admin with Spoiler Support, Custom URL and Download Links
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../../config/config.php';

// Require authentication
requireAuth();

// Get database instance
$db = Database::getInstance();

// Get review ID
$review_id = intval($_GET['id'] ?? 0);

// Get review data
$review = $db->fetchOne("SELECT * FROM reviews WHERE id = ?", [$review_id]);

if (!$review) {
    $_SESSION['error_message'] = 'রিভিউ পাওয়া যায়নি।';
    header('Location: index.php');
    exit;
}

// Get review categories
$review_categories = $db->fetchAll("SELECT category_id FROM review_categories WHERE review_id = ?", [$review_id]);
$review_category_ids = array_column($review_categories, 'category_id');

// Get all categories
$categories = $db->fetchAll("SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC");

// Get review stats
$like_count = $db->count("SELECT COUNT(*) FROM review_likes WHERE review_id = ?", [$review_id]);
$comment_count = $db->count("SELECT COUNT(*) FROM comments WHERE review_id = ? AND status = 'approved'", [$review_id]);

$error = '';

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
        
        // Don't sanitize rich content - keep HTML
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
            // Handle custom URL or generate slug
            $slug = $review['slug']; // Keep existing slug by default
            
            if (!empty($custom_url)) {
                // Validate custom URL format
                if (!preg_match('/^[a-z0-9-]+$/', $custom_url)) {
                    $error = 'কাস্টম URL শুধুমাত্র ছোট হাতের অক্ষর, সংখ্যা এবং হাইফেন (-) থাকতে পারে।';
                } elseif (strlen($custom_url) < 3) {
                    $error = 'কাস্টম URL কমপক্ষে ৩ অক্ষরের হতে হবে।';
                } else {
                    // Check if custom URL exists for other reviews
                    $existing = $db->fetchOne("SELECT id FROM reviews WHERE slug = ? AND id != ?", [$custom_url, $review_id]);
                    if ($existing) {
                        $error = 'এই URL ইতিমধ্যে ব্যবহৃত হয়েছে। অন্য URL ব্যবহার করুন।';
                    } else {
                        $slug = $custom_url;
                    }
                }
            } elseif ($title !== $review['title']) {
                // Generate new slug if title changed
                $slug = generateSlug($title);
                
                // Check if slug exists
                $existing = $db->fetchOne("SELECT id FROM reviews WHERE slug = ? AND id != ?", [$slug, $review_id]);
                if ($existing) {
                    $slug .= '-' . time();
                }
            }
            
            // Handle poster image upload
            $poster_image = $review['poster_image'];
            if (empty($error) && isset($_FILES['poster_image']) && $_FILES['poster_image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = uploadFile($_FILES['poster_image'], 'reviews');
                if ($upload_result['success']) {
                    // Delete old image
                    if ($poster_image) {
                        deleteFile('reviews', $poster_image);
                    }
                    $poster_image = $upload_result['filename'];
                } else {
                    $error = $upload_result['message'];
                }
            }
            
            if (empty($error)) {
                // Check if download columns exist in database
                $columns_exist = false;
                try {
                    $test_query = $db->fetchOne("SHOW COLUMNS FROM reviews LIKE 'download_movie_url'");
                    $columns_exist = ($test_query !== false);
                } catch (Exception $e) {
                    $columns_exist = false;
                }
                
                if ($columns_exist) {
                    // Update review with download URLs
                    $updated = $db->query("
                        UPDATE reviews SET 
                            title = ?, slug = ?, reviewer_name = ?, year = ?, type = ?, language = ?, 
                            director = ?, cast = ?, rating = ?, content = ?, spoiler_content = ?, excerpt = ?, 
                            poster_image = ?, trailer_url = ?, download_movie_url = ?, download_subtitle_url = ?, 
                            featured = ?, status = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, 
                            updated_at = NOW()
                        WHERE id = ?
                    ", [
                        $title, $slug, $reviewer_name, $year, $type, $language, $director, $cast,
                        $rating, $content, $spoiler_content, $excerpt, $poster_image, $trailer_url,
                        $download_movie_url, $download_subtitle_url, $featured, $status,
                        $meta_title, $meta_description, $meta_keywords, $review_id
                    ]);
                } else {
                    // Update review without download URLs (for compatibility)
                    $updated = $db->query("
                        UPDATE reviews SET 
                            title = ?, slug = ?, reviewer_name = ?, year = ?, type = ?, language = ?, 
                            director = ?, cast = ?, rating = ?, content = ?, spoiler_content = ?, excerpt = ?, 
                            poster_image = ?, trailer_url = ?, featured = ?, status = ?, 
                            meta_title = ?, meta_description = ?, meta_keywords = ?, updated_at = NOW()
                        WHERE id = ?
                    ", [
                        $title, $slug, $reviewer_name, $year, $type, $language, $director, $cast,
                        $rating, $content, $spoiler_content, $excerpt, $poster_image, $trailer_url, $featured,
                        $status, $meta_title, $meta_description, $meta_keywords, $review_id
                    ]);
                }
                
                if ($updated) {
                    // Update categories
                    // Delete existing categories
                    $db->query("DELETE FROM review_categories WHERE review_id = ?", [$review_id]);
                    
                    // Insert new categories
                    if (!empty($category_ids)) {
                        foreach ($category_ids as $category_id) {
                            $db->query("INSERT INTO review_categories (review_id, category_id) VALUES (?, ?)", [$review_id, $category_id]);
                        }
                    }
                    
                    $_SESSION['success_message'] = 'রিভিউ সফলভাবে আপডেট করা হয়েছে।';
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'রিভিউ আপডেট করতে সমস্যা হয়েছে।';
                }
            }
        }
    }
} else {
    // Pre-fill form with existing data
    // Ensure download URL fields exist (for backward compatibility)
    if (!array_key_exists('download_movie_url', $review)) {
        $review['download_movie_url'] = '';
    }
    if (!array_key_exists('download_subtitle_url', $review)) {
        $review['download_subtitle_url'] = '';
    }
    
    $_POST = [
        'title' => $review['title'],
        'custom_url' => '', // We'll show current slug in a readonly field
        'reviewer_name' => $review['reviewer_name'],
        'year' => $review['year'],
        'type' => $review['type'],
        'language' => $review['language'],
        'director' => $review['director'],
        'cast' => $review['cast'],
        'rating' => $review['rating'],
        'content' => $review['content'],
        'spoiler_content' => $review['spoiler_content'],
        'excerpt' => $review['excerpt'],
        'trailer_url' => $review['trailer_url'],
        'download_movie_url' => $review['download_movie_url'],
        'download_subtitle_url' => $review['download_subtitle_url'],
        'featured' => $review['featured'],
        'status' => $review['status'],
        'categories' => $review_category_ids,
        'meta_title' => $review['meta_title'],
        'meta_description' => $review['meta_description'],
        'meta_keywords' => $review['meta_keywords']
    ];
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
}

.spoiler-input-container {
    background: linear-gradient(135deg, #fef3c7, #fed7aa);
    border: 2px dashed #f59e0b;
    border-radius: 12px;
    padding: 20px;
    margin: 16px 0;
}

.spoiler-input {
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid #d97706;
}

.spoiler-input:focus {
    background: rgba(255, 255, 255, 0.95);
    border-color: #b45309;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
}

.stat-card {
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 12px;
    text-align: center;
}

.stat-number {
    font-size: 24px;
    font-weight: bold;
    color: #1e293b;
}

.stat-label {
    font-size: 12px;
    color: #64748b;
    margin-top: 4px;
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

.current-url {
    background: #f0f9ff;
    border: 1px solid #0ea5e9;
    color: #0c4a6e;
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

/* Preview styles for spoiler content */
.spoiler-preview {
    background: #fff7ed;
    border: 1px solid #fb923c;
    border-radius: 8px;
    padding: 16px;
    margin: 16px 0;
}

.spoiler-preview h4 {
    color: #ea580c;
    margin-bottom: 8px;
    font-weight: 600;
}
</style>

<!-- Mobile Header -->
<div class="mobile-header md:hidden">
    <button id="mobile-menu-btn" class="p-2 rounded-lg hover:bg-gray-100">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
    <h1 class="text-lg font-bold">রিভিউ সম্পাদনা</h1>
    <div class="w-8"></div>
</div>

<div class="flex-1 bg-gray-100 admin-content">
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-4 md:px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900">রিভিউ সম্পাদনা</h1>
                        <p class="text-gray-600 text-sm md:text-base truncate max-w-xs md:max-w-none">
                            <?php echo htmlspecialchars($review['title']); ?>
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="../../review/<?php echo $review['slug']; ?>" 
                       target="_blank"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-3 md:px-4 py-2 rounded-lg text-sm transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        <span class="hidden sm:inline">দেখুন</span>
                    </a>
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
                                       required>
                            </div>
                            
                            <!-- Current URL Display -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">বর্তমান URL</label>
                                <div class="current-url px-4 py-3 rounded-lg text-base">
                                    <?php echo SITE_URL; ?>/review/<?php echo $review['slug']; ?>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">এই URL পরিবর্তন করতে নিচের কাস্টম URL ব্যবহার করুন</p>
                            </div>
                            
                            <!-- Custom URL -->
                            <div>
                                <label for="custom_url" class="block text-sm font-medium text-gray-700 mb-2">
                                    নতুন কাস্টম URL (ঐচ্ছিক)
                                </label>
                                <div class="flex items-center">
                                    <span class="bg-gray-100 border border-r-0 border-gray-300 px-3 py-3 text-sm text-gray-600 rounded-l-lg">
                                        <?php echo SITE_URL; ?>/review/
                                    </span>
                                    <input type="text" 
                                           id="custom_url" 
                                           name="custom_url" 
                                           value="<?php echo htmlspecialchars($_POST['custom_url'] ?? ''); ?>"
                                           class="flex-1 px-4 py-3 border border-gray-300 rounded-r-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                           placeholder="new-review-url"
                                           pattern="[a-z0-9-]+"
                                           title="শুধুমাত্র ছোট হাতের অক্ষর, সংখ্যা এবং হাইফেন ব্যবহার করুন">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    খালি রাখলে বর্তমান URL অপরিবর্তিত থাকবে। শুধুমাত্র ছোট হাতের অক্ষর, সংখ্যা এবং হাইফেন (-) ব্যবহার করুন।
                                </p>
                            </div>
                            
                            <!-- Reviewer Name -->
                            <div>
                                <label for="reviewer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    লেখক নাম <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="reviewer_name" 
                                       name="reviewer_name" 
                                       value="<?php echo htmlspecialchars($_POST['reviewer_name'] ?? ''); ?>"
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
                                           value="<?php echo htmlspecialchars($_POST['year'] ?? ''); ?>"
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
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
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
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"><?php echo htmlspecialchars($_POST['cast'] ?? ''); ?></textarea>
                            </div>
                            
                            <!-- Trailer URL -->
                            <div>
                                <label for="trailer_url" class="block text-sm font-medium text-gray-700 mb-2">ট্রেইলার URL</label>
                                <input type="url" 
                                       id="trailer_url" 
                                       name="trailer_url" 
                                       value="<?php echo htmlspecialchars($_POST['trailer_url'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
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
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"><?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?></textarea>
                            </div>
                            
                            <!-- Main Content -->
                            <div>
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                    রিভিউ কন্টেন্ট <span class="text-red-500">*</span>
                                </label>
                                <textarea id="content" 
                                          name="content" 
                                          class="tinymce-editor w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                          rows="10"
                                          required><?php echo $_POST['content'] ?? ''; ?></textarea>
                            </div>
                            
                            <!-- Enhanced Spoiler Content with TinyMCE -->
                            <div class="spoiler-input-container">
                                <div class="flex items-start mb-3">
                                    <span class="text-2xl mr-3 mt-1">⚠️</span>
                                    <div class="flex-1">
                                        <label for="spoiler_content" class="block text-sm font-bold text-orange-800 mb-1">
                                            স্পয়লার কন্টেন্ট (ঐচ্ছিক)
                                        </label>
                                        <p class="text-xs text-orange-700 mb-2">
                                            গল্পের গুরুত্বপূর্ণ তথ্য বা সারপ্রাইজ যা পাঠকদের জন্য স্পয়লার হতে পারে
                                        </p>
                                        <?php if (!empty($_POST['spoiler_content'])): ?>
                                            <div class="bg-orange-100 border border-orange-300 rounded px-3 py-2 mb-2">
                                                <span class="text-xs text-orange-800 font-medium">✅ এই রিভিউতে স্পয়লার কন্টেন্ট আছে</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <textarea id="spoiler_content" 
                                          name="spoiler_content" 
                                          class="tinymce-editor spoiler-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-base"
                                          rows="8"><?php echo $_POST['spoiler_content'] ?? ''; ?></textarea>
                                <div class="mt-2 flex items-center text-xs text-orange-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    স্পয়লার কন্টেন্ট একটি বিশেষ বাটনের পিছনে লুকানো থাকবে এবং পাঠকরা ইচ্ছা করলেই দেখতে পারবেন।
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
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                            </div>
                            
                            <!-- Meta Description -->
                            <div>
                                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                                <textarea id="meta_description" 
                                          name="meta_description" 
                                          rows="2"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"><?php echo htmlspecialchars($_POST['meta_description'] ?? ''); ?></textarea>
                            </div>
                            
                            <!-- Meta Keywords -->
                            <div>
                                <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                                <input type="text" 
                                       id="meta_keywords" 
                                       name="meta_keywords" 
                                       value="<?php echo htmlspecialchars($_POST['meta_keywords'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
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
                                    <option value="published" <?php echo ($_POST['status'] ?? '') === 'published' ? 'selected' : ''; ?>>প্রকাশিত</option>
                                    <option value="draft" <?php echo ($_POST['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>ড্রাফট</option>
                                    <option value="pending" <?php echo ($_POST['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>অপেক্ষমাণ</option>
                                </select>
                            </div>
                            
                            <!-- Featured -->
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" 
                                           name="featured" 
                                           <?php echo ($_POST['featured'] ?? 0) ? 'checked' : ''; ?>
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">ফিচার্ড রিভিউ</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                রিভিউ আপডেট করুন
                            </button>
                        </div>
                    </div>
                    
                    <!-- Poster Image -->
                    <div class="form-section">
                        <h3>পোস্টার ছবি</h3>
                        
                        <?php if ($review['poster_image']): ?>
                            <div class="mb-4">
                                <img src="<?php echo (defined('UPLOADS_URL') ? UPLOADS_URL : '/assets/uploads'); ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                     alt="Current poster" 
                                     class="w-full h-48 object-cover rounded-lg">
                                <p class="text-sm text-gray-500 mt-2">বর্তমান পোস্টার</p>
                            </div>
                        <?php endif; ?>
                        
                        <div>
                            <input type="file" 
                                   id="poster_image" 
                                   name="poster_image" 
                                   accept="image/*"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                   onchange="previewImage(this, 'poster-preview')">
                            <p class="text-xs text-gray-500 mt-2">নতুন ছবি নির্বাচন করুন (ঐচ্ছিক)</p>
                            
                            <div id="poster-preview" class="mt-4 hidden">
                                <img id="preview-img" src="" alt="Preview" class="w-full h-48 object-cover rounded-lg">
                                <p class="text-sm text-gray-600 mt-2">নতুন পোস্টার প্রিভিউ</p>
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
                    
                    <!-- Enhanced Review Stats -->
                    <div class="form-section">
                        <h3>পরিসংখ্যান ও ইন্টারঅ্যাকশন</h3>
                        
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-number"><?php echo number_format($review['view_count']); ?></div>
                                <div class="stat-label">ভিউ</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?php echo number_format($like_count); ?></div>
                                <div class="stat-label">লাইক</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?php echo number_format($comment_count); ?></div>
                                <div class="stat-label">মন্তব্য</div>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">তৈরি:</span>
                                    <span class="font-medium"><?php echo date('d M Y', strtotime($review['created_at'])); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">আপডেট:</span>
                                    <span class="font-medium"><?php echo date('d M Y', strtotime($review['updated_at'])); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Slug:</span>
                                    <span class="text-xs font-mono text-gray-500 max-w-xs truncate"><?php echo $review['slug']; ?></span>
                                </div>
                                <?php if (!empty($_POST['spoiler_content'])): ?>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">স্পয়লার:</span>
                                        <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full">আছে</span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($_POST['download_movie_url']) || !empty($_POST['download_subtitle_url'])): ?>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">ডাউনলোড:</span>
                                        <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">আছে</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="space-y-2">
                                <a href="../../review/<?php echo $review['slug']; ?>" 
                                   target="_blank"
                                   class="w-full bg-gray-600 hover:bg-gray-700 text-white text-center py-2 px-3 rounded text-sm transition-colors flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    লাইভ দেখুন
                                </a>
                                <?php if ($comment_count > 0): ?>
                                    <a href="#" onclick="showCommentsModal(<?php echo $review_id; ?>)" 
                                       class="w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 px-3 rounded text-sm transition-colors flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        মন্তব্য দেখুন
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        </form>
    </div>
</div>

<!-- TinyMCE Script -->
<script src="https://cdn.tiny.cloud/1/hhiyirqkh3fnrmgjs7nq6tpk6nqb62m3vww7smgrz7kjfv6v/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
// TinyMCE initialization - Fixed configuration with image upload
tinymce.init({
    selector: '.tinymce-editor',
    height: 300,
    language: 'en',
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons'
    ],
    toolbar: 'undo redo | blocks | ' +
        'bold italic forecolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | image media link | code preview | help',
    content_style: `
        body { 
            font-family: Helvetica, Arial, sans-serif; 
            font-size: 14px; 
            line-height: 1.6;
        }
        img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 4px;
            margin: 10px 0;
            display: block;
        }
    `,
    
    // Critical fixes
    convert_urls: false,
    relative_urls: false,
    remove_script_host: false,
    
    // Image upload configuration
    automatic_uploads: true,
    images_upload_url: 'upload_handler.php',
    images_reuse_filename: false,
    
    // Upload handler
    images_upload_handler: function (blobInfo, progress) {
        return new Promise(function(resolve, reject) {
            const xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', 'upload_handler.php');
            
            showUploadProgress();
            
            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progress(percentComplete);
                    updateUploadProgress(percentComplete);
                }
            };
            
            xhr.onload = function() {
                hideUploadProgress();
                
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
                    reject('Invalid JSON response: ' + xhr.responseText);
                    return;
                }
                
                if (!json || !json.success || typeof json.location != 'string') {
                    reject(json.message || 'Invalid JSON: ' + xhr.responseText);
                    return;
                }
                
                resolve(json.location);
            };
            
            xhr.onerror = function () {
                hideUploadProgress();
                reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
            };
            
            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            
            const csrfToken = document.querySelector('input[name="csrf_token"]');
            if (csrfToken) {
                formData.append('csrf_token', csrfToken.value);
            }
            
            xhr.send(formData);
        });
    },
    
    setup: function (editor) {
        editor.on('change keyup', function () {
            editor.save();
        });
        
        editor.on('init', function() {
            console.log('TinyMCE initialized successfully for:', editor.id);
        });
    },
    image_advtab: true,
    image_caption: true,
    paste_data_images: true
});

// Upload progress functions
function showUploadProgress() {
    const progressDiv = document.getElementById('image-upload-progress');
    const progressFill = document.getElementById('upload-progress-fill');
    if (progressDiv) {
        progressDiv.classList.add('active');
        progressFill.style.width = '0%';
    }
}

function updateUploadProgress(percent) {
    const progressFill = document.getElementById('upload-progress-fill');
    if (progressFill) {
        progressFill.style.width = percent + '%';
    }
}

function hideUploadProgress() {
    const progressDiv = document.getElementById('image-upload-progress');
    if (progressDiv) {
        setTimeout(() => {
            progressDiv.classList.remove('active');
        }, 500);
    }
}

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
    overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50';
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

// Comments modal function
function showCommentsModal(reviewId) {
    // This would open a modal to view/moderate comments
    // For now, we'll redirect to a comments management page
    window.open(`comments.php?review_id=${reviewId}`, '_blank');
}

// Custom URL validation
document.getElementById('custom_url').addEventListener('input', function() {
    const value = this.value;
    const sanitized = value.toLowerCase().replace(/[^a-z0-9-]/g, '');
    if (value !== sanitized) {
        this.value = sanitized;
    }
});

// Mobile input focus fix
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
</script>

<?php include '../includes/footer.php'; ?>