<?php
    session_start();

    // Verificar que el socio esté identificado
    if(!isset($_SESSION['socioID'])){
        header("Location: index.php");
        exit();
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gimnasio - Selección de Actividad</title>
    </head>
    <body>
        <?php
        include 'conexion_bd.php';

        // ========== DECLARACIÓN DE VARIABLES ==========
        $actividadID = isset($_POST["actividadID"]) ? $_POST["actividadID"] : "";
        $fechaInicio = isset($_POST["fechaInicio"]) ? $_POST["fechaInicio"] : "";
        $monitorID = isset($_POST["monitorID"]) ? $_POST["monitorID"] : "";

        $socioID = $_SESSION['socioID'];
        $socioNombre = $_SESSION['socioNombre'];
        $socioNIF = $_SESSION['socioNIF'];

        // ========== DECLARACIÓN DE FUNCIONES ==========

        function mostrarAlerta($mensaje){
            $alerta = <<<ALERTA
                <script>
                    var miAlerta = "$mensaje";
                    alert(miAlerta);
                </script>
ALERTA;
            print $alerta;
        }

        function obtener_actividades_activas($conex){
            // Solo actividades cuya fecha de fin sea mayor o igual a la fecha actual
            $query = "SELECT actividadID, nombre, fechaFin FROM Actividad WHERE fechaFin >= CURDATE() ORDER BY nombre";
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return $resultado;
        }

        function obtener_monitores($conex){
            $query = "SELECT monitorID, nombre, descripcion FROM Monitor ORDER BY nombre";
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return $resultado;
        }

        function pintar_formulario($actividadID, $fechaInicio, $monitorID, $actividades, $monitores){
            $fecha_minima = date('Y-m-d', strtotime('+1 day'));

            $formulario1 = <<<FORM1
            <form action="seleccion_actividad.php" method="post">
                <p>
                    Actividad:
                    <select name="actividadID" required>
                        <option value="">-- Seleccione una actividad --</option>
FORM1;
            print $formulario1;

            while($actividad = mysqli_fetch_array($actividades)){
                $selected = ($actividadID == $actividad['actividadID']) ? "selected" : "";
                echo "<option value='" . $actividad['actividadID'] . "' $selected>" . $actividad['nombre'] . " (" . $actividad['fechaFin'] . ")</option>";
            }

            $formulario2 = <<<FORM2
                    </select>
                </p>
                <p>
                    Fecha de Inicio de Inscripción:
                    <input type="date" name="fechaInicio" min="$fecha_minima" value="$fechaInicio" required>
                    <small>(Debe ser posterior a la fecha actual)</small>
                </p>
                <p>
                    Monitor:
                    <select name="monitorID" required>
                        <option value="">-- Seleccione un monitor --</option>
FORM2;
            print $formulario2;

            while($monitor = mysqli_fetch_array($monitores)){
                $selected = ($monitorID == $monitor['monitorID']) ? "selected" : "";
                echo "<option value='" . $monitor['monitorID'] . "' $selected>" . $monitor['descripcion'] . "</option>";
            }

            $formulario3 = <<<FORM3
                    </select>
                </p>
                <p>
                    <input type="submit" value="Inscribirse">
                    <a href="index.php">Volver</a>
                </p>
            </form>
FORM3;
            print $formulario3;
        }

        function validar_inscripcion(&$actividadID, &$fechaInicio, &$monitorID, &$errores, $conex){
            $flag = true;

            // Validar actividad
            if($actividadID == "" || !is_numeric($actividadID)){
                $errores .= " / Debe seleccionar una actividad";
                $flag = false;
            } else {
                // Verificar que la actividad existe y está activa
                $query = "SELECT actividadID FROM Actividad WHERE actividadID = " . intval($actividadID) . " AND fechaFin >= CURDATE()";
                $resultado = mysqli_query($conex, $query);
                if(mysqli_num_rows($resultado) == 0){
                    $errores .= " / La actividad seleccionada no es válida";
                    $flag = false;
                }
            }

            // Validar fecha de inicio (mayor a fecha actual)
            if($fechaInicio == ""){
                $errores .= " / Debe seleccionar una fecha de inicio";
                $flag = false;
            } else {
                $fecha_actual = date('Y-m-d');
                if($fechaInicio <= $fecha_actual){
                    $errores .= " / La fecha de inicio debe ser posterior a hoy";
                    $flag = false;
                }
            }

            // Validar monitor
            if($monitorID == "" || !is_numeric($monitorID)){
                $errores .= " / Debe seleccionar un monitor";
                $flag = false;
            } else {
                // Verificar que el monitor existe
                $query = "SELECT monitorID FROM Monitor WHERE monitorID = " . intval($monitorID);
                $resultado = mysqli_query($conex, $query);
                if(mysqli_num_rows($resultado) == 0){
                    $errores .= " / El monitor seleccionado no es válido";
                    $flag = false;
                }
            }

            return $flag;
        }

        function verificar_inscripcion_existente($conex, $actividadID, $socioID, $monitorID){
            $query = "SELECT * FROM Inscripciones WHERE actividadID = " . intval($actividadID) .
                     " AND socioID = " . intval($socioID) .
                     " AND monitorID = " . intval($monitorID);
            $resultado = mysqli_query($conex, $query);
            return mysqli_num_rows($resultado) > 0;
        }

        // ========== EJECUCIÓN ==========

        echo '<h1>Sistema de Gestión de Gimnasio</h1>';
        echo '<h2>Pantalla 2: Selección de Actividad</h2>';
        echo '<hr>';
        echo "<p><strong>Socio:</strong> $socioNombre ($socioNIF)</p>";
        echo '<hr>';

        if(empty($_POST)){
            // Primera carga - mostrar formulario
            $actividades = obtener_actividades_activas($conex);
            $monitores = obtener_monitores($conex);
            pintar_formulario($actividadID, $fechaInicio, $monitorID, $actividades, $monitores);
        } else {
            // Procesar inscripción
            $errores = "";

            if(!validar_inscripcion($actividadID, $fechaInicio, $monitorID, $errores, $conex)){
                mostrarAlerta($errores);
                $actividades = obtener_actividades_activas($conex);
                $monitores = obtener_monitores($conex);
                pintar_formulario($actividadID, $fechaInicio, $monitorID, $actividades, $monitores);
            } else {
                // Verificar si ya existe esta inscripción
                if(verificar_inscripcion_existente($conex, $actividadID, $socioID, $monitorID)){
                    mostrarAlerta("Ya existe una inscripción para esta actividad con este monitor");
                    $actividades = obtener_actividades_activas($conex);
                    $monitores = obtener_monitores($conex);
                    pintar_formulario($actividadID, $fechaInicio, $monitorID, $actividades, $monitores);
                } else {
                    // Generar precio aleatorio entre 100 y 1000
                    $precioMensual = rand(100, 1000) + (rand(0, 99) / 100);
                    $precioMensual = round($precioMensual, 2);

                    // Fecha de inscripción es la fecha actual
                    $fechaInscripcion = date('Y-m-d');

                    // Insertar inscripción
                    $query = "INSERT INTO Inscripciones (actividadID, socioID, monitorID, fechaInscripcion, precioMensual)
                              VALUES (" . intval($actividadID) . ", " . intval($socioID) . ", " . intval($monitorID) . ",
                                      '$fechaInscripcion', $precioMensual)";
                    $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

                    if($resultado){
                        $_SESSION['actividadID'] = $actividadID;
                        $_SESSION['monitorID'] = $monitorID;
                        $_SESSION['fechaInscripcion'] = $fechaInscripcion;
                        $_SESSION['precioMensual'] = $precioMensual;
                        header("Location: confirmacion.php");
                        exit();
                    } else {
                        mostrarAlerta("Error al registrar la inscripción");
                        $actividades = obtener_actividades_activas($conex);
                        $monitores = obtener_monitores($conex);
                        pintar_formulario($actividadID, $fechaInicio, $monitorID, $actividades, $monitores);
                    }
                }
            }
        }

        mysqli_close($conex);
        ?>
    </body>
</html>
