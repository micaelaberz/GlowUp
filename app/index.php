<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    header(header: "Location: login.html");
    exit();  
}
?>


<!DOCTYPE html>
<html lang="esp">

<head>
  <meta http-equiv="Content-Type" charset="UTF-8" />
  <meta name="addsearch-category" content="skinroutine" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Parkinsans&display=swap" rel="stylesheet">


  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <link rel="stylesheet" type="text/css" href="style/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


</head>

<body>
  <header>
    <h1 id="titulo">Glow up</h1>
    <div id="botones">
      <button id="salir">Salir</button>
      <button id="atras">Atrás</button>
    </div>
  </header>


  <nav class="navbar">
    <ul class="nav-list" id="stepList">
      <li><button>Limpieza</button></li>
      <li><button>Exfoliación</button></li>
      <li><button>Tonificación</button></li>
      <li><button>Serums</button></li>
      <li><button>Contorno de ojos</button></li>
      <li><button>Hidratación</button></li>
      <li><button>Protección solar</button></li>
    </ul>
  </nav>




  <div id="fondo">
    <div id="productos"></div>
    <div id="contenedor">
      <h2 id="bien">¿Todo listo para empezar?</h2>
      <p class="texto">Bienvenido a Glow Up! Sumérgete en el mundo de rutinas para el cuidado de la piel.</p>
      <p class="texto">Explore los mejores productos de limpieza, hidratación y sueros, adaptados a las necesidades únicas de su piel.</p>
      <button class="enviar" id="enviar">Empezar</button>
      <button class="enviar" id="traerrutina">Ver mis rutinas</button>

      <div id="myModal" class="modal">
        <div class="modal-content">
          <span class="close">&times;</span>
          <h2 id="tituloform">Empecemos a armar tu rutina</h2>

          <form id="modalForm">

            <label for="steps">¿Cuántos pasos querés que tenga?</label>
            <div class="slidecontainer">
              <input type="range" required="required" title="Seleccionar cantidad pasos" class="slider" id="steps"
                name="steps" min="3" max="7" value="3" oninput="updateStepsValue(this.value)">
              <span id="stepValueDisplay">3</span> pasos <!-- Mostrará el número dinámicamente -->
            </div>


            <input type="submit" value="Aceptar" id="enviarform">

          </form>
        </div>
      </div>
    </div>
  </div>


  <div id="nombreRutinaModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h3>Nombre de la Rutina</h3>
      <input type="text" id="nombre-rutina" placeholder="Escribe el nombre de la rutina">
      <button id="guardarRutinaBtn" >Guardar Rutina</button>
    </div>
  </div>

  <div id="rutinaModal" class="modal scrolrut">
  <div class="modalrut">
  <div class="modal-header">
  <span class="close-btn" onclick="closeModal()">×</span>
  <h3 id="text">Mis rutinas</h3>
</div>
    <div>
      <ul id="listadorutinas">

      </ul> 
    </div>
  </div>
</div>

  <div class="fixed-routine" style="display: none;">
    <div id="panel">
      <h3 class="textorutina">Mi Rutina</h3>
      <span class="textorutina">Productos seleccionados: <span id="contador-productos">0</span></span>
      <button class="confirmar" id="confirmar-btn">Confirmar Rutina</button>
    </div>


    <div id="listado">
      <ul id="routine-list">
      </ul>
    </div>


  </div>



  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    var usuario_id = <?php echo $_SESSION['user_id']; ?>;
  </script>
  <script src="js/func.js"></script>



  <!-- <button id="actualizarImagenBtn">Actualizar Imagen</button> -->

  <!-- <img src="http://http2.mlstatic.com/D_893534-MLA79027733297_092024-O.jpg" alt="Thumbnail del Producto"> -->

  <!-- <img src="../database/verimagen.php?id_producto=14" alt="Producto" /> -->
</body>

</html>



