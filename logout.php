<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cerrando sesión...</title>
</head>
<body>
    <script>
        // ESTO BORRA EL CARRITO DEL NAVEGADOR 
        localStorage.removeItem('carritoCompras');
        
        // Redirigimos al usuario de vuelta al inicio
        window.location.href = 'index.php';
    </script>
</body>
</html>