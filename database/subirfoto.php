<?php

require 'database.php';  // Incluye tu archivo de conexión a la base de datos

// Ruta del archivo de imagen en el servidor
$imagenRuta = 'C:\Users\Micaela\Downloads\246391-600-auto.jpg';

// Verificar si el archivo existe
if (!file_exists($imagenRuta)) {
    die("La imagen no se encuentra en la ruta especificada.");
}

// Leer la imagen y convertirla a binario
$imagenBinaria = file_get_contents($imagenRuta);

// Preparar la consulta SQL para actualizar la imagen en el producto con ID 3
$sql = "UPDATE productos SET foto = :foto WHERE id_producto = :id";

// Usar sentencia preparada con PDO para evitar SQL Injection
$stmt = $pdo->prepare($sql);

// Verificar que la preparación fue exitosa
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $pdo->errorInfo());
}

// Enlazar los parámetros con el valor correspondiente
$stmt->bindParam(':foto', $imagenBinaria, PDO::PARAM_LOB);  // PDO::PARAM_LOB es para datos binarios
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

// Establecer el ID del producto
$id = 3;  // ID del producto a actualizar

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "Imagen actualizada correctamente para el producto con ID 3.";
} else {
    echo "Error: " . implode(" ", $stmt->errorInfo());
}

?>
