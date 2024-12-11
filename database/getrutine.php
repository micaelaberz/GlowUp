<?php
session_start();
require "database.php";  // Incluye la conexión a la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

$usuario_id = $_SESSION['user_id'];  // Obtener el ID del usuario desde la sesión

try {
    // Consulta para obtener las rutinas del usuario
    $sql = "
        SELECT rp.id_rutina, r.nombreRutina, rp.id_producto, p.nombre_producto, s.nombre_paso
        FROM usuario_rutina ur
        LEFT JOIN rutina_producto rp ON ur.id_rutina = rp.id_rutina
        LEFT JOIN rutina r ON r.id_rutina = ur.id_rutina
        LEFT JOIN productos p ON rp.id_producto = p.id_producto
        LEFT JOIN producto_paso ps ON p.id_producto = ps.id_producto
        LEFT JOIN pasos s ON ps.id_paso = s.id_pasos
        WHERE ur.id_usuario = :usuario_id
    ";

    // Preparar la consulta
    $stmt = $pdo->prepare($sql);
    
    // Vincular el parámetro correctamente
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);  // Vincular el parámetro :usuario_id con el valor de $usuario_id
    
    // Ejecutar la consulta
    $stmt->execute();
    
    // Obtener todos los resultados
    $rutinas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rutinas) > 0) {
        // Agrupar los datos de productos y pasos por rutina
        $resultado = [];
        foreach ($rutinas as $rutina) {
            $id_rutina = $rutina['id_rutina'];

            // Agrupar los datos de la rutina
            if (!isset($resultado[$id_rutina])) {
                $resultado[$id_rutina] = [
                    'id_rutina' => $id_rutina, // Agregar id_rutina
                    'nombreRutina' => $rutina['nombreRutina'],
                    'productos' => [],
                    'pasos' => []
                ];
            }

        
            // Añadir producto y paso a la rutina correspondiente
            $resultado[$id_rutina]['productos'][] = ['id_producto' => $rutina['id_producto'],'nombre_producto' => $rutina['nombre_producto']];
                        $resultado[$id_rutina]['pasos'][] = $rutina['nombre_paso'];
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
