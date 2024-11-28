<?php

var_dump(value: $_GET); // Esto imprimirá todo el contenido de $_GET


if (!empty($_GET['id_producto']) && is_numeric($_GET['id_producto'])) {
    $id_producto = intval($_GET['id_producto']); // Asegúrate de convertirlo a un número

    // Conexión a la base de datos
    require 'database.php';

    // Consulta la imagen desde la base de datos
    $stmt = $pdo->prepare("SELECT imagen_base64 FROM productos WHERE id_producto = ?");
    $stmt->execute([$id_producto]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Mostrar la imagen
        header("Content-Type: image/jpeg");
        echo base64_decode($row['imagen_base64']);
    } else {
        header("Content-Type: text/plain");
        echo "Producto no encontrado.";
    }
} else {
    header("Content-Type: text/plain");
    echo "ID de producto no especificado o inválido.";
}
