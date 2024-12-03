let currentSlide = 0;

function showSlide(index) {
    const slides = document.querySelectorAll('.slide');

    // Wrap around if index is out of bounds
    if (index >= slides.length) currentSlide = 0;
    else if (index < 0) currentSlide = slides.length - 1;
    else currentSlide = index;

    // Hide all slides, then show the active slide
    slides.forEach((slide, i) => {
        slide.classList.remove('active');
        if (i === currentSlide) slide.classList.add('active');
    });
}

function changeSlide(direction) {
    showSlide(currentSlide + direction);
}

// Initialize the first slide as active
showSlide(currentSlide);
