/**
 * Hero Slider JavaScript
 * Controla a funcionalidade do slider na seção principal
 */
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const sliderContainer = document.querySelector('.slider-container');
    const sliderItems = document.querySelectorAll('.slider-item');
    const sliderDots = document.querySelector('.slider-dots');
    const prevButton = document.querySelector('.slider-prev');
    const nextButton = document.querySelector('.slider-next');
    
    // Variables
    let currentSlideIndex = 0;
    let slideInterval;
    const slideDelay = 5000; // 5 seconds per slide
    
    // Initialize the slider
    function initSlider() {
        // Create dot indicators
        sliderItems.forEach((_, index) => {
            const dot = document.createElement('button');
            dot.classList.add('slider-dot');
            dot.setAttribute('aria-label', `Slide ${index + 1}`);
            dot.setAttribute('role', 'tab');
            dot.setAttribute('aria-selected', index === 0 ? 'true' : 'false');
            
            if (index === 0) dot.classList.add('active');
            
            dot.addEventListener('click', () => {
                goToSlide(index);
                resetInterval();
            });
            
            sliderDots.appendChild(dot);
        });
        
        // Event listeners for controls
        prevButton.addEventListener('click', () => {
            goToPrevSlide();
            resetInterval();
        });
        
        nextButton.addEventListener('click', () => {
            goToNextSlide();
            resetInterval();
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                goToPrevSlide();
                resetInterval();
            } else if (e.key === 'ArrowRight') {
                goToNextSlide();
                resetInterval();
            }
        });
        
        // Swipe detection for mobile
        let touchStartX = 0;
        let touchEndX = 0;
        
        sliderContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        sliderContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });
        
        function handleSwipe() {
            const swipeThreshold = 50;
            if (touchEndX < touchStartX - swipeThreshold) {
                // Swipe left
                goToNextSlide();
                resetInterval();
            } else if (touchEndX > touchStartX + swipeThreshold) {
                // Swipe right
                goToPrevSlide();
                resetInterval();
            }
        }
        
        // Start automatic slideshow
        startInterval();
        
        // Pause slideshow on hover
        sliderContainer.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
        });
        
        sliderContainer.addEventListener('mouseleave', () => {
            startInterval();
        });
    }
    
    // Go to specific slide
    function goToSlide(index) {
        // Update slider items
        sliderItems.forEach((item, i) => {
            item.classList.toggle('active', i === index);
            item.setAttribute('aria-hidden', i !== index);
        });
        
        // Update dots
        const dots = sliderDots.querySelectorAll('.slider-dot');
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
            dot.setAttribute('aria-selected', i === index ? 'true' : 'false');
        });
        
        currentSlideIndex = index;
    }
    
    // Go to previous slide
    function goToPrevSlide() {
        let newIndex = currentSlideIndex - 1;
        if (newIndex < 0) {
            newIndex = sliderItems.length - 1;
        }
        goToSlide(newIndex);
    }
    
    // Go to next slide
    function goToNextSlide() {
        let newIndex = currentSlideIndex + 1;
        if (newIndex >= sliderItems.length) {
            newIndex = 0;
        }
        goToSlide(newIndex);
    }
    
    // Start automatic slideshow
    function startInterval() {
        slideInterval = setInterval(() => {
            goToNextSlide();
        }, slideDelay);
    }
    
    // Reset the interval timer
    function resetInterval() {
        clearInterval(slideInterval);
        startInterval();
    }
    
    // Initialize slider if elements exist
    if (sliderContainer && sliderItems.length > 0) {
        initSlider();
    }
});