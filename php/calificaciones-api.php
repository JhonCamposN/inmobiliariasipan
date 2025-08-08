<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config-database.php';

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
        // Obtener estadísticas y comentarios
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
            $comentarios = obtenerComentariosRecientes(10);
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
            // Obtener todo: estadísticas y comentarios
            $estadisticas = obtenerEstadisticas();
            $comentarios = obtenerComentariosRecientes(10);
            
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
        // Agregar nueva calificación
        $input = json_decode(file_get_contents('php://input'), true);
        
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