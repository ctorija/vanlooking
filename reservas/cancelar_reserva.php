<?php
session_start();
include 'conexion.php';

// Sólo clientes pueden cancelar
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.html");
    exit;
}

// Validar id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: mis_reservas.php");
    exit;
}

$reserva_id = (int) $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];

// Actualizar sólo si es del cliente y está pendiente
$stmt = $conn->prepare("
  UPDATE Reserva
  SET estado = 'cancelada'
  WHERE id = ? AND usuario_id = ? AND estado = 'pendiente'
");
$stmt->bind_param("ii", $reserva_id, $usuario_id);
$stmt->execute();
$stmt->close();
$conn->close();

// Volver al listado
header("Location: mis_reservas.php");
exit;
?>
