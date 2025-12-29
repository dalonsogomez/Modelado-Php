<?php
    session_start();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Apartamentos Turísticos - Selección de Cliente</title>
    </head>
    <body>
        <?php
        include 'conexion_bd.php';

        // ========== DECLARACIÓN DE VARIABLES ==========
        $usuarioID = isset($_POST["usuarioID"]) ? $_POST["usuarioID"] : "";

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

        function obtener_usuarios($conex){
            $query = "SELECT ID_Usuario, NIF, nombre FROM Usuario ORDER BY nombre";
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return $resultado;
        }

        function pintar_formulario($usuarios){
            $formulario1 = <<<FORM1
            <form action="index.php" method="post">
                <p>
                    Seleccione un cliente:
                    <select name="usuarioID" required>
                        <option value="">-- Seleccione --</option>
FORM1;
            print $formulario1;

            while($usuario = mysqli_fetch_array($usuarios)){
                echo "<option value='" . $usuario['ID_Usuario'] . "'>" . $usuario['nombre'] . "</option>";
            }

            $formulario2 = <<<FORM2
                    </select>
                </p>
                <p>
                    <input type="submit" value="Continuar">
                </p>
            </form>
FORM2;
            print $formulario2;
        }

        // ========== EJECUCIÓN ==========

        echo '<h1>Sistema de Reservas de Apartamentos Turísticos</h1>';
        echo '<h2>Pantalla 1: Selección de Cliente</h2>';
        echo '<hr>';

        if(empty($_POST)){
            $usuarios = obtener_usuarios($conex);
            pintar_formulario($usuarios);
        } else {
            if($usuarioID == "" || !is_numeric($usuarioID)){
                mostrarAlerta("Debe seleccionar un cliente");
                $usuarios = obtener_usuarios($conex);
                pintar_formulario($usuarios);
            } else {
                // Obtener datos del usuario
                $query = "SELECT * FROM Usuario WHERE ID_Usuario = " . intval($usuarioID);
                $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

                if(mysqli_num_rows($resultado) > 0){
                    $usuario = mysqli_fetch_array($resultado);
                    $_SESSION['usuarioID'] = $usuario['ID_Usuario'];
                    $_SESSION['usuarioNombre'] = $usuario['nombre'];
                    $_SESSION['usuarioNIF'] = $usuario['NIF'];
                    header("Location: seleccion_inmueble.php");
                    exit();
                } else {
                    mostrarAlerta("Cliente no encontrado");
                    $usuarios = obtener_usuarios($conex);
                    pintar_formulario($usuarios);
                }
            }
        }

        mysqli_close($conex);
        ?>
    </body>
</html>
