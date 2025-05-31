<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.html");
    exit;
}

$cliente_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("
  SELECT 
    r.id,
    v.id as vehiculo_id,
    v.titulo,
    r.fecha_inicio,
    r.fecha_fin,
    r.estado
  FROM Reserva r
  JOIN Vehiculo v ON r.vehiculo_id = v.id
  WHERE r.usuario_id = ?
  ORDER BY r.fecha_inicio DESC
");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mis Reservas – VanLooking</title>

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
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .reservas-stats {
      background: white;
      border-radius: 16px;
      padding: 1.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      gap: 1rem;
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
      overflow: hidden;
    }

    .reservas-stats::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #2A9D8F, #264653);
    }

    .stats-icon {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.5rem;
      box-shadow: 0 4px 15px rgba(42, 157, 143, 0.3);
      position: relative;
      overflow: hidden;
    }

    .stats-icon::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .stats-icon:hover::before {
      left: 100%;
    }

    .stats-text {
      flex: 1;
    }

    .stats-number {
      font-size: 2rem;
      font-weight: 700;
      color: #264653;
      margin: 0;
      margin-bottom: 0.25rem;
    }

    .stats-label {
      font-size: 0.95rem;
      color: #6b7280;
      margin: 0;
      font-weight: 500;
    }

    .reservas-container {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .reserva-card {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
      overflow: hidden;
    }

    .reserva-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #2A9D8F, #264653);
    }

    .reserva-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .reserva-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1.5rem;
    }

    .reserva-titulo {
      font-size: 1.3rem;
      font-weight: 700;
      color: #264653;
      margin: 0;
      line-height: 1.3;
    }

    .estado-badge {
      padding: 0.6rem 1.2rem;
      border-radius: 25px;
      font-size: 0.85rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .estado-confirmada {
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      color: white;
    }

    .estado-pendiente {
      background: linear-gradient(135deg, #F4A261 0%, #e8944a 100%);
      color: white;
    }

    .estado-cancelada {
      background: linear-gradient(135deg, #E76F51 0%, #d4553a 100%);
      color: white;
    }

    .reserva-fechas {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .fecha-item {
      background: #f8f9fa;
      padding: 1rem;
      border-radius: 12px;
      border-left: 4px solid #2a9d8f;
    }

    .fecha-label {
      font-size: 0.85rem;
      color: #6b7280;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 0.5rem;
      display: block;
    }

    .fecha-valor {
      font-size: 1.1rem;
      font-weight: 700;
      color: #264653;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .fecha-valor i {
      color: #2A9D8F;
      font-size: 1rem;
    }

    .reserva-actions {
      display: flex;
      gap: 1rem;
      margin-top: 1.5rem;
    }

    .btn-ver {
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      color: white;
      padding: 1rem 2rem;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      transition: all 0.3s ease;
      flex: 1;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      position: relative;
      overflow: hidden;
    }

    .btn-ver::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .btn-ver:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(42, 157, 143, 0.3);
    }

    .btn-ver:hover::before {
      left: 100%;
    }

    .no-reservas {
      background: white;
      border-radius: 20px;
      padding: 3rem 2rem;
      text-align: center;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
      overflow: hidden;
    }

    .no-reservas::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #F4A261, #e8944a);
    }

    .no-reservas-icon {
      font-size: 4rem;
      color: #F4A261;
      margin-bottom: 2rem;
    }

    .no-reservas h3 {
      color: #264653;
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .no-reservas p {
      color: #6b7280;
      margin-bottom: 2rem;
      line-height: 1.6;
      font-size: 1rem;
    }

    .btn-buscar {
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      color: white;
      padding: 1.2rem 2rem;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.75rem;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      position: relative;
      overflow: hidden;
    }

    .btn-buscar::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .btn-buscar:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(42, 157, 143, 0.3);
    }

    .btn-buscar:hover::before {
      left: 100%;
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

    @media (max-width: 600px) {
      body {
        padding: 0 1rem;
      }

      .container {
        padding: 1rem 0;
      }

      .reserva-fechas {
        grid-template-columns: 1fr;
        gap: 1rem;
      }

      .reserva-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
      }

      .reserva-card {
        padding: 1.5rem;
      }

      .no-reservas {
        padding: 2rem 1.5rem;
      }

      .no-reservas-icon {
        font-size: 3rem;
      }

      .page-header {
        padding: 1rem;
        margin-bottom: 1.5rem;
      }

      .page-header h2 {
        font-size: 1.3rem;
      }

      .reservas-stats {
        padding: 1.25rem;
      }

      .stats-icon {
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
      }

      .stats-number {
        font-size: 1.8rem;
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

      .reservas-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 2rem;
      }

      .reserva-card {
        padding: 2.5rem;
      }
    }

    .page-header {
      animation: slideDown 0.8s ease;
    }

    .reservas-stats {
      animation: slideDown 0.8s ease 0.1s both;
    }

    .reserva-card {
      animation: fadeInUp 0.6s ease both;
    }

    .reserva-card:nth-child(1) { animation-delay: 0.2s; }
    .reserva-card:nth-child(2) { animation-delay: 0.3s; }
    .reserva-card:nth-child(3) { animation-delay: 0.4s; }
    .reserva-card:nth-child(4) { animation-delay: 0.5s; }
    .reserva-card:nth-child(5) { animation-delay: 0.6s; }

    .no-reservas {
      animation: fadeInUp 0.8s ease 0.2s both;
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
  </style>
</head>
<body>
  <div class="container">
    <div class="page-header">
      <a href="dashboard.php" class="back-btn">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h2>
        <i class="fas fa-calendar-alt"></i>
        Mis Reservas
      </h2>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <div class="reservas-stats">
        <div class="stats-icon">
          <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stats-text">
          <p class="stats-number"><?php echo $result->num_rows; ?></p>
          <p class="stats-label">reserva<?php echo $result->num_rows > 1 ? 's' : ''; ?> total<?php echo $result->num_rows > 1 ? 'es' : ''; ?></p>
        </div>
      </div>

      <div class="reservas-container">
        <?php while ($r = $result->fetch_assoc()): ?>
          <div class="reserva-card">
            <div class="reserva-header">
              <h3 class="reserva-titulo"><?php echo htmlspecialchars($r['titulo']); ?></h3>
              <span class="estado-badge estado-<?php echo htmlspecialchars($r['estado']); ?>">
                <?php echo htmlspecialchars($r['estado']); ?>
              </span>
            </div>

            <div class="reserva-fechas">
              <div class="fecha-item">
                <span class="fecha-label">Fecha de inicio</span>
                <span class="fecha-valor">
                  <i class="fas fa-calendar-plus"></i>
                  <?php 
                    $fecha_inicio = new DateTime($r['fecha_inicio']);
                    echo $fecha_inicio->format('d/m/Y');
                  ?>
                </span>
              </div>
              <div class="fecha-item">
                <span class="fecha-label">Fecha de fin</span>
                <span class="fecha-valor">
                  <i class="fas fa-calendar-minus"></i>
                  <?php 
                    $fecha_fin = new DateTime($r['fecha_fin']);
                    echo $fecha_fin->format('d/m/Y');
                  ?>
                </span>
              </div>
            </div>

            <div class="reserva-actions">
              <a href="detalle_vehiculo.php?id=<?php echo $r['vehiculo_id']; ?>" class="btn-ver">
                <i class="fas fa-eye"></i>
                Ver vehículo
              </a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="no-reservas">
        <div class="no-reservas-icon">
          <i class="fas fa-calendar-times"></i>
        </div>
        <h3>No tienes reservas aún</h3>
        <p>¡Es el momento perfecto para planear tu próxima aventura! Explora nuestra selección de vehículos y encuentra el compañero ideal para tu viaje.</p>
        <a href="buscar_vehiculos.php" class="btn-buscar">
          <i class="fas fa-search"></i>
          Buscar vehículos
        </a>
      </div>
    <?php endif; ?>
  </div>

  <nav class="bottom-nav">
    <a href="dashboard.php" class="nav-item">
      <i class="fas fa-home"></i>
      <span>Inicio</span>
    </a>
    <a href="buscar_vehiculos.php" class="nav-item">
      <i class="fas fa-search"></i>
      <span>Búsqueda</span>
    </a>
    <a href="mis_reservas.php" class="nav-item active">
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
<?php
$stmt->close();
$conn->close();
?>