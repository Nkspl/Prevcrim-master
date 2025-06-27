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
  $sql = "SELECT id, rut, apellidos_nombres, apodo, domicilio, ultimo_lugar_visto, fono_fijo, celular, email, fecha_nacimiento, delitos, estado
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
  $sql = "SELECT id, rut, apellidos_nombres, apodo, domicilio, ultimo_lugar_visto, fono_fijo, celular, email, fecha_nacimiento, delitos, estado
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

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>RUT</th>
        <th>Nombre Completo</th>
        <th>Apodo</th>
        <th>Último Lugar Visto</th>
        <th>Delitos</th>
        <th>Estado</th>
        <th>Fecha Nacimiento</th>
        <th>Teléfono</th>
        <th>Email</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($delincuentes)): ?>
        <?php foreach ($delincuentes as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['rut']) ?></td>
            <td><?= htmlspecialchars($row['apellidos_nombres']) ?></td>
            <td><?= htmlspecialchars($row['apodo']) ?></td>
            <td><?= htmlspecialchars($row['ultimo_lugar_visto']) ?></td>
            <td><?= htmlspecialchars($row['delitos']) ?></td>
            <td style="text-align: center;">
  <?php
    $estado = $row['estado'];
    $estadoImg = '';
    $estadoTexto = '';

    if ($estado === 'P') {
      $estadoImg = 'preso.png';
      $estadoTexto = 'Preso';
    } elseif ($estado === 'A') {
      $estadoImg = 'ordenDeArresto.png';
      $estadoTexto = 'Orden de Arresto';
    } elseif ($estado === 'L') {
      $estadoImg = 'libre.png';
      $estadoTexto = 'Libre';
    }

    if ($estadoImg):
  ?>
    <img src="/img/<?= $estadoImg ?>" alt="<?= $estadoTexto ?>" style="width:30px; height:30px;"><br>
    <small><?= $estadoTexto ?></small>
  <?php else: ?>
    <?= htmlspecialchars($estado) ?>
  <?php endif; ?>
</td>
            <td><?= htmlspecialchars($row['fecha_nacimiento']) ?></td>
            <td><?= htmlspecialchars($row['fono_fijo']) ?: htmlspecialchars($row['celular']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td>
              <a href="editar_delincuente.php?id=<?= htmlspecialchars($row['id']) ?>">
                <button>Editar</button>
              </a>
              <?php if (!empty($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <form method="POST" action="eliminar_delincuente.php" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro?');">
                  <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                  <button type="submit" style="background-color:red; color:white;">Eliminar</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="11">No hay delincuentes registrados.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</main>