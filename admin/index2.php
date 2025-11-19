<?php
/**
 * Admin Dashboard
 * Movie Review Hub - Modern & Professional
 */

// Define access constant
define('MRH_ACCESS', true);

// Start session
session_start();

// Include core files
require_once '../config.php';
require_once '../functions.php';

// Initialize database
$db = Database::getInstance();

// Check authentication
if (!Auth::check() || !Auth::hasRole(['admin', 'editor'])) {
    header('Location: /admin/login.php');
    exit;
}

$user = Auth::user();

// Get dashboard statistics
$stats = [
    'total_reviews' => $db->count('reviews'),
    'published_reviews' => $db->count('reviews', ['status' => 'published']),
    'draft_reviews' => $db->count('reviews', ['status' => 'draft']),
    'pending_reviews' => $db->count('reviews', ['status' => 'pending']),
    'total_categories' => $db->count('categories', ['status' => 'active']),
    'total_comments' => $db->count('comments'),
    'pending_comments' => $db->count('comments', ['status' => 'pending']),
    'newsletter_subscribers' => $db->count('newsletter_subscribers', ['status' => 'active']),
    'total_users' => $db->count('users', ['status' => 'active'])
];

// Get total views
$views_result = $db->query("SELECT SUM(view_count) as total FROM reviews WHERE status = 'published'");
$stats['total_views'] = $views_result ? ($views_result->first()['total'] ?? 0) : 0;

