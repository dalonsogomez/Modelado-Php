<?php
/**
 * ============================================
 * ARCHIVO DE CONFIGURACIÓN
 * ============================================
 * Sistema de Gestión de Apartamentos Turísticos
 * Configuración de conexión a base de datos
 * ============================================
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'apartamentos_turisticos');

// Configuración de zona horaria
date_default_timezone_set('Europe/Madrid');

// Configuración de errores (cambiar a false en producción)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Función de conexión a la base de datos
function conectar_bd() {
    $conex = mysqli_connect(DB_HOST, DB_USER, DB_PASS) or die("Error de conexión: " . mysqli_connect_error());
    mysqli_select_db($conex, DB_NAME) or die("Error al seleccionar BD: " . mysqli_error($conex));
    mysqli_set_charset($conex, "utf8mb4");
    return $conex;
}
?>
