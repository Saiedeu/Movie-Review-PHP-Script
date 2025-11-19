<?php
if (!defined('MSR_ACCESS')) {
    die('Direct access not permitted.');
}

// Check remember token if not logged in
if (!isLoggedIn()) {
    checkRememberToken();
}

// Get navigation items
$db = Database::getInstance();
$categories = $db->fetchAll("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC LIMIT 6");

// Get dynamic colors and settings
$primary_color = getSiteSetting('primary_color', '#3B82F6');
$secondary_color = getSiteSetting('secondary_color', '#10B981');
$body_color = getSiteSetting('body_color', '#F9FAFB');
$text_color = getSiteSetting('text_color', '#1F2937');
$header_color = getSiteSetting('header_color', '#FFFFFF');
$footer_color = getSiteSetting('footer_color', '#1F2937');
$custom_css = getSiteSetting('custom_css', '');

// Get site settings (for website display only)
$site_name = getSiteSetting('site_name', SITE_NAME);
$site_description = getSiteSetting('site_description', SITE_DESCRIPTION);

// Get SEO settings (for SEO purposes only)
$seo_meta_title = getSeoSetting('meta_title', $site_name); // Fallback to site name if no custom meta title
$seo_meta_description = getSeoSetting('meta_description', $site_description);
$seo_meta_keywords = getSeoSetting('meta_keywords', SITE_KEYWORDS);
$seo_og_image = getSeoSetting('og_image', '');

