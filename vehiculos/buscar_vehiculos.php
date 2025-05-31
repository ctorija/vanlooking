<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.html");
    exit;
}

$ubicacion    = $_GET['ubicacion']    ?? '';
$tipo         = $_GET['tipo']         ?? 'todo';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin    = $_GET['fecha_fin']    ?? '';
$precio_min   = $_GET['precio_min']   ?? '';
$precio_max   = $_GET['precio_max']   ?? '';
$orden        = $_GET['orden']        ?? 'cercania';
$radio        = $_GET['radio']        ?? '';
$lat_user     = $_GET['lat_user']     ?? null;
$lng_user     = $_GET['lng_user']     ?? null;

$resultados = [];

if (isset($_GET['buscar'])) {
    $where  = "WHERE v.ubicacion LIKE ?";
    $params = ["%{$ubicacion}%"];
    $types  = "s";

    if ($tipo !== 'todo') {
        $where    .= " AND v.tipo = ?";
        $types    .= "s";
        $params[] = $tipo;
    }

    if ($fecha_inicio && $fecha_fin) {
        $where   .= " AND v.id NOT IN (
                        SELECT vehiculo_id
                          FROM Reserva
                         WHERE estado = 'confirmada'
                           AND NOT (fecha_fin < ? OR fecha_inicio > ?)
                     )";
        $types   .= "ss";
        $params[] = $fecha_inicio;
        $params[] = $fecha_fin;
    }

    if ($precio_min !== '') {
        $where   .= " AND v.precio_dia >= ?";
        $types   .= "d";
        $params[] = $precio_min;
    }
    if ($precio_max !== '') {
        $where   .= " AND v.precio_dia <= ?";
        $types   .= "d";
        $params[] = $precio_max;
    }

    if ($orden === 'cercania' && $lat_user && $lng_user) {
        $select_dist = ",
          (6371 * ACOS(
             COS(RADIANS(?)) * COS(RADIANS(v.latitud)) *
             COS(RADIANS(v.longitud) - RADIANS(?)) +
             SIN(RADIANS(?)) * SIN(RADIANS(v.latitud))
          )) AS distancia
        ";
        array_unshift($params, $lat_user, $lng_user, $lat_user);
        $types = "ddd" . $types;

        if (in_array($radio, ['5','10','20','50','100'], true)) {
            $having_sql = "HAVING distancia <= ?";
            $types      .= "d";
            $params[]    = (float)$radio;
        } else {
            $having_sql = "";
        }

        $order_sql = "ORDER BY distancia ASC";
    } elseif ($orden === 'precio_desc') {
        $select_dist = "";
        $having_sql  = "";
        $order_sql   = "ORDER BY v.precio_dia DESC";
    } else {
        $select_dist = "";
        $having_sql  = "";
        $order_sql   = "ORDER BY v.precio_dia ASC";
    }

    $sql = "
      SELECT
        v.id,
        v.titulo,
        v.tipo,
        v.ubicacion,
        v.precio_dia
        {$select_dist},
        COALESCE(iv.url,'placeholder.jpg') AS imagen
      FROM Vehiculo v
      LEFT JOIN ImagenVehiculo iv
        ON v.id = iv.vehiculo_id
       AND iv.es_principal = 1
      {$where}
      {$having_sql}
      {$order_sql}
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $resultados[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Buscar Vehículos – VanLooking</title>

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
    }

    .search-form {
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

    .search-form::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #2A9D8F, #264653);
    }

    .search-form h3 {
      margin: 0 0 1.5rem 0;
      color: #264653;
      font-size: 1.2rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 0.75rem;
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

    .search-form label {
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: #264653;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .search-form input,
    .search-form select {
      padding: 1rem;
      border: 2px solid #e9ecef;
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: #f8f9fa;
      color: #264653;
    }

    .search-form input:focus,
    .search-form select:focus {
      outline: none;
      border-color: #2a9d8f;
      background: white;
      box-shadow: 0 0 0 4px rgba(42, 157, 143, 0.1);
      transform: translateY(-2px);
    }

    .search-btn {
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      color: white;
      padding: 1.2rem;
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
      margin-top: 1.5rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      position: relative;
      overflow: hidden;
    }

    .search-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .search-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(42, 157, 143, 0.3);
    }

    .search-btn:hover::before {
      left: 100%;
    }

    .results-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.5rem;
      padding: 0 0.5rem;
    }

    .results-count {
      color: white;
      font-size: 0.95rem;
      font-weight: 600;
      background: rgba(255, 255, 255, 0.15);
      padding: 0.5rem 1rem;
      border-radius: 20px;
      backdrop-filter: blur(10px);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .no-results {
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

    .no-results::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #F4A261, #e8944a);
    }

    .no-results i {
      font-size: 4rem;
      color: #F4A261;
      margin-bottom: 1.5rem;
    }

    .no-results h3 {
      color: #264653;
      margin-bottom: 0.75rem;
      font-size: 1.3rem;
      font-weight: 700;
    }

    .no-results p {
      color: #6b7280;
      margin: 0;
      font-size: 1rem;
      line-height: 1.5;
    }

    .cards {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: all 0.3s ease;
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
    }

    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .card img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .card:hover img {
      transform: scale(1.05);
    }

    .card-content {
      padding: 1.75rem;
    }

    .card-content h3 {
      font-size: 1.3rem;
      font-weight: 700;
      color: #264653;
      margin-bottom: 1rem;
    }

    .card-info {
      display: grid;
      gap: 0.75rem;
      margin-bottom: 1.5rem;
    }

    .card-info-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      color: #6b7280;
      font-size: 0.95rem;
      font-weight: 500;
    }

    .card-info-item i {
      width: 18px;
      color: #2A9D8F;
      font-size: 1rem;
    }

    .price-tag {
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      color: white;
      padding: 0.75rem 1.25rem;
      border-radius: 25px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1rem;
      font-size: 1rem;
      box-shadow: 0 4px 15px rgba(42, 157, 143, 0.3);
    }

    .distance-badge {
      background: linear-gradient(135deg, #F4A261 0%, #e8944a 100%);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin-left: 0.75rem;
      box-shadow: 0 4px 15px rgba(244, 162, 97, 0.3);
    }

    .card-content .btn {
      background: linear-gradient(135deg, #2a9d8f 0%, #238d7a 100%);
      color: white;
      padding: 1rem 1.5rem;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      transition: all 0.3s ease;
      width: 100%;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-size: 0.95rem;
      position: relative;
      overflow: hidden;
    }

    .card-content .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .card-content .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(42, 157, 143, 0.3);
    }

    .card-content .btn:hover::before {
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

      .search-form {
        padding: 1.5rem;
      }

      .card img {
        height: 220px;
      }

      .card-content {
        padding: 1.5rem;
      }
    }

    @media (min-width: 768px) {
      body {
        max-width: none;
        padding: 0 2rem;
      }

      .container {
        max-width: 1200px;
        padding: 2rem 0;
      }

      .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 2rem;
      }

      .search-form {
        padding: 2.5rem;
      }

      .form-row {
        grid-template-columns: 1fr 1fr 1fr;
      }
    }

    .search-form {
      animation: slideDown 0.8s ease;
    }

    .card {
      animation: fadeInUp 0.6s ease both;
    }

    .card:nth-child(1) { animation-delay: 0.1s; }
    .card:nth-child(2) { animation-delay: 0.2s; }
    .card:nth-child(3) { animation-delay: 0.3s; }
    .card:nth-child(4) { animation-delay: 0.4s; }
    .card:nth-child(5) { animation-delay: 0.5s; }

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
      <h2>Buscar Vehículos</h2>
    </div>

    <form method="GET" action="buscar_vehiculos.php" class="search-form">
      <h3>
        <i class="fas fa-filter"></i>
        Filtros de búsqueda
      </h3>
      
      <div class="form-grid">
        <div class="form-group full-width">
          <label>Ubicación:</label>
          <input type="text" name="ubicacion" value="<?php echo htmlspecialchars($ubicacion); ?>" placeholder="Ej: Madrid, Barcelona...">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Tipo de vehículo:</label>
            <select name="tipo">
              <option value="todo"         <?php if($tipo==='todo')         echo 'selected'; ?>>Todos los tipos</option>
              <option value="autocaravana" <?php if($tipo==='autocaravana') echo 'selected'; ?>>Autocaravana</option>
              <option value="camper"       <?php if($tipo==='camper')       echo 'selected'; ?>>Camper</option>
            </select>
          </div>

          <div class="form-group">
            <label>Ordenar por:</label>
            <select name="orden">
              <option value="cercania"    <?php if($orden==='cercania')   echo 'selected'; ?>>Más cercanos primero</option>
              <option value="precio_asc"  <?php if($orden==='precio_asc') echo 'selected'; ?>>Precio (menor primero)</option>
              <option value="precio_desc" <?php if($orden==='precio_desc')echo 'selected'; ?>>Precio (mayor primero)</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Fecha de inicio:</label>
            <input type="date" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
          </div>

          <div class="form-group">
            <label>Fecha de fin:</label>
            <input type="date" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Precio mínimo (€/día):</label>
            <input type="number" step="0.01" name="precio_min" value="<?php echo htmlspecialchars($precio_min); ?>" placeholder="0">
          </div>

          <div class="form-group">
            <label>Precio máximo (€/día):</label>
            <input type="number" step="0.01" name="precio_max" value="<?php echo htmlspecialchars($precio_max); ?>" placeholder="Sin límite">
          </div>
        </div>

        <div class="form-group">
          <label>Radio de búsqueda:</label>
          <select name="radio">
            <option value=""      <?php if($radio==='')      echo 'selected'; ?>>Cualquier distancia</option>
            <option value="5"     <?php if($radio==='5')     echo 'selected'; ?>>Hasta 5 km</option>
            <option value="10"    <?php if($radio==='10')    echo 'selected'; ?>>Hasta 10 km</option>
            <option value="20"    <?php if($radio==='20')    echo 'selected'; ?>>Hasta 20 km</option>
            <option value="50"    <?php if($radio==='50')    echo 'selected'; ?>>Hasta 50 km</option>
            <option value="100"   <?php if($radio==='100')   echo 'selected'; ?>>Hasta 100 km</option>
            <option value="mas"   <?php if($radio==='mas')   echo 'selected'; ?>>Más de 100 km</option>
          </select>
        </div>
      </div>

      <button type="submit" name="buscar" class="search-btn">
        <i class="fas fa-search"></i>
        Buscar vehículos
      </button>
    </form>

    <?php if (count($resultados) > 0): ?>
      <div class="results-header">
        <div class="results-count">
          <i class="fas fa-car"></i>
          <?php echo count($resultados); ?> vehículo<?php echo count($resultados) > 1 ? 's' : ''; ?> encontrado<?php echo count($resultados) > 1 ? 's' : ''; ?>
        </div>
      </div>
      
      <div class="cards">
        <?php foreach ($resultados as $v): ?>
          <div class="card">
            <img src="uploads/<?php echo htmlspecialchars($v['imagen']); ?>" alt="Foto vehículo">
            <div class="card-content">
              <h3><?php echo htmlspecialchars($v['titulo']); ?></h3>
              
              <div class="card-info">
                <div class="card-info-item">
                  <i class="fas fa-car"></i>
                  <span><?php echo ucfirst(htmlspecialchars($v['tipo'])); ?></span>
                </div>
                <div class="card-info-item">
                  <i class="fas fa-map-marker-alt"></i>
                  <span><?php echo htmlspecialchars($v['ubicacion']); ?></span>
                </div>
              </div>

              <div class="price-tag">
                <i class="fas fa-euro-sign"></i>
                <?php echo htmlspecialchars($v['precio_dia']); ?> / día
              </div>

              <?php if (isset($v['distancia'])): ?>
                <div class="distance-badge">
                  <i class="fas fa-route"></i>
                  <?php echo round($v['distancia'],1); ?> km
                </div>
              <?php endif; ?>

              <a href="detalle_vehiculo.php?id=<?php echo $v['id']; ?>" class="btn">
                <i class="fas fa-eye"></i>
                Ver detalles
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php elseif (isset($_GET['buscar'])): ?>
      <div class="no-results">
        <i class="fas fa-search"></i>
        <h3>No se encontraron vehículos</h3>
        <p>Prueba a modificar los filtros de búsqueda para obtener más resultados.</p>
      </div>
    <?php endif; ?>
  </div>

  <script>
    if (navigator.geolocation) {
      const params = new URLSearchParams(window.location.search);
      if (!params.has('lat_user') || !params.has('lng_user')) {
        navigator.geolocation.getCurrentPosition(pos => {
          params.set('lat_user', pos.coords.latitude.toFixed(6));
          params.set('lng_user', pos.coords.longitude.toFixed(6));
          params.set('buscar','1');
          window.location.search = params.toString();
        });
      }
    }
  </script>

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