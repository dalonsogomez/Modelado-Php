<?php
session_start();

if(!isset($_SESSION['expediente']) || !isset($_SESSION['nombre'])){
    header("Location: index.php");
    exit();
}

$exp = $_SESSION['expediente'];
$nombre = $_SESSION['nombre'];
$orig = isset($_SESSION['origen']) ? $_SESSION['origen'] : 'No especificado';

echo '<h1>Datos del alumno</h1>';

printf("<p>Expediente: %s, Nombre: %s, Origen: %s</p>", $exp, $nombre, $orig);
echo '<p>';
    echo '<a href="get_calificaciones_alum.php" title="Ver notas de ' . $nombre . '">Ver calificaciones</a> / ';
    echo '<a href="modificar_alumno.php" title="Actualizar datos del alumno">Modificar</a> / ';
    echo '<a href="matricular_alumno.php" title="Matricular al alumno ' . $nombre . '">Matricular</a> / ';
    echo '<a href="borrar_alumno.php" title="Borrar alumno" onclick="return confirm(\'¿Está seguro?\')">Eliminar</a>';
echo '</p>';

?>