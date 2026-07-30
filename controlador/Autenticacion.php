<?php

session_start();

require_once '../config/conexion.php'; 

$operacion = $_POST['operacion'] ?? '';

switch ($operacion) {
    
    case 'registro':
        $nombre = $_POST['nombre'] ?? '';
        $apellido = $_POST['apellido'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $password_segura = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

        // Preparamos la consulta con PDO
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, correo, password) VALUES (?, ?, ?, ?)");
        
        // En PDO pasamos los valores como un array directamente al execute()
        if ($stmt->execute([$nombre, $apellido, $correo, $password_segura])) {
            
            
            // 1. Recuperamos el ID del nuevo usuario usando la sintaxis de PDO
            $id_nuevo_usuario = $conexion->lastInsertId();

            // 2. Declaramos las variables de sesión que tu index necesita para reconocerlo
            $_SESSION['id_usuario'] = $id_nuevo_usuario;
            $_SESSION['nombre'] = $nombre;
            
            // 3. Redirigimos al index. ¡Ahora sí será reconocido y aparecerá el botón Salir!
            header("Location: ../index.php");
            exit();
            
            // --- FIN DEL AUTO-LOGIN ---
            
        } else {
            header("Location: ../login.php?error=registro");
            exit();
        }
        break;

    case 'login':
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';

    // Incluimos 'rol' en la consulta
    $sql = "SELECT id_usuario, nombre, password, rol FROM usuarios WHERE correo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            // Redirección según rol
            if ($usuario['rol'] === 'admin') {
                header("Location: /Proyecto-Cafeteria/admin_panel/usuarios.php");
                exit();
            } else {
                header("Location: ../index.php");
                exit();
            }
        } else {
            header("Location: ../login.php?error=credenciales");
            exit();
        }
    } else {
        header("Location: ../login.php?error=credenciales");
        exit();
    }
    break;
        
    default:
        header("Location: ../login.php");
        exit();
        break;
}
?>