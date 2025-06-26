<?php
// jefe/editar_usuario.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'jefe_zona') {
  header("Location: ../index.php");
  exit();
}
require_once '../config.php';
require_once '../inc/funciones.php';
$iid = $_SESSION['institucion_id'];
$id  = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM usuario WHERE id=:id AND institucion_id=:iid");
$stmt->execute(['id'=>$id,'iid'=>$iid]);
$u = $stmt->fetch() ?: die("No existe o no autorizado");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $rut = trim($_POST['rut']);
  if (!validarRut($rut)) { $error = "RUT inválido"; }
  else {
    $nombre = trim($_POST['nombre']);
    $rol = $_POST['rol'];
    $fh = $_POST['fecha_habilitacion'];
    $sql = "UPDATE usuario SET rut=:rut,nombre=:nom,rol=:rol,fecha_habilitacion=:fh";
    $params = ['rut'=>$rut,'nom'=>$nombre,'rol'=>$rol,'fh'=>$fh];
    if(!empty($_POST['password'])){
      $sql .= ",password=:pass";
      $params['pass'] = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    }
    $sql .= " WHERE id=:id AND institucion_id=:iid";
    $params['id']=$id; $params['iid']=$iid;
    $st=$pdo->prepare($sql);
    if($st->execute($params)) {
      header("Location: gestion_usuarios.php");
      exit();
    } else { $error="Error al actualizar"; }
  }
}
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Editar Usuario de Zona</h2>
    <?php if(!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
      <div class="form-group">
        <label for="rut">RUT:</label>
        <input id="rut" name="rut" required value="<?php echo htmlspecialchars($u['rut']); ?>">
      </div>
      <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input id="nombre" name="nombre" required value="<?php echo htmlspecialchars($u['nombre']); ?>">
      </div>
      <div class="form-group">
        <label for="password">Contraseña (dejar vacío si no cambia):</label>
        <input type="password" id="password" name="password">
      </div>
      <div class="form-group">
        <label for="rol">Rol:</label>
        <select id="rol" name="rol">
          <option value="operador" <?php if($u['rol']=='operador') echo 'selected'; ?>>Operador</option>
          <option value="jefe_zona" <?php if($u['rol']=='jefe_zona') echo 'selected'; ?>>Jefe de Zona</option>
        </select>
      </div>
      <div class="form-group">
        <label for="fecha_habilitacion">Fecha Habilitación:</label>
        <input type="date" id="fecha_habilitacion" name="fecha_habilitacion" value="<?php echo htmlspecialchars($u['fecha_habilitacion']); ?>">
      </div>
      <button type="submit">Actualizar Usuario</button>
    </form>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
