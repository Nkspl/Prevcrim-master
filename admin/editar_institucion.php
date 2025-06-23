<?php
// admin/editar_institucion.php
session_start();
if (!isset($_SESSION['user_id'])||$_SESSION['rol']!=='admin') {
  header("Location: ../index.php"); exit();
}
require_once '../config.php';
$id = intval($_GET['id']??0);
$stmt=$pdo->prepare("SELECT * FROM institucion WHERE id=:id");
$stmt->execute(['id'=>$id]);
$inst=$stmt->fetch()?:die("No existe");
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $n=trim($_POST['nombre']);
  $c=trim($_POST['codigo']);
  $s=intval($_POST['num_sectores']);
  $u=$pdo->prepare("
    UPDATE institucion
    SET nombre=:n,codigo=:c,num_sectores=:s
    WHERE id=:id
  ");
  if ($u->execute(['n'=>$n,'c'=>$c,'s'=>$s,'id'=>$id])) {
    header("Location: gestion_instituciones.php");exit();
  } else { $error="Error al actualizar"; }
}
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Editar Institución</h2>
    <?php if(!empty($error)) echo "<p class='error'>$error</p>";?>
    <form method="post">
      <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input id="nombre" name="nombre" required value="<?php echo htmlspecialchars($inst['nombre']);?>">
      </div>
      <div class="form-group">
        <label for="codigo">Código:</label>
        <input id="codigo" name="codigo" required value="<?php echo htmlspecialchars($inst['codigo']);?>">
      </div>
      <div class="form-group">
        <label for="num_sectores">N° Sectores:</label>
        <input type="number" id="num_sectores" name="num_sectores" required
               value="<?php echo $inst['num_sectores'];?>">
      </div>
      <button type="submit">Actualizar Institución</button>
    </form>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
