<?php
session_start();
include 'conexion.php';
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'propietario') {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Agregar Vehículo – VanLooking</title>

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

    .form-container {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
      overflow: hidden;
    }

    .form-container::before {
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

    .form-grid {
      display: grid;
      gap: 1.5rem;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    .form-group.full-width {
      grid-column: 1 / -1;
    }

    .form-group label {
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: #264653;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .form-group label i {
      color: #2A9D8F;
      width: 16px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      padding: 1rem;
      border: 2px solid #e9ecef;
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: #f8f9fa;
      color: #264653;
      font-family: 'Inter', sans-serif;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #2a9d8f;
      background: white;
      box-shadow: 0 0 0 4px rgba(42, 157, 143, 0.1);
      transform: translateY(-2px);
    }

    .form-group textarea {
      resize: vertical;
      min-height: 120px;
    }

    .form-group input[type="file"] {
      padding: 0.75rem;
      background: white;
      border-style: dashed;
      border-width: 2px;
      border-color: #2a9d8f;
      cursor: pointer;
    }

    .form-group input[type="file"]:hover {
      background: rgba(42, 157, 143, 0.05);
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
      margin-top: 2rem;
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

    .location-note {
      font-size: 0.85rem;
      color: #6b7280;
      margin-top: 0.5rem;
      font-style: italic;
      padding-left: 1rem;
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

      .form-row {
        grid-template-columns: 1fr;
      }

      .page-header {
        padding: 1rem;
        margin-bottom: 1.5rem;
      }

      .page-header h2 {
        font-size: 1.3rem;
      }

      .form-container {
        padding: 1.5rem 1rem;
        margin: 0;
        width: 100%;
      }

      .form-header {
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
      }

      .form-group input,
      .form-group select,
      .form-group textarea {
        width: 100%;
        box-sizing: border-box;
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

      .form-container {
        padding: 3rem;
      }

      .form-row {
        grid-template-columns: 1fr 1fr 1fr;
      }

      .form-row.two-cols {
        grid-template-columns: 1fr 1fr;
      }
    }

    .page-header {
      animation: slideDown 0.8s ease;
    }

    .form-container {
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
        <i class="fas fa-plus-circle"></i>
        Agregar Nuevo Vehículo
      </h2>
    </div>

    <div class="form-container">
      <div class="form-header">
        <i class="fas fa-car"></i>
        Información del vehículo
      </div>

      <form action="guardar_vehiculo.php" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
          <div class="form-group full-width">
            <label for="titulo">
              <i class="fas fa-tag"></i>
              Título del anuncio
            </label>
            <input type="text" id="titulo" name="titulo" required placeholder="Ej: Autocaravana Ford Transit espaciosa">
          </div>

          <div class="form-row two-cols">
            <div class="form-group">
              <label for="tipo">
                <i class="fas fa-car"></i>
                Tipo de vehículo
              </label>
              <select id="tipo" name="tipo" required>
                <option value="">Selecciona el tipo</option>
                <option value="autocaravana">Autocaravana</option>
                <option value="camper">Camper</option>
              </select>
            </div>

            <div class="form-group">
              <label for="precio_dia">
                <i class="fas fa-euro-sign"></i>
                Precio por día (€)
              </label>
              <input type="number" id="precio_dia" step="0.01" name="precio_dia" required placeholder="50.00">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="marca">
                <i class="fas fa-industry"></i>
                Marca
              </label>
              <input type="text" id="marca" name="marca" placeholder="Ford, Mercedes, etc.">
            </div>

            <div class="form-group">
              <label for="modelo">
                <i class="fas fa-car-side"></i>
                Modelo
              </label>
              <input type="text" id="modelo" name="modelo" placeholder="Transit, Sprinter, etc.">
            </div>

            <div class="form-group">
              <label for="ano">
                <i class="fas fa-calendar-alt"></i>
                Año
              </label>
              <input type="number" id="ano" name="ano" min="1900" max="2100" placeholder="2020">
            </div>
          </div>

          <div class="form-group full-width">
            <label for="ubicacion">
              <i class="fas fa-map-marker-alt"></i>
              Ubicación
            </label>
            <input list="ubicaciones" id="ubicacion" name="ubicacion" type="text" required placeholder="Escribe tu ciudad...">
            <datalist id="ubicaciones"></datalist>
            <div class="location-note">
              Empieza a escribir y selecciona una ubicación de la lista
            </div>
            <input type="hidden" id="lat" name="latitud">
            <input type="hidden" id="lng" name="longitud">
          </div>

          <div class="form-group full-width">
            <label for="descripcion">
              <i class="fas fa-align-left"></i>
              Descripción
            </label>
            <textarea id="descripcion" name="descripcion" rows="4" placeholder="Describe tu vehículo: características, equipamiento, capacidad..."></textarea>
          </div>

          <div class="form-group full-width">
            <label for="imagenes">
              <i class="fas fa-images"></i>
              Imágenes del vehículo
            </label>
            <input type="file" id="imagenes" name="imagenes[]" multiple accept="image/*">
          </div>
        </div>

        <button type="submit" class="submit-btn">
          <i class="fas fa-save"></i>
          Guardar Vehículo
        </button>
      </form>
    </div>
  </div>

  <nav class="bottom-nav">
    <a href="dashboard.php" class="nav-item">
      <i class="fas fa-home"></i>
      <span>Inicio</span>
    </a>
    <a href="nuevo_vehiculo.php" class="nav-item active">
      <i class="fas fa-plus"></i>
      <span>Agregar</span>
    </a>
    <a href="panel_propietario.php" class="nav-item">
      <i class="fas fa-car"></i>
      <span>Mis vehículos</span>
    </a>
    <a href="perfil.php" class="nav-item">
      <i class="fas fa-user"></i>
      <span>Perfil</span>
    </a>
  </nav>

  <script>
    const ubicInput = document.getElementById('ubicacion');
    const ubicList  = document.getElementById('ubicaciones');
    ubicInput.addEventListener('input', () => {
      const q = ubicInput.value.trim();
      if (q.length < 3) return;
      fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=5&q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(places => {
          ubicList.innerHTML = '';
          places.forEach(p => {
            const opt = document.createElement('option');
            opt.value       = p.display_name;
            opt.dataset.lat = p.lat;
            opt.dataset.lon = p.lon;
            ubicList.appendChild(opt);
          });
        });
    });
    ubicInput.addEventListener('change', () => {
      Array.from(ubicList.options).forEach(opt => {
        if (opt.value === ubicInput.value) {
          document.getElementById('lat').value = opt.dataset.lat;
          document.getElementById('lng').value = opt.dataset.lon;
        }
      });
    });
  </script>
</body>
</html>