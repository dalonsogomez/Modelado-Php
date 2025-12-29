<?php
    session_start();

    if(!isset($_SESSION['clienteID']) || !isset($_SESSION['fechaEntrada'])){
        header("Location: index.php");
        exit();
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Hotel - Selección de Habitación</title>
    </head>
    <body>
        <?php
        include 'conexion_bd.php';

        // ========== DECLARACIÓN DE VARIABLES ==========
        $habitacionID = isset($_POST["habitacionID"]) ? $_POST["habitacionID"] : "";

        $clienteID = $_SESSION['clienteID'];
        $clienteNombre = $_SESSION['clienteNombre'];
        $clienteNIF = $_SESSION['clienteNIF'];
        $fechaEntrada = $_SESSION['fechaEntrada'];
        $fechaSalida = $_SESSION['fechaSalida'];
        $tipoHabitacionID = $_SESSION['tipoHabitacionID'];

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

        function obtener_habitaciones_disponibles($conex, $tipoHabitacionID, $fechaEntrada, $fechaSalida){
            // Habitaciones del tipo seleccionado que NO tengan reservas solapadas
            $query = "SELECT h.habitacionID, h.numeroHabitacion, t.descripcion, t.precioBase
                      FROM Habitaciones h
                      INNER JOIN TiposHabitacion t ON h.tipoHabitacionID = t.tipoHabitacionID
                      WHERE h.tipoHabitacionID = " . intval($tipoHabitacionID) . "
                      AND h.habitacionID NOT IN (
                          SELECT r.habitacionID FROM Reservas r
                          WHERE (r.fechaEntrada < '$fechaSalida' AND r.fechaSalida > '$fechaEntrada')
                      )
                      ORDER BY h.numeroHabitacion";
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return $resultado;
        }

        function obtener_tipo_habitacion($conex, $tipoHabitacionID){
            $query = "SELECT * FROM TiposHabitacion WHERE tipoHabitacionID = " . intval($tipoHabitacionID);
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return mysqli_fetch_array($resultado);
        }

        function pintar_formulario($habitaciones, $tipo){
            if(mysqli_num_rows($habitaciones) == 0){
                echo '<p style="color: red;"><strong>No hay habitaciones disponibles para las fechas seleccionadas.</strong></p>';
                echo '<p><a href="seleccion_fechas.php">Seleccionar otras fechas</a></p>';
                return;
            }

            $formulario1 = <<<FORM1
            <form action="seleccion_habitacion.php" method="post">
                <p>
                    Habitación Disponible:
                    <select name="habitacionID" required>
                        <option value="">-- Seleccione una habitación --</option>
FORM1;
            print $formulario1;

            while($hab = mysqli_fetch_array($habitaciones)){
                echo "<option value='" . $hab['habitacionID'] . "'>Hab. " . $hab['numeroHabitacion'] . " (" . $hab['descripcion'] . ")</option>";
            }

            $formulario2 = <<<FORM2
                    </select>
                </p>
                <p>
                    <input type="submit" value="Reservar">
                    <a href="seleccion_fechas.php">Cambiar fechas</a>
                </p>
            </form>
FORM2;
            print $formulario2;
        }

        // ========== EJECUCIÓN ==========

        echo '<h1>Sistema de Gestión Hotelera</h1>';
        echo '<h2>Pantalla 3: Selección de Habitación Disponible</h2>';
        echo '<hr>';
        echo "<p><strong>Cliente:</strong> $clienteNombre ($clienteNIF)</p>";

        $tipo = obtener_tipo_habitacion($conex, $tipoHabitacionID);
        echo "<p><strong>Tipo de Habitación:</strong> " . $tipo['descripcion'] . " (" . $tipo['precioBase'] . " €/noche)</p>";
        echo "<p><strong>Fechas:</strong> $fechaEntrada a $fechaSalida</p>";
        echo '<hr>';

        if(empty($_POST)){
            $habitaciones = obtener_habitaciones_disponibles($conex, $tipoHabitacionID, $fechaEntrada, $fechaSalida);
            pintar_formulario($habitaciones, $tipo);
        } else {
            if($habitacionID == "" || !is_numeric($habitacionID)){
                mostrarAlerta("Debe seleccionar una habitación");
                $habitaciones = obtener_habitaciones_disponibles($conex, $tipoHabitacionID, $fechaEntrada, $fechaSalida);
                pintar_formulario($habitaciones, $tipo);
            } else {
                // Calcular precio total
                $fecha1 = new DateTime($fechaEntrada);
                $fecha2 = new DateTime($fechaSalida);
                $diferencia = $fecha1->diff($fecha2);
                $noches = $diferencia->days;

                $precioBase = $tipo['precioBase'];
                $precioTotal = $precioBase * $noches;

                // Aplicar descuento aleatorio entre 10% y 30%
                $descuento = rand(10, 30);
                $importeDescuento = $precioTotal * ($descuento / 100);
                $importeTotal = round($precioTotal - $importeDescuento, 2);

                // Fecha de reserva es hoy
                $fechaReserva = date('Y-m-d');

                // Insertar reserva
                $query = "INSERT INTO Reservas (habitacionID, clienteID, fechaReserva, fechaEntrada, fechaSalida, importeTotal)
                          VALUES (" . intval($habitacionID) . ", " . intval($clienteID) . ", '$fechaReserva', '$fechaEntrada', '$fechaSalida', $importeTotal)";
                $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

                if($resultado){
                    $_SESSION['reservaID'] = mysqli_insert_id($conex);
                    $_SESSION['habitacionID'] = $habitacionID;
                    $_SESSION['noches'] = $noches;
                    $_SESSION['precioBase'] = $precioBase;
                    $_SESSION['precioTotal'] = $precioTotal;
                    $_SESSION['descuento'] = $descuento;
                    $_SESSION['importeTotal'] = $importeTotal;
                    $_SESSION['fechaReserva'] = $fechaReserva;
                    header("Location: confirmacion.php");
                    exit();
                } else {
                    mostrarAlerta("Error al registrar la reserva");
                    $habitaciones = obtener_habitaciones_disponibles($conex, $tipoHabitacionID, $fechaEntrada, $fechaSalida);
                    pintar_formulario($habitaciones, $tipo);
                }
            }
        }

        mysqli_close($conex);
        ?>
    </body>
</html>
