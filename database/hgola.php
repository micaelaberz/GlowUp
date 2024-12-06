<?php
// URL de la página a scrapear
$url = 'https://www.farmaplus.com.ar/cerave?_q=cerave&map=ft';

// Usar cURL para obtener el contenido HTML de la página
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
curl_close($ch);

// Usar DOMDocument para parsear el HTML
$dom = new DOMDocument();
libxml_use_internal_errors(true);  // Evitar errores de parsing
$dom->loadHTML($html);
libxml_clear_errors();

// Usar DOMXPath para buscar las imágenes
$xpath = new DOMXPath($dom);
$images = $xpath->query('//img');

// Crear una carpeta para guardar las imágenes
if (!file_exists('imagenes')) {
    mkdir('imagenes', 0777, true);
}

// Descargar las imágenes
foreach ($images as $img) {
    // Obtener el atributo 'src' de cada imagen
    $imgSrc = $img->getAttribute('src');

    // Asegurarse de que el atributo 'src' esté definido
    if (empty($imgSrc)) {
        continue;  // Saltar esta imagen si el atributo 'src' no está definido
    }

    // Comprobar si la URL de la imagen es absoluta o relativa
    if (strpos($imgSrc, 'http') === false) {
        $imgSrc = 'https://www.farmaplus.com.ar' . $imgSrc;  // Convertir a URL absoluta
    }

    // Obtener el nombre del archivo (solo el nombre del archivo sin el path completo)
    $imgName = basename($imgSrc);

    // Descargar la imagen y guardarla en la carpeta 'imagenes'
    $imgData = file_get_contents($imgSrc);
    if ($imgData) {
        file_put_contents('imagenes/' . $imgName, $imgData);
        echo 'Imagen descargada: ' . $imgName . "\n";
    } else {
        echo 'Error al descargar la imagen: ' . $imgSrc . "\n";
    }
}
?>
