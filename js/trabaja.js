// JavaScript para la página Trabaja con Nosotros

document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer para animaciones de entrada
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    // Animaciones para las tarjetas de razones
    const razonCards = document.querySelectorAll('.razon-card');
    
    const razonObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
                razonObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Aplicar estilos iniciales y observar tarjetas de razones
    razonCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        razonObserver.observe(card);
    });

    // Animaciones para los pasos del proceso
    const stepItems = document.querySelectorAll('.step-item');
    
    const stepObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 150);
                stepObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Aplicar estilos iniciales y observar pasos
    stepItems.forEach(step => {
        step.style.opacity = '0';
        step.style.transform = 'translateY(20px)';
        step.style.transition = 'all 0.5s ease';
        stepObserver.observe(step);
    });

    // Animación para las tarjetas de requisitos y beneficios
    const cards = document.querySelectorAll('.requisitos-card, .beneficios-card');
    
    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateX(0)';
                }, index * 200);
                cardObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Aplicar estilos iniciales y observar tarjetas
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transition = 'all 0.6s ease';
        
        // Alternar dirección de entrada
        if (index % 2 === 0) {
            card.style.transform = 'translateX(-30px)';
        } else {
            card.style.transform = 'translateX(30px)';
        }
        
        cardObserver.observe(card);
    });

    // Animación de contador para las estadísticas del hero
    const statNumbers = document.querySelectorAll('.stat-number');
    
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const finalValue = parseInt(target.textContent);
                const duration = 2000; // 2 segundos
                const increment = finalValue / (duration / 16); // 60 FPS
                let currentValue = 0;
                
                const counter = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        target.textContent = finalValue + '+';
                        clearInterval(counter);
                    } else {
                        target.textContent = Math.floor(currentValue) + '+';
                    }
                }, 16);
                
                statsObserver.unobserve(target);
            }
        });
    }, { threshold: 0.5 });

    // Observar números de estadísticas
    statNumbers.forEach(stat => {
        statsObserver.observe(stat);
    });

    // Efecto parallax suave para el hero
    const heroBackground = document.querySelector('.hero-background');
    
    if (heroBackground) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            heroBackground.style.transform = `translateY(${rate}px)`;
        });
    }

    // Smooth scroll para enlaces internos
    const smoothScrollLinks = document.querySelectorAll('a[href^="#"]');
    
    smoothScrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                const headerHeight = document.querySelector('.header').offsetHeight;
                const targetPosition = targetSection.offsetTop - headerHeight - 20;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Efecto hover mejorado para botones de WhatsApp
    const whatsappButtons = document.querySelectorAll('.btn-whatsapp-principal');
    
    whatsappButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Animación de escritura para títulos principales
    const mainTitles = document.querySelectorAll('.oportunidades-header h2, .por-que-header h2');
    
    const titleObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const title = entry.target;
                const text = title.textContent;
                title.textContent = '';
                title.style.borderRight = '2px solid #1e7bbf';
                
                let i = 0;
                const typeWriter = setInterval(() => {
                    if (i < text.length) {
                        title.textContent += text.charAt(i);
                        i++;
                    } else {
                        clearInterval(typeWriter);
                        setTimeout(() => {
                            title.style.borderRight = 'none';
                        }, 500);
                    }
                }, 100);
                
                titleObserver.unobserve(title);
            }
        });
    }, { threshold: 0.8 });

    // Observar títulos principales
    mainTitles.forEach(title => {
        titleObserver.observe(title);
    });

    // Efecto de partículas en el hero (opcional)
    function createParticles() {
        const heroContent = document.querySelector('.hero-content');
        if (!heroContent) return;
        
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.style.position = 'absolute';
            particle.style.width = '4px';
            particle.style.height = '4px';
            particle.style.background = 'rgba(255, 215, 0, 0.6)';
            particle.style.borderRadius = '50%';
            particle.style.pointerEvents = 'none';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animation = `float ${3 + Math.random() * 4}s ease-in-out infinite`;
            
            heroContent.appendChild(particle);
        }
    }

    // Crear partículas después de un pequeño delay
    setTimeout(createParticles, 1000);

    // Agregar animación CSS para las partículas
    const style = document.createElement('style');
    style.textContent = `
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
});

// Función para manejar el redimensionamiento de ventana
window.addEventListener('resize', function() {
    // Reajustar animaciones si es necesario
    const cards = document.querySelectorAll('.razon-card, .step-item');
    cards.forEach(card => {
        if (window.innerWidth <= 768) {
            card.style.transform = 'none';
        }
    });
});
