<?php
/**
 * Archivo: menu_principal.php
 * Propósito: Menú de navegación para alumno autenticado
 * 
 * Funcionalidades:
 * - Verificar sesión activa (protección de ruta)
 * - Mostrar información del alumno logueado
 * - Links a funcionalidades: calificaciones, modificar, matricular, eliminar
 * - Confirmación JavaScript para acción de eliminar
 * 
 * Dependencias: Sesión iniciada con datos del alumno
 * 
 * @author Sistema de Gestión Escolar
 * @version 1.0
 */

// Iniciar sesión para acceder a variables de sesión
session_start();

// Verificar que el usuario esté autenticado
// Si no existe expediente o nombre en sesión, redirigir a login
if(!isset($_SESSION['expediente']) || !isset($_SESSION['nombre'])){
    header("Location: index.php");
    exit();
}

// Obtener datos de la sesión
$exp = $_SESSION['expediente'];
$nombre = $_SESSION['nombre'];
// Operador ternario para manejar valor opcional
$orig = isset($_SESSION['origen']) ? $_SESSION['origen'] : 'No especificado';

// Mostrar información del alumno
echo '<h1>Datos del alumno</h1>';

// printf: imprime con formato (similar a sprintf de C)
// %s representa un string que será reemplazado
printf("<p>Expediente: %s, Nombre: %s, Origen: %s</p>", $exp, $nombre, $orig);

// Links de navegación a diferentes funcionalidades
echo '<p>';
    // Ver calificaciones del alumno
    echo '<a href="get_calificaciones_alum.php" title="Ver notas de ' . $nombre . '">Ver calificaciones</a> / ';
    // Modificar datos del alumno
    echo '<a href="modificar_alumno.php" title="Actualizar datos del alumno">Modificar</a> / ';
    // Matricular al alumno en asignaturas
    echo '<a href="matricular_alumno.php" title="Matricular al alumno ' . $nombre . '">Matricular</a> / ';
    // Eliminar alumno con confirmación JavaScript
    // onclick: muestra diálogo de confirmación antes de navegar
    echo '<a href="borrar_alumno.php" title="Borrar alumno" onclick="return confirm(\'¿Está seguro?\')">Eliminar</a>';
echo '</p>';

?>