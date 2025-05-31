<?php
session_start();
include 'conexion.php';

// Validar sesión y rol
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'propietario') {
    header("Location: login.html");
    exit;
}

// Comprobar que llega un id válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: panel_propietario.php");
    exit;
}

$vehiculo_id    = (int) $_GET['id'];
$propietario_id = $_SESSION['usuario_id'];

// Borrar sólo si es del propietario logueado
$stmt = $conn->prepare(
    "DELETE FROM Vehiculo WHERE id = ? AND propietario_id = ?"
);
$stmt->bind_param("ii", $vehiculo_id, $propietario_id);
$stmt->execute();
$stmt->close();
$conn->close();

// Volver al panel con los vehículos
header("Location: panel_propietario.php");
exit;
?>
