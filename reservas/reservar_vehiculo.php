<?php
session_start();
include 'conexion.php';

// Sólo clientes pueden reservar
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.html");
    exit;
}

// Recoger datos del formulario
$vehiculo_id  = (int) $_POST['vehiculo_id'];
$usuario_id   = $_SESSION['usuario_id'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin    = $_POST['fecha_fin'];

// Calcular días y monto total
$d1 = new DateTime($fecha_inicio);
$d2 = new DateTime($fecha_fin);
$interval = $d2->diff($d1)->days + 1;
$monto = 0;

// Obtener precio por día
$stmt = $conn->prepare("SELECT precio_dia FROM Vehiculo WHERE id = ?");
$stmt->bind_param("i", $vehiculo_id);
$stmt->execute();
$stmt->bind_result($precio_x_dia);
$stmt->fetch();
$stmt->close();

$monto = $precio_x_dia * $interval;

// Insertar reserva
$stmt2 = $conn->prepare("
  INSERT INTO Reserva (vehiculo_id, usuario_id, fecha_inicio, fecha_fin, estado, monto_total)
  VALUES (?, ?, ?, ?, 'pendiente', ?)
");
$stmt2->bind_param("iissd", $vehiculo_id, $usuario_id, $fecha_inicio, $fecha_fin, $monto);
if ($stmt2->execute()) {
    header("Location: mis_reservas.php");
    exit;
} else {
    echo "Error al reservar: " . $stmt2->error;
}
$stmt2->close();
$conn->close();
?>
