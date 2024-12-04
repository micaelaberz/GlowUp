<?php
require "database.php";



// Verificar si el producto ya está en la rutina
$query = "SELECT * FROM rutina_usuario WHERE id_usuario = :usuarioId AND id_producto = :idProducto";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':usuarioId', $usuarioId);
$stmt->bindParam(':idProducto', $idProducto);
$stmt->execute();

// Si el producto no está en la rutina, lo agregamos
if ($stmt->rowCount() == 0) {
    $query = "INSERT INTO rutina_usuario (id_usuario, id_producto, fecha_agregado) 
              VALUES (:usuarioId, :idProducto, NOW())";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':usuarioId', $usuarioId);
    $stmt->bindParam(':idProducto', $idProducto);
    $stmt->execute();
    
    echo json_encode(['mensaje' => 'Producto agregado a la rutina']);
} else {
    echo json_encode(['mensaje' => 'Este producto ya está en tu rutina']);
}
