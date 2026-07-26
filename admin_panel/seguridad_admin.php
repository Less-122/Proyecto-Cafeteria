
<?php 
session_start();

// Si el usuario no ha iniciado sesión, O su nombre NO es exactamente 'administrador'
if (!isset($_SESSION['id_usuario']) || $_SESSION['nombre'] !== 'administrador') {
    header("Location: ../index.php"); 
    exit();
}
?>