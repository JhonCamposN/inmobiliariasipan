<?php
/**
 * Archivo de diagnóstico para probar la conexión a la base de datos
 * Accede a: http://localhost/inmobiliaria_sipan/php/test-database.php
 */

// Mostrar errores para diagnóstico
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Diagnóstico de Base de Datos - Sipán Inmobiliaria</h2>";
echo "<hr>";

// Incluir configuración
require_once 'config-database.php';

echo "<h3>1. Verificando configuración de base de datos:</h3>";
echo "Host: " . DB_HOST . "<br>";
echo "Base de datos: " . DB_NAME . "<br>";
echo "Usuario: " . DB_USER . "<br>";
echo "Charset: " . DB_CHARSET . "<br><br>";

echo "<h3>2. Probando conexión PDO:</h3>";
$pdo = conectarDB();
if ($pdo) {
    echo "✅ <strong>Conexión exitosa a la base de datos</strong><br><br>";
    
    echo "<h3>3. Verificando tablas existentes:</h3>";
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            echo "❌ <strong>No se encontraron tablas. Necesitas importar el archivo sipan_inmobiliaria.sql</strong><br>";
        } else {
            echo "✅ Tablas encontradas:<br>";
            foreach ($tables as $table) {
                echo "- " . $table . "<br>";
            }
        }
        echo "<br>";
        
        // Verificar tabla calificaciones específicamente
        if (in_array('calificaciones', $tables)) {
            echo "<h3>4. Verificando estructura de tabla 'calificaciones':</h3>";
            $stmt = $pdo->query("DESCRIBE calificaciones");
            $columns = $stmt->fetchAll();
            
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th></tr>";
            foreach ($columns as $column) {
                echo "<tr>";
                echo "<td>" . $column['Field'] . "</td>";
                echo "<td>" . $column['Type'] . "</td>";
                echo "<td>" . $column['Null'] . "</td>";
                echo "<td>" . $column['Key'] . "</td>";
                echo "<td>" . $column['Default'] . "</td>";
                echo "</tr>";
            }
            echo "</table><br>";
            
            // Contar registros existentes
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM calificaciones");
            $count = $stmt->fetch();
            echo "📊 Registros existentes en calificaciones: <strong>" . $count['total'] . "</strong><br><br>";
            
            // Mostrar últimos 3 registros
            if ($count['total'] > 0) {
                echo "<h3>5. Últimos registros en calificaciones:</h3>";
                $stmt = $pdo->query("SELECT * FROM calificaciones ORDER BY fecha_creacion DESC LIMIT 3");
                $registros = $stmt->fetchAll();
                
                echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
                echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Calificación</th><th>Comentario</th><th>Fecha</th></tr>";
                foreach ($registros as $registro) {
                    echo "<tr>";
                    echo "<td>" . $registro['id'] . "</td>";
                    echo "<td>" . $registro['nombre'] . "</td>";
                    echo "<td>" . $registro['email'] . "</td>";
                    echo "<td>" . $registro['calificacion'] . "</td>";
                    echo "<td>" . substr($registro['comentario'], 0, 50) . "...</td>";
                    echo "<td>" . $registro['fecha_creacion'] . "</td>";
                    echo "</tr>";
                }
                echo "</table><br>";
            }
        } else {
            echo "❌ <strong>Tabla 'calificaciones' no encontrada</strong><br><br>";
        }
        
    } catch (PDOException $e) {
        echo "❌ <strong>Error al verificar tablas: " . $e->getMessage() . "</strong><br><br>";
    }
    
} else {
    echo "❌ <strong>Error de conexión a la base de datos</strong><br>";
    echo "Verifica que:<br>";
    echo "- XAMPP esté ejecutándose<br>";
    echo "- MySQL esté activo<br>";
    echo "- La base de datos 'sipan_inmobiliaria' exista<br>";
    echo "- Las credenciales en config-database.php sean correctas<br><br>";
}

echo "<h3>6. Probando funciones de la API:</h3>";

// Probar función de estadísticas
echo "<strong>Probando obtenerEstadisticas():</strong><br>";
$estadisticas = obtenerEstadisticas();
if ($estadisticas) {
    echo "✅ Estadísticas obtenidas correctamente:<br>";
    echo "- Total calificaciones: " . $estadisticas['total_calificaciones'] . "<br>";
    echo "- Promedio: " . $estadisticas['promedio_calificacion'] . "<br>";
    echo "- % Satisfechos: " . $estadisticas['porcentaje_satisfechos'] . "<br>";
} else {
    echo "❌ Error al obtener estadísticas<br>";
}
echo "<br>";

// Probar función de comentarios
echo "<strong>Probando obtenerComentariosRecientes():</strong><br>";
$comentarios = obtenerComentariosRecientes(3);
if (!empty($comentarios)) {
    echo "✅ Comentarios obtenidos correctamente (" . count($comentarios) . " registros)<br>";
    foreach ($comentarios as $comentario) {
        echo "- " . $comentario['nombre'] . " (" . $comentario['calificacion'] . " estrellas)<br>";
    }
} else {
    echo "❌ No se pudieron obtener comentarios o no hay registros<br>";
}
echo "<br>";

echo "<h3>7. Probando inserción de datos:</h3>";
echo "<strong>Intentando agregar calificación de prueba:</strong><br>";

$resultado = agregarCalificacion(
    "Usuario Prueba", 
    "prueba@test.com", 
    5, 
    "Esta es una calificación de prueba para verificar el funcionamiento del sistema."
);

if ($resultado) {
    echo "✅ <strong>Calificación de prueba agregada exitosamente</strong><br>";
    
    // Verificar que se agregó
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM calificaciones WHERE email = 'prueba@test.com'");
    $count = $stmt->fetch();
    echo "📊 Registros de prueba encontrados: " . $count['total'] . "<br>";
    
    // Limpiar registro de prueba
    $pdo->query("DELETE FROM calificaciones WHERE email = 'prueba@test.com'");
    echo "🧹 Registro de prueba eliminado<br>";
} else {
    echo "❌ <strong>Error al agregar calificación de prueba</strong><br>";
}

echo "<br><hr>";
echo "<h3>📋 Resumen del Diagnóstico:</h3>";

if ($pdo && in_array('calificaciones', $tables ?? []) && $estadisticas && !empty($comentarios)) {
    echo "✅ <strong style='color: green;'>SISTEMA FUNCIONANDO CORRECTAMENTE</strong><br>";
    echo "La base de datos está configurada y las funciones trabajan bien.<br>";
    echo "Si el formulario no funciona, el problema está en el frontend (JavaScript) o en la comunicación AJAX.<br>";
} else {
    echo "❌ <strong style='color: red;'>PROBLEMAS DETECTADOS</strong><br>";
    echo "Revisa los errores mostrados arriba y corrige la configuración de la base de datos.<br>";
}

echo "<br><strong>Siguiente paso:</strong> ";
echo "<a href='calificaciones-api.php' target='_blank'>Probar API directamente</a>";
?>
