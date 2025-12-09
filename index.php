<?php
    session_start();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        
        function pintar_formulario(){
            $formulario = <<<FORMULARIO
                <form action="index.php" method="post">
                    <h1> Iniciar sesión </h1>
                    <p>
                        Nombre de usuario: 
                        <input type="text" name="user">
                    </p>

                    <p>
                        Contraseña: 
                        <input type="password" name="pwd">
                    </p>

                    <p>
                    <input type="submit" name="envio" value="Iniciar sesion">
                    <a href="form_alta.php">Registrar nuevo alumno </a>
                    </p>
                </form>
FORMULARIO;
            
            print $formulario;
        }
        
        if (empty($_POST)){
            pintar_formulario();
        } else {
            include 'conexion_bd.php';
            
            $user = isset($_POST['user']) ? $_POST['user'] : "";
            $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : "";
            
            
            $query = "SELECT expediente, usuario, clave, nombre, origen FROM alumnos"
                    . " WHERE (usuario = '$user') and (clave = '$pwd')";
            
            $res_valid = mysqli_query($conex, $query) or die (mysqli_error($conex));
            
            
            
            if((mysqli_num_rows($res_valid) == 0) || !$user || !$pwd){
                echo "Las credenciales introducidad no son válidas";
                pintar_formulario();
            } else {
                $reg_usuario = mysqli_fetch_array($res_valid);
                
                $_SESSION['nombre'] = $reg_usuario['nombre'];
                $_SESSION['expediente'] = $reg_usuario['expediente'];
                $_SESSION['usuario'] = $reg_usuario['usuario'];
                $_SESSION['origen'] = $reg_usuario['origen'];
                
                header("location: menu_principal.php");
                exit();
            }
        }
        
                
                
        ?>
    </body>
</html>
