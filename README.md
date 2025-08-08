# 🏠 Sipán Inmobiliaria - Sitio Web Corporativo

Sitio web moderno y responsive para Corporación Sipán Inmobiliaria, especializada en bienes raíces en Chiclayo, Perú.

## 📋 Características

### ✨ **Funcionalidades Principales**
- **Diseño Responsive**: Adaptable a todos los dispositivos
- **SEO Optimizado**: Meta tags, sitemap, structured data
- **Formularios Funcionales**: Envío real de emails con PHP
- **Galería de Proyectos**: Modal interactivo con imágenes
- **Performance Optimizada**: Lazy loading, caché, compresión
- **Accesibilidad**: Navegación por teclado, ARIA labels

### 🎨 **Diseño y UX**
- Paleta de colores profesional (azul corporativo)
- Animaciones suaves y modernas
- Menú hamburguesa para móviles
- Botón flotante de WhatsApp
- Efectos hover interactivos

### 🚀 **Optimizaciones Técnicas**
- **Service Worker**: Caché offline y carga rápida
- **Compresión GZIP**: Archivos más pequeños
- **Lazy Loading**: Imágenes cargan bajo demanda
- **Preload**: Recursos críticos precargados
- **Headers de Seguridad**: Protección XSS, CSP

## 📁 Estructura del Proyecto

```
inmobiliaria_sipan/
├── index.html              # Página principal
├── html/
│   ├── proyecto.html       # Página de proyectos
│   └── contacto.html       # Página de contacto
├── css/
│   └── styles.css          # Estilos principales
├── js/
│   ├── script.js           # JavaScript principal
│   └── hero-reverse.js     # Efecto video hero
├── img/                    # Imágenes del proyecto
├── video/                  # Videos promocionales
├── php/
│   └── contacto.php        # Backend formularios
├── sitemap.xml             # Sitemap para SEO
├── robots.txt              # Directivas para buscadores
├── sw.js                   # Service Worker
├── .htaccess               # Configuración Apache
└── README.md               # Documentación
```

## 🛠️ Instalación y Configuración

### **Requisitos**
- Servidor web con PHP (Apache/Nginx)
- PHP 7.4 o superior
- Soporte para mod_rewrite (Apache)

### **Pasos de Instalación**

1. **Clonar/Descargar el proyecto**
   ```bash
   git clone [url-del-repositorio]
   cd inmobiliaria_sipan
   ```

2. **Configurar el servidor web**
   - Colocar los archivos en el directorio web (htdocs, public_html, etc.)
   - Asegurar que PHP esté habilitado

3. **Configurar emails (Opcional)**
   - Editar `php/contacto.php` con la configuración de email del servidor
   - Cambiar `info@sipaninmobiliaria.com` por el email real

4. **Verificar permisos**
   ```bash
   chmod 644 .htaccess
   chmod 755 php/
   ```

### **Configuración de Dominio**

1. **Actualizar URLs en archivos:**
   - `index.html` - Líneas 25, 30, 35, 40
   - `html/contacto.html` - Líneas 15, 20
   - `sitemap.xml` - Todas las URLs
   - `sw.js` - URLs de caché

2. **Configurar SSL/HTTPS** (Recomendado)
   - El `.htaccess` incluye redirecciones HTTPS
   - Obtener certificado SSL del hosting

## 📧 Configuración de Formularios

### **PHP Mail (Configuración básica)**
El archivo `php/contacto.php` está configurado para usar la función `mail()` de PHP.

### **Configuración SMTP (Recomendado)**
Para mejor entrega de emails, configurar SMTP:

```php
// En php/contacto.php, reemplazar la función mail() con:
ini_set("SMTP", "smtp.tuservidor.com");
ini_set("smtp_port", "587");
```

### **Servicios de Email Recomendados**
- **SendGrid**: 100 emails gratis/día
- **Mailgun**: 5,000 emails gratis/mes
- **Amazon SES**: Muy económico para grandes volúmenes

## 🔧 Personalización

### **Cambiar Información de Contacto**
Editar en los siguientes archivos:
- `index.html` - Footer y sección ubicación
- `html/contacto.html` - Footer
- `html/proyecto.html` - Footer

### **Modificar Colores**
Editar en `css/styles.css`:
```css
/* Paleta principal */
- Azul oscuro: rgb(12, 60, 97)
- Azul medio: #1e7bbf
- Azul claro: #b2d4ee
- Blanco: rgb(255, 255, 255)
```

### **Agregar Nuevos Proyectos**
1. Agregar imágenes en `img/`
2. Editar `html/proyecto.html` - Sección proyectos
3. Actualizar galería y formulario de interés

## 📊 SEO y Analytics

### **Google Analytics**
Agregar en `<head>` de todas las páginas:
```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

### **Google Search Console**
1. Verificar propiedad del sitio
2. Enviar sitemap: `https://tudominio.com/sitemap.xml`
3. Monitorear rendimiento en búsquedas

## 🚀 Optimización de Performance

### **Comprimir Imágenes**
```bash
# Usando ImageOptim (Mac) o FileOptimizer (Windows)
# Convertir a WebP para mejor compresión
```

### **Minificar CSS/JS**
```bash
# Usar herramientas online o build tools
# CSS: https://cssminifier.com/
# JS: https://jscompress.com/
```

### **CDN para Recursos**
Considerar usar CDN para:
- Font Awesome
- Google Fonts
- Imágenes estáticas

## 🔒 Seguridad

### **Protecciones Implementadas**
- Headers de seguridad en `.htaccess`
- Validación de formularios en PHP
- Sanitización de datos
- Protección contra XSS

### **Recomendaciones Adicionales**
- Mantener PHP actualizado
- Usar HTTPS obligatorio
- Monitorear logs de errores
- Backup regular de archivos

## 📱 Responsive Design

### **Breakpoints**
- **Desktop**: > 1024px
- **Tablet**: 768px - 1024px
- **Mobile**: < 768px

### **Características Móviles**
- Menú hamburguesa
- Imágenes optimizadas
- Touch-friendly buttons
- Scroll suave

## 🐛 Solución de Problemas

### **Formularios no envían emails**
1. Verificar configuración PHP mail()
2. Revisar logs de errores del servidor
3. Probar con configuración SMTP

### **Imágenes no cargan**
1. Verificar rutas de archivos
2. Comprobar permisos de directorios
3. Revisar configuración de caché

### **SEO no funciona**
1. Verificar sitemap.xml
2. Comprobar robots.txt
3. Revisar meta tags
4. Enviar sitemap a Google Search Console

## 📞 Soporte

Para soporte técnico o consultas:
- **Email**: info@sipaninmobiliaria.com
- **Teléfono**: +51 932 359 551
- **Dirección**: C.C Boulevar - Oficina j3 (2d piso - Patio principal)

## 📄 Licencia

© 2025 Sipán Inmobiliaria. Todos los derechos reservados.

---

**Desarrollado con ❤️ para Corporación Sipán Inmobiliaria** 