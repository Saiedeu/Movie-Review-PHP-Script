<?php
/**
 * Search Results Page
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config/config.php';

// Get database instance
$db = Database::getInstance();

// Get search query
$query = trim($_GET['q'] ?? '');

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = POSTS_PER_PAGE;
$offset = ($page - 1) * $per_page;

$reviews = [];
$total_results = 0;
$total_pages = 0;

if (!empty($query)) {
    // Prepare search terms
    $search_terms = explode(' ', $query);
    $search_conditions = [];
    $search_params = [];
    
    foreach ($search_terms as $term) {
        if (strlen($term) >= 2) {
            $search_conditions[] = "(r.title LIKE ? OR r.content LIKE ? OR r.director LIKE ? OR r.cast LIKE ?)";
            $like_term = '%' . $term . '%';
            $search_params = array_merge($search_params, [$like_term, $like_term, $like_term, $like_term]);
        }
    }
    
    if (!empty($search_conditions)) {
        $search_clause = implode(' AND ', $search_conditions);
        
        // Get total count
        $count_query = "
            SELECT COUNT(DISTINCT r.id) 
            FROM reviews r 
            WHERE r.status = 'published' AND ($search_clause)
        ";
        $total_results = $db->count($count_query, $search_params);
        $total_pages = ceil($total_results / $per_page);
        
        // Get search results
        $search_query = "
            SELECT DISTINCT r.*, 
                   GROUP_CONCAT(c.name_bn ORDER BY c.name_bn SEPARATOR ', ') as categories
            FROM reviews r 
            LEFT JOIN review_categories rc ON r.id = rc.review_id
            LEFT JOIN categories c ON rc.category_id = c.id
            WHERE r.status = 'published' AND ($search_clause)
            GROUP BY r.id
            ORDER BY r.created_at DESC
            LIMIT $per_page OFFSET $offset
        ";
        
        $reviews = $db->fetchAll($search_query, $search_params);
    }
}

// SEO Meta Data
$seo_title = !empty($query) ? "খোঁজার ফলাফল: \"$query\"" : 'খুঁজুন';
if ($page > 1) $seo_title .= ' - পেজ ' . $page;
$seo_title .= ' - ' . getSiteSetting('site_name', SITE_NAME);

$seo_description = !empty($query) ? "\"$query\" এর জন্য খোঁজার ফলাফল। সিনেমা ও সিরিয়ালের রিভিউ খুঁজুন।" : 'আমাদের সাইটে সিনেমা ও সিরিয়ালের রিভিউ খুঁজুন।';
$seo_keywords = 'search, খুঁজুন, movie search, review search';

// Breadcrumbs
$breadcrumbs = [
    ['name' => 'হোম', 'url' => '/'],
    ['name' => 'খোঁজার ফলাফল', 'url' => '/search']
];

// Include header
include 'includes/header.php';
?>

<!-- Search Header -->
<section class="py-16 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center text-white">
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
            
            <h1 class="text-4xl lg:text-5xl font-bold mb-6">খুঁজুন</h1>
            
            <!-- Search Form -->
            <form action="/search" method="GET" class="mb-6">
                <div class="relative">
                    <input type="text" 
                           name="q" 
                           value="<?php echo htmlspecialchars($query); ?>"
                           placeholder="সিনেমা বা সিরিয়ালের নাম, অভিনেতা বা পরিচালকের নাম লিখুন..." 
                           class="w-full px-6 py-4 text-lg text-gray-900 rounded-xl focus:outline-none focus:ring-4 focus:ring-white/50"
                           autocomplete="off">
                    <button type="submit" 
                            class="absolute right-2 top-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
            
            <!-- Search Info -->
            <?php if (!empty($query)): ?>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 inline-block">
                    <span class="text-lg">
                        "<?php echo htmlspecialchars($query); ?>" এর জন্য 
                        <?php echo number_format($total_results); ?>টি ফলাফল পাওয়া গেছে
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Search Results -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <?php if (!empty($query)): ?>
            <?php if (!empty($reviews)): ?>
                <!-- Results Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
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
                                        <!-- Highlight search terms -->
                                        <?php 
                                        $highlighted_title = $review['title'];
                                        if (!empty($query)) {
                                            $highlighted_title = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200">$1</mark>', $highlighted_title);
                                        }
                                        echo $highlighted_title;
                                        ?>
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
                                
                                <!-- Categories -->
                                <?php if ($review['categories']): ?>
                                    <div class="flex flex-wrap gap-1 mb-3">
                                        <?php 
                                        $cats = explode(', ', $review['categories']);
                                        foreach (array_slice($cats, 0, 2) as $cat): 
                                        ?>
                                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                                <?php echo htmlspecialchars($cat); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Excerpt -->
                                <?php if ($review['excerpt']): ?>
                                    <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">
                                        <?php 
                                        $excerpt = htmlspecialchars(strip_tags($review['excerpt']));
                                        if (!empty($query)) {
                                            $excerpt = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200">$1</mark>', $excerpt);
                                        }
                                        echo $excerpt;
                                        ?>
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
                    <div class="flex justify-center">
                        <nav class="flex items-center space-x-2">
                            <!-- Previous -->
                            <?php if ($page > 1): ?>
                                <a href="?<?php echo http_build_query(['q' => $query, 'page' => $page - 1]); ?>" 
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
                                <a href="?<?php echo http_build_query(['q' => $query, 'page' => $i]); ?>" 
                                   class="px-4 py-2 border rounded-lg transition-colors <?php echo $i === $page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <!-- Next -->
                            <?php if ($page < $total_pages): ?>
                                <a href="?<?php echo http_build_query(['q' => $query, 'page' => $page + 1]); ?>" 
                                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                    পরবর্তী »
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                    
                    <!-- Page Info -->
                    <div class="text-center mt-4 text-gray-600">
                        পেজ <?php echo $page; ?> of <?php echo $total_pages; ?> 
                        (<?php echo number_format($total_results); ?>টি ফলাফল)
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <!-- No Results -->
                <div class="text-center py-16">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">কোনো ফলাফল পাওয়া যায়নি</h3>
                    <p class="text-gray-600 mb-8">
                        "<?php echo htmlspecialchars($query); ?>" এর জন্য কোনো রিভিউ খুঁজে পাওয়া যায়নি। 
                        অন্য কিছু খোঁজার চেষ্টা করুন।
                    </p>
                    
                    <!-- Search Suggestions -->
                    <div class="bg-white rounded-xl p-8 max-w-md mx-auto">
                        <h4 class="font-bold text-gray-900 mb-4">খোঁজার টিপস:</h4>
                        <ul class="text-gray-600 text-sm space-y-2 text-left">
                            <li>• সিনেমা বা সিরিয়ালের সম্পূর্ণ নাম লিখুন</li>
                            <li>• অভিনেতা বা পরিচালকের নাম দিয়ে খুঁজুন</li>
                            <li>• ইংরেজি বা বাংলা যেকোনো ভাষায় লিখুন</li>
                            <li>• বানান ঠিক আছে কিনা দেখুন</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- Search Page Content -->
            <div class="max-w-4xl mx-auto text-center">
                <div class="bg-white rounded-2xl shadow-lg p-12">
                    <svg class="w-24 h-24 text-blue-600 mx-auto mb-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">আপনার পছন্দের কন্টেন্ট খুঁজুন</h2>
                    <p class="text-gray-600 text-lg mb-8">
                        সিনেমা, সিরিয়াল, অভিনেতা বা পরিচালকের নাম দিয়ে খুঁজুন
                    </p>
                    
                    <!-- Popular Searches -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">জনপ্রিয় অনুসন্ধান:</h3>
                        <div class="flex flex-wrap justify-center gap-3">
                            <a href="/search?q=বাংলা সিনেমা" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-2 rounded-full text-sm transition-colors">বাংলা সিনেমা</a>
                            <a href="/search?q=কোরিয়ান ড্রামা" class="bg-green-100 hover:bg-green-200 text-green-800 px-4 py-2 rounded-full text-sm transition-colors">কোরিয়ান ড্রামা</a>
                            <a href="/search?q=হলিউড" class="bg-purple-100 hover:bg-purple-200 text-purple-800 px-4 py-2 rounded-full text-sm transition-colors">হলিউড</a>
                            <a href="/search?q=নেটফ্লিক্স" class="bg-red-100 hover:bg-red-200 text-red-800 px-4 py-2 rounded-full text-sm transition-colors">নেটফ্লিক্স</a>
                            <a href="/search?q=থ্রিলার" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-full text-sm transition-colors">থ্রিলার</a>
                            <a href="/search?q=কমেডি" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-4 py-2 rounded-full text-sm transition-colors">কমেডি</a>
                        </div>
                    </div>
                    
                    <!-- Browse Links -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/reviews" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            সব রিভিউ দেখুন
                        </a>
                        <a href="/categories" class="border border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-3 rounded-lg font-medium transition-colors">
                            ক্যাটেগরি দেখুন
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
mark {
    background-color: #fef08a;
    padding: 0 2px;
    border-radius: 2px;
}
</style>

<?php include 'includes/footer.php'; ?>