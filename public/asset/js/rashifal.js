// rashifal.js
console.log("rashifal.js loaded");

function initRashifal() {
    const rashifals = {
        aries: { title: "आपके तारे - दैनिक: मेष", text: "आज का दिन ऊर्जावान रहेगा। करियर में प्रगति होगी। शुभ रंग: लाल। उपाय: हनुमान जी को प्रसाद चढ़ाएँ।" },
        taurus: { title: "आपके तारे - दैनिक: वृषभ", text: "मन की चिंता दूर होगी। शिक्षा से जुड़े लोगों को लाभ होगा। परिवार से संबंधित काम बनेंगे। शुभ रंग: गुलाबी। उपाय: गरीब को भोजन दान करें।" },
        gemini: { title: "आपके तारे - दैनिक: मिथुन", text: "नई योजनाएँ बनेंगी। भाई-बहनों का सहयोग मिलेगा। शुभ रंग: हरा। उपाय: तुलसी में जल दें।" },
        cancer: { title: "आपके तारे - दैनिक: कर्क", text: "परिवार में खुशी का माहौल रहेगा। स्वास्थ्य अच्छा रहेगा। शुभ रंग: सफेद। उपाय: दूध का दान करें।" },
        leo: { title: "आपके तारे - दैनिक: सिंह", text: "आत्मविश्वास बढ़ेगा। नया काम शुरू करने का समय शुभ है। शुभ रंग: सुनहरा। उपाय: सूर्य को जल अर्पित करें।" },
        virgo: { title: "आपके तारे - दैनिक: कन्या", text: "कामकाज में सफलता मिलेगी। मित्रों का सहयोग रहेगा। शुभ रंग: हरा। उपाय: गणेश जी की पूजा करें।" },
        libra: { title: "आपके तारे - दैनिक: तुला", text: "नए अवसर प्राप्त होंगे। दांपत्य जीवन सुखमय रहेगा। शुभ रंग: नीला। उपाय: देवी की आराधना करें।" },
        scorpio: { title: "आपके तारे - दैनिक: वृश्चिक", text: "धन लाभ होगा। रुके हुए कार्य पूरे होंगे। शुभ रंग: लाल। उपाय: जल में तिल डालकर स्नान करें।" },
        sagittarius: { title: "आपके तारे - दैनिक: धनु", text: "यात्रा लाभदायक रहेगी। भाग्य का साथ मिलेगा। शुभ रंग: पीला। उपाय: केले के पेड़ में जल चढ़ाएँ।" },
        capricorn: { title: "आपके तारे - दैनिक: मकर", text: "कठिन परिश्रम से सफलता मिलेगी। बुजुर्गों का आशीर्वाद प्राप्त होगा। शुभ रंग: काला। उपाय: शनि देव की पूजा करें।" },
        aquarius: { title: "आपके तारे - दैनिक: कुंभ", text: "रुके हुए काम पूरे होंगे। मित्रों से सहयोग मिलेगा। शुभ रंग: नीला। उपाय: जरूरतमंद की मदद करें।" },
        pisces: { title: "आपके तारे - दैनिक: मीन", text: "धार्मिक कार्यों में रुचि बढ़ेगी। आर्थिक स्थिति मजबूत होगी। शुभ रंग: हल्का पीला। उपाय: विष्णु भगवान की पूजा करें।" }
    };

    const slider = document.querySelector('.rashifal-slider');
    if (!slider) {
        console.warn("Rashifal slider not found.");
        return;
    }

    const titleEl = document.getElementById('rashifal-title');
    const textEl = document.getElementById('rashifal-text');
    const prevBtn = document.querySelector('.nav-btn.prev');
    const nextBtn = document.querySelector('.nav-btn.next');

    function centerIcon(icon) {
        if (!icon) return;
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

        const sign = icon.dataset.sign;
        setActiveIcon(icon);

        if (rashifals[sign]) {
            titleEl.textContent = rashifals[sign].title;
            textEl.textContent = rashifals[sign].text;
        }

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
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            slider.scrollBy({ left: -120, behavior: 'smooth' });
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            slider.scrollBy({ left: 120, behavior: 'smooth' });
        });
    }

    // Mobile swipe
    slider.addEventListener('touchstart', (e) => {
        slider._startX = e.touches[0].pageX - slider.offsetLeft;
        slider._scrollLeft = slider.scrollLeft;
    }, { passive: true });

    slider.addEventListener('touchmove', (e) => {
        const x = e.touches[0].pageX - slider.offsetLeft;
        const walk = (x - slider._startX) * 2;
        slider.scrollLeft = slider._scrollLeft - walk;
    }, { passive: true });

    // If initial icon exists, center it
    const activeIcon = slider.querySelector('img.active') || slider.querySelector('img');
    if (activeIcon) centerIcon(activeIcon);

    console.log("Rashifal initialized");
}

// Auto-init when page is ready
document.addEventListener('DOMContentLoaded', initRashifal);
