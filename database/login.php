<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: index.php"); //si ya hay sesion se va 
    exit();
}

require "database.php"; 


// Verifica si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verifica si es un intento de inicio de sesión
    if (isset($_POST['username'], $_POST['password'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        try {
            // Busca al usuario en la base de datos usando PDO
            $sql = "SELECT id_usuario, contrasena FROM usuarios WHERE nombre_usuario = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['contrasena'])) {
                // Credenciales válidas, inicia sesión
                $_SESSION['user_id'] = $user['id_usuario'];
                $_SESSION['username'] = $username;
                session_start(); 

                // Redirige al usuario a la página principal
                header("Location: ../app/index.php");
                exit;
            }
            else{
                header("Location: ../app/login.html");
            }
        } catch (PDOException $e) {
            echo "Error al iniciar sesión: " . $e->getMessage();
        }

    // Verifica si es un intento de registro
    } elseif (isset($_POST['fullName'], $_POST['email'], $_POST['password'], $_POST['confirmPassword'])) {
        $fullName = trim($_POST['fullName']);
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];



        // Hashea la contraseña
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        try {
            // Verifica si el nombre de usuario o el correo ya están registrados
            $sql = "SELECT id_usuario FROM usuarios WHERE nombre_usuario = :nombre_usuario OR email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_usuario', $fullName);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'El usuario ya esta en uso']);
            } else {
                // Inserta el usuario en la base de datos
                $sql = "INSERT INTO usuarios (nombre_usuario, email, contrasena) VALUES (:nombre_usuario, :email, :contrasena)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nombre_usuario', $fullName);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':contrasena', $hashedPassword);
                $stmt->execute();
                header("Location: ../app/login.html");
            }

        } catch (PDOException $e) {
            echo "Error al registrar el usuario: " . $e->getMessage();
        }
    } else {
        echo "Solicitud no válida.";
    }

} else {
    echo "Método de solicitud no permitido.";
}
?>

    
    
    
    
    
