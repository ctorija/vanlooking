<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.html");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: buscar_vehiculos.php");
    exit;
}

$vehiculo_id = (int) $_GET['id'];

$stmt = $conn->prepare("
  SELECT titulo, tipo, marca, modelo, ano, ubicacion, precio_dia, descripcion
  FROM Vehiculo
  WHERE id = ?
");
$stmt->bind_param("i", $vehiculo_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows !== 1) {
    header("Location: buscar_vehiculos.php");
    exit;
}
$veh = $res->fetch_assoc();
$stmt->close();

$imgs = [];
$stmt2 = $conn->prepare("
  SELECT url
  FROM ImagenVehiculo
  WHERE vehiculo_id = ?
  ORDER BY es_principal DESC, id ASC
");
$stmt2->bind_param("i", $vehiculo_id);
$stmt2->execute();
$r2 = $stmt2->get_result();
while ($f = $r2->fetch_assoc()) {
    $imgs[] = $f['url'];
}
$stmt2->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Detalle – <?php echo htmlspecialchars($veh['titulo']); ?></title>
  
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    crossorigin="anonymous" />

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2a9d8f 0%, #264653 100%);
      color: #264653;
      min-height: 100vh;
      padding: 0 1rem;
      margin: 0;
      max-width: none;
      padding-bottom: 70px !important;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      padding: 1.5rem 0;
    }

    .page-header {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 2rem;
      background: white;
      padding: 1.25rem;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .back-btn {
      background: linear-gradient(135deg, #264653 0%, #1e3a40 100%);
      color: white;
      padding: 0.75rem;
      border-radius: 12px;
      text-decoration: none;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 45px;
      height: 45px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(38, 70, 83, 0.3);
      position: relative;
      overflow: hidden;
    }

    .back-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .back-btn:hover {
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      transform: translateX(-2px);
      box-shadow: 0 6px 20px rgba(42, 157, 143, 0.3);
    }

    .back-btn:hover::before {
      left: 100%;
    }

    .page-header h2 {
      margin: 0;
      color: #264653;
      font-size: 1.5rem;
      font-weight: 700;
      text-align: left;
      flex: 1;
    }

    .gallery-container {
      margin-bottom: 2rem;
    }

    .gallery {
      display: grid;
      gap: 1rem;
      grid-template-columns: 1fr;
    }

    .gallery.multiple {
      grid-template-columns: 1fr 1fr;
    }

    .gallery-item {
      background: white;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .gallery-item:hover {
      transform: translateY(-4px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .gallery-item.main {
      grid-column: 1 / -1;
    }

    .gallery-item img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .gallery-item:hover img {
      transform: scale(1.05);
    }

    .vehicle-details {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
      overflow: hidden;
    }

    .vehicle-details::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #2A9D8F, #264653);
    }

    .vehicle-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #264653;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .price-highlight {
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 25px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
      font-size: 1.1rem;
      box-shadow: 0 4px 15px rgba(42, 157, 143, 0.3);
    }

    .vehicle-info {
      display: grid;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .info-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem;
      background: #f8f9fa;
      border-radius: 12px;
      border-left: 4px solid #2a9d8f;
    }

    .info-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1rem;
      flex-shrink: 0;
    }

    .info-content {
      flex: 1;
    }

    .info-label {
      font-size: 0.85rem;
      font-weight: 600;
      color: #6b7280;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 0.25rem;
    }

    .info-value {
      font-size: 1rem;
      font-weight: 600;
      color: #264653;
    }

    .description-section {
      margin-top: 1.5rem;
      padding-top: 1.5rem;
      border-top: 2px solid #e9ecef;
    }

    .description-title {
      font-size: 1.1rem;
      font-weight: 700;
      color: #264653;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .description-text {
      color: #6b7280;
      line-height: 1.6;
      font-size: 1rem;
      background: #f8f9fa;
      padding: 1rem;
      border-radius: 12px;
      border-left: 4px solid #2a9d8f;
    }

    .reservation-form {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
      overflow: hidden;
    }

    .reservation-form::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #2A9D8F, #264653);
    }

    .form-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: #264653;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: #264653;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .form-group input {
      width: 100%;
      padding: 1rem;
      border: 2px solid #e9ecef;
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: #f8f9fa;
      color: #264653;
    }

    .form-group input:focus {
      outline: none;
      border-color: #2a9d8f;
      background: white;
      box-shadow: 0 0 0 4px rgba(42, 157, 143, 0.1);
      transform: translateY(-2px);
    }

    .submit-btn {
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      color: white;
      padding: 1.2rem 2rem;
      border: none;
      border-radius: 12px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      width: 100%;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      position: relative;
      overflow: hidden;
    }

    .submit-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .submit-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(42, 157, 143, 0.3);
    }

    .submit-btn:hover::before {
      left: 100%;
    }

    .submit-btn:active {
      transform: translateY(-1px);
    }

    @media (max-width: 600px) {
      body {
        padding: 0 1rem;
      }

      .container {
        padding: 1rem 0;
      }

      .gallery.multiple {
        grid-template-columns: 1fr;
      }

      .gallery-item img {
        height: 250px;
      }

      .page-header {
        padding: 1rem;
        margin-bottom: 1.5rem;
      }

      .page-header h2 {
        font-size: 1.3rem;
      }

      .vehicle-details,
      .reservation-form {
        padding: 1.5rem;
      }

      .vehicle-title {
        font-size: 1.3rem;
      }

      .form-title {
        font-size: 1.2rem;
      }
    }

    @media (min-width: 768px) {
      body {
        max-width: none;
        padding: 0 2rem;
      }

      .container {
        max-width: 1000px;
        padding: 2rem 0;
      }

      .main-content {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        align-items: start;
      }

      .reservation-form {
        position: sticky;
        top: 2rem;
      }

      .vehicle-details {
        margin-bottom: 0;
      }
    }

    .page-header {
      animation: slideDown 0.8s ease;
    }

    .gallery-item {
      animation: fadeInUp 0.6s ease both;
    }

    .gallery-item:nth-child(1) { animation-delay: 0.1s; }
    .gallery-item:nth-child(2) { animation-delay: 0.2s; }
    .gallery-item:nth-child(3) { animation-delay: 0.3s; }
    .gallery-item:nth-child(4) { animation-delay: 0.4s; }

    .vehicle-details {
      animation: fadeInUp 0.8s ease 0.2s both;
    }

    .reservation-form {
      animation: fadeInUp 0.8s ease 0.4s both;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 70px;
      padding-bottom: env(safe-area-inset-bottom);
      background: white;
      box-shadow: 0 -8px 25px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: row;
      justify-content: space-around;
      align-items: center;
      z-index: 1000;
      border-top: 2px solid rgba(42, 157, 143, 0.1);
    }

    .bottom-nav .nav-item {
      flex: none;
      width: 25%;
      margin: 0;
      padding: 0.75rem 0;
      text-decoration: none;
      color: #264653;
      font-size: 0.75rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      border-radius: 12px;
      font-weight: 600;
    }

    .bottom-nav .nav-item i {
      font-size: 1.4rem;
      margin-bottom: 6px;
      transition: all 0.3s ease;
    }

    .bottom-nav .nav-item span {
      font-size: 0.75rem;
      line-height: 1;
      font-weight: 600;
      letter-spacing: 0.3px;
    }

    .bottom-nav .nav-item.active {
      color: #2A9D8F;
    }

    .bottom-nav .nav-item.active i {
      transform: scale(1.2);
    }

    .bottom-nav .nav-item:hover {
      color: #2A9D8F;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="page-header">
      <a href="buscar_vehiculos.php" class="back-btn">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h2><?php echo htmlspecialchars($veh['titulo']); ?></h2>
    </div>

    <?php if (count($imgs) > 0): ?>
      <div class="gallery-container">
        <div class="gallery <?php echo count($imgs) > 1 ? 'multiple' : ''; ?>">
          <?php foreach ($imgs as $index => $img): ?>
            <div class="gallery-item <?php echo $index === 0 ? 'main' : ''; ?>">
              <img src="uploads/<?php echo htmlspecialchars($img); ?>" alt="Foto vehículo">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="main-content">
      <div class="vehicle-details">
        <h3 class="vehicle-title">
          <i class="fas fa-car"></i>
          Detalles del vehículo
        </h3>

        <div class="price-highlight">
          <i class="fas fa-euro-sign"></i>
          <?php echo htmlspecialchars($veh['precio_dia']); ?> / día
        </div>

        <div class="vehicle-info">
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-car"></i>
            </div>
            <div class="info-content">
              <div class="info-label">Tipo</div>
              <div class="info-value"><?php echo ucfirst(htmlspecialchars($veh['tipo'])); ?></div>
            </div>
          </div>

          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-cogs"></i>
            </div>
            <div class="info-content">
              <div class="info-label">Marca / Modelo</div>
              <div class="info-value"><?php echo htmlspecialchars($veh['marca'] . ' / ' . $veh['modelo']); ?></div>
            </div>
          </div>

          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="info-content">
              <div class="info-label">Año</div>
              <div class="info-value"><?php echo htmlspecialchars($veh['ano']); ?></div>
            </div>
          </div>

          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="info-content">
              <div class="info-label">Ubicación</div>
              <div class="info-value"><?php echo htmlspecialchars($veh['ubicacion']); ?></div>
            </div>
          </div>
        </div>

        <div class="description-section">
          <h4 class="description-title">
            <i class="fas fa-info-circle"></i>
            Descripción
          </h4>
          <div class="description-text">
            <?php echo nl2br(htmlspecialchars($veh['descripcion'])); ?>
          </div>
        </div>
      </div>

      <form action="reservar_vehiculo.php" method="POST" class="reservation-form">
        <input type="hidden" name="vehiculo_id" value="<?php echo $vehiculo_id; ?>">

        <h3 class="form-title">
          <i class="fas fa-calendar-check"></i>
          Reservar vehículo
        </h3>

        <div class="form-group">
          <label for="fecha_inicio">Fecha de inicio:</label>
          <input type="date" id="fecha_inicio" name="fecha_inicio" required>
        </div>

        <div class="form-group">
          <label for="fecha_fin">Fecha de fin:</label>
          <input type="date" id="fecha_fin" name="fecha_fin" required>
        </div>

        <button type="submit" class="submit-btn">
          <i class="fas fa-calendar-plus"></i>
          Confirmar reserva
        </button>
      </form>
    </div>
  </div>

  <nav class="bottom-nav">
    <a href="dashboard.php" class="nav-item">
      <i class="fas fa-home"></i>
      <span>Inicio</span>
    </a>
    <a href="buscar_vehiculos.php" class="nav-item active">
      <i class="fas fa-search"></i>
      <span>Búsqueda</span>
    </a>
    <a href="mis_reservas.php" class="nav-item">
      <i class="fas fa-calendar-alt"></i>
      <span>Reservas</span>
    </a>
    <a href="perfil.php" class="nav-item">
      <i class="fas fa-user"></i>
      <span>Perfil</span>
    </a>
  </nav>
</body>
</html>