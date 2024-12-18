<?php

require("database.php"); // Archivo con la conexión a la base de datos

// Endpoint base de la API de Mercado Libre
$endpoint = "https://api.mercadolibre.com/sites/MLA/search?q=aceite+protector+solar";

// Marcas aceptadas
$marcas = [
    1 => "Garnier",
    2 => "Neutrogena",
    3 => "Lidherma",
    4 => "Eucerin",
    5 => "Bagóvit",
    6 => "Dermaglós",
    7 => "ACF",
    8 => "Dadatina",
    9 => "Idraet",
    10 => "Vichy",
    11 => "La Roche-Posay",
    12 => "Cetaphil",
    13 => "Loreal Paris",
    14 => "CeraVe"
];

$texturas = [
    "Crema" => 1,
    "Gel" => 4,
    "Serum" => 2,
    "Bruma" => 3,
    "Espuma" => 3,
    "Aceite" => 5,
    "Tónico" => 6,
    "Agua Micelar" => 7,
    "Leche" => 1 // Tratar "leche" como "crema"
];

$excluir = ["Kit", "Ducha", "Baño", "Cuerpo", "Manos", "Corporal", "Spray", "Combo", "Emulsión"];

// Hacer la solicitud a la API
$response = file_get_contents($endpoint);
$data = json_decode($response, true);

// Verificar si hay resultados
if (isset($data['results']) && is_array($data['results'])) {
    $productosCargados = 0; // Contador de productos procesados
    foreach ($data['results'] as $item) {
        // Extraer datos del producto
        $nombre_producto = $item['title'];
        $descripcion = $item['title']; // Usamos el título como descripción por defecto
        $brand = $item['attributes'][0]['value_name'] ?? ''; // Marca si está disponible
        $volumen = null;
        $unidad = null;

        // Lógica para seleccionar una mejor imagen
        $foto_url = $item['thumbnail']; // Miniatura como predeterminada
        if (isset($item['pictures']) && is_array($item['pictures']) && count($item['pictures']) > 0) {
            $imagen_mas_grande = null;
            $mayor_tamano = 0;

            // Iterar sobre las imágenes para encontrar la más grande
            foreach ($item['pictures'] as $imagen) {
                // Verificar si hay información sobre el tamaño
                if (!empty($imagen['url']) && isset($imagen['ratio']) && $imagen['ratio'] > $mayor_tamano) {
                    $mayor_tamano = $imagen['ratio'];
                    $imagen_mas_grande = $imagen['url'];
                }
            }

            // Usar la imagen más grande encontrada
            if ($imagen_mas_grande) {
                $foto_url = $imagen_mas_grande;
            }
        }
        // Continuar con el procesamiento del producto
        $nombre_producto = preg_replace('/\bx(\d+)\s*(ml|gr)\b/i', '$1 $2', $nombre_producto);

        // Verificar si la marca está en nuestra lista
        $id_marca = array_search($brand, $marcas);
        if ($id_marca !== false && !empty($foto_url)) {
            // Identificar la textura y ajustar el nombre
            $id_textura = null;
            foreach ($texturas as $clave => $id) {
                if (stripos($nombre_producto, $clave) !== false) {
                    $id_textura = $id;
                    $nombre_producto = str_ireplace($clave, "", $nombre_producto); // Eliminar la textura del nombre
                    break;
                }
            }

            // Extraer el volumen y la unidad de medida (si están en el nombre)
            if (preg_match('/(\d+)\s*(ml|gr)/i', $nombre_producto, $matches)) {
                $volumen = (int)$matches[1];
                $unidad = strtolower($matches[2]);
                $nombre_producto = str_replace($matches[0], "", $nombre_producto); // Eliminar la cantidad del nombre
                $descripcion = str_replace($matches[0], "", $descripcion); // Eliminar la cantidad de la descripción
            } else {
                // Si no se encuentra un volumen, saltamos este producto
                continue;
            }

            // Eliminar palabras excluidas de nombre y descripción
            foreach ($excluir as $palabra) {
                if (stripos($nombre_producto, $palabra) !== false || stripos($descripcion, $palabra) !== false) {
                    continue 2; // Saltar este producto
                }
            }

            // Verificar si el producto ya existe en la base de datos
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE nombre_producto = ? AND id_marca = ?");
            $stmt->bindValue(1, $nombre_producto, PDO::PARAM_STR);
            $stmt->bindValue(2, $id_marca, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count == 0) { // Si no existe, insertamos el producto
                // Descargar la imagen desde la URL y convertirla a base64
                $foto_contenido = file_get_contents($foto_url);
                $foto_base64 = base64_encode($foto_contenido);

                // Insertar en la base de datos
                $stmt = $pdo->prepare("INSERT INTO productos (nombre_producto, id_marca, id_textura, volumen, descripcion, unidad_medida, imagen_base64) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bindValue(1, $nombre_producto, PDO::PARAM_STR);
                $stmt->bindValue(2, $id_marca, PDO::PARAM_INT);
                $stmt->bindValue(3, $id_textura, PDO::PARAM_INT);
                $stmt->bindValue(4, $volumen, PDO::PARAM_INT);
                $stmt->bindValue(5, $descripcion, PDO::PARAM_STR);
                $stmt->bindValue(6, $unidad, PDO::PARAM_STR);
                $stmt->bindValue(7, $foto_base64, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    echo "Producto insertado: $nombre_producto<br>";
                } else {
                    echo "Error al insertar el producto: " . $stmt->errorInfo()[2] . "<br>";
                }

                // Incrementar el contador
                $productosCargados++;
                if ($productosCargados = 1) {
                    break; // Limitar a solo el primer producto
                }
            } else {
                echo "Producto ya existe en la base de datos: $nombre_producto<br>";
            }
        }
    }
}
