<?php
/**
 * ARCHIVO DE PRUEBA - WHATSAPP BUSINESS API
 * 
 * Este archivo te permite probar la configuración de WhatsApp
 * sin enviar un formulario completo.
 * 
 * USO: Accede a http://localhost/inmobiliaria_sipan/php/test-whatsapp.php
 */

// Incluir configuración
require_once 'config-whatsapp.php';

// Headers para JSON
header('Content-Type: application/json');

// Función para enviar mensaje de prueba
function enviarMensajePrueba() {
    if (!isWhatsAppConfigured()) {
        return [
            'success' => false,
            'message' => 'WhatsApp Business API no está configurado correctamente',
            'config_status' => [
                'token_set' => WHATSAPP_TOKEN !== 'TU_TOKEN_AQUI',
                'phone_id_set' => WHATSAPP_PHONE_ID !== 'TU_PHONE_ID_AQUI',
                'destination_phone' => DESTINATION_PHONE
            ]
        ];
    }
    
    $mensajePrueba = "🧪 *MENSAJE DE PRUEBA*\n\n";
    $mensajePrueba .= "Este es un mensaje de prueba para verificar que la configuración de WhatsApp Business API funciona correctamente.\n\n";
    $mensajePrueba .= "📅 *Fecha:* " . date('d/m/Y H:i:s') . "\n";
    $mensajePrueba .= "🌐 *Servidor:* " . $_SERVER['HTTP_HOST'] . "\n";
    $mensajePrueba .= "🔧 *Estado:* Configuración verificada\n\n";
    $mensajePrueba .= "✅ Si recibes este mensaje, la configuración es correcta.\n";
    $mensajePrueba .= "❌ Si no lo recibes, revisa la configuración.\n\n";
    $mensajePrueba .= "---\n";
    $mensajePrueba .= "Sipán Inmobiliaria\n";
    $mensajePrueba .= "WhatsApp Business API Test";
    
    $url = FACEBOOK_API_URL . "/" . WHATSAPP_PHONE_ID . "/messages";
    
    $data = [
        'messaging_product' => 'whatsapp',
        'to' => DESTINATION_PHONE,
        'type' => 'text',
        'text' => [
            'body' => $mensajePrueba
        ]
    ];
    
    $headers = [
        'Authorization: Bearer ' . WHATSAPP_TOKEN,
        'Content-Type: application/json'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Registrar el evento
    logEvent('TEST_MESSAGE', [
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error,
        'success' => $httpCode === 200
    ]);
    
    if ($httpCode === 200) {
        return [
            'success' => true,
            'message' => 'Mensaje de prueba enviado correctamente. Revisa WhatsApp.',
            'http_code' => $httpCode,
            'response' => json_decode($response, true)
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Error al enviar mensaje de prueba',
            'http_code' => $httpCode,
            'error' => $error,
            'response' => $response
        ];
    }
}

// Función para verificar configuración
function verificarConfiguracion() {
    return [
        'whatsapp_configured' => isWhatsAppConfigured(),
        'config_details' => [
            'token_set' => WHATSAPP_TOKEN !== 'TU_TOKEN_AQUI',
            'phone_id_set' => WHATSAPP_PHONE_ID !== 'TU_PHONE_ID_AQUI',
            'destination_phone' => DESTINATION_PHONE,
            'api_version' => FACEBOOK_API_VERSION,
            'company_email' => COMPANY_EMAIL,
            'max_file_size' => MAX_FILE_SIZE
        ],
        'server_info' => [
            'php_version' => PHP_VERSION,
            'curl_enabled' => function_exists('curl_init'),
            'mail_enabled' => function_exists('mail'),
            'server_time' => date('Y-m-d H:i:s')
        ]
    ];
}

// Procesar solicitud
$action = $_GET['action'] ?? 'test';

switch ($action) {
    case 'config':
        echo json_encode(verificarConfiguracion(), JSON_PRETTY_PRINT);
        break;
        
    case 'test':
    default:
        echo json_encode(enviarMensajePrueba(), JSON_PRETTY_PRINT);
        break;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test WhatsApp Business API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f8fa;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1e7bbf;
            text-align: center;
            margin-bottom: 30px;
        }
        .button {
            background: #1e7bbf;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
            text-decoration: none;
            display: inline-block;
        }
        .button:hover {
            background: #0c3c61;
        }
        .result {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #1e7bbf;
        }
        .success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test WhatsApp Business API</h1>
        
        <p>Esta página te permite probar la configuración de WhatsApp Business API.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="?action=config" class="button">🔍 Verificar Configuración</a>
            <a href="?action=test" class="button">📱 Enviar Mensaje de Prueba</a>
        </div>
        
        <?php if (isset($_GET['action'])): ?>
            <div class="result <?php echo $action === 'test' ? 'success' : 'warning'; ?>">
                <h3>Resultado:</h3>
                <pre><?php 
                    if ($action === 'config') {
                        echo json_encode(verificarConfiguracion(), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode(enviarMensajePrueba(), JSON_PRETTY_PRINT);
                    }
                ?></pre>
            </div>
        <?php endif; ?>
        
        <div class="result warning">
            <h3>📋 Instrucciones:</h3>
            <ol>
                <li><strong>Verificar Configuración:</strong> Revisa si todos los parámetros están configurados correctamente.</li>
                <li><strong>Enviar Mensaje de Prueba:</strong> Envía un mensaje de prueba a WhatsApp para verificar la conexión.</li>
                <li>Si hay errores, revisa el archivo <code>php/whatsapp-log.txt</code> para más detalles.</li>
                <li>Asegúrate de haber configurado correctamente el archivo <code>php/config-whatsapp.php</code>.</li>
            </ol>
        </div>
        
        <div class="result">
            <h3>🔗 Enlaces Útiles:</h3>
            <ul>
                <li><a href="https://developers.facebook.com/" target="_blank">Facebook Developers</a></li>
                <li><a href="https://developers.facebook.com/docs/whatsapp" target="_blank">Documentación WhatsApp Business API</a></li>
                <li><a href="../WHATSAPP-SETUP.md" target="_blank">Guía de Configuración Completa</a></li>
            </ul>
        </div>
    </div>
</body>
</html> 