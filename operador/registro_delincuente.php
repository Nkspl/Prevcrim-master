<?php
// operador/registro_delincuente.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'operador') {
  header("Location: ../index.php");
  exit();
}
require_once '../config.php';

// Obtener tipos de delitos desde la base de datos
$stmtTipos = $pdo->query("SELECT nombre FROM tipo_delito ORDER BY nombre");
$tiposDelito = $stmtTipos->fetchAll();
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Registro de Delincuentes</h2>
    <?php if (isset($_GET['msg'])) echo "<p class='msg'>" . htmlspecialchars($_GET['msg']) . "</p>"; ?>
    <form action="process_registro_delincuente.php" method="post">
      <div class="form-group">
        <label for="rut">RUT:</label>
        <input id="rut" name="rut" required>
      </div>
      <div class="form-group">
        <label for="nombre">Apellidos y Nombres:</label>
        <input id="nombre" name="nombre" required>
      </div>
      <div class="form-group">
        <label for="apodo">Apodo:</label>
        <input id="apodo" name="apodo">
      </div>
      <div class="form-group">
        <label for="domicilio">Domicilio:</label>
        <input id="domicilio" name="domicilio" required>
      </div>
      <div class="form-group">
        <label for="ultimo_lugar">Último Lugar Visto:</label>
        <input id="ultimo_lugar" name="ultimo_lugar" required>
      </div>
      <div class="form-group">
        <label for="fono">Fono Fijo:</label>
        <input id="fono" name="fono">
      </div>
      <div class="form-group">
        <label for="celular">Celular:</label>
        <input id="celular" name="celular">
      </div>
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email">
      </div>
      <div class="form-group">
        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
      </div>
      <div class="form-group">
        <label for="delitos">Delitos:</label>
        <select id="delitos" name="delitos[]" multiple>
          <?php foreach ($tiposDelito as $t): ?>
            <option value="<?= htmlspecialchars($t['nombre']) ?>">
              <?= htmlspecialchars($t['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="estado">Estado:</label>
        <select id="estado" name="estado" required>
          <option value="P">Preso</option>
          <option value="L">Libre</option>
          <option value="A">Orden de Arresto</option>
        </select>
      </div>
      <div class="form-group">
        <label>Selecciona ubicación en el mapa:</label>
        <div id="map" style="height: 400px; width: 100%; margin-bottom: 10px;"></div>
      </div>
      <div class="form-group">
        <label for="latitud">Latitud:</label>
        <input id="latitud" name="latitud" placeholder="Ej: -33.4489" required>
      </div>
      <div class="form-group">
        <label for="longitud">Longitud:</label>
        <input id="longitud" name="longitud" placeholder="Ej: -70.6693" required>
      </div>
      <button type="submit">Registrar Delincuente</button>
    </form>
  </div>

  <!-- Script para el mapa -->
  <script>
    let map;

    function initMap() {
      const latInput = document.getElementById("latitud");
      const lngInput = document.getElementById("longitud");
      const addressInput = document.getElementById("ultimo_lugar");

      const initialLat = parseFloat(latInput.value) || -33.4489;
      const initialLng = parseFloat(lngInput.value) || -70.6693;

      const initialPosition = {
        lat: initialLat,
        lng: initialLng
      };

      map = new google.maps.Map(document.getElementById("map"), {
        zoom: 12,
        center: initialPosition,
      });

      const marker = new google.maps.Marker({
        position: initialPosition,
        map: map,
        draggable: true,
      });

      const geocoder = new google.maps.Geocoder();
      const autocomplete = new google.maps.places.Autocomplete(addressInput);

      function updatePosition(location) {
        const lat = location.lat();
        const lng = location.lng();
        map.setCenter(location);
        marker.setPosition(location);
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
      }

      function geocodeAddress(value) {
        if (!value) return;
        geocoder.geocode({ address: value }, (results, status) => {
          if (status === "OK" && results[0]) {
            updatePosition(results[0].geometry.location);
          }
        });
      }

      autocomplete.addListener("place_changed", () => {
        const place = autocomplete.getPlace();
        if (place.geometry) {
          updatePosition(place.geometry.location);
        } else {
          geocodeAddress(addressInput.value);
        }
      });

      addressInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
          e.preventDefault();
          geocodeAddress(addressInput.value);
        }
      });

      map.addListener("click", function(e) {
        const lat = e.latLng.lat();
        const lng = e.latLng.lng();
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
        marker.setPosition({
          lat,
          lng
        });
      });

      marker.addListener("dragend", function(e) {
        latInput.value = e.latLng.lat().toFixed(6);
        lngInput.value = e.latLng.lng().toFixed(6);
      });
    }
  </script>

  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBS9ZZ_-SfCP-HnAOJcRRUVDJO5TqBs2gg&callback=initMap&libraries=places">
  </script>

  <?php include('../inc/footer.php'); ?>
</div>