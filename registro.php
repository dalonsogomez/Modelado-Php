<?php
    session_start();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Registro - Tienda de Libros</title>
    </head>
    <body>
        
        <?php
            $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
            $apellido = isset($_POST["apellido"]) ? $_POST["apellido"] : "";
            $email = isset($_POST["email"]) ? $_POST["email"] : "";
            $password = isset($_POST["password"]) ?  $_POST["password"] : "";
            $f_foto = isset($_FILES["f_foto"]) ?  $_FILES["f_foto"] : "";
        
            function mostrarAlerta($mensaje){
                $alerta = <<<ALERTA
                        <script>
                            var miAlerta = "$mensaje";
                            alert(miAlerta);
                        </script>
ALERTA;
                print $alerta;
            }

            function mostrar_formulario($nombre, $apellido, $email){
                $formulario = <<<FORM
                       <h1>Registro de Usuario - Tienda de Libros</h1>
                        <br>
                        <form action="registro.php" method="post" enctype="multipart/form-data">
                            <p>
                                Nombre:
                                <input type="text" name="nombre" size="30" maxlength="50" value="$nombre">
                            </p>

                            <p>
                                Apellido:
                                <input type="text" name="apellido" size="30" maxlength="50" value="$apellido">
                            </p>

                            <p>
                                Email:
                                <input type="email" name="email" size="30" value="$email">
                            </p>

                            <p>
                                Contraseña:
                                <input type="password" name="password" size="30">
                                <br><small>Mínimo 8 caracteres, incluir mayúscula, minúscula y número</small>
                            </p>

                            <p>
                                Foto de perfil:
                                <input type="file" name="f_foto" accept="image/*">
                            </p>
                        
                            <p>    
                                <input type="submit" value="Registrarse">
                            </p>
                        </form> 
FORM;
                print $formulario;
            }
            
            function cargar_imagen(){
                if($_FILES['f_foto']['error'] > 0){
                    echo ("Problema en la carga del archivo!");
                    return false; 
                } else {
                    if (!is_dir('./perfiles')){
                        mkdir("perfiles", 0777);
                    }
                    
                    if (is_uploaded_file($_FILES["f_foto"]["tmp_name"])){
                        // Validar extensión usando explode
                        $nombre_archivo = $_FILES["f_foto"]["name"];
                        $partes = explode(".", $nombre_archivo);
                        $extension = strtolower(end($partes));
                        
                        $extensiones_permitidas = array("jpg", "jpeg", "png", "gif");
                        
                        if (!in_array($extension, $extensiones_permitidas)){
                            echo ("El archivo debe ser una imagen (JPG, JPEG, PNG o GIF)!");
                            return false;
                        }
                        
                        $time = time();
                        $ruta_fich = './perfiles/' . $time . '-' . $nombre_archivo;
                        
                        if (move_uploaded_file($_FILES["f_foto"]["tmp_name"], $ruta_fich)){
                            echo 'Imagen subida correctamente';
                            $_SESSION["foto_perfil"] = $ruta_fich;
                            return true;
                        } else {
                            echo ("Problema en la carga del archivo!");
                            return false; 
                        }
                        
                    } else {
                        echo ("Problema en la carga del archivo!");
                        return false; 
                    }
                }
            }
            
            function validar_datos (&$nombre, &$apellido, &$email, &$password, &$errores, $f_foto){
                $flag = true;
                
                // Validar nombre
                if($nombre == ""){
                    $flag = false;
                    $errores .= " - El nombre no puede estar vacío ";
                    $nombre = "";
                }
                
                // Validar apellido
                if($apellido == ""){
                    $flag = false;
                    $errores .= " - El apellido no puede estar vacío ";
                    $apellido = "";
                }
                
                // Validar email
                if(($email == "") || (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email))){
                    $flag = false;
                    $errores .= " - El email introducido es incorrecto ";
                    $email = "";
                }
                
                // Validar contraseña: mínimo 8 caracteres, al menos una mayúscula, una minúscula y un número
                if(($password == "") || (strlen($password) < 8) || (!preg_match("/[a-z]/", $password)) || (!preg_match("/[A-Z]/", $password)) || (!preg_match("/[0-9]/", $password))){
                    $flag = false;
                    $errores . = " - La contraseña debe tener al menos 8 caracteres, incluir mayúscula, minúscula y número ";
                }
                
                // Validar foto
                if ($f_foto == "" || $_FILES["f_foto"]["error"] == 4){
                    $flag = false;
                    $errores .= " - Debe subir una foto de perfil ";
                } else {
                    // Función para subir el archivo
                    if(!cargar_imagen()){
                        $flag = false;
                        $errores .= " - No se ha podido subir la foto ";
                    }
                }

                return $flag;
            }
            
            
            if(empty($_POST)){
                mostrar_formulario($nombre, $apellido, $email);
            } else {
                
                $errores = "";
                
                if(validar_datos($nombre, $apellido, $email, $password, $errores, $f_foto)){
                    $_SESSION["nombre"] = $nombre;
                    $_SESSION["apellido"] = $apellido;
                    $_SESSION["email"] = $email;
                    $_SESSION["password"] = password_hash($password, PASSWORD_DEFAULT);
                    header("location: inicio.php");
                    exit();
                } else {
                    mostrarAlerta($errores);
                    mostrar_formulario($nombre, $apellido, $email);
                }
            }
            
        ?>
    </body>
</html>