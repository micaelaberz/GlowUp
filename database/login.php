<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Redirigir si ya hay una sesión activa
if (isset($_SESSION['usuario'])) {
    header("Location: ../../index.php"); // Si ya hay sesión activa, redirige a la página principal
    exit();
}

require 'database.php'; // Asegúrate de que la conexión a la base de datos esté configurada correctamente

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Proceso de login
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Verificar que el usuario exista en la base de datos
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE nombre_usuario = :username');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['contrasena'])) { // Verificar la contraseña encriptada
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['username'] = $username;
            echo json_encode(['success' => true, 'message' => 'Login exitoso']);
        } else {
            // Si las credenciales son incorrectas
            echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
        }
    }

    // Proceso de registro
    elseif (isset($_POST['fullName']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirmPassword'])) {
        $username = $_POST['fullName'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];

        // Validación de contraseñas coincidentes
        if ($password !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
            exit;
        }

        // Verificar si el correo ya está registrado
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            echo json_encode(['success' => false, 'message' => 'El correo ya está registrado']);
            exit;
        }

        // Encriptar la contraseña antes de almacenarla
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insertar usuario en la base de datos
        $stmt = $pdo->prepare('INSERT INTO usuarios (nombre_usuario, email, contrasena) VALUES (:username, :email, :password)');
        $success = $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashedPassword]);

        if ($success) {
            $userId = $pdo->lastInsertId(); 

            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            echo json_encode(['success' => true, 'message' => 'Registro exitoso']);
        } 
    }
}
?>
