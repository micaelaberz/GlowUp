<?php
session_start();
require "database.php";  // Incluye la conexión a la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["error" => "Usuario no autenticado"]);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];  // Obtener el ID del usuario desde la sesión

try {
    // Consulta para obtener las rutinas del usuario
    $sql = "
        SELECT r.id_rutina, r.nombre_rutina, p.id_producto, p.nombre_producto, s.nombre_paso
        FROM rutina r
        LEFT JOIN rutina_productos rp ON r.id_rutina = rp.id_rutina
        LEFT JOIN productos p ON rp.id_producto = p.id_producto
        LEFT JOIN rutina_pasos rp2 ON r.id_rutina = rp2.id_rutina
        LEFT JOIN pasos s ON rp2.id_paso = s.id_paso
        WHERE r.usuario_id = :usuario_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Obtener todos los resultados
    $rutinas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rutinas) > 0) {
        // Agrupar los datos de productos y pasos por rutina
        $resultado = [];
        foreach ($rutinas as $rutina) {
            $resultado[$rutina['id_rutina']]['nombre_rutina'] = $rutina['nombre_rutina'];
            $resultado[$rutina['id_rutina']]['productos'][] = $rutina['nombre_producto'];
            $resultado[$rutina['id_rutina']]['pasos'][] = $rutina['nombre_paso'];
        }

        // Enviar la respuesta en formato JSON
        echo json_encode($resultado);
    } else {
        echo json_encode(["message" => "No se encontraron rutinas para este usuario"]);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
    exit();
}
?>
