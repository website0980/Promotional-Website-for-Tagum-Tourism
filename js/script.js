// Carousel functionality
let currentSlideIndex = 1;
const totalSlides = document.querySelectorAll('.carousel-slide').length;

function changeSlide(n) {
    showSlide((currentSlideIndex += n));
}

function currentSlide(n) {
    showSlide((currentSlideIndex = n));
}

function showSlide(n) {
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.dot');

    // Wrap around
    if (n > slides.length) {
        currentSlideIndex = 1;
    }
    if (n < 1) {
        currentSlideIndex = slides.length;
    }

    // Hide all slides
    slides.forEach(slide => {
        slide.classList.remove('active');
    });

    // Remove active class from all dots
    dots.forEach(dot => {
        dot.classList.remove('active');
    });

    // Show current slide
    slides[currentSlideIndex - 1].classList.add('active');

    // Highlight current dot
    dots[currentSlideIndex - 1].classList.add('active');
}

// Auto-advance carousel every 6 seconds
setInterval(() => {
    changeSlide(1);
}, 6000);

// Smooth scrolling for navigation links and buttons
document.querySelectorAll('.smooth-scroll, a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const href = this.getAttribute('href');
        if (href && href.startsWith('#')) {
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// Initialize carousel on page load
document.addEventListener('DOMContentLoaded', function() {
    showSlide(currentSlideIndex);

    // Hide images that fail to load to remove broken picture icons
    document.querySelectorAll('img').forEach(img => {
        if (!img.getAttribute('src')) {
            img.style.display = 'none';
            return;
        }
        img.addEventListener('error', function() {
            this.style.display = 'none';
        });
    });
});
