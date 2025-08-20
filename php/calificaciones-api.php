<?php
// Habilitar reporte de errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en pantalla, solo en logs
ini_set('log_errors', 1);

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once 'config-database.php';
} catch (Exception $e) {
    error_log("Error al cargar config-database.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error de configuración del servidor'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Función para limpiar datos
function limpiarDatos($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato, ENT_QUOTES, 'UTF-8');
    return $dato;
}

// Función para formatear fecha relativa
function tiempoRelativo($fecha) {
    $ahora = new DateTime();
    $fecha = new DateTime($fecha);
    $diferencia = $ahora->diff($fecha);
    
    if ($diferencia->y > 0) {
        return "Hace " . $diferencia->y . " año" . ($diferencia->y > 1 ? "s" : "");
    } elseif ($diferencia->m > 0) {
        return "Hace " . $diferencia->m . " mes" . ($diferencia->m > 1 ? "es" : "");
    } elseif ($diferencia->d > 0) {
        return "Hace " . $diferencia->d . " día" . ($diferencia->d > 1 ? "s" : "");
    } elseif ($diferencia->h > 0) {
        return "Hace " . $diferencia->h . " hora" . ($diferencia->h > 1 ? "s" : "");
    } elseif ($diferencia->i > 0) {
        return "Hace " . $diferencia->i . " minuto" . ($diferencia->i > 1 ? "s" : "");
    } else {
        return "Ahora";
    }
}

// Manejar diferentes tipos de peticiones
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        // Verificar conexión a BD primero
        $dbStatus = verificarDB();
        if (!$dbStatus['success']) {
            echo json_encode([
                'success' => false,
                'message' => 'Error de base de datos: ' . $dbStatus['message']
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $accion = $_GET['accion'] ?? '';
        
        if ($accion === 'estadisticas') {
            $estadisticas = obtenerEstadisticas();
            if ($estadisticas) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'total_calificaciones' => $estadisticas['total_calificaciones'],
                        'promedio_calificacion' => number_format($estadisticas['promedio_calificacion'], 1),
                        'porcentaje_satisfechos' => number_format($estadisticas['porcentaje_satisfechos'], 0)
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudieron obtener las estadísticas'
                ], JSON_UNESCAPED_UNICODE);
            }
        } elseif ($accion === 'comentarios') {
            $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 3;
            $comentarios = obtenerComentariosRecientes($limite);
            $comentariosFormateados = [];
            
            foreach ($comentarios as $comentario) {
                $comentariosFormateados[] = [
                    'nombre' => $comentario['nombre'],
                    'calificacion' => (int)$comentario['calificacion'],
                    'comentario' => $comentario['comentario'],
                    'fecha' => tiempoRelativo($comentario['fecha_creacion'])
                ];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $comentariosFormateados
            ], JSON_UNESCAPED_UNICODE);
        } else {
            // Obtener todo: estadísticas y comentarios (solo 3 más recientes)
            $estadisticas = obtenerEstadisticas();
            $comentarios = obtenerComentariosRecientes(3);
            
            $comentariosFormateados = [];
            foreach ($comentarios as $comentario) {
                $comentariosFormateados[] = [
                    'nombre' => $comentario['nombre'],
                    'calificacion' => (int)$comentario['calificacion'],
                    'comentario' => $comentario['comentario'],
                    'fecha' => tiempoRelativo($comentario['fecha_creacion'])
                ];
            }
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'estadisticas' => $estadisticas ? [
                        'total_calificaciones' => $estadisticas['total_calificaciones'],
                        'promedio_calificacion' => number_format($estadisticas['promedio_calificacion'], 1),
                        'porcentaje_satisfechos' => number_format($estadisticas['porcentaje_satisfechos'], 0)
                    ] : null,
                    'comentarios' => $comentariosFormateados
                ]
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
        
    case 'POST':
        // Leer datos de entrada
        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        
        if (!$input) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos no válidos'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $nombre = limpiarDatos($input['nombre'] ?? '');
        $email = limpiarDatos($input['email'] ?? '');
        $calificacion = (int)($input['calificacion'] ?? 0);
        $comentario = limpiarDatos($input['comentario'] ?? '');
        
        // Validar datos
        $errores = validarCalificacion($nombre, $email, $calificacion, $comentario);
        
        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $errores
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Verificar si la base de datos está disponible
        $dbStatus = verificarDB();
        if (!$dbStatus['success']) {
            echo json_encode([
                'success' => false,
                'message' => 'Error de base de datos: ' . $dbStatus['message']
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Agregar calificación
        $resultado = agregarCalificacion($nombre, $email, $calificacion, $comentario);
        
        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => '¡Gracias por tu calificación! Tu comentario ha sido agregado exitosamente.',
                'data' => [
                    'nombre' => $nombre,
                    'calificacion' => $calificacion,
                    'comentario' => $comentario,
                    'fecha' => 'Ahora'
                ]
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al guardar la calificación. Inténtalo de nuevo.'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ], JSON_UNESCAPED_UNICODE);
        break;
}
?>