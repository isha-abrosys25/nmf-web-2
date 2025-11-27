function initRashifal() {
    const slider = document.querySelector('.rashifal-slider');
    if (!slider) return;

    const titleEl = document.getElementById('rashifal-title');
    const textEl = document.getElementById('rashifal-text');
    const prevBtn = document.querySelector('.nav-btn.prev');
    const nextBtn = document.querySelector('.nav-btn.next');

    function centerIcon(icon) {
        const containerWidth = slider.clientWidth;
        const iconCenter = icon.offsetLeft + icon.offsetWidth / 2;
        const scrollPosition = Math.max(0, iconCenter - containerWidth / 2);
        slider.scrollTo({ left: scrollPosition, behavior: 'smooth' });
    }

    function setActiveIcon(icon) {
        slider.querySelectorAll('img').forEach(img => img.classList.remove('active'));
        icon.classList.add('active');
    }

    // Click delegation
    slider.addEventListener('click', (e) => {
        const icon = e.target.closest('img');
        if (!icon) return;

        setActiveIcon(icon);

        //  Use dynamic DB values
        titleEl.textContent = "आपके तारे - दैनिक: " + icon.dataset.title;
        textEl.textContent = icon.dataset.description;

        centerIcon(icon);
    });

    // Keyboard support
    slider.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            const icon = e.target.closest('img');
            if (icon) icon.click();
        }
    });

    // Scroll buttons
    prevBtn?.addEventListener('click', () => slider.scrollBy({ left: -120, behavior: 'smooth' }));
    nextBtn?.addEventListener('click', () => slider.scrollBy({ left: 120, behavior: 'smooth' }));

    // Swipe support
    slider.addEventListener('touchstart', (e) => {
        slider._startX = e.touches[0].pageX - slider.offsetLeft;
        slider._scrollLeft = slider.scrollLeft;
    }, { passive: true });

    slider.addEventListener('touchmove', (e) => {
        const x = e.touches[0].pageX - slider.offsetLeft;
        const walk = (x - slider._startX) * 2;
        slider.scrollLeft = slider._scrollLeft - walk;
    }, { passive: true });

    // Auto-set first item
    const activeIcon = slider.querySelector('img.active') || slider.querySelector('img');
    if (activeIcon) {
        titleEl.textContent = "आपके तारे - दैनिक: " + activeIcon.dataset.title;
        textEl.textContent = activeIcon.dataset.description;
        centerIcon(activeIcon);
    }

    console.log("Rashifal initialized");
}

document.addEventListener('DOMContentLoaded', initRashifal);