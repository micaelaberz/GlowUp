<?php
require ("database.php");

// Obtener todos los productos de la base de datos
$stmt = $pdo->prepare("SELECT id_producto, nombre_producto, descripcion FROM productos");
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$palabras_clave = [
    1 => ["gel de limpieza", "espuma", "limpieza", "jabón"], // limpieza
    2 => ["exfoliante", "scrub", "peeling"], // exfoliación
    3 => ["tonico", "equilibrante", "refrescante","tónica","tonica","tónico"], // tonificación
    4 => ["serum", "concentrado", "ampolla"], // serums
    5 => ["contorno de ojos", "ojeras", "antiojeras"], // contorno de ojos
    6 => ["hidratante", "crema", "loción", "emulsión", "sérum hidratante"], // hidratante
    7 => ["protector solar", "bloqueador", "SPF", "protección UV"] // protección solar
];

// Iniciar la transacción
try {
    $pdo->beginTransaction();

    // Iterar a través de los productos y asignarles un paso según las palabras clave
    foreach ($productos as $producto) {
        // Verificar si el ID del producto es válido
        if (empty($producto['id_producto'])) {
            echo "El producto con nombre " . $producto['nombre_producto'] . " no tiene ID válido. Saltando...<br>";
            continue;
        }

        // Obtener el nombre y la descripción del producto
        $nombre_producto = $producto['nombre_producto'];
        $descripcion_producto = $producto['descripcion'];

        // Determinar a qué paso pertenece el producto
        $id_paso = null;
        foreach ($palabras_clave as $paso => $palabras) {
            foreach ($palabras as $palabra) {
                if (stripos($nombre_producto, $palabra) !== false || stripos($descripcion_producto, $palabra) !== false) {
                    $id_paso = $paso;
                    break 2; // Si encontramos una palabra clave, no seguimos buscando
                }
            }
        }

        // Si se encontró el paso, insertar la relación en la tabla `producto_paso`
        if ($id_paso !== null) {
            // Verificar si la relación ya existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM producto_paso WHERE id_producto = ? AND id_paso = ?");
            $stmt->bindParam(1, $producto['id_producto']);
            $stmt->bindParam(2, $id_paso);
            $stmt->execute();
            $existe = $stmt->fetchColumn();

            if ($existe == 0) {
                // Insertar la relación si no existe
                $stmt = $pdo->prepare("INSERT INTO producto_paso (id_producto, id_paso) VALUES (?, ?)");
                $stmt->bindParam(1, $producto['id_producto']);
                $stmt->bindParam(2, $id_paso);
                $stmt->execute();
                echo "Producto " . $producto['nombre_producto'] . " asignado a paso " . $id_paso . "<br>";
            } else {
                echo "El producto " . $producto['nombre_producto'] . " ya está asignado al paso " . $id_paso . "<br>";
            }
        } else {
            echo "No se pudo asignar un paso al producto " . $producto['nombre_producto'] . "<br>";
        }
    }

    // Confirmar la transacción si todo ha ido bien
    $pdo->commit();

} catch (Exception $e) {
    // Si algo falla, deshacer la transacción
    $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "<br>";
}
?>
