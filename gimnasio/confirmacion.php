<?php
    session_start();

    // Verificar que existan los datos de la inscripción
    if(!isset($_SESSION['socioID']) || !isset($_SESSION['actividadID'])){
        header("Location: index.php");
        exit();
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gimnasio - Confirmación de Inscripción</title>
    </head>
    <body>
        <?php
        include 'conexion_bd.php';

        // ========== OBTENER DATOS DE SESIÓN ==========
        $socioID = $_SESSION['socioID'];
        $socioNombre = $_SESSION['socioNombre'];
        $socioNIF = $_SESSION['socioNIF'];
        $actividadID = $_SESSION['actividadID'];
        $monitorID = $_SESSION['monitorID'];
        $fechaInscripcion = $_SESSION['fechaInscripcion'];
        $precioMensual = $_SESSION['precioMensual'];

        // ========== DECLARACIÓN DE FUNCIONES ==========

        function obtener_datos_socio($conex, $socioID){
            $query = "SELECT * FROM Socio WHERE socioID = " . intval($socioID);
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return mysqli_fetch_array($resultado);
        }

        function obtener_datos_actividad($conex, $actividadID){
            $query = "SELECT * FROM Actividad WHERE actividadID = " . intval($actividadID);
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return mysqli_fetch_array($resultado);
        }

        function obtener_datos_monitor($conex, $monitorID){
            $query = "SELECT * FROM Monitor WHERE monitorID = " . intval($monitorID);
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return mysqli_fetch_array($resultado);
        }

        function obtener_inscripciones_socio($conex, $socioID){
            $query = "SELECT i.*, a.nombre AS actividad_nombre, a.fechaInicio, a.fechaFin,
                             m.nombre AS monitor_nombre, m.descripcion AS monitor_descripcion
                      FROM Inscripciones i
                      INNER JOIN Actividad a ON i.actividadID = a.actividadID
                      INNER JOIN Monitor m ON i.monitorID = m.monitorID
                      WHERE i.socioID = " . intval($socioID) . "
                      ORDER BY i.fechaInscripcion DESC";
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return $resultado;
        }

        // ========== EJECUCIÓN ==========

        echo '<h1>Sistema de Gestión de Gimnasio</h1>';
        echo '<h2>Pantalla 3: Confirmación de Inscripción</h2>';
        echo '<hr>';

        // Obtener datos completos
        $socio = obtener_datos_socio($conex, $socioID);
        $actividad = obtener_datos_actividad($conex, $actividadID);
        $monitor = obtener_datos_monitor($conex, $monitorID);

        // Mostrar confirmación
        $confirmacion = <<<CONFIRMACION
        <div style="background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
            <h3>¡Inscripción realizada con éxito!</h3>
        </div>

        <h3>Datos del Socio</h3>
        <table border="1" cellpadding="10">
            <tr><td><strong>ID:</strong></td><td>{$socio['socioID']}</td></tr>
            <tr><td><strong>NIF:</strong></td><td>{$socio['nif']}</td></tr>
            <tr><td><strong>Nombre:</strong></td><td>{$socio['nombre']}</td></tr>
            <tr><td><strong>Teléfono:</strong></td><td>{$socio['telefono']}</td></tr>
            <tr><td><strong>Email:</strong></td><td>{$socio['email']}</td></tr>
        </table>

        <h3>Datos de la Actividad</h3>
        <table border="1" cellpadding="10">
            <tr><td><strong>Nombre:</strong></td><td>{$actividad['nombre']}</td></tr>
            <tr><td><strong>Descripción:</strong></td><td>{$actividad['descripcion']}</td></tr>
            <tr><td><strong>Fecha Inicio:</strong></td><td>{$actividad['fechaInicio']}</td></tr>
            <tr><td><strong>Fecha Fin:</strong></td><td>{$actividad['fechaFin']}</td></tr>
        </table>

        <h3>Datos del Monitor</h3>
        <table border="1" cellpadding="10">
            <tr><td><strong>Nombre:</strong></td><td>{$monitor['nombre']}</td></tr>
            <tr><td><strong>Descripción:</strong></td><td>{$monitor['descripcion']}</td></tr>
        </table>

        <h3>Datos de la Inscripción</h3>
        <table border="1" cellpadding="10">
            <tr><td><strong>Fecha de Inscripción:</strong></td><td>$fechaInscripcion</td></tr>
            <tr><td><strong>Precio Mensual:</strong></td><td>$precioMensual €</td></tr>
            <tr><td><strong>Duración:</strong></td><td>1 año</td></tr>
        </table>
CONFIRMACION;
        print $confirmacion;

        echo '<hr>';
        echo '<h3>Listado de Inscripciones del Socio</h3>';

        $inscripciones = obtener_inscripciones_socio($conex, $socioID);

        if(mysqli_num_rows($inscripciones) > 0){
            echo '<table border="1" cellpadding="10">';
            echo '<tr>';
            echo '<th>Actividad</th>';
            echo '<th>Monitor</th>';
            echo '<th>Fecha Inscripción</th>';
            echo '<th>Precio Mensual</th>';
            echo '<th>Período Actividad</th>';
            echo '</tr>';

            while($inscripcion = mysqli_fetch_array($inscripciones)){
                echo '<tr>';
                echo '<td>' . $inscripcion['actividad_nombre'] . '</td>';
                echo '<td>' . $inscripcion['monitor_descripcion'] . '</td>';
                echo '<td>' . $inscripcion['fechaInscripcion'] . '</td>';
                echo '<td>' . $inscripcion['precioMensual'] . ' €</td>';
                echo '<td>' . $inscripcion['fechaInicio'] . ' - ' . $inscripcion['fechaFin'] . '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p>No hay inscripciones registradas.</p>';
        }

        echo '<hr>';
        echo '<p><a href="index.php">Realizar otra inscripción</a></p>';

        // Limpiar datos de inscripción de la sesión (mantener socio)
        unset($_SESSION['actividadID']);
        unset($_SESSION['monitorID']);
        unset($_SESSION['fechaInscripcion']);
        unset($_SESSION['precioMensual']);

        mysqli_close($conex);
        ?>
    </body>
</html>
