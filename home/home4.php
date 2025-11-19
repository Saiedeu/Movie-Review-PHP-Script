<?php
/**
 * Popcorn Media Homepage - Design 2: Modern Grid Layout
 */

// Define access constant
define('POPCORN_ACCESS', true);

// Include configuration (assuming similar structure)
// require_once 'config/config.php';

// Sample data - replace with your actual database calls
$featured_movies = [
    ['id' => 1, 'title' => 'Dune: Part Two', 'rating' => 4.8, 'year' => 2024, 'genre' => 'Sci-Fi', 'poster' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=500', 'description' => 'Epic continuation of the Dune saga'],
    ['id' => 2, 'title' => 'Oppenheimer', 'rating' => 4.7, 'year' => 2023, 'genre' => 'Biography', 'poster' => 'https://images.unsplash.com/photo-1595769816263-9b910be24d5f?w=500', 'description' => 'The story of the atomic bomb'],
    ['id' => 3, 'title' => 'Spider-Verse', 'rating' => 4.9, 'year' => 2023, 'genre' => 'Animation', 'poster' => 'https://images.unsplash.com/photo-1635805737707-575885ab0820?w=500', 'description' => 'Multiverse spider adventure'],
    ['id' => 4, 'title' => 'John Wick 4', 'rating' => 4.6, 'year' => 2023, 'genre' => 'Action', 'poster' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=500', 'description' => 'The final chapter'],
    ['id' => 5, 'title' => 'Guardians 3', 'rating' => 4.5, 'year' => 2023, 'genre' => 'Action', 'poster' => 'https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?w=500', 'description' => 'Final Guardians adventure'],
    ['id' => 6, 'title' => 'The Batman', 'rating' => 4.4, 'year' => 2022, 'genre' => 'Action', 'poster' => 'https://images.unsplash.com/photo-1509347528160-9329d33b2588?w=500', 'description' => 'Dark knight returns']
];

$new_releases = [
    ['title' => 'Avatar 3', 'genre' => 'Sci-Fi', 'coming' => 'Dec 2024'],
    ['title' => 'Marvel Phase 5', 'genre' => 'Action', 'coming' => 'Mar 2024'],
    ['title' => 'Fast X Part 2', 'genre' => 'Action', 'coming' => 'Apr 2024'],
    ['title' => 'Mission Impossible 8', 'genre' => 'Action', 'coming' => 'Jun 2024']
];

$top_rated = [
    ['title' => 'The Godfather', 'rating' => 9.2, 'year' => 1972],
    ['title' => 'Shawshank Redemption', 'rating' => 9.3, 'year' => 1994],
    ['title' => 'Schindler\'s List', 'rating' => 9.0, 'year' => 1993],
    ['title' => 'Pulp Fiction', 'rating' => 8.9, 'year' => 1994],
    ['title' => '12 Angry Men', 'rating' => 9.0, 'year' => 1957],
    ['title' => 'The Dark Knight', 'rating' => 9.0, 'year' => 2008]
];

$categories = [
    ['name' => 'Action', 'count' => 1245, 'color' => 'from-red-500 to-orange-500'],
    ['name' => 'Drama', 'count' => 989, 'color' => 'from-blue-500 to-purple-500'],
    ['name' => 'Comedy', 'count' => 756, 'color' => 'from-yellow-500 to-pink-500'],
    ['name' => 'Horror', 'count' => 456, 'color' => 'from-purple-500 to-red-500'],
    ['name' => 'Sci-Fi', 'count' => 634, 'color' => 'from-cyan-500 to-blue-500'],
    ['name' => 'Romance', 'count' => 512, 'color' => 'from-pink-500 to-rose-500']
];
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popcorn Media - Modern Cinema Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#5D5CDE',
                        secondary: '#EC4899',
                        accent: '#F59E0B',
                        cinema: '#111827',
                        light: '#F8FAFC'
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
        
        .grid-container {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }
        
        .hero-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            min-height: 100vh;
        }
        
        .sidebar-sticky {
            position: sticky;
            top: 6rem;
            height: fit-content;
        }
        
        .card-3d {
            transform-style: preserve-3d;
            transition: all 0.5s ease;
        }
        
        .card-3d:hover {
            transform: rotateY(5deg) rotateX(5deg) translateZ(20px);
        }
        
        .glass-morphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        @media (max-width: 768px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .sidebar-sticky {
                position: static;
            }
        }
    </style>
</head>
<body class="bg-white dark:bg-cinema text-gray-900 dark:text-white font-display">

    <!-- Top Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/90 dark:bg-cinema/90 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Brand -->
                <div class="flex items-center space-x-3">
                    <!-- Premium Popcorn Icon -->
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8.5 2C9.3 2 10 2.7 10 3.5C10 4.3 9.3 5 8.5 5S7 4.3 7 3.5C7 2.7 7.7 2 8.5 2M12.5 2C13.3 2 14 2.7 14 3.5C14 4.3 13.3 5 12.5 5S11 4.3 11 3.5C11 2.7 11.7 2 12.5 2M16.5 2C17.3 2 18 2.7 18 3.5C18 4.3 17.3 5 16.5 5S15 4.3 15 3.5C15 2.7 15.7 2 16.5 2M6 6L7 18C7.1 19.1 8 20 9 20H17C18 20 18.9 19.1 19 18L20 6H6Z"/>
                                <circle cx="9.5" cy="8" r="0.8" fill="white" opacity="0.8"/>
                                <circle cx="14.5" cy="9" r="0.8" fill="white" opacity="0.8"/>
                                <circle cx="11.5" cy="11" r="0.8" fill="white" opacity="0.8"/>
                                <circle cx="16" cy="12" r="0.8" fill="white" opacity="0.8"/>
                                <circle cx="13" cy="14" r="0.8" fill="white" opacity="0.8"/>
                            </svg>
                        </div>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-accent rounded-full animate-pulse"></div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            Popcorn Media
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Modern Cinema Hub</p>
                    </div>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="#home" class="text-primary font-semibold">Home</a>
                    <a href="#movies" class="hover:text-primary transition-colors">Movies</a>
                    <a href="#series" class="hover:text-primary transition-colors">Series</a>
                    <a href="#reviews" class="hover:text-primary transition-colors">Reviews</a>
                    <a href="#about" class="hover:text-primary transition-colors">About</a>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-4">
                    <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <button class="bg-gradient-to-r from-primary to-secondary text-white px-6 py-2 rounded-xl font-semibold hover:shadow-lg transition-all">
                        Join Now
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Grid Layout -->
    <main class="pt-20">
        <div class="container mx-auto px-4 py-8">
            <div class="hero-grid">
                <!-- Main Content Area -->
                <div class="space-y-12">
                    
                    <!-- Hero Section -->
                    <section class="relative bg-gradient-to-br from-primary/10 via-secondary/10 to-accent/10 dark:from-primary/20 dark:via-secondary/20 dark:to-accent/20 rounded-3xl p-8 lg:p-12 overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-10">
                            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-primary to-transparent"></div>
                            <div class="absolute bottom-0 right-0 w-64 h-64 bg-secondary rounded-full blur-3xl"></div>
                        </div>
                        
                        <div class="relative z-10">
                            <div class="grid lg:grid-cols-2 gap-8 items-center">
                                <!-- Content -->
                                <div class="space-y-6">
                                    <div class="inline-flex items-center bg-white/20 dark:bg-black/20 backdrop-blur-sm rounded-full px-4 py-2 text-sm font-medium">
                                        <div class="w-2 h-2 bg-accent rounded-full mr-2 animate-pulse"></div>
                                        Featured This Week
                                    </div>
                                    
                                    <h1 class="text-4xl lg:text-6xl font-black leading-tight text-shadow">
                                        <span class="bg-gradient-to-r from-primary via-secondary to-accent bg-clip-text text-transparent">
                                            Discover
                                        </span>
                                        <br>
                                        <span class="text-gray-900 dark:text-white">Amazing Cinema</span>
                                    </h1>
                                    
                                    <p class="text-lg lg:text-xl text-gray-600 dark:text-gray-300 leading-relaxed">
                                        Explore the latest movies, read expert reviews, and discover your next favorite film 
                                        in our modern cinema hub.
                                    </p>
                                    
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <button class="group bg-primary text-white px-8 py-4 rounded-2xl font-semibold text-lg hover:bg-primary/90 transition-all flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                            Watch Now
                                        </button>
                                        <button class="border-2 border-primary text-primary dark:text-white hover:bg-primary hover:text-white px-8 py-4 rounded-2xl font-semibold text-lg transition-all">
                                            Browse Library
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Featured Movie Card -->
                                <?php if (!empty($featured_movies)): ?>
                                    <div class="relative">
                                        <?php $hero_movie = $featured_movies[0]; ?>
                                        <div class="card-3d bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-2xl">
                                            <div class="relative h-80">
                                                <img src="<?php echo $hero_movie['poster']; ?>" 
                                                     alt="<?php echo htmlspecialchars($hero_movie['title']); ?>"
                                                     class="w-full h-full object-cover">
                                                
                                                <!-- Overlay -->
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                                
                                                <!-- Play Button -->
                                                <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M8 5v14l11-7z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                
                                                <!-- Rating -->
                                                <div class="absolute top-4 right-4 bg-black/70 backdrop-blur-sm text-white px-3 py-1 rounded-full font-semibold text-sm">
                                                    ★ <?php echo $hero_movie['rating']; ?>
                                                </div>
                                            </div>
                                            
                                            <div class="p-6">
                                                <div class="flex items-center space-x-2 mb-3">
                                                    <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-medium">
                                                        <?php echo $hero_movie['genre']; ?>
                                                    </span>
                                                    <span class="text-gray-500"><?php echo $hero_movie['year']; ?></span>
                                                </div>
                                                
                                                <h3 class="text-xl font-bold mb-2">
                                                    <?php echo htmlspecialchars($hero_movie['title']); ?>
                                                </h3>
                                                
                                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                                    <?php echo $hero_movie['description']; ?>
                                                </p>
                                                
                                                <button class="w-full bg-gradient-to-r from-primary to-secondary text-white py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
                                                    Watch Trailer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Featured Movies Grid -->
                    <section>
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-3xl font-bold">Featured Movies</h2>
                            <button class="text-primary hover:text-primary/80 font-semibold flex items-center">
                                View All
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach (array_slice($featured_movies, 1, 6) as $movie): ?>
                                <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 group">
                                    <div class="relative h-48 overflow-hidden">
                                        <img src="<?php echo $movie['poster']; ?>" 
                                             alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        
                                        <!-- Hover Overlay -->
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <!-- Rating Badge -->
                                        <div class="absolute top-3 right-3 bg-black/70 backdrop-blur-sm text-white px-2 py-1 rounded-full text-xs font-semibold">
                                            ★ <?php echo $movie['rating']; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="bg-primary/10 text-primary px-2 py-1 rounded-full text-xs font-medium">
                                                <?php echo $movie['genre']; ?>
                                            </span>
                                            <span class="text-gray-500 text-sm"><?php echo $movie['year']; ?></span>
                                        </div>
                                        
                                        <h3 class="font-bold text-lg mb-1 group-hover:text-primary transition-colors">
                                            <?php echo htmlspecialchars($movie['title']); ?>
                                        </h3>
                                        
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                                            <?php echo $movie['description']; ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Categories Grid -->
                    <section>
                        <h2 class="text-3xl font-bold mb-8">Browse by Genre</h2>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <?php foreach ($categories as $category): ?>
                                <div class="group relative bg-gradient-to-br <?php echo $category['color']; ?> rounded-2xl p-6 text-white cursor-pointer hover:scale-105 transition-transform">
                                    <div class="relative z-10">
                                        <h3 class="text-xl font-bold mb-2"><?php echo $category['name']; ?></h3>
                                        <p class="text-white/80 text-sm"><?php echo number_format($category['count']); ?> movies</p>
                                    </div>
                                    
                                    <!-- Background Icon -->
                                    <div class="absolute bottom-2 right-2 opacity-20">
                                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                </div>

                <!-- Sidebar -->
                <aside class="sidebar-sticky space-y-8">
                    
                    <!-- Search Widget -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                        <h3 class="text-xl font-bold mb-4">Quick Search</h3>
                        <div class="relative">
                            <input type="text" 
                                   placeholder="Search movies..." 
                                   class="w-full px-4 py-3 text-base bg-gray-100 dark:bg-gray-700 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary">
                            <button class="absolute right-2 top-1/2 transform -translate-y-1/2 p-2 text-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Top Rated Movies -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold">Top Rated</h3>
                            <span class="bg-accent/10 text-accent px-2 py-1 rounded-full text-xs font-semibold">HOT</span>
                        </div>
                        
                        <div class="space-y-3">
                            <?php foreach (array_slice($top_rated, 0, 5) as $index => $movie): ?>
                                <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <div class="w-8 h-8 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        <?php echo $index + 1; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-sm truncate"><?php echo $movie['title']; ?></h4>
                                        <div class="flex items-center space-x-2 text-xs text-gray-500">
                                            <span><?php echo $movie['year']; ?></span>
                                            <span>•</span>
                                            <span class="text-accent font-semibold">★ <?php echo $movie['rating']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Coming Soon -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center space-x-2 mb-4">
                            <h3 class="text-xl font-bold">Coming Soon</h3>
                            <div class="w-2 h-2 bg-secondary rounded-full animate-pulse"></div>
                        </div>
                        
                        <div class="space-y-4">
                            <?php foreach ($new_releases as $release): ?>
                                <div class="border-l-4 border-primary pl-4">
                                    <h4 class="font-semibold"><?php echo $release['title']; ?></h4>
                                    <div class="flex items-center justify-between text-sm text-gray-500 mt-1">
                                        <span><?php echo $release['genre']; ?></span>
                                        <span class="bg-primary/10 text-primary px-2 py-1 rounded-full text-xs font-medium">
                                            <?php echo $release['coming']; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Newsletter Signup -->
                    <div class="bg-gradient-to-br from-primary to-secondary rounded-2xl p-6 text-white">
                        <h3 class="text-xl font-bold mb-3">Stay Updated</h3>
                        <p class="text-white/80 text-sm mb-4">Get the latest movie reviews and recommendations.</p>
                        
                        <div class="space-y-3">
                            <input type="email" 
                                   placeholder="Enter your email" 
                                   class="w-full px-4 py-3 text-base bg-white/20 border-0 rounded-xl placeholder-white/70 text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                            <button class="w-full bg-white text-primary py-3 rounded-xl font-semibold hover:bg-gray-100 transition-colors">
                                Subscribe
                            </button>
                        </div>
                    </div>

                </aside>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 dark:bg-gray-900 py-16 mt-20">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-2xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8.5 2C9.3 2 10 2.7 10 3.5C10 4.3 9.3 5 8.5 5S7 4.3 7 3.5C7 2.7 7.7 2 8.5 2M12.5 2C13.3 2 14 2.7 14 3.5C14 4.3 13.3 5 12.5 5S11 4.3 11 3.5C11 2.7 11.7 2 12.5 2M16.5 2C17.3 2 18 2.7 18 3.5C18 4.3 17.3 5 16.5 5S15 4.3 15 3.5C15 2.7 15.7 2 16.5 2M6 6L7 18C7.1 19.1 8 20 9 20H17C18 20 18.9 19.1 19 18L20 6H6Z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">Popcorn Media</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">Your modern destination for premium cinema experiences and movie reviews.</p>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li><a href="#" class="hover:text-primary transition-colors">Movies</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">TV Shows</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Reviews</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Genres</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li><a href="#" class="hover:text-primary transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Contact</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Privacy</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Terms</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Connect</h4>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 bg-primary/20 rounded-xl flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-primary/20 rounded-xl flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-primary/20 rounded-xl flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.719-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.347-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-200 dark:border-gray-800 mt-12 pt-8 text-center text-gray-600 dark:text-gray-400">
                <p>&copy; 2024 Popcorn Media. All rights reserved. Made with ❤️ for movie lovers.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation
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

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards
        document.querySelectorAll('.card-3d, .bg-white, .bg-gray-800').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Search functionality
        const searchInput = document.querySelector('input[type="text"]');
        searchInput?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                // Add your search functionality here
                console.log('Searching for:', this.value);
            }
        });
    </script>
</body>
</html>