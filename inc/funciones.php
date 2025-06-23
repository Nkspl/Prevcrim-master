<?php
// inc/funciones.php

/**
 * Limpia un RUT (quita puntos, guiones, mayúsculas).
 */
function limpiarRut($rut) {
    return strtoupper(preg_replace('/[^0-9Kk]/', '', $rut));
}

/**
 * Calcula el dígito verificador usando factores 2‑7.
 */
function calcularDigitoVerificador($rutSinDv) {
    $sum = 0;
    $factor = 2;
    for ($i = strlen($rutSinDv) - 1; $i >= 0; $i--) {
        $sum += intval($rutSinDv[$i]) * $factor;
        $factor = ($factor === 7) ? 2 : $factor + 1;
    }
    $dv = 11 - ($sum % 11);
    if ($dv === 11) return '0';
    if ($dv === 10) return 'K';
    return (string)$dv;
}

/**
 * Valida que un RUT sea auténtico.
 */
function validarRut($rut) {
    $rutLimpio = limpiarRut($rut);
    if (strlen($rutLimpio) < 2) {
        return false;
    }
    $cuerpo = substr($rutLimpio, 0, -1);
    $dvIngresado = substr($rutLimpio, -1);
    $dvCalculado = calcularDigitoVerificador($cuerpo);
    return $dvIngresado === $dvCalculado;
}
