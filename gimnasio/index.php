<?php
    session_start();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gimnasio - Identificación de Socio</title>
    </head>
    <body>
        <?php
        include 'conexion_bd.php';

        // ========== DECLARACIÓN DE VARIABLES ==========
        $nif = isset($_POST["nif"]) ? $_POST["nif"] : "";
        $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
        $telefono = isset($_POST["telefono"]) ? $_POST["telefono"] : "";
        $email = isset($_POST["email"]) ? $_POST["email"] : "";
        $socio_existente = isset($_POST["socio_existente"]) ? $_POST["socio_existente"] : "";
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

        function obtener_socios($conex){
            $query = "SELECT socioID, nif, nombre FROM Socio ORDER BY nombre";
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return $resultado;
        }

        function pintar_formulario_registro($nif, $nombre, $telefono, $email){
            $formulario = <<<FORM
            <h2>Opción 1: Registrar Nuevo Socio</h2>
            <form action="index.php" method="post">
                <input type="hidden" name="accion" value="registro">
                <p>
                    NIF:
                    <input type="text" name="nif" size="9" maxlength="9" value="$nif" required>
                    <small>(Formato: 12345678A)</small>
                </p>
                <p>
                    Nombre:
                    <input type="text" name="nombre" size="30" maxlength="50" value="$nombre" required>
                </p>
                <p>
                    Teléfono:
                    <input type="text" name="telefono" size="9" maxlength="9" value="$telefono">
                </p>
                <p>
                    Email:
                    <input type="email" name="email" size="30" maxlength="50" value="$email">
                </p>
                <p>
                    <input type="submit" value="Registrar y Continuar">
                </p>
            </form>
FORM;
            print $formulario;
        }

        function pintar_formulario_seleccion($socios){
            $formulario1 = <<<FORM1
            <h2>Opción 2: Seleccionar Socio Existente</h2>
            <form action="index.php" method="post">
                <input type="hidden" name="accion" value="seleccion">
                <p>
                    Seleccione un socio:
                    <select name="socio_existente" required>
                        <option value="">-- Seleccione --</option>
FORM1;
            print $formulario1;

            while($socio = mysqli_fetch_array($socios)){
                echo "<option value='" . $socio['socioID'] . "'>" . $socio['nombre'] . " (" . $socio['nif'] . ")</option>";
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

        function validar_registro(&$nif, &$nombre, &$telefono, &$email, &$errores){
            $flag = true;

            // Validar NIF (8 dígitos + 1 letra)
            if(($nif == "") || (!preg_match("/^[0-9]{8}[A-Za-z]$/", $nif))){
                $nif = "";
                $errores .= " / NIF inválido (formato: 12345678A)";
                $flag = false;
            } else {
                $nif = strtoupper($nif);
            }

            // Validar nombre
            if($nombre == ""){
                $errores .= " / El nombre no puede estar vacío";
                $flag = false;
            } else {
                $nombre = addslashes($nombre);
            }

            // Validar teléfono (9 dígitos)
            if($telefono != "" && !preg_match("/^[0-9]{9}$/", $telefono)){
                $errores .= " / Teléfono inválido (9 dígitos)";
                $flag = false;
            }

            // Validar email
            if($email != "" && !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)){
                $errores .= " / Email inválido";
                $flag = false;
            }

            return $flag;
        }

        function verificar_nif_existente($conex, $nif){
            $query = "SELECT socioID FROM Socio WHERE nif = '$nif'";
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return mysqli_num_rows($resultado) > 0;
        }

        // ========== EJECUCIÓN ==========

        echo '<h1>Sistema de Gestión de Gimnasio</h1>';
        echo '<h2>Pantalla 1: Identificación del Socio</h2>';
        echo '<hr>';

        if(empty($_POST)){
            // Primera carga - mostrar formularios
            pintar_formulario_registro($nif, $nombre, $telefono, $email);
            echo '<hr>';
            $socios = obtener_socios($conex);
            pintar_formulario_seleccion($socios);
        } else {
            if($accion == "registro"){
                // Procesar registro de nuevo socio
                $errores = "";

                if(!validar_registro($nif, $nombre, $telefono, $email, $errores)){
                    mostrarAlerta($errores);
                    pintar_formulario_registro($nif, $nombre, $telefono, $email);
                    echo '<hr>';
                    $socios = obtener_socios($conex);
                    pintar_formulario_seleccion($socios);
                } else {
                    // Verificar si el NIF ya existe
                    if(verificar_nif_existente($conex, $nif)){
                        mostrarAlerta("El NIF ya está registrado en el sistema");
                        pintar_formulario_registro($nif, $nombre, $telefono, $email);
                        echo '<hr>';
                        $socios = obtener_socios($conex);
                        pintar_formulario_seleccion($socios);
                    } else {
                        // Insertar nuevo socio
                        $query = "INSERT INTO Socio (nif, nombre, telefono, email) VALUES ('$nif', '$nombre', '$telefono', '$email')";
                        $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

                        if($resultado){
                            $socioID = mysqli_insert_id($conex);
                            $_SESSION['socioID'] = $socioID;
                            $_SESSION['socioNombre'] = $nombre;
                            $_SESSION['socioNIF'] = $nif;
                            header("Location: seleccion_actividad.php");
                            exit();
                        } else {
                            mostrarAlerta("Error al registrar el socio");
                            pintar_formulario_registro($nif, $nombre, $telefono, $email);
                            echo '<hr>';
                            $socios = obtener_socios($conex);
                            pintar_formulario_seleccion($socios);
                        }
                    }
                }
            } elseif($accion == "seleccion"){
                // Procesar selección de socio existente
                if($socio_existente == ""){
                    mostrarAlerta("Debe seleccionar un socio");
                    pintar_formulario_registro($nif, $nombre, $telefono, $email);
                    echo '<hr>';
                    $socios = obtener_socios($conex);
                    pintar_formulario_seleccion($socios);
                } else {
                    // Obtener datos del socio seleccionado
                    $query = "SELECT socioID, nombre, nif FROM Socio WHERE socioID = " . intval($socio_existente);
                    $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
                    $socio = mysqli_fetch_array($resultado);

                    $_SESSION['socioID'] = $socio['socioID'];
                    $_SESSION['socioNombre'] = $socio['nombre'];
                    $_SESSION['socioNIF'] = $socio['nif'];
                    header("Location: seleccion_actividad.php");
                    exit();
                }
            }
        }

        mysqli_close($conex);
        ?>
    </body>
</html>
