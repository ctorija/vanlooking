<?php
// conexión a MySQL
$host = "localhost";
$usuario = "root";
$contrasena = "";          // XAMPP por defecto
$basededatos = "camperapp";

$conn = new mysqli($host, $usuario, $contrasena, $basededatos);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>

