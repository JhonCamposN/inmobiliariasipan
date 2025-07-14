document.addEventListener('DOMContentLoaded', function () {
    // Menú hamburguesa
    const menuToggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.menu-superior');
    menuToggle.addEventListener('click', function () {
        menu.classList.toggle('open');
        document.body.classList.toggle('menu-open');
    });
    // Cerrar menú al hacer click en un enlace
    menu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function () {
            menu.classList.remove('open');
            document.body.classList.remove('menu-open');
        });
    });

    // Scroll suave y animación de deslizamiento
    function scrollToSection(selector) {
        const target = document.querySelector(selector);
        if (!target) return;
        const y = target.getBoundingClientRect().top + window.pageYOffset - 60;
        window.scrollTo({
            top: y,
            behavior: 'smooth'
        });
        target.classList.add('slide-section');
        setTimeout(() => target.classList.remove('slide-section'), 900);
    }
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href.length > 1 && document.querySelector(href)) {
                e.preventDefault();
                scrollToSection(href);
            }
        });
    });

    // Reproducir video cuando esté al 85% visible, y luego dejarlo correr sin pausar
    const videoProyecto = document.querySelector('.proyecto-video video');
    if (videoProyecto) {
        let started = false;
        const observer = new IntersectionObserver(
            (entries, obs) => {
                entries.forEach(entry => {
                    if (!started && entry.isIntersecting && entry.intersectionRatio >= 0.85) {
                        videoProyecto.play();
                        started = true;
                        obs.disconnect(); // Deja de observar, ya no pausará el video
                    }
                });
            },
            {
                threshold: [0, 0.85]
            }
        );
        observer.observe(videoProyecto);
    }
});