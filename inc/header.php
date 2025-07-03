<?php
// inc/header.php
// NO session_start() aquí; ya lo haces al inicio de cada página
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>SIPC – Sistema Integrado de Prevención de Crímenes</title>

  <!-- RUTAS ABSOLUTAS: siempre arrancan en la raíz -->
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="/js/script.js" defer></script>
</head>
<body>
<header>
  <div class="header-left">
    <button id="btnToggleSidebar">
      <i class="fa-solid fa-bars"></i>
    </button>
    <!-- HOME (inicio) / Dashboard -->
    <a href="/dashboard.php" class="home-btn">PREVCRIM</a>
  </div>
  <div class="user-info">
    <?php if (!empty($_SESSION['nombre'])): ?>
      <!-- mostrar el nombre del usuario que inicio sesion en el encabezado-->
      <?php echo htmlspecialchars($_SESSION['nombre']); ?> 

      <a href="/logout.php" class="logout-btn">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span class="logout-text">Cerrar Sesión</span>
      </a>
    <?php endif; ?>
  </div>
</header>

<div id="sidebar">
  <!-- Botones con rutas absolutas -->
  <button onclick="location.href='/dashboard.php';">
    <i class="fa-solid fa-house"></i> Inicio
  </button>
  <?php if (!empty($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
    <button onclick="location.href='/admin/gestion_instituciones.php';">
      <i class="fa-solid fa-building"></i> Instituciones
    </button>
    <button onclick="location.href='/admin/gestion_usuarios.php';">
      <i class="fa-solid fa-users"></i> Usuarios
    </button>
  <?php endif; ?>
  <?php if (!empty($_SESSION['rol']) && $_SESSION['rol'] === 'jefe_zona'): ?>
    <button onclick="location.href='/jefe/gestion_usuarios.php';">
      <i class="fa-solid fa-users"></i> Usuarios de Zona
    </button>
    <button onclick="location.href='/jefe/gestion_sectores.php';">
      <i class="fa-solid fa-map"></i> Sectores
    </button>
  <?php endif; ?>
  <?php if (!empty($_SESSION['rol']) && $_SESSION['rol'] === 'operador'): ?>
    <button onclick="location.href='/operador/registro_delincuente.php';">
      <i class="fa-solid fa-user-plus"></i> Registrar Delincuente
    </button>
    <button onclick="location.href='/operador/listado_delincuentes.php';">
      <i class="fa-solid fa-user-group"></i> Delincuentes
    </button>
    <button onclick="location.href='/operador/registro_control.php';">
      <i class="fa-solid fa-clipboard"></i> Registrar Control
    </button>
    <button onclick="location.href='/operador/ver_controles.php';">
      <i class="fa-solid fa-eye"></i> Ver Controles
    </button>
    <button onclick="location.href='/operador/registro_delito.php';">
      <i class="fa-solid fa-gavel"></i> Registrar Delito
    </button>
  <?php endif; ?>
  <button onclick="location.href='/operador/historial_delincuente.php';">
    <i class="fa-solid fa-book"></i> Historial
  </button>
  <button onclick="location.href='/reportes.php';">
    <i class="fa-solid fa-chart-simple"></i> Reportes
  </button>
  <?php if (!empty($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
    <button onclick="location.href='/actividad/usuarios.php';">
      <i class="fa-solid fa-list"></i> Actividad Usuarios
    </button>
    <button onclick="location.href='/actividad/movimientos.php';">
      <i class="fa-solid fa-clock-rotate-left"></i> Actividad Sistema
    </button>
  <?php endif; ?>
  <button onclick="location.href='/mapa_delincuentes.php';">
    <i class="fa-solid fa-map-location-dot"></i> Mapa
  </button>
</div>