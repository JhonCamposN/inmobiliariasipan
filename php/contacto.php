<?php
// Configuración de seguridad
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Función para limpiar datos
function limpiarDatos($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para validar email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Función para validar teléfono
function validarTelefono($telefono) {
    return preg_match('/^[\+]?[0-9\s\-\(\)]{7,15}$/', $telefono);
}

try {
    // Obtener datos del formulario
    $nombre = isset($_POST['nombre']) ? limpiarDatos($_POST['nombre']) : '';
    $apellido = isset($_POST['apellido']) ? limpiarDatos($_POST['apellido']) : '';
    $email = isset($_POST['email']) ? limpiarDatos($_POST['email']) : '';
    $telefono = isset($_POST['telefono']) ? limpiarDatos($_POST['telefono']) : '';
    $mensaje = isset($_POST['mensaje']) ? limpiarDatos($_POST['mensaje']) : '';
    
    // Validaciones
    if (empty($nombre) || strlen($nombre) < 2) {
        throw new Exception('El nombre es requerido y debe tener al menos 2 caracteres');
    }
    
    if (empty($apellido) || strlen($apellido) < 2) {
        throw new Exception('El apellido es requerido y debe tener al menos 2 caracteres');
    }
    
    if (empty($email) || !validarEmail($email)) {
        throw new Exception('El email es requerido y debe ser válido');
    }
    
    if (!empty($telefono) && !validarTelefono($telefono)) {
        throw new Exception('El teléfono debe tener un formato válido');
    }
    
    if (empty($mensaje) || strlen($mensaje) < 10) {
        throw new Exception('El mensaje es requerido y debe tener al menos 10 caracteres');
    }
    
    // Configuración del email
    $para = 'info@sipaninmobiliaria.com';
    $asunto = 'Nuevo mensaje de contacto - Sipán Inmobiliaria';
    
    // Construir el mensaje
    $contenido = "Se ha recibido un nuevo mensaje de contacto:\n\n";
    $contenido .= "Nombre: " . $nombre . " " . $apellido . "\n";
    $contenido .= "Email: " . $email . "\n";
    $contenido .= "Teléfono: " . ($telefono ? $telefono : 'No proporcionado') . "\n";
    $contenido .= "Mensaje:\n" . $mensaje . "\n\n";
    $contenido .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
    $contenido .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    
    // Headers del email
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Enviar email
    if (mail($para, $asunto, $contenido, $headers)) {
        // Email de confirmación al usuario
        $confirmacion_asunto = 'Gracias por contactarnos - Sipán Inmobiliaria';
        $confirmacion_contenido = "Estimado/a " . $nombre . " " . $apellido . ",\n\n";
        $confirmacion_contenido .= "Hemos recibido su mensaje y nos pondremos en contacto con usted pronto.\n\n";
        $confirmacion_contenido .= "Sus datos:\n";
        $confirmacion_contenido .= "Email: " . $email . "\n";
        $confirmacion_contenido .= "Teléfono: " . ($telefono ? $telefono : 'No proporcionado') . "\n\n";
        $confirmacion_contenido .= "Mensaje:\n" . $mensaje . "\n\n";
        $confirmacion_contenido .= "Atentamente,\n";
        $confirmacion_contenido .= "Equipo de Sipán Inmobiliaria\n";
        $confirmacion_contenido .= "Tel: +51 932 359 551\n";
        $confirmacion_contenido .= "Email: info@sipaninmobiliaria.com";
        
        $confirmacion_headers = "From: info@sipaninmobiliaria.com\r\n";
        $confirmacion_headers .= "Reply-To: info@sipaninmobiliaria.com\r\n";
        $confirmacion_headers .= "X-Mailer: PHP/" . phpversion();
        
        mail($email, $confirmacion_asunto, $confirmacion_contenido, $confirmacion_headers);
        
        // Respuesta exitosa
        echo json_encode([
            'success' => true,
            'message' => 'Mensaje enviado exitosamente. Nos pondremos en contacto pronto.'
        ]);
    } else {
        throw new Exception('Error al enviar el email. Por favor, inténtelo nuevamente.');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?> 