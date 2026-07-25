<?php
// Incluir la conexión (subiendo un nivel si está en la carpeta /php/)
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // 1. Validar campos vacíos
    if (empty($nombre) || empty($apellido) || empty($telefono) || empty($password)) {
        echo "<script>
                alert('Por favor complete todos los campos.');
                window.history.back();
              </script>";
        exit;
    }

    // 2. Validar formato de teléfono (10 dígitos)
    if (!preg_match('/^[0-9]{10}$/', $telefono)) {
        echo "<script>
                alert('El número de teléfono debe tener exactamente 10 dígitos.');
                window.history.back();
              </script>";
        exit;
    }

    try {
        // 3. Verificar si el número de teléfono ya existe
        $checkStmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE telefono = :telefono");
        $checkStmt->bindParam(':telefono', $telefono);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            echo "<script>
                    alert('Este número de teléfono ya está registrado.');
                    window.history.back();
                  </script>";
            exit;
        }

        // 4. Hash de la contraseña mediante BCRYPT
        $passHash = password_hash($password, PASSWORD_BCRYPT);

        // 5. Insertar en la base de datos
        $sql = "INSERT INTO usuarios (nombre, apellido, telefono, password) VALUES (:nombre, :apellido, :telefono, :password)";
        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':telefono', $telefono);
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