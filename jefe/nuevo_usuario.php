<?php
// jefe/nuevo_usuario.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'jefe_zona') {
  header("Location: ../index.php");
  exit();
}
require_once '../config.php';
require_once '../inc/funciones.php';
$iid = $_SESSION['institucion_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $rut = trim($_POST['rut']);
  if (!validarRut($rut)) {
    $error = "RUT inválido";
  } else {
    // Verificar que no exista el usuario
    $chk = $pdo->prepare("SELECT id FROM usuario WHERE rut=:rut LIMIT 1");
    $chk->execute(['rut'=>$rut]);
    if ($chk->fetch()) {
      $error = "RUT ya registrado";
    } else {
      $nombre = trim($_POST['nombre']);
      $rol = $_POST['rol'];
      $fh = $_POST['fecha_habilitacion'];
      $pass = trim($_POST['password']);
      $hash = password_hash($pass, PASSWORD_DEFAULT);
      $sql = "INSERT INTO usuario(rut,nombre,password,rol,institucion_id,fecha_habilitacion)
              VALUES(:rut,:n,:pass,:rol,:iid,:fh)";
      $st = $pdo->prepare($sql);
      if ($st->execute(['rut'=>$rut,'n'=>$nombre,'pass'=>$hash,'rol'=>$rol,'iid'=>$iid,'fh'=>$fh])) {
        header("Location: gestion_usuarios.php");
        exit();
      } else {
        $error = "Error al crear usuario";
      }
    }
  }
}
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Agregar Usuario de Zona</h2>
    <?php if(!empty($error)) echo "<p class='error'>$error</p>"; ?>
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
          <option value="operador">Operador</option>
          <option value="jefe_zona">Jefe de Zona</option>
        </select>
      </div>
      <div class="form-group">
        <label for="fecha_habilitacion">Fecha Habilitación:</label>
        <input type="date" id="fecha_habilitacion" name="fecha_habilitacion" value="<?php echo date('Y-m-d'); ?>" required>
      </div>
      <button type="submit">Crear Usuario</button>
    </form>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
