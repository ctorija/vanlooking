<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje = "";
$tipo_mensaje = "";

$stmt = $conn->prepare("SELECT nombre, email FROM Usuario WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre    = trim($_POST['nombre']);
    $nuevo_email     = trim($_POST['email']);
    $password_plano  = $_POST['password'];

    if ($nuevo_email !== $usuario['email']) {
        $verif = $conn->prepare("SELECT id FROM Usuario WHERE email = ? AND id != ?");
        $verif->bind_param("si", $nuevo_email, $usuario_id);
        $verif->execute();
        $verif->store_result();
        if ($verif->num_rows > 0) {
            $mensaje = "El email ya está en uso por otro usuario.";
            $tipo_mensaje = "error";
            $verif->close();
        } else {
            $verif->close();
            if (!empty($password_plano)) {
                $password_hash = password_hash($password_plano, PASSWORD_DEFAULT);
                $sql = "UPDATE Usuario SET nombre = ?, email = ?, password = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $nuevo_nombre, $nuevo_email, $password_hash, $usuario_id);
            } else {
                $sql = "UPDATE Usuario SET nombre = ?, email = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $nuevo_nombre, $nuevo_email, $usuario_id);
            }
            if ($stmt->execute()) {
                $mensaje = "Datos de perfil actualizados correctamente.";
                $tipo_mensaje = "success";
                $_SESSION['nombre']      = $nuevo_nombre;
                $usuario['nombre']       = $nuevo_nombre;
                $usuario['email']        = $nuevo_email;
            } else {
                $mensaje = "Error al actualizar perfil: " . $stmt->error;
                $tipo_mensaje = "error";
            }
            $stmt->close();
        }
    } else {
        if (!empty($password_plano)) {
            $password_hash = password_hash($password_plano, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE Usuario SET nombre = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nuevo_nombre, $password_hash, $usuario_id);
            if ($stmt->execute()) {
                $mensaje = "Datos de perfil actualizados correctamente.";
                $tipo_mensaje = "success";
                $_SESSION['nombre'] = $nuevo_nombre;
                $usuario['nombre']  = $nuevo_nombre;
            } else {
                $mensaje = "Error al actualizar perfil: " . $stmt->error;
                $tipo_mensaje = "error";
            }
            $stmt->close();
        } else {
            if ($nuevo_nombre !== $usuario['nombre']) {
                $stmt = $conn->prepare("UPDATE Usuario SET nombre = ? WHERE id = ?");
                $stmt->bind_param("si", $nuevo_nombre, $usuario_id);
                if ($stmt->execute()) {
                    $mensaje = "Nombre actualizado correctamente.";
                    $tipo_mensaje = "success";
                    $_SESSION['nombre'] = $nuevo_nombre;
                    $usuario['nombre']  = $nuevo_nombre;
                } else {
                    $mensaje = "Error al actualizar nombre: " . $stmt->error;
                    $tipo_mensaje = "error";
                }
                $stmt->close();
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mi Perfil – VanLooking</title>

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

    .profile-avatar {
      background: white;
      border-radius: 20px;
      padding: 2.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
      text-align: center;
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
      overflow: hidden;
    }

    .profile-avatar::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #2A9D8F, #264653);
    }

    .avatar-circle {
      width: 120px;
      height: 120px;
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 3rem;
      font-weight: 700;
      margin: 0 auto 1.5rem;
      box-shadow: 0 10px 30px rgba(42, 157, 143, 0.3);
      position: relative;
      overflow: hidden;
    }

    .avatar-circle::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .avatar-circle:hover::before {
      left: 100%;
    }

    .profile-name {
      font-size: 1.5rem;
      font-weight: 700;
      color: #264653;
      margin-bottom: 1rem;
    }

    .profile-role {
      background: linear-gradient(135deg, #264653 0%, #1e3a40 100%);
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

    .profile-info {
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

    .profile-info::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #F4A261, #e8944a);
    }

    .info-header {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1.5rem;
      color: #264653;
      font-size: 1.2rem;
      font-weight: 700;
    }

    .info-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem;
      background: #f8f9fa;
      border-radius: 12px;
      margin-bottom: 1rem;
      border-left: 4px solid #F4A261;
    }

    .info-item:last-child {
      margin-bottom: 0;
    }

    .info-label {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      color: #6b7280;
      font-size: 0.95rem;
      font-weight: 500;
    }

    .info-label i {
      color: #F4A261;
      width: 18px;
      font-size: 1rem;
    }

    .info-value {
      font-weight: 700;
      color: #264653;
      font-size: 1rem;
    }

    .message {
      padding: 1.5rem;
      border-radius: 16px;
      margin-bottom: 2rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      font-weight: 600;
      animation: slideIn 0.5s ease;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .message.success {
      background: white;
      border-left: 4px solid #2A9D8F;
      color: #264653;
    }

    .message.error {
      background: white;
      border-left: 4px solid #E76F51;
      color: #264653;
    }

    .message i {
      font-size: 1.3rem;
    }

    .message.success i {
      color: #2A9D8F;
    }

    .message.error i {
      color: #E76F51;
    }

    .profile-form {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
      overflow: hidden;
    }

    .profile-form::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #2A9D8F, #264653);
    }

    .form-header {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 2rem;
      color: #264653;
      font-size: 1.3rem;
      font-weight: 700;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .profile-form label {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: #264653;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .profile-form label i {
      color: #2A9D8F;
      width: 18px;
      font-size: 1rem;
    }

    .profile-form input {
      width: 100%;
      padding: 1rem;
      border: 2px solid #e9ecef;
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: #f8f9fa;
      color: #264653;
      font-family: 'Inter', sans-serif;
    }

    .profile-form input:focus {
      outline: none;
      border-color: #2a9d8f;
      background: white;
      box-shadow: 0 0 0 4px rgba(42, 157, 143, 0.1);
      transform: translateY(-2px);
    }

    .password-note {
      font-size: 0.85rem;
      color: #6b7280;
      margin-top: 0.5rem;
      font-style: italic;
      padding-left: 1rem;
    }

    .save-btn {
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
      margin-top: 1.5rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      position: relative;
      overflow: hidden;
    }

    .save-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .save-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(42, 157, 143, 0.3);
    }

    .save-btn:hover::before {
      left: 100%;
    }

    .save-btn:active {
      transform: translateY(-1px);
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

      .avatar-circle {
        width: 100px;
        height: 100px;
        font-size: 2.5rem;
        margin-bottom: 1rem;
      }

      .profile-name {
        font-size: 1.3rem;
      }

      .profile-form,
      .profile-info,
      .profile-avatar {
        padding: 1.5rem;
      }

      .page-header {
        padding: 1rem;
        margin-bottom: 1.5rem;
      }

      .page-header h2 {
        font-size: 1.3rem;
      }
    }

    @media (min-width: 768px) {
      body {
        max-width: none;
        padding: 0 2rem;
      }

      .container {
        max-width: 800px;
        padding: 2rem 0;
      }

      .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        align-items: start;
      }

      .profile-avatar {
        grid-column: 1 / -1;
      }
    }

    .page-header {
      animation: slideDown 0.8s ease;
    }

    .profile-avatar {
      animation: slideDown 0.8s ease 0.1s both;
    }

    .profile-info {
      animation: fadeInUp 0.6s ease 0.2s both;
    }

    .profile-form {
      animation: fadeInUp 0.6s ease 0.3s both;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateX(-30px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
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
        <i class="fas fa-user-cog"></i>
        Mi Perfil
      </h2>
    </div>

    <div class="profile-avatar">
      <div class="avatar-circle">
        <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
      </div>
      <div class="profile-name"><?php echo htmlspecialchars($usuario['nombre']); ?></div>
      <div class="profile-role">
        <i class="fas fa-<?php echo $_SESSION['rol'] === 'propietario' ? 'car' : 'user'; ?>"></i>
        <?php echo htmlspecialchars($_SESSION['rol']); ?>
      </div>
    </div>

    <?php if ($mensaje): ?>
      <div class="message <?php echo $tipo_mensaje; ?>">
        <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
        <?php echo htmlspecialchars($mensaje); ?>
      </div>
    <?php endif; ?>

    <div class="content-grid">
      <div class="profile-info">
        <div class="info-header">
          <i class="fas fa-info-circle"></i>
          Información de la cuenta
        </div>
        <div class="info-item">
          <div class="info-label">
            <i class="fas fa-envelope"></i>
            Email actual
          </div>
          <div class="info-value"><?php echo htmlspecialchars($usuario['email']); ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">
            <i class="fas fa-calendar-alt"></i>
            Tipo de cuenta
          </div>
          <div class="info-value"><?php echo ucfirst($_SESSION['rol']); ?></div>
        </div>
      </div>

      <form action="perfil.php" method="POST" class="profile-form">
        <div class="form-header">
          <i class="fas fa-edit"></i>
          Editar información
        </div>

        <div class="form-group">
          <label for="nombre">
            <i class="fas fa-user"></i>
            Nombre completo
          </label>
          <input
            type="text"
            id="nombre"
            name="nombre"
            value="<?php echo htmlspecialchars($usuario['nombre']); ?>"
            required
            placeholder="Tu nombre completo">
        </div>

        <div class="form-group">
          <label for="email">
            <i class="fas fa-envelope"></i>
            Dirección de email
          </label>
          <input
            type="email"
            id="email"
            name="email"
            value="<?php echo htmlspecialchars($usuario['email']); ?>"
            required
            placeholder="tu@email.com">
        </div>

        <div class="form-group">
          <label for="password">
            <i class="fas fa-lock"></i>
            Nueva contraseña
          </label>
          <input 
            type="password" 
            id="password" 
            name="password"
            placeholder="Dejar vacío para mantener la actual">
          <div class="password-note">
            Solo completa este campo si deseas cambiar tu contraseña
          </div>
        </div>

        <button type="submit" class="save-btn">
          <i class="fas fa-save"></i>
          Guardar cambios
        </button>
      </form>
    </div>
  </div>

  <nav class="bottom-nav">
    <a href="dashboard.php" class="nav-item">
      <i class="fas fa-home"></i>
      <span>Inicio</span>
    </a>
    <?php if ($_SESSION['rol'] === 'propietario'): ?>
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
    <a href="perfil.php" class="nav-item active">
      <i class="fas fa-user"></i>
      <span>Perfil</span>
    </a>
  </nav>
</body>
</html>