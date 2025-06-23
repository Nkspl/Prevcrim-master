<?php
// jefe/gestion_usuarios.php
session_start();
if (!isset($_SESSION['user_id'])||$_SESSION['rol']!=='jefe_zona'){
  header("Location: ../index.php");exit();
}
require_once '../config.php';
$iid=$_SESSION['institucion_id'];
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Usuarios de Zona</h2>
    <button onclick="location.href='nuevo_usuario.php'">Agregar Usuario Zona</button>
    <table>
      <thead><tr><th>ID</th><th>RUT</th><th>Nombre</th><th>Rol</th><th>F. Habilitación</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php
          $st=$pdo->prepare("SELECT * FROM usuario
                             WHERE institucion_id=:iid ORDER BY id");
          $st->execute(['iid'=>$iid]);
          while($u=$st->fetch()):
        ?>
        <tr>
          <td><?php echo $u['id'];?></td>
          <td><?php echo htmlspecialchars($u['rut']);?></td>
          <td><?php echo htmlspecialchars($u['nombre']);?></td>
          <td><?php echo $u['rol'];?></td>
          <td><?php echo $u['fecha_habilitacion'];?></td>
          <td>
            <button onclick="location.href='editar_usuario.php?id=<?php echo $u['id'];?>'">Editar</button>
            <button onclick="if(confirm('¿Seguro?')) location.href=
              'eliminar_usuario.php?id=<?php echo $u['id'];?>'">Eliminar</button>
          </td>
        </tr>
        <?php endwhile;?>
      </tbody>
    </table>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
