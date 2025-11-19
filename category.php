<?php
/**
 * Category Page
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config/config.php';

// Get database instance
$db = Database::getInstance();

// Get category slug from URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Get category data
$category = $db->fetchOne("SELECT * FROM categories WHERE slug = ? AND status = 'active'", [$slug]);

if (!$category) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = POSTS_PER_PAGE;
$offset = ($page - 1) * $per_page;

// Filters
$type = sanitize($_GET['type'] ?? '');
$language = sanitize($_GET['language'] ?? '');
$sort = sanitize($_GET['sort'] ?? 'latest');

// Build query
$where_conditions = ["r.status = 'published'", "rc.category_id = ?"];
$params = [$category['id']];

if ($type) {
    $where_conditions[] = "r.type = ?";
    $params[] = $type;
}

if ($language) {
    $where_conditions[] = "r.language = ?";
    $params[] = $language;
}

$where_clause = implode(' AND ', $where_conditions);

// Sort options
$order_clause = match($sort) {
    'oldest' => 'r.created_at ASC',
    'rating' => 'r.rating DESC, r.created_at DESC',
    'popular' => 'r.view_count DESC, r.created_at DESC',
    'title' => 'r.title ASC',
    default => 'r.created_at DESC'
};

// Get total count
$total_reviews = $db->count("
    SELECT COUNT(DISTINCT r.id) 
    FROM reviews r 
    INNER JOIN review_categories rc ON r.id = rc.review_id 
    WHERE $where_clause
", $params);

$total_pages = ceil($total_reviews / $per_page);

// Get reviews
$reviews = $db->fetchAll("
    SELECT DISTINCT r.*
    FROM reviews r 
    INNER JOIN review_categories rc ON r.id = rc.review_id 
    WHERE $where_clause
    ORDER BY $order_clause
    LIMIT $per_page OFFSET $offset
", $params);

// SEO Meta Data
$seo_title = $category['name_bn'] ?: $category['name'];
if ($page > 1) $seo_title .= ' - পেজ ' . $page;
$seo_title .= ' - ' . getSiteSetting('site_name', SITE_NAME);

$seo_description = ($category['description'] ?: $category['name_bn'] ?: $category['name']) . ' ক্যাটেগরির সব রিভিউ দেখুন।';
$seo_keywords = ($category['name_bn'] ?: $category['name']) . ', movie review, drama review';

// Breadcrumbs
$breadcrumbs = [
    ['name' => 'হোম', 'url' => '/'],
    ['name' => 'ক্যাটেগরি', 'url' => '/categories'],
    ['name' => $category['name_bn'] ?: $category['name'], 'url' => '/category/' . $category['slug']]
];

// Include header
include 'includes/header.php';
?>

<!-- Category Header -->
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
        
        <!-- Category Icon & Title -->
        <div class="mb-6">
            <?php if ($category['icon']): ?>
                <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center" 
                     style="background-color: <?php echo $category['color']; ?>">
                    <i class="<?php echo $category['icon']; ?> text-3xl text-white"></i>
                </div>
            <?php endif; ?>
            
            <h1 class="text-4xl lg:text-5xl font-bold mb-4">
                <?php echo htmlspecialchars($category['name_bn'] ?: $category['name']); ?>
            </h1>
            
            <?php if ($category['description']): ?>
                <p class="text-xl text-white/90 max-w-2xl mx-auto">
                    <?php echo htmlspecialchars($category['description']); ?>
                </p>
            <?php endif; ?>
        </div>
        
        <!-- Stats -->
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 inline-block">
            <div class="text-2xl font-bold"><?php echo number_format($total_reviews); ?>টি রিভিউ</div>
            <div class="text-white/80">এই ক্যাটেগরিতে</div>
        </div>
    </div>
</section>

<!-- Filters -->
<section class="py-6 bg-white sticky top-0 z-40 border-b border-gray-200">
    <div class="container mx-auto px-4">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <!-- Filters -->
            <div class="flex flex-wrap gap-3">
                <!-- Type Filter -->
                <select onchange="updateFilter('type', this.value)" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">সব ধরণ</option>
                    <option value="movie" <?php echo $type === 'movie' ? 'selected' : ''; ?>>সিনেমা</option>
                    <option value="series" <?php echo $type === 'series' ? 'selected' : ''; ?>>সিরিয়াল</option>
                </select>
                
                <!-- Language Filter -->
                <select onchange="updateFilter('language', this.value)" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">সব ভাষা</option>
                    <option value="বাংলা" <?php echo $language === 'বাংলা' ? 'selected' : ''; ?>>বাংলা</option>
                    <option value="ইংরেজি" <?php echo $language === 'ইংরেজি' ? 'selected' : ''; ?>>ইংরেজি</option>
                    <option value="হিন্দি" <?php echo $language === 'হিন্দি' ? 'selected' : ''; ?>>হিন্দি</option>
                    <option value="কোরিয়ান" <?php echo $language === 'কোরিয়ান' ? 'selected' : ''; ?>>কোরিয়ান</option>
                </select>
            </div>
            
            <!-- Sort Options -->
            <div class="flex items-center space-x-4">
                <span class="text-gray-700 text-sm">সাজান:</span>
                <select onchange="updateFilter('sort', this.value)" 
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>সর্বশেষ</option>
                    <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>পুরাতন</option>
                    <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>উচ্চ রেটিং</option>
                    <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>জনপ্রিয়</option>
                    <option value="title" <?php echo $sort === 'title' ? 'selected' : ''; ?>>নাম অনুযায়ী</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Grid -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <?php if (!empty($reviews)): ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php foreach ($reviews as $review): ?>
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group hover:-translate-y-2">
                        <!-- Poster -->
                        <div class="relative h-80 bg-gray-200 overflow-hidden">
                            <?php if ($review['poster_image']): ?>
                                <img src="<?php echo UPLOADS_URL; ?>/reviews/<?php echo $review['poster_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <!-- Play Button -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Rating Badge -->
                            <?php if ($review['rating'] > 0): ?>
                                <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm font-medium">
                                    ★ <?php echo number_format($review['rating'], 1); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Type Badge -->
                            <div class="absolute top-4 left-4 bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-medium">
                                <?php echo $review['type'] === 'movie' ? 'সিনেমা' : 'সিরিয়াল'; ?>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                <a href="/review/<?php echo $review['slug']; ?>">
                                    <?php echo htmlspecialchars($review['title']); ?>
                                </a>
                            </h3>
                            
                            <div class="flex items-center text-gray-500 text-sm mb-3">
                                <?php if ($review['year']): ?>
                                    <span><?php echo $review['year']; ?></span>
                                    <span class="mx-2">•</span>
                                <?php endif; ?>
                                <?php if ($review['language']): ?>
                                    <span><?php echo $review['language']; ?></span>
                                    <span class="mx-2">•</span>
                                <?php endif; ?>
                                <span><?php echo number_format($review['view_count']); ?> ভিউ</span>
                            </div>
                            
                            <!-- Excerpt -->
                            <?php if ($review['excerpt']): ?>
                                <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">
                                    <?php echo htmlspecialchars(strip_tags($review['excerpt'])); ?>
                                </p>
                            <?php endif; ?>
                            
                            <!-- Footer -->
                            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                <span class="text-gray-500 text-sm">
                                    <?php echo timeAgo($review['created_at']); ?>
                                </span>
                                <a href="/review/<?php echo $review['slug']; ?>" 
                                   class="text-blue-600 hover:text-blue-700 font-medium text-sm transition-colors">
                                    পড়ুন →
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="flex justify-center mt-12">
                    <nav class="flex items-center space-x-2">
                        <!-- Previous -->
                        <?php if ($page > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                « পূর্ববর্তী
                            </a>
                        <?php endif; ?>
                        
                        <!-- Page Numbers -->
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                               class="px-4 py-2 border rounded-lg transition-colors <?php echo $i === $page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <!-- Next -->
                        <?php if ($page < $total_pages): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                পরবর্তী »
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
                
                <!-- Page Info -->
                <div class="text-center mt-4 text-gray-600">
                    পেজ <?php echo $page; ?> of <?php echo $total_pages; ?> 
                    (<?php echo number_format($total_reviews); ?>টি রিভিউ)
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- No Reviews Found -->
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 rounded-full flex items-center justify-center" 
                     style="background-color: <?php echo $category['color']; ?>20;">
                    <?php if ($category['icon']): ?>
                        <i class="<?php echo $category['icon']; ?> text-4xl" style="color: <?php echo $category['color']; ?>"></i>
                    <?php else: ?>
                        <svg class="w-12 h-12" style="color: <?php echo $category['color']; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>
                        </svg>
                    <?php endif; ?>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">এই ক্যাটেগরিতে কোনো রিভিউ পাওয়া যায়নি</h3>
                <p class="text-gray-600 mb-8">
                    আপনার নির্বাচিত ফিল্টার অনুযায়ী কোনো রিভিউ খুঁজে পাওয়া যায়নি। 
                    অন্য ফিল্টার চেষ্টা করুন।
                </p>
                <div class="flex justify-center space-x-4">
                    <button onclick="clearFilters()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                        ফিল্টার মুছুন
                    </button>
                    <a href="/submit-review" 
                       class="border border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-3 rounded-lg transition-colors">
                        রিভিউ জমা দিন
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Filter functions
function updateFilter(key, value) {
    const url = new URL(window.location);
    
    if (value) {
        url.searchParams.set(key, value);
    } else {
        url.searchParams.delete(key);
    }
    
    // Reset to first page when filter changes
    url.searchParams.delete('page');
    
    window.location.href = url.toString();
}

function clearFilters() {
    const url = new URL(window.location);
    const allowedParams = ['slug']; // Keep category slug
    
    for (const key of Array.from(url.searchParams.keys())) {
        if (!allowedParams.includes(key)) {
            url.searchParams.delete(key);
        }
    }
    
    window.location.href = url.toString();
}
</script>

<?php include 'includes/footer.php'; ?>