<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config-database.php';

try {
    $pdo = conectarDB();
    if (!$pdo) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch($method) {
        case 'GET':
            if (isset($_GET['action'])) {
                switch($_GET['action']) {
                    case 'estadisticas':
                        obtenerEstadisticasContactos($pdo);
                        break;
                    case 'contactos':
                        obtenerContactos($pdo);
                        break;
                    default:
                        http_response_code(400);
                        echo json_encode(['error' => 'Acción no válida']);
                }
            } elseif (isset($_GET['id'])) {
                obtenerContactoPorId($pdo, $_GET['id']);
            } else {
                obtenerContactos($pdo);
            }
            break;
            
        case 'PUT':
            if (isset($_GET['id']) && isset($_GET['action'])) {
                if ($_GET['action'] === 'estado') {
                    actualizarEstadoContacto($pdo, $_GET['id']);
                } elseif ($_GET['action'] === 'restaurar') {
                    restaurarContacto($pdo, $_GET['id']);
                } elseif ($_GET['action'] === 'revertir') {
                    revertirEstadoContacto($pdo, $_GET['id']);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Acción no válida']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Parámetros faltantes']);
            }
            break;
            
        case 'DELETE':
            if (isset($_GET['id']) && isset($_GET['action'])) {
                eliminarContacto($pdo, $_GET['id'], $_GET['action']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Parámetros faltantes']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
}

function obtenerContactos($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, nombre, dni, email, telefono, mensaje, fecha_creacion, estado, activo 
            FROM contactos 
            ORDER BY fecha_creacion DESC
        ");
        $stmt->execute();
        $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear fechas y limpiar datos
        foreach ($contactos as &$contacto) {
            $fecha = new DateTime($contacto['fecha_creacion']);
            $contacto['fecha_formateada'] = $fecha->format('d/m/Y');
            $contacto['fecha_hora'] = $fecha->format('d/m/Y H:i');
            
            // Limpiar y validar datos para exportación
            $contacto['nombre'] = htmlspecialchars($contacto['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
            $contacto['dni'] = htmlspecialchars($contacto['dni'] ?? '', ENT_QUOTES, 'UTF-8');
            $contacto['email'] = htmlspecialchars($contacto['email'] ?? '', ENT_QUOTES, 'UTF-8');
            $contacto['telefono'] = htmlspecialchars($contacto['telefono'] ?? '', ENT_QUOTES, 'UTF-8');
            $contacto['mensaje'] = htmlspecialchars($contacto['mensaje'] ?? '', ENT_QUOTES, 'UTF-8');
            $contacto['estado'] = htmlspecialchars($contacto['estado'] ?? 'pendiente', ENT_QUOTES, 'UTF-8');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $contactos
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al obtener contactos: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error general: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
}

function obtenerEstadisticasContactos($pdo) {
    try {
        // Total de contactos
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM contactos");
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Contactos pendientes
        $stmt = $pdo->prepare("SELECT COUNT(*) as pendientes FROM contactos WHERE estado = 'pendiente'");
        $stmt->execute();
        $pendientes = $stmt->fetch(PDO::FETCH_ASSOC)['pendientes'];
        
        // Contactos atendidos
        $stmt = $pdo->prepare("SELECT COUNT(*) as atendidos FROM contactos WHERE estado = 'atendido'");
        $stmt->execute();
        $atendidos = $stmt->fetch(PDO::FETCH_ASSOC)['atendidos'];
        
        // Contactos de este mes
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as este_mes 
            FROM contactos 
            WHERE MONTH(fecha_creacion) = MONTH(CURRENT_DATE()) 
            AND YEAR(fecha_creacion) = YEAR(CURRENT_DATE())
        ");
        $stmt->execute();
        $esteMes = $stmt->fetch(PDO::FETCH_ASSOC)['este_mes'];
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total' => (int)$total,
                'pendientes' => (int)$pendientes,
                'atendidos' => (int)$atendidos,
                'este_mes' => (int)$esteMes
            ]
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al obtener estadísticas: ' . $e->getMessage()]);
    }
}

function actualizarEstadoContacto($pdo, $id) {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $nuevoEstado = $input['estado'] ?? 'atendido';
        
        $stmt = $pdo->prepare("UPDATE contactos SET estado = ? WHERE id = ?");
        $stmt->execute([$nuevoEstado, $id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Contacto no encontrado']);
        }
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar estado: ' . $e->getMessage()]);
    }
}

function eliminarContacto($pdo, $id, $action) {
    try {
        $id = (int)$id;
        
        if ($id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de contacto no válido'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if ($action === 'ocultar') {
            // Eliminación lógica - agregar campo 'activo' si no existe
            $stmt = $pdo->prepare("UPDATE contactos SET activo = 0 WHERE id = ?");
            $resultado = $stmt->execute([$id]);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Contacto ocultado exitosamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al ocultar el contacto'
                ], JSON_UNESCAPED_UNICODE);
            }
        } elseif ($action === 'eliminar') {
            // Eliminación permanente
            $stmt = $pdo->prepare("DELETE FROM contactos WHERE id = ?");
            $resultado = $stmt->execute([$id]);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Contacto eliminado permanentemente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al eliminar el contacto'
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

function restaurarContacto($pdo, $id) {
    try {
        $id = (int)$id;
        
        if ($id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de contacto no válido'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $stmt = $pdo->prepare("UPDATE contactos SET activo = 1 WHERE id = ?");
        $resultado = $stmt->execute([$id]);
        
        if ($resultado && $stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Contacto restaurado exitosamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo restaurar el contacto o ya estaba activo'
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error del servidor: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

function revertirEstadoContacto($pdo, $id) {
    try {
        $id = (int)$id;
        
        if ($id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de contacto no válido'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $stmt = $pdo->prepare("UPDATE contactos SET estado = 'pendiente' WHERE id = ?");
        $resultado = $stmt->execute([$id]);
        
        if ($resultado && $stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Estado revertido a pendiente exitosamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo revertir el estado del contacto'
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error del servidor: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

function obtenerContactoPorId($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT id, nombre, dni, email, telefono, mensaje, estado, fecha_creacion, activo FROM contactos WHERE id = ?");
        $stmt->execute([$id]);
        $contacto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($contacto) {
            // Limpiar datos
            $contacto['nombre'] = htmlspecialchars($contacto['nombre'], ENT_QUOTES, 'UTF-8');
            $contacto['dni'] = htmlspecialchars($contacto['dni'], ENT_QUOTES, 'UTF-8');
            $contacto['email'] = htmlspecialchars($contacto['email'], ENT_QUOTES, 'UTF-8');
            $contacto['telefono'] = htmlspecialchars($contacto['telefono'], ENT_QUOTES, 'UTF-8');
            $contacto['mensaje'] = htmlspecialchars($contacto['mensaje'], ENT_QUOTES, 'UTF-8');
            
            // Formatear fecha
            $fecha = new DateTime($contacto['fecha_creacion']);
            $contacto['fecha_formateada'] = $fecha->format('d/m/Y H:i');
            
            echo json_encode([
                'success' => true,
                'contacto' => $contacto
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Contacto no encontrado'
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error al obtener contacto: ' . $e->getMessage()
        ]);
    }
}
?>
