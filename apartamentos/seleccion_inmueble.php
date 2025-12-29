<?php
    session_start();

    if(!isset($_SESSION['usuarioID'])){
        header("Location: index.php");
        exit();
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Apartamentos Turísticos - Selección de Inmueble</title>
    </head>
    <body>
        <?php
        include 'conexion_bd.php';

        // ========== DECLARACIÓN DE VARIABLES ==========
        $inmuebleID = isset($_POST["inmuebleID"]) ? $_POST["inmuebleID"] : "";
        $fechaEntrada = isset($_POST["fechaEntrada"]) ? $_POST["fechaEntrada"] : "";
        $fechaSalida = isset($_POST["fechaSalida"]) ? $_POST["fechaSalida"] : "";

        $usuarioID = $_SESSION['usuarioID'];
        $usuarioNombre = $_SESSION['usuarioNombre'];
        $usuarioNIF = $_SESSION['usuarioNIF'];

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

        function obtener_inmuebles($conex){
            $query = "SELECT ID_Inmueble, nombre, direccion, num_habitaciones, precio FROM Inmueble ORDER BY nombre";
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return $resultado;
        }

        function pintar_formulario($inmuebleID, $fechaEntrada, $fechaSalida, $inmuebles){
            $fecha_minima = date('Y-m-d', strtotime('+1 day'));

            $formulario1 = <<<FORM1
            <form action="seleccion_inmueble.php" method="post">
                <p>
                    Inmueble:
                    <select name="inmuebleID" required>
                        <option value="">-- Seleccione un inmueble --</option>
FORM1;
            print $formulario1;

            while($inmueble = mysqli_fetch_array($inmuebles)){
                $selected = ($inmuebleID == $inmueble['ID_Inmueble']) ? "selected" : "";
                echo "<option value='" . $inmueble['ID_Inmueble'] . "' $selected>" . $inmueble['nombre'] . " (" . $inmueble['ID_Inmueble'] . ")</option>";
            }

            $formulario2 = <<<FORM2
                    </select>
                </p>
                <p>
                    Fecha de Entrada:
                    <input type="date" name="fechaEntrada" min="$fecha_minima" value="$fechaEntrada" required>
                    <small>(Posterior a la fecha actual)</small>
                </p>
                <p>
                    Fecha de Salida:
                    <input type="date" name="fechaSalida" min="$fecha_minima" value="$fechaSalida" required>
                    <small>(Posterior a la fecha de entrada)</small>
                </p>
                <p>
                    <input type="submit" value="Realizar Reserva">
                    <a href="index.php">Volver</a>
                </p>
            </form>
FORM2;
            print $formulario2;
        }

        function validar_reserva(&$inmuebleID, &$fechaEntrada, &$fechaSalida, &$errores, $conex){
            $flag = true;
            $fecha_actual = date('Y-m-d');

            // Validar inmueble
            if($inmuebleID == "" || !is_numeric($inmuebleID)){
                $errores .= " / Debe seleccionar un inmueble";
                $flag = false;
            } else {
                $query = "SELECT ID_Inmueble FROM Inmueble WHERE ID_Inmueble = " . intval($inmuebleID);
                $resultado = mysqli_query($conex, $query);
                if(mysqli_num_rows($resultado) == 0){
                    $errores .= " / Inmueble no válido";
                    $flag = false;
                }
            }

            // Validar fecha de entrada (mayor a fecha actual)
            if($fechaEntrada == ""){
                $errores .= " / Debe seleccionar fecha de entrada";
                $flag = false;
            } elseif($fechaEntrada <= $fecha_actual){
                $errores .= " / La fecha de entrada debe ser posterior a hoy";
                $flag = false;
            }

            // Validar fecha de salida (mayor a fecha de entrada)
            if($fechaSalida == ""){
                $errores .= " / Debe seleccionar fecha de salida";
                $flag = false;
            } elseif($fechaSalida <= $fechaEntrada){
                $errores .= " / La fecha de salida debe ser posterior a la de entrada";
                $flag = false;
            }

            return $flag;
        }

        function obtener_inmueble($conex, $inmuebleID){
            $query = "SELECT * FROM Inmueble WHERE ID_Inmueble = " . intval($inmuebleID);
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return mysqli_fetch_array($resultado);
        }

        // ========== EJECUCIÓN ==========

        echo '<h1>Sistema de Reservas de Apartamentos Turísticos</h1>';
        echo '<h2>Pantalla 2: Selección de Inmueble y Fechas</h2>';
        echo '<hr>';
        echo "<p><strong>Cliente:</strong> $usuarioNombre ($usuarioNIF)</p>";
        echo '<hr>';

        if(empty($_POST)){
            $inmuebles = obtener_inmuebles($conex);
            pintar_formulario($inmuebleID, $fechaEntrada, $fechaSalida, $inmuebles);
        } else {
            $errores = "";

            if(!validar_reserva($inmuebleID, $fechaEntrada, $fechaSalida, $errores, $conex)){
                mostrarAlerta($errores);
                $inmuebles = obtener_inmuebles($conex);
                pintar_formulario($inmuebleID, $fechaEntrada, $fechaSalida, $inmuebles);
            } else {
                // Obtener datos del inmueble
                $inmueble = obtener_inmueble($conex, $inmuebleID);

                // Calcular precio total
                $fecha1 = new DateTime($fechaEntrada);
                $fecha2 = new DateTime($fechaSalida);
                $diferencia = $fecha1->diff($fecha2);
                $noches = $diferencia->days;
                $precioTotal = $inmueble['precio'] * $noches;

                // Insertar reserva
                $query = "INSERT INTO Reserva (ID_inmueble, ID_usuario, fecha_entrada, fecha_salida)
                          VALUES (" . intval($inmuebleID) . ", " . intval($usuarioID) . ", '$fechaEntrada', '$fechaSalida')";
                $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

                if($resultado){
                    $_SESSION['reservaID'] = mysqli_insert_id($conex);
                    $_SESSION['inmuebleID'] = $inmuebleID;
                    $_SESSION['fechaEntrada'] = $fechaEntrada;
                    $_SESSION['fechaSalida'] = $fechaSalida;
                    $_SESSION['noches'] = $noches;
                    $_SESSION['precioTotal'] = $precioTotal;
                    header("Location: confirmacion.php");
                    exit();
                } else {
                    mostrarAlerta("Error al registrar la reserva");
                    $inmuebles = obtener_inmuebles($conex);
                    pintar_formulario($inmuebleID, $fechaEntrada, $fechaSalida, $inmuebles);
                }
            }
        }

        mysqli_close($conex);
        ?>
    </body>
</html>
