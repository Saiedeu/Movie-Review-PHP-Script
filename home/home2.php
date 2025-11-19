<?php
/**
 * Popcorn Media Homepage - Design 1: Cinematic Dark Theme
 */

// Define access constant
define('POPCORN_ACCESS', true);

// Include configuration (assuming similar structure)
// require_once 'config/config.php';

// Sample data - replace with your actual database calls
$featured_movies = [
    ['id' => 1, 'title' => 'The Dark Knight', 'rating' => 4.8, 'year' => 2008, 'genre' => 'Action', 'poster' => 'https://images.unsplash.com/photo-1489599732522-2e4e4d64e6b8?w=400'],
    ['id' => 2, 'title' => 'Inception', 'rating' => 4.7, 'year' => 2010, 'genre' => 'Sci-Fi', 'poster' => 'https://images.unsplash.com/photo-1518676590629-3dcbd9c5a5c9?w=400'],
    ['id' => 3, 'title' => 'Parasite', 'rating' => 4.9, 'year' => 2019, 'genre' => 'Thriller', 'poster' => 'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?w=400']
];

$trending_movies = [
    ['title' => 'Dune: Part Two', 'views' => '2.4M', 'rating' => 4.6],
    ['title' => 'Oppenheimer', 'views' => '1.8M', 'rating' => 4.5],
    ['title' => 'Spider-Verse', 'views' => '3.2M', 'rating' => 4.8],
    ['title' => 'John Wick 4', 'views' => '2.1M', 'rating' => 4.4]
];

