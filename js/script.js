// js/script.js

/**
 * Limpia el RUT: elimina puntos, guiones y pasa a mayúsculas.
 */
function limpiarRut(rut) {
  return rut.replace(/[^0-9kK]/g, '').toUpperCase();
}

/**
 * Calcula el dígito verificador con factores 2‑7.
 */
function calcularDigitoVerificador(rutSinDv) {
  let sum = 0;
  let factor = 2;
  for (let i = rutSinDv.length - 1; i >= 0; i--) {
    sum += parseInt(rutSinDv.charAt(i), 10) * factor;
    factor = (factor === 7) ? 2 : factor + 1;
  }
  const dv = 11 - (sum % 11);
  if (dv === 11) return '0';
  if (dv === 10) return 'K';
  return dv.toString();
}

/**
 * Valida el RUT completo con formato y cálculo del dígito verificador.
 */
function validarRut(rut) {
  // Elimina espacios, convierte a mayúsculas
  rut = rut.trim().toUpperCase();

  // Expresión regular para validar formato básico
  if (!/^0*(\d{1,3}(\.?\d{3})*)\-?([\dkK])$/.test(rut)) return false;

  // Limpiar puntos y separar cuerpo y dígito verificador
  rut = rut.replace(/\./g, '').replace('-', '');
  const cuerpo = rut.slice(0, -1);
  const dvIngresado = rut.slice(-1);

  const dvCalculado = calcularDigitoVerificador(cuerpo);
  return dvIngresado === dvCalculado;
}

document.addEventListener("DOMContentLoaded", () => {
  /**
   * Validación de RUT en todos los inputs con name="rut"
   */
  document.querySelectorAll('input[name="rut"]').forEach(field => {
    field.addEventListener("blur", () => {
      if (field.value && !validarRut(field.value)) {
        alert(`El RUT ingresado (${field.value}) no es válido.`);
        // Usamos setTimeout para evitar el loop infinito del focus inmediato
        setTimeout(() => field.focus(), 10);
      }
    });
  });

  /**
   * Toggle del sidebar (muestra u oculta)
   */
  const btn = document.getElementById('btnToggleSidebar');
  if (btn) {
    btn.addEventListener('click', () => {
      const sidebar = document.getElementById('sidebar');
      if (sidebar) {
        sidebar.classList.toggle('hidden');
      }
    });
  }

  /**
   * Validación personalizada de campo obligatorio en formulario de reportes
   */
  const input = document.getElementById("ing_list_del");
  if (input) {
    input.addEventListener("invalid", () => {
      input.setCustomValidity("Por favor, rellenar el campo.");
    });

    input.addEventListener("input", () => {
      input.setCustomValidity("");
    });
  }

  /**
   * Autocompletar datos del delincuente si el RUT ya existe
   */
  const formRegistro = document.querySelector('form[action="process_registro_delincuente.php"]');
  if (formRegistro) {
    const rutField = document.getElementById('rut');
    const nombresField = document.getElementById('nombres');
    const apellidosField = document.getElementById('apellidos');
    const apodoField = document.getElementById('apodo');
    const fechaField = document.getElementById('fecha_nacimiento');
    const delitosField = document.getElementById('delitos');

    if (rutField) {
      rutField.addEventListener('blur', () => {
        const rut = rutField.value.trim();
        if (!rut) return;

        fetch(`/api/get_delincuente_by_rut.php?rut=${encodeURIComponent(rut)}`)
          .then(r => r.ok ? r.json() : {})
          .then(data => {
            if (data) {
              if (nombresField) nombresField.value = data.nombres || '';
              if (apellidosField) apellidosField.value = data.apellidos || '';
              if (!data.nombres && data.apellidos_nombres) {
                const parts = data.apellidos_nombres.split(' ', 2);
                if (apellidosField && !apellidosField.value) apellidosField.value = parts[0] || '';
                if (nombresField && !nombresField.value) nombresField.value = parts[1] || '';
              }
              if (apodoField) apodoField.value = data.apodo || '';
              if (fechaField) fechaField.value = data.fecha_nacimiento || '';
            }
          })
          .catch(() => { /* ignorar errores */ });

        if (delitosField) {
          fetch(`/api/get_delitos_count.php?rut=${encodeURIComponent(rut)}`)
            .then(r => r.ok ? r.json() : {count:0})
            .then(info => {
              const c = parseInt(info.count, 10);
              delitosField.value = c > 0 ? String(c) : 'sin registros aun';
            })
            .catch(() => { delitosField.value = 'sin registros aun'; });
        }
      });
    }
  }

  const formControl = document.querySelector('form[action="process_registro_control.php"]');
  if (formControl) {
    const rutCtl = document.getElementById('rut');
    const nombreCtl = document.getElementById('nombre');
    const apellidoCtl = document.getElementById('apellido');
    if (rutCtl) {
      rutCtl.addEventListener('blur', () => {
        const rut = rutCtl.value.trim();
        if (!rut) return;
        fetch(`/api/get_delincuente_by_rut.php?rut=${encodeURIComponent(rut)}`)
          .then(r => r.ok ? r.json() : {})
          .then(data => {
            if (data) {
              if (nombreCtl && !nombreCtl.value) nombreCtl.value = data.nombres || '';
              if (apellidoCtl && !apellidoCtl.value) apellidoCtl.value = data.apellidos || '';
              if (data.tiene_delitos) alert('rut con antecedentes delictivos');
            }
          })
          .catch(() => {});
      });
    }
  }

  // Autocompletar lat/lon al seleccionar comuna
  const comunaSelect = document.getElementById('comuna');
  const latField = document.getElementById('latitud');
  const lngField = document.getElementById('longitud');
  if (comunaSelect && latField && lngField) {
    comunaSelect.addEventListener('change', () => {
      const opt = comunaSelect.selectedOptions[0];
      if (opt) {
        latField.value = opt.dataset.lat || '';
        lngField.value = opt.dataset.lng || '';
      }
    });
  }
});
