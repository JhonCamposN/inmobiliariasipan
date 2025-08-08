# 🚀 Configuración WhatsApp Business API

## 📋 Requisitos Previos

1. **Cuenta de Meta for Developers**
   - Ve a: https://developers.facebook.com/
   - Crea una cuenta si no tienes

2. **WhatsApp Business Account**
   - Necesitas una cuenta de WhatsApp Business verificada

## 🔧 Paso 1: Crear Aplicación en Meta

1. **Accede a Meta for Developers**
   - Ve a: https://developers.facebook.com/
   - Inicia sesión

2. **Crear Nueva Aplicación**
   - Haz clic en "Crear App"
   - Selecciona "Business" como tipo
   - Nombre: "Sipán Inmobiliaria WhatsApp"
   - Email de contacto: tu email

3. **Agregar WhatsApp**
   - En el dashboard, busca "WhatsApp"
   - Haz clic en "Configurar"
   - Selecciona "API de la nube" (Cloud API)

## 📱 Paso 2: Configurar WhatsApp Business

1. **Agregar Número de Teléfono**
   - Ve a "WhatsApp" > "Getting Started"
   - Haz clic en "Add phone number"
   - Agrega: `+51 932 359 551`
   - Verifica el número con el código SMS

2. **Obtener Token de Acceso**
   - Ve a "WhatsApp" > "API Setup"
   - Copia el "Permanent access token"

3. **Obtener Phone Number ID**
   - Ve a "WhatsApp" > "Phone Numbers"
   - Copia el "Phone number ID"

## 🔑 Paso 3: Configurar Variables

Una vez tengas los datos, edita el archivo `php/config-whatsapp-api.php` y reemplaza:

```php
define('WHATSAPP_PHONE_NUMBER_ID', 'TU_PHONE_NUMBER_ID_AQUI');
define('WHATSAPP_ACCESS_TOKEN', 'TU_ACCESS_TOKEN_AQUI');
define('WHATSAPP_BUSINESS_ACCOUNT_ID', 'TU_BUSINESS_ACCOUNT_ID_AQUI');
```

Con tus credenciales reales.

## 📝 Paso 4: Implementación en el Código

Los archivos ya están creados:

- ✅ `php/config-whatsapp-api.php` - Configuración de la API
- ✅ `php/contacto-whatsapp-api.php` - Procesamiento del formulario
- ✅ `test-whatsapp-api.html` - Página de prueba

## 🧪 Paso 5: Probar la API

1. **Configura las credenciales** en `php/config-whatsapp-api.php`
2. **Abre la página de prueba**: `http://localhost/inmobiliaria_sipan/test-whatsapp-api.html`
3. **Verifica el estado** de la configuración
4. **Envía un mensaje de prueba** con archivos adjuntos

---

## ⚠️ Notas Importantes

- **Costo**: WhatsApp Business API tiene costo por mensaje
- **Límites**: Hay límites de mensajes por día
- **Verificación**: El número debe estar verificado
- **Plantillas**: Para mensajes iniciados por el negocio necesitas plantillas aprobadas

## 🎯 Próximos Pasos

1. ✅ Completa la configuración en Meta for Developers
2. ✅ Obtén las credenciales (Phone Number ID, Access Token, Business Account ID)
3. ✅ Configura las credenciales en `php/config-whatsapp-api.php`
4. ✅ Prueba el envío con `test-whatsapp-api.html`
5. 🔄 Actualiza el formulario principal para usar la API

## 🔄 Actualizar Formulario Principal

Para usar la API en el formulario principal, cambia en `html/contacto.html`:

```javascript
// Cambiar esta línea:
fetch('../php/contacto-whatsapp-directo.php', {

// Por esta:
fetch('../php/contacto-whatsapp-api.php', {
```

---

**¿Ya tienes acceso a Meta for Developers? ¿Quieres que te ayude con algún paso específico?** 