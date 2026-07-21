<!-- Slider Component for Members Page -->
<?php if (!empty($sliders) && count($sliders) > 0): ?>
<div class="slider-container" style="margin-bottom:24px;position:relative;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
    <div class="slider-wrapper" id="membersSlider" style="display:flex;transition:transform 0.5s ease-in-out;height:280px;">
        <?php foreach ($sliders as $index => $slider): ?>
        <div class="slide-item" data-index="<?php echo $index; ?>" 
             style="min-width:100%;height:100%;position:relative;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#065F46,#059669);">
            <!-- Background Image with Overlay -->
            <div style="position:absolute;inset:0;z-index:1;">
                <img src="<?php echo asset($slider['image_path']); ?>" alt="<?php echo e($slider['title']); ?>" 
                     style="width:100%;height:100%;object-fit:cover;opacity:0.3;">
            </div>
            
            <!-- Content -->
            <div style="position:relative;z-index:2;text-align:center;color:#fff;padding:40px;max-width:800px;">
                <h2 style="font-size:2rem;font-weight:700;margin-bottom:16px;text-shadow:0 2px 8px rgba(0,0,0,0.3);">
                    <?php echo e($slider['title']); ?>
                </h2>
                <?php if (!empty($slider['description'])): ?>
                <p style="font-size:1.1rem;line-height:1.8;opacity:0.95;margin-bottom:24px;">
                    <?php echo e($slider['description']); ?>
                </p>
                <?php endif; ?>
                <?php if (!empty($slider['link_url'])): ?>
                <a href="<?php echo e($slider['link_url']); ?>" 
                   class="btn btn-lg" 
                   style="background:#fff;color:#065F46;font-weight:600;padding:12px 32px;border-radius:50px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all 0.3s;box-shadow:0 4px 15px rgba(0,0,0,0.2);">
                    <span>بیشتر بدانید</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Navigation Arrows -->
    <button class="slider-nav prev" onclick="moveSlide(-1)" 
            style="position:absolute;left:16px;top:50%;transform:translateY(-50%);z-index:3;width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,0.9);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 15px rgba(0,0,0,0.2);transition:all 0.3s;">
        <i class="fas fa-chevron-right" style="color:#065F46;font-size:1.2rem;"></i>
    </button>
    <button class="slider-nav next" onclick="moveSlide(1)" 
            style="position:absolute;right:16px;top:50%;transform:translateY(-50%);z-index:3;width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,0.9);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 15px rgba(0,0,0,0.2);transition:all 0.3s;">
        <i class="fas fa-chevron-left" style="color:#065F46;font-size:1.2rem;"></i>
    </button>

    <!-- Dots Indicator -->
    <div class="slider-dots" style="position:absolute;bottom:20px;left:50%;transform:translateX(-50%);z-index:3;display:flex;gap:10px;">
        <?php foreach ($sliders as $index => $slider): ?>
        <button class="slider-dot" onclick="goToSlide(<?php echo $index; ?>)" 
                data-index="<?php echo $index; ?>"
                style="width:12px;height:12px;border-radius:50%;border:2px solid #fff;background:<?php echo $index === 0 ? '#fff' : 'rgba(255,255,255,0.5)'; ?>;cursor:pointer;transition:all 0.3s;"></button>
        <?php endforeach; ?>
    </div>
</div>

<script>
let currentSlide = 0;
const totalSlides = <?php echo count($sliders); ?>;
let autoSlideInterval;

function updateSlider() {
    const wrapper = document.getElementById('membersSlider');
    if (!wrapper) return;
    
    wrapper.style.transform = 'translateX(-' + (currentSlide * 100) + '%)';
    
    // Update dots
    document.querySelectorAll('.slider-dot').forEach(function(dot, index) {
        dot.style.background = index === currentSlide ? '#fff' : 'rgba(255,255,255,0.5)';
    });
}

function moveSlide(direction) {
    currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
    updateSlider();
    resetAutoSlide();
}

function goToSlide(index) {
    currentSlide = index;
    updateSlider();
    resetAutoSlide();
}

function resetAutoSlide() {
    if (autoSlideInterval) clearInterval(autoSlideInterval);
    autoSlideInterval = setInterval(function() {
        moveSlide(1);
    }, 5000);
}

// Initialize slider
document.addEventListener('DOMContentLoaded', function() {
    resetAutoSlide();
    
    // Touch support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    const sliderContainer = document.querySelector('.slider-container');
    
    if (sliderContainer) {
        sliderContainer.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, false);
        
        sliderContainer.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, false);
    }
    
    function handleSwipe() {
        const swipeThreshold = 50;
        if (touchStartX - touchEndX > swipeThreshold) {
            moveSlide(1); // Swipe left - next slide
        } else if (touchEndX - touchStartX > swipeThreshold) {
            moveSlide(-1); // Swipe right - previous slide
        }
    }
});

// Responsive styles
window.addEventListener('resize', function() {
    const sliderWrapper = document.getElementById('membersSlider');
    if (window.innerWidth < 768) {
        if (sliderWrapper) sliderWrapper.style.height = '220px';
    } else {
        if (sliderWrapper) sliderWrapper.style.height = '280px';
    }
});
</script>

<style>
.slider-container:hover .slider-nav {
    opacity: 1;
}
.slider-nav:hover {
    background: #fff;
    transform: translateY(-50%) scale(1.1);
}
.slider-dot:hover {
    background: #fff !important;
    transform: scale(1.2);
}
@media (max-width: 768px) {
    .slider-container h2 {
        font-size: 1.4rem !important;
    }
    .slider-container p {
        font-size: 0.95rem !important;
    }
    .slider-nav {
        width: 40px !important;
        height: 40px !important;
    }
}
</style>
<?php endif; ?>
