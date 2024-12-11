
var modal = document.getElementById("myModal");
var btn = document.getElementById("enviar");
var span = document.getElementsByClassName("close")[0];
btn.onclick = function () {
    modal.style.display = "block";
}
span.onclick = function () {
    modal.style.display = "none";
}
window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

$(document).ready(function() {
    $('#salir').click(function() {
        location.href = "../database/logout.php";
    });
});

$(document).ready(function() {
    $('#atras').click(function() {
    window.location.href = 'index.php'; // Cambia la URL al home
});
});
//funcion para seleccionar cantidad de pasos
$(document).ready(function () {
    $('#modalForm').submit(function (event) {
        event.preventDefault(); // Evita el comportamiento por defecto del formulario

        var selectedSteps = $('#steps').val(); // Obtiene el valor de los pasos seleccionados

        $.ajax({
            url: '../../database/getthatstep.php', // Ruta al archivo PHP
            type: 'GET',
            data: { steps: selectedSteps },
            dataType: 'json',
            success: function (data) {
                console.log(data); // Log para depuración
                $("#contenedor").empty();  // Vacía el contenido del contenedor
                $("#contenedor").hide();   // Oculta el contenedor

                if (data && data.length > 0) {
                    $('.nav-list').empty(); // Limpia la lista de navegación

                    data.forEach(function (step) {
                        // Verifica que el paso tenga nombre e ID
                        if (step.nombre_paso && step.id_pasos) { 
                            var stepItem = $('<li></li>')
                                .append('<button class="step-btn" data-step="' + step.id_pasos + '">' + step.nombre_paso + '</button>');

                            $('.nav-list').append(stepItem); // Agrega el elemento a la lista
                        }
                    });
                } else {
                    console.log('No se encontraron pasos');
                }
                $('#myModal').hide(); // Oculta el modal
            },
            error: function (xhr, status, error) {
                console.log('Error al obtener los pasos:', error);
            }
        });
    });
});



//funcion para mostrar productos
$(document).on('click', '.step-btn', function () {
    var pasoId = $(this).data('step');  // Obtiene el 'data-step' del botón
    //alert("el paso es"+pasoId);

    $.ajax({
        url: '../../database/getproduct.php',
        method: 'GET',
        data: { paso_id: pasoId },
        success: function (response) {
            var productos = JSON.parse(response);
            $('#productos').empty();


            productos.forEach(function (producto) {


                var productoDiv = document.createElement("div");
                productoDiv.id = producto.id_producto;

                productoDiv.classList.add("producto-item");
                productoDiv.setAttribute("data-idprod", producto.id_producto);
                productoDiv.setAttribute("data-idpaso", producto.id_pasos);
                productoDiv.setAttribute("data-nombre-paso", producto.nombre_paso);

                var imagenDiv = document.createElement("div");
                imagenDiv.classList.add("producto-imagen")
                var imagen = document.createElement("img");

                if (producto.foto) {
                    imagen.src = producto.foto; 
                    imagen.alt = producto.nombre_producto || "Producto sin nombre";
                }
                else if (producto.imagen_base64) {
                    imagen.src = producto.imagen_base64;

                }
                else {
                    imagen.src = "minimalistic-science-banner-with-sample.jpg"; // URL de la imagen por defecto
                }

                imagenDiv.appendChild(imagen); // Agrega la imagen al div

                var nombreDiv = document.createElement("div");
                nombreDiv.classList.add("producto-nombre");
                nombreDiv.setAttribute("nombreproducto", producto.nombre_producto);
                nombreDiv.textContent = producto.nombre_producto;


                var descripcionDiv = document.createElement("div");
                descripcionDiv.classList.add("producto-descripcion");
                descripcionDiv.textContent = producto.descripcion;


                productoDiv.appendChild(imagenDiv);
                productoDiv.appendChild(nombreDiv);
                productoDiv.appendChild(descripcionDiv);

                $('#productos').append(productoDiv);
            });
        },
        error: function () {
            alert('Hubo un error al cargar los productos');
        }
    });
});

