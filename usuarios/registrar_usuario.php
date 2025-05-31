<?php
include 'conexion.php';

// Datos de ejemplo
$nombre = "Laura";
$email = "laura@example.com";
$password_plano = "123456";

// Encriptar la contraseña
$password_segura = password_hash($password_plano, PASSWORD_DEFAULT);

// Rol: puede ser "cliente", "propietario" o "admin"
$rol = "cliente";

// 
$sql = "INSERT INTO Usuario (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nombre, $email, $password_segura, $rol);

// Ejecutamos
if ($stmt->execute()) {
    echo "Usuario registrado correctamente.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
