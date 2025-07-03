<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'operador') {
  header('Location: /login.php');
  exit;
}

require_once __DIR__ . '/../config.php';

// Capturar el término de búsqueda
$buscar = $_GET['buscar'] ?? '';

if ($buscar !== '') {
  $sql = "SELECT id, rut, apellidos_nombres, apodo, domicilio, ultimo_lugar_visto, fono_fijo, celular, email, fecha_nacimiento, delitos, estado, imagen
          FROM delincuente
          WHERE apellidos_nombres LIKE :buscar1
             OR apodo LIKE :buscar2
             OR delitos LIKE :buscar3
          ORDER BY id DESC";
  $stmt = $pdo->prepare($sql);
  $param = "%$buscar%";
  $stmt->execute([
    'buscar1' => $param,
    'buscar2' => $param,
    'buscar3' => $param,
  ]);
} else {
  $sql = "SELECT id, rut, apellidos_nombres, apodo, domicilio, ultimo_lugar_visto, fono_fijo, celular, email, fecha_nacimiento, delitos, estado, imagen
          FROM delincuente
          ORDER BY id DESC";
  $stmt = $pdo->query($sql);
}

$delincuentes = $stmt->fetchAll();
?>

<?php include __DIR__ . '/../inc/header.php'; ?>

<main style="padding-left: 16%; padding-right: 5%;">
  <h1>Listado de Delincuentes</h1>

  <?php if (isset($_GET['msg'])): ?>
    <p style="color:green; background-color:#e6ffe6; padding:10px; border-radius:5px;">
      <?= htmlspecialchars($_GET['msg']) ?>
    </p>
  <?php endif; ?>

  <form method="GET" action="" style="padding-top: 87px;">
    <input
      type="text"
      name="buscar"
      placeholder="Buscar por nombre, apodo o delito"
      value="<?= htmlspecialchars($buscar) ?>">
    <button type="submit">Filtrar</button>
    <button type="button" onclick="window.location.href='<?= $_SERVER['PHP_SELF'] ?>'">Mostrar todos</button>
  </form>

  <div class="cards-container">
    <?php if (!empty($delincuentes)): ?>
      <?php foreach ($delincuentes as $row): ?>
        <div class="delincuente-card">
          <?php $img = $row['imagen'] ? '/' . htmlspecialchars($row['imagen']) : '/img/libre.png'; ?>
          <img src="<?= $img ?>" alt="Foto de <?= htmlspecialchars($row['apellidos_nombres']) ?>">
          <p><strong>ID:</strong> <?= htmlspecialchars($row['id']) ?></p>
          <p><strong>RUT:</strong> <?= htmlspecialchars($row['rut']) ?></p>
          <p><strong>Nombre Completo:</strong> <?= htmlspecialchars($row['apellidos_nombres']) ?></p>
          <p><strong>Apodo:</strong> <?= htmlspecialchars($row['apodo']) ?></p>
          <p><strong>Último Lugar Visto:</strong> <?= htmlspecialchars($row['ultimo_lugar_visto']) ?></p>
          <?php
            $count = 0;
            if (!empty($row['delitos'])) {
              $count = count(array_filter(array_map('trim', explode(',', $row['delitos']))));
            }
          ?>
          <p><strong>Delitos:</strong> <?= $count > 0 ? $count : 'sin registros aun' ?></p>
          <p><strong>Estado:</strong>
            <?php
              $estado = $row['estado'];
              $estadoImg = '';
              $estadoTexto = '';
              $map = [
                'Preso' => ['preso.png', 'Preso'],
                'Orden de arresto' => ['ordenDeArresto.png', 'Orden de Arresto'],
                'Libre' => ['libre.png', 'Libre'],
              ];
              if (isset($map[$estado])) {
                [$estadoImg, $estadoTexto] = $map[$estado];
              }
              if ($estadoImg):
            ?>
              <img src="/img/<?= $estadoImg ?>" alt="<?= $estadoTexto ?>" style="width:20px; height:20px; vertical-align:middle;">
              <small><?= $estadoTexto ?></small>
            <?php else: ?>
              <?= htmlspecialchars($estado) ?>
            <?php endif; ?>
          </p>
          <p><strong>Fecha Nacimiento:</strong> <?= htmlspecialchars($row['fecha_nacimiento']) ?></p>
          <p><strong>Teléfono:</strong> <?= htmlspecialchars($row['fono_fijo']) ?: htmlspecialchars($row['celular']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
          <a href="editar_delincuente.php?id=<?= htmlspecialchars($row['id']) ?>">
            <button class="Actuali">Actualizar</button> 
          </a>
          <?php if (!empty($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
            <form method="POST" action="eliminar_delincuente.php" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro?');">
              <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
              <button type="submit" style="background-color:red; color:white;">Eliminar</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No hay delincuentes registrados.</p>
    <?php endif; ?>
  </div>
</main>
<?php include __DIR__ . '/../inc/footer.php'; ?>
