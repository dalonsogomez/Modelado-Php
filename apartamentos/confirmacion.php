<?php
    session_start();

    if(!isset($_SESSION['usuarioID']) || !isset($_SESSION['reservaID'])){
        header("Location: index.php");
        exit();
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Apartamentos Turísticos - Confirmación</title>
    </head>
    <body>
        <?php
        include 'conexion_bd.php';

        // ========== OBTENER DATOS DE SESIÓN ==========
        $usuarioID = $_SESSION['usuarioID'];
        $usuarioNombre = $_SESSION['usuarioNombre'];
        $reservaID = $_SESSION['reservaID'];
        $inmuebleID = $_SESSION['inmuebleID'];
        $fechaEntrada = $_SESSION['fechaEntrada'];
        $fechaSalida = $_SESSION['fechaSalida'];
        $noches = $_SESSION['noches'];
        $precioTotal = $_SESSION['precioTotal'];

        // Variables del formulario de valoración
        $puntuacion = isset($_POST["puntuacion"]) ? $_POST["puntuacion"] : "";
        $comentario = isset($_POST["comentario"]) ? $_POST["comentario"] : "";
        $accion = isset($_POST["accion"]) ? $_POST["accion"] : "";

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

        function obtener_datos_usuario($conex, $usuarioID){
            $query = "SELECT * FROM Usuario WHERE ID_Usuario = " . intval($usuarioID);
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return mysqli_fetch_array($resultado);
        }

        function obtener_datos_inmueble($conex, $inmuebleID){
            $query = "SELECT * FROM Inmueble WHERE ID_Inmueble = " . intval($inmuebleID);
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return mysqli_fetch_array($resultado);
        }

        function obtener_reservas_anteriores($conex, $usuarioID, $reservaActual){
            $query = "SELECT r.*, i.nombre AS inmueble_nombre, i.direccion AS inmueble_direccion, i.precio
                      FROM Reserva r
                      INNER JOIN Inmueble i ON r.ID_inmueble = i.ID_Inmueble
                      WHERE r.ID_usuario = " . intval($usuarioID) . "
                      AND r.ID_Reserva != " . intval($reservaActual) . "
                      ORDER BY r.fecha_entrada DESC";
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return $resultado;
        }

        function verificar_valoracion_existente($conex, $reservaID){
            $query = "SELECT ID_resena FROM Comentario WHERE ID_reserva = " . intval($reservaID);
            $resultado = mysqli_query($conex, $query);
            return mysqli_num_rows($resultado) > 0;
        }

        function pintar_formulario_valoracion($reservaID){
            $formulario = <<<FORM
            <h3>Valorar la Atención Recibida</h3>
            <form action="confirmacion.php" method="post">
                <input type="hidden" name="accion" value="valorar">
                <p>
                    Puntuación (0-10):
                    <input type="number" name="puntuacion" min="0" max="10" step="0.1" required>
                </p>
                <p>
                    Comentario:
                    <textarea name="comentario" cols="50" rows="4" maxlength="200" placeholder="Escriba su valoración (máx. 200 caracteres)"></textarea>
                </p>
                <p>
                    <input type="submit" value="Enviar Valoración">
                </p>
            </form>
FORM;
            print $formulario;
        }

        function validar_valoracion(&$puntuacion, &$comentario, &$errores){
            $flag = true;

            // Validar puntuación
            if($puntuacion === "" || !is_numeric($puntuacion)){
                $errores .= " / Debe introducir una puntuación";
                $flag = false;
            } elseif($puntuacion < 0 || $puntuacion > 10){
                $errores .= " / La puntuación debe estar entre 0 y 10";
                $flag = false;
            }

            // Validar comentario (opcional pero con límite)
            if(strlen($comentario) > 200){
                $errores .= " / El comentario no puede superar 200 caracteres";
                $flag = false;
            } else {
                $comentario = addslashes($comentario);
            }

            return $flag;
        }

        // ========== EJECUCIÓN ==========

        echo '<h1>Sistema de Reservas de Apartamentos Turísticos</h1>';
        echo '<h2>Pantalla 3: Confirmación y Valoración</h2>';
        echo '<hr>';

        $usuario = obtener_datos_usuario($conex, $usuarioID);
        $inmueble = obtener_datos_inmueble($conex, $inmuebleID);

        // Mostrar información de la reserva
        $confirmacion = <<<CONFIRMACION
        <div style="background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
            <h3>¡Reserva realizada con éxito!</h3>
            <p>Número de Reserva: <strong>$reservaID</strong></p>
        </div>

        <h3>Información de la Reserva</h3>
        <table border="1" cellpadding="10">
            <tr><td><strong>Cliente:</strong></td><td>{$usuario['nombre']} ({$usuario['NIF']})</td></tr>
            <tr><td><strong>Inmueble:</strong></td><td>{$inmueble['nombre']}</td></tr>
            <tr><td><strong>Dirección:</strong></td><td>{$inmueble['direccion']}</td></tr>
            <tr><td><strong>Habitaciones:</strong></td><td>{$inmueble['num_habitaciones']}</td></tr>
            <tr><td><strong>Precio por noche:</strong></td><td>{$inmueble['precio']} €</td></tr>
            <tr><td><strong>Fecha de Entrada:</strong></td><td>$fechaEntrada</td></tr>
            <tr><td><strong>Fecha de Salida:</strong></td><td>$fechaSalida</td></tr>
            <tr><td><strong>Número de Noches:</strong></td><td>$noches</td></tr>
            <tr><td><strong>PRECIO TOTAL:</strong></td><td><strong>$precioTotal €</strong></td></tr>
        </table>
CONFIRMACION;
        print $confirmacion;

        // Procesar valoración si se envió
        if($accion == "valorar"){
            $errores = "";

            if(!validar_valoracion($puntuacion, $comentario, $errores)){
                mostrarAlerta($errores);
            } else {
                if(verificar_valoracion_existente($conex, $reservaID)){
                    mostrarAlerta("Ya existe una valoración para esta reserva");
                } else {
                    $query = "INSERT INTO Comentario (ID_reserva, puntuacion, comentario)
                              VALUES (" . intval($reservaID) . ", $puntuacion, '$comentario')";
                    $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

                    if($resultado){
                        echo '<div style="background-color: #cce5ff; padding: 10px; border: 1px solid #b8daff; margin: 10px 0;">';
                        echo '<p><strong>¡Valoración enviada correctamente!</strong></p>';
                        echo '<p>Puntuación: ' . $puntuacion . '/10</p>';
                        if($comentario != ""){
                            echo '<p>Comentario: ' . stripslashes($comentario) . '</p>';
                        }
                        echo '</div>';
                    } else {
                        mostrarAlerta("Error al guardar la valoración");
                    }
                }
            }
        }

        echo '<hr>';
        echo '<h3>Reservas Anteriores del Cliente</h3>';

        $reservas = obtener_reservas_anteriores($conex, $usuarioID, $reservaID);

        if(mysqli_num_rows($reservas) > 0){
            echo '<table border="1" cellpadding="10">';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>Inmueble</th>';
            echo '<th>Dirección</th>';
            echo '<th>Entrada</th>';
            echo '<th>Salida</th>';
            echo '<th>Precio/Noche</th>';
            echo '</tr>';

            while($reserva = mysqli_fetch_array($reservas)){
                echo '<tr>';
                echo '<td>' . $reserva['ID_Reserva'] . '</td>';
                echo '<td>' . $reserva['inmueble_nombre'] . '</td>';
                echo '<td>' . $reserva['inmueble_direccion'] . '</td>';
                echo '<td>' . $reserva['fecha_entrada'] . '</td>';
                echo '<td>' . $reserva['fecha_salida'] . '</td>';
                echo '<td>' . $reserva['precio'] . ' €</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p>No hay reservas anteriores registradas.</p>';
        }

        echo '<hr>';

        // Mostrar formulario de valoración si no existe
        if(!verificar_valoracion_existente($conex, $reservaID) && $accion != "valorar"){
            pintar_formulario_valoracion($reservaID);
        }

        echo '<hr>';
        echo '<p><a href="seleccion_inmueble.php">Realizar otra reserva</a> | <a href="index.php">Cambiar cliente</a></p>';

        mysqli_close($conex);
        ?>
    </body>
</html>
