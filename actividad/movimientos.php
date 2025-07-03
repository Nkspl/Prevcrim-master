<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
require_once '../config.php';

$logs = $pdo->query("SELECT da.*, u.nombre AS autor_nombre, u.rut AS autor_rut
                      FROM delincuente_actividad da
                      LEFT JOIN usuario u ON da.autor_id = u.id
                      ORDER BY da.fecha DESC")->fetchAll();
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Actividad del Sistema</h2>
    <table>
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Acción</th>
          <th>RUT Delincuente</th>
          <th>Nombre</th>
          <th>Datos</th>
          <th>Autor</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $l): ?>
        <tr>
          <td><?= htmlspecialchars($l['fecha']) ?></td>
          <td><?= htmlspecialchars($l['accion']) ?></td>
          <td><?= htmlspecialchars($l['rut']) ?></td>
          <td><?= htmlspecialchars($l['nombre']) ?></td>
          <td><?= htmlspecialchars($l['datos']) ?></td>
          <td><?= htmlspecialchars($l['autor_nombre']) ?> (<?= htmlspecialchars($l['autor_rut']) ?>)</td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
