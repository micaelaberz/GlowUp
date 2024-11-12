<?php


$host = "srv1660.hstgr.io";
$dbname = "u208245273_glowup";
$username = "u208245273_micaelaa";
$password = "j2?W7d7Z";
$respuesta_estado = "";

// Conexión usando PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    echo($respuesta_estado);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
