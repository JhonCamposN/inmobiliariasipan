<?php
// Incluir configuración
require_once 'config-whatsapp.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Función para limpiar datos
function limpiarDatos($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $dato;
}

// Función para validar email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Función para validar teléfono
function validarTelefono($telefono) {
    return preg_match('/^[\+]?[0-9\s\-\(\)]{7,15}$/', $telefono);
}

// Función para enviar mensaje a WhatsApp
function enviarWhatsApp($mensaje) {
    // Verificar si WhatsApp está configurado
    if (!isWhatsAppConfigured()) {
        logEvent('WHATSAPP_NOT_CONFIGURED', ['message' => 'WhatsApp Business API no está configurado']);
        return ['success' => false, 'error' => 'whatsapp_not_configured'];
    }
    
    $url = FACEBOOK_API_URL . "/" . WHATSAPP_PHONE_ID . "/messages";
    
    $data = [
        'messaging_product' => 'whatsapp',
        'to' => DESTINATION_PHONE,
        'type' => 'text',
        'text' => [
            'body' => $mensaje
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
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'success' => $httpCode === 200,
        'response' => json_decode($response, true),
        'http_code' => $httpCode
    ];
}

// Función para procesar archivos adjuntos
function procesarArchivos($archivos) {
    $archivosInfo = [];
    
    if (isset($archivos['adjuntos']) && is_array($archivos['adjuntos']['name'])) {
        for ($i = 0; $i < count($archivos['adjuntos']['name']); $i++) {
            if ($archivos['adjuntos']['error'][$i] === UPLOAD_ERR_OK) {
                $nombre = $archivos['adjuntos']['name'][$i];
                $tipo = $archivos['adjuntos']['type'][$i];
                $tamaño = $archivos['adjuntos']['size'][$i];
                
                $archivosInfo[] = [
                    'nombre' => $nombre,
                    'tipo' => $tipo,
                    'tamaño' => $tamaño
                ];
            }
        }
    }
    
    return $archivosInfo;
}

// Procesar solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener y validar datos
        $nombre = isset($_POST['nombre']) ? limpiarDatos($_POST['nombre']) : '';
        $apellido = isset($_POST['apellido']) ? limpiarDatos($_POST['apellido']) : '';
        $email = isset($_POST['email']) ? limpiarDatos($_POST['email']) : '';
        $telefono = isset($_POST['telefono']) ? limpiarDatos($_POST['telefono']) : '';
        $mensaje = isset($_POST['mensaje']) ? limpiarDatos($_POST['mensaje']) : '';
        
        // Validaciones
        if (empty($nombre) || empty($apellido) || empty($email) || empty($mensaje)) {
            throw new Exception('Todos los campos obligatorios deben estar completos.');
        }
        
        if (!validarEmail($email)) {
            throw new Exception('El formato del email no es válido.');
        }
        
        if (!empty($telefono) && !validarTelefono($telefono)) {
            throw new Exception('El formato del teléfono no es válido.');
        }
        
        // Procesar archivos adjuntos
        $archivosAdjuntos = procesarArchivos($_FILES);
        
        // Construir mensaje para WhatsApp
        $mensajeWhatsApp = "🆕 *NUEVO CONTACTO DESDE LA WEB*\n\n";
        $mensajeWhatsApp .= "👤 *Nombre:* {$nombre} {$apellido}\n";
        $mensajeWhatsApp .= "📧 *Email:* {$email}\n";
        
        if (!empty($telefono)) {
            $mensajeWhatsApp .= "📱 *Teléfono:* {$telefono}\n";
        }
        
        $mensajeWhatsApp .= "\n💬 *Mensaje:*\n{$mensaje}\n";
        
        if (!empty($archivosAdjuntos)) {
            $mensajeWhatsApp .= "\n📎 *Archivos adjuntos:*\n";
            foreach ($archivosAdjuntos as $archivo) {
                $mensajeWhatsApp .= "• {$archivo['nombre']} ({$archivo['tipo']}, " . 
                                  round($archivo['tamaño'] / 1024, 2) . " KB)\n";
            }
        }
        
        $mensajeWhatsApp .= "\n🌐 *Enviado desde:* " . $_SERVER['HTTP_HOST'] . "\n";
        $mensajeWhatsApp .= "⏰ *Fecha:* " . date('d/m/Y H:i:s') . "\n";
        
        // Enviar a WhatsApp
        $resultadoWhatsApp = enviarWhatsApp($mensajeWhatsApp);
        
        if ($resultadoWhatsApp['success']) {
            // Enviar email de confirmación al usuario
            $asuntoConfirmacion = "Confirmación de contacto - Sipán Inmobiliaria";
            $mensajeConfirmacion = "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9;'>
                    <div style='background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                        <h2 style='color: #1e7bbf; text-align: center; margin-bottom: 30px;'>
                            <i class='fas fa-check-circle'></i> ¡Mensaje Recibido!
                        </h2>
                        
                        <p>Hola <strong>{$nombre} {$apellido}</strong>,</p>
                        
                        <p>Hemos recibido tu mensaje y nos pondremos en contacto contigo lo antes posible.</p>
                        
                        <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                            <h4 style='color: #1e7bbf; margin-top: 0;'>Detalles de tu consulta:</h4>
                            <p><strong>Email:</strong> {$email}</p>
                            " . (!empty($telefono) ? "<p><strong>Teléfono:</strong> {$telefono}</p>" : "") . "
                            <p><strong>Mensaje:</strong> {$mensaje}</p>
                        </div>
                        
                        <p>Mientras tanto, puedes contactarnos directamente:</p>
                        <ul>
                            <li>📱 WhatsApp: <a href='https://wa.me/51932359551' style='color: #1e7bbf;'>+51 932 359 551</a></li>
                            <li>📧 Email: <a href='mailto:info@sipaninmobiliaria.com' style='color: #1e7bbf;'>info@sipaninmobiliaria.com</a></li>
                            <li>📍 Oficina: C.C Boulevar - Oficina j3 (2do piso - Patio principal)</li>
                        </ul>
                        
                        <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;'>
                            <p style='color: #666; font-size: 14px;'>
                                Gracias por confiar en <strong>Sipán Inmobiliaria</strong><br>
                                Tu socio inmobiliario de confianza
                            </p>
                        </div>
                    </div>
                </div>
            </body>
            </html>";
            
            $headersEmail = "MIME-Version: 1.0\r\n";
            $headersEmail .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headersEmail .= "From: Sipán Inmobiliaria <noreply@sipaninmobiliaria.com>\r\n";
            $headersEmail .= "Reply-To: info@sipaninmobiliaria.com\r\n";
            
            mail($email, $asuntoConfirmacion, $mensajeConfirmacion, $headersEmail);
            
            echo json_encode([
                'success' => true,
                'message' => '¡Mensaje enviado exitosamente! Nos pondremos en contacto contigo pronto.',
                'whatsapp_sent' => true
            ]);
        } else {
            // Si falla WhatsApp, enviar solo por email
            $asunto = "Nuevo contacto desde la web - Sipán Inmobiliaria";
            $mensajeEmail = "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #1e7bbf;'>🆕 Nuevo Contacto desde la Web</h2>
                    
                    <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                        <p><strong>Nombre:</strong> {$nombre} {$apellido}</p>
                        <p><strong>Email:</strong> {$email}</p>
                        " . (!empty($telefono) ? "<p><strong>Teléfono:</strong> {$telefono}</p>" : "") . "
                        <p><strong>Mensaje:</strong></p>
                        <p style='background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #1e7bbf;'>{$mensaje}</p>
                    </div>
                    
                    " . (!empty($archivosAdjuntos) ? "
                    <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                        <h4 style='color: #856404; margin-top: 0;'>📎 Archivos adjuntos:</h4>
                        <ul style='margin: 0; padding-left: 20px;'>
                        " . implode('', array_map(function($archivo) {
                            return "<li>{$archivo['nombre']} ({$archivo['tipo']}, " . round($archivo['tamaño'] / 1024, 2) . " KB)</li>";
                        }, $archivosAdjuntos)) . "
                        </ul>
                    </div>" : "") . "
                    
                    <p style='color: #666; font-size: 14px;'>
                        <strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "<br>
                        <strong>IP:</strong> " . $_SERVER['REMOTE_ADDR'] . "<br>
                        <strong>User Agent:</strong> " . $_SERVER['HTTP_USER_AGENT'] . "
                    </p>
                </div>
            </body>
            </html>";
            
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: {$email}\r\n";
            $headers .= "Reply-To: {$email}\r\n";
            
            if (mail('info@sipaninmobiliaria.com', $asunto, $mensajeEmail, $headers)) {
                echo json_encode([
                    'success' => true,
                    'message' => '¡Mensaje enviado exitosamente! (WhatsApp temporalmente no disponible)',
                    'whatsapp_sent' => false,
                    'email_sent' => true
                ]);
            } else {
                throw new Exception('Error al enviar el mensaje. Por favor, inténtelo nuevamente.');
            }
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?> 