<?php
session_start();
include '../conexion.php'; // Asegúrate de que la ruta a tu archivo de conexión sea correcta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Sanitizar y limpiar entradas
    $correo = trim($_POST['correo'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validar que los campos no estén vacíos
    if (empty($correo) || empty($password)) {
        echo '
            <script>
                alert("Por favor, llena todos los campos.");
                window.location = "../login.php";
            </script>
        ';
        exit();
    }

    try {
        // 2. Consultar el usuario en la base de datos por email
        $stmt = $conexion->prepare("SELECT id_usuario, nombre, apellido, correo, password FROM usuarios WHERE telefono = :telefono LIMIT 1");
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // 3. Verificar si existe el usuario y validar la contraseña
        // Si usaste password_hash() al registrar: password_verify($password, $usuario['password'])
        // Si guardas en texto plano (menos recomendado): $password === $usuario['password']
        if ($usuario && (password_verify($password, $usuario['password']) || $password === $usuario['password'])) {
            
            // 4. Iniciar variables de sesión
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['apellido'] = $usuario['apellido'];
            $_SESSION['correo'] = $usuario['correo'];

            // 5. Redireccionar al usuario al catálogo/inicio
            header("Location: ../index.php"); // o la página principal de tu cafetería
            exit();

        } else {
            // Contraseña incorrecta o usuario no encontrado
            echo '
                <script>
                    alert("Correo o contraseña incorrectos.");
                    window.location = "../login.php";
                </script>
            ';
            exit();
        }

    } catch (PDOException $e) {
        echo '
            <script>
                alert("Error al intentar iniciar sesión. Inténtalo de nuevo más tarde.");
                window.location = "../login.php";
            </script>
        ';
        exit();
    }

} else {
    // Si intentan entrar al archivo directamente sin enviar el formulario
    header("Location: ../login.php");
    exit();
}
?>