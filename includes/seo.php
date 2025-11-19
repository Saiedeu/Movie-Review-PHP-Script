<?php
/**
 * SEO Functions - No duplicate functions
 */

if (!defined('MSR_ACCESS')) {
    die('Direct access not permitted.');
}

/**
 * Generate meta tags
 */
function generateMetaTags($title = '', $description = '', $keywords = '', $image = '', $url = '') {
    $site_name = getSiteSetting('site_name', SITE_NAME);
    $default_description = getSeoSetting('meta_description', SITE_DESCRIPTION);
    $default_keywords = getSeoSetting('meta_keywords', SITE_KEYWORDS);
    $default_image = getSeoSetting('og_image', SITE_URL . '/assets/images/og-default.jpg');
    
    // For SEO meta title, use the custom meta title if set, otherwise use site name
    $seo_meta_title = getSeoSetting('meta_title', $site_name);
    
    $title = $title ?: $seo_meta_title;
    $description = $description ?: $default_description;
    $keywords = $keywords ?: $default_keywords;
    $image = $image ?: $default_image;
    $url = $url ?: getCurrentURL();
    
    echo '<title>' . htmlspecialchars($title) . '</title>' . "\n";
    echo '<meta name="description" content="' . htmlspecialchars($description) . '">' . "\n";
    echo '<meta name="keywords" content="' . htmlspecialchars($keywords) . '">' . "\n";
    echo '<link rel="canonical" href="' . htmlspecialchars($url) . '">' . "\n";
    
    // Open Graph tags
    echo '<meta property="og:title" content="' . htmlspecialchars($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . htmlspecialchars($description) . '">' . "\n";
    echo '<meta property="og:image" content="' . htmlspecialchars($image) . '">' . "\n";
    echo '<meta property="og:url" content="' . htmlspecialchars($url) . '">' . "\n";
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:site_name" content="' . htmlspecialchars($site_name) . '">' . "\n";
    
    // Twitter Card tags
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . htmlspecialchars($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . htmlspecialchars($description) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . htmlspecialchars($image) . '">' . "\n";
}

/**
 * Generate JSON-LD schema for website
 */
function generateWebsiteSchema() {
    $site_name = getSiteSetting('site_name', SITE_NAME);
    $site_description = getSeoSetting('meta_description', SITE_DESCRIPTION);
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $site_name,
        'description' => $site_description,
        'url' => SITE_URL,
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => SITE_URL . '/search?q={search_term_string}'
            ],
            'query-input' => 'required name=search_term_string'
        ]
    ];
    
    echo '<script type="application/ld+json">' . "\n";
    echo json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    echo '</script>' . "\n";
}

/**
 * Generate JSON-LD schema for review
 */
function generateReviewSchema($review) {
    $site_name = getSiteSetting('site_name', SITE_NAME);
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Review',
        'itemReviewed' => [
            '@type' => $review['type'] === 'movie' ? 'Movie' : 'TVSeries',
            'name' => $review['title'],
            'image' => $review['poster_image'] ? UPLOADS_URL . '/reviews/' . $review['poster_image'] : '',
            'dateCreated' => $review['year'],
            'genre' => explode(',', $review['genres'] ?? ''),
            'director' => $review['director'] ?? '',
            'actor' => explode(',', $review['cast'] ?? '')
        ],
        'reviewRating' => [
            '@type' => 'Rating',
            'ratingValue' => $review['rating'],
            'bestRating' => '5',
            'worstRating' => '1'
        ],
        'author' => [
            '@type' => 'Person',
            'name' => $review['reviewer_name'] ?? 'MSR Team'
        ],
        'datePublished' => date('c', strtotime($review['created_at'])),
        'reviewBody' => strip_tags($review['content']),
        'publisher' => [
            '@type' => 'Organization',
            'name' => $site_name,
            'url' => SITE_URL
        ]
    ];
    
    echo '<script type="application/ld+json">' . "\n";
    echo json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    echo '</script>' . "\n";
}

/**
 * Generate breadcrumb schema
 */
function generateBreadcrumbSchema($breadcrumbs) {
    $itemListElement = [];
    
    foreach ($breadcrumbs as $index => $breadcrumb) {
        $itemListElement[] = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $breadcrumb['name'],
            'item' => $breadcrumb['url']
        ];
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $itemListElement
    ];
    
    echo '<script type="application/ld+json">' . "\n";
    echo json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    echo '</script>' . "\n";
}

/**
 * Generate analytics code
 */
function generateAnalyticsCode() {
    $google_analytics = getSeoSetting('google_analytics', '');
    $facebook_pixel = getSeoSetting('facebook_pixel', '');
    
    // Google Analytics
    if ($google_analytics) {
        echo "<!-- Google Analytics -->\n";
        echo "<script async src='https://www.googletagmanager.com/gtag/js?id={$google_analytics}'></script>\n";
        echo "<script>\n";
        echo "window.dataLayer = window.dataLayer || [];\n";
        echo "function gtag(){dataLayer.push(arguments);}\n";
        echo "gtag('js', new Date());\n";
        echo "gtag('config', '{$google_analytics}');\n";
        echo "</script>\n";
    }
    
    // Facebook Pixel
    if ($facebook_pixel) {
        echo "<!-- Facebook Pixel -->\n";
        echo "<script>\n";
        echo "!function(f,b,e,v,n,t,s)\n";
        echo "{if(f.fbq)return;n=f.fbq=function(){n.callMethod?\n";
        echo "n.callMethod.apply(n,arguments):n.queue.push(arguments)};\n";
        echo "if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';\n";
        echo "n.queue=[];t=b.createElement(e);t.async=!0;\n";
        echo "t.src=v;s=b.getElementsByTagName(e)[0];\n";
        echo "s.parentNode.insertBefore(t,s)}(window, document,'script',\n";
        echo "'https://connect.facebook.net/en_US/fbevents.js');\n";
        echo "fbq('init', '{$facebook_pixel}');\n";
        echo "fbq('track', 'PageView');\n";
        echo "</script>\n";
        echo "<noscript><img height='1' width='1' style='display:none' src='https://www.facebook.com/tr?id={$facebook_pixel}&ev=PageView&noscript=1'/></noscript>\n";
    }
}

/**
 * Generate verification tags
 */
function generateVerificationTags() {
    $google_console = getSeoSetting('google_console_verification', '');
    $bing_webmaster = getSeoSetting('bing_webmaster_verification', '');
    
    if ($google_console) {
        echo '<meta name="google-site-verification" content="' . htmlspecialchars($google_console) . '">' . "\n";
    }
    
    if ($bing_webmaster) {
        echo '<meta name="msvalidate.01" content="' . htmlspecialchars($bing_webmaster) . '">' . "\n";
    }
}

/**
 * Generate page-specific SEO meta tags
 */
function generatePageSEO($page_title = '', $page_description = '', $page_keywords = '', $page_image = '') {
    $site_name = getSiteSetting('site_name', SITE_NAME);
    $seo_meta_title = getSeoSetting('meta_title', $site_name);
    
    // Build the complete title
    $complete_title = '';
    if ($page_title) {
        $complete_title = $page_title . ' - ' . $seo_meta_title;
    } else {
        $complete_title = $seo_meta_title;
    }
    
    return [
        'title' => $complete_title,
        'description' => $page_description ?: getSeoSetting('meta_description', SITE_DESCRIPTION),
        'keywords' => $page_keywords ?: getSeoSetting('meta_keywords', SITE_KEYWORDS),
        'image' => $page_image ?: getSeoSetting('og_image', ''),
        'site_name' => $site_name,
        'meta_title' => $seo_meta_title
    ];
}
?>