$(document).ready(function() {
    $('.step-btn').click(function() {
        var pasoId = $(this).data('step');  // Obtiene el 'data-step' del botón

        $.ajax({
            url: '../database/getproduct.php', 
            method: 'GET',
            data: { paso_id: pasoId },  // Pasa el id del paso correctamente
            success: function(response) {
                var productos = JSON.parse(response);
                $('#contenedor').empty();

                // Itera sobre cada producto y crea un div para cada uno
                productos.forEach(function(producto) {
                    var productoDiv = $('<div></div>').addClass('producto-item');
                    
                    // Añadir detalles del producto al div
                    productoDiv.append('<h3>' + producto.nombre_producto + '</h3>');
                    productoDiv.append('<p>Descripción: ' + producto.descripcion + '</p>');
                    
                    // Añadir el div al contenedor principal
                    $('#contenedor').append(productoDiv);
                });
            },
            error: function() {
                alert('Hubo un error al cargar los productos');
            }
        });
    });
});

function generarcont(){

}



