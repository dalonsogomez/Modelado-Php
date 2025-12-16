<html>
<head>
    <meta charset="UTF-8">
    <title>Reserva de Apartamentos - Paso 2</title>
</head>
<body>
    <h1>Paso 2: Seleccionar Inmueble y Fechas</h1>

    <?php
    include 'conexion_apartamentos.php';

    // Validar que se ha recibido un id_usuario
    if (!isset($_POST['id_usuario']) || !is_numeric($_POST['id_usuario'])) {
        echo "<p>Error: No se ha seleccionado un cliente válido. <a href='reservas_paso1.php'>Volver al paso 1</a>.</p>";
        exit();
    }

    $id_usuario = mysqli_real_escape_string($conex, $_POST['id_usuario']);

    // Obtener nombre del cliente
    $query_cliente = "SELECT nombre FROM Usuario WHERE ID_Usuario = $id_usuario";
    $res_cliente = mysqli_query($conex, $query_cliente) or die(mysqli_error($conex));
    if (mysqli_num_rows($res_cliente) == 0) {
        echo "<p>Error: El cliente seleccionado no existe. <a href='reservas_paso1.php'>Volver al paso 1</a>.</p>";
        exit();
    }
    $cliente = mysqli_fetch_assoc($res_cliente);
    $nombre_cliente = $cliente['nombre'];

    echo "<h2>Cliente: $nombre_cliente</h2>";

    // Obtener lista de inmuebles
    $query_inmuebles = "SELECT ID_Inmueble, nombre FROM Inmueble ORDER BY nombre ASC";
    $res_inmuebles = mysqli_query($conex, $query_inmuebles) or die(mysqli_error($conex));

    // Fecha actual para validación en el input date
    $fecha_actual = date('Y-m-d');

    // Pintar formulario
    $formulario = <<<FORMULARIO
        <form action="reservas_paso3.php" method="post">
            <input type="hidden" name="id_usuario" value="$id_usuario">
            
            <p>
                Seleccione un inmueble:
                <select name="id_inmueble">
FORMULARIO;
    print $formulario;

    while ($inmueble = mysqli_fetch_assoc($res_inmuebles)) {
        echo "<option value=\"{$inmueble['ID_Inmueble']}\">{$inmueble['nombre']} ({$inmueble['ID_Inmueble']})</option>";
    }

    $formulario_fin = <<<FORMULARIO_FIN
                </select>
            </p>
            
            <p>
                Fecha de entrada:
                <input type="date" name="fecha_entrada" min="$fecha_actual" required>
            </p>
            
            <p>
                Fecha de salida:
                <input type="date" name="fecha_salida" min="$fecha_actual" required>
            </p>
            
            <p>
                <input type="submit" value="Realizar Reserva">
            </p>
        </form>
FORMULARIO_FIN;
    print $formulario_fin;

    ?>
</body>
</html>
