<?php
// jefe/gestion_sectores.php
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
    <h2>Gestión de Sectores</h2>
    <button onclick="location.href='nuevo_sector.php'">Agregar Sector</button>
    <table>
      <thead><tr><th>ID</th><th>Nombre</th><th>Código</th><th>Descripción</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php
          $st=$pdo->prepare("SELECT * FROM sector
                             WHERE institucion_id=:iid ORDER BY id");
          $st->execute(['iid'=>$iid]);
          while($s=$st->fetch()):
        ?>
        <tr>
          <td><?php echo $s['id'];?></td>
          <td><?php echo htmlspecialchars($s['nombre']);?></td>
          <td><?php echo htmlspecialchars($s['codigo']);?></td>
          <td><?php echo htmlspecialchars($s['descripcion']);?></td>
          <td>
            <button onclick="location.href='editar_sector.php?id=<?php echo $s['id'];?>'">Editar</button>
            <button onclick="if(confirm('¿Seguro?')) location.href=
              'eliminar_sector.php?id=<?php echo $s['id'];?>'">Eliminar</button>
          </td>
        </tr>
        <?php endwhile;?>
      </tbody>
    </table>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
