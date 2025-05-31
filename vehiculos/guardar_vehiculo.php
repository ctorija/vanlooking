<?php
session_start();
include 'conexion.php';
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'propietario') {
    header("Location: login.html");
    exit;
}

// 1) Recoger datos del formulario
$propietario_id = $_SESSION['usuario_id'];
$titulo         = $_POST['titulo'];
$tipo           = $_POST['tipo'];
$marca          = $_POST['marca'];
$modelo         = $_POST['modelo'];
$ano            = $_POST['ano'];
$ubicacion      = $_POST['ubicacion'];
$latitud        = $_POST['latitud'];
$longitud       = $_POST['longitud'];
$precio_dia     = $_POST['precio_dia'];
$descripcion    = $_POST['descripcion'];

// 2) Si lat/lng vienen vacíos, hacemos geocoding en el servidor
if (empty($latitud) || empty($longitud)) {
    $url = "https://nominatim.openstreetmap.org/search?"
         . "format=json&limit=1&q=" . urlencode($ubicacion);
    $json = @file_get_contents($url);
    if ($json !== false) {
        $data = json_decode($json, true);
        if (!empty($data[0]['lat']) && !empty($data[0]['lon'])) {
            $latitud  = $data[0]['lat'];
            $longitud = $data[0]['lon'];
        }
    }
}

// 3) Insertar el vehículo con coordenadas
$stmt = $conn->prepare("
  INSERT INTO Vehiculo
    (propietario_id, titulo, tipo, marca, modelo, ano, ubicacion, latitud, longitud, precio_dia, descripcion)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "issssissdds",
    $propietario_id,
    $titulo,
    $tipo,
    $marca,
    $modelo,
    $ano,
    $ubicacion,
    $latitud,
    $longitud,
    $precio_dia,
    $descripcion
);
$stmt->execute();
$vehiculo_id = $conn->insert_id;
$stmt->close();

// 4) Procesar subidas de imágenes
if (!empty($_FILES['imagenes'])) {
    foreach ($_FILES['imagenes']['tmp_name'] as $i => $tmpName) {
        if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
            $ext  = pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION);
            $name = uniqid('img_', true) . ".$ext";
            move_uploaded_file($tmpName, __DIR__ . "/uploads/$name");
            $isMain = $i === 0 ? 1 : 0;
            $st = $conn->prepare("
              INSERT INTO ImagenVehiculo (vehiculo_id, url, es_principal)
              VALUES (?, ?, ?)
            ");
            $st->bind_param("isi", $vehiculo_id, $name, $isMain);
            $st->execute();
            $st->close();
        }
    }
}

$conn->close();
header("Location: panel_propietario.php");
exit;
?>
