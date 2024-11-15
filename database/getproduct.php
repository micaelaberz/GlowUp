<?php
require 'database.php'; // Incluye la conexión

if (isset($_GET['paso_id'])) {
    $pasoId = $_GET['paso_id'];

    // Corrección en la consulta SQL (falta una coma entre los campos)
    $query = "SELECT p.nombre_producto, p.descripcion, p.foto FROM productos p
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
        // Convertir la foto (que es binaria) a Base64 para cada producto
        foreach ($productos as &$producto) {
            if ($producto['foto']) {
                // Convertir la imagen binaria a Base64
                $producto['foto'] = base64_encode($producto['foto']);
                // Asegurarse de que la imagen esté en formato adecuado para un <img src="data:image/png;base64,...">
                $producto['foto'] = 'data:image/jpeg;base64,' . $producto['foto']; // Cambiar el formato a JPEG si es necesario
            }
        }

        // Devuelve los productos en formato JSON
        echo json_encode($productos);
    } else {
        echo json_encode(['mensaje' => 'No se encontraron productos para este paso.']);
    }
}
?>
