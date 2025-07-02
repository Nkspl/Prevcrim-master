<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'operador') {
    header('Location: /login.php');
    exit;
}

require_once '../config.php';

$stmtTipos = $pdo->query("SELECT nombre, descripcion FROM tipo_delito ORDER BY nombre");
$tiposDelito = $stmtTipos->fetchAll();

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID no válido.";
    exit;
}

// Obtener los datos del delincuente
$stmt = $pdo->prepare("SELECT * FROM delincuente WHERE id = ?");
$stmt->execute([$id]);
$delincuente = $stmt->fetch();

$apellidos = $delincuente['apellidos'] ?? '';
$nombres = $delincuente['nombres'] ?? '';
if (!$apellidos && !$nombres && !empty($delincuente['apellidos_nombres'])) {
    $parts = explode(' ', $delincuente['apellidos_nombres'], 2);
    $apellidos = $parts[0] ?? '';
    $nombres = $parts[1] ?? '';
}

$selectedDelitos = array_filter(array_map('trim', explode(',', $delincuente['delitos'])));

// Estado actual guardado en la base de datos
$estadoActual = $delincuente['estado'] ?? '';

if (!$delincuente) {
    echo "Delincuente no encontrado.";
    exit;
}
?>

<?php include('../inc/header.php'); ?>
<div class="wrapper">
    <div class="content">
        <h2>Editar Delincuente</h2>
        <form action="procesar_edicion_delincuente.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($delincuente['id']) ?>">
            <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($delincuente['imagen']) ?>">

            <div class="form-group">
                <label for="rut">RUT:</label>
                <input id="rut" name="rut" required value="<?= htmlspecialchars($delincuente['rut']) ?>">
            </div>
            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input id="apellidos" name="apellidos" required value="<?= htmlspecialchars($apellidos) ?>">
            </div>
            <div class="form-group">
                <label for="nombres">Nombres:</label>
                <input id="nombres" name="nombres" required value="<?= htmlspecialchars($nombres) ?>">
            </div>
            <div class="form-group">
                <label for="apodo">Apodo:</label>
                <input id="apodo" name="apodo" value="<?= htmlspecialchars($delincuente['apodo']) ?>">
            </div>
            <div class="form-group">
                <label for="domicilio">Domicilio:</label>
                <input id="domicilio" name="domicilio" required value="<?= htmlspecialchars($delincuente['domicilio']) ?>">
            </div>
            <div class="form-group">
                <label for="ultimo_lugar">Último Lugar Visto:</label>
                <input id="ultimo_lugar" name="ultimo_lugar" placeholder="seleccionalo en el mapa de abajo" required readonly value="<?= htmlspecialchars($delincuente['ultimo_lugar_visto']) ?>">
            </div>
            <div class="form-group">
                <label for="fono">Fono Fijo:</label>
                <input id="fono" name="fono" value="<?= htmlspecialchars($delincuente['fono_fijo']) ?>">
            </div>
            <div class="form-group">
                <label for="celular">Celular:</label>
                <input id="celular" name="celular" value="<?= htmlspecialchars($delincuente['celular']) ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($delincuente['email']) ?>">
            </div>
            <div class="form-group">
                <label for="imagen">Foto (opcional):</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <?php if ($delincuente['imagen']): ?>
                  <br><img src="/<?= htmlspecialchars($delincuente['imagen']) ?>" style="max-width:150px;">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required value="<?= htmlspecialchars($delincuente['fecha_nacimiento']) ?>">
            </div>
            <div class="form-group">
                <label for="delitos">Delitos:</label>
                <select id="delitos" name="delitos[]" multiple>
                    <?php foreach ($tiposDelito as $t): ?>
                        <option value="<?= htmlspecialchars($t['nombre']) ?>" <?= in_array($t['nombre'], $selectedDelitos) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['nombre'] . ' - ' . $t['descripcion']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="Preso" <?= $estadoActual === 'Preso' ? 'selected' : '' ?>>Preso</option>
                    <option value="Libre" <?= $estadoActual === 'Libre' ? 'selected' : '' ?>>Libre</option>
                    <option value="Orden de arresto" <?= $estadoActual === 'Orden de arresto' ? 'selected' : '' ?>>Orden de Arresto</option>
                </select>
            </div>
            <div class="form-group">
                <label>Selecciona ubicación en el mapa:</label>
                <div id="map" style="height: 400px; width: 100%; margin-bottom: 10px;"></div>
            </div>
            <div class="form-group">
                <label for="latitud">Latitud:</label>
                <input id="latitud" name="latitud" required readonly value="<?= htmlspecialchars($delincuente['latitud']) ?>">
            </div>
            <div class="form-group">
                <label for="longitud">Longitud:</label>
                <input id="longitud" name="longitud" required readonly value="<?= htmlspecialchars($delincuente['longitud']) ?>">
            </div>
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
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

    const initialPosition = { lat: initialLat, lng: initialLng };

    map = new google.maps.Map(document.getElementById("map"), {
      zoom: 12,
      center: initialPosition,
    });

    let marker = new google.maps.Marker({
      position: initialPosition,
      map: map,
      draggable: true,
    });

    const geocoder = new google.maps.Geocoder();
    const autocomplete = new google.maps.places.Autocomplete(addressInput);

    function reverseGeocodeOSM(lat, lng) {
      const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;
      fetch(url)
        .then(r => r.json())
        .then(data => {
          if (data && data.display_name) {
            addressInput.value = data.display_name;
          }
        })
        .catch(() => {});
    }

    function updatePosition(location) {
      const lat = location.lat();
      const lng = location.lng();
      map.setCenter(location);
      marker.setPosition(location);
      latInput.value = lat.toFixed(6);
      lngInput.value = lng.toFixed(6);
      geocoder.geocode({ location: { lat, lng } }, (results, status) => {
        if (status === "OK" && results[0]) {
          addressInput.value = results[0].formatted_address;
        } else {
          reverseGeocodeOSM(lat, lng);
        }
      });
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

    map.addListener("click", function (e) {
      updatePosition(e.latLng);
    });

    marker.addListener("dragend", function (e) {
      updatePosition(e.latLng);
    });
  }
</script>

<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaYoejG_5UXM_POLcQ47plW0tDytSmHqQ&callback=initMap">
</script>

<?php include('../inc/footer.php'); ?>