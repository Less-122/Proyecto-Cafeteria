<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "cafe_db";

//crear conexion
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

//verificar la conexion
if($conexion -> connect_error){
    die("Error de conexión: " . $conexion -> connect_error);
}
//echo "Conexion exitosa <br>";

//Cerrar la conexion
// $conexion -> close();