// Build page title (for browser tab and SEO)
$page_title = '';
if (isset($seo_title)) {
    // If specific page has its own SEO title, combine with meta title
    $page_title = $seo_title . ' - ' . $seo_meta_title;
} else {
    // Use meta title for homepage and general pages
    $page_title = $seo_meta_title;
}
?>
<!DOCTYPE html>
<html lang="bn" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(isset($seo_description) ? $seo_description : $seo_meta_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars(isset($seo_keywords) ? $seo_keywords : $seo_meta_keywords); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($site_name); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo getCurrentURL(); ?>">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(isset($seo_description) ? $seo_description : $seo_meta_description); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo getCurrentURL(); ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($site_name); ?>">
    <?php if (!empty($seo_og_image)): ?>
    <meta property="og:image" content="<?php echo UPLOADS_URL; ?>/seo/<?php echo $seo_og_image; ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <?php elseif (isset($seo_image) && !empty($seo_image)): ?>
    <meta property="og:image" content="<?php echo $seo_image; ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <?php endif; ?>
    
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars(isset($seo_description) ? $seo_description : $seo_meta_description); ?>">
    <?php if (!empty($seo_og_image)): ?>
    <meta name="twitter:image" content="<?php echo UPLOADS_URL; ?>/seo/<?php echo $seo_og_image; ?>">
    <?php elseif (isset($seo_image) && !empty($seo_image)): ?>
    <meta name="twitter:image" content="<?php echo $seo_image; ?>">
    <?php endif; ?>
    
    <!-- Verification Tags -->
    <?php
    $google_verification = getSeoSetting('google_console_verification', '');
    $bing_verification = getSeoSetting('bing_webmaster_verification', '');
    
    if (!empty($google_verification)): ?>
    <meta name="google-site-verification" content="<?php echo htmlspecialchars($google_verification); ?>">
    <?php endif; ?>
    
    <?php if (!empty($bing_verification)): ?>
    <meta name="msvalidate.01" content="<?php echo htmlspecialchars($bing_verification); ?>">
    <?php endif; ?>
    
    <!-- Favicon -->
    <?php
    $favicon = getSiteSetting('favicon', '');
    if ($favicon) {
        echo '<link rel="icon" type="image/x-icon" href="' . UPLOADS_URL . '/seo/' . $favicon . '">';
        echo '<link rel="shortcut icon" type="image/x-icon" href="' . UPLOADS_URL . '/seo/' . $favicon . '">';
        echo '<link rel="apple-touch-icon" href="' . UPLOADS_URL . '/seo/' . $favicon . '">';
    } else {
        echo '<link rel="icon" type="image/x-icon" href="' . ASSETS_URL . '/images/favicon.ico">';
    }
    ?>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link href="<?php echo ASSETS_URL; ?>/css/style.css" rel="stylesheet">
    
    <!-- Dynamic CSS Variables -->
    <style>
        :root {
            --color-primary: <?php echo $primary_color; ?>;
            --color-secondary: <?php echo $secondary_color; ?>;
            --color-body: <?php echo $body_color; ?>;
            --color-text: <?php echo $text_color; ?>;
            --color-header: <?php echo $header_color; ?>;
            --color-footer: <?php echo $footer_color; ?>;
        }
        
        /* Font Configuration */
        body {
            font-family: 'Hind Siliguri', 'Roboto', sans-serif;
            background-color: var(--color-body);
            color: var(--color-text);
        }
        
        /* Dynamic Colors */
        .dynamic-header {
            background-color: var(--color-header) !important;
        }
        
        .dynamic-footer {
            background-color: var(--color-footer) !important;
        }
        
        .btn-primary {
            background-color: var(--color-primary) !important;
            color: white !important;
        }
        
        .btn-primary:hover {
            background-color: var(--color-primary) !important;
            filter: brightness(0.9);
        }
        
        .btn-secondary {
            background-color: var(--color-secondary) !important;
            color: white !important;
        }
        
        .btn-secondary:hover {
            background-color: var(--color-secondary) !important;
            filter: brightness(0.9);
        }
        
        .text-primary {
            color: var(--color-primary) !important;
        }
        
        .text-secondary {
            color: var(--color-secondary) !important;
        }
        
        .bg-primary {
            background-color: var(--color-primary) !important;
        }
        
        .bg-secondary {
            background-color: var(--color-secondary) !important;
        }
        
        .border-primary {
            border-color: var(--color-primary) !important;
        }
        
        .focus\:ring-primary:focus {
            --tw-ring-color: var(--color-primary) !important;
        }
        
        .hover\:text-primary:hover {
            color: var(--color-primary) !important;
        }
        
        .hover\:bg-primary:hover {
            background-color: var(--color-primary) !important;
        }
        
        /* Navigation Styles */
        .nav-link {
            transition: color 0.2s ease;
        }
        
        .nav-link:hover {
            color: var(--color-primary) !important;
        }
        
        /* Button Styles */
        .btn-dynamic {
            background-color: var(--color-primary);
            color: white;
            transition: all 0.2s ease;
        }
        
        .btn-dynamic:hover {
            background-color: var(--color-primary);
            filter: brightness(0.9);
            transform: translateY(-1px);
        }
        
        /* Search Styles */
        .search-btn {
            background-color: var(--color-primary);
        }
        
        .search-btn:hover {
            background-color: var(--color-primary);
            filter: brightness(0.9);
        }
        
        /* Link Styles */
        a:not(.no-dynamic) {
            transition: color 0.2s ease;
        }
        
        a:not(.no-dynamic):hover {
            color: var(--color-primary);
        }
        
        /* Category Dropdown */
        .category-dropdown-item:hover {
            background-color: var(--color-primary) !important;
            color: white !important;
        }
        
        /* Mobile Menu */
        @media (max-width: 1024px) {
            .mobile-menu {
                background-color: var(--color-header);
            }
        }
        
        <?php if (!empty($custom_css)): ?>
        /* Custom CSS from Admin Settings */
        <?php echo $custom_css; ?>
        <?php endif; ?>
    </style>
    
    <!-- Tailwind Config with Dynamic Colors -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'bengali': ['Hind Siliguri', 'sans-serif'],
                        'english': ['Roboto', 'sans-serif'],
                    },
                    colors: {
                        'primary': '<?php echo $primary_color; ?>',
                        'secondary': '<?php echo $secondary_color; ?>',
                        'body': '<?php echo $body_color; ?>',
                        'text': '<?php echo $text_color; ?>',
                        'header': '<?php echo $header_color; ?>',
                        'footer': '<?php echo $footer_color; ?>',
                    }
                }
            }
        }
    </script>
    
    <?php
    // Generate structured data
    if (isset($review)) {
        generateReviewSchema($review);
    } else {
        generateWebsiteSchema();
    }
    
    // Generate breadcrumb schema if breadcrumbs exist
    if (isset($breadcrumbs)) {
        generateBreadcrumbSchema($breadcrumbs);
    }
    
    // Generate analytics code
    $google_analytics = getSeoSetting('google_analytics', '');
    if (!empty($google_analytics)):
    ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $google_analytics; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo $google_analytics; ?>');
    </script>
    <?php endif; ?>
    
    <?php
    $facebook_pixel = getSeoSetting('facebook_pixel', '');
    if (!empty($facebook_pixel)):
    ?>
    <!-- Facebook Pixel -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo $facebook_pixel; ?>');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo $facebook_pixel; ?>&ev=PageView&noscript=1"/></noscript>
    <?php endif; ?>
