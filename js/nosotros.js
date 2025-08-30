// Intersection Observer para animaciones de entrada
document.addEventListener('DOMContentLoaded', function() {
    // Configurar el observer para las tarjetas de valores
    const observerOptions = {
        threshold: 0.3,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Agregar clase animate a todas las tarjetas de valores
                const valorCards = entry.target.querySelectorAll('.valor-card');
                valorCards.forEach(card => {
                    card.classList.add('animate');
                });
                
                // Dejar de observar una vez que se activa
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observar la sección de valores
    const valoresContainer = document.querySelector('.valores-container');
    if (valoresContainer) {
        observer.observe(valoresContainer);
    }
});
