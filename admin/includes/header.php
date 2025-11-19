<?php
if (!defined('MSR_ACCESS')) {
    die('Direct access not permitted.');
}

// Check authentication
requireAuth();

// Get current admin
$current_admin = getCurrentAdmin();
?>
<!DOCTYPE html>
<html lang="bn" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অ্যাডমিন প্যানেল - <?php echo getSiteSetting('site_name', SITE_NAME); ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/<?php echo TINYMCE_API_KEY; ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <!-- Custom CSS -->
    <link href="<?php echo ASSETS_URL; ?>/css/admin.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Hind Siliguri', 'Roboto', sans-serif;
        }
        
        /* Ensure proper scrolling */
        .admin-scrollable {
            height: calc(100vh - 73px); /* Subtract header height */
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Custom scrollbar */
        .admin-scrollable::-webkit-scrollbar {
            width: 6px;
        }
        
        .admin-scrollable::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .admin-scrollable::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .admin-scrollable::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-gray-100">
    
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-900 text-white flex-shrink-0">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-800">
                <div class="flex items-center space-x-3">
                    <?php
                    $logo = getSiteSetting('site_logo', '');
                    if ($logo) {
                        echo '<img src="' . UPLOADS_URL . '/logo/' . $logo . '" alt="' . getSiteSetting('site_name', SITE_NAME) . '" class="h-8 w-auto">';
                    } else {
                        echo '<div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">MSR</div>';
                    }
                    ?>
                    <span class="text-lg font-bold">অ্যাডমিন</span>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-6 h-full overflow-y-auto">
                <div class="px-6 mb-6">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">মূল মেনু</h3>
                </div>
                
                <ul class="space-y-1 pb-20">
                    <li>
                        <a href="/admin/index.php" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gray-800 transition-colors <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'bg-gray-800 text-white' : ''; ?>">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z"></path>
                            </svg>
                            ড্যাশবোর্ড
                        </a>
                    </li>
                    
                    <!-- Reviews -->
                    <li>
                        <div class="px-6 py-2">
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">রিভিউ ব্যবস্থাপনা</h4>
                        </div>
                    </li>
                    <li>
                        <a href="/admin/reviews/index.php" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gray-800 transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'reviews/') !== false ? 'bg-gray-800 text-white' : ''; ?>">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            সব রিভিউ
                        </a>
                    </li>
                    <li>
                        <a href="/admin/reviews/add.php" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            নতুন রিভিউ
                        </a>
                    </li>
                    <li>
                        <a href="/admin/reviews/pending.php" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            অপেক্ষমাণ রিভিউ
                            <?php
                            try {
                                $pending_count = $db->count("SELECT COUNT(*) FROM reviews WHERE status = 'pending'");
                                if ($pending_count > 0) {
                                    echo '<span class="ml-auto bg-yellow-600 text-white text-xs px-2 py-1 rounded-full">' . $pending_count . '</span>';
                                }
                            } catch (Exception $e) {
                                // Handle gracefully if table doesn't exist
                            }
                            ?>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/comments/index.php" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gray-800 transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'comments/') !== false ? 'bg-gray-800 text-white' : ''; ?>">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            অপেক্ষমাণ মন্তব্য
                            <?php
                            try {
                                $pending_comments_count = $db->count("SELECT COUNT(*) FROM comments WHERE status = 'pending'");
                                if ($pending_comments_count > 0) {
                                    echo '<span class="ml-auto bg-orange-600 text-white text-xs px-2 py-1 rounded-full">' . $pending_comments_count . '</span>';
                                }
                            } catch (Exception $e) {
                                // Handle gracefully if comments table doesn't exist yet
                            }
                            ?>
                        </a>
                    </li>
                    
                    <!-- Categories -->
                    <li>
                        <div class="px-6 py-2 mt-4">
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">ক্যাটেগরি</h4>
                        </div>
                    </li>
                    <li>
                        <a href="/admin/categories/index.php" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gray-800 transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'categories/') !== false ? 'bg-gray-800 text-white' : ''; ?>">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            ক্যাটেগরি ব্যবস্থাপনা
                        </a>
                    </li>
                    
                    <!-- Users -->
                    <li>
                        <div class="px-6 py-2 mt-4">
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">ব্যবহারকারী</h4>
                        </div>
                    </li>
                    <li>
                        <a href="/admin/users/index.php" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gray-800 transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'users/') !== false ? 'bg-gray-800 text-white' : ''; ?>">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            ব্যবহারকারী ব্যবস্থাপনা
                        </a>
                    </li>
                    
                    <!-- Settings -->
                    <li>
                        <div class="px-6 py-2 mt-4">
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">সেটিংস</h4>
                        </div>
                    </li>
                    <li>
                        <a href="/admin/settings/index.php" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gray-800 transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'settings/') !== false && basename($_SERVER['PHP_SELF']) !== 'seo.php' ? 'bg-gray-800 text-white' : ''; ?>">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            সাইট সেটিংস
                        </a>
                    </li>
                    <li>
                        <a href="/admin/settings/seo.php" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gray-800 transition-colors <?php echo basename($_SERVER['PHP_SELF']) === 'seo.php' ? 'bg-gray-800 text-white' : ''; ?>">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            SEO সেটিংস
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-xl font-semibold text-gray-800">
                            <?php echo getSiteSetting('site_name', SITE_NAME); ?> - অ্যাডমিন প্যানেল
                        </h2>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Visit Site -->
                        <a href="../" target="_blank" class="text-gray-600 hover:text-gray-800 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                        
                        <!-- User Menu -->
                        <div class="relative group">
                            <button class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 transition-colors">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                    <?php echo strtoupper(substr($current_admin['username'], 0, 1)); ?>
                                </div>
                                <span class="font-medium"><?php echo htmlspecialchars($current_admin['username']); ?></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Dropdown -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-2">
                                    <div class="px-4 py-2 text-sm text-gray-600 border-b border-gray-100">
                                        <div class="font-medium"><?php echo htmlspecialchars($current_admin['username']); ?></div>
                                        <div class="text-xs"><?php echo htmlspecialchars($current_admin['email']); ?></div>
                                    </div>
                                    <a href="/admin/settings/index.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        সেটিংস
                                    </a>
                                    <a href="/admin/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        লগআউট
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Scrollable Content Area -->
            <div class="flex-1 admin-scrollable">
                <!-- Page content will be inserted here -->