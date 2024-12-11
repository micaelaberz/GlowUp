<?php
require 'database.php'; // Conexión a la base de datos
ini_set('display_errors', 1);
error_reporting(E_ALL);


$response = ['success' => false];

if (isset($_POST['producto_id'], $_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $producto_id = $_POST['producto_id']; // Obtener el ID del producto
    $imagen = file_get_contents($_FILES['imagen']['tmp_name']); // Obtener la imagen


    // Consulta SQL para actualizar la imagen en el producto correspondiente
    $query = "UPDATE productos SET foto = :foto WHERE id_producto = :producto_id";

    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':foto', $imagen, PDO::PARAM_LOB);
    $stmt->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Error al actualizar la imagen.';
    }
} else {
    $response['message'] = 'Faltan parámetros o la imagen no se cargó correctamente.';
}

// Esto es para verificar si se está retornando un JSON válido
header('Content-Type: application/json');
echo json_encode($response);
?>