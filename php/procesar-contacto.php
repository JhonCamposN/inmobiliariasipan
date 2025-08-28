<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config-database.php';

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

// Función para construir mensaje completo
function construirMensajeCompleto($nombre, $apellidos, $email, $telefono, $tipoConsulta, $mensaje) {
    $mensajeCompleto = "DATOS DEL CONTACTO:\n";
    $mensajeCompleto .= "Nombre: {$nombre} {$apellidos}\n";
    $mensajeCompleto .= "Email: {$email}\n";
    $mensajeCompleto .= "Teléfono: {$telefono}\n";
    
    if (!empty($tipoConsulta)) {
        $mensajeCompleto .= "Tipo de consulta: {$tipoConsulta}\n";
    }
    
    $mensajeCompleto .= "\nMENSAJE:\n{$mensaje}";
    
    return $mensajeCompleto;
}

// Función para construir mensaje de WhatsApp
function construirMensajeWhatsApp($nombre, $apellidos, $email, $telefono, $mensaje) {
    $mensajeWhatsApp = "🆕 *NUEVO CONTACTO DESDE LA WEB*\n\n";
    $mensajeWhatsApp .= "👤 *Nombre:* {$nombre} {$apellidos}\n";
    $mensajeWhatsApp .= "📧 *Email:* {$email}\n";
    
    if (!empty($telefono)) {
        $mensajeWhatsApp .= "📱 *Teléfono:* {$telefono}\n";
    }
    
    $mensajeWhatsApp .= "\n💬 *Mensaje:*\n{$mensaje}\n";
    $mensajeWhatsApp .= "\n🌐 *Enviado desde:* " . $_SERVER['HTTP_HOST'] . "\n";
    $mensajeWhatsApp .= "⏰ *Fecha:* " . date('d/m/Y H:i:s') . "\n";
    $mensajeWhatsApp .= "\n🏢 *Sipán Inmobiliaria*\n";
    $mensajeWhatsApp .= "📍 C.C Boulevar - Oficina j3 (2do piso - Patio principal)\n";
    $mensajeWhatsApp .= "📞 +51 932 359 551";
    
    return $mensajeWhatsApp;
}

// Procesar solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Conectar a la base de datos usando la función del config
        $pdo = conectarDB();
        if (!$pdo) {
            throw new Exception('No se pudo conectar a la base de datos. Verifica la configuración.');
        }
        
        // Obtener y validar datos
        $nombre = isset($_POST['nombre']) ? limpiarDatos($_POST['nombre']) : '';
        $apellidos = isset($_POST['apellidos']) ? limpiarDatos($_POST['apellidos']) : '';
        $email = isset($_POST['email']) ? limpiarDatos($_POST['email']) : '';
        $telefono = isset($_POST['telefono']) ? limpiarDatos($_POST['telefono']) : '';
        $tipoConsulta = isset($_POST['tipo_consulta']) ? limpiarDatos($_POST['tipo_consulta']) : '';
        $mensaje = isset($_POST['mensaje']) ? limpiarDatos($_POST['mensaje']) : '';
        
        // Validaciones
        if (empty($nombre) || empty($apellidos) || empty($email) || empty($mensaje)) {
            throw new Exception('Todos los campos obligatorios deben estar completos.');
        }
        
        if (!validarEmail($email)) {
            throw new Exception('El formato del email no es válido.');
        }
        
        if (!empty($telefono) && !validarTelefono($telefono)) {
            throw new Exception('El formato del teléfono no es válido.');
        }
        
        // Construir mensaje completo para la base de datos
        $mensajeCompleto = construirMensajeCompleto($nombre, $apellidos, $email, $telefono, $tipoConsulta, $mensaje);
        $nombreCompleto = $nombre . ' ' . $apellidos;
        
        // Obtener IP del cliente
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // Insertar en la base de datos
        $stmt = $pdo->prepare("
            INSERT INTO contactos (nombre, email, telefono, mensaje, fecha_creacion, estado, ip_address) 
            VALUES (?, ?, ?, ?, NOW(), 'pendiente', ?)
        ");
        
        $stmt->execute([
            $nombreCompleto,
            $email,
            $telefono,
            $mensajeCompleto,
            $ipAddress
        ]);
        
        $contactoId = $pdo->lastInsertId();
        
        // Construir mensaje para WhatsApp
        $mensajeWhatsApp = construirMensajeWhatsApp($nombre, $apellidos, $email, $telefono, $mensaje);
        
        // Codificar mensaje para URL de WhatsApp
        $mensajeCodificado = rawurlencode($mensajeWhatsApp);
        
        // Crear URL de WhatsApp
        $whatsappUrl = "https://wa.me/51932359551?text=" . $mensajeCodificado;
        
        // Devolver respuesta exitosa
        echo json_encode([
            'success' => true,
            'message' => '¡Formulario enviado exitosamente! Tus datos han sido guardados y nos pondremos en contacto contigo pronto.',
            'whatsapp_url' => $whatsappUrl,
            'contacto_id' => $contactoId,
            'method' => 'database_and_whatsapp'
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (PDOException $e) {
        error_log("Error PDO en procesar-contacto.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos. Verifica que XAMPP esté ejecutándose y la base de datos configurada correctamente.'
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
