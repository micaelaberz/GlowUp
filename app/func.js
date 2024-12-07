
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


//funcion para seleccionar cantidad de pasos
$(document).ready(function () {
    $('#modalForm').submit(function (event) {
        event.preventDefault();

        var selectedSteps = $('#steps').val();

        // console.log("Pasos seleccionados: ", selectedSteps); 

        $.ajax({
            url: '../database/getthatstep.php',
            type: 'GET',
            data: { steps: selectedSteps },
            dataType: 'json',
            success: function (data) {
                console.log(data);

                if (data && data.length > 0) {
                    $('.nav-list').empty();
                    var paso = 0;


                    data.forEach(function (step) {
                        if (step.nombre_paso) {
                            paso++; // Incrementa el paso

                            var stepItem = $('<li></li>')
                                .append('<button class="step-btn" data-step="' + paso + '">' + step.nombre_paso + '</button>');

                            $('.nav-list').append(stepItem);
                        }
                    });
                } else {
                    console.log('No se encontraron pasos');
                }
                $('#myModal').hide();
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
        url: '../database/getproduct.php',
        method: 'GET',
        data: { paso_id: pasoId },
        success: function (response) {
            var productos = JSON.parse(response);
            $('#contenedor').empty();
            $('#contenedor').addClass('contpro');


            productos.forEach(function (producto) {


                var productoDiv = document.createElement("div");
                productoDiv.id = producto.id_producto;

                productoDiv.classList.add("producto-item");
                productoDiv.setAttribute("data-idprod", producto.id_producto);
                productoDiv.setAttribute("data-idpaso", producto.id_pasos);
                productoDiv.setAttribute("data-nombre-paso", producto.nombre_paso);

                var imagenDiv = document.createElement("div");
                imagenDiv.classList.add("producto-imagen")

                if (producto.foto) {
                    var imagen = document.createElement("img");
                    imagen.src = producto.foto; // Usa la URL o Base64 como fuente
                    imagen.alt = producto.nombre_producto || "Producto sin nombre";
                    imagenDiv.appendChild(imagen); // Agrega la imagen al div
                }
                else if (producto.imagen_base64) {
                    var imagen = document.createElement("img");
                    imagen.src = producto.imagen_base64;
                    imagenDiv.appendChild(imagen); // Agrega la imagen al div

                }
                else {
                    var imagenPorDefecto = document.createElement("img");
                    imagenPorDefecto.src = "minimalistic-science-banner-with-sample.jpg"; // URL de la imagen por defecto
                    imagenPorDefecto.alt = "Imagen no disponible";
                    imagenDiv.appendChild(imagenPorDefecto); // Agrega la imagen por defecto al div
                }


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

                $('#contenedor').append(productoDiv);
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
        alert("Contador para el paso " + idpaso + ": " + contador);

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
$(document).on('click', '.confirmar-btn', function () {
    crearrutina();  
});
//mandar rutina al php
function crearrutina() {
    if (arrayprod.length === 0) {
        alert("No hay productos en la rutina para enviar.");
        return;
    }

    const data = {
        rutinaNombre: "prueba1",  
        usuario_id: 1,           
        productos: arrayprod
    };

    console.log("datos enviados:", JSON.stringify(data));  // Verifica que arrayprod contenga los productos

    fetch('../database/crearrutina.php', {
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

$(document).ready(function(){
    
    $(".contenedor-formularios").find("input, textarea").on("keyup blur focus", function (e) {

        var $this = $(this),
          label = $this.prev("label");

        if (e.type === "keyup") {
            if ($this.val() === "") {
                label.removeClass("active highlight");
            } else {
                label.addClass("active highlight");
            }
        } else if (e.type === "blur") {
            if($this.val() === "") {
                label.removeClass("active highlight"); 
                } else {
                label.removeClass("highlight");   
                }   
        } else if (e.type === "focus") {
            if($this.val() === "") {
                label.removeClass("highlight"); 
            } 
            else if($this.val() !== "") {
                label.addClass("highlight");
            }
        }

    });

    $(".tab a").on("click", function (e) {

        e.preventDefault();

        $(this).parent().addClass("active");
        $(this).parent().siblings().removeClass("active");

        target = $(this).attr("href");

        $(".contenido-tab > div").not(target).hide();

        $(target).fadeIn(600);

    });
    
});







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

