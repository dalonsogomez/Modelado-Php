<?php
    session_start();

    if(!isset($_SESSION['clienteID'])){
        header("Location: index.php");
        exit();
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Hotel - Selección de Fechas y Tipo</title>
    </head>
    <body>
        <?php
        include 'conexion_bd.php';

        // ========== DECLARACIÓN DE VARIABLES ==========
        $fechaEntrada = isset($_POST["fechaEntrada"]) ? $_POST["fechaEntrada"] : "";
        $fechaSalida = isset($_POST["fechaSalida"]) ? $_POST["fechaSalida"] : "";
        $tipoHabitacionID = isset($_POST["tipoHabitacionID"]) ? $_POST["tipoHabitacionID"] : "";

        $clienteID = $_SESSION['clienteID'];
        $clienteNombre = $_SESSION['clienteNombre'];
        $clienteNIF = $_SESSION['clienteNIF'];

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

        function obtener_tipos_habitacion($conex){
            $query = "SELECT tipoHabitacionID, descripcion, precioBase FROM TiposHabitacion ORDER BY descripcion";
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return $resultado;
        }

        function pintar_formulario($fechaEntrada, $fechaSalida, $tipoHabitacionID, $tipos){
            $fecha_minima = date('Y-m-d');

            $formulario1 = <<<FORM1
            <form action="seleccion_fechas.php" method="post">
                <p>
                    Fecha de Entrada:
                    <input type="date" name="fechaEntrada" min="$fecha_minima" value="$fechaEntrada" required>
                    <small>(Igual o posterior a hoy)</small>
                </p>
                <p>
                    Fecha de Salida:
                    <input type="date" name="fechaSalida" min="$fecha_minima" value="$fechaSalida" required>
                    <small>(Posterior a la fecha de entrada)</small>
                </p>
                <p>
                    Tipo de Habitación:
                    <select name="tipoHabitacionID" required>
                        <option value="">-- Seleccione un tipo --</option>
FORM1;
            print $formulario1;

            while($tipo = mysqli_fetch_array($tipos)){
                $selected = ($tipoHabitacionID == $tipo['tipoHabitacionID']) ? "selected" : "";
                echo "<option value='" . $tipo['tipoHabitacionID'] . "' $selected>" . $tipo['descripcion'] . " (" . $tipo['precioBase'] . " €/noche)</option>";
            }

            $formulario2 = <<<FORM2
                    </select>
                </p>
                <p>
                    <input type="submit" value="Buscar Habitaciones Disponibles">
                    <a href="index.php">Volver</a>
                </p>
            </form>
FORM2;
            print $formulario2;
        }

        function validar_fechas(&$fechaEntrada, &$fechaSalida, &$tipoHabitacionID, &$errores, $conex){
            $flag = true;
            $fecha_actual = date('Y-m-d');

            // Validar fecha de entrada
            if($fechaEntrada == ""){
                $errores .= " / Debe seleccionar fecha de entrada";
                $flag = false;
            } elseif($fechaEntrada < $fecha_actual){
                $errores .= " / La fecha de entrada debe ser igual o posterior a hoy";
                $flag = false;
            }

            // Validar fecha de salida
            if($fechaSalida == ""){
                $errores .= " / Debe seleccionar fecha de salida";
                $flag = false;
            } elseif($fechaSalida <= $fechaEntrada){
                $errores .= " / La fecha de salida debe ser posterior a la de entrada";
                $flag = false;
            }

            // Validar tipo de habitación
            if($tipoHabitacionID == "" || !is_numeric($tipoHabitacionID)){
                $errores .= " / Debe seleccionar un tipo de habitación";
                $flag = false;
            } else {
                $query = "SELECT tipoHabitacionID FROM TiposHabitacion WHERE tipoHabitacionID = " . intval($tipoHabitacionID);
                $resultado = mysqli_query($conex, $query);
                if(mysqli_num_rows($resultado) == 0){
                    $errores .= " / Tipo de habitación inválido";
                    $flag = false;
                }
            }

            return $flag;
        }

        // ========== EJECUCIÓN ==========

        echo '<h1>Sistema de Gestión Hotelera</h1>';
        echo '<h2>Pantalla 2: Selección de Fechas y Tipo de Habitación</h2>';
        echo '<hr>';
        echo "<p><strong>Cliente:</strong> $clienteNombre ($clienteNIF)</p>";
        echo '<hr>';

        if(empty($_POST)){
            $tipos = obtener_tipos_habitacion($conex);
            pintar_formulario($fechaEntrada, $fechaSalida, $tipoHabitacionID, $tipos);
        } else {
            $errores = "";

            if(!validar_fechas($fechaEntrada, $fechaSalida, $tipoHabitacionID, $errores, $conex)){
                mostrarAlerta($errores);
                $tipos = obtener_tipos_habitacion($conex);
                pintar_formulario($fechaEntrada, $fechaSalida, $tipoHabitacionID, $tipos);
            } else {
                // Guardar en sesión y pasar a selección de habitación
                $_SESSION['fechaEntrada'] = $fechaEntrada;
                $_SESSION['fechaSalida'] = $fechaSalida;
                $_SESSION['tipoHabitacionID'] = $tipoHabitacionID;
                header("Location: seleccion_habitacion.php");
                exit();
            }
        }

        mysqli_close($conex);
        ?>
    </body>
</html>
