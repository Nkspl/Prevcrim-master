<?php
// admin/gestion_instituciones.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
require_once '../config.php';
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Gestión de Instituciones</h2>
    <p><button onclick="location.href='nueva_institucion.php'">Agregar Nueva Institución</button></p>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Código</th>
          <th>N° Sectores</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT * FROM institucion ORDER BY id ASC";
        foreach ($pdo->query($sql) as $institucion) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($institucion['id']) . "</td>";
            echo "<td>" . htmlspecialchars($institucion['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($institucion['codigo']) . "</td>";
            echo "<td>" . htmlspecialchars($institucion['num_sectores']) . "</td>";
            echo "<td>
                    <button onclick=\"location.href='editar_institucion.php?id=" . $institucion['id'] . "'\">Editar</button>
                    <button onclick=\"if(confirm('¿Estás seguro de eliminar esta institución?')) location.href='eliminar_institucion.php?id=" . $institucion['id'] . "'\">Eliminar</button>
                  </td>";
            echo "</tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
