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
                    console.log(producto.foto);  // Verifica la URL/Base64 de la imagen

                    var productoDiv = $('<div></div>').addClass('producto-item');
                    
                    // Contenedor de imagen
                    var imagenDiv = $('<div></div>').addClass('producto-imagen');
                    
                    // Verifica si la imagen está disponible
                    if (producto.foto) {
                        // Si la foto es Base64 (asegúrate que el backend la envíe correctamente)
                        if (producto.foto.startsWith("data:image")) {
                            imagenDiv.append('<img src="' + producto.foto + '" alt="' + producto.nombre_producto + '">');
                        } else {
                            // Si es una URL normal, la usa como fuente de la imagen
                            imagenDiv.append('<img src="' + producto.foto + '" alt="' + producto.nombre_producto + '">');
                        }
                    } else {
                        // Si no hay imagen, puedes poner una imagen por defecto
                        imagenDiv.append('<img src="https://via.placeholder.com/150" alt="Imagen no disponible">');
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





var modal = document.getElementById("myModal");
var btn = document.getElementById("enviar");
var span = document.getElementsByClassName("close")[0];
btn.onclick = function() {
  modal.style.display = "block";
}
span.onclick = function() {
  modal.style.display = "none";
}
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
// $(document).ready(function() {
//     // Cuando se hace clic en el botón
//     $('#actualizarImagenBtn').click(function() {
//         // Enviar la solicitud AJAX al archivo PHP
//         $.ajax({
//             url: 'actualizar_imagen.php',  // El archivo PHP que maneja la actualización
//             type: 'POST',  // Método de la solicitud
//             data: { 
//                 producto_id: 3  // El ID del producto que quieres actualizar (en este caso, ID 3)
//             },
//             success: function(response) {
//                 // Mostrar el mensaje de éxito o error
//                 $('#mensaje').html(response);
//             },
//             error: function() {
//                 $('#mensaje').html('Hubo un error al actualizar la imagen.');
//             }
//         });
//     });
// });
// $(document).ready(function() {
//     $('#actualizarImagenBtn').click(function() {
//         // Realizar la solicitud fetch a 'verimagen.php'
//         fetch('../database/verimagen.php?id=1') // Cambia la ruta si es necesario
//         .then(response => {
//             if (!response.ok) {
//                 throw new Error('Error en la solicitud: ' + response.statusText);
//             }
//             return response.blob();  // Esperamos un Blob (un tipo de objeto binario)
//         })
//         .then(blob => {
//             // Crear un objeto URL para la imagen y mostrarla
//             const img = document.createElement('img');
//             img.src = URL.createObjectURL(blob);  // Crea una URL para el Blob de la imagen
//             img.alt = 'Imagen producto';
//             img.style.width = '100px';  // Ajusta el tamaño si es necesario
//             img.style.margin = '10px';
            
//             // Agregar la imagen al contenedor
//             const contenedor = document.getElementById('contenedor');
//             contenedor.innerHTML = '';  // Limpiar el contenedor
//             contenedor.appendChild(img);
//         })
//         .catch(error => {
//             console.error('Error al obtener la imagen:', error);
//         });
//     });
// });

function updateStepsValue(value) {
    document.getElementById("stepValueDisplay").innerText = value;
  }