<?php
// jefe/nuevo_sector.php
session_start();
if (!isset($_SESSION['user_id'])||$_SESSION['rol']!=='jefe_zona'){
  header("Location: ../index.php");exit();
}
require_once '../config.php';
$iid=$_SESSION['institucion_id'];
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $n=trim($_POST['nombre']);
  $c=trim($_POST['codigo']);
  $d=trim($_POST['descripcion']);
  $sql="INSERT INTO sector(institucion_id,nombre,codigo,descripcion)
        VALUES(:iid,:n,:c,:d)";
  $st=$pdo->prepare($sql);
  if ($st->execute(['iid'=>$iid,'n'=>$n,'c'=>$c,'d'=>$d])) {
    header("Location: gestion_sectores.php");exit();
  } else { $error="Error al insertar"; }
}
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Agregar Nuevo Sector</h2>
    <?php if(!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
      <div class="form-group">
        <label for="nombre">Nombre del Sector:</label>
        <input id="nombre" name="nombre" required>
      </div>
      <div class="form-group">
        <label for="codigo">Código:</label>
        <input id="codigo" name="codigo" required>
      </div>
      <div class="form-group">
        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion"></textarea>
      </div>
      <button type="submit">Agregar Sector</button>
    </form>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
