<?php
/**
 * Archivo: conexion_bd.php
 * Propósito: Establecer conexión con la base de datos MySQL
 * Dependencias: Extensión MySQLi habilitada en PHP
 * Uso: include 'conexion_bd.php'; en archivos que requieran acceso a BD
 * 
 * @author Sistema de Gestión Escolar
 * @version 1.0
 */

// Establecer conexión con el servidor MySQL
// Parámetros: host, usuario, contraseña
// or die() detiene la ejecución si hay error
$conex = mysqli_connect('localhost', 'root', '') or die (mysqli_error($conex));

// Seleccionar la base de datos "escuela"
// Todos los queries posteriores se ejecutarán en esta BD
mysqli_select_db($conex, "escuela") or die (mysqli_error($conex));
?>