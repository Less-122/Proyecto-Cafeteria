<?php

session_start();

require_once '../config/conexion.php'; 

$operacion = $_POST['operacion'] ?? '';

switch ($operacion) {
    
    case 'registro':
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $telefono = $_POST['telefono'];
        $password_segura = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Preparamos la consulta para evitar inyecciones SQL
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, telefono, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $apellido, $telefono, $password_segura);
        
        if ($stmt->execute()) {
            header("Location: ../login.php?mensaje=registrado");
        } else {
            header("Location: ../login.php?error=registro");
        }
        $stmt->close();
        break;

    case 'login':
        $telefono = $_POST['telefono'];
        $password = $_POST['password'];

        // Buscamos al usuario por su teléfono
        $sql = "SELECT id_usuario, nombre, password FROM usuarios WHERE telefono = ?";
        $stmt = $conexion -> prepare($sql);
        $stmt->bind_param("s", $telefono);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($usuario = $resultado->fetch_assoc()) {
            if (password_verify($password, $usuario['password'])) {
                //variables de sesión
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre'] = $usuario['nombre'];
            
                header("Location: ../index.php");
                exit();
            } else {
                header("Location: ../login.php?error=credenciales");
            }
        } else {
            header("Location: ../login.php?error=credenciales");
        }
        $stmt->close();
        break;
        
    default:
        header("Location: login.php");
        break;
}
?>