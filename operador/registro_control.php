<?php
// operador/registro_control.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'operador') {
  header("Location: ../index.php");
  exit();
}
require_once '../config.php';
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Registro de Control Policial</h2>
    <?php if (isset($_GET['msg'])) echo "<p class='msg'>".htmlspecialchars($_GET['msg'])."</p>"; ?>
    <form action="process_registro_control.php" method="post" id="frmControl">
      <div class="form-group">
        <label for="tipo">Tipo de Control:</label>
        <select id="tipo" name="tipo" required>
          <option value="">-- Seleccione --</option>
          <option value="identidad">Control de Identidad</option>
          <option value="vehicular">Control Vehicular</option>
          <option value="armas_drogas">Control Preventivo de Armas o Drogas</option>
          <option value="transito">Control de Tránsito</option>
        </select>
      </div>

      <div id="seccion-basicos" style="display:none;">
        <div class="form-group">
          <label for="rut">RUT:</label>
          <input id="rut" name="rut">
        </div>
        <div class="form-group">
          <label for="nombre">Nombre:</label>
          <input id="nombre" name="nombre">
        </div>
        <div class="form-group">
          <label for="apellido">Apellido:</label>
          <input id="apellido" name="apellido">
        </div>
      </div>

      <div id="seccion-identidad" class="tipo-section" style="display:none;">
        <div class="form-group">
          <label for="motivo_desplazamiento">Motivo de Desplazamiento:</label>
          <input id="motivo_desplazamiento" name="motivo_desplazamiento">
        </div>
      </div>

      <div id="seccion-ubicacion" class="tipo-section" style="display:none;">
        <div class="form-group">
          <label for="ubicacion">Ubicación:</label>
          <input id="ubicacion" name="ubicacion">
        </div>
        <div class="form-group">
          <label for="latitud">Latitud:</label>
          <input id="latitud" name="latitud" readonly>
        </div>
        <div class="form-group">
          <label for="longitud">Longitud:</label>
          <input id="longitud" name="longitud" readonly>
        </div>
        <div class="form-group">
          <label for="observacion">Observación:</label>
          <textarea id="observacion" name="observacion"></textarea>
        </div>
      </div>

      <div id="seccion-vehicular" class="tipo-section" style="display:none;">
        <div class="form-group">
          <label for="licencia_conducir">Licencia de Conducir:</label>
          <input id="licencia_conducir" name="licencia_conducir">
        </div>
        <div class="form-group">
          <label for="padron_vehiculo">Padrón del Vehículo:</label>
          <input id="padron_vehiculo" name="padron_vehiculo">
        </div>
        <div class="form-group">
          <label for="revision_seguro">Revisión Técnica / SOAP:</label>
          <input id="revision_seguro" name="revision_seguro">
        </div>
      </div>

      <div id="seccion-armas" class="tipo-section" style="display:none;">
        <div class="form-group">
          <label for="pertenencias">Pertenencias:</label>
          <textarea id="pertenencias" name="pertenencias"></textarea>
        </div>
        <div class="form-group">
          <label for="permisos_arma">Permisos de Arma:</label>
          <input id="permisos_arma" name="permisos_arma">
        </div>
        <div class="form-group">
          <label for="revision_mochila">Revisión de Vehículo/Mochila:</label>
          <textarea id="revision_mochila" name="revision_mochila"></textarea>
        </div>
      </div>

      <div id="seccion-transito" class="tipo-section" style="display:none;">
        <div class="form-group">
          <label for="test_alcoholemia">Test de Alcoholemia/Narcotest:</label>
          <input id="test_alcoholemia" name="test_alcoholemia">
        </div>
        <div class="form-group">
          <label for="doc_vehicular">Documentación Vehicular y del Conductor:</label>
          <textarea id="doc_vehicular" name="doc_vehicular"></textarea>
        </div>
      </div>

      <button type="submit">Registrar Control</button>
    </form>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>

<script>
  const tipoSelect = document.getElementById('tipo');
  const basicos = document.getElementById('seccion-basicos');
  function actualizarSecciones() {
    document.querySelectorAll('.tipo-section').forEach(s => s.style.display = 'none');
    const val = tipoSelect.value;
    if (basicos) basicos.style.display = val ? 'block' : 'none';
    if (val) document.getElementById('seccion-ubicacion').style.display = 'block';
    if (val === 'identidad') document.getElementById('seccion-identidad').style.display = 'block';
    else if (val === 'vehicular') document.getElementById('seccion-vehicular').style.display = 'block';
    else if (val === 'armas_drogas') document.getElementById('seccion-armas').style.display = 'block';
    else if (val === 'transito') document.getElementById('seccion-transito').style.display = 'block';
  }
  tipoSelect.addEventListener('change', actualizarSecciones);
  actualizarSecciones();

  const ubicacionInput = document.getElementById('ubicacion');
  const latInput = document.getElementById('latitud');
  const lngInput = document.getElementById('longitud');

  function geocodeAddress() {
    const address = ubicacionInput.value.trim();
    if (!address) return;
    fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(address), {
      headers: { 'Accept': 'application/json', 'User-Agent': 'prevcrim-app' }
    })
    .then(r => r.json())
    .then(data => {
      if (data && data[0]) {
        latInput.value = parseFloat(data[0].lat).toFixed(6);
        lngInput.value = parseFloat(data[0].lon).toFixed(6);
      }
    })
    .catch(e => console.error('Geocoding error', e));
  }

  ubicacionInput.addEventListener('change', geocodeAddress);
  ubicacionInput.addEventListener('blur', geocodeAddress);
</script>
