// Intersection Observer para animaciones de entrada
document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer para animar las tarjetas de valores cuando entren en el viewport
    const valorCards = document.querySelectorAll('.valor-card');
    
    const valorObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const cards = Array.from(valorCards);
                const index = cards.indexOf(entry.target);
                
                setTimeout(() => {
                    entry.target.classList.add('animate');
                }, index * 200); // Delay escalonado de 200ms entre tarjetas
                valorObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.2,
        rootMargin: '0px 0px -100px 0px'
    });
    
    valorCards.forEach(card => {
        valorObserver.observe(card);
    });

    // Intersection Observer para reproducir automáticamente el video cuando sea 70% visible
    const elegirVideo = document.getElementById('elegir-video');
    
    if (elegirVideo) {
        const videoObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Reproducir el video automáticamente
                    elegirVideo.play().catch(error => {
                        console.log('Error al reproducir el video automáticamente:', error);
                    });
                } else {
                    // Pausar el video cuando no esté visible
                    elegirVideo.pause();
                }
            });
        }, {
            threshold: 0.7, // Se activa cuando el 70% del video es visible
            rootMargin: '0px'
        });
        
        videoObserver.observe(elegirVideo);
        // Configurar Intersection Observer para animaciones
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observar elementos para animación del header
        const headerElements = document.querySelectorAll('.header-text-container, .company-title, .company-description');
        headerElements.forEach(el => observer.observe(el));
        
        // Observar elementos de misión y visión
        const misionVisionElements = document.querySelectorAll('.mision-vision-cards, .mision-card, .vision-card, .mision-section, .vision-section, .families-image');
        misionVisionElements.forEach(el => observer.observe(el));
        
        // Observar otros elementos (excluyendo valor-card para evitar conflicto)
        const otherElements = document.querySelectorAll('.timeline-item');
        otherElements.forEach(el => observer.observe(el));
    }

    // Intersection Observer para animar las tarjetas de "¿Por qué elegir?"
    const reasonItems = document.querySelectorAll('.reason-item');
    
    if (reasonItems.length > 0) {
        const reasonObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px 0px -50px 0px'
        });
        
        reasonItems.forEach(item => {
            reasonObserver.observe(item);
        });
    }

    // Header Slider funcionalidad
    const sliderTrack = document.getElementById('sliderTrack');
    const sliderDots = document.querySelectorAll('.slider-dot');
    const sliderPrev = document.getElementById('sliderPrev');
    const sliderNext = document.getElementById('sliderNext');
    
    let currentHeaderSlide = 0;
    const totalHeaderSlides = 5;
    
    function updateHeaderSlider() {
        const translateX = -currentHeaderSlide * 20; // 20% por cada slide
        sliderTrack.style.transform = `translateX(${translateX}%)`;
        
        // Actualizar indicadores
        sliderDots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentHeaderSlide);
        });
    }
    
    function nextHeaderSlide() {
        currentHeaderSlide = (currentHeaderSlide + 1) % totalHeaderSlides;
        updateHeaderSlider();
    }
    
    function prevHeaderSlide() {
        currentHeaderSlide = (currentHeaderSlide - 1 + totalHeaderSlides) % totalHeaderSlides;
        updateHeaderSlider();
    }
    
    // Event listeners para navegación
    if (sliderNext) {
        sliderNext.addEventListener('click', nextHeaderSlide);
    }
    
    if (sliderPrev) {
        sliderPrev.addEventListener('click', prevHeaderSlide);
    }
    
    // Event listeners para indicadores
    sliderDots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentHeaderSlide = index;
            updateHeaderSlider();
        });
    });
    
    // Auto-play del slider (opcional)
    setInterval(nextHeaderSlide, 5000); // Cambia cada 5 segundos
});
