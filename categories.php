<?php
/**
 * All Categories Page - Fixed Array Handling
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config/config.php';

// Get database instance
$db = Database::getInstance();

// Initialize categories array
$categories = [];

// Get all active categories with review counts using a safer approach
try {
    // First get all categories
    $all_categories = $db->fetchAll("
        SELECT * FROM categories 
        WHERE status = 'active' 
        ORDER BY sort_order ASC, name ASC
    ");
    
    // Then get review counts for each category separately
    foreach ($all_categories as $category) {
        if (is_array($category) && isset($category['id'])) {
            try {
                $count_result = $db->fetchOne("
                    SELECT COUNT(DISTINCT r.id) as review_count
                    FROM review_categories rc
                    INNER JOIN reviews r ON rc.review_id = r.id
                    WHERE rc.category_id = ? AND r.status = 'published'
                ", [$category['id']]);
                
                $review_count = 0;
                if ($count_result && is_array($count_result) && isset($count_result['review_count'])) {
                    $review_count = (int)$count_result['review_count'];
                }
                
                $category['review_count'] = $review_count;
                $category['color'] = $category['color'] ?? '#3B82F6';
                $category['icon'] = $category['icon'] ?? '';
                $category['description'] = $category['description'] ?? '';
                $category['name_bn'] = $category['name_bn'] ?? $category['name'];
                
                $categories[] = $category;
                
            } catch (Exception $e) {
                error_log("Review count query error for category {$category['id']}: " . $e->getMessage());
                $category['review_count'] = 0;
                $category['color'] = $category['color'] ?? '#3B82F6';
                $category['icon'] = $category['icon'] ?? '';
                $category['description'] = $category['description'] ?? '';
                $category['name_bn'] = $category['name_bn'] ?? $category['name'];
                $categories[] = $category;
            }
        }
    }
    
} catch (Exception $e) {
    error_log("Categories query error: " . $e->getMessage());
    $categories = [];
}

// SEO Meta Data
$site_name = 'MSR';
try {
    $site_name = getSiteSetting('site_name', 'MSR');
} catch (Exception $e) {
    error_log("Site name fetch error: " . $e->getMessage());
}

$seo_title = 'সব ক্যাটেগরি - ' . $site_name;
$seo_description = 'বিভিন্ন ক্যাটেগরি অনুযায়ী সিনেমা ও সিরিয়ালের রিভিউ খুঁজুন। অ্যাকশন, কমেডি, ড্রামা, থ্রিলার এবং আরো অনেক ধরনের কন্টেন্ট।';
$seo_keywords = 'movie categories, ক্যাটেগরি, জনরা, অ্যাকশন, কমেডি, ড্রামা, থ্রিলার';

// Breadcrumbs
$breadcrumbs = [
    ['name' => 'হোম', 'url' => '/'],
    ['name' => 'ক্যাটেগরি', 'url' => '/categories']
];

// Include header
include 'includes/header.php';
?>

<!-- Page Header -->
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
        
        <h1 class="text-4xl lg:text-5xl font-bold mb-4">সব ক্যাটেগরি</h1>
        <p class="text-xl lg:text-2xl text-white/90 mb-6">
            আপনার পছন্দের ধরনের সিনেমা ও সিরিয়াল খুঁজে নিন
        </p>
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 inline-block">
            <span class="text-lg font-medium"><?php echo count($categories); ?>টি ক্যাটেগরি</span>
        </div>
    </div>
</section>

<!-- Categories Grid -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <?php if (!empty($categories) && is_array($categories)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php foreach ($categories as $category): ?>
                    <?php if (is_array($category) && isset($category['slug'])): ?>
                        <?php
                        // Ensure variables are safe to use with proper fallbacks
                        $categoryName = htmlspecialchars($category['name_bn'] ?? $category['name'] ?? '');
                        $categorySlug = htmlspecialchars($category['slug'] ?? '');
                        $categoryColor = htmlspecialchars($category['color'] ?? '#3B82F6');
                        $categoryIcon = htmlspecialchars($category['icon'] ?? '');
                        $categoryDescription = htmlspecialchars($category['description'] ?? '');
                        $reviewCount = (int)($category['review_count'] ?? 0);
                        ?>
                        <div class="group">
                            <a href="/category/<?php echo $categorySlug; ?>" 
                               class="block bg-white rounded-2xl p-8 text-center hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border-2 border-transparent hover:border-blue-200">
                                
                                <!-- Icon -->
                                <div class="w-20 h-20 mx-auto mb-6 rounded-full flex items-center justify-center transition-all duration-300 group-hover:scale-110" 
                                     style="background-color: <?php echo $categoryColor; ?>20;">
                                    <?php if ($categoryIcon): ?>
                                        <i class="<?php echo $categoryIcon; ?> text-3xl" style="color: <?php echo $categoryColor; ?>"></i>
                                    <?php else: ?>
                                        <svg class="w-8 h-8" style="color: <?php echo $categoryColor; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Name -->
                                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                    <?php echo $categoryName; ?>
                                </h3>
                                
                                <!-- English Name (if different) -->
                                <?php if (!empty($category['name_bn']) && $category['name'] !== $category['name_bn']): ?>
                                    <p class="text-gray-500 text-sm mb-3">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <!-- Description -->
                                <?php if ($categoryDescription): ?>
                                    <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">
                                        <?php echo $categoryDescription; ?>
                                    </p>
                                <?php endif; ?>
                                
                                <!-- Review Count -->
                                <div class="flex justify-center items-center space-x-4 pt-4 border-t border-gray-100">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold" style="color: <?php echo $categoryColor; ?>">
                                            <?php echo number_format($reviewCount); ?>
                                        </div>
                                        <div class="text-gray-500 text-sm">রিভিউ</div>
                                    </div>
                                </div>
                                
                                <!-- Hover Effect -->
                                <div class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="inline-flex items-center text-blue-600 font-medium">
                                        দেখুন
                                        <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
        <?php else: ?>
            <!-- No Categories -->
            <div class="text-center py-16">
                <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">কোনো ক্যাটেগরি পাওয়া যায়নি</h3>
                <p class="text-gray-600">এখনো কোনো ক্যাটেগরি যোগ করা হয়নি।</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Popular Categories Stats -->
<?php if (!empty($categories) && is_array($categories)): ?>
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">জনপ্রিয় ক্যাটেগরি</h2>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php 
            // Sort by review count and take top 4
            $popular_categories = $categories;
            usort($popular_categories, function($a, $b) {
                $count_a = (int)($a['review_count'] ?? 0);
                $count_b = (int)($b['review_count'] ?? 0);
                return $count_b - $count_a;
            });
            $popular_categories = array_slice($popular_categories, 0, 4);
            ?>
            
            <?php foreach ($popular_categories as $category): ?>
                <?php if (is_array($category) && isset($category['slug'])): ?>
                    <?php
                    $categoryName = htmlspecialchars($category['name_bn'] ?? $category['name'] ?? '');
                    $categorySlug = htmlspecialchars($category['slug'] ?? '');
                    $categoryColor = htmlspecialchars($category['color'] ?? '#3B82F6');
                    $categoryIcon = htmlspecialchars($category['icon'] ?? '');
                    $reviewCount = (int)($category['review_count'] ?? 0);
                    ?>
                    <div class="text-center p-6 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" 
                             style="background-color: <?php echo $categoryColor; ?>20;">
                            <?php if ($categoryIcon): ?>
                                <i class="<?php echo $categoryIcon; ?> text-2xl" style="color: <?php echo $categoryColor; ?>"></i>
                            <?php else: ?>
                                <svg class="w-6 h-6" style="color: <?php echo $categoryColor; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="font-bold text-gray-900 mb-2">
                            <a href="/category/<?php echo $categorySlug; ?>" class="hover:text-blue-600 transition-colors">
                                <?php echo $categoryName; ?>
                            </a>
                        </h3>
                        
                        <div class="text-2xl font-bold mb-1" style="color: <?php echo $categoryColor; ?>">
                            <?php echo number_format($reviewCount); ?>
                        </div>
                        <div class="text-gray-600 text-sm">রিভিউ</div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Category Stats Summary -->
<?php if (!empty($categories) && is_array($categories)): ?>
<section class="py-12 bg-blue-50">
    <div class="container mx-auto px-4">
        <div class="text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">ক্যাটেগরি পরিসংখ্যান</h3>
            
            <?php
            $total_reviews = 0;
            $max_reviews = 0;
            $most_popular = null;
            
            foreach ($categories as $category) {
                if (is_array($category)) {
                    $count = (int)($category['review_count'] ?? 0);
                    $total_reviews += $count;
                    if ($count > $max_reviews) {
                        $max_reviews = $count;
                        $most_popular = $category;
                    }
                }
            }
            
            $avg_per_category = count($categories) > 0 ? round($total_reviews / count($categories), 1) : 0;
            ?>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2"><?php echo number_format($total_reviews); ?></div>
                    <div class="text-gray-600">মোট রিভিউ</div>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600 mb-2"><?php echo number_format(count($categories)); ?></div>
                    <div class="text-gray-600">মোট ক্যাটেগরি</div>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2"><?php echo number_format($avg_per_category, 1); ?></div>
                    <div class="text-gray-600">গড় রিভিউ/ক্যাটেগরি</div>
                </div>
            </div>
            
            <?php if ($most_popular && is_array($most_popular) && $max_reviews > 0): ?>
                <div class="mt-8 p-6 bg-white rounded-xl inline-block">
                    <div class="text-sm text-gray-600 mb-1">সবচেয়ে জনপ্রিয় ক্যাটেগরি</div>
                    <div class="text-xl font-bold text-gray-900">
                        <a href="/category/<?php echo htmlspecialchars($most_popular['slug'] ?? ''); ?>" class="hover:text-blue-600 transition-colors">
                            <?php echo htmlspecialchars($most_popular['name_bn'] ?? $most_popular['name'] ?? ''); ?>
                        </a>
                    </div>
                    <div class="text-lg text-blue-600"><?php echo number_format($max_reviews); ?> রিভিউ</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>