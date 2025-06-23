<?php
// buscar.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'config.php';

$query = trim($_GET['q'] ?? '');
$results = [];
if ($query !== '') {
    $sql = "SELECT rut, apellidos_nombres, delitos
            FROM delincuente
            WHERE apellidos_nombres LIKE :q1
               OR delitos            LIKE :q2
               OR domicilio          LIKE :q3
               OR ultimo_lugar_visto LIKE :q4";
    $stmt = $pdo->prepare($sql);
    $p = "%$query%";
    $stmt->execute(['q1'=>$p,'q2'=>$p,'q3'=>$p,'q4'=>$p]);
    $results = $stmt->fetchAll();
}
?>
<?php include('inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Resultados para "<?php echo htmlspecialchars($query); ?>"</h2>
    <?php if ($results): ?>
      <table>
        <thead><tr><th>RUT</th><th>Nombre</th><th>Delitos</th></tr></thead>
        <tbody>
          <?php foreach ($results as $r): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['rut']); ?></td>
            <td><?php echo htmlspecialchars($r['apellidos_nombres']); ?></td>
            <td><?php echo htmlspecialchars($r['delitos']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No se encontraron resultados.</p>
    <?php endif; ?>
  </div>
  <?php include('inc/footer.php'); ?>
</div>
