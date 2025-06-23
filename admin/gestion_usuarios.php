<?php
// admin/gestion_usuarios.php
session_start();
if (!isset($_SESSION['user_id'])||$_SESSION['rol']!=='admin'){
  header("Location: ../index.php");exit();
}
require_once '../config.php';
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Gestión de Usuarios</h2>
    <button onclick="location.href='nuevo_usuario.php'">Agregar Nuevo Usuario</button>
    <table>
      <thead>
        <tr><th>ID</th><th>RUT</th><th>Nombre</th><th>Rol</th>
            <th>Institución</th><th>F. Habilitación</th><th>Acciones</th></tr>
      </thead>
      <tbody>
        <?php
          $sql="SELECT u.*,i.nombre AS inst_nom 
                FROM usuario u
                LEFT JOIN institucion i ON u.institucion_id=i.id
                ORDER BY u.id";
          foreach($pdo->query($sql) as $u):
        ?>
        <tr>
          <td><?php echo $u['id'];?></td>
          <td><?php echo htmlspecialchars($u['rut']);?></td>
          <td><?php echo htmlspecialchars($u['nombre']);?></td>
          <td><?php echo $u['rol'];?></td>
          <td><?php echo htmlspecialchars($u['inst_nom']);?></td>
          <td><?php echo $u['fecha_habilitacion'];?></td>
          <td>
            <button onclick="location.href='editar_usuario.php?id=<?php echo $u['id'];?>'">
              Editar
            </button>
            <button onclick="if(confirm('¿Seguro?')) location.href=
              'eliminar_usuario.php?id=<?php echo $u['id'];?>'">
              Eliminar
            </button>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
