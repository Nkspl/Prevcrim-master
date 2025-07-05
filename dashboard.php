<?php
// dashboard.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'config.php';

$cards = [];
if ($_SESSION['rol'] === 'admin') {
    $cards[] = ['admin/gestion_instituciones.php', 'fa-building', 'Instituciones'];
    $cards[] = ['admin/gestion_usuarios.php', 'fa-users', 'Usuarios'];
}
if ($_SESSION['rol'] === 'jefe_zona') {
    $cards[] = ['jefe/gestion_usuarios.php', 'fa-users', 'Usuarios de Zona'];
    $cards[] = ['jefe/gestion_sectores.php', 'fa-map', 'Sectores'];
}
if ($_SESSION['rol'] === 'operador') {
    $cards[] = ['operador/registro_delincuente.php', 'fa-user-plus', 'Registrar Delincuente'];
    $cards[] = ['operador/listado_delincuentes.php', 'fa-user-group', 'Delincuentes'];
}
$cards[] = ['mapa_delincuentes.php', 'fa-map-location-dot', 'Mapa'];
$cards[] = ['reportes.php', 'fa-chart-simple', 'Reportes'];
?>
<?php include('inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
    <div class="dashboard-cards">
      <?php foreach ($cards as $card): ?>
        <a class="dashboard-card" href="/<?php echo $card[0]; ?>">
          <i class="fa-solid <?php echo $card[1]; ?>"></i>
          <span><?php echo $card[2]; ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php include('inc/footer.php'); ?>
</div>
