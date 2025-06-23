<?php
// jefe/editar_sector.php
session_start();
if (!isset($_SESSION['user_id'])||$_SESSION['rol']!=='jefe_zona'){
  header("Location: ../index.php");exit();
}
require_once '../config.php';
$iid=$_SESSION['institucion_id'];
$id=intval($_GET['id']??0);
$stmt=$pdo->prepare("
  SELECT * FROM sector
  WHERE id=:id AND institucion_id=:iid
");
$stmt->execute(['id'=>$id,'iid'=>$iid]);
$s=$stmt->fetch()?:die("No existe o no autorizado");

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $n=trim($_POST['nombre']);
  $c=trim($_POST['codigo']);
  $d=trim($_POST['descripcion']);
  $u=$pdo->prepare("
    UPDATE sector
    SET nombre=:n,codigo=:c,descripcion=:d
    WHERE id=:id AND institucion_id=:iid
  ");
  if ($u->execute(['n'=>$n,'c'=>$c,'d'=>$d,'id'=>$id,'iid'=>$iid])) {
    header("Location: gestion_sectores.php");exit();
  } else { $error="Error al actualizar"; }
}
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Editar Sector</h2>
    <?php if(!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
      <div class="form-group">
        <label for="nombre">Nombre del Sector:</label>
        <input id="nombre" name="nombre" required
               value="<?php echo htmlspecialchars($s['nombre']);?>">
      </div>
      <div class="form-group">
        <label for="codigo">Código:</label>
        <input id="codigo" name="codigo" required
               value="<?php echo htmlspecialchars($s['codigo']);?>">
      </div>
      <div class="form-group">
        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($s['descripcion']);?></textarea>
      </div>
      <button type="submit">Actualizar Sector</button>
    </form>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
