<?php
    session_start();
    
    // Verificar que el usuario esté registrado
    if(! isset($_SESSION["nombre"])){
        header("location: registro. php");
        exit();
    }
    
    // Inicializar array de libros en sesión si no existe
    if(! isset($_SESSION["libros"])){
        $_SESSION["libros"] = array(
            array(
                "titulo" => "Don Quijote de la Mancha",
                "autor" => "Miguel de Cervantes",
                "precio" => "15.50",
                "imagen" => "https://images.cdn1.buscalibre.com/fit-in/360x360/8f/8d/8f8d52f7c90c09bc796a0dd0b3aef99a.jpg"
            ),
            array(
                "titulo" => "Cien años de soledad",
                "autor" => "Gabriel García Márquez",
                "precio" => "18.90",
                "imagen" => "https://images.cdn3.buscalibre.com/fit-in/360x360/61/8d/618d227e8967274cd9589a549adff52d.jpg"
            ),
            array(
                "titulo" => "El principito",
                "autor" => "Antoine de Saint-Exupéry",
                "precio" => "12.00",
                "imagen" => "https://images.cdn2.buscalibre.com/fit-in/360x360/dc/51/dc51e52ad5eb1173e3f1ca03bcb9b698.jpg"
            )
        );
    }
    
    // ========== DECLARACIÓN DE VARIABLES ==========
    $titulo = isset($_POST["titulo"]) ? $_POST["titulo"] : "";
    $autor = isset($_POST["autor"]) ? $_POST["autor"] : "";
    $precio = isset($_POST["precio"]) ? $_POST["precio"] : "";
    $f_imagen = isset($_FILES["f_imagen"]) ? $_FILES["f_imagen"] : "";
    
    
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
    
    function mostrar_info_usuario(){
        $nombre = $_SESSION["nombre"];
        $apellido = $_SESSION["apellido"];
        $email = $_SESSION["email"];
        $foto = isset($_SESSION["foto_perfil"]) ?  $_SESSION["foto_perfil"] : "";
        
        $info = <<<INFO
            <h2>Bienvenido/a: $nombre $apellido</h2>
            <p>Email: $email</p>
INFO;
        
        if($foto != ""){
            $info .= "<p><img src='$foto' width='100' height='100' alt='Foto de perfil'></p>";
        }
        
        print $info;
    }
    
    function mostrar_libros($libros){
        echo '<hr>';
        echo '<h2>Libros Disponibles</h2>';
        
        foreach($libros as $libro){
            $libro_html = <<<LIBRO
                <hr>
                <h3>{$libro['titulo']}</h3>
                <p><strong>Autor:</strong> {$libro['autor']}</p>
                <p><strong>Precio:</strong> {$libro['precio']} €</p>
                <p><img src="{$libro['imagen']}" width="200" alt="{$libro['titulo']}"></p>
LIBRO;
            echo $libro_html;
        }
        
        echo '<hr>';
    }
    
    function mostrar_formulario($titulo, $autor, $precio){
        $formulario = <<<FORM
            <h2>Añadir Nuevo Libro</h2>
            <form action="inicio. php" method="post" enctype="multipart/form-data">
                <p>
                    Título del libro:
                    <input type="text" name="titulo" size="50" maxlength="100" value="$titulo">
                </p>
                
                <p>
                    Autor del libro:
                    <input type="text" name="autor" size="50" maxlength="100" value="$autor">
                </p>
                
                <p>
                    Precio (€):
                    <input type="text" name="precio" size="10" value="$precio">
                </p>
                
                <p>
                    Imagen de la portada:
                    <input type="file" name="f_imagen" accept="image/*">
                </p>
                
                <p>
                    <input type="submit" value="Añadir Libro">
                </p>
            </form>
FORM;
        print $formulario;
    }
    
    function cargar_imagen_libro(){
        if($_FILES['f_imagen']['error'] > 0){
            echo ("Problema en la carga del archivo!");
            return false; 
        } else {
            if (! is_dir('./libros')){
                mkdir("libros", 0777);
            }
            
            if (is_uploaded_file($_FILES["f_imagen"]["tmp_name"])){
                // Validar extensión usando explode
                $nombre_archivo = $_FILES["f_imagen"]["name"];
                $partes = explode(".", $nombre_archivo);
                $extension = strtolower(end($partes));
                
                $extensiones_permitidas = array("jpg", "jpeg", "png", "gif");
                
                if (!in_array($extension, $extensiones_permitidas)){
                    echo ("El archivo debe ser una imagen (JPG, JPEG, PNG o GIF)!");
                    return false;
                }
                
                $time = time();
                $ruta_fich = './libros/' . $time .  '-' . $nombre_archivo;
                
                if (move_uploaded_file($_FILES["f_imagen"]["tmp_name"], $ruta_fich)){
                    return $ruta_fich;
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
    
    function validar_libro(&$titulo, &$autor, &$precio, &$errores, $f_imagen){
        $flag = true;
        
        // Validar título
        if($titulo == ""){
            $flag = false;
            $errores .= " - El título no puede estar vacío ";
            $titulo = "";
        }
        
        // Validar autor
        if($autor == ""){
            $flag = false;
            $errores .= " - El autor no puede estar vacío ";
            $autor = "";
        }
        
        // Validar precio
        if(($precio == "") || (! preg_match("/^\d+(\.\d{1,2})?$/", $precio)) || ($precio <= 0)){
            $flag = false;
            $errores .= " - El precio es incorrecto (debe ser un número positivo) ";
            $precio = "";
        }
        
        // Validar imagen
        if ($f_imagen == "" || $_FILES["f_imagen"]["error"] == 4){
            $flag = false;
            $errores .= " - Debe subir una imagen de la portada ";
        }

        return $flag;
    }
    
    
    // ========== EJECUCIÓN ==========
    
    if(empty($_POST)){
        // Primera carga - mostrar página
        echo '<html>';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>Inicio - Tienda de Libros</title>';
        echo '</head>';
        echo '<body>';
        echo '<h1>Tienda de Libros Online</h1>';
        
        mostrar_info_usuario();
        mostrar_libros($_SESSION["libros"]);
        mostrar_formulario($titulo, $autor, $precio);
        
        echo '</body>';
        echo '</html>';
    } else {
        // Procesar formulario de añadir libro
        $errores = "";
        
        if(validar_libro($titulo, $autor, $precio, $errores, $f_imagen)){
            $ruta_imagen = cargar_imagen_libro();
            
            if($ruta_imagen !== false){
                $nuevo_libro = array(
                    "titulo" => $titulo,
                    "autor" => $autor,
                    "precio" => $precio,
                    "imagen" => $ruta_imagen
                );
                
                $_SESSION["libros"][] = $nuevo_libro;
                mostrarAlerta("Libro añadido correctamente!");
            } else {
                mostrarAlerta("Error al subir la imagen del libro");
            }
        } else {
            mostrarAlerta($errores);
        }
        
        // Mostrar página después de procesar
        echo '<html>';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>Inicio - Tienda de Libros</title>';
        echo '</head>';
        echo '<body>';
        echo '<h1>Tienda de Libros Online</h1>';
        
        mostrar_info_usuario();
        mostrar_libros($_SESSION["libros"]);
        mostrar_formulario($titulo, $autor, $precio);
        
        echo '</body>';
        echo '</html>';
    }
?>