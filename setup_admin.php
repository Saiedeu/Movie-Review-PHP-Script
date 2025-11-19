<?php
/**
 * Admin Setup & Validation Script
 * Use this to create/update admin user and validate credentials
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once 'config/config.php';

// Security: Only allow in development or with special token
$setup_token = $_GET['token'] ?? '';
$valid_token = 'msr_setup_2024'; // Change this token for security

if ($setup_token !== $valid_token) {
    die('
    <h1>Admin Setup</h1>
    <p>Add this token to URL: <code>?token=msr_setup_2024</code></p>
    <p>Example: <code>setup_admin.php?token=msr_setup_2024</code></p>
    ');
}

$message = '';
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_admin') {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            
            // Admin credentials
            $username = 'MSR';
            $email = 'msa.masum.bd@gmail.com';
            $password = 'MasumAhmed@10';
            $hashed_password = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
            
            // Check if admin already exists
            $existing_admin = $db->fetchOne("SELECT * FROM users WHERE username = ? OR email = ?", [$username, $email]);
            
            if ($existing_admin) {
                // Update existing admin
                $result = $db->query(
                    "UPDATE users SET password = ?, email = ?, role = 'admin', status = 'active', updated_at = NOW() WHERE username = ?",
                    [$hashed_password, $email, $username]
                );
                
                if ($result) {
                    $success = "Admin user updated successfully!";
                } else {
                    $error = "Failed to update admin user.";
                }
            } else {
                // Create new admin
                $result = $db->query(
                    "INSERT INTO users (username, email, password, role, status, created_at, updated_at) VALUES (?, ?, ?, 'admin', 'active', NOW(), NOW())",
                    [$username, $email, $hashed_password]
                );
                
                if ($result) {
                    $success = "Admin user created successfully!";
                } else {
                    $error = "Failed to create admin user.";
                }
            }
            
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } elseif ($action === 'test_login') {
        $test_username = trim($_POST['test_username'] ?? '');
        $test_password = $_POST['test_password'] ?? '';
        
        if (empty($test_username) || empty($test_password)) {
            $error = "Username and password are required for testing.";
        } else {
            try {
                $db = Database::getInstance();
                $user = $db->fetchOne("SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'active'", [$test_username, $test_username]);
                
                if ($user && password_verify($test_password, $user['password'])) {
                    $success = "✅ Login test successful! Credentials are working correctly.";
                } else {
                    if (!$user) {
                        $error = "❌ User not found with username/email: " . htmlspecialchars($test_username);
                    } else {
                        $error = "❌ Password verification failed. The password doesn't match.";
                    }
                }
            } catch (Exception $e) {
                $error = "Database error during test: " . $e->getMessage();
            }
        }
    }
}

// Get current admin info
$current_admin = null;
$db_status = 'Unknown';
try {
    $db = Database::getInstance();
    $current_admin = $db->fetchOne("SELECT * FROM users WHERE username = 'MSR' OR email = 'msa.masum.bd@gmail.com'");
    $db_status = 'Connected';
} catch (Exception $e) {
    $db_status = 'Error: ' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Setup & Validation - MSR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', 'Roboto', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">MSR Admin Setup & Validation</h1>
            <p class="text-gray-600">Create, update, and validate admin credentials</p>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <strong>Success:</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Current Status -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Current Status</h2>
                
                <!-- Database Status -->
                <div class="mb-4">
                    <h3 class="font-medium text-gray-700 mb-2">Database Connection:</h3>
                    <span class="inline-block px-3 py-1 rounded-full text-sm <?php echo strpos($db_status, 'Error') === false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                        <?php echo htmlspecialchars($db_status); ?>
                    </span>
                </div>

                <!-- Admin User Status -->
                <div class="mb-4">
                    <h3 class="font-medium text-gray-700 mb-2">Admin User Status:</h3>
                    <?php if ($current_admin): ?>
                        <div class="bg-gray-50 p-4 rounded border">
                            <p><strong>ID:</strong> <?php echo htmlspecialchars($current_admin['id']); ?></p>
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($current_admin['username']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($current_admin['email']); ?></p>
                            <p><strong>Role:</strong> <?php echo htmlspecialchars($current_admin['role']); ?></p>
                            <p><strong>Status:</strong> 
                                <span class="inline-block px-2 py-1 rounded text-xs <?php echo $current_admin['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo htmlspecialchars($current_admin['status']); ?>
                                </span>
                            </p>
                            <p><strong>Password Hash:</strong> 
                                <code class="text-xs bg-gray-200 px-2 py-1 rounded">
                                    <?php echo substr($current_admin['password'], 0, 20) . '...'; ?>
                                </code>
                            </p>
                            <p><strong>Last Login:</strong> <?php echo $current_admin['last_login'] ?: 'Never'; ?></p>
                        </div>
                    <?php else: ?>
                        <span class="inline-block px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">
                            No admin user found
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Default Credentials -->
                <div class="bg-blue-50 p-4 rounded border-l-4 border-blue-400">
                    <h4 class="font-medium text-blue-800 mb-2">Default Admin Credentials:</h4>
                    <p><strong>Username:</strong> MSR</p>
                    <p><strong>Email:</strong> msa.masum.bd@gmail.com</p>
                    <p><strong>Password:</strong> MasumAhmed@10</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-6">
                
                <!-- Create/Update Admin -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Create/Update Admin</h2>
                    <p class="text-gray-600 mb-4">This will create or update the admin user with the correct credentials.</p>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="create_admin">
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            Create/Update Admin User
                        </button>
                    </form>
                </div>

                <!-- Test Login -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Test Login</h2>
                    <p class="text-gray-600 mb-4">Test if the admin credentials are working correctly.</p>
                    
                    <form method="POST" action="" class="space-y-4">
                        <input type="hidden" name="action" value="test_login">
                        
                        <div>
                            <label for="test_username" class="block text-sm font-medium text-gray-700 mb-1">
                                Username or Email
                            </label>
                            <input type="text" 
                                   id="test_username" 
                                   name="test_username" 
                                   value="MSR"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                        </div>
                        
                        <div>
                            <label for="test_password" class="block text-sm font-medium text-gray-700 mb-1">
                                Password
                            </label>
                            <input type="password" 
                                   id="test_password" 
                                   name="test_password" 
                                   value="MasumAhmed@10"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                        </div>
                        
                        <button type="submit" class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors">
                            Test Login Credentials
                        </button>
                    </form>
                </div>

            </div>
        </div>

        <!-- Database Configuration -->
        <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Database Configuration</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p><strong>Host:</strong> <?php echo DB_HOST; ?></p>
                    <p><strong>Database:</strong> <?php echo DB_NAME; ?></p>
                </div>
                <div>
                    <p><strong>Username:</strong> <?php echo DB_USER; ?></p>
                    <p><strong>Charset:</strong> <?php echo DB_CHARSET; ?></p>
                </div>
            </div>
        </div>

        <!-- Security Warning -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Security Warning</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>🔒 <strong>Delete this file after setup!</strong> This file should not be accessible in production.</p>
                        <p>🔑 Change the setup token after use for additional security.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-gray-50 rounded-lg p-4 mt-6 text-center">
            <h3 class="font-medium text-gray-800 mb-2">Quick Links</h3>
            <div class="space-x-4">
                <a href="admin/login.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                    Go to Admin Login
                </a>
                <a href="index.php" class="inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition-colors">
                    Go to Website
                </a>
            </div>
        </div>

    </div>

    <script>
        // Auto-refresh every 30 seconds if there are no messages
        <?php if (empty($error) && empty($success)): ?>
        setTimeout(function() {
            // Only refresh if user hasn't interacted recently
            if (document.visibilityState === 'visible') {
                location.reload();
            }
        }, 30000);
        <?php endif; ?>
    </script>
</body>
</html>