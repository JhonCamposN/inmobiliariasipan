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
function construirMensajeWhatsApp($nombre, $apellidos, $email, $telefono, $dni, $tipoConsulta, $mensaje) {
    // Configurar zona horaria de Perú
    date_default_timezone_set('America/Lima');
    
    // Determinar el tipo de mensaje según la consulta
    $tiposEspeciales = [
        'venta-propiedades' => 'CONSULTA DE VENTA DE PROPIEDADES',
        'desarrollos-inmobiliarios' => 'CONSULTA DE DESARROLLOS INMOBILIARIOS', 
        'gestion-proyectos' => 'CONSULTA DE GESTION DE PROYECTOS',
        'tramites-legales' => 'CONSULTA DE TRAMITES LEGALES',
        'servicios-postventa' => 'CONSULTA DE SERVICIOS POSTVENTA',
        'asesoria-financiera' => 'CONSULTA DE ASESORIA FINANCIERA',
        'dominio-media-luna' => 'CONSULTA PROYECTO DOMINIO MEDIA LUNA'
    ];
    
    $tipoMensaje = isset($tiposEspeciales[$tipoConsulta]) ? $tiposEspeciales[$tipoConsulta] : 'CONSULTA GENERAL';
    
    // Crear mensaje personalizado según el tipo
    if (in_array($tipoConsulta, ['venta-propiedades', 'desarrollos-inmobiliarios', 'dominio-media-luna'])) {
        // Mensaje estilo personal para ventas y proyectos
        $mensajeWhatsApp = "Hola, soy *{$nombre} {$apellidos}*. ";
        $mensajeWhatsApp .= "Estoy interesado(a) en obtener más información sobre ";
        
        switch($tipoConsulta) {
            case 'venta-propiedades':
                $mensajeWhatsApp .= "la compra/venta de propiedades";
                break;
            case 'desarrollos-inmobiliarios':
                $mensajeWhatsApp .= "sus desarrollos inmobiliarios";
                break;
            case 'dominio-media-luna':
                $mensajeWhatsApp .= "el Proyecto Dominio Media Luna";
                break;
        }
        
        $mensajeWhatsApp .= ".\n\n";
        $mensajeWhatsApp .= "*Mis datos de contacto:*\n";
        $mensajeWhatsApp .= "DNI: {$dni}\n";
        $mensajeWhatsApp .= "Email: {$email}\n";
        $mensajeWhatsApp .= "Teléfono: {$telefono}\n\n";
        $mensajeWhatsApp .= "*Detalles de mi consulta:*\n{$mensaje}\n\n";
        $mensajeWhatsApp .= "Quedo atento(a) a su respuesta. ¡Gracias!";
        
    } else {
        // Mensaje formal para servicios especializados
        $mensajeWhatsApp = "*{$tipoMensaje} - SIPAN INMOBILIARIA*\n\n";
        $mensajeWhatsApp .= "Fecha y hora: " . date('d/m/Y, h:i a') . "\n";
        $mensajeWhatsApp .= "Nombre completo: {$nombre} {$apellidos}\n";
        $mensajeWhatsApp .= "DNI: {$dni}\n";
        $mensajeWhatsApp .= "Teléfono: {$telefono}\n";
        $mensajeWhatsApp .= "Email: {$email}\n\n";
        
        // Detalles específicos según el tipo de consulta
        switch($tipoConsulta) {
            case 'gestion-proyectos':
                $mensajeWhatsApp .= "*Detalles del proyecto:*\n";
                break;
            case 'tramites-legales':
                $mensajeWhatsApp .= "*Detalles de trámites requeridos:*\n";
                break;
            case 'servicios-postventa':
                $mensajeWhatsApp .= "*Detalles del servicio postventa:*\n";
                break;
            case 'asesoria-financiera':
                $mensajeWhatsApp .= "*Detalles de asesoría financiera:*\n";
                break;
            default:
                $mensajeWhatsApp .= "*Detalles de la consulta:*\n";
        }
        
        $mensajeWhatsApp .= "{$mensaje}\n\n";
        $mensajeWhatsApp .= "Mensaje generado automáticamente desde el formulario web de Sipán Inmobiliaria\n";
        $mensajeWhatsApp .= "C.C Boulevard - Oficina J-3\n";
        $mensajeWhatsApp .= "+51 932 359 551";
    }
    
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
        $dni = isset($_POST['dni']) ? limpiarDatos($_POST['dni']) : '';
        $email = isset($_POST['email']) ? limpiarDatos($_POST['email']) : '';
        $telefono = isset($_POST['telefono']) ? limpiarDatos($_POST['telefono']) : '';
        $tipoConsulta = isset($_POST['tipo_consulta']) ? limpiarDatos($_POST['tipo_consulta']) : '';
        $mensaje = isset($_POST['mensaje']) ? limpiarDatos($_POST['mensaje']) : '';
        
        // Validaciones
        if (empty($nombre) || empty($apellidos) || empty($dni) || empty($email)) {
            throw new Exception('Todos los campos obligatorios deben estar completos.');
        }
        
        // Debug: Log para verificar el mensaje recibido
        error_log("Mensaje recibido: '" . $mensaje . "' - Tipo consulta: '" . $tipoConsulta . "'");
        
        // Si el mensaje está vacío, generar mensaje automático según el tipo de consulta
        if (empty($mensaje) || trim($mensaje) === '') {
            error_log("Generando mensaje automático para tipo: " . $tipoConsulta);
            
            $mensajesAutomaticos = [
                'venta-propiedades' => 'Necesito más información sobre la compra/venta de propiedades. Me gustaría conocer las opciones disponibles y el proceso.',
                'desarrollos-inmobiliarios' => 'Necesito más información sobre sus desarrollos inmobiliarios. Estoy interesado en conocer los proyectos disponibles.',
                'gestion-proyectos' => 'Necesito más información sobre la gestión de proyectos inmobiliarios. Requiero asesoría para mi proyecto.',
                'tramites-legales' => 'Necesito más información sobre trámites legales inmobiliarios. Requiero asesoría en documentación y procesos legales.',
                'servicios-postventa' => 'Necesito más información sobre los servicios postventa. Me interesa conocer las garantías y soporte disponible.',
                'asesoria-financiera' => 'Necesito más información sobre asesoría financiera. Estoy interesado en opciones de financiamiento y programas disponibles.',
                'dominio-media-luna' => 'Necesito más información sobre el Proyecto Dominio Media Luna. Me gustaría conocer detalles, precios y disponibilidad.',
                'mi-vivienda' => 'Necesito más información sobre el Programa Mi Vivienda. Requiero asesoría sobre requisitos y proceso.',
                'requisitos-mi-vivienda' => 'Necesito más información sobre los requisitos para Mi Vivienda. Quiero conocer qué documentos y condiciones necesito.',
                'financiamiento' => 'Necesito más información sobre opciones de financiamiento. Estoy interesado en conocer las alternativas disponibles.',
                'visita-proyecto' => 'Necesito más información para agendar una visita al proyecto. Me gustaría coordinar una cita.',
                'asesoria-general' => 'Necesito más información y asesoría general inmobiliaria. Estoy explorando opciones de inversión.',
                'otro' => 'Necesito más información. Me gustaría que se pongan en contacto conmigo para brindarme detalles.'
            ];
            
            $mensaje = isset($mensajesAutomaticos[$tipoConsulta]) ? $mensajesAutomaticos[$tipoConsulta] : 'Necesito más información. Por favor, contáctenme para brindarme detalles.';
            error_log("Mensaje automático generado: " . $mensaje);
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
            INSERT INTO contactos (nombre, dni, email, telefono, mensaje, fecha_creacion, estado, ip_address) 
            VALUES (?, ?, ?, ?, ?, NOW(), 'pendiente', ?)
        ");
        
        $stmt->execute([
            $nombreCompleto,
            $dni,
            $email,
            $telefono,
            $mensajeCompleto,
            $ipAddress
        ]);
        
        $contactoId = $pdo->lastInsertId();
        
        // Construir mensaje para WhatsApp
        $mensajeWhatsApp = construirMensajeWhatsApp($nombre, $apellidos, $email, $telefono, $dni, $tipoConsulta, $mensaje);
        
        // Codificar mensaje para URL de WhatsApp (usar urlencode para emojis)
        $mensajeCodificado = urlencode($mensajeWhatsApp);
        
        // Crear URL de WhatsApp
        $whatsappUrl = "https://wa.me/51932359551?text=" . $mensajeCodificado;
        
        // Devolver respuesta exitosa
        echo json_encode([
            'success' => true,
            'message' => '¡Formulario enviado exitosamente! Tus datos han sido guardados.',
            'whatsapp_url' => $whatsappUrl,
            'contacto_id' => $contactoId,
            'method' => 'manual_whatsapp'
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
