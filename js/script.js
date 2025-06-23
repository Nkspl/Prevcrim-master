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
});