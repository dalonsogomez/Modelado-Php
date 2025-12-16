<?php
/**
 * ARCHIVO: conexion.php
 * PROPÓSITO: Gestionar la conexión a la base de datos MySQL del gimnasio
 *
 * Este archivo establece la conexión con la base de datos usando mysqli
 */

// Parámetros de conexión
$servidor = 'localhost';
$usuario = 'root';
$password = '';
$base_datos = 'gimnasio';

// Establecer conexión con el servidor MySQL
$conex = mysqli_connect($servidor, $usuario, $password) or die("Error al conectar con el servidor: " . mysqli_error($conex));

// Seleccionar la base de datos
mysqli_select_db($conex, $base_datos) or die("Error al seleccionar la base de datos: " . mysqli_error($conex));

// Configurar el conjunto de caracteres
mysqli_set_charset($conex, "utf8mb4");

?>