</head>
<body class="font-bengali" style="background-color: <?php echo $body_color; ?>; color: <?php echo $text_color; ?>;">
    
    <!-- Navigation -->
    <nav class="dynamic-header shadow-lg sticky top-0 z-50" style="background-color: <?php echo $header_color; ?>;">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <a href="/" class="flex items-center space-x-3 no-dynamic">
                        <?php
                        $logo = getSiteSetting('site_logo', '');
                        if ($logo) {
                            echo '<img src="' . UPLOADS_URL . '/logo/' . $logo . '" alt="' . htmlspecialchars($site_name) . '" class="h-10 w-auto">';
                        } else {
                            echo '<div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-xl" style="background-color: ' . $primary_color . ';">MSR</div>';
                        }
                        ?>
                        <span class="text-xl font-bold hidden sm:block" style="color: <?php echo $text_color; ?>;">
                            <?php echo htmlspecialchars($site_name); ?>
                        </span>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="/" class="nav-link font-medium" style="color: <?php echo $text_color; ?>;">হোম</a>
                    <a href="/reviews" class="nav-link font-medium" style="color: <?php echo $text_color; ?>;">সব রিভিউ</a>
                    
                    <!-- Categories Dropdown -->
                    <div class="relative group">
                        <button class="nav-link font-medium flex items-center" style="color: <?php echo $text_color; ?>;">
                            ক্যাটাগরি
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute top-full left-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <?php foreach ($categories as $category): ?>
                                <a href="/category/<?php echo $category['slug']; ?>" class="category-dropdown-item block px-4 py-3 text-gray-700 transition-colors border-b border-gray-100 last:border-b-0">
                                    <?php echo $category['name_bn'] ?: $category['name']; ?>
                                </a>
                            <?php endforeach; ?>
                            <a href="/categories" class="block px-4 py-3 font-medium transition-colors" style="color: <?php echo $primary_color; ?>;">
                                সব ক্যাটাগরি দেখুন →
                            </a>
                        </div>
                    </div>
                    
                    <a href="/submit-review" class="btn-dynamic px-4 py-2 rounded-lg font-medium" style="background-color: <?php echo $primary_color; ?>; color: white;">সাবমিট</a>
                    <a href="/contact" class="nav-link font-medium" style="color: <?php echo $text_color; ?>;">যোগাযোগ</a>
                </div>
                
                <!-- Search & Mobile Menu Button -->
                <div class="flex items-center space-x-4">
                    <button onclick="toggleSearch()" class="transition-colors" style="color: <?php echo $text_color; ?>;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    
                    <!-- Mobile Menu Button -->
                    <button onclick="toggleMobileMenu()" class="lg:hidden transition-colors" style="color: <?php echo $text_color; ?>;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="mobile-menu lg:hidden hidden border-t border-gray-200" style="background-color: <?php echo $header_color; ?>;">
                <div class="py-4 space-y-4">
                    <a href="/" class="block font-medium transition-colors" style="color: <?php echo $text_color; ?>;">হোম</a>
                    <a href="/reviews" class="block font-medium transition-colors" style="color: <?php echo $text_color; ?>;">সব রিভিউ</a>
                    
                    <!-- Mobile Categories -->
                    <div>
                        <button onclick="toggleMobileCategories()" class="w-full text-left font-medium transition-colors flex items-center justify-between" style="color: <?php echo $text_color; ?>;">
                            ক্যাটাগরি
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="mobile-categories" class="hidden mt-2 ml-4 space-y-2">
                            <?php foreach ($categories as $category): ?>
                                <a href="/category/<?php echo $category['slug']; ?>" class="block transition-colors" style="color: <?php echo $text_color; ?>; opacity: 0.8;">
                                    <?php echo $category['name_bn'] ?: $category['name']; ?>
                                </a>
                            <?php endforeach; ?>
                            <a href="/categories" class="block font-medium transition-colors" style="color: <?php echo $primary_color; ?>;">
                                সব ক্যাটাগরি দেখুন →
                            </a>
                        </div>
                    </div>
                    
                    <a href="/submit-review" class="block text-center px-4 py-2 rounded-lg font-medium transition-colors" style="background-color: <?php echo $primary_color; ?>; color: white;">সাবমিট</a>
                    <a href="/contact" class="block font-medium transition-colors" style="color: <?php echo $text_color; ?>;">যোগাযোগ</a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Search Overlay -->
    <div id="search-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl p-8 w-full max-w-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">খুঁজুন</h3>
                    <button onclick="toggleSearch()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form action="/search" method="GET" class="mb-6">
                    <div class="relative">
                        <input type="text" 
                               name="q" 
                               id="search-input"
                               placeholder="সিনেমা বা সিরিয়ালের নাম লিখুন..." 
                               class="w-full px-6 py-4 text-lg border-2 rounded-xl focus:outline-none transition-colors"
                               style="border-color: <?php echo $primary_color; ?>;"
                               autocomplete="off">
                        <button type="submit" class="search-btn absolute right-2 top-2 text-white p-2 rounded-lg transition-colors" style="background-color: <?php echo $primary_color; ?>;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
                
                <!-- Popular searches -->
                <div>
                    <h4 class="text-sm font-medium text-gray-600 mb-3">জনপ্রিয় অনুসন্ধান:</h4>
                    <div class="flex flex-wrap gap-2">
                        <a href="/search?q=বাংলা সিনেমা" class="bg-gray-100 hover:text-white text-gray-700 px-3 py-1 rounded-full text-sm transition-colors category-dropdown-item">বাংলা সিনেমা</a>
                        <a href="/search?q=কোরিয়ান ড্রামা" class="bg-gray-100 hover:text-white text-gray-700 px-3 py-1 rounded-full text-sm transition-colors category-dropdown-item">কোরিয়ান ড্রামা</a>
                        <a href="/search?q=হলিউড" class="bg-gray-100 hover:text-white text-gray-700 px-3 py-1 rounded-full text-sm transition-colors category-dropdown-item">হলিউড</a>
                        <a href="/search?q=নেটফ্লিক্স" class="bg-gray-100 hover:text-white text-gray-700 px-3 py-1 rounded-full text-sm transition-colors category-dropdown-item">নেটফ্লিক্স</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <main>

<!-- JavaScript for Mobile Menu and Search -->
<script>
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenu.classList.toggle('hidden');
}

function toggleMobileCategories() {
    const mobileCategories = document.getElementById('mobile-categories');
    mobileCategories.classList.toggle('hidden');
}

function toggleSearch() {
    const searchOverlay = document.getElementById('search-overlay');
    const searchInput = document.getElementById('search-input');
    
    searchOverlay.classList.toggle('hidden');
    
    if (!searchOverlay.classList.contains('hidden')) {
        setTimeout(() => {
            searchInput.focus();
        }, 100);
    }
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileButton = event.target.closest('button[onclick="toggleMobileMenu()"]');
    
    if (!mobileButton && !mobileMenu.contains(event.target)) {
        mobileMenu.classList.add('hidden');
    }
});

// Close search when pressing Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const searchOverlay = document.getElementById('search-overlay');
        searchOverlay.classList.add('hidden');
    }
});
</script>