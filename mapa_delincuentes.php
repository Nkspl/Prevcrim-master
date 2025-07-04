<?php
// mapa_delincuentes.php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}
?>
<?php include 'inc/header.php'; ?>
<div class="wrapper">
  <div class="content">
    <h2>Mapa de Delincuentes</h2>

    <!-- Formulario de Búsqueda -->
    <form id="searchForm" class="map-search">
      <label for="searchRut">RUT:</label>
      <select id="searchRut">
        <option value="">-- Selecciona un RUT --</option>
      </select>

      <label for="searchNombre">Nombre:</label>
      <input type="text" id="searchNombre" placeholder="Apellido o Nombre" autocomplete="off">

      <label for="searchApodo">Apodo:</label>
      <input type="text" id="searchApodo" placeholder="Apodo" autocomplete="off">

      <button type="submit" id="searchBtn">Buscar</button>
      <button type="button" id="resetBtn">Mostrar Todos</button>
    </form>

    <!-- Contenedor del mapa -->
    <div id="map" style="height:500px;"></div>
  </div>
  <?php include 'inc/footer.php'; ?>
</div>

<script>
  let map;
  let markers = [];
  let delincuentes = [];

  function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
      zoom: 6,
      center: { lat: -33.4489, lng: -70.6693 }
    });
    loadMarkers();
  }

  function clearMarkers() {
    markers.forEach(m => m.setMap(null));
    markers = [];
  }

  function loadMarkers(filters = {}) {
    const qs = new URLSearchParams(filters).toString();
    fetch('api/get_delincuentes.php' + (qs ? '?' + qs : ''))
      .then(r => r.json())
      .then(data => {
        clearMarkers();
        data.forEach(d => {
          const pos = {
            lat: parseFloat(d.latitud),
            lng: parseFloat(d.longitud)
          };
          const marker = new google.maps.Marker({
            position: pos,
            map,
            title: `${d.apellidos_nombres} (${d.rut})\nApodo: ${d.apodo}\nÚltimo lugar: ${d.ultimo_lugar_visto}`
          });
          markers.push(marker);
        });
        if (markers.length) {
          const bounds = new google.maps.LatLngBounds();
          markers.forEach(m => bounds.extend(m.getPosition()));
          map.fitBounds(bounds);
        }
      });
  }

  function cargarDelincuentes() {
    fetch('api/getRutDelincuentes.php')
      .then(res => res.json())
      .then(data => {
        delincuentes = data;

        const select = document.getElementById('searchRut');
        select.innerHTML = '<option value="">-- Selecciona un RUT --</option>';
        data.forEach(d => {
          const option = document.createElement('option');
          option.value = d.rut;
          option.textContent = d.rut;
          select.appendChild(option);
        });
      })
      .catch(err => console.error('Error cargando RUTs:', err));
  }

  document.addEventListener('DOMContentLoaded', () => {
    cargarDelincuentes();

    document.getElementById('searchRut').addEventListener('change', (e) => {
      const rut = e.target.value;
      const delincuente = delincuentes.find(d => d.rut === rut);

      if (delincuente) {
        document.getElementById('searchNombre').value = delincuente.apellidos_nombres || '';
        document.getElementById('searchApodo').value = delincuente.apodo || '';
      } else {
        document.getElementById('searchNombre').value = '';
        document.getElementById('searchApodo').value = '';
      }
    });

    const searchForm = document.getElementById('searchForm');
    const handleSearch = () => {
      const rut = document.getElementById('searchRut').value.trim();
      const nombre = document.getElementById('searchNombre').value.trim();
      const apodo = document.getElementById('searchApodo').value.trim();
      loadMarkers({ rut, nombre, apodo });
    };

    document.getElementById('searchBtn').addEventListener('click', handleSearch);
    searchForm.addEventListener('submit', (e) => {
      e.preventDefault();
      handleSearch();
    });

    document.getElementById('resetBtn').addEventListener('click', () => {
      document.getElementById('searchRut').value = '';
      document.getElementById('searchNombre').value = '';
      document.getElementById('searchApodo').value = '';
      loadMarkers();
    });
  });
</script>

<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaYoejG_5UXM_POLcQ47plW0tDytSmHqQ&callback=initMap">
</script>