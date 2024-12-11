<?php
require "database.php";  // Conexión a la base de datos

// Activar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Verificar si se recibe el parámetro 'steps' desde el frontend
    $num_steps = isset($_GET['steps']) && is_numeric($_GET['steps']) ? (int)$_GET['steps'] : 0;

    // Construir la consulta según el número de pasos
    if ($num_steps > 0) {
        // Caso especial si el número de pasos es 3
        if ($num_steps == 3) {
            $sql = "SELECT id_pasos, nombre_paso FROM pasos WHERE id_pasos IN (1, 6, 7)";
            $stmt = $pdo->prepare($sql);
        } else {
            $sql = "SELECT id_pasos, nombre_paso FROM pasos LIMIT :num_steps";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':num_steps', $num_steps, PDO::PARAM_INT);
        }
    } else {
        // Si no se recibe 'steps', cargar todos los pasos
        $sql = "SELECT id_pasos, nombre_paso FROM pasos";
        $stmt = $pdo->query($sql);  // Usar query directamente si no hay límite
    }

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados
    $steps = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si se encontraron pasos
    if ($steps) {
        // Devolver los datos en formato JSON
        echo json_encode($steps);
    } else {
        // Enviar un mensaje de error si no hay pasos
        echo json_encode(["error" => "No se encontraron pasos."]);
    }

} catch (PDOException $e) {
    // Manejo de errores en formato JSON
    echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
    exit();
}
?>
