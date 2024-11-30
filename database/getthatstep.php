<?php
require "database.php";  // Incluye la conexión a la base de datos

// Activar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Verificar si se recibe el parámetro 'steps' desde el frontend
    if (isset($_GET['steps']) && is_numeric($_GET['steps'])) {
        $num_steps = (int)$_GET['steps'];  // Convertir el valor a entero
    } else {
        $num_steps = 0;  // Si no se pasa el parámetro, cargamos todos los pasos
    }

    // Si 'steps' es recibido, limitar los pasos a ese número
    if ($num_steps > 0) {
        // Verificar si el número de pasos es igual a 3
        if ($num_steps == 3) {
            // Consulta para traer los pasos con los IDs 1, 6 y 7
            $sql = "SELECT nombre_paso FROM pasos WHERE id_pasos IN (1, 6, 7)";
            $stmt = $pdo->prepare($sql);
        } else {
            // Consulta para traer los primeros 'n' pasos según el número recibido
            $sql = "SELECT nombre_paso FROM pasos LIMIT :num_steps";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':num_steps', $num_steps, PDO::PARAM_INT);
        }
    } else {
        // Si no se recibe 'steps', cargar todos los pasos
        $sql = "SELECT nombre_paso FROM pasos"; 
        $stmt = $pdo->query($sql);  // Usar query directamente si no hay límite
    }

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados
    $steps = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Añadir contador de pasos en cada paso
    $steps_with_counter = array_map(function($step, $index) {
        return [
            'contador' => $index + 1,  // Contador basado en el índice del array
            'nombre_paso' => $step['nombre_paso']
        ];
    }, $steps, array_keys($steps));  // Usamos array_map para agregar el contador

    // Devolver los datos en formato JSON
    echo json_encode($steps_with_counter);

} catch (PDOException $e) {
    // Devolver el error en formato JSON para manejo adecuado en frontend
    echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
    exit();
}
?>
