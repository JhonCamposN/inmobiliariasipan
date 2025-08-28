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

// Función para formatear fecha relativa o absoluta
function tiempoRelativo($fecha) {
    // Configurar zona horaria de Perú
    date_default_timezone_set('America/Lima');
    
    $ahora = new DateTime();
    $fechaComentario = new DateTime($fecha);
    $diferencia = $ahora->diff($fechaComentario);
    
    // Calcular total de horas transcurridas
    $totalHoras = ($diferencia->days * 24) + $diferencia->h;
    
    // Si han pasado menos de 24 horas, mostrar formato relativo
    if ($totalHoras < 24) {
        if ($diferencia->h > 0) {
            return "Hace " . $diferencia->h . " hora" . ($diferencia->h > 1 ? "s" : "");
        } elseif ($diferencia->i > 0) {
            return "Hace " . $diferencia->i . " minuto" . ($diferencia->i > 1 ? "s" : "");
        } else {
            return "Ahora";
        }
    } else {
        // Si han pasado 24 horas o más, mostrar fecha exacta en formato DD/MM/YYYY
        return $fechaComentario->format('d/m/Y');
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
                $fechaCreacion = new DateTime($comentario['fecha_creacion']);
                $comentariosFormateados[] = [
                    'nombre' => $comentario['nombre'],
                    'email' => $comentario['email'] ?? '',
                    'calificacion' => (int)$comentario['calificacion'],
                    'comentario' => $comentario['comentario'],
                    'fecha' => tiempoRelativo($comentario['fecha_creacion']),
                    'fecha_completa' => $fechaCreacion->format('d/m/Y H:i:s'),
                    'ip_address' => $comentario['ip_address'] ?? 'N/A'
                ];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $comentariosFormateados
            ], JSON_UNESCAPED_UNICODE);
        } elseif ($accion === 'todas') {
            // Obtener todas las calificaciones para el dashboard
            $pdo = conectarDB();
            $stmt = $pdo->prepare("
                SELECT id, nombre, email, calificacion, comentario, fecha_creacion, activo 
                FROM calificaciones 
                ORDER BY fecha_creacion DESC
            ");
            $stmt->execute();
            $calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $calificacionesFormateadas = [];
            foreach ($calificaciones as $calificacion) {
                $fechaCreacion = new DateTime($calificacion['fecha_creacion']);
                $calificacionesFormateadas[] = [
                    'id' => (int)$calificacion['id'],
                    'nombre' => $calificacion['nombre'],
                    'email' => $calificacion['email'] ?? '',
                    'calificacion' => (int)$calificacion['calificacion'],
                    'comentario' => $calificacion['comentario'],
                    'fecha' => $fechaCreacion->format('d/m/Y'),
                    'fecha_completa' => $fechaCreacion->format('d/m/Y H:i:s'),
                    'activo' => (int)$calificacion['activo']
                ];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $calificacionesFormateadas
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
        $pdo = conectarDB();
        $stmt = $pdo->prepare("
            SELECT id, nombre, email, calificacion, comentario, fecha_creacion 
            FROM calificaciones 
            WHERE activo = 1
            ORDER BY fecha_creacion DESC
        ");
        $stmt->execute();
        $comentarios = $stmt->fetchAll();
        
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
        
    case 'DELETE':
        if (isset($_GET['id']) && isset($_GET['action'])) {
            eliminarCalificacion($_GET['id'], $_GET['action']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Parámetros faltantes']);
        }
        break;
        
    case 'PUT':
        if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] === 'restaurar') {
            restaurarCalificacion($_GET['id']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Parámetros faltantes']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
}

function eliminarCalificacion($id, $action) {
    try {
        $id = (int)$id;
        
        if ($id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de calificación no válido'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $pdo = conectarDB();
        
        if ($action === 'ocultar') {
            // Eliminación lógica
            $stmt = $pdo->prepare("UPDATE calificaciones SET activo = 0 WHERE id = ?");
            $resultado = $stmt->execute([$id]);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Calificación ocultada exitosamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al ocultar la calificación'
                ], JSON_UNESCAPED_UNICODE);
            }
        } elseif ($action === 'eliminar') {
            // Eliminación permanente
            $stmt = $pdo->prepare("DELETE FROM calificaciones WHERE id = ?");
            $resultado = $stmt->execute([$id]);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Calificación eliminada permanentemente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al eliminar la calificación'
                ], JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida'
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error del servidor: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

function restaurarCalificacion($id) {
    try {
        $id = (int)$id;
        
        if ($id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de calificación no válido'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $pdo = conectarDB();
        $stmt = $pdo->prepare("UPDATE calificaciones SET activo = 1 WHERE id = ?");
        $resultado = $stmt->execute([$id]);
        
        if ($resultado && $stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Calificación restaurada exitosamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo restaurar la calificación o ya estaba activa'
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error del servidor: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}
?>