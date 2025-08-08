<?php
/**
 * Configuración para WhatsApp Business API
 * 
 * IMPORTANTE: Reemplaza estos valores con tus credenciales reales
 * que obtendrás de Meta for Developers
 */

// Configuración de la API de WhatsApp Business
define('WHATSAPP_API_VERSION', 'v18.0'); // Versión actual de la API
define('WHATSAPP_PHONE_NUMBER_ID', 'TU_PHONE_NUMBER_ID_AQUI'); // Phone Number ID de Meta
define('WHATSAPP_ACCESS_TOKEN', 'TU_ACCESS_TOKEN_AQUI'); // Permanent Access Token
define('WHATSAPP_BUSINESS_ACCOUNT_ID', 'TU_BUSINESS_ACCOUNT_ID_AQUI'); // Business Account ID

// URL base de la API
define('WHATSAPP_API_BASE_URL', 'https://graph.facebook.com/' . WHATSAPP_API_VERSION);

// Configuración de archivos
define('MAX_FILE_SIZE', 16 * 1024 * 1024); // 16MB máximo por archivo
define('ALLOWED_FILE_TYPES', [
    'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
    'video/mp4', 'video/avi', 'video/mov', 'video/wmv',
    'application/pdf', 'application/msword', 
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel', 
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-powerpoint', 
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'text/plain'
]);

// Configuración de mensajes
define('COMPANY_NAME', 'Sipán Inmobiliaria');
define('COMPANY_PHONE', '+51 932 359 551');
define('COMPANY_ADDRESS', 'C.C Boulevar - Oficina j3 (2do piso - Patio principal)');

// Función para validar configuración
function validateWhatsAppConfig() {
    $errors = [];
    
    if (WHATSAPP_PHONE_NUMBER_ID === 'TU_PHONE_NUMBER_ID_AQUI') {
        $errors[] = 'WHATSAPP_PHONE_NUMBER_ID no está configurado';
    }
    
    if (WHATSAPP_ACCESS_TOKEN === 'TU_ACCESS_TOKEN_AQUI') {
        $errors[] = 'WHATSAPP_ACCESS_TOKEN no está configurado';
    }
    
    if (WHATSAPP_BUSINESS_ACCOUNT_ID === 'TU_BUSINESS_ACCOUNT_ID_AQUI') {
        $errors[] = 'WHATSAPP_BUSINESS_ACCOUNT_ID no está configurado';
    }
    
    return $errors;
}

// Función para hacer peticiones a la API
function makeWhatsAppAPIRequest($endpoint, $method = 'GET', $data = null) {
    $url = WHATSAPP_API_BASE_URL . '/' . $endpoint;
    
    $headers = [
        'Authorization: Bearer ' . WHATSAPP_ACCESS_TOKEN,
        'Content-Type: application/json'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'success' => false,
            'error' => 'Error de conexión: ' . $error
        ];
    }
    
    $responseData = json_decode($response, true);
    
    return [
        'success' => $httpCode >= 200 && $httpCode < 300,
        'http_code' => $httpCode,
        'data' => $responseData,
        'raw_response' => $response
    ];
}

// Función para subir archivo a WhatsApp
function uploadFileToWhatsApp($filePath, $fileName, $fileType) {
    // Primero subimos el archivo a WhatsApp
    $uploadUrl = WHATSAPP_API_BASE_URL . '/' . WHATSAPP_PHONE_NUMBER_ID . '/media';
    
    $postData = [
        'messaging_product' => 'whatsapp',
        'file' => new CURLFile($filePath, $fileType, $fileName)
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uploadUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . WHATSAPP_ACCESS_TOKEN
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'success' => false,
            'error' => 'Error al subir archivo: ' . $error
        ];
    }
    
    $responseData = json_decode($response, true);
    
    if ($httpCode >= 200 && $httpCode < 300 && isset($responseData['id'])) {
        return [
            'success' => true,
            'media_id' => $responseData['id']
        ];
    } else {
        return [
            'success' => false,
            'error' => 'Error al subir archivo: ' . ($responseData['error']['message'] ?? 'Error desconocido'),
            'response' => $responseData
        ];
    }
}

// Función para enviar mensaje con archivos
function sendWhatsAppMessageWithFiles($to, $message, $files = []) {
    $data = [
        'messaging_product' => 'whatsapp',
        'to' => $to,
        'type' => 'template',
        'template' => [
            'name' => 'contact_form_notification',
            'language' => [
                'code' => 'es'
            ],
            'components' => []
        ]
    ];
    
    // Agregar componentes de texto
    $textComponent = [
        'type' => 'body',
        'parameters' => [
            [
                'type' => 'text',
                'text' => $message
            ]
        ]
    ];
    
    $data['template']['components'][] = $textComponent;
    
    // Agregar archivos si existen
    if (!empty($files)) {
        foreach ($files as $file) {
            $uploadResult = uploadFileToWhatsApp($file['path'], $file['name'], $file['type']);
            
            if ($uploadResult['success']) {
                $data['template']['components'][] = [
                    'type' => 'header',
                    'parameters' => [
                        [
                            'type' => 'document',
                            'document' => [
                                'id' => $uploadResult['media_id'],
                                'filename' => $file['name']
                            ]
                        ]
                    ]
                ];
            }
        }
    }
    
    return makeWhatsAppAPIRequest(WHATSAPP_PHONE_NUMBER_ID . '/messages', 'POST', $data);
}

// Función para enviar mensaje simple
function sendWhatsAppTextMessage($to, $message) {
    $data = [
        'messaging_product' => 'whatsapp',
        'to' => $to,
        'type' => 'text',
        'text' => [
            'body' => $message
        ]
    ];
    
    return makeWhatsAppAPIRequest(WHATSAPP_PHONE_NUMBER_ID . '/messages', 'POST', $data);
}

// Función para verificar estado de la API
function checkWhatsAppAPIStatus() {
    $configErrors = validateWhatsAppConfig();
    
    if (!empty($configErrors)) {
        return [
            'success' => false,
            'errors' => $configErrors,
            'message' => 'Configuración incompleta'
        ];
    }
    
    // Verificar conectividad con la API
    $result = makeWhatsAppAPIRequest(WHATSAPP_PHONE_NUMBER_ID);
    
    return [
        'success' => $result['success'],
        'message' => $result['success'] ? 'API conectada correctamente' : 'Error de conexión',
        'details' => $result
    ];
}
?> 