$categories = [
    ['name' => 'Action', 'count' => 245, 'icon' => 'explosion'],
    ['name' => 'Drama', 'count' => 189, 'icon' => 'masks'],
    ['name' => 'Comedy', 'count' => 156, 'icon' => 'laugh'],
    ['name' => 'Horror', 'count' => 98, 'icon' => 'ghost'],
    ['name' => 'Sci-Fi', 'count' => 134, 'icon' => 'rocket'],
    ['name' => 'Romance', 'count' => 112, 'icon' => 'heart']
];
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popcorn Media - Premium Movie Experience</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF6B35',
                        secondary: '#F7931E',
                        dark: '#0A0A0A',
                        cinema: '#1A1A1A',
                        accent: '#FFD700'
                    },
                    fontFamily: {
                        'display': ['Inter', 'sans-serif']
                    }
                }
            }
        }

        // Dark mode detection
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            if (event.matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        
        .hero-gradient {
            background: linear-gradient(135deg, #0A0A0A 0%, #1A1A1A 50%, #2A1810 100%);
        }
        
        .movie-glow {
            box-shadow: 0 0 30px rgba(255, 107, 53, 0.3);
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .text-glow {
            text-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body class="bg-dark text-white font-display overflow-x-hidden">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-dark/90 backdrop-blur-lg border-b border-primary/20">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <!-- Popcorn Media Icon -->
                    <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.5 2C19.3 2 20 2.7 20 3.5C20 4.3 19.3 5 18.5 5S17 4.3 17 3.5C17 2.7 17.7 2 18.5 2M14.5 2C15.3 2 16 2.7 16 3.5C16 4.3 15.3 5 14.5 5S13 4.3 13 3.5C13 2.7 13.7 2 14.5 2M10.5 2C11.3 2 12 2.7 12 3.5C12 4.3 11.3 5 10.5 5S9 4.3 9 3.5C9 2.7 9.7 2 10.5 2M6.5 2C7.3 2 8 2.7 8 3.5C8 4.3 7.3 5 6.5 5S5 4.3 5 3.5C5 2.7 5.7 2 6.5 2M19 6L20 18C20.1 19.1 19.2 20 18 20H6C4.8 20 3.9 19.1 4 18L5 6H19Z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-glow">Popcorn Media</h1>
                        <p class="text-xs text-gray-400">Premium Cinema</p>
                    </div>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-white hover:text-primary transition-colors">Home</a>
                    <a href="#movies" class="text-gray-300 hover:text-primary transition-colors">Movies</a>
                    <a href="#series" class="text-gray-300 hover:text-primary transition-colors">Series</a>
                    <a href="#genres" class="text-gray-300 hover:text-primary transition-colors">Genres</a>
                    <a href="#reviews" class="text-gray-300 hover:text-primary transition-colors">Reviews</a>
                </div>
                
                <!-- Search & User -->
                <div class="flex items-center space-x-4">
                    <button class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <button class="bg-gradient-to-r from-primary to-secondary px-6 py-2 rounded-full text-white font-semibold hover:shadow-lg transition-all">
                        Sign In
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="min-h-screen hero-gradient flex items-center relative overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0">
            <div class="absolute top-20 left-10 w-32 h-32 bg-primary/20 rounded-full blur-xl float-animation"></div>
            <div class="absolute top-40 right-20 w-24 h-24 bg-secondary/20 rounded-full blur-xl float-animation" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-20 left-1/4 w-40 h-40 bg-accent/10 rounded-full blur-xl float-animation" style="animation-delay: 4s;"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Content -->
                <div class="text-white space-y-8">
                    <!-- Badge -->
                    <div class="inline-flex items-center bg-white/10 backdrop-blur-sm rounded-full px-6 py-3 text-sm font-medium">
                        <div class="w-2 h-2 bg-accent rounded-full mr-3 animate-pulse"></div>
                        Premium Movie Experience
                    </div>
                    
                    <!-- Main Heading -->
                    <div class="space-y-4">
                        <h1 class="text-6xl lg:text-8xl font-black leading-tight">
                            <span class="text-glow">CINEMA</span>
                            <br>
                            <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">REIMAGINED</span>
                        </h1>
                        <p class="text-xl lg:text-2xl text-gray-300 leading-relaxed max-w-2xl">
                            Discover, review, and experience the world's greatest films like never before. 
                            Your premium destination for cinematic excellence.
                        </p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button class="group bg-gradient-to-r from-primary to-secondary text-white px-8 py-4 rounded-2xl font-bold text-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                            <svg class="w-6 h-6 mr-3 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            Watch Trailer
                        </button>
                        <button class="border-2 border-white/30 hover:border-primary text-white hover:bg-primary/10 px-8 py-4 rounded-2xl font-bold text-lg transition-all duration-300">
                            Explore Library
                        </button>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-8 pt-8">
                        <div class="text-center lg:text-left">
                            <div class="text-4xl font-black text-accent">50K+</div>
                            <div class="text-gray-400 text-sm font-medium">Movies</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div class="text-4xl font-black text-accent">2M+</div>
                            <div class="text-gray-400 text-sm font-medium">Users</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div class="text-4xl font-black text-accent">4.9★</div>
                            <div class="text-gray-400 text-sm font-medium">Rating</div>
                        </div>
                    </div>
                </div>
                
                <!-- Featured Movie Showcase -->
                <div class="relative">
                    <div class="relative max-w-lg mx-auto">
                        <!-- Main Featured Card -->
                        <?php if (!empty($featured_movies)): ?>
                            <?php $main_movie = $featured_movies[0]; ?>
                            <div class="bg-cinema/80 backdrop-blur-lg rounded-3xl overflow-hidden movie-glow card-hover">
                                <div class="relative h-96">
                                    <img src="<?php echo $main_movie['poster']; ?>" 
                                         alt="<?php echo htmlspecialchars($main_movie['title']); ?>" 
                                         class="w-full h-full object-cover">
                                    
                                    <!-- Play Overlay -->
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300">
                                        <div class="w-20 h-20 bg-primary/80 backdrop-blur-sm rounded-full flex items-center justify-center">
                                            <svg class="w-10 h-10 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Rating Badge -->
                                    <div class="absolute top-6 right-6 bg-black/70 backdrop-blur-sm text-white px-4 py-2 rounded-full font-bold">
                                        ★ <?php echo $main_movie['rating']; ?>
                                    </div>
                                </div>
                                
                                <div class="p-8">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <span class="bg-primary/20 text-primary px-3 py-1 rounded-full text-sm font-semibold">
                                            <?php echo $main_movie['genre']; ?>
                                        </span>
                                        <span class="text-gray-400"><?php echo $main_movie['year']; ?></span>
                                    </div>
                                    
                                    <h3 class="text-2xl font-bold text-white mb-4">
                                        <?php echo htmlspecialchars($main_movie['title']); ?>
                                    </h3>
                                    
                                    <button class="w-full bg-gradient-to-r from-primary to-secondary text-white py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
                                        Watch Now
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Floating Mini Cards -->
                        <?php if (count($featured_movies) > 1): ?>
                            <div class="absolute -top-8 -left-16 bg-cinema/90 backdrop-blur-lg rounded-2xl p-4 card-hover" style="animation-delay: 1s;">
                                <div class="flex items-center space-x-3">
                                    <img src="<?php echo $featured_movies[1]['poster']; ?>" 
                                         alt="<?php echo htmlspecialchars($featured_movies[1]['title']); ?>" 
                                         class="w-12 h-16 object-cover rounded-lg">
                                    <div>
                                        <h4 class="font-semibold text-white text-sm">
                                            <?php echo htmlspecialchars(substr($featured_movies[1]['title'], 0, 15)); ?>...
                                        </h4>
                                        <div class="flex text-accent text-xs">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="<?php echo $i <= $featured_movies[1]['rating'] ? 'text-accent' : 'text-gray-600'; ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (count($featured_movies) > 2): ?>
                            <div class="absolute -bottom-8 -right-16 bg-cinema/90 backdrop-blur-lg rounded-2xl p-4 card-hover" style="animation-delay: 2s;">
                                <div class="flex items-center space-x-3">
                                    <img src="<?php echo $featured_movies[2]['poster']; ?>" 
                                         alt="<?php echo htmlspecialchars($featured_movies[2]['title']); ?>" 
                                         class="w-12 h-16 object-cover rounded-lg">
                                    <div>
                                        <h4 class="font-semibold text-white text-sm">
                                            <?php echo htmlspecialchars(substr($featured_movies[2]['title'], 0, 15)); ?>...
                                        </h4>
                                        <div class="flex text-accent text-xs">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="<?php echo $i <= $featured_movies[2]['rating'] ? 'text-accent' : 'text-gray-600'; ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <div class="w-8 h-12 border-2 border-primary rounded-full flex justify-center">
                <div class="w-1 h-3 bg-primary rounded-full mt-2 animate-pulse"></div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="py-20 bg-cinema">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-4xl font-bold text-white mb-8">Find Your Next Favorite</h2>
                
                <!-- Search Bar -->
                <div class="relative mb-8">
                    <input type="text" 
                           placeholder="Search movies, series, actors..." 
                           class="w-full px-8 py-6 text-lg bg-dark/50 border-2 border-primary/30 rounded-2xl focus:outline-none focus:border-primary transition-all text-white placeholder-gray-400">
                    <button class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-primary text-white p-4 rounded-xl hover:bg-primary/80 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Filter Pills -->
                <div class="flex flex-wrap justify-center gap-3">
                    <button class="bg-primary text-white px-6 py-3 rounded-full font-medium">All</button>
                    <button class="bg-dark/50 text-gray-300 hover:bg-primary/20 hover:text-white px-6 py-3 rounded-full font-medium transition-colors">Movies</button>
                    <button class="bg-dark/50 text-gray-300 hover:bg-primary/20 hover:text-white px-6 py-3 rounded-full font-medium transition-colors">Series</button>
                    <button class="bg-dark/50 text-gray-300 hover:bg-primary/20 hover:text-white px-6 py-3 rounded-full font-medium transition-colors">Documentaries</button>
                    <button class="bg-dark/50 text-gray-300 hover:bg-primary/20 hover:text-white px-6 py-3 rounded-full font-medium transition-colors">Animation</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Trending Section -->
    <section class="py-20 bg-dark">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h2 class="text-4xl font-bold text-white mb-4">Trending Now</h2>
                    <p class="text-gray-400 text-lg">Most watched this week</p>
                </div>
                <button class="hidden md:flex items-center bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/80 transition-colors">
                    View All
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($trending_movies as $index => $movie): ?>
                    <div class="bg-cinema rounded-2xl p-6 card-hover group">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-3xl font-bold text-primary">#<?php echo $index + 1; ?></span>
                            <div class="flex text-accent text-sm">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="<?php echo $i <= $movie['rating'] ? 'text-accent' : 'text-gray-600'; ?>">★</span>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-primary transition-colors">
                            <?php echo htmlspecialchars($movie['title']); ?>
                        </h3>
                        
                        <div class="flex items-center justify-between text-gray-400 text-sm">
                            <span><?php echo $movie['views']; ?> views</span>
                            <span>★ <?php echo $movie['rating']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-20 bg-cinema">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-white mb-6">Browse by Genre</h2>
                <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                    Discover movies and series across all your favorite genres
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <?php foreach ($categories as $category): ?>
                    <div class="bg-dark/50 hover:bg-primary/20 rounded-2xl p-6 text-center card-hover group cursor-pointer">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary to-secondary rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <?php
                            $icons = [
                                'explosion' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>',
                                'masks' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>',
                                'laugh' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                                'ghost' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>',
                                'rocket' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>',
                                'heart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>'
                            ];
                            ?>
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <?php echo $icons[$category['icon']] ?? $icons['masks']; ?>
                            </svg>
                        </div>
                        <h3 class="font-bold text-white group-hover:text-primary transition-colors mb-2">
                            <?php echo $category['name']; ?>
                        </h3>
                        <p class="text-gray-400 text-sm">
                            <?php echo $category['count']; ?> titles
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20 bg-gradient-to-r from-primary to-secondary">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-5xl font-bold text-white mb-6">Ready to Start Watching?</h2>
                <p class="text-xl text-white/90 mb-8 leading-relaxed">
                    Join millions of movie lovers and discover your next favorite film today.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <button class="bg-white text-primary px-8 py-4 rounded-2xl font-bold text-lg hover:bg-gray-100 transition-all transform hover:scale-105">
                        Start Free Trial
                    </button>
                    <button class="border-2 border-white/50 text-white hover:bg-white/10 px-8 py-4 rounded-2xl font-bold text-lg transition-all">
                        Learn More
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark py-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18.5 2C19.3 2 20 2.7 20 3.5C20 4.3 19.3 5 18.5 5S17 4.3 17 3.5C17 2.7 17.7 2 18.5 2M14.5 2C15.3 2 16 2.7 16 3.5C16 4.3 15.3 5 14.5 5S13 4.3 13 3.5C13 2.7 13.7 2 14.5 2M10.5 2C11.3 2 12 2.7 12 3.5C12 4.3 11.3 5 10.5 5S9 4.3 9 3.5C9 2.7 9.7 2 10.5 2M6.5 2C7.3 2 8 2.7 8 3.5C8 4.3 7.3 5 6.5 5S5 4.3 5 3.5C5 2.7 5.7 2 6.5 2M19 6L20 18C20.1 19.1 19.2 20 18 20H6C4.8 20 3.9 19.1 4 18L5 6H19Z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white">Popcorn Media</span>
                    </div>
                    <p class="text-gray-400">Your premium destination for cinematic excellence.</p>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-primary transition-colors">Movies</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">TV Shows</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Reviews</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Genres</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-primary transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Contact Us</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.719-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.347-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2024 Popcorn Media. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Add scroll effect to navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('nav');
            if (window.scrollY > 100) {
                navbar.classList.add('bg-dark');
            } else {
                navbar.classList.remove('bg-dark');
            }
        });
    </script>
</body>
</html>