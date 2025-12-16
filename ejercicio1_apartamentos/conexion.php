<?php
/**
 * ARCHIVO: conexion.php
 * PROPÓSITO: Gestionar la conexión a la base de datos MySQL
 *
 * Este archivo establece la conexión con la base de datos usando mysqli
 * y puede ser incluido en cualquier script que necesite acceso a la BD
 */

// Parámetros de conexión
$servidor = 'localhost';
$usuario = 'root';
$password = '';
$base_datos = 'apartamentos_turisticos';

// Establecer conexión con el servidor MySQL
$conex = mysqli_connect($servidor, $usuario, $password) or die("Error al conectar con el servidor: " . mysqli_error($conex));

// Seleccionar la base de datos
mysqli_select_db($conex, $base_datos) or die("Error al seleccionar la base de datos: " . mysqli_error($conex));

// Configurar el conjunto de caracteres para evitar problemas con acentos y caracteres especiales
mysqli_set_charset($conex, "utf8mb4");

?>
