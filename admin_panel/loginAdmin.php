<?php
session_start();
require_once '../config/conexion.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // Buscamos específicamente a un usuario que se llame 'administrador'
    $sql = "SELECT id_usuario, nombre, password FROM usuarios WHERE correo = ? AND nombre = 'administrador'";
    
    // Preparación y ejecución con sintaxis PDO
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Verificamos la contraseña encriptada
        if (password_verify($password, $usuario['password'])) {
            // Guardamos las variables de sesión requeridas por seguridad_admin.php
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            
            // Redirección al panel
            header("Location: productos.php"); 
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "No tienes permisos de administrador o los datos son incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso Administrativo - Aroma a Café</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #2c3e50; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .admin-login-box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); width: 100%; max-width: 350px; text-align: center; }
        input { width: 90%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #e67e22; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #d35400; }
        .error { color: red; font-size: 14px; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="admin-login-box">
        <h2>Panel de Control</h2>
        <p>Solo personal autorizado</p>
        
        <?php if ($error != ''): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="correo" name="correo" placeholder="Correo del Admin" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar al Panel</button>
        </form>
    </div>

</body>
</html>