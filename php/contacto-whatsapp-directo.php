<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Función para limpiar datos
function limpiarDatos($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato, ENT_QUOTES, 'UTF-8');
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

// Función para procesar archivos adjuntos
function procesarArchivos($archivos) {
    $archivosInfo = [];
    
    if (isset($archivos['adjuntos']) && is_array($archivos['adjuntos']['name'])) {
        for ($i = 0; $i < count($archivos['adjuntos']['name']); $i++) {
            if ($archivos['adjuntos']['error'][$i] === UPLOAD_ERR_OK) {
                $nombre = $archivos['adjuntos']['name'][$i];
                $tipo = $archivos['adjuntos']['type'][$i];
                $tamaño = $archivos['adjuntos']['size'][$i];
                
                // Validar tipo de archivo
                $tiposPermitidos = [
                    'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
                    'video/mp4', 'video/avi', 'video/mov', 'video/wmv',
                    'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'text/plain'
                ];
                
                if (in_array($tipo, $tiposPermitidos) && $tamaño <= 10 * 1024 * 1024) { // 10MB máximo
                    $archivosInfo[] = [
                        'nombre' => $nombre,
                        'tipo' => $tipo,
                        'tamaño' => $tamaño
                    ];
                }
            }
        }
    }
    
    return $archivosInfo;
}

// Función para construir mensaje de WhatsApp con emojis
function construirMensajeWhatsApp($nombre, $apellido, $email, $telefono, $mensaje, $archivosAdjuntos) {
    // Usar emojis Unicode directamente
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
            $tamañoKB = round($archivo['tamaño'] / 1024, 2);
            $mensajeWhatsApp .= "• 📄 {$archivo['nombre']} ({$archivo['tipo']}, {$tamañoKB} KB)\n";
        }
    }
    
    $mensajeWhatsApp .= "\n🌐 *Enviado desde:* " . $_SERVER['HTTP_HOST'] . "\n";
    $mensajeWhatsApp .= "⏰ *Fecha:* " . date('d/m/Y H:i:s') . "\n";
    $mensajeWhatsApp .= "\n🏢 *Sipán Inmobiliaria*\n";
    $mensajeWhatsApp .= "📍 C.C Boulevar - Oficina j3 (2do piso - Patio principal)\n";
    $mensajeWhatsApp .= "📞 +51 932 359 551";
    
    return $mensajeWhatsApp;
}

// Función para enviar email de confirmación
function enviarEmailConfirmacion($nombre, $apellido, $email, $telefono, $mensaje) {
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
    
    return mail($email, $asuntoConfirmacion, $mensajeConfirmacion, $headersEmail);
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
        $mensajeWhatsApp = construirMensajeWhatsApp($nombre, $apellido, $email, $telefono, $mensaje, $archivosAdjuntos);
        
        // Codificar mensaje para URL de WhatsApp usando rawurlencode para preservar emojis
        $mensajeCodificado = rawurlencode($mensajeWhatsApp);
        
        // Crear URL de WhatsApp
        $whatsappUrl = "https://wa.me/51932359551?text=" . $mensajeCodificado;
        
        // Email deshabilitado temporalmente
        $emailEnviado = false;
        
        // Devolver respuesta exitosa
        echo json_encode([
            'success' => true,
            'message' => '¡Formulario procesado exitosamente! Se abrirá WhatsApp con el mensaje listo para enviar.',
            'whatsapp_url' => $whatsappUrl,
            'email_sent' => $emailEnviado,
            'method' => 'direct',
            'archivos_count' => count($archivosAdjuntos)
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ], JSON_UNESCAPED_UNICODE);
}
?> 