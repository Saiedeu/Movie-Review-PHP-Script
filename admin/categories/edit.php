<?php
/**
 * Edit Category
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../../config/config.php';

// Require authentication
requireAuth();

// Get database instance
$db = Database::getInstance();

// Get category ID
$id = intval($_GET['id'] ?? 0);

// Get category data
$category = $db->fetchOne("SELECT * FROM categories WHERE id = ?", [$id]);

if (!$category) {
    header('Location: index.php?error=category_not_found');
    exit;
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'নিরাপত্তা টোকেন যাচাই করা যায়নি।';
    } else {
        // Get form data
        $name = sanitize($_POST['name'] ?? '');
        $name_bn = sanitize($_POST['name_bn'] ?? '');
        $slug = sanitize($_POST['slug'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $icon = sanitize($_POST['icon'] ?? '');
        $color = sanitize($_POST['color'] ?? '#3B82F6');
        $sort_order = intval($_POST['sort_order'] ?? 0);
        $status = sanitize($_POST['status'] ?? 'active');
        
        // Validate required fields
        if (empty($name)) {
            $error = 'ক্যাটেগরি নাম আবশ্যক।';
        } elseif (empty($slug)) {
            $error = 'স্লাগ আবশ্যক।';
        } else {
            // Check if slug already exists (excluding current category)
            $existing = $db->fetchOne("SELECT id FROM categories WHERE slug = ? AND id != ?", [$slug, $id]);
            if ($existing) {
                $error = 'এই স্লাগ ইতিমধ্যে ব্যবহৃত হয়েছে।';
            } else {
                // Update category
                $result = $db->query("
                    UPDATE categories 
                    SET name = ?, name_bn = ?, slug = ?, description = ?, icon = ?, color = ?, sort_order = ?, status = ?, updated_at = NOW()
                    WHERE id = ?
                ", [$name, $name_bn, $slug, $description, $icon, $color, $sort_order, $status, $id]);
                
                if ($result) {
                    $success = 'ক্যাটেগরি সফলভাবে আপডেট করা হয়েছে।';
                    // Refresh category data
                    $category = $db->fetchOne("SELECT * FROM categories WHERE id = ?", [$id]);
                } else {
                    $error = 'ক্যাটেগরি আপডেট করতে সমস্যা হয়েছে।';
                }
            }
        }
    }
}

// Include admin header
include '../includes/header.php';
?>

<div class="flex-1 bg-gray-100">
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="index.php" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">ক্যাটেগরি সম্পাদনা</h1>
                    <p class="text-gray-600">"<?php echo htmlspecialchars($category['name']); ?>" সম্পাদনা করুন</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <!-- Messages -->
        <?php if ($error): ?>
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <form method="POST" class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                ক্যাটেগরি নাম (ইংরেজি) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo htmlspecialchars($category['name']); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Action, Comedy, Drama"
                                   required
                                   onkeyup="generateSlug(this.value, 'slug')">
                        </div>
                        
                        <!-- Name Bengali -->
                        <div>
                            <label for="name_bn" class="block text-sm font-medium text-gray-700 mb-2">
                                ক্যাটেগরি নাম (বাংলা)
                            </label>
                            <input type="text" 
                                   id="name_bn" 
                                   name="name_bn" 
                                   value="<?php echo htmlspecialchars($category['name_bn']); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="অ্যাকশন, কমেডি, ড্রামা">
                        </div>
                        
                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                স্লাগ <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="slug" 
                                   name="slug" 
                                   value="<?php echo htmlspecialchars($category['slug']); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="action, comedy, drama"
                                   required>
                            <p class="text-sm text-gray-500 mt-1">URL-এ ব্যবহৃত হবে (শুধু ইংরেজি অক্ষর, সংখ্যা এবং হাইফেন)</p>
                        </div>
                        
                        <!-- Sort Order -->
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                                সাজানোর ক্রম
                            </label>
                            <input type="number" 
                                   id="sort_order" 
                                   name="sort_order" 
                                   value="<?php echo htmlspecialchars($category['sort_order']); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   min="0">
                            <p class="text-sm text-gray-500 mt-1">ছোট সংখ্যা আগে দেখানো হবে</p>
                        </div>
                        
                        <!-- Icon -->
                        <div>
                            <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                                আইকন (FontAwesome ক্লাস)
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       id="icon" 
                                       name="icon" 
                                       value="<?php echo htmlspecialchars($category['icon']); ?>"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="fas fa-film, fas fa-heart, fas fa-laugh">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center" 
                                     style="background-color: <?php echo $category['color']; ?>; color: white;" 
                                     id="icon-preview">
                                    <?php if ($category['icon']): ?>
                                        <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-folder"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                <a href="https://fontawesome.com/icons" target="_blank" class="text-blue-600 hover:underline">FontAwesome</a> 
                                থেকে আইকন খুঁজুন
                            </p>
                        </div>
                        
                        <!-- Color -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                                রঙ
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="color" 
                                       id="color" 
                                       name="color" 
                                       value="<?php echo htmlspecialchars($category['color']); ?>"
                                       class="w-12 h-12 border border-gray-300 rounded-lg">
                                <input type="text" 
                                       id="color_text" 
                                       value="<?php echo htmlspecialchars($category['color']); ?>"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="#3B82F6"
                                       onchange="document.getElementById('color').value = this.value">
                            </div>
                        </div>
                        
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                স্ট্যাটাস
                            </label>
                            <select id="status" 
                                    name="status" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="active" <?php echo $category['status'] === 'active' ? 'selected' : ''; ?>>সক্রিয়</option>
                                <option value="inactive" <?php echo $category['status'] === 'inactive' ? 'selected' : ''; ?>>নিষ্ক্রিয়</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            বিবরণ
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="এই ক্যাটেগরি সম্পর্কে বিস্তারিত বিবরণ লিখুন"><?php echo htmlspecialchars($category['description']); ?></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4">
                        <a href="index.php" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            বাতিল
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            পরিবর্তন সংরক্ষণ করুন
                        </button>
                    </div>
                    
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-generate slug
function generateSlug(title, slugField) {
    const slug = title.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
    
    document.getElementById(slugField).value = slug;
}

// Color picker sync
document.getElementById('color').addEventListener('change', function() {
    document.getElementById('color_text').value = this.value;
    updateIconPreview();
});

document.getElementById('color_text').addEventListener('input', function() {
    if (/^#[0-9A-F]{6}$/i.test(this.value)) {
        document.getElementById('color').value = this.value;
        updateIconPreview();
    }
});

// Icon preview
document.getElementById('icon').addEventListener('input', function() {
    updateIconPreview();
});

function updateIconPreview() {
    const iconClass = document.getElementById('icon').value || 'fas fa-folder';
    const color = document.getElementById('color').value;
    const preview = document.getElementById('icon-preview');
    
    preview.style.backgroundColor = color;
    preview.innerHTML = `<i class="${iconClass}"></i>`;
}
</script>

<?php include '../includes/footer.php'; ?>