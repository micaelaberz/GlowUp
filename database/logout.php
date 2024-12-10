<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../app/login.html");
    exit();
}
session_unset(); // Eliminar todas las variables de sesión
session_destroy(); // Destruir la sesión
header("Location: ../app/login.html");
exit();
?>
