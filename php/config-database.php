<?php
/**
 * Configuración de Base de Datos para Sipán Inmobiliaria
 * 
 * IMPORTANTE: Configura estos valores según tu servidor MySQL
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'sipan_inmobiliaria');
define('DB_USER', 'root'); // Cambia por tu usuario de MySQL
define('DB_PASS', ''); // Cambia por tu contraseña de MySQL
define('DB_CHARSET', 'utf8mb4');

// Función para conectar a la base de datos
function conectarDB() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Error de conexión a la base de datos: " . $e->getMessage());
        return false;
    }
}

// Función para verificar si la base de datos está disponible
function verificarDB() {
    $pdo = conectarDB();
    if (!$pdo) {
        return [
            'success' => false,
            'message' => 'No se pudo conectar a la base de datos'
        ];
    }
    
    try {
        // Verificar si las tablas existen
        $stmt = $pdo->query("SHOW TABLES LIKE 'calificaciones'");
        if ($stmt->rowCount() == 0) {
            return [
                'success' => false,
                'message' => 'La tabla de calificaciones no existe. Ejecuta el archivo database.sql'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Base de datos conectada correctamente'
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Error al verificar la base de datos: ' . $e->getMessage()
        ];
    }
}

// Función para obtener estadísticas de calificaciones
function obtenerEstadisticas() {
    $pdo = conectarDB();
    if (!$pdo) return false;
    
    try {
        // Calcular estadísticas en tiempo real desde la tabla calificaciones
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total_calificaciones,
                AVG(calificacion) as promedio_calificacion,
                (COUNT(CASE WHEN calificacion >= 4 THEN 1 END) * 100.0 / COUNT(*)) as porcentaje_satisfechos
            FROM calificaciones 
            WHERE estado = 'activo' AND activo = 1
        ");
        
        $resultado = $stmt->fetch();
        
        // Si no hay datos, devolver valores por defecto
        if (!$resultado || $resultado['total_calificaciones'] == 0) {
            return [
                'total_calificaciones' => 0,
                'promedio_calificacion' => 0.0,
                'porcentaje_satisfechos' => 0.0
            ];
        }
        
        return $resultado;
        
    } catch (PDOException $e) {
        error_log("Error al obtener estadísticas: " . $e->getMessage());
        return false;
    }
}

// Función para obtener comentarios recientes
function obtenerComentariosRecientes($limite = 10) {
    $pdo = conectarDB();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->prepare("
            SELECT nombre, email, calificacion, comentario, fecha_creacion, ip_address
            FROM calificaciones 
            WHERE estado = 'activo' AND activo = 1
            ORDER BY fecha_creacion DESC 
            LIMIT ?
        ");
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error al obtener comentarios: " . $e->getMessage());
        return [];
    }
}

// Función para agregar una nueva calificación
function agregarCalificacion($nombre, $email, $calificacion, $comentario) {
    $pdo = conectarDB();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO calificaciones (nombre, email, calificacion, comentario, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $resultado = $stmt->execute([
            $nombre,
            $email,
            $calificacion,
            $comentario,
            $ip,
            $userAgent
        ]);
        
        return $resultado;
    } catch (PDOException $e) {
        error_log("Error al agregar calificación: " . $e->getMessage());
        return false;
    }
}

// Función para validar datos de entrada
function validarCalificacion($nombre, $email, $calificacion, $comentario) {
    $errores = [];
    
    if (empty($nombre) || strlen($nombre) < 2) {
        $errores[] = 'El nombre debe tener al menos 2 caracteres';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El email no es válido';
    }
    
    if (!is_numeric($calificacion) || $calificacion < 1 || $calificacion > 5) {
        $errores[] = 'La calificación debe ser entre 1 y 5';
    }
    
    if (empty($comentario) || strlen($comentario) < 10) {
        $errores[] = 'El comentario debe tener al menos 10 caracteres';
    }
    
    return $errores;
}
?> 