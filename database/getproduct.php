<?php
require 'database.php'; 

if (isset($_GET['paso_id'])) {
    $pasoId = $_GET['paso_id'];

    $query = "SELECT p.nombre_producto, p.descripcion, p.id_producto, p.foto, ps.id_pasos, ps.nombre_paso 
              FROM productos p
              JOIN producto_paso pp ON p.id_producto = pp.id_producto
              JOIN pasos ps ON pp.id_paso = ps.id_pasos
              WHERE ps.id_pasos = :pasoId"; 

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':pasoId', $pasoId, PDO::PARAM_INT);

    $stmt->execute();

    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($productos) {
        foreach ($productos as &$producto) {
            // Procesar la imagen BLOB (si existe)
            if ($producto['foto']) {
                // Convertir la imagen BLOB a Base64
                $producto['foto'] = base64_encode($producto['foto']);
                $producto['foto'] = 'data:image/jpeg;base64,' . $producto['foto']; // Cambiar el formato a JPEG si es necesario
            }
        }

        // Devuelve los productos en formato JSON
        echo json_encode($productos);
    } else {
        // Devuelve un mensaje en formato JSON si no se encuentran productos
        echo json_encode(['mensaje' => "No se encontraron productos para este paso."]);
    }
}
?>
