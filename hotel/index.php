<?php
    session_start();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Hotel - Identificación de Cliente</title>
    </head>
    <body>
        <?php
        include 'conexion_bd.php';

        // ========== DECLARACIÓN DE VARIABLES ==========
        // Variables de registro
        $reg_nif = isset($_POST["reg_nif"]) ? $_POST["reg_nif"] : "";
        $reg_nombre = isset($_POST["reg_nombre"]) ? $_POST["reg_nombre"] : "";
        $reg_telefono = isset($_POST["reg_telefono"]) ? $_POST["reg_telefono"] : "";
        $reg_email = isset($_POST["reg_email"]) ? $_POST["reg_email"] : "";
        $reg_direccion = isset($_POST["reg_direccion"]) ? $_POST["reg_direccion"] : "";
        $reg_tarjeta = isset($_POST["reg_tarjeta"]) ? $_POST["reg_tarjeta"] : "";
        $reg_contrasena = isset($_POST["reg_contrasena"]) ? $_POST["reg_contrasena"] : "";
        $reg_contrasena2 = isset($_POST["reg_contrasena2"]) ? $_POST["reg_contrasena2"] : "";

        // Variables de login
        $login_nif = isset($_POST["login_nif"]) ? $_POST["login_nif"] : "";
        $login_contrasena = isset($_POST["login_contrasena"]) ? $_POST["login_contrasena"] : "";

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

        function pintar_formulario_registro($nif, $nombre, $telefono, $email, $direccion, $tarjeta){
            $formulario = <<<FORM
            <h2>Opción 1: Registrar Nuevo Cliente</h2>
            <form action="index.php" method="post">
                <input type="hidden" name="accion" value="registro">
                <p>
                    NIF:
                    <input type="text" name="reg_nif" size="9" maxlength="9" value="$nif" required>
                    <small>(Formato: 12345678A)</small>
                </p>
                <p>
                    Nombre:
                    <input type="text" name="reg_nombre" size="30" maxlength="50" value="$nombre" required>
                </p>
                <p>
                    Teléfono:
                    <input type="text" name="reg_telefono" size="9" maxlength="9" value="$telefono">
                </p>
                <p>
                    Email:
                    <input type="email" name="reg_email" size="30" maxlength="50" value="$email">
                </p>
                <p>
                    Dirección:
                    <input type="text" name="reg_direccion" size="40" maxlength="50" value="$direccion">
                </p>
                <p>
                    Tarjeta de Crédito:
                    <input type="text" name="reg_tarjeta" size="20" maxlength="20" value="$tarjeta">
                </p>
                <p>
                    Contraseña:
                    <input type="password" name="reg_contrasena" size="20" required>
                </p>
                <p>
                    Repetir Contraseña:
                    <input type="password" name="reg_contrasena2" size="20" required>
                </p>
                <p>
                    <input type="submit" value="Registrar y Continuar">
                </p>
            </form>
FORM;
            print $formulario;
        }

        function pintar_formulario_login($nif){
            $formulario = <<<FORM
            <h2>Opción 2: Iniciar Sesión</h2>
            <form action="index.php" method="post">
                <input type="hidden" name="accion" value="login">
                <p>
                    NIF:
                    <input type="text" name="login_nif" size="9" maxlength="9" value="$nif" required>
                </p>
                <p>
                    Contraseña:
                    <input type="password" name="login_contrasena" size="20" required>
                </p>
                <p>
                    <input type="submit" value="Iniciar Sesión">
                </p>
            </form>
FORM;
            print $formulario;
        }

        function validar_registro(&$nif, &$nombre, &$telefono, &$email, &$direccion, &$tarjeta, $contrasena, $contrasena2, &$errores){
            $flag = true;

            // Validar NIF
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

            // Validar teléfono
            if($telefono != "" && !preg_match("/^[0-9]{9}$/", $telefono)){
                $errores .= " / Teléfono inválido (9 dígitos)";
                $flag = false;
            }

            // Validar email
            if($email != "" && !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)){
                $errores .= " / Email inválido";
                $flag = false;
            }

            // Validar contraseña
            if($contrasena == "" || $contrasena2 == ""){
                $errores .= " / Debe introducir la contraseña";
                $flag = false;
            } elseif($contrasena != $contrasena2){
                $errores .= " / Las contraseñas no coinciden";
                $flag = false;
            } elseif(strlen($contrasena) < 6){
                $errores .= " / La contraseña debe tener al menos 6 caracteres";
                $flag = false;
            }

            return $flag;
        }

        function verificar_nif_existente($conex, $nif){
            $query = "SELECT clienteID FROM Clientes WHERE nif = '$nif'";
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
            return mysqli_num_rows($resultado) > 0;
        }

        function validar_login($conex, $nif, $contrasena, &$cliente){
            $query = "SELECT * FROM Clientes WHERE nif = '$nif'";
            $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

            if(mysqli_num_rows($resultado) == 0){
                return false;
            }

            $cliente = mysqli_fetch_array($resultado);

            // Verificar contraseña hasheada
            if(password_verify($contrasena, $cliente['contrasena'])){
                return true;
            }

            return false;
        }

        // ========== EJECUCIÓN ==========

        echo '<h1>Sistema de Gestión Hotelera</h1>';
        echo '<h2>Pantalla 1: Identificación del Cliente</h2>';
        echo '<hr>';

        if(empty($_POST)){
            pintar_formulario_registro($reg_nif, $reg_nombre, $reg_telefono, $reg_email, $reg_direccion, $reg_tarjeta);
            echo '<hr>';
            pintar_formulario_login($login_nif);
        } else {
            if($accion == "registro"){
                $errores = "";

                if(!validar_registro($reg_nif, $reg_nombre, $reg_telefono, $reg_email, $reg_direccion, $reg_tarjeta, $reg_contrasena, $reg_contrasena2, $errores)){
                    mostrarAlerta($errores);
                    pintar_formulario_registro($reg_nif, $reg_nombre, $reg_telefono, $reg_email, $reg_direccion, $reg_tarjeta);
                    echo '<hr>';
                    pintar_formulario_login($login_nif);
                } else {
                    if(verificar_nif_existente($conex, $reg_nif)){
                        mostrarAlerta("El NIF ya está registrado en el sistema");
                        pintar_formulario_registro($reg_nif, $reg_nombre, $reg_telefono, $reg_email, $reg_direccion, $reg_tarjeta);
                        echo '<hr>';
                        pintar_formulario_login($login_nif);
                    } else {
                        // Hashear contraseña
                        $contrasena_hash = password_hash($reg_contrasena, PASSWORD_DEFAULT);

                        $query = "INSERT INTO Clientes (nif, nombre, telefono, email, direccion, tarjetaCredito, contrasena)
                                  VALUES ('$reg_nif', '$reg_nombre', '$reg_telefono', '$reg_email', '$reg_direccion', '$reg_tarjeta', '$contrasena_hash')";
                        $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

                        if($resultado){
                            $clienteID = mysqli_insert_id($conex);
                            $_SESSION['clienteID'] = $clienteID;
                            $_SESSION['clienteNombre'] = $reg_nombre;
                            $_SESSION['clienteNIF'] = $reg_nif;
                            header("Location: seleccion_fechas.php");
                            exit();
                        } else {
                            mostrarAlerta("Error al registrar el cliente");
                            pintar_formulario_registro($reg_nif, $reg_nombre, $reg_telefono, $reg_email, $reg_direccion, $reg_tarjeta);
                            echo '<hr>';
                            pintar_formulario_login($login_nif);
                        }
                    }
                }
            } elseif($accion == "login"){
                if($login_nif == "" || $login_contrasena == ""){
                    mostrarAlerta("Debe introducir NIF y contraseña");
                    pintar_formulario_registro($reg_nif, $reg_nombre, $reg_telefono, $reg_email, $reg_direccion, $reg_tarjeta);
                    echo '<hr>';
                    pintar_formulario_login($login_nif);
                } else {
                    $cliente = null;
                    if(validar_login($conex, $login_nif, $login_contrasena, $cliente)){
                        $_SESSION['clienteID'] = $cliente['clienteID'];
                        $_SESSION['clienteNombre'] = $cliente['nombre'];
                        $_SESSION['clienteNIF'] = $cliente['nif'];
                        header("Location: seleccion_fechas.php");
                        exit();
                    } else {
                        mostrarAlerta("NIF o contraseña incorrectos");
                        pintar_formulario_registro($reg_nif, $reg_nombre, $reg_telefono, $reg_email, $reg_direccion, $reg_tarjeta);
                        echo '<hr>';
                        pintar_formulario_login($login_nif);
                    }
                }
            }
        }

        mysqli_close($conex);
        ?>
    </body>
</html>
