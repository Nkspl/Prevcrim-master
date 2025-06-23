<?php
// admin/nueva_institucion.php
session_start();
if (!isset($_SESSION['user_id'])||$_SESSION['rol']!=='admin') {
  header("Location: ../index.php"); exit();
}
require_once '../config.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $n=trim($_POST['nombre']);
  $c=trim($_POST['codigo']);
  $s=intval($_POST['num_sectores']);
  $q="INSERT INTO institucion(nombre,codigo,num_sectores)
      VALUES(:n,:c,:s)";
  $st=$pdo->prepare($q);
  if ($st->execute(['n'=>$n,'c'=>$c,'s'=>$s])) {
    header("Location: gestion_instituciones.php"); exit();
  } else { $error="Error al insertar."; }
}
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Agregar Nueva Institución</h2>
    <?php if(!empty($error)) echo "<p class='error'>$error</p>";?>
    <form method="post">
      <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input id="nombre" name="nombre" required>
      </div>
      <div class="form-group">
        <label for="codigo">Código:</label>
        <input id="codigo" name="codigo" required>
      </div>
      <div class="form-group">
        <label for="num_sectores">N° Sectores:</label>
        <input type="number" id="num_sectores" name="num_sectores" required>
      </div>
      <button type="submit">Agregar Institución</button>
    </form>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
