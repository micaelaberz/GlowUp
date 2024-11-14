$(document).ready(function() {
    $('.step-btn').click(function() {
        var pasoId = $(this).data('step');  // Obtiene el 'data-step' del botón

        $.ajax({
            url: '../database/getproduct.php', 
            method: 'GET',
            data: { paso_id: pasoId },  // Pasa el id del paso correctamente
            success: function(response) {
                var productos = JSON.parse(response); // Parseamos la respuesta JSON
                $('#contenedor').empty(); // Limpiamos el contenedor antes de agregar los nuevos productos
            
                // Itera sobre cada producto y crea un div para cada uno
                productos.forEach(function(producto) {
                    var productoDiv = $('<div></div>').addClass('producto-item');
                    
                    // Contenedor de imagen
                    var imagenDiv = $('<div></div>').addClass('producto-imagen');
                    
                    // Verifica si la imagen está disponible
                    if (producto.foto) {
                        imagenDiv.append('<img src="' + producto.foto + '" alt="' + producto.nombre_producto + '">');
                    } else {
                        // Si no hay imagen, puedes poner una imagen por defecto
                        imagenDiv.append('<img src="ruta/a/imagen/por/defecto.jpg" alt="Imagen no disponible">');
                    }
            
                    // Contenedor para el nombre con fondo transparente
                    var nombreDiv = $('<div></div>').addClass('producto-nombre');
                    nombreDiv.text(producto.nombre_producto);
                    
                    // Contenedor para la descripción (puede mostrarse en hover o expandirse)
                    var descripcionDiv = $('<div></div>').addClass('producto-descripcion');
                    descripcionDiv.text(producto.descripcion);
                    
                    // Agregar las partes al div principal
                    productoDiv.append(imagenDiv);
                    productoDiv.append(nombreDiv);
                    productoDiv.append(descripcionDiv);
                    
                    // Agregar el div del producto al contenedor principal
                    $('#contenedor').append(productoDiv);
                });
            },
            error: function() {
                alert('Hubo un error al cargar los productos');
            }
        });
    });
});

$(document).ready(function() {
    // Cuando se hace clic en el botón
    $('#actualizarImagenBtn').click(function() {
        // Enviar la solicitud AJAX al archivo PHP
        $.ajax({
            url: 'actualizar_imagen.php',  // El archivo PHP que maneja la actualización
            type: 'POST',  // Método de la solicitud
            data: { 
                producto_id: 3  // El ID del producto que quieres actualizar (en este caso, ID 3)
            },
            success: function(response) {
                // Mostrar el mensaje de éxito o error
                $('#mensaje').html(response);
            },
            error: function() {
                $('#mensaje').html('Hubo un error al actualizar la imagen.');
            }
        });
    });
});