var arrayprod = [];
//funcion para crear rutina y llenar array con productos
$(document).on('click', '.producto-item', function () {
    var idProducto = $(this).data('idprod'); 
    var paso = $(this).data('nombre-paso');
    var idpaso = $(this).data('idpaso');    
    var nombreProducto = $(this).find('.producto-nombre').text(); 
    $('.fixed-routine').show();

    var productoExistente = arrayprod.find(function (producto) {
        return producto.idProducto === idProducto; 
    });

    if (productoExistente) {
        alert("Producto ya agregado");
        return;
    } else {
        let contador = 0;
        arrayprod.forEach(function(esteprod) {
            if (esteprod.idpaso == idpaso) {
                contador++;
            }
        });
       // alert("Contador para el paso " + idpaso + ": " + contador);

        if (contador >= 2) {
            alert("Se recomienda usar solo 2 productos por paso.");
            return; // Salimos de la función
        } else {
            arrayprod.push({
                nombre: nombreProducto,
                idProducto: idProducto,
                idpaso: idpaso
            });
            actualizarContadorProductos();

        }
    }
    console.log(arrayprod);


    // Crear un contenedor horizontal para la fila del producto + paso asociado
    var productoRow = document.createElement("div");
    productoRow.classList.add("producto-row");
    productoRow.setAttribute("productorutina", idProducto);
    productoRow.setAttribute("idpasorutina", idpaso);

    // Crear el div solamente visual para el nombre del producto
    var productoNombreDiv = document.createElement("div");
    productoNombreDiv.classList.add('producto-nombre-cell')
    productoNombreDiv.textContent = nombreProducto;

    var pasoDiv = document.createElement("div");
    pasoDiv.classList.add("producto-paso-cell");
    pasoDiv.textContent = paso;

    var botoneliminar = document.createElement("button");
    botoneliminar.classList.add('eliminar');
    botoneliminar.textContent = "Eliminar";
    botoneliminar.onclick = function() {
        // Eliminar del array
        arrayprod = arrayprod.filter(function(producto) {
            return producto.idProducto !== idProducto || producto.idpaso !== idpaso;
        });

        // Actualizar el contador
        let contador = 0;
        arrayprod.forEach(function(esteprod) {
            if (esteprod.idpaso == idpaso) {
                contador++;
            }
        });
        console.log("Contador actualizado para el paso " + idpaso + ": " + contador);
        console.log(arrayprod);

        productoRow.remove();
        actualizarContadorProductos();

    };


    productoRow.append(productoNombreDiv)
    productoRow.append(pasoDiv);
    productoRow.append(botoneliminar);

    // Agregar la fila al contenedor de la rutina
    $('#routine-list').append(productoRow);
});

// Función para actualizar el contador de productos
function actualizarContadorProductos() {
    var contadorProductos = arrayprod.length;
    $('#contador-productos').text(contadorProductos); // Actualiza el número en el HTML
}

$(document).on('click', '#confirmar-btn', function () {
    $('#nombreRutinaModal').show();
});
$(document).on('click', '.close', function () {
    $('#nombreRutinaModal').hide();
});
$(document).on('click', '#guardarRutinaBtn', function () {
crearrutina();
});

$(document).on('click', '#traerrutina', function () {
    traerrutina();
    });
    
    
    
    
    // traer rutinas del usuario
// Función para traer las rutinas del usuario
function traerrutina() {
    const usuarioId = usuario_id; // Asegúrate de tener el ID del usuario disponible aquí
    
    fetch('../../database/getrutine.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ usuario_id: usuarioId }) // Pasar el ID del usuario en el cuerpo de la solicitud
    })
    .then(response => response.json())
    .then(data => {
        console.log("Rutinas obtenidas:", data);
        
        if (data.error) {
            alert(data.error);  // Si hay un error, mostrarlo
        } else if (data.message) {
            alert(data.message);  // Si no hay rutinas, mostrar mensaje
        } else {
            // Si se obtuvieron rutinas, mostrarlas
            mostrarRutinas(data);
        }
    })
    .catch(error => {
        console.error("Error al obtener rutinas:", error);
        alert("Hubo un problema al obtener las rutinas.");
    });
}


