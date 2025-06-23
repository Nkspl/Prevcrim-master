<?php
// crear_usuario.php

// 1) Incluir configuración y funciones
require_once 'config.php';
require_once 'inc/funciones.php';

// 2) Datos del nuevo usuario (puedes cambiar estos valores)
$rut               = '24.471.968-6';      // formateado con puntos y guión
$nombre            = 'Nickens Pierre Louis';
$password_plain    = '1234';   // contraseña en texto plano
$rol               = 'operador';          // 'admin', 'jefe_zona' o 'operador'
$institucion_id    = null ;                   // ID de la institución (o NULL)
$fecha_habilitacion= date('Y-m-d');       // hoy

// 3) Validar RUT antes de intentar insertar
if (!validarRut($rut)) {
    die("Error: El RUT '{$rut}' no es válido.\n");
}

// 4) Preparar el hash de la contraseña
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

// 5) Insertar en la tabla 'usuario'
$sql = "INSERT INTO usuario
        (rut, nombre, password, rol, institucion_id, fecha_habilitacion)
        VALUES
        (:rut, :nombre, :password, :rol, :inst_id, :fh)";

$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        'rut'     => $rut,
        'nombre'  => $nombre,
        'password'=> $password_hash,
        'rol'     => $rol,
        'inst_id' => $institucion_id,
        'fh'      => $fecha_habilitacion
    ]);
    echo "Usuario creado con éxito. ID generado: " . $pdo->lastInsertId() . "\n";
} catch (PDOException $e) {
    // Si hay error de ejecución (por ejemplo, RUT duplicado), lo mostramos:
    echo "Error al crear usuario: " . $e->getMessage() . "\n";
}