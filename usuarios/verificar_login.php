<?php
session_start();
include 'conexion.php';

$email    = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare(
  "SELECT id, nombre, password, rol FROM Usuario WHERE email = ?"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $u = $result->fetch_assoc();
    if (password_verify($password, $u['password'])) {
        $_SESSION['usuario_id'] = $u['id'];
        $_SESSION['nombre']     = $u['nombre'];
        $_SESSION['rol']        = $u['rol'];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "Email no registrado.";
}
