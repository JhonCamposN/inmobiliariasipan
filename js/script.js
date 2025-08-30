document.addEventListener('DOMContentLoaded', function () {
    // Cargar estadísticas de calificaciones al iniciar
    cargarEstadisticasCalificaciones();
    // Cargar testimonios dinámicos
    cargarTestimoniosRecientes();
    // Cargar estadísticas para la sección de testimonios
    cargarEstadisticasTestimonios();
    
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
                header.style.background = 'linear-gradient(135deg, rgba(12, 60, 97, 0.95) 0%, rgba(30, 123, 191, 0.95) 70%, rgba(40, 167, 69, 0.95) 100%)';
                header.style.backdropFilter = 'blur(10px)';
            } else {
                header.style.background = 'linear-gradient(135deg, #0c3c61 0%, #1e7bbf 70%, #28a745 100%)';
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

    // Control del video de testimonios
    function initTestimonioVideo() {
        const testimonioVideo = document.getElementById('testimonioVideo');
        if (testimonioVideo) {
            testimonioVideo.muted = true;
            testimonioVideo.volume = 0;
            
            let videoStarted = false;
            const videoObserver = new IntersectionObserver(
                (entries, obs) => {
                    entries.forEach(entry => {
                        if (!videoStarted && entry.isIntersecting && entry.intersectionRatio >= 0.75) {
                            testimonioVideo.play().catch(e => console.log('Video autoplay prevented'));
                            videoStarted = true;
                        } else if (videoStarted && !entry.isIntersecting && entry.intersectionRatio < 0.25) {
                            testimonioVideo.pause();
                            videoStarted = false;
                        }
                    });
                },
                {
                    threshold: [0, 0.25, 0.75]
                }
            );
            videoObserver.observe(testimonioVideo);
            
            // Crear botón para activar/desactivar sonido
            const soundButton = document.createElement('button');
            soundButton.innerHTML = '<i class="fas fa-volume-mute"></i>';
            soundButton.className = 'testimonio-sound-toggle-btn';
            soundButton.title = 'Activar sonido';
            soundButton.style.cssText = `
                position: absolute;
                top: 10px;
                left: 10px;
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
                display: flex;
                align-items: center;
                justify-content: center;
            `;
            
            // Agregar botón al contenedor del video
            const videoContainer = document.querySelector('.testimonios-video .video-container');
            if (videoContainer) {
                videoContainer.style.position = 'relative';
                videoContainer.appendChild(soundButton);
            }
            
            // Funcionalidad del botón de sonido
            soundButton.addEventListener('click', function() {
                if (testimonioVideo.muted) {
                    testimonioVideo.muted = false;
                    testimonioVideo.volume = 0.5;
                    soundButton.innerHTML = '<i class="fas fa-volume-up"></i>';
                    soundButton.title = 'Desactivar sonido';
                } else {
                    testimonioVideo.muted = true;
                    testimonioVideo.volume = 0;
                    soundButton.innerHTML = '<i class="fas fa-volume-mute"></i>';
                    soundButton.title = 'Activar sonido';
                }
            });
            
            // Hover effect
            soundButton.addEventListener('mouseenter', function() {
                soundButton.style.background = 'rgba(0,0,0,0.9)';
                soundButton.style.transform = 'scale(1.1)';
            });
            
            soundButton.addEventListener('mouseleave', function() {
                soundButton.style.background = 'rgba(0,0,0,0.7)';
                soundButton.style.transform = 'scale(1)';
            });
        }
    }

    // Control del video de ubicación
    function initUbicacionVideo() {
        const ubicacionVideo = document.getElementById('ubicacionVideo');
        if (ubicacionVideo) {
            ubicacionVideo.muted = true;
            ubicacionVideo.volume = 0;
            
            let videoStarted = false;
            const videoObserver = new IntersectionObserver(
                (entries, obs) => {
                    entries.forEach(entry => {
                        if (!videoStarted && entry.isIntersecting && entry.intersectionRatio >= 0.75) {
                            ubicacionVideo.play().catch(e => console.log('Video autoplay prevented'));
                            videoStarted = true;
                        } else if (videoStarted && !entry.isIntersecting && entry.intersectionRatio < 0.25) {
                            ubicacionVideo.pause();
                            videoStarted = false;
                        }
                    });
                },
                {
                    threshold: [0, 0.25, 0.75]
                }
            );
            videoObserver.observe(ubicacionVideo);
            
            // Crear botón para activar/desactivar sonido
            const soundButton = document.createElement('button');
            soundButton.innerHTML = '<i class="fas fa-volume-mute"></i>';
            soundButton.className = 'ubicacion-sound-toggle-btn';
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
                display: flex;
                align-items: center;
                justify-content: center;
            `;
            
            // Agregar botón al contenedor del video
            const videoWrapper = document.querySelector('.ubicacion-video .video-wrapper');
            if (videoWrapper) {
                videoWrapper.style.position = 'relative';
                videoWrapper.appendChild(soundButton);
            }
            
            // Funcionalidad del botón de sonido
            soundButton.addEventListener('click', function() {
                if (ubicacionVideo.muted) {
                    ubicacionVideo.muted = false;
                    ubicacionVideo.volume = 0.5;
                    soundButton.innerHTML = '<i class="fas fa-volume-up"></i>';
                    soundButton.title = 'Desactivar sonido';
                } else {
                    ubicacionVideo.muted = true;
                    ubicacionVideo.volume = 0;
                    soundButton.innerHTML = '<i class="fas fa-volume-mute"></i>';
                    soundButton.title = 'Activar sonido';
                }
            });
            
            // Hover effect
            soundButton.addEventListener('mouseenter', function() {
                soundButton.style.background = 'rgba(0,0,0,0.9)';
                soundButton.style.transform = 'scale(1.1)';
            });
            
            soundButton.addEventListener('mouseleave', function() {
                soundButton.style.background = 'rgba(0,0,0,0.7)';
                soundButton.style.transform = 'scale(1)';
            });
        }
    }

    // Inicializar funciones de optimización
    lazyLoadImages();
    animateOnScroll();
    preloadCriticalResources();
    initTestimonioVideo();
    initUbicacionVideo();

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
                throw new Error(data.error || 'Error desconocido al enviar la calificación');
            }
        })
        .catch(error => {
            console.error('Error al enviar calificación:', error);
            mostrarPopupCalificacion('Error al enviar tu experiencia: ' + error.message + '. Por favor, intenta nuevamente.', 'error');
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
                mostrarPopupCalificacion('Por favor, selecciona una calificación antes de enviar.', 'error');
                return;
            }
            
            // Obtener datos del formulario
            const nombre = document.getElementById('nombreExperiencia').value.trim();
            const email = document.getElementById('emailExperiencia').value.trim();
            const calificacion = parseInt(ratingValue);
            const experiencia = document.getElementById('experienciaTexto').value.trim();
            
            // Validaciones básicas
            if (!nombre || nombre.length < 2) {
                mostrarPopupCalificacion('Por favor, ingresa un nombre válido (mínimo 2 caracteres).', 'error');
                return;
            }
            
            // Validar dominios de email permitidos
            const dominiosPermitidos = ['@gmail.com', '@hotmail.com', '@email.com'];
            const emailValido = dominiosPermitidos.some(dominio => email.toLowerCase().endsWith(dominio));
            
            if (!email || !email.includes('@')) {
                mostrarPopupCalificacion('Por favor, ingresa un email válido.', 'error');
                return;
            }
            
            if (!emailValido) {
                mostrarPopupCalificacion('Solo se permiten correos de Gmail, Hotmail o Email.com', 'error');
                return;
            }
            
            if (!experiencia || experiencia.length < 10) {
                mostrarPopupCalificacion('Por favor, escribe tu experiencia (mínimo 10 caracteres).', 'error');
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
                    mostrarPopupCalificacion('¡Gracias por compartir tu experiencia! Tu testimonio ha sido enviado exitosamente y aparecerá en nuestro sitio web.', 'success');
                    
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
                    
                    // Recargar testimonios para mostrar el nuevo comentario
                    cargarTestimoniosRecientes();
                    
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

// Función para cargar estadísticas de calificaciones en tiempo real
async function cargarEstadisticasCalificaciones() {
    try {
        const response = await fetch('php/calificaciones-api.php?accion=estadisticas');
        const data = await response.json();
        
        if (data.success) {
            // Actualizar porcentaje de satisfacción
            const satisfaccionElement = document.getElementById('satisfaccionPorcentaje');
            if (satisfaccionElement) {
                const porcentaje = Math.round(parseFloat(data.data.porcentaje_satisfechos));
                satisfaccionElement.textContent = porcentaje + '%';
            }
            
            // Actualizar calificación promedio
            const promedioElement = document.getElementById('calificacionPromedio');
            if (promedioElement) {
                const promedio = parseFloat(data.data.promedio_calificacion).toFixed(1);
                promedioElement.textContent = promedio;
            }
        } else {
            console.error('Error al cargar estadísticas:', data.message);
            // Mostrar valores por defecto en caso de error
            const satisfaccionElement = document.getElementById('satisfaccionPorcentaje');
            const promedioElement = document.getElementById('calificacionPromedio');
            
            if (satisfaccionElement) satisfaccionElement.textContent = 'N/A';
            if (promedioElement) promedioElement.textContent = 'N/A';
        }
    } catch (error) {
        console.error('Error de conexión al cargar estadísticas:', error);
        // Mostrar valores por defecto en caso de error de conexión
        const satisfaccionElement = document.getElementById('satisfaccionPorcentaje');
        const promedioElement = document.getElementById('calificacionPromedio');
        
        if (satisfaccionElement) satisfaccionElement.textContent = 'N/A';
        if (promedioElement) promedioElement.textContent = 'N/A';
    }
}

// Función para cargar testimonios recientes desde la base de datos
async function cargarTestimoniosRecientes() {
    try {
        const response = await fetch('php/calificaciones-api.php?accion=comentarios&limite=3');
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            mostrarTestimonios(data.data);
        } else {
            mostrarTestimoniosVacios();
        }
    } catch (error) {
        console.error('Error al cargar testimonios:', error);
        mostrarTestimoniosError();
    }
}

// Función para mostrar testimonios dinámicos manteniendo el diseño original
function mostrarTestimonios(testimonios) {
    const testimoniosGrid = document.getElementById('testimoniosGrid');
    if (!testimoniosGrid) return;
    
    testimoniosGrid.innerHTML = '';
    
    testimonios.forEach((testimonio, index) => {
        const testimonioCard = document.createElement('div');
        testimonioCard.className = 'testimonio-card';
        
        // Generar estrellas basadas en la calificación
        const estrellas = generarEstrellasTestimonio(testimonio.calificacion);
        
        // Determinar ubicación/proyecto basado en el comentario o usar genérico
        const ubicacion = determinarUbicacion(testimonio.comentario);
        
        // Todos los testimonios tendrán el mismo diseño consistente
        testimonioCard.innerHTML = `
            <div class="testimonio-header">
                <div class="cliente-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="cliente-info">
                    <h4>${testimonio.nombre}</h4>
                    <span class="cliente-ubicacion">${ubicacion}</span>
                    <div class="estrellas-rating">
                        ${estrellas}
                    </div>
                </div>
                <div class="testimonio-fecha">
                    <i class="fas fa-calendar-alt"></i>
                    <span>${testimonio.fecha}</span>
                </div>
            </div>
            <div class="testimonio-contenido">
                <p>"${testimonio.comentario}"</p>
            </div>
        `;
        
        testimoniosGrid.appendChild(testimonioCard);
    });
}

// Función para generar estrellas para testimonios
function generarEstrellasTestimonio(calificacion) {
    let estrellas = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= calificacion) {
            estrellas += '<i class="fas fa-star"></i>';
        } else {
            estrellas += '<i class="far fa-star"></i>';
        }
    }
    return estrellas;
}

// Función para determinar ubicación/proyecto basado en palabras clave
function determinarUbicacion(comentario) {
    const comentarioLower = comentario.toLowerCase();
    
    if (comentarioLower.includes('media luna') || comentarioLower.includes('dominio')) {
        return 'Condominio Media Luna';
    } else if (comentarioLower.includes('techo propio')) {
        return 'Programa Techo Propio';
    } else if (comentarioLower.includes('mi vivienda')) {
        return 'Programa Mi Vivienda';
    } else if (comentarioLower.includes('proyecto') || comentarioLower.includes('habitacional')) {
        return 'Proyecto Habitacional';
    } else {
        return 'Cliente Sipán';
    }
}

// Función para mostrar mensaje cuando no hay testimonios
function mostrarTestimoniosVacios() {
    const testimoniosGrid = document.getElementById('testimoniosGrid');
    if (!testimoniosGrid) return;
    
    testimoniosGrid.innerHTML = `
        <div class="testimonio-card no-data">
            <div class="no-data-content">
                <i class="fas fa-comments"></i>
                <h4>¡Sé el primero en compartir tu experiencia!</h4>
                <p>Aún no hay testimonios disponibles. Comparte tu experiencia con nosotros.</p>
            </div>
        </div>
    `;
}

// Función para mostrar mensaje de error
function mostrarTestimoniosError() {
    const testimoniosGrid = document.getElementById('testimoniosGrid');
    if (!testimoniosGrid) return;
    
    testimoniosGrid.innerHTML = `
        <div class="testimonio-card error">
            <div class="error-content">
                <i class="fas fa-exclamation-triangle"></i>
                <h4>Error al cargar testimonios</h4>
                <p>No se pudieron cargar los testimonios. Intenta recargar la página.</p>
            </div>
        </div>
    `;
}

// Función para mostrar pop-up de calificaciones (similar al de exportación)
function mostrarPopupCalificacion(mensaje, tipo = 'success') {
    // Crear el pop-up si no existe
    let popup = document.getElementById('popupCalificacion');
    if (!popup) {
        popup = document.createElement('div');
        popup.id = 'popupCalificacion';
        popup.className = 'popup-overlay';
        popup.innerHTML = `
            <div class="popup-content">
                <div class="popup-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="popup-text">
                    <h3 class="popup-title">Mensaje</h3>
                    <p class="popup-message"></p>
                </div>
                <div class="popup-progress">
                    <div class="progress-bar"></div>
                </div>
            </div>
        `;
        document.body.appendChild(popup);
    }
    
    const icon = popup.querySelector('.popup-icon i');
    const title = popup.querySelector('.popup-title');
    const message = popup.querySelector('.popup-message');
    const progressBar = popup.querySelector('.progress-bar');
    
    // Configurar según el tipo
    if (tipo === 'success') {
        icon.className = 'fas fa-check-circle';
        title.textContent = '¡Éxito!';
        popup.querySelector('.popup-content').style.borderColor = '#28a745';
        icon.style.color = '#28a745';
        progressBar.style.backgroundColor = '#28a745';
    } else if (tipo === 'error') {
        icon.className = 'fas fa-exclamation-triangle';
        title.textContent = 'Error';
        popup.querySelector('.popup-content').style.borderColor = '#dc3545';
        icon.style.color = '#dc3545';
        progressBar.style.backgroundColor = '#dc3545';
    } else if (tipo === 'info') {
        icon.className = 'fas fa-info-circle';
        title.textContent = 'Información';
        popup.querySelector('.popup-content').style.borderColor = '#17a2b8';
        icon.style.color = '#17a2b8';
        progressBar.style.backgroundColor = '#17a2b8';
    }
    
    message.textContent = mensaje;
    
    // Mostrar el pop-up
    popup.style.display = 'flex';
    setTimeout(() => popup.classList.add('show'), 10);
    
    // Animar barra de progreso sincronizada con el tiempo del popup
    progressBar.style.width = '0%';
    progressBar.style.transition = 'width 1.5s ease-in-out';
    setTimeout(() => {
        progressBar.style.width = '100%';
    }, 100);
    
    // Ocultar después de 1.5 segundos
    setTimeout(() => {
        popup.classList.remove('show');
        setTimeout(() => {
            popup.style.display = 'none';
        }, 300);
    }, 1500);
}

// Función para cargar estadísticas en la sección de testimonios
async function cargarEstadisticasTestimonios() {
    try {
        const response = await fetch('php/calificaciones-api.php?accion=estadisticas');
        const data = await response.json();
        
        if (data.success && data.data) {
            const estadisticas = data.data;
            
            // Actualizar elementos de la sección testimonios
            const clientesSatisfechos = document.getElementById('clientesSatisfechosTestimonios');
            const calificacionPromedio = document.getElementById('calificacionPromedioTestimonios');
            
            if (clientesSatisfechos) {
                clientesSatisfechos.textContent = estadisticas.porcentaje_satisfechos + '%';
            }
            
            if (calificacionPromedio) {
                calificacionPromedio.textContent = estadisticas.promedio_calificacion;
            }
            
        } else {
            console.error('Error al obtener estadísticas para testimonios:', data.message);
            // Mantener valores por defecto en caso de error
            const clientesSatisfechos = document.getElementById('clientesSatisfechosTestimonios');
            const calificacionPromedio = document.getElementById('calificacionPromedioTestimonios');
            
            if (clientesSatisfechos) clientesSatisfechos.textContent = 'N/A';
            if (calificacionPromedio) calificacionPromedio.textContent = 'N/A';
        }
    } catch (error) {
        console.error('Error de conexión al cargar estadísticas de testimonios:', error);
        // Mostrar valores por defecto en caso de error de conexión
        const clientesSatisfechos = document.getElementById('clientesSatisfechosTestimonios');
        const calificacionPromedio = document.getElementById('calificacionPromedioTestimonios');
        
        if (clientesSatisfechos) clientesSatisfechos.textContent = 'N/A';
        if (calificacionPromedio) calificacionPromedio.textContent = 'N/A';
    }
}

// Hacer las funciones globales para poder ser llamadas desde otros lugares
window.cargarEstadisticasCalificaciones = cargarEstadisticasCalificaciones;
window.cargarTestimoniosRecientes = cargarTestimoniosRecientes;
window.mostrarPopupCalificacion = mostrarPopupCalificacion;
window.cargarEstadisticasTestimonios = cargarEstadisticasTestimonios;