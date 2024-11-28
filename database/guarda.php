<?php
// Conexión a la base de datos
require 'database.php';  // Asegúrate de que este archivo esté en el mismo directorio

// Paso 1: Realizar la solicitud a la API de Mercado Libre
$endpoint = "https://api.mercadolibre.com/sites/MLA/search?q=serum";
$response = file_get_contents($endpoint);  // Hacer la solicitud a la API

// Verificar si la respuesta fue exitosa
if ($response === false) {
    die('Error en la solicitud a la API de Mercado Libre.');
}

// Decodificar el JSON de la respuesta
$data = json_decode($response, true);

// Verificar si los datos fueron decodificados correctamente
if ($data === null) {
    die('Error al decodificar la respuesta JSON.');
}

// Paso 2: Buscar el primer producto con 'variations_data'
$found = false;

foreach ($data['results'] as $product) {
    // Verificar si el producto tiene la clave 'variations_data'
    if (isset($product['variations_data']) && count($product['variations_data']) > 0) {
        // Obtener las variaciones de este producto
        $variationsData = $product['variations_data'];
        
        // Mostrar las variaciones de este producto
        echo "Variations Data del primer producto con variaciones: <br>";
        echo "<pre>";
        print_r($variationsData);  // Esto te ayudará a inspeccionar las variaciones de ese producto
        echo "</pre>";

        // Verificar imágenes en variaciones
        echo "Imágenes en las variaciones: <br>";
        foreach ($variationsData as $variation) {
            // Mostrar la imagen de la variación si existe
            if (isset($variation['thumbnail'])) {
                echo "Imagen de variación: <img src='" . $variation['thumbnail'] . "' alt='Imagen de variación' style='max-width: 200px;'><br>";

                // Obtener la imagen y convertirla a base64
                $imageUrl = $variation['thumbnail'];  // URL de la imagen
                $imageData = file_get_contents($imageUrl);  // Obtener los datos de la imagen
                $imageBase64 = base64_encode($imageData);  // Convertir a base64

                // Aquí puedes guardar la imagen base64 en la base de datos
                $stmt = $pdo->prepare("INSERT INTO productos (nombre_producto, imagen_base64) VALUES (?, ?)");
                $stmt->bindValue(1, $product['nombre_producto'], PDO::PARAM_STR);  // Nombre del producto
                $stmt->bindValue(2, $imageBase64, PDO::PARAM_STR);       // Imagen en base64
                $stmt->execute();

                echo "Producto guardado con la imagen en base64.<br>";
            }
        }

        // Establecer que ya encontramos el producto con variaciones
        $found = true;
        break;  // Salir del bucle una vez que encontramos el primer producto con variaciones
    }
}

// Si no se encontró ningún producto con variaciones
if (!$found) {
    echo "No se encontró ningún producto con variaciones.";
}
?>
