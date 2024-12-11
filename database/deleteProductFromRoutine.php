<?php
require "database.php";  

$data = json_decode(file_get_contents("php://input"), true);

$id_rutina = $data['id_rutina'] ?? null;
$id_producto = $data['id_producto'] ?? null;

if (!$id_rutina || !$id_producto) {
    echo json_encode(["error" => "Datos incompletos."]);
    exit();
}

try {
    // Consulta para eliminar el producto de la rutina
    $sql = "DELETE FROM rutina_producto WHERE id_rutina = :id_rutina AND id_producto = :id_producto";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_rutina', $id_rutina, PDO::PARAM_INT);
    $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "No se encontró el producto en la rutina."]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
}
?>
