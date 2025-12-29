<?php
    session_start();

    if(!isset($_SESSION['clienteID']) || !isset($_SESSION['reservaID'])){
        header("Location: index.php");
        exit();
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Hotel - Confirmación de Reserva</title>
    </head>
    <body>
        <?php
        include 'conexion_bd.php';

        // ========== OBTENER DATOS DE SESIÓN ==========
        $clienteID = $_SESSION['clienteID'];
        $reservaID = $_SESSION['reservaID'];
        $habitacionID = $_SESSION['habitacionID'];
        $fechaEntrada = $_SESSION['fechaEntrada'];
        $fechaSalida = $_SESSION['fechaSalida'];
        $noches = $_SESSION['noches'];
        $precioBase = $_SESSION['precioBase'];
        $precioTotal = $_SESSION['precioTotal'];
        $descuento = $_SESSION['descuento'];
        $importeTotal = $_SESSION['importeTotal'];
        $fechaReserva = $_SESSION['fechaReserva'];

        // ========== DECLARACIÓN DE FUNCIONES ==========

        function obtener_datos_cliente($conex, $clienteID){
            $query = "SELECT * FROM Clientes WHERE clienteID = " . intval($clienteID);
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return mysqli_fetch_array($resultado);
        }

        function obtener_datos_habitacion($conex, $habitacionID){
            $query = "SELECT h.*, t.descripcion, t.precioBase
                      FROM Habitaciones h
                      INNER JOIN TiposHabitacion t ON h.tipoHabitacionID = t.tipoHabitacionID
                      WHERE h.habitacionID = " . intval($habitacionID);
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return mysqli_fetch_array($resultado);
        }

        function obtener_reservas_cliente($conex, $clienteID){
            $fecha_actual = date('Y-m-d');
            $query = "SELECT r.*, h.numeroHabitacion, t.descripcion
                      FROM Reservas r
                      INNER JOIN Habitaciones h ON r.habitacionID = h.habitacionID
                      INNER JOIN TiposHabitacion t ON h.tipoHabitacionID = t.tipoHabitacionID
                      WHERE r.clienteID = " . intval($clienteID) . "
                      AND r.fechaSalida >= '$fecha_actual'
                      ORDER BY r.fechaEntrada ASC";
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return $resultado;
        }

        // ========== EJECUCIÓN ==========

        echo '<h1>Sistema de Gestión Hotelera</h1>';
        echo '<h2>Pantalla 4: Confirmación de Reserva</h2>';
        echo '<hr>';

        $cliente = obtener_datos_cliente($conex, $clienteID);
        $habitacion = obtener_datos_habitacion($conex, $habitacionID);

        $confirmacion = <<<CONFIRMACION
        <div style="background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
            <h3>¡Reserva realizada con éxito!</h3>
            <p>Número de Reserva: <strong>$reservaID</strong></p>
        </div>

        <h3>Datos del Cliente</h3>
        <table border="1" cellpadding="10">
            <tr><td><strong>ID:</strong></td><td>{$cliente['clienteID']}</td></tr>
            <tr><td><strong>NIF:</strong></td><td>{$cliente['nif']}</td></tr>
            <tr><td><strong>Nombre:</strong></td><td>{$cliente['nombre']}</td></tr>
            <tr><td><strong>Teléfono:</strong></td><td>{$cliente['telefono']}</td></tr>
            <tr><td><strong>Email:</strong></td><td>{$cliente['email']}</td></tr>
            <tr><td><strong>Dirección:</strong></td><td>{$cliente['direccion']}</td></tr>
        </table>

        <h3>Datos de la Habitación</h3>
        <table border="1" cellpadding="10">
            <tr><td><strong>Número:</strong></td><td>{$habitacion['numeroHabitacion']}</td></tr>
            <tr><td><strong>Tipo:</strong></td><td>{$habitacion['descripcion']}</td></tr>
            <tr><td><strong>Precio Base:</strong></td><td>{$habitacion['precioBase']} €/noche</td></tr>
        </table>

        <h3>Datos de la Reserva</h3>
        <table border="1" cellpadding="10">
            <tr><td><strong>Fecha de Reserva:</strong></td><td>$fechaReserva</td></tr>
            <tr><td><strong>Fecha de Entrada:</strong></td><td>$fechaEntrada</td></tr>
            <tr><td><strong>Fecha de Salida:</strong></td><td>$fechaSalida</td></tr>
            <tr><td><strong>Número de Noches:</strong></td><td>$noches</td></tr>
            <tr><td><strong>Precio sin descuento:</strong></td><td>$precioTotal €</td></tr>
            <tr><td><strong>Descuento aplicado:</strong></td><td>$descuento%</td></tr>
            <tr><td><strong>IMPORTE TOTAL:</strong></td><td><strong>$importeTotal €</strong></td></tr>
        </table>
CONFIRMACION;
        print $confirmacion;

        echo '<hr>';
        echo '<h3>Listado de Reservas del Cliente (a partir de hoy)</h3>';

        $reservas = obtener_reservas_cliente($conex, $clienteID);

        if(mysqli_num_rows($reservas) > 0){
            echo '<table border="1" cellpadding="10">';
            echo '<tr>';
            echo '<th>ID Reserva</th>';
            echo '<th>Habitación</th>';
            echo '<th>Tipo</th>';
            echo '<th>Entrada</th>';
            echo '<th>Salida</th>';
            echo '<th>Importe Total</th>';
            echo '</tr>';

            while($reserva = mysqli_fetch_array($reservas)){
                echo '<tr>';
                echo '<td>' . $reserva['reservaID'] . '</td>';
                echo '<td>' . $reserva['numeroHabitacion'] . '</td>';
                echo '<td>' . $reserva['descripcion'] . '</td>';
                echo '<td>' . $reserva['fechaEntrada'] . '</td>';
                echo '<td>' . $reserva['fechaSalida'] . '</td>';
                echo '<td>' . $reserva['importeTotal'] . ' €</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p>No hay reservas futuras registradas.</p>';
        }

        echo '<hr>';
        echo '<p><a href="seleccion_fechas.php">Realizar otra reserva</a> | <a href="index.php">Cerrar sesión</a></p>';

        // Limpiar datos de reserva de la sesión
        unset($_SESSION['reservaID']);
        unset($_SESSION['habitacionID']);
        unset($_SESSION['fechaEntrada']);
        unset($_SESSION['fechaSalida']);
        unset($_SESSION['tipoHabitacionID']);
        unset($_SESSION['noches']);
        unset($_SESSION['precioBase']);
        unset($_SESSION['precioTotal']);
        unset($_SESSION['descuento']);
        unset($_SESSION['importeTotal']);
        unset($_SESSION['fechaReserva']);

        mysqli_close($conex);
        ?>
    </body>
</html>
