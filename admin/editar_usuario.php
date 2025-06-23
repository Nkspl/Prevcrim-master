<?php
// admin/editar_usuario.php
session_start();
// validación de rol...
require_once '../config.php';
require_once '../inc/funciones.php';
?> 
<?php include('../inc/header.php'); ?>
<div class="wrapper"><div class="content">
   
<?php
$id=intval($_GET['id']??0);
$stmt=$pdo->prepare("SELECT * FROM usuario WHERE id=:id");
$stmt->execute(['id'=>$id]);
$u=$stmt->fetch()?:die("No existe");

$instituciones=$pdo->query("SELECT id,nombre FROM institucion")->fetchAll();

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $rut=trim($_POST['rut']);
  if (!validarRut($rut)){ $error="RUT inválido"; }
  else {
    $nombre=trim($_POST['nombre']);
    $rol=$_POST['rol'];
    $inst_id=empty($_POST['institucion_id'])?null:intval($_POST['institucion_id']);
    $fh=$_POST['fecha_habilitacion'];
    $sql="UPDATE usuario SET rut=:rut,nombre=:nom,rol=:rol,
          institucion_id=:iid,fecha_habilitacion=:fh";
    $params=['rut'=>$rut,'nom'=>$nombre,
             'rol'=>$rol,'iid'=>$inst_id,'fh'=>$fh];
    if(!empty($_POST['password'])){
      $sql.=",password=:pass";
      $params['pass']=password_hash(trim($_POST['password']),PASSWORD_DEFAULT);
    }
    $sql.=" WHERE id=:id";
    $params['id']=$id;
    $st=$pdo->prepare($sql);
    if ($st->execute($params)) {
      header("Location: gestion_usuarios.php");exit();
    } else { $error="Error al actualizar"; }
  }
}
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Editar Usuario</h2>
    <?php if(!empty($error)) echo "<p class='error'>$error</p>";?>
    <form method="post">
      <div class="form-group">
        <label for="rut">RUT:</label>
        <input id="rut" name="rut" required value="<?php echo htmlspecialchars($u['rut']);?>">
      </div>
      <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input id="nombre" name="nombre" required value="<?php echo htmlspecialchars($u['nombre']);?>">
      </div>
      <div class="form-group">
        <label for="password">Contraseña (dejar vacío si no cambia):</label>
        <input type="password" id="password" name="password">
      </div>
      <div class="form-group">
        <label for="rol">Rol:</label>
        <select id="rol" name="rol">
          <option value="admin"    <?php if($u['rol']=='admin')    echo 'selected';?>>Administrador</option>
          <option value="jefe_zona"<?php if($u['rol']=='jefe_zona') echo 'selected';?>>Jefe Zona</option>
          <option value="operador" <?php if($u['rol']=='operador')  echo 'selected';?>>Operador</option>
        </select>
      </div>
      <div class="form-group">
        <label for="institucion_id">Institución (opcional):</label>
        <select id="institucion_id" name="institucion_id">
          <option value="">-- Ninguna --</option>
          <?php foreach($instituciones as $ins): ?>
            <option value="<?php echo $ins['id'];?>"
              <?php if($ins['id']==$u['institucion_id']) echo 'selected';?>>
              <?php echo htmlspecialchars($ins['nombre']);?>
            </option>
          <?php endforeach;?>
        </select>
      </div>
      <div class="form-group">
        <label for="fecha_habilitacion">Fecha Habilitación:</label>
        <input type="date" id="fecha_habilitacion" name="fecha_habilitacion"
               value="<?php echo htmlspecialchars($u['fecha_habilitacion']);?>">
      </div>
      <button type="submit">Actualizar Usuario</button>
    </form>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
