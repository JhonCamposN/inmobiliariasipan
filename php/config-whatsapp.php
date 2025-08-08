<?php
/**
 * CONFIGURACIÓN DE WHATSAPP BUSINESS API
 * 
 * INSTRUCCIONES PARA CONFIGURAR:
 * 
 * 1. CREAR CUENTA DE WHATSAPP BUSINESS API:
 *    - Ve a: https://developers.facebook.com/
 *    - Crea una nueva aplicación
 *    - Selecciona "Business" como tipo
 *    - Agrega el producto "WhatsApp"
 * 
 * 2. OBTENER TOKEN DE ACCESO:
 *    - En tu aplicación, ve a "WhatsApp" > "Getting Started"
 *    - Copia el "Temporary access token" o genera uno permanente
 * 
 * 3. OBTENER PHONE NUMBER ID:
 *    - Ve a "WhatsApp" > "Phone Numbers"
 *    - Agrega un número de teléfono
 *    - Copia el "Phone number ID"
 * 
 * 4. CONFIGURAR WEBHOOK (OPCIONAL):
 *    - Ve a "WhatsApp" > "Configuration"
 *    - Configura el webhook para recibir mensajes
 * 
 * 5. REEMPLAZAR LOS VALORES ABAJO:
 */

// ========================================
// CONFIGURACIÓN - REEMPLAZAR ESTOS VALORES
// ========================================

// Token de acceso de WhatsApp Business API
define('WHATSAPP_TOKEN', 'TU_TOKEN_AQUI');

// ID del número de teléfono de WhatsApp Business
define('WHATSAPP_PHONE_ID', 'TU_PHONE_ID_AQUI');

// Número de teléfono de destino (sin el +)
define('DESTINATION_PHONE', '51932359551');

// ========================================
// CONFIGURACIÓN ADICIONAL
// ========================================

// Versión de la API de Facebook (cambiar si es necesario)
define('FACEBOOK_API_VERSION', 'v18.0');

// URL base de la API de Facebook
define('FACEBOOK_API_URL', 'https://graph.facebook.com/' . FACEBOOK_API_VERSION);

// Configuración de email
define('COMPANY_EMAIL', 'info@sipaninmobiliaria.com');
define('COMPANY_NAME', 'Sipán Inmobiliaria');

// Configuración de archivos
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_FILE_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp',
    'video/mp4',
    'video/avi',
    'video/mov',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'text/plain'
]);

// ========================================
// FUNCIONES DE VALIDACIÓN
// ========================================

/**
 * Valida si el token de WhatsApp está configurado
 */
function isWhatsAppConfigured() {
    return WHATSAPP_TOKEN !== 'TU_TOKEN_AQUI' && 
           WHATSAPP_PHONE_ID !== 'TU_PHONE_ID_AQUI' &&
           !empty(WHATSAPP_TOKEN) && 
           !empty(WHATSAPP_PHONE_ID);
}

/**
 * Valida el formato del número de teléfono
 */
function validatePhoneNumber($phone) {
    // Remover caracteres no numéricos
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Validar longitud (7-15 dígitos)
    return strlen($phone) >= 7 && strlen($phone) <= 15;
}

/**
 * Valida el tipo de archivo
 */
function validateFileType($fileType) {
    return in_array($fileType, ALLOWED_FILE_TYPES);
}

/**
 * Valida el tamaño del archivo
 */
function validateFileSize($fileSize) {
    return $fileSize <= MAX_FILE_SIZE;
}

/**
 * Formatea el número de teléfono para WhatsApp
 */
function formatPhoneForWhatsApp($phone) {
    // Remover caracteres no numéricos
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Si no empieza con código de país, agregar +51 (Perú)
    if (!preg_match('/^51/', $phone)) {
        $phone = '51' . $phone;
    }
    
    return $phone;
}

// ========================================
// MENSAJES DE ERROR
// ========================================

define('ERROR_MESSAGES', [
    'whatsapp_not_configured' => 'WhatsApp Business API no está configurado. Contacta al administrador.',
    'invalid_phone' => 'El formato del número de teléfono no es válido.',
    'invalid_email' => 'El formato del email no es válido.',
    'file_too_large' => 'El archivo es demasiado grande. Máximo 10MB.',
    'invalid_file_type' => 'Tipo de archivo no permitido.',
    'missing_required_fields' => 'Todos los campos obligatorios deben estar completos.',
    'whatsapp_send_failed' => 'Error al enviar mensaje a WhatsApp. Se enviará por email.',
    'email_send_failed' => 'Error al enviar email. Por favor, inténtelo nuevamente.'
]);

// ========================================
// LOGGING (OPCIONAL)
// ========================================

/**
 * Registra eventos en un archivo de log
 */
function logEvent($event, $data = []) {
    $logFile = __DIR__ . '/whatsapp-log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] {$event}: " . json_encode($data) . "\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// ========================================
// VERIFICACIÓN DE CONFIGURACIÓN
// ========================================

if (!isWhatsAppConfigured()) {
    // Log del error de configuración
    logEvent('CONFIG_ERROR', [
        'message' => 'WhatsApp Business API no está configurado correctamente',
        'token_set' => WHATSAPP_TOKEN !== 'TU_TOKEN_AQUI',
        'phone_id_set' => WHATSAPP_PHONE_ID !== 'TU_PHONE_ID_AQUI'
    ]);
}

?> 