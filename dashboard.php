<?php
// dashboard.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'config.php';
?>
<?php include('inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
    <p>Rol: <?php echo htmlspecialchars($_SESSION['rol']); ?></p>
    <p>Utilice el menú lateral para navegar por el sistema.</p>
  </div>
  <?php include('inc/footer.php'); ?>
</div>
