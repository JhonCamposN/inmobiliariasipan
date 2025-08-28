<?php
header('Content-Type: application/json; charset=UTF-8');

require_once 'config-database.php';

try {
    // Probar conexión
    $pdo = conectarDB();
    
    if (!$pdo) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: No se pudo conectar a la base de datos',
            'details' => 'La función conectarDB() retornó false'
        ]);
        exit;
    }
    
    // Probar consulta simple
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM contactos");
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'message' => 'Conexión exitosa a la base de datos',
        'contactos_count' => $result['total'],
        'database' => DB_NAME,
        'host' => DB_HOST
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error PDO: ' . $e->getMessage(),
        'code' => $e->getCode()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error general: ' . $e->getMessage()
    ]);
}
?>