// Get recent reviews
$recent_reviews = $db->query("
    SELECT r.*, u.full_name as author_name 
    FROM reviews r 
    LEFT JOIN users u ON r.author_id = u.id 
    ORDER BY r.created_at DESC 
    LIMIT 5
");
$recent_reviews = $recent_reviews ? $recent_reviews->results() : [];

// Get recent comments
$recent_comments = $db->query("
    SELECT c.*, r.title as review_title, r.slug as review_slug 
    FROM comments c 
    LEFT JOIN reviews r ON c.review_id = r.id 
    ORDER BY c.created_at DESC 
    LIMIT 5
");
$recent_comments = $recent_comments ? $recent_comments->results() : [];

// Get analytics data for last 30 days
$analytics_data = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $analytics_data[] = [
        'date' => $date,
        'views' => rand(50, 200), // In real app, get from analytics table
        'visitors' => rand(30, 150)
    ];
}

// Get top performing reviews
$top_reviews = $db->query("
    SELECT title, slug, view_count, rating 
    FROM reviews 
    WHERE status = 'published' 
    ORDER BY view_count DESC 
    LIMIT 5
");
$top_reviews = $top_reviews ? $top_reviews->results() : [];

// Set page metadata
$page_title = 'Dashboard - Admin Panel';

// Include admin header
include 'includes/header.php';
?>

<!-- Dashboard Content -->
<div class="admin-content">
    <div class="container-fluid">
        
        <!-- Page Header -->
        <div class="admin-page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="admin-page-title">Dashboard</h1>
                    <p class="admin-page-subtitle">
                        স্বাগতম, <?php echo escape($user['display_name'] ?: $user['full_name']); ?>! 
                        আজকের পরিসংখ্যান ও আপডেট দেখুন।
                    </p>
                </div>
                <div class="col-auto">
                    <div class="admin-page-actions">
                        <a href="/admin/reviews/create.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>নতুন রিভিউ
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download me-2"></i>Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/admin/export/reviews.php">Reviews Data</a></li>
                                <li><a class="dropdown-item" href="/admin/export/analytics.php">Analytics</a></li>
                                <li><a class="dropdown-item" href="/admin/export/subscribers.php">Subscribers</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid mb-4">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-film"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo number_format($stats['total_reviews']); ?></div>
                        <div class="stat-label">মোট রিভিউ</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+<?php echo number_format($stats['published_reviews']); ?> প্রকাশিত</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-success">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo number_format($stats['total_views']); ?></div>
                        <div class="stat-label">মোট ভিউ</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>গত মাসের তুলনায়</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo number_format($stats['total_comments']); ?></div>
                        <div class="stat-label">মোট মন্তব্য</div>
                        <?php if ($stats['pending_comments'] > 0): ?>
                            <div class="stat-change pending">
                                <i class="fas fa-clock"></i>
                                <span><?php echo number_format($stats['pending_comments']); ?> অপেক্ষমান</span>
                            </div>
                        <?php else: ?>
                            <div class="stat-change neutral">
                                <i class="fas fa-check"></i>
                                <span>সব অনুমোদিত</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-info">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo number_format($stats['newsletter_subscribers']); ?></div>
                        <div class="stat-label">সাবস্ক্রাইবার</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>বৃদ্ধি পাচ্ছে</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Dashboard Content -->
        <div class="row g-4">
            
            <!-- Analytics Chart -->
            <div class="col-lg-8">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title">
                            <i class="fas fa-chart-line me-2"></i>পরিসংখ্যান (গত ৩০ দিন)
                        </h5>
                        <div class="admin-card-actions">
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="chartType" id="viewsChart" checked>
                                <label class="btn btn-outline-primary" for="viewsChart">ভিউ</label>
                                
                                <input type="radio" class="btn-check" name="chartType" id="visitorsChart">
                                <label class="btn btn-outline-primary" for="visitorsChart">ভিজিটর</label>
                            </div>
                        </div>
                    </div>
                    <div class="admin-card-body">
                        <canvas id="analyticsChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="col-lg-4">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title">
                            <i class="fas fa-bolt me-2"></i>দ্রুত অ্যাকশন
                        </h5>
                    </div>
                    <div class="admin-card-body">
                        <div class="quick-actions">
                            <a href="/admin/reviews/create.php" class="quick-action-item">
                                <div class="quick-action-icon bg-primary">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="quick-action-content">
                                    <h6>নতুন রিভিউ</h6>
                                    <p>রিভিউ লিখুন</p>
                                </div>
                            </a>
                            
                            <a href="/admin/categories/create.php" class="quick-action-item">
                                <div class="quick-action-icon bg-success">
                                    <i class="fas fa-folder-plus"></i>
                                </div>
                                <div class="quick-action-content">
                                    <h6>নতুন ক্যাটেগরি</h6>
                                    <p>ক্যাটেগরি যোগ করুন</p>
                                </div>
                            </a>
                            
                            <?php if ($stats['pending_comments'] > 0): ?>
                                <a href="/admin/comments.php?status=pending" class="quick-action-item">
                                    <div class="quick-action-icon bg-warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="quick-action-content">
                                        <h6>মন্তব্য পর্যালোচনা</h6>
                                        <p><?php echo $stats['pending_comments']; ?>টি অপেক্ষমান</p>
                                    </div>
                                </a>
                            <?php endif; ?>
                            
                            <a href="/admin/settings.php" class="quick-action-item">
                                <div class="quick-action-icon bg-secondary">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div class="quick-action-content">
                                    <h6>সেটিংস</h6>
                                    <p>সাইট কনফিগার করুন</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Reviews -->
            <div class="col-lg-6">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title">
                            <i class="fas fa-clock me-2"></i>সাম্প্রতিক রিভিউ
                        </h5>
                        <a href="/admin/reviews.php" class="admin-card-link">সব দেখুন</a>
                    </div>
                    <div class="admin-card-body p-0">
                        <?php if (!empty($recent_reviews)): ?>
                            <div class="admin-list">
                                <?php foreach ($recent_reviews as $review): ?>
                                    <div class="admin-list-item">
                                        <div class="item-thumbnail">
                                            <img src="<?php echo $review['poster_image'] ? upload('posters/' . $review['poster_image']) : asset('images/placeholder-poster.jpg'); ?>" 
                                                 alt="<?php echo escape($review['title']); ?>">
                                        </div>
                                        <div class="item-content">
                                            <h6 class="item-title">
                                                <a href="/admin/reviews/edit.php?id=<?php echo $review['id']; ?>">
                                                    <?php echo escape(Utils::truncateText($review['title'], 40)); ?>
                                                </a>
                                            </h6>
                                            <div class="item-meta">
                                                <span class="status-badge status-<?php echo $review['status']; ?>">
                                                    <?php echo ucfirst($review['status']); ?>
                                                </span>
                                                <span class="item-author">
                                                    <?php echo escape($review['author_name']); ?>
                                                </span>
                                                <span class="item-date">
                                                    <?php echo Utils::timeAgo($review['created_at']); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="item-actions">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-ghost" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="/admin/reviews/edit.php?id=<?php echo $review['id']; ?>">সম্পাদনা</a></li>
                                                    <li><a class="dropdown-item" href="/review/<?php echo $review['slug']; ?>" target="_blank">দেখুন</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteItem('review', <?php echo $review['id']; ?>)">মুছুন</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="admin-empty-state">
                                <i class="fas fa-film"></i>
                                <p>কোন রিভিউ নেই</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Recent Comments -->
            <div class="col-lg-6">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title">
                            <i class="fas fa-comments me-2"></i>সাম্প্রতিক মন্তব্য
                        </h5>
                        <a href="/admin/comments.php" class="admin-card-link">সব দেখুন</a>
                    </div>
                    <div class="admin-card-body p-0">
                        <?php if (!empty($recent_comments)): ?>
                            <div class="admin-list">
                                <?php foreach ($recent_comments as $comment): ?>
                                    <div class="admin-list-item">
                                        <div class="item-avatar">
                                            <img src="<?php echo asset('images/avatar-default.png'); ?>" 
                                                 alt="<?php echo escape($comment['author_name']); ?>">
                                        </div>
                                        <div class="item-content">
                                            <h6 class="item-title">
                                                <?php echo escape($comment['author_name']); ?>
                                            </h6>
                                            <p class="item-text">
                                                <?php echo escape(Utils::truncateText($comment['content'], 80)); ?>
                                            </p>
                                            <div class="item-meta">
                                                <span class="status-badge status-<?php echo $comment['status']; ?>">
                                                    <?php echo ucfirst($comment['status']); ?>
                                                </span>
                                                <a href="/review/<?php echo $comment['review_slug']; ?>" class="item-link">
                                                    <?php echo escape(Utils::truncateText($comment['review_title'], 30)); ?>
                                                </a>
                                                <span class="item-date">
                                                    <?php echo Utils::timeAgo($comment['created_at']); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="item-actions">
                                            <?php if ($comment['status'] === 'pending'): ?>
                                                <button class="btn btn-sm btn-success me-1" onclick="approveComment(<?php echo $comment['id']; ?>)">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-ghost" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="/admin/comments/edit.php?id=<?php echo $comment['id']; ?>">সম্পাদনা</a></li>
                                                    <?php if ($comment['status'] === 'pending'): ?>
                                                        <li><a class="dropdown-item" href="#" onclick="approveComment(<?php echo $comment['id']; ?>)">অনুমোদন</a></li>
                                                    <?php endif; ?>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteItem('comment', <?php echo $comment['id']; ?>)">মুছুন</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="admin-empty-state">
                                <i class="fas fa-comments"></i>
                                <p>কোন মন্তব্য নেই</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Top Performing Content -->
            <div class="col-lg-6">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title">
                            <i class="fas fa-trophy me-2"></i>সেরা পারফরমিং রিভিউ
                        </h5>
                    </div>
                    <div class="admin-card-body p-0">
                        <?php if (!empty($top_reviews)): ?>
                            <div class="admin-list">
                                <?php foreach ($top_reviews as $index => $review): ?>
                                    <div class="admin-list-item">
                                        <div class="item-rank">
                                            #<?php echo $index + 1; ?>
                                        </div>
                                        <div class="item-content">
                                            <h6 class="item-title">
                                                <a href="/review/<?php echo $review['slug']; ?>" target="_blank">
                                                    <?php echo escape(Utils::truncateText($review['title'], 35)); ?>
                                                </a>
                                            </h6>
                                            <div class="item-meta">
                                                <span class="metric">
                                                    <i class="fas fa-eye"></i>
                                                    <?php echo number_format($review['view_count']); ?>
                                                </span>
                                                <?php if ($review['rating'] > 0): ?>
                                                    <span class="metric">
                                                        <i class="fas fa-star text-warning"></i>
                                                        <?php echo Utils::formatRating($review['rating']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="admin-empty-state">
                                <i class="fas fa-chart-line"></i>
                                <p>কোন ডেটা নেই</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="col-lg-6">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title">
                            <i class="fas fa-server me-2"></i>সিস্টেম স্ট্যাটাস
                        </h5>
                    </div>
                    <div class="admin-card-body">
                        <div class="system-status">
                            <div class="status-item">
                                <div class="status-label">PHP Version</div>
                                <div class="status-value">
                                    <span class="badge bg-success"><?php echo PHP_VERSION; ?></span>
                                </div>
                            </div>
                            
                            <div class="status-item">
                                <div class="status-label">Database</div>
                                <div class="status-value">
                                    <span class="badge bg-success">Connected</span>
                                </div>
                            </div>
                            
                            <div class="status-item">
                                <div class="status-label">Storage</div>
                                <div class="status-value">
                                    <?php
                                    $disk_total = disk_total_space('.');
                                    $disk_free = disk_free_space('.');
                                    $disk_used_percent = (($disk_total - $disk_free) / $disk_total) * 100;
                                    ?>
                                    <span class="badge bg-<?php echo $disk_used_percent > 80 ? 'warning' : 'success'; ?>">
                                        <?php echo round(100 - $disk_used_percent, 1); ?>% Free
                                    </span>
                                </div>
                            </div>
                            
                            <div class="status-item">
                                <div class="status-label">Cache</div>
                                <div class="status-value">
                                    <span class="badge bg-info">Active</span>
                                </div>
                            </div>
                            
                            <div class="status-item">
                                <div class="status-label">Last Backup</div>
                                <div class="status-value">
                                    <span class="text-muted">Never</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="/admin/system/backup.php" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download me-1"></i>Create Backup
                            </a>
                            <a href="/admin/system/cache.php" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-sync me-1"></i>Clear Cache
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Analytics Chart
    const ctx = document.getElementById('analyticsChart').getContext('2d');
    const analyticsData = <?php echo json_encode($analytics_data); ?>;
    
    const chartData = {
        labels: analyticsData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('bn-BD', { month: 'short', day: 'numeric' });
        }),
        datasets: [{
            label: 'ভিউ',
            data: analyticsData.map(item => item.views),
            borderColor: 'rgb(99, 102, 241)',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'ভিজিটর',
            data: analyticsData.map(item => item.visitors),
            borderColor: 'rgb(245, 158, 11)',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
            tension: 0.4,
            fill: true
        }]
    };
    
    const chart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            elements: {
                point: {
                    radius: 3,
                    hoverRadius: 6
                }
            }
        }
    });
    
    // Chart type toggle
    document.querySelectorAll('input[name="chartType"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.id === 'viewsChart') {
                chart.data.datasets[0].hidden = false;
                chart.data.datasets[1].hidden = true;
            } else {
                chart.data.datasets[0].hidden = true;
                chart.data.datasets[1].hidden = false;
            }
            chart.update();
        });
    });
    
    // Initialize with views only
    chart.data.datasets[1].hidden = true;
    chart.update();
});

// Quick action functions
function approveComment(commentId) {
    if (confirm('এই মন্তব্যটি অনুমোদন করবেন?')) {
        fetch(`/admin/api/comments.php?action=approve&id=${commentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?php echo csrf_token(); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}

function deleteItem(type, id) {
    if (confirm(`এই ${type === 'review' ? 'রিভিউ' : 'মন্তব্য'}টি মুছে ফেলবেন?`)) {
        fetch(`/admin/api/${type}s.php?action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?php echo csrf_token(); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}
</script>

<?php
// Include admin footer
include 'includes/footer.php';
?>