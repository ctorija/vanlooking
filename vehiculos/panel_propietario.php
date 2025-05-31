<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'propietario') {
    header("Location: login.html");
    exit;
}
$propietario_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("
    SELECT id, titulo, tipo, marca, modelo, ano, ubicacion, precio_dia 
    FROM Vehiculo 
    WHERE propietario_id = ?
");
$stmt->bind_param("i", $propietario_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mis Vehículos – VanLooking</title>

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
      max-width: 1200px;
      margin: 0 auto;
      padding: 1.5rem 0;
      width: 100%;
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

    .vehiculos-stats {
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

    .vehiculos-stats::before {
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

    .vehiculos-container {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .vehiculo-card {
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

    .vehiculo-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #2A9D8F, #264653);
    }

    .vehiculo-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .vehiculo-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1.5rem;
    }

    .vehiculo-titulo {
      font-size: 1.3rem;
      font-weight: 700;
      color: #264653;
      margin: 0;
      line-height: 1.3;
      flex: 1;
      margin-right: 1rem;
    }

    .precio-badge {
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      color: white;
      padding: 0.75rem 1.25rem;
      border-radius: 25px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 1rem;
      box-shadow: 0 4px 15px rgba(42, 157, 143, 0.3);
      white-space: nowrap;
    }

    .vehiculo-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .info-item {
      background: #f8f9fa;
      padding: 1rem;
      border-radius: 12px;
      border-left: 4px solid #2a9d8f;
    }

    .info-label {
      font-size: 0.85rem;
      color: #6b7280;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .info-label i {
      color: #2A9D8F;
      width: 14px;
    }

    .info-value {
      font-size: 1rem;
      font-weight: 700;
      color: #264653;
    }

    .vehiculo-actions {
      display: flex;
      gap: 1rem;
      margin-top: 1.5rem;
    }

    .btn-editar {
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      color: white;
      padding: 1rem 1.5rem;
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

    .btn-editar::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .btn-editar:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(42, 157, 143, 0.3);
    }

    .btn-editar:hover::before {
      left: 100%;
    }

    .btn-eliminar {
      background: linear-gradient(135deg, #E76F51 0%, #d4553a 100%);
      color: white;
      padding: 1rem 1.5rem;
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

    .btn-eliminar::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .btn-eliminar:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(231, 111, 81, 0.3);
    }

    .btn-eliminar:hover::before {
      left: 100%;
    }

    .no-vehiculos {
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

    .no-vehiculos::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #F4A261, #e8944a);
    }

    .no-vehiculos-icon {
      font-size: 4rem;
      color: #F4A261;
      margin-bottom: 2rem;
    }

    .no-vehiculos h3 {
      color: #264653;
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .no-vehiculos p {
      color: #6b7280;
      margin-bottom: 2rem;
      line-height: 1.6;
      font-size: 1rem;
    }

    .btn-agregar {
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

    .btn-agregar::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .btn-agregar:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(42, 157, 143, 0.3);
    }

    .btn-agregar:hover::before {
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
        padding: 0 0.75rem;
      }

      .container {
        padding: 1rem 0;
        max-width: 100%;
      }

      .vehiculo-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
      }

      .vehiculo-titulo {
        margin-right: 0;
      }

      .vehiculo-info {
        grid-template-columns: 1fr;
        gap: 0.75rem;
      }

      .vehiculo-actions {
        flex-direction: column;
        gap: 0.75rem;
      }

      .vehiculo-card {
        padding: 1.5rem 1rem;
        margin: 0;
        width: 100%;
      }

      .no-vehiculos {
        padding: 2rem 1.5rem;
      }

      .no-vehiculos-icon {
        font-size: 3rem;
      }

      .page-header {
        padding: 1rem;
        margin-bottom: 1.5rem;
      }

      .page-header h2 {
        font-size: 1.3rem;
      }

      .vehiculos-stats {
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
        padding: 2rem 0;
      }

      .vehiculos-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 2rem;
      }

      .vehiculo-card {
        padding: 2.5rem;
      }

      .vehiculo-actions {
        flex-direction: row;
      }
    }

    .page-header {
      animation: slideDown 0.8s ease;
    }

    .vehiculos-stats {
      animation: slideDown 0.8s ease 0.1s both;
    }

    .vehiculo-card {
      animation: fadeInUp 0.6s ease both;
    }

    .vehiculo-card:nth-child(1) { animation-delay: 0.2s; }
    .vehiculo-card:nth-child(2) { animation-delay: 0.3s; }
    .vehiculo-card:nth-child(3) { animation-delay: 0.4s; }
    .vehiculo-card:nth-child(4) { animation-delay: 0.5s; }
    .vehiculo-card:nth-child(5) { animation-delay: 0.6s; }

    .no-vehiculos {
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
        <i class="fas fa-car"></i>
        Mis Vehículos
      </h2>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <div class="vehiculos-stats">
        <div class="stats-icon">
          <i class="fas fa-car"></i>
        </div>
        <div class="stats-text">
          <p class="stats-number"><?php echo $result->num_rows; ?></p>
          <p class="stats-label">vehículo<?php echo $result->num_rows > 1 ? 's' : ''; ?> registrado<?php echo $result->num_rows > 1 ? 's' : ''; ?></p>
        </div>
      </div>

      <div class="vehiculos-container">
        <?php while ($v = $result->fetch_assoc()): ?>
          <div class="vehiculo-card">
            <div class="vehiculo-header">
              <h3 class="vehiculo-titulo"><?php echo htmlspecialchars($v['titulo']); ?></h3>
              <div class="precio-badge">
                <i class="fas fa-euro-sign"></i>
                <?php echo htmlspecialchars($v['precio_dia']); ?> / día
              </div>
            </div>

            <div class="vehiculo-info">
              <div class="info-item">
                <div class="info-label">
                  <i class="fas fa-car"></i>
                  Tipo
                </div>
                <div class="info-value"><?php echo ucfirst(htmlspecialchars($v['tipo'])); ?></div>
              </div>

              <div class="info-item">
                <div class="info-label">
                  <i class="fas fa-cogs"></i>
                  Marca/Modelo
                </div>
                <div class="info-value">
                  <?php echo htmlspecialchars($v['marca']); ?> / <?php echo htmlspecialchars($v['modelo']); ?>
                </div>
              </div>

              <div class="info-item">
                <div class="info-label">
                  <i class="fas fa-calendar-alt"></i>
                  Año
                </div>
                <div class="info-value"><?php echo htmlspecialchars($v['ano']); ?></div>
              </div>

              <div class="info-item">
                <div class="info-label">
                  <i class="fas fa-map-marker-alt"></i>
                  Ubicación
                </div>
                <div class="info-value"><?php echo htmlspecialchars($v['ubicacion']); ?></div>
              </div>
            </div>

            <div class="vehiculo-actions">
              <a href="editar_vehiculo.php?id=<?php echo $v['id']; ?>" class="btn-editar">
                <i class="fas fa-edit"></i>
                Editar
              </a>
              <a href="eliminar_vehiculo.php?id=<?php echo $v['id']; ?>" class="btn-eliminar"
                 onclick="return confirm('¿Seguro que quieres eliminar este vehículo?');">
                <i class="fas fa-trash"></i>
                Eliminar
              </a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="no-vehiculos">
        <div class="no-vehiculos-icon">
          <i class="fas fa-car"></i>
        </div>
        <h3>No tienes vehículos registrados</h3>
        <p>¡Comienza tu negocio! Agrega tu primer vehículo y empieza a recibir reservas de viajeros que buscan la aventura perfecta.</p>
        <a href="nuevo_vehiculo.php" class="btn-agregar">
          <i class="fas fa-plus"></i>
          Agregar vehículo
        </a>
      </div>
    <?php endif; ?>
  </div>

  <nav class="bottom-nav">
    <a href="dashboard.php" class="nav-item">
      <i class="fas fa-home"></i>
      <span>Inicio</span>
    </a>
    <a href="nuevo_vehiculo.php" class="nav-item">
      <i class="fas fa-plus"></i>
      <span>Agregar</span>
    </a>
    <a href="panel_propietario.php" class="nav-item active">
      <i class="fas fa-car"></i>
      <span>Mis vehículos</span>
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