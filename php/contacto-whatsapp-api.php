<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir configuración de WhatsApp API
require_once 'config-whatsapp-api.php';

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
    $archivosParaEnviar = [];
    
    if (isset($archivos['adjuntos']) && is_array($archivos['adjuntos']['name'])) {
        for ($i = 0; $i < count($archivos['adjuntos']['name']); $i++) {
            if ($archivos['adjuntos']['error'][$i] === UPLOAD_ERR_OK) {
                $nombre = $archivos['adjuntos']['name'][$i];
                $tipo = $archivos['adjuntos']['type'][$i];
                $tamaño = $archivos['adjuntos']['size'][$i];
                $tmpPath = $archivos['adjuntos']['tmp_name'][$i];
                
                // Validar tipo y tamaño
                if (in_array($tipo, ALLOWED_FILE_TYPES) && $tamaño <= MAX_FILE_SIZE) {
                    $archivosInfo[] = [
                        'nombre' => $nombre,
                        'tipo' => $tipo,
                        'tamaño' => $tamaño
                    ];
                    
                    $archivosParaEnviar[] = [
                        'path' => $tmpPath,
                        'name' => $nombre,
                        'type' => $tipo
                    ];
                }
            }
        }
    }
    
    return [
        'info' => $archivosInfo,
        'files' => $archivosParaEnviar
    ];
}

// Función para construir mensaje de WhatsApp
function construirMensajeWhatsApp($nombre, $apellido, $email, $telefono, $mensaje, $archivosInfo) {
    $mensajeWhatsApp = "🆕 *NUEVO CONTACTO DESDE LA WEB*\n\n";
    $mensajeWhatsApp .= "👤 *Nombre:* {$nombre} {$apellido}\n";
    $mensajeWhatsApp .= "📧 *Email:* {$email}\n";
    
    if (!empty($telefono)) {
        $mensajeWhatsApp .= "📱 *Teléfono:* {$telefono}\n";
    }
    
    $mensajeWhatsApp .= "\n💬 *Mensaje:*\n{$mensaje}\n";
    
    if (!empty($archivosInfo)) {
        $mensajeWhatsApp .= "\n📎 *Archivos adjuntos:*\n";
        foreach ($archivosInfo as $archivo) {
            $tamañoKB = round($archivo['tamaño'] / 1024, 2);
            $mensajeWhatsApp .= "• 📄 {$archivo['nombre']} ({$archivo['tipo']}, {$tamañoKB} KB)\n";
        }
    }
    
    $mensajeWhatsApp .= "\n🌐 *Enviado desde:* " . $_SERVER['HTTP_HOST'] . "\n";
    $mensajeWhatsApp .= "⏰ *Fecha:* " . date('d/m/Y H:i:s') . "\n";
    $mensajeWhatsApp .= "\n🏢 *" . COMPANY_NAME . "*\n";
    $mensajeWhatsApp .= "📍 " . COMPANY_ADDRESS . "\n";
    $mensajeWhatsApp .= "📞 " . COMPANY_PHONE;
    
    return $mensajeWhatsApp;
}

// Procesar solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificar configuración de la API
        $configStatus = checkWhatsAppAPIStatus();
        if (!$configStatus['success']) {
            throw new Exception('Error de configuración: ' . implode(', ', $configStatus['errors'] ?? ['Configuración incompleta']));
        }
        
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
        $archivosResult = procesarArchivos($_FILES);
        $archivosInfo = $archivosResult['info'];
        $archivosParaEnviar = $archivosResult['files'];
        
        // Construir mensaje para WhatsApp
        $mensajeWhatsApp = construirMensajeWhatsApp($nombre, $apellido, $email, $telefono, $mensaje, $archivosInfo);
        
        // Enviar mensaje usando la API
        $numeroDestino = COMPANY_PHONE; // Enviar al número de la empresa
        $numeroDestino = preg_replace('/[^0-9]/', '', $numeroDestino); // Solo números
        
        if (!empty($archivosParaEnviar)) {
            // Enviar mensaje con archivos
            $resultado = sendWhatsAppMessageWithFiles($numeroDestino, $mensajeWhatsApp, $archivosParaEnviar);
        } else {
            // Enviar solo mensaje de texto
            $resultado = sendWhatsAppTextMessage($numeroDestino, $mensajeWhatsApp);
        }
        
        if ($resultado['success']) {
            echo json_encode([
                'success' => true,
                'message' => '¡Mensaje enviado exitosamente a WhatsApp!',
                'method' => 'api',
                'archivos_count' => count($archivosInfo),
                'api_response' => $resultado['data']
            ], JSON_UNESCAPED_UNICODE);
        } else {
            throw new Exception('Error al enviar mensaje: ' . ($resultado['error'] ?? 'Error desconocido'));
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'method' => 'api'
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ], JSON_UNESCAPED_UNICODE);
}
?> 