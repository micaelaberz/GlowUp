<?php
require "database.php";
header('Content-Type: application/json');  // Asegúrate de que la respuesta sea JSON

 $input = file_get_contents("php://input");
$data = json_decode($input, true); // Decodificar el JSON recibido en un array
error_log("Datos recibidos: " . print_r($data, true));  // Esto mostrará el contenido de $data en el log de errores

// Verificar si los datos necesarios están presentes
if (isset($data['productos']) && is_array($data['productos']) && isset($data['rutinaNombre']) && isset($data['usuario_id'])) {
    $productos = $data['productos']; // Obtener el array de productos
    $rutinaNombre = $data['rutinaNombre'];
    $usuario_id = $data['usuario_id'];

    // Verificar que los valores necesarios no estén vacíos
    if (empty($rutinaNombre) || empty($usuario_id)) {
        echo json_encode(['success' => false, 'message' => "Faltan datos necesarios: rutinaNombre o usuario_id."]);
        exit;
    }

    try {
        // Iniciar la transacción
        $pdo->beginTransaction();

        // Insertar la rutina en la tabla "rutina"
        $sqlRutina = "INSERT INTO rutina (nombreRutina) VALUES (:rutinaNombre)";
        $stmt = $pdo->prepare($sqlRutina);
        $stmt->bindParam(':rutinaNombre', $rutinaNombre);
        $stmt->execute();

        // Obtener el ID de la rutina recién creada
        $rutina_id = $pdo->lastInsertId(); 
        if (!$rutina_id) {
            throw new Exception("No se pudo obtener el ID de la rutina.");
        }

        // 2. Asociar la rutina con el usuario en la tabla "usuario_rutina"
        $sqlUsuarioRutina = "INSERT INTO usuario_rutina (id_usuario, id_rutina) VALUES (:usuario_id, :rutina_id)";
        $stmt = $pdo->prepare($sqlUsuarioRutina);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':rutina_id', $rutina_id);
        $stmt->execute();

        // 3. Insertar los productos relacionados con la rutina en "rutina_producto"
        foreach ($productos as $producto) {
            if (!isset($producto['idProducto'])) {
                throw new Exception("Producto sin ID.");
            }

            $producto_id = $producto['idProducto'];
            $sqlProducto = "INSERT INTO rutina_producto (id_rutina, id_producto) VALUES (:rutina_id, :producto_id)";
            $stmt = $pdo->prepare($sqlProducto);
            $stmt->bindParam(':rutina_id', $rutina_id);
            $stmt->bindParam(':producto_id', $producto_id);
            $stmt->execute();
        }

        // Confirmar la transacción
        $pdo->commit();
        
        echo json_encode(['success' => true, 'rutina_id' => $rutina_id]);
    } catch (Exception $e) {
        // Si ocurre un error, se revierte la transacción
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => "Error al procesar la solicitud: " . $e->getMessage()]);
    }
} else {
    // Verificar si los parámetros básicos no están presentes
    echo json_encode(['success' => false, 'message' => "Faltan datos requeridos: productos, rutinaNombre o usuario_id."]);
}

// Cerrar la conexión PDO
$pdo = null;
?>
