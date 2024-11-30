<?php

require "database.php"; // Asegúrate de que la configuración de la conexión esté bien


$sql = "SELECT nombre_paso FROM pasos";
$stmt = $pdo->query($sql);  // Ejecutar la consulta

if ($stmt) {
    $steps = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los resultados
    echo json_encode($steps); // Devolver los pasos en formato JSON
} else {
    echo json_encode(['error' => 'No se encontraron pasos en la base de datos']);
}