// Función para mostrar las rutinas en la interfaz
function mostrarRutinas(rutinas) {
    const contenedorRutinas = document.getElementById('productos');  // Un contenedor en tu HTML donde mostrarás las rutinas
    contenedorRutinas.innerHTML = '';  // Limpiar el contenido anterior
    $("#contenedor").empty();  // Vacía el contenido del contenedor
    $("#contenedor").hide();   // Oculta el contenedor


    // Iterar sobre las rutinas y crear elementos HTML para cada una
    for (let idRutina in rutinas) {
        const rutina = rutinas[idRutina];
        
        const rutinaDiv = document.createElement('div');
        rutinaDiv.classList.add('rutina');
        
        const nombreRutina = document.createElement('h4');
        nombreRutina.textContent = rutina.nombre_rutina;
        rutinaDiv.appendChild(nombreRutina);
        
        const productosList = document.createElement('ul');
        rutina.productos.forEach(producto => {
            const productoItem = document.createElement('li');
            productoItem.textContent = producto;
            productosList.appendChild(productoItem);
        });
        rutinaDiv.appendChild(productosList);
        
        const pasosList = document.createElement('ul');
        rutina.pasos.forEach(paso => {
            const pasoItem = document.createElement('li');
            pasoItem.textContent = paso;
            pasosList.appendChild(pasoItem);
        });
        rutinaDiv.appendChild(pasosList);
        
        contenedorRutinas.appendChild(rutinaDiv);  // Añadir la rutina al contenedor
    }
}



//mandar rutina al php
function crearrutina() {

    if (arrayprod.length === 0) {
        alert("No hay productos en la rutina para enviar.");
        return;
    }

    const nombreRutina = $('#nombre-rutina').val().trim();

    if (!nombreRutina) {
        alert("Por favor, ingresa un nombre para la rutina.");
        return;
    }

    const data = {
        rutinaNombre:nombreRutina,  
        usuario_id: usuario_id,  
        productos: arrayprod
    };

    console.log("datos enviados:", JSON.stringify(data));  // Verifica que arrayprod contenga los productos

    fetch('../../database/crearrutina.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',  
        },
        body: JSON.stringify(data)  
    })    
    .then(response => response.json())  // Convierte la respuesta a JSON
    .then(result => {
        console.log('Respuesta del servidor:', result);  // Ver qué está devolviendo el servidor

        if (result.success) {
            $('.fixed-routine').hide(); 
            $('.fixed-routine').empty();
            location.reload();  // Recarga la página actual

            arrayprod = [];  
            alert("Rutina creada exitosamente.");
        } else {
            alert('Hubo un error al crear la rutina.');
        }
    })
    .catch(error => {
        console.log('Error:', error);
        alert('Hubo un problema con la solicitud.');
    });
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

// estilo para q se vean los numeros en la barra
function updateStepsValue(value) {
    document.getElementById("stepValueDisplay").innerText = value;
}


// //borrar para abajo
// document.getElementById('uploadForm').addEventListener('submit', function(event) {
//     event.preventDefault(); // Prevenir el comportamiento por defecto del formulario

//     var formData = new FormData(); // Crear un objeto FormData para manejar el archivo
//     var productoId = document.getElementById('producto_id').value; // Obtener el ID del producto
//     var fileInput = document.getElementById('imagen');
//     var file = fileInput.files[0]; // Obtener el archivo seleccionado

//     if (file && productoId) {
//         formData.append('producto_id', productoId); // Agregar el ID del producto al FormData
//         formData.append('imagen', file); // Agregar el archivo al FormData

//         // Enviar la imagen al servidor con fetch
//         fetch("../../database/subirimagen.php", {
//             method: 'POST',
//             body: formData,
//         })
//         .then(response => response.json()) // Suponemos que el servidor responderá con JSON
//         .then(data => {
//             if (data.success) {
//                 document.getElementById('resultado').innerHTML = "¡Imagen subida con éxito!";
//             } else {
//                 document.getElementById('resultado').innerHTML = "Error al subir la imagen.";
//             }
//         })
//         .catch(error => {
//             console.error('Error al subir la imagen:', error);
//             document.getElementById('resultado').innerHTML = "Hubo un error al procesar la imagen.";
//         });
//     } else {
//         document.getElementById('resultado').innerHTML = "Por favor, selecciona una imagen y un ID de producto.";
//     }
// });