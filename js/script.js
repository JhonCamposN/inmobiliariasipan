document.addEventListener('DOMContentLoaded', function () {
    // Menú hamburguesa con overlay
    const menuToggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.menu-superior');
    const menuOverlay = document.querySelector('.menu-overlay');
    
    if (menuToggle && menu) {
        menuToggle.addEventListener('click', function () {
            menu.classList.toggle('open');
            document.body.classList.toggle('menu-open');
            
            // Manejar overlay
            if (menuOverlay) {
                menuOverlay.classList.toggle('active');
            }
        });
        
        // Cerrar menú al hacer click en el overlay
        if (menuOverlay) {
            menuOverlay.addEventListener('click', function () {
                menu.classList.remove('open');
                document.body.classList.remove('menu-open');
                menuOverlay.classList.remove('active');
            });
        }
        
        // Cerrar menú al hacer click en un enlace
        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function () {
                menu.classList.remove('open');
                document.body.classList.remove('menu-open');
                if (menuOverlay) {
                    menuOverlay.classList.remove('active');
                }
            });
        });
        
        // Cerrar menú con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && menu.classList.contains('open')) {
                menu.classList.remove('open');
                document.body.classList.remove('menu-open');
                if (menuOverlay) {
                    menuOverlay.classList.remove('active');
                }
            }
        });
    }

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

    // Lazy loading mejorado para imágenes
    function lazyLoadImages() {
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy-image');
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });

        images.forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Reproducir video cuando esté al 75% visible, sin sonido por defecto
    const videoProyecto = document.querySelector('.proyecto-video video');
    if (videoProyecto) {
        // Configurar video sin sonido por defecto
        videoProyecto.muted = true;
        videoProyecto.volume = 0;
        
        let started = false;
        const observer = new IntersectionObserver(
            (entries, obs) => {
                entries.forEach(entry => {
                    if (!started && entry.isIntersecting && entry.intersectionRatio >= 0.75) {
                        videoProyecto.play().catch(e => console.log('Video autoplay prevented'));
                        started = true;
                        obs.disconnect();
                    }
                });
            },
            {
                threshold: [0, 0.75]
            }
        );
        observer.observe(videoProyecto);
        
        // Crear botón para activar sonido
        const soundButton = document.createElement('button');
        soundButton.innerHTML = '<i class="fas fa-volume-mute"></i>';
        soundButton.className = 'sound-toggle-btn';
        soundButton.title = 'Activar sonido';
        soundButton.style.cssText = `
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            background: rgba(0,0,0,0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        `;
        
        // Agregar botón al contenedor del video
        const videoContainer = document.querySelector('.proyecto-video');
        if (videoContainer) {
            videoContainer.style.position = 'relative';
            videoContainer.appendChild(soundButton);
        }
        
        // Funcionalidad del botón de sonido
        soundButton.addEventListener('click', function() {
            if (videoProyecto.muted) {
                videoProyecto.muted = false;
                videoProyecto.volume = 0.5;
                soundButton.innerHTML = '<i class="fas fa-volume-up"></i>';
                soundButton.title = 'Desactivar sonido';
            } else {
                videoProyecto.muted = true;
                videoProyecto.volume = 0;
                soundButton.innerHTML = '<i class="fas fa-volume-mute"></i>';
                soundButton.title = 'Activar sonido';
            }
        });
    }

    // Optimización de performance - Throttle para scroll events
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    }

    // Header sticky con efecto de scroll
    const header = document.querySelector('.header');
    let lastScrollTop = 0;
    
    if (header) {
        window.addEventListener('scroll', throttle(function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 100) {
                header.style.background = 'rgba(12, 60, 97, 0.95)';
                header.style.backdropFilter = 'blur(10px)';
            } else {
                header.style.background = 'rgb(12, 60, 97)';
                header.style.backdropFilter = 'none';
            }
            
            lastScrollTop = scrollTop;
        }, 100));
    }

    // Animaciones de entrada para elementos
    function animateOnScroll() {
        const elements = document.querySelectorAll('.servicio-card, .proyecto-info, .ubicacion-info');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        elements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    }

    // Preload de recursos críticos
    function preloadCriticalResources() {
        const criticalImages = [
            'img/logosipan.png',
            'img/logo_medialuna.png'
        ];

        criticalImages.forEach(src => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = src;
            document.head.appendChild(link);
        });
    }

    // Inicializar funciones de optimización
    lazyLoadImages();
    animateOnScroll();
    preloadCriticalResources();

    // Service Worker (comentado temporalmente)
    // if ('serviceWorker' in navigator) {
    //     navigator.serviceWorker.register('/sw.js')
    //         .then(registration => console.log('SW registered'))
    //         .catch(error => console.log('SW registration failed'));
    // }

    // Funcionalidad para calificaciones con estrellas
    function initCalificaciones() {
        const estrellas = document.querySelectorAll('.estrellas-calificacion i');
        const ratingInput = document.getElementById('ratingValue');
        const formCalificacion = document.getElementById('formCalificacion');
        
        if (!estrellas.length) return;
        
        let rating = 0;
        
        estrellas.forEach((estrella, index) => {
            estrella.addEventListener('click', function() {
                rating = index + 1;
                ratingInput.value = rating;
                
                // Actualizar visualización de estrellas
                estrellas.forEach((star, i) => {
                    if (i < rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            });
            
            estrella.addEventListener('mouseenter', function() {
                const hoverRating = index + 1;
                estrellas.forEach((star, i) => {
                    if (i < hoverRating) {
                        star.style.color = '#ffd700';
                    } else {
                        star.style.color = '#ddd';
                    }
                });
            });
            
            estrella.addEventListener('mouseleave', function() {
                estrellas.forEach((star, i) => {
                    if (i < rating) {
                        star.style.color = '#ffd700';
                    } else {
                        star.style.color = '#ddd';
                    }
                });
            });
        });
        
        // Cargar datos iniciales desde la base de datos
        cargarCalificaciones();
        
        // Manejar envío del formulario de calificación
        if (formCalificacion) {
            formCalificacion.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const nombre = document.getElementById('nombreCalificacion').value;
                const email = document.getElementById('emailCalificacion').value;
                const comentario = document.getElementById('comentarioCalificacion').value;
                
                if (rating === 0) {
                    alert('Por favor, selecciona una calificación');
                    return;
                }
                
                if (!nombre || !email || !comentario) {
                    alert('Por favor, completa todos los campos');
                    return;
                }
                
                // Enviar calificación a la base de datos
                enviarCalificacion(nombre, email, rating, comentario);
                
                // Limpiar formulario
                formCalificacion.reset();
                rating = 0;
                ratingInput.value = 0;
                estrellas.forEach(star => {
                    star.classList.remove('active');
                    star.style.color = '#ddd';
                });
            });
        }
    }
    
    // Función para cargar calificaciones desde la base de datos
    function cargarCalificaciones() {
        fetch('php/calificaciones-api.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    // Actualizar estadísticas
                    if (data.data.estadisticas) {
                        actualizarEstadisticas(data.data.estadisticas);
                    }
                    
                    // Actualizar comentarios
                    if (data.data.comentarios) {
                        actualizarComentarios(data.data.comentarios);
                    }
                }
            })
            .catch(error => {
                console.error('Error al cargar calificaciones:', error);
            });
    }
    
    // Función para enviar calificación a la base de datos
    function enviarCalificacion(nombre, email, calificacion, comentario) {
        const datos = {
            nombre: nombre,
            email: email,
            calificacion: calificacion,
            comentario: comentario
        };
        
        fetch('php/calificaciones-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(datos)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Recargar calificaciones
                cargarCalificaciones();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error al enviar calificación:', error);
            alert('Error al enviar la calificación. Inténtalo de nuevo.');
        });
    }
    
    // Función para actualizar estadísticas
    function actualizarEstadisticas(estadisticas) {
        const statItems = document.querySelectorAll('.stat-item .stat-number');
        if (statItems.length >= 3) {
            statItems[0].textContent = estadisticas.porcentaje_satisfechos + '%';
            statItems[1].textContent = estadisticas.promedio_calificacion;
            statItems[2].textContent = estadisticas.total_calificaciones + '+';
        }
    }
    
    // Función para actualizar comentarios
    function actualizarComentarios(comentarios) {
        const testimoniosGrid = document.getElementById('testimoniosGrid');
        if (!testimoniosGrid) return;
        
        // Limpiar contenedor
        testimoniosGrid.innerHTML = '';
        
        // Si no hay comentarios, mostrar mensaje
        if (!comentarios || comentarios.length === 0) {
            testimoniosGrid.innerHTML = `
                <div class="no-testimonios">
                    <i class="fas fa-comments"></i>
                    <p>Sé el primero en compartir tu experiencia</p>
                </div>
            `;
            return;
        }
        
        // Agregar comentarios desde la base de datos con el diseño exacto del HTML estático
        comentarios.forEach((comentario, index) => {
            const testimonioCard = document.createElement('div');
            testimonioCard.className = `testimonio-card ${index === 0 ? 'premium' : ''}`;
            
            const estrellasHTML = '<i class="fas fa-star"></i>'.repeat(comentario.calificacion);
            
            // Usar el formato exacto del HTML estático
            if (index === 0) {
                // Primer testimonio (premium) - sin avatar, con icono de quote
                testimonioCard.innerHTML = `
                    <div class="testimonio-header">
                        <div class="cliente-info">
                            <h4>${comentario.nombre}</h4>
                            <span class="cliente-ubicacion">Cliente Verificado</span>
                            <div class="estrellas-rating">
                                ${estrellasHTML}
                            </div>
                        </div>
                        <div class="testimonio-icon">
                            <i class="fas fa-quote-right"></i>
                        </div>
                    </div>
                    <div class="testimonio-contenido">
                        <p>"${comentario.comentario}"</p>
                    </div>
                    <div class="testimonio-verificado">
                        <i class="fas fa-shield-check"></i>
                        <span>Cliente Verificado</span>
                    </div>
                `;
            } else {
                // Testimonios normales - con avatar y fecha
                testimonioCard.innerHTML = `
                    <div class="testimonio-header">
                        <div class="cliente-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="cliente-info">
                            <h4>${comentario.nombre}</h4>
                            <span class="cliente-ubicacion">Cliente Verificado</span>
                            <div class="estrellas-rating">
                                ${estrellasHTML}
                            </div>
                        </div>
                        <div class="testimonio-fecha">
                            <i class="fas fa-calendar-alt"></i>
                            <span>${comentario.fecha}</span>
                        </div>
                    </div>
                    <div class="testimonio-contenido">
                        <p>"${comentario.comentario}"</p>
                    </div>
                    <div class="testimonio-verificado">
                        <i class="fas fa-shield-check"></i>
                        <span>Cliente Verificado</span>
                    </div>
                `;
            }
            
            testimoniosGrid.appendChild(testimonioCard);
        });
        
        // Mostrar botón "Ver más" si hay exactamente 3 comentarios (indica que hay más)
        const verMasBtn = document.getElementById('verMasComentarios');
        if (verMasBtn && comentarios.length === 3) {
            verMasBtn.style.display = 'inline-block';
        }
    }
    
    // Función para cargar todos los comentarios
    function cargarTodosLosComentarios() {
        const verMasBtn = document.getElementById('verMasComentarios');
        if (verMasBtn) {
            verMasBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
            verMasBtn.disabled = true;
        }
        
        fetch('php/calificaciones-api.php?accion=comentarios&limite=100')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    actualizarComentarios(data.data);
                    // Ocultar botón después de cargar todos
                    if (verMasBtn) {
                        verMasBtn.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error al cargar todos los comentarios:', error);
                if (verMasBtn) {
                    verMasBtn.innerHTML = '<i class="fas fa-comments"></i> Ver todos los comentarios';
                    verMasBtn.disabled = false;
                }
            });
    }
    
    // Inicializar calificaciones cuando el DOM esté listo
    initCalificaciones();
    
    // Inicializar animaciones de entrada
    initAnimaciones();

    // Optimización de formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            }
        });
    });

    // Mejora de accesibilidad - Navegación por teclado
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && menu.classList.contains('open')) {
            menu.classList.remove('open');
            document.body.classList.remove('menu-open');
        }
    });

    // Optimización de videos
    const videos = document.querySelectorAll('video');
    videos.forEach(video => {
        // Variable para rastrear si el usuario pausó manualmente
        video.userPaused = false;
        
        video.addEventListener('loadedmetadata', function() {
            video.style.opacity = '1';
        });
        
        // Detectar cuando el usuario pausa manualmente
        video.addEventListener('pause', function() {
            if (!video.ended) {
                video.userPaused = true;
            }
        });
        
        // Detectar cuando el usuario reproduce manualmente
        video.addEventListener('play', function() {
            video.userPaused = false;
        });
        
        // Pausar videos cuando no están visibles
        const videoObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Solo reproducir si el usuario no lo pausó manualmente
                    if (video.paused && !video.muted && !video.userPaused) {
                        video.play().catch(e => console.log('Video play prevented'));
                    }
                } else {
                    if (!video.paused) {
                        video.pause();
                    }
                }
            });
        }, { threshold: 0.5 });
        
        videoObserver.observe(video);
    });

    // Configurar volumen de video de ubicación al cargar la página
    const videoUbicacion = document.querySelector('.ubicacion-video video');
    if (videoUbicacion) {
        videoUbicacion.addEventListener('loadeddata', function() {
            this.volume = 0.25; // Establecer volumen al 25%
        });
    }

    // Función para animaciones de aparición de secciones
    function initSectionAnimations() {
        // Configuración del Intersection Observer
        const observerOptions = {
            threshold: 0.1, // Se activa cuando el 10% de la sección es visible
            rootMargin: '0px 0px -50px 0px' // Se activa un poco antes de que la sección sea completamente visible
        };

        // Crear el observer
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Agregar clase animate cuando la sección entra en el viewport
                    entry.target.classList.add('animate');
                    // Dejar de observar esta sección una vez animada
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Seleccionar todas las secciones que necesitan animación
        const sectionsToAnimate = document.querySelectorAll(
            '.proyecto-section, .servicios-section, .proyecto-destacado-section, .testimonios-section, .calificaciones-section, .oportunidades-section, .ubicacion-section, .experiencia-testimonios-section'
        );

        // Observar cada sección
        sectionsToAnimate.forEach(section => {
            observer.observe(section);
        });
    }

    // Inicializar animaciones de secciones
    initSectionAnimations();

    // Funcionalidad para estrellas interactivas del formulario de experiencia
    const estrellasInteractivas = document.querySelectorAll('.estrellas-interactivas i');
    const ratingInput = document.getElementById('ratingValue');
    const ratingText = document.querySelector('.rating-text');
    
    if (estrellasInteractivas.length > 0) {
        estrellasInteractivas.forEach((estrella, index) => {
            estrella.addEventListener('click', function() {
                const rating = index + 1;
                ratingInput.value = rating;
                
                // Actualizar texto de calificación
                const textos = {
                    1: 'Muy insatisfecho',
                    2: 'Insatisfecho', 
                    3: 'Neutral',
                    4: 'Satisfecho',
                    5: 'Muy satisfecho'
                };
                ratingText.textContent = textos[rating];
                
                // Actualizar estrellas visuales
                estrellasInteractivas.forEach((star, starIndex) => {
                    if (starIndex < rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            });
            
            // Efecto hover
            estrella.addEventListener('mouseenter', function() {
                const hoverRating = index + 1;
                estrellasInteractivas.forEach((star, starIndex) => {
                    if (starIndex < hoverRating) {
                        star.style.color = '#ffd700';
                        star.style.transform = 'scale(1.1)';
                    } else {
                        star.style.color = '#d1d5db';
                        star.style.transform = 'scale(1)';
                    }
                });
            });
        });
        
        // Restaurar estado al salir del hover
        const contenedorEstrellas = document.querySelector('.estrellas-interactivas');
        if (contenedorEstrellas) {
            contenedorEstrellas.addEventListener('mouseleave', function() {
                const currentRating = parseInt(ratingInput.value) || 0;
                estrellasInteractivas.forEach((star, starIndex) => {
                    if (starIndex < currentRating) {
                        star.style.color = '#ffd700';
                        star.style.transform = 'scale(1.1)';
                    } else {
                        star.style.color = '#d1d5db';
                        star.style.transform = 'scale(1)';
                    }
                });
            });
        }
    }

    // Manejo del formulario de experiencia
    const formExperiencia = document.getElementById('formExperiencia');
    if (formExperiencia) {
        formExperiencia.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar que se haya seleccionado una calificación
            const ratingValue = document.getElementById('ratingValue').value;
            if (ratingValue === '0') {
                alert('Por favor, selecciona una calificación antes de enviar.');
                return;
            }
            
            // Obtener datos del formulario
            const nombre = document.getElementById('nombreExperiencia').value.trim();
            const email = document.getElementById('emailExperiencia').value.trim();
            const calificacion = parseInt(ratingValue);
            const experiencia = document.getElementById('experienciaTexto').value.trim();
            
            // Validaciones básicas
            if (!nombre || nombre.length < 2) {
                alert('Por favor, ingresa un nombre válido (mínimo 2 caracteres).');
                return;
            }
            
            if (!email || !email.includes('@')) {
                alert('Por favor, ingresa un email válido.');
                return;
            }
            
            if (!experiencia || experiencia.length < 10) {
                alert('Por favor, escribe tu experiencia (mínimo 10 caracteres).');
                return;
            }
            
            // Preparar botón de envío
            const submitBtn = formExperiencia.querySelector('.btn-experiencia-premium');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Enviando...</span>';
            submitBtn.disabled = true;
            
            // Preparar datos para enviar
            const datosCalificacion = {
                nombre: nombre,
                email: email,
                calificacion: calificacion,
                comentario: experiencia
            };
            
            // Enviar a la API
            fetch('php/calificaciones-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(datosCalificacion)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    alert('¡Gracias por compartir tu experiencia! Tu testimonio ha sido enviado exitosamente y aparecerá en nuestro sitio web.');
                    
                    // Limpiar formulario
                    formExperiencia.reset();
                    document.getElementById('ratingValue').value = '0';
                    
                    // Resetear estrellas
                    const ratingText = document.querySelector('.rating-text');
                    const estrellasInteractivas = document.querySelectorAll('.estrellas-interactivas i');
                    
                    if (ratingText) {
                        ratingText.textContent = 'Selecciona tu calificación';
                    }
                    
                    estrellasInteractivas.forEach(star => {
                        star.classList.remove('active');
                    });
                    
                    // Recargar calificaciones para mostrar la nueva
                    cargarCalificaciones();
                    
                } else {
                    // Mostrar error
                    let errorMessage = 'Error al enviar tu experiencia:\n\n';
                    if (data.errors && data.errors.length > 0) {
                        errorMessage += data.errors.join('\n• ');
                    } else {
                        errorMessage += data.message || 'Error desconocido. Inténtalo de nuevo.';
                    }
                    alert(errorMessage);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión. Verifica que XAMPP esté ejecutándose y que accedas desde http://localhost/inmobiliaria_sipan/');
            })
            .finally(() => {
                // Restaurar botón
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Función para animaciones de entrada al hacer scroll
    function initAnimaciones() {
        // Elementos que se animarán
        const elementosAnimables = [
            '.hero',
            '.header',
            '.proyecto-section',
            '.servicios-section',
            '.proyecto-destacado-section',
            '.testimonios-section',
            '.calificaciones-section',
            '.oportunidades-section',
            '.ubicacion-section',
            '.contacto-section',
            '.proyecto-info',
            '.servicio-card',
            '.proyecto-destacado-content',
            '.oportunidades-content',
            '.ubicacion-container',
            '.contacto-container',
            '.calificaciones-container',
            '.btn-principal',
            '.btn-secundario',
            '.btn-calificar',
            'img',
            '.form-contacto',
            '.form-calificacion',
            '.stat-item'
        ];
        
        // Función para verificar si un elemento está en el viewport
        function estaEnViewport(elemento) {
            const rect = elemento.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }
        
        // Función para animar elementos
        function animarElementos() {
            elementosAnimables.forEach(selector => {
                const elementos = document.querySelectorAll(selector);
                elementos.forEach(elemento => {
                    if (estaEnViewport(elemento) && !elemento.classList.contains('animate')) {
                        elemento.classList.add('animate');
                    }
                });
            });
        }
        
        // Animar elementos al cargar la página
        setTimeout(() => {
            animarElementos();
        }, 100);
        
        // Animar elementos al hacer scroll
        window.addEventListener('scroll', () => {
            animarElementos();
        });
        
        // Animar elementos al cambiar el tamaño de la ventana
        window.addEventListener('resize', () => {
            animarElementos();
        });
    }
});

// Funciones para Modal de Video Exterior (fuera del DOMContentLoaded para acceso global)
function abrirModalVideo(tipo) {
    if (tipo === 'exterior') {
        const modal = document.getElementById('modalVideoExterior');
        const video = document.getElementById('videoExterior');
        
        if (modal && video) {
            modal.style.display = 'block';
            video.currentTime = 0; // Reiniciar video desde el inicio
            video.volume = 0.25; // Establecer volumen al 25%
            
            // Prevenir scroll del body cuando el modal está abierto
            document.body.style.overflow = 'hidden';
        }
    } else if (tipo === 'ubicacion') {
        const modal = document.getElementById('modalVideoUbicacion');
        const video = document.getElementById('videoUbicacion');
        
        if (modal && video) {
            modal.style.display = 'block';
            video.currentTime = 0; // Reiniciar video desde el inicio
            video.volume = 0.25; // Establecer volumen al 25%
            
            // Prevenir scroll del body cuando el modal está abierto
            document.body.style.overflow = 'hidden';
        }
    }
}

function cerrarModalVideo() {
    // Cerrar modal de exterior
    const modalExterior = document.getElementById('modalVideoExterior');
    const videoExterior = document.getElementById('videoExterior');
    
    if (modalExterior && videoExterior) {
        modalExterior.style.display = 'none';
        videoExterior.pause(); // Pausar video al cerrar
        videoExterior.currentTime = 0; // Reiniciar posición
    }
    
    // Cerrar modal de ubicación
    const modalUbicacion = document.getElementById('modalVideoUbicacion');
    const videoUbicacion = document.getElementById('videoUbicacion');
    
    if (modalUbicacion && videoUbicacion) {
        modalUbicacion.style.display = 'none';
        videoUbicacion.pause(); // Pausar video al cerrar
        videoUbicacion.currentTime = 0; // Reiniciar posición
    }
    
    // Restaurar scroll del body
    document.body.style.overflow = 'auto';
}

// Cerrar modal con tecla Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalVideo();
    }
});