<?php
/**
 * Enhanced Add New Review - Admin with Spoiler Support
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
        $reviewer_name = sanitize($_POST['reviewer_name'] ?? '');
        $year = sanitize($_POST['year'] ?? '');
        $type = sanitize($_POST['type'] ?? 'movie');
        $language = sanitize($_POST['language'] ?? '');
        $director = sanitize($_POST['director'] ?? '');
        $cast = sanitize($_POST['cast'] ?? '');
        $rating = floatval($_POST['rating'] ?? 0);
        $content = $_POST['content'] ?? '';
        $spoiler_content = $_POST['spoiler_content'] ?? '';  // New spoiler field
        $excerpt = sanitize($_POST['excerpt'] ?? '');
        $trailer_url = sanitize($_POST['trailer_url'] ?? '');
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
                        title, slug, reviewer_name, year, type, language, director, cast, 
                        rating, content, spoiler_content, excerpt, poster_image, trailer_url, featured, 
                        status, created_by, meta_title, meta_description, meta_keywords, 
                        created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'admin', ?, ?, ?, NOW())
                ", [
                    $title, $slug, $reviewer_name, $year, $type, $language, $director, $cast,
                    $rating, $content, $spoiler_content, $excerpt, $poster_image, $trailer_url, $featured,
                    $status, $meta_title, $meta_description, $meta_keywords
                ]);
                
                if ($review_id) {
                    $review_id = $db->lastInsertId();
                    
                    // Insert categories
                    if (!empty($category_ids)) {
                        foreach ($category_ids as $category_id) {
                            $db->query("INSERT INTO review_categories (review_id, category_id) VALUES (?, ?)", [$review_id, $category_id]);
                        }
                    }
                    
                    $_SESSION['success_message'] = 'রিভিউ সফলভাবে যোগ করা হয়েছে।';
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'রিভিউ যোগ করতে সমস্যা হয়েছে।';
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
                                          class="tinymce-editor w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                          rows="10"
                                          required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                            </div>
                            
                            <!-- NEW: Spoiler Content -->
                            <div class="spoiler-input-container">
                                <div class="flex items-center mb-3">
                                    <span class="text-2xl mr-2">⚠️</span>
                                    <div>
                                        <label for="spoiler_content" class="block text-sm font-bold text-orange-800 mb-1">
                                            স্পয়লার কন্টেন্ট (ঐচ্ছিক)
                                        </label>
                                        <p class="text-xs text-orange-700">
                                            গল্পের গুরুত্বপূর্ণ তথ্য বা সারপ্রাইজ যা পাঠকদের জন্য স্পয়লার হতে পারে
                                        </p>
                                    </div>
                                </div>
                                <textarea id="spoiler_content" 
                                          name="spoiler_content" 
                                          rows="6"
                                          class="spoiler-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-base"
                                          placeholder="এখানে স্পয়লার সংক্রান্ত আলোচনা লিখুন। যেমন: গল্পের মোড়, চরিত্রের ভাগ্য, সারপ্রাইজ টুইস্ট ইত্যাদি। এই অংশটি পাঠকরা শুধুমাত্র 'স্পয়লার দেখান' বাটনে ক্লিক করার পর দেখতে পাবেন।"><?php echo htmlspecialchars($_POST['spoiler_content'] ?? ''); ?></textarea>
                                <div class="mt-2 text-xs text-orange-600">
                                    💡 টিপস: স্পয়লার কন্টেন্ট একটি বিশেষ বাটনের পিছনে লুকানো থাকবে এবং পাঠকরা ইচ্ছা করলেই দেখতে পারবেন।
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
                </div>
            </div>
            
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        </form>
    </div>
</div>

<script>
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