<?php
// reportes.php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}
require_once 'config.php';
$stmt = $pdo->query("SELECT rut, apellidos_nombres, estado FROM delincuente ORDER BY apellidos_nombres ASC");
$delincuentes = $stmt->fetchAll();
?>
<?php include('inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Listado de Delincuentes (Alfabético)</h2>
    <h3 id="sub_tit_ing_list_del">Búsqueda</h3>

    <form class="contenidooo" method="get" action="buscar.php">
      <div>

              <input id="ing_list_del" type="text" name="q" placeholder="Buscar..." required>


              <button id="btn_ing_list_del" type="submit">Buscar</button>

      </div>
    </form>


    <table>
      <thead>
        <tr>
          <th>RUT</th>
          <th>Nombre</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($delincuentes as $d): ?>
          <tr>
            <td><?php echo htmlspecialchars($d['rut']); ?></td>
            <td><?php echo htmlspecialchars($d['apellidos_nombres']); ?></td>
            <td><?php echo htmlspecialchars($d['estado']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  </div>
  <?php include('inc/footer.php'); ?>
</div>