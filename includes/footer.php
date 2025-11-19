<?php
// Get dynamic colors and settings
$footer_color = getSiteSetting('footer_color', '#1F2937');
$primary_color = getSiteSetting('primary_color', '#3B82F6');
$footer_text = getSiteSetting('footer_text', '© ২০২৫ MSR - Movie & Series Review. সকল অধিকার সংরক্ষিত।');
?>
</main>
    
    <!-- Footer -->
    <footer class="dynamic-footer text-white" style="background-color: <?php echo $footer_color; ?>;">
        <div class="container mx-auto px-4 py-12">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- About Section -->
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <?php
                        $logo = getSiteSetting('site_logo', '');
                        if ($logo) {
                            echo '<img src="' . UPLOADS_URL . '/logo/' . $logo . '" alt="' . getSiteSetting('site_name', SITE_NAME) . '" class="h-8 w-auto">';
                        } else {
                            echo '<div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold" style="background-color: ' . $primary_color . ';">MSR</div>';
                        }
                        ?>
                        <span class="text-lg font-bold"><?php echo getSiteSetting('site_name', SITE_NAME); ?></span>
                    </div>
                    <p class="text-gray-300 leading-relaxed mb-4">
                        <?php echo getSiteSetting('site_description', 'বাংলা ভাষায় সেরা সিনেমা ও সিরিয়াল রিভিউ। আমাদের লক্ষ্য দর্শকদের সঠিক গাইড করা।'); ?>
                    </p>
                    <div class="flex space-x-4">
                        <!-- Social Media Links -->
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/share/1GVQ1UzbJU/?mibextid=wwXIfr" target="_blank" class="text-gray-400 hover:text-white transition-colors" style="--hover-color: <?php echo $primary_color; ?>;" onmouseover="this.style.color='<?php echo $primary_color; ?>';" onmouseout="this.style.color='';">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <!-- Instagram -->
                        <a href="https://Instagram.com/cinereviewbd" target="_blank" class="text-gray-400 hover:text-white transition-colors" style="--hover-color: <?php echo $primary_color; ?>;" onmouseover="this.style.color='<?php echo $primary_color; ?>';" onmouseout="this.style.color='';">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        <!-- Twitter/X -->
                        <a href="https://x.com/cinereviewbd" target="_blank" class="text-gray-400 hover:text-white transition-colors" style="--hover-color: <?php echo $primary_color; ?>;" onmouseover="this.style.color='<?php echo $primary_color; ?>';" onmouseout="this.style.color='';">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <!-- YouTube -->
                        <a href="https://www.youtube.com/@cinereviewbd" target="_blank" class="text-gray-400 hover:text-white transition-colors" style="--hover-color: <?php echo $primary_color; ?>;" onmouseover="this.style.color='<?php echo $primary_color; ?>';" onmouseout="this.style.color='';">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <!-- Categories -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">ক্যাটাগরি</h3>
                    <ul class="space-y-3">
                        <?php foreach (array_slice($categories, 0, 6) as $category): ?>
                            <li>
                                <a href="/category/<?php echo $category['slug']; ?>" class="text-gray-300 hover:text-white transition-colors" style="--hover-color: <?php echo $primary_color; ?>;" onmouseover="this.style.color='<?php echo $primary_color; ?>';" onmouseout="this.style.color='';">
                                    <?php echo $category['name_bn'] ?: $category['name']; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">দ্রুত লিংক</h3>
                    <ul class="space-y-3">
                        <li><a href="/reviews" class="text-gray-300 hover:text-white transition-colors" style="--hover-color: <?php echo $primary_color; ?>;" onmouseover="this.style.color='<?php echo $primary_color; ?>';" onmouseout="this.style.color='';">সব রিভিউ</a></li>
                        <li><a href="/reviews?featured=1" class="text-gray-300 hover:text-white transition-colors" style="--hover-color: <?php echo $primary_color; ?>;" onmouseover="this.style.color='<?php echo $primary_color; ?>';" onmouseout="this.style.color='';">জনপ্রিয় রিভিউ</a></li>
                        <li><a href="/submit-review" class="text-gray-300 hover:text-white transition-colors" style="--hover-color: <?php echo $primary_color; ?>;" onmouseover="this.style.color='<?php echo $primary_color; ?>';" onmouseout="this.style.color='';">রিভিউ সাবমিট</a></li>
                        <li><a href="/about" class="text-gray-300 hover:text-white transition-colors" style="--hover-color: <?php echo $primary_color; ?>;" onmouseover="this.style.color='<?php echo $primary_color; ?>';" onmouseout="this.style.color='';">আমাদের সম্পর্কে</a></li>
                        <li><a href="/contact" class="text-gray-300 hover:text-white transition-colors" style="--hover-color: <?php echo $primary_color; ?>;" onmouseover="this.style.color='<?php echo $primary_color; ?>';" onmouseout="this.style.color='';">যোগাযোগ</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">যোগাযোগ</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center space-x-3">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-gray-300">info.saidur_bd@aol.com</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="text-gray-300">+974-66489944</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-gray-300">বিয়ানীবাজার, সিলেট</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="border-t border-gray-800 mt-12 pt-8">
                <!-- Legal Pages Links -->
                <div class="text-center mb-6">
                    <div class="flex flex-wrap justify-center items-center space-x-1 text-sm">
                        <a href="/privacy.php" class="text-gray-400 hover:text-white transition-colors" style="--hover-color: <?php echo $primary_color; ?>;" onmouseover="this.style.color='<?php echo $primary_color; ?>';" onmouseout="this.style.color='';">
                            গোপনীয়তা নীতি
                        </a>
                        <span class="text-gray-600">|</span>
                        <a href="/terms.php" class="text-gray-400 hover:text-white transition-colors" style="--hover-color: <?php echo $primary_color; ?>;" onmouseover="this.style.color='<?php echo $primary_color; ?>';" onmouseout="this.style.color='';">
                            ব্যবহারের শর্তাবলী
                        </a>
                        <span class="text-gray-600">|</span>
                        <a href="/dmca-removal.php" class="text-gray-400 hover:text-white transition-colors" style="--hover-color: <?php echo $primary_color; ?>;" onmouseover="this.style.color='<?php echo $primary_color; ?>';" onmouseout="this.style.color='';">
                            DMCA অপসারণ
                        </a>
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm mb-4 md:mb-0">
                        <?php echo $footer_text; ?>
                    </p>
                    <p class="text-gray-400 text-sm">
                        Developed by 
                        <a href="https://wa.me/+97466489944?text=Hello%20Massum%2C%0ACine%20Review%20%E0%A6%93%E0%A6%AF%E0%A6%BC%E0%A7%87%E0%A6%AC%E0%A6%B8%E0%A6%BE%E0%A6%87%E0%A6%9F%20%E0%A6%B8%E0%A6%82%E0%A6%AA%E0%A6%B0%E0%A7%8D%E0%A6%95%E0%A7%87%E2%80%A6" class="transition-colors" style="color: <?php echo $primary_color; ?>;">
                            Massum Ahmed
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Dynamic CSS for Buttons and Interactive Elements -->
    <style>
        /* Override any remaining static colors */
        .btn-primary,
        button[type="submit"],
        .bg-blue-600 {
            background-color: <?php echo $primary_color; ?> !important;
        }
        
        .btn-primary:hover,
        button[type="submit"]:hover,
        .bg-blue-600:hover {
            background-color: <?php echo $primary_color; ?> !important;
            filter: brightness(0.9);
        }
        
        .text-blue-600 {
            color: <?php echo $primary_color; ?> !important;
        }
        
        .border-blue-500 {
            border-color: <?php echo $primary_color; ?> !important;
        }
        
        /* Form focus states */
        input:focus,
        textarea:focus,
        select:focus {
            border-color: <?php echo $primary_color; ?> !important;
            box-shadow: 0 0 0 3px rgba(<?php echo hexToRgb($primary_color); ?>, 0.1) !important;
        }
        
        /* Links in content */
        .content a,
        article a {
            color: <?php echo $primary_color; ?>;
        }
        
        .content a:hover,
        article a:hover {
            color: <?php echo $primary_color; ?>;
            filter: brightness(0.8);
        }
    </style>
    
    <!-- JavaScript -->
    <script src="<?php echo ASSETS_URL; ?>/js/script.js"></script>
    
    <script>
        // Search functionality
        function toggleSearch() {
            const overlay = document.getElementById('search-overlay');
            const input = document.getElementById('search-input');
            
            if (overlay.classList.contains('hidden')) {
                overlay.classList.remove('hidden');
                setTimeout(() => input.focus(), 100);
            } else {
                overlay.classList.add('hidden');
            }
        }
        
        // Mobile menu functionality
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
        
        function toggleMobileCategories() {
            const categories = document.getElementById('mobile-categories');
            categories.classList.toggle('hidden');
        }
        
        // Close search on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const overlay = document.getElementById('search-overlay');
                if (!overlay.classList.contains('hidden')) {
                    toggleSearch();
                }
            }
        });
        
        // Close search when clicking outside
        document.getElementById('search-overlay').addEventListener('click', function(e) {
            if (e.target === this) {
                toggleSearch();
            }
        });
        
        // Dynamic color helper function
        function hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? 
                parseInt(result[1], 16) + ',' + parseInt(result[2], 16) + ',' + parseInt(result[3], 16)
                : null;
        }
    </script>
</body>
</html>

<?php
// Helper function for hex to RGB conversion (for PHP use)
function hexToRgb($hex) {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return "$r, $g, $b";
}
?>