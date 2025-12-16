<html>
<head>
    <meta charset="UTF-8">
    <title>Reserva de Apartamentos - Paso 1</title>
</head>
<body>
    <h1>Paso 1: Seleccionar Cliente</h1>
    
    <?php
    include 'conexion_apartamentos.php';

    // Consultar la lista de usuarios
    $query_usuarios = "SELECT ID_Usuario, nombre FROM Usuario ORDER BY nombre ASC";
    $res_usuarios = mysqli_query($conex, $query_usuarios) or die (mysqli_error($conex));

    if (mysqli_num_rows($res_usuarios) == 0) {
        echo "<p>No hay clientes registrados en la base de datos.</p>";
    } else {
        // Pintar el formulario
        $formulario = <<<FORMULARIO
            <form action="reservas_paso2.php" method="post">
                <p>
                    Seleccione un cliente:
                    <select name="id_usuario">
FORMULARIO;
        print $formulario;

        while ($usuario = mysqli_fetch_assoc($res_usuarios)) {
            echo "<option value=\"{$usuario['ID_Usuario']}\">{$usuario['nombre']}</option>";
        }

        $formulario_fin = <<<FORMULARIO_FIN
                    </select>
                </p>
                <p>
                    <input type="submit" value="Siguiente">
                </p>
            </form>
FORMULARIO_FIN;
        print $formulario_fin;
    }
    ?>
</body>
</html>
