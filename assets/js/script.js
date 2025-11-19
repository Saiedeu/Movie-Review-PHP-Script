/**
 * Main JavaScript for MSR Website
 */

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    initializeLazyLoading();
    initializeScrollAnimations();
    initializeSearchFunctionality();
    initializeFormValidations();
    initializeImageGallery();
});

// Lazy Loading for Images
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy-image');
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => {
            img.classList.add('lazy-image');
            imageObserver.observe(img);
        });
    } else {
        // Fallback for older browsers
        images.forEach(img => {
            img.src = img.dataset.src;
            img.classList.add('loaded');
        });
    }
}

// Scroll Animations
function initializeScrollAnimations() {
    const animatedElements = document.querySelectorAll('[data-aos]');
    
    if ('IntersectionObserver' in window) {
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const animationType = element.dataset.aos;
                    
                    element.classList.add('fade-in');
                    
                    // Add specific animation classes
                    switch(animationType) {
                        case 'fade-up':
                            element.classList.add('slide-up');
                            break;
                        case 'zoom-in':
                            element.classList.add('scale-in');
                            break;
                    }
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        animatedElements.forEach(element => {
            animationObserver.observe(element);
        });
    }
}

// Search Functionality
function initializeSearchFunctionality() {
    const searchInput = document.getElementById('search-input');
    const searchForm = document.querySelector('.search-form');
    
    if (searchInput) {
        // Real-time search suggestions (if needed)
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 3) {
                searchTimeout = setTimeout(() => {
                    // Implement search suggestions here
                    fetchSearchSuggestions(query);
                }, 300);
            }
        });
        
        // Search form submission
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                const query = searchInput.value.trim();
                if (!query) {
                    e.preventDefault();
                    showAlert('অনুসন্ধানের জন্য কিছু লিখুন', 'warning');
                }
            });
        }
    }
}

// Form Validations
function initializeFormValidations() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    });
}

// Form Validation Helper
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    return isValid;
}

// Field Validation
function validateField(field) {
    const value = field.value.trim();
    const fieldType = field.type;
    const isRequired = field.hasAttribute('required');
    
    // Remove previous error classes
    field.classList.remove('border-red-500', 'border-green-500');
    
    // Check if required field is empty
    if (isRequired && !value) {
        showFieldError(field, 'এই ফিল্ডটি আবশ্যক');
        return false;
    }
    
    // Email validation
    if (fieldType === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showFieldError(field, 'সঠিক ইমেইল ঠিকানা দিন');
            return false;
        }
    }
    
    // Phone validation
    if (field.name === 'phone' && value) {
        const phoneRegex = /^[০-৯0-9+\-\s()]+$/;
        if (!phoneRegex.test(value)) {
            showFieldError(field, 'সঠিক ফোন নম্বর দিন');
            return false;
        }
    }
    
    // Success state
    field.classList.add('border-green-500');
    hideFieldError(field);
    return true;
}

// Show field error
function showFieldError(field, message) {
    field.classList.add('border-red-500');
    
    // Remove existing error message
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message text-red-500 text-sm mt-1';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

// Hide field error
function hideFieldError(field) {
    const errorMessage = field.parentNode.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
}

// Image Gallery
function initializeImageGallery() {
    const galleryImages = document.querySelectorAll('.gallery-image');
    
    galleryImages.forEach(img => {
        img.addEventListener('click', function() {
            openImageModal(this.src, this.alt);
        });
    });
}

// Open Image Modal
function openImageModal(src, alt) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="relative max-w-4xl max-h-full p-4">
            <img src="${src}" alt="${alt}" class="max-w-full max-h-full object-contain">
            <button class="absolute top-4 right-4 text-white text-xl bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75 transition-colors" onclick="this.closest('.fixed').remove()">
                ×
            </button>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close on outside click
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.remove();
        }
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            modal.remove();
        }
    });
}

// Show Alert
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    const bgColor = {
        'success': 'bg-green-500',
        'error': 'bg-red-500',
        'warning': 'bg-yellow-500',
        'info': 'bg-blue-500'
    };
    
    alertDiv.className = `fixed top-4 right-4 ${bgColor[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300`;
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alertDiv.classList.add('opacity-0', 'translate-x-full');
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

// Search Suggestions (placeholder)
async function fetchSearchSuggestions(query) {
    try {
        // Implement API call here
        const response = await fetch(`/api/search-suggestions?q=${encodeURIComponent(query)}`);
        const suggestions = await response.json();
        
        displaySearchSuggestions(suggestions);
    } catch (error) {
        console.error('Search suggestions error:', error);
    }
}

// Display Search Suggestions
function displaySearchSuggestions(suggestions) {
    const suggestionsContainer = document.getElementById('search-suggestions');
    
    if (!suggestionsContainer || !suggestions.length) {
        return;
    }
    
    suggestionsContainer.innerHTML = '';
    
    suggestions.forEach(suggestion => {
        const suggestionDiv = document.createElement('div');
        suggestionDiv.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0';
        suggestionDiv.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-8 h-10 bg-gray-200 rounded flex-shrink-0"></div>
                <div>
                    <div class="font-medium text-gray-900">${suggestion.title}</div>
                    <div class="text-sm text-gray-600">${suggestion.year} • ${suggestion.type}</div>
                </div>
            </div>
        `;
        
        suggestionDiv.addEventListener('click', () => {
            window.location.href = `/review/${suggestion.slug}`;
        });
        
        suggestionsContainer.appendChild(suggestionDiv);
    });
    
    suggestionsContainer.classList.remove('hidden');
}

// Smooth Scroll to Element
function scrollToElement(elementId, offset = 0) {
    const element = document.getElementById(elementId);
    if (element) {
        const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = elementPosition - offset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
}

// Copy to Clipboard
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showAlert('কপি করা হয়েছে!', 'success');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showAlert('কপি করা হয়েছে!', 'success');
    }
}

// Format Numbers in Bengali
function formatBengaliNumber(number) {
    const bengaliNumbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    const englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    
    let formattedNumber = number.toString();
    
    for (let i = 0; i < englishNumbers.length; i++) {
        formattedNumber = formattedNumber.replace(new RegExp(englishNumbers[i], 'g'), bengaliNumbers[i]);
    }
    
    return formattedNumber;
}

// Check if element is in viewport
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Debounce function
function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func(...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func(...args);
    };
}

// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Performance monitoring
function trackPageLoad() {
    window.addEventListener('load', () => {
        const loadTime = window.performance.timing.loadEventEnd - window.performance.timing.navigationStart;
        console.log(`Page load time: ${loadTime}ms`);
        
        // Send analytics data if needed
        if (typeof gtag !== 'undefined') {
            gtag('event', 'page_load_time', {
                value: loadTime,
                custom_parameter: 'milliseconds'
            });
        }
    });
}

// Initialize performance tracking
trackPageLoad();

// Export functions for global use
window.MSR = {
    showAlert,
    copyToClipboard,
    scrollToElement,
    formatBengaliNumber,
    isInViewport,
    debounce,
    throttle
};