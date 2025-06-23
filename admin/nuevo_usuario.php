<?php
// admin/nuevo_usuario.php
session_start();
if (!isset($_SESSION['user_id'])||$_SESSION['rol']!=='admin'){
  header("Location: ../index.php");exit();
}
require_once '../config.php';
require_once '../inc/funciones.php';

$instituciones = $pdo->query("SELECT id,nombre FROM institucion")->fetchAll();
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $rut=trim($_POST['rut']);
  if (!validarRut($rut)){ $error="RUT inválido"; }
  else {
    $nombre=trim($_POST['nombre']);
    $rol=$_POST['rol'];
    $inst_id=empty($_POST['institucion_id'])?null:intval($_POST['institucion_id']);
    $fh=$_POST['fecha_habilitacion'];
    $pas=trim($_POST['password']);
    $hash=password_hash($pas,PASSWORD_DEFAULT);
    $sql="INSERT INTO usuario(rut,nombre,password,rol, institucion_id,fecha_habilitacion)
          VALUES(:rut,:nombre,:pass,:rol,:iid,:fh)";
    $st=$pdo->prepare($sql);
    if ($st->execute([
      'rut'=>$rut,'nombre'=>$nombre,'pass'=>$hash,
      'rol'=>$rol,'iid'=>$inst_id,'fh'=>$fh
    ])) {
      header("Location: gestion_usuarios.php");exit();
    } else { $error="Error al crear usuario"; }
  }
}
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Agregar Nuevo Usuario</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
      <div class="form-group">
        <label for="rut">RUT:</label>
        <input id="rut" name="rut" required>
      </div>
      <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input id="nombre" name="nombre" required>
      </div>
      <div class="form-group">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <label for="rol">Rol:</label>
        <select id="rol" name="rol">
          <option value="admin">Administrador</option>
          <option value="jefe_zona">Jefe de Zona</option>
          <option value="operador">Operador</option>
        </select>
      </div>
      <div class="form-group">
        <label for="institucion_id">Institución (opcional):</label>
        <select id="institucion_id" name="institucion_id">
          <option value="">-- Ninguna --</option>
          <?php foreach($instituciones as $ins): ?>
            <option value="<?php echo $ins['id'];?>">
              <?php echo htmlspecialchars($ins['nombre']);?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="fecha_habilitacion">Fecha Habilitación:</label>
        <input type="date" id="fecha_habilitacion"
               name="fecha_habilitacion"
               value="<?php echo date('Y-m-d');?>" required>
      </div>
      <button type="submit">Crear Usuario</button>
    </form>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
