<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}
$nombre = $_SESSION['nombre'];
$rol    = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Inicio – VanLooking</title>

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    crossorigin="anonymous" />

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="style.css?v=2">

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
    }

    .dashboard-wrapper {
      max-width: 600px;
      margin: 0 auto;
      padding: 2rem 0 100px;
      position: relative;
      width: 100%;
    }

    @media (min-width: 768px) {
      body {
        max-width: none !important;
        padding: 0 2rem !important;
      }

      .dashboard-wrapper {
        max-width: 1400px;
        padding: 3rem 0 100px;
        margin: 0 auto;
      }

      .welcome-header {
        padding: 3rem 2rem;
        margin-bottom: 3rem;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 3rem;
      }

      .welcome-header h1 {
        font-size: 2.2rem;
      }

      .user-avatar {
        width: 120px;
        height: 120px;
        font-size: 3rem;
        margin-bottom: 2rem;
      }

      .role-badge {
        padding: 1rem 2rem;
        font-size: 1rem;
      }

      .main-navigation {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 2rem !important;
        margin-bottom: 3rem;
      }

      .nav-action {
        padding: 2rem 2.5rem !important;
        height: 120px !important;
        justify-content: flex-start !important;
        gap: 1.5rem !important;
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        flex-direction: row !important;
        max-width: none !important;
        margin: 0 !important;
      }

      .nav-icon {
        width: 45px !important;
        height: 45px !important;
        font-size: 1.1rem !important;
        flex-shrink: 0 !important;
      }

      .nav-text {
        flex: 1 !important;
      }

      .nav-text h3 {
        font-size: 1.2rem !important;
        margin-bottom: 0.4rem !important;
      }

      .nav-text p {
        font-size: 0.9rem !important;
        line-height: 1.3 !important;
      }
    }

    .welcome-header {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
      text-align: center;
      position: relative;
      overflow: hidden;
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .welcome-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #2A9D8F, #264653);
    }

    .user-avatar {
      width: 100px;
      height: 100px;
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 2.5rem;
      font-weight: 700;
      margin: 0 auto 1.5rem;
      box-shadow: 0 10px 30px rgba(42, 157, 143, 0.3);
      position: relative;
      overflow: hidden;
    }

    .user-avatar::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .user-avatar:hover::before {
      left: 100%;
    }

    .welcome-header h1 {
      font-size: 1.8rem;
      margin-bottom: 1rem;
      color: #264653;
      font-weight: 700;
    }

    .role-badge {
      background: #264653;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 25px;
      font-size: 0.9rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-transform: capitalize;
      letter-spacing: 0.5px;
      box-shadow: 0 5px 15px rgba(38, 70, 83, 0.3);
    }

    .main-navigation {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .nav-action {
      background: white;
      border-radius: 16px;
      padding: 1.5rem;
      text-decoration: none;
      color: #264653;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 1.25rem;
      border-left: 4px solid transparent;
      position: relative;
      overflow: hidden;
    }

    .nav-action::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(42, 157, 143, 0.05), transparent);
      transition: left 0.5s;
    }

    .nav-action:hover {
      transform: translateY(-4px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.15);
      border-left-color: #2A9D8F;
    }

    .nav-action:hover::before {
      left: 100%;
    }

    .nav-action.primary {
      border-left-color: #2A9D8F;
    }

    .nav-action.primary:hover {
      
    }

    .nav-icon {
      width: 60px;
      height: 60px;
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      color: white;
      flex-shrink: 0;
      position: relative;
      overflow: hidden;
    }

    .nav-icon::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .nav-icon:hover::before {
      left: 100%;
    }

    .nav-icon.primary { 
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      box-shadow: 0 8px 20px rgba(42, 157, 143, 0.3);
    }
    .nav-icon.secondary { 
      background: linear-gradient(135deg, #264653 0%, #1e3a40 100%);
      box-shadow: 0 8px 20px rgba(38, 70, 83, 0.3);
    }
    .nav-icon.accent { 
      background: linear-gradient(135deg, #264653 0%, #1e3a40 100%);
      box-shadow: 0 8px 20px rgba(38, 70, 83, 0.3);
    }
    .nav-icon.danger { 
      background: linear-gradient(135deg, #E76F51 0%, #d4553a 100%);
      box-shadow: 0 8px 20px rgba(231, 111, 81, 0.3);
    }

    .nav-text h3 {
      font-size: 1.2rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: #264653;
    }

    .nav-text p {
      font-size: 0.95rem;
      color: #6b7280;
      margin: 0;
      line-height: 1.4;
      font-weight: 500;
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

    .welcome-header {
      animation: slideDown 0.8s ease;
    }

    .nav-action {
      animation: fadeInUp 0.6s ease both;
    }

    .nav-action:nth-child(1) { animation-delay: 0.1s; }
    .nav-action:nth-child(2) { animation-delay: 0.2s; }
    .nav-action:nth-child(3) { animation-delay: 0.3s; }
    .nav-action:nth-child(4) { animation-delay: 0.4s; }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-40px);
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

    @media (max-width: 600px) {
      body {
        padding: 0 1rem;
      }

      .dashboard-wrapper {
        padding: 1.5rem 0 100px;
      }

      .welcome-header {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
      }

      .user-avatar {
        width: 80px;
        height: 80px;
        font-size: 2rem;
        margin-bottom: 1rem;
      }

      .welcome-header h1 {
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
      }

      .role-badge {
        padding: 0.6rem 1.2rem;
        font-size: 0.85rem;
      }

      .nav-action {
        padding: 1.25rem;
        gap: 1rem;
      }

      .nav-icon {
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
      }

      .nav-text h3 {
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
      }

      .nav-text p {
        font-size: 0.9rem;
      }

      .main-navigation {
        gap: 1.25rem;
      }
    }
  </style>
</head>
<body>
  <div class="dashboard-wrapper">
    <div class="welcome-header">
      <div class="user-avatar">
        <?php echo strtoupper(substr($nombre, 0, 1)); ?>
      </div>
      <h1>¡Bienvenido, <?php echo htmlspecialchars($nombre); ?>!</h1>
      <div class="role-badge">
        <i class="fas fa-<?php echo $rol === 'propietario' ? 'car' : 'user'; ?>"></i>
        <?php echo htmlspecialchars($rol); ?>
      </div>
    </div>

    <nav class="main-navigation">
      <?php if ($rol === 'propietario'): ?>
        <a href="nuevo_vehiculo.php" class="nav-action primary">
          <div class="nav-icon primary">
            <i class="fas fa-plus"></i>
          </div>
          <div class="nav-text">
            <h3>Agregar vehículo</h3>
            <p>Registra un nuevo vehículo en tu flota</p>
          </div>
        </a>
        <a href="panel_propietario.php" class="nav-action">
          <div class="nav-icon secondary">
            <i class="fas fa-car"></i>
          </div>
          <div class="nav-text">
            <h3>Mis vehículos</h3>
            <p>Gestiona tu flota y revisa las reservas</p>
          </div>
        </a>
      <?php else: ?>
        <a href="buscar_vehiculos.php" class="nav-action primary">
          <div class="nav-icon primary">
            <i class="fas fa-search"></i>
          </div>
          <div class="nav-text">
            <h3>Buscar vehículos</h3>
            <p>Encuentra el vehículo perfecto para ti</p>
          </div>
        </a>
        <a href="mis_reservas.php" class="nav-action">
          <div class="nav-icon secondary">
            <i class="fas fa-calendar-alt"></i>
          </div>
          <div class="nav-text">
            <h3>Mis reservas</h3>
            <p>Revisa tus reservas activas e historial</p>
          </div>
        </a>
      <?php endif; ?>
      
      <a href="perfil.php" class="nav-action">
        <div class="nav-icon accent">
          <i class="fas fa-user-cog"></i>
        </div>
        <div class="nav-text">
          <h3>Mi perfil</h3>
          <p>Actualiza tu información personal</p>
        </div>
      </a>
      
      <a href="logout.php" class="nav-action">
        <div class="nav-icon danger">
          <i class="fas fa-sign-out-alt"></i>
        </div>
        <div class="nav-text">
          <h3>Cerrar sesión</h3>
          <p>Finaliza tu sesión de forma segura</p>
        </div>
      </a>
    </nav>
  </div>

  <nav class="bottom-nav">
    <a href="dashboard.php" class="nav-item active">
      <i class="fas fa-home"></i>
      <span>Inicio</span>
    </a>
    <?php if ($rol === 'propietario'): ?>
      <a href="nuevo_vehiculo.php" class="nav-item">
        <i class="fas fa-plus"></i>
        <span>Agregar</span>
      </a>
      <a href="panel_propietario.php" class="nav-item">
        <i class="fas fa-car"></i>
        <span>Mis vehículos</span>
      </a>
    <?php else: ?>
      <a href="buscar_vehiculos.php" class="nav-item">
        <i class="fas fa-search"></i>
        <span>Búsqueda</span>
      </a>
      <a href="mis_reservas.php" class="nav-item">
        <i class="fas fa-calendar-alt"></i>
        <span>Reservas</span>
      </a>
    <?php endif; ?>
    <a href="perfil.php" class="nav-item">
      <i class="fas fa-user"></i>
      <span>Perfil</span>
    </a>
  </nav>
</body>
</html>