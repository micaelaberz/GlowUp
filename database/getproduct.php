<?php
require 'database.php'; // Incluye la conexión

if (isset($_GET['paso_id'])) {
    $pasoId = $_GET['paso_id'];

    $query = "SELECT p.nombre_producto FROM productos p
              JOIN producto_paso pp ON p.id_producto = pp.id_producto
              JOIN pasos ps ON pp.id_paso = ps.id_pasos
              WHERE ps.id_pasos = :pasoId";

    // Usar PDO para ejecutar la consulta
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':pasoId', $pasoId, PDO::PARAM_INT);
    $stmt->execute();

    // Recuperar los resultados
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($productos) {
        echo json_encode($productos); // Devuelve en formato JSON
    } else {
        echo json_encode(['mensaje' => 'No se encontraron productos para este paso.']);
    }
}
?>
