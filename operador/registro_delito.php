<?php
// operador/registro_delito.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'operador') {
  header("Location: ../index.php");
  exit();
}
require_once '../config.php';

// Obtener tipos de delito con su descripción
$stmtTipos = $pdo->query("SELECT id, nombre, descripcion FROM tipo_delito ORDER BY nombre");
$tipos = $stmtTipos->fetchAll();

// Obtener comunas para selector
$stmtComunas = $pdo->query("SELECT nombre, latitud, longitud FROM comuna ORDER BY nombre");
$comunas = $stmtComunas->fetchAll();

// Obtener delincuentes existentes para asociar (opcional)
$stmtDelincuentes = $pdo->query("SELECT id, rut, apellidos_nombres FROM delincuente ORDER BY apellidos_nombres");
$delincuentes = $stmtDelincuentes->fetchAll();
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Registro de Delitos</h2>
    <?php if (isset($_GET['msg'])) echo "<p class='msg'>" . htmlspecialchars($_GET['msg']) . "</p>"; ?>
    <form action="process_registro_delito.php" method="post">
      <div class="form-group">
        <label for="codigo">Código:</label>
        <input id="codigo" name="codigo" required>
      </div>
      <div class="form-group">
        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" required></textarea>
      </div>
      <div class="form-group">
        <label for="comuna">Comuna:</label>
        <select id="comuna" name="comuna" required>
          <option value="">-- Seleccione --</option>
          <?php foreach ($comunas as $c): ?>
            <option value="<?= htmlspecialchars($c['nombre']) ?>" data-lat="<?= htmlspecialchars($c['latitud']) ?>" data-lng="<?= htmlspecialchars($c['longitud']) ?>">
              <?= htmlspecialchars($c['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="sector">Sector:</label>
        <input id="sector" name="sector">
      </div>
      <div class="form-group">
        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" required>
      </div>
      <div class="form-group">
        <label for="latitud">Latitud:</label>
        <input id="latitud" name="latitud">
      </div>
      <div class="form-group">
        <label for="longitud">Longitud:</label>
        <input id="longitud" name="longitud">
      </div>
      <div class="form-group">
        <label for="tipo_id">Tipo de Delito:</label>
        <select id="tipo_id" name="tipo_id">
          <option value="">-- Seleccione --</option>
          <?php foreach ($tipos as $t): ?>
            <option value="<?= htmlspecialchars($t['id']) ?>">
              <?= htmlspecialchars($t['nombre'] . ' - ' . $t['descripcion']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="delincuente_id">Delincuente Asociado:</label>
        <select id="delincuente_id" name="delincuente_id">
          <option value="">-- Ninguno --</option>
          <?php foreach ($delincuentes as $d): ?>
            <option value="<?= htmlspecialchars($d['id']) ?>">
              <?= htmlspecialchars($d['apellidos_nombres']) ?> (<?= htmlspecialchars($d['rut']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit">Registrar Delito</button>
    </form>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
