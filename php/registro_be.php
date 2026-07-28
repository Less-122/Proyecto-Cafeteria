<?php
// Incluir la conexión (subiendo un nivel si está en la carpeta /php/)
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // 1. Validar campos vacíos
    if (empty($nombre) || empty($apellido) || empty($correo) || empty($password)) {
        echo "<script>
                alert('Por favor complete todos los campos.');
                window.history.back();
              </script>";
        exit;
    }

    // Validar formato de correo electrónico (con Regex)
if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $correo)) {
    echo "<script>
            alert('El correo electrónico no tiene un formato válido.');
            window.history.back();
          </script>";
    exit;
}

    try {
    // 3. Verificar si el correo electrónico ya existe
    $checkStmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE correo = :correo");
    $checkStmt->bindParam(':correo', $correo);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        echo "<script>
                alert('Este correo electrónico ya está registrado.');
                window.history.back();
              </script>";
        exit;
    }

        // 4. Hash de la contraseña mediante BCRYPT
        $passHash = password_hash($password, PASSWORD_BCRYPT);

        // 5. Insertar en la base de datos
        $sql = "INSERT INTO usuarios (nombre, apellido, correo, password) VALUES (:nombre, :apellido, :correo, :password)";
        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':telefono', $correo);
        $stmt->bindParam(':password', $passHash);

        if ($stmt->execute()) {
            echo "<script>
                    alert('¡Cuenta creada exitosamente! Ahora puedes iniciar sesión.');
                    window.location.href = '../login.php';
                  </script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "<script>
                alert('Error al registrar usuario: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>