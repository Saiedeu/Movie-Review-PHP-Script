<?php
/**
 * Admin Login Page
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $error = 'ইউজারনেম এবং পাসওয়ার্ড উভয়ই প্রয়োজন।';
    } else {
        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'active'", [$username, $username]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['login_time'] = time();
            
            // Update last login
            $db->query("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
            
            // Remember me functionality
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 3600), '/', '', false, true);
                $db->query("UPDATE users SET remember_token = ? WHERE id = ?", [hash('sha256', $token), $user['id']]);
            }
            
            header('Location: index.php');
            exit;
        } else {
            $error = 'ভুল ইউজারনেম বা পাসওয়ার্ড।';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অ্যাডমিন লগইন - <?php echo getSiteSetting('site_name', SITE_NAME); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Hind Siliguri', 'Roboto', sans-serif;
        }
        
        .login-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .login-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="50%" cy="50%" r="50%" fill="url(%23a)"/></svg>') center/cover;
            animation: float 20s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-30px) rotate(1deg); }
            66% { transform: translateY(20px) rotate(-1deg); }
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
        }
        
        .input-field {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            color: #4f46e5;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .floating-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        
        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: particle-float 15s infinite linear;
        }
        
        .particle:nth-child(1) { left: 10%; animation-delay: 0s; width: 10px; height: 10px; }
        .particle:nth-child(2) { left: 20%; animation-delay: 2s; width: 6px; height: 6px; }
        .particle:nth-child(3) { left: 30%; animation-delay: 4s; width: 8px; height: 8px; }
        .particle:nth-child(4) { left: 40%; animation-delay: 6s; width: 12px; height: 12px; }
        .particle:nth-child(5) { left: 50%; animation-delay: 8s; width: 4px; height: 4px; }
        .particle:nth-child(6) { left: 60%; animation-delay: 10s; width: 14px; height: 14px; }
        .particle:nth-child(7) { left: 70%; animation-delay: 12s; width: 8px; height: 8px; }
        .particle:nth-child(8) { left: 80%; animation-delay: 14s; width: 6px; height: 6px; }
        .particle:nth-child(9) { left: 90%; animation-delay: 16s; width: 10px; height: 10px; }
        
        @keyframes particle-float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }
        
        .logo-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .pulse-glow {
            animation: pulse-glow 2s infinite;
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(255, 255, 255, 0.3); }
            50% { box-shadow: 0 0 30px rgba(255, 255, 255, 0.5); }
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center relative">
    
    <!-- Floating Particles -->
    <div class="floating-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>
    
    <div class="w-full max-w-md mx-4 relative z-10">
        <!-- Login Card -->
        <div class="glass-card rounded-3xl p-8 shadow-2xl relative">
            <!-- Decorative Corner Elements -->
            <div class="absolute top-0 left-0 w-20 h-20 border-l-2 border-t-2 border-white/20 rounded-tl-3xl"></div>
            <div class="absolute bottom-0 right-0 w-20 h-20 border-r-2 border-b-2 border-white/20 rounded-br-3xl"></div>
            
            <!-- Header with Logo -->
            <div class="text-center mb-8">
                <!-- Logo Container -->
                <div class="logo-container w-24 h-24 rounded-2xl flex items-center justify-center mx-auto mb-6 pulse-glow">
                    <?php
                    $logo = getSiteSetting('site_logo', '');
                    if ($logo) {
                        echo '<img src="' . UPLOADS_URL . '/logo/' . $logo . '" alt="' . getSiteSetting('site_name', SITE_NAME) . '" class="h-16 w-auto max-w-full object-contain">';
                    } else {
                        echo '<div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center text-white font-bold text-2xl">MSR</div>';
                    }
                    ?>
                </div>
                
                <h1 class="text-3xl font-bold text-white mb-2">অ্যাডমিন প্যানেল</h1>
                <p class="text-white/80 text-lg"><?php echo getSiteSetting('site_name', SITE_NAME); ?></p>
                <div class="w-16 h-1 bg-gradient-to-r from-white/50 to-white/20 rounded-full mx-auto mt-4"></div>
            </div>
            
            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="bg-red-500/20 border border-red-400/50 text-red-100 px-4 py-3 rounded-xl mb-6 backdrop-blur-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Success Message -->
            <?php if ($success): ?>
                <div class="bg-green-500/20 border border-green-400/50 text-green-100 px-4 py-3 rounded-xl mb-6 backdrop-blur-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span><?php echo htmlspecialchars($success); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form method="POST" action="" class="space-y-6">
                <!-- Username Field -->
                <div class="space-y-2">
                    <label for="username" class="block text-white/90 text-sm font-medium">
                        ইউজারনেম বা ইমেইল
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                               class="input-field w-full text-white placeholder-white/60 pl-12 pr-4 py-4 rounded-xl focus:outline-none transition-all"
                               placeholder="আপনার ইউজারনেম বা ইমেইল লিখুন"
                               required>
                    </div>
                </div>
                
                <!-- Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-white/90 text-sm font-medium">
                        পাসওয়ার্ড
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="input-field w-full text-white placeholder-white/60 pl-12 pr-14 py-4 rounded-xl focus:outline-none transition-all"
                               placeholder="আপনার পাসওয়ার্ড লিখুন"
                               required>
                        <button type="button" 
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-white/60 hover:text-white/90 transition-colors">
                            <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-white/90 cursor-pointer">
                        <input type="checkbox" 
                               name="remember" 
                               class="w-4 h-4 text-blue-600 bg-white/10 border-white/30 rounded focus:ring-white/50 focus:ring-2 transition-all">
                        <span class="ml-3 text-sm">আমাকে মনে রাখুন</span>
                    </label>
                </div>
                
                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                            class="btn-login w-full py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-white/20 relative overflow-hidden">
                        <span class="relative z-10 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            লগইন করুন
                        </span>
                    </button>
                </div>
            </form>
            
            <!-- Footer Links -->
            <div class="mt-8 pt-6 border-t border-white/20 text-center">
                <a href="../" class="inline-flex items-center text-white/80 hover:text-white text-sm transition-colors group">
                    <svg class="w-4 h-4 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    ওয়েবসাইটে ফিরে যান
                </a>
            </div>
        </div>
        
        <!-- Security Notice -->
        <div class="text-center mt-6">
            <p class="text-white/60 text-xs">
                🔒 এই সিস্টেম সুরক্ষিত এবং এনক্রিপ্টেড
            </p>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordField.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
        
        // Auto-focus username field after page load
        window.addEventListener('load', function() {
            document.getElementById('username').focus();
        });
        
        // Add loading state to submit button
        document.querySelector('form').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = `
                <span class="relative z-10 flex items-center justify-center">
                    <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    লগইন হচ্ছে...
                </span>
            `;
            
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>