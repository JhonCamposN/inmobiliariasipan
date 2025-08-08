# 📱 Configuración de WhatsApp Business API

## 🎯 ¿Qué hace esta integración?

Esta integración permite que cuando un cliente envíe un formulario de contacto desde tu sitio web, automáticamente se envíe un mensaje a WhatsApp con todos los detalles del contacto, incluyendo:

- ✅ Nombre y apellido del cliente
- ✅ Email y teléfono
- ✅ Mensaje completo
- ✅ Archivos adjuntos (información)
- ✅ Fecha y hora del envío
- ✅ Información del navegador

## 🚀 Beneficios

- **Respuesta inmediata**: Recibes notificaciones instantáneas en WhatsApp
- **Información completa**: Todos los datos del formulario en un solo mensaje
- **Archivos adjuntos**: Información de archivos enviados
- **Fallback automático**: Si WhatsApp falla, envía por email
- **Confirmación al cliente**: Email automático de confirmación

## 📋 Requisitos Previos

1. **Cuenta de Facebook Developer**
2. **Aplicación de Facebook Business**
3. **Número de WhatsApp Business verificado**
4. **Token de acceso de WhatsApp Business API**

## 🔧 Pasos de Configuración

### Paso 1: Crear Aplicación en Facebook Developer

1. Ve a [Facebook Developers](https://developers.facebook.com/)
2. Haz clic en "Crear App"
3. Selecciona "Business" como tipo de aplicación
4. Completa la información básica
5. Ve a "Agregar producto" y selecciona "WhatsApp"

### Paso 2: Configurar WhatsApp Business API

1. En tu aplicación, ve a "WhatsApp" > "Getting Started"
2. Lee y acepta los términos de servicio
3. Copia el **Temporary access token** (lo necesitarás después)

### Paso 3: Agregar Número de Teléfono

1. Ve a "WhatsApp" > "Phone Numbers"
2. Haz clic en "Add phone number"
3. Selecciona tu número de WhatsApp Business
4. Copia el **Phone number ID** (lo necesitarás después)

### Paso 4: Generar Token Permanente (Opcional)

1. Ve a "WhatsApp" > "Getting Started"
2. Haz clic en "Generate token"
3. Selecciona los permisos necesarios:
   - `whatsapp_business_messaging`
   - `whatsapp_business_management`
4. Copia el token generado

### Paso 5: Configurar el Código

1. Abre el archivo `php/config-whatsapp.php`
2. Reemplaza los valores:

```php
// Reemplazar con tu token real
define('WHATSAPP_TOKEN', 'TU_TOKEN_REAL_AQUI');

// Reemplazar con tu Phone ID real
define('WHATSAPP_PHONE_ID', 'TU_PHONE_ID_REAL_AQUI');

// Verificar que el número de destino sea correcto
define('DESTINATION_PHONE', '51932359551');
```

## 🔍 Verificación de Configuración

### Test Manual

1. Abre tu sitio web
2. Ve a la página de contacto
3. Llena el formulario con datos de prueba
4. Envía el formulario
5. Verifica que recibas el mensaje en WhatsApp

### Verificación de Logs

Si hay errores, revisa el archivo `php/whatsapp-log.txt` que se crea automáticamente.

## 🛠️ Solución de Problemas

### Error: "WhatsApp Business API no está configurado"

**Causa**: Los tokens no están configurados correctamente
**Solución**: 
1. Verifica que hayas reemplazado `TU_TOKEN_AQUI` y `TU_PHONE_ID_AQUI`
2. Asegúrate de que el token sea válido
3. Verifica que el Phone ID sea correcto

### Error: "Invalid phone number"

**Causa**: El número de teléfono no está en formato correcto
**Solución**:
1. Asegúrate de que el número incluya código de país
2. No incluyas el símbolo `+`
3. Ejemplo correcto: `51932359551`

### Error: "Permission denied"

**Causa**: El token no tiene permisos suficientes
**Solución**:
1. Genera un nuevo token con permisos completos
2. Verifica que la aplicación esté en modo "Live"
3. Asegúrate de que el número esté verificado

### Error: "Rate limit exceeded"

**Causa**: Demasiadas solicitudes en poco tiempo
**Solución**:
1. Espera unos minutos antes de enviar otro mensaje
2. Implementa un sistema de cola si es necesario

## 📊 Monitoreo y Logs

### Archivos de Log

- `php/whatsapp-log.txt`: Registra todos los eventos de WhatsApp
- `php/contacto.php`: Registra envíos por email (fallback)

### Información Registrada

- Fecha y hora de cada envío
- Estado del envío (éxito/error)
- Detalles del error si ocurre
- Información del cliente

## 🔒 Seguridad

### Recomendaciones

1. **Nunca compartas tu token** en repositorios públicos
2. **Usa variables de entorno** en producción
3. **Configura HTTPS** en tu servidor
4. **Valida todos los datos** de entrada
5. **Limita el tamaño** de archivos adjuntos

### Configuración de Producción

```php
// En producción, usa variables de entorno
define('WHATSAPP_TOKEN', $_ENV['WHATSAPP_TOKEN']);
define('WHATSAPP_PHONE_ID', $_ENV['WHATSAPP_PHONE_ID']);
```

## 📞 Soporte

Si tienes problemas con la configuración:

1. Revisa los logs en `php/whatsapp-log.txt`
2. Verifica la documentación oficial de WhatsApp Business API
3. Contacta al soporte de Facebook Developer
4. Revisa que tu servidor tenga cURL habilitado

## 🎉 ¡Listo!

Una vez configurado correctamente, cada vez que alguien envíe un formulario de contacto:

1. 📱 Recibirás un mensaje instantáneo en WhatsApp
2. 📧 El cliente recibirá un email de confirmación
3. 📊 Se registrará todo en los logs
4. 🔄 Si WhatsApp falla, se enviará por email automáticamente

¡Tu sistema de contacto está ahora completamente integrado con WhatsApp Business! 