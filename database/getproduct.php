<?php
require 'database.php'; 

if (isset($_GET['paso_id'])) {
    $pasoId = $_GET['paso_id'];

    $query = "SELECT p.nombre_producto, p.descripcion, p.id_producto, p.foto, p.imagen_base64, ps.id_pasos, ps.nombre_paso 
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
            
            // Procesar la imagen Base64 (si existe)
            if ($producto['imagen_base64']) {
                $producto['imagen_base64'] = 'data:image/jpeg;base64,' . $producto['imagen_base64'];
            }

            // Si no hay foto en BLOB, usar la de Base64, o viceversa
            if (!$producto['foto'] && $producto['imagen_base64']) {
                $producto['foto'] = $producto['imagen_base64'];
            } elseif (!$producto['imagen_base64'] && $producto['foto']) {
                $producto['imagen_base64'] = $producto['foto'];
            }
        }

        // Devuelve los productos en formato JSON
        echo json_encode($productos);
    } else {
        echo json_encode(['mensaje' => "No se encontraron productos para este paso."]);
    }
}