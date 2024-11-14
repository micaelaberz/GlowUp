$(document).ready(function() {
    $('.step-btn').click(function() {
        var pasoId = $(this).data('step');  // Obtiene el 'data-step' del botón

        $.ajax({
            url: '../database/getproduct.php', 
            method: 'GET',
            data: { paso_id: pasoId },  // Pasa el id del paso correctamente
            success: function(response) {
                $('#contenedor').html(response);  
            },
            error: function() {
                alert('Hubo un error al cargar los productos');
            }
        });
    });
});




