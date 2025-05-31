<?php
include 'conexion.php';

$nombre         = $_POST['nombre'];
$email          = $_POST['email'];
$password_plano = $_POST['password'];
$rol            = $_POST['rol'];

$password_hash = password_hash($password_plano, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
  "INSERT INTO Usuario (nombre, email, password, rol) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $nombre, $email, $password_hash, $rol);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit;
} else {
    echo "Error al registrar: " . $stmt->error;
}
