<?php

$opciones = array("Local", "Castilla-León", "Otra comunidad", "Extranjero");

$opciones2 = array(
    "Local" => "Local",
    "Regional" => "Castilla y León",
    "Nacional" => "Otra comunidad",
    "Extranjera" => "Extranjero"
);



$exp = isset($_POST["exp"]) ? $_POST["exp"] : "";
$nom = isset($_POST["nom"]) ? $_POST["nom"] : "";
$user = isset($_POST["user"]) ? $_POST["user"] : "";
$email = isset($_POST["email"]) ? $_POST["email"] : "";
$f_nac = isset($_POST["f_nac"]) ? $_POST["f_nac"] : "";
$pwd = isset($_POST["pwd"]) ? $_POST["pwd"] : "";
$pwd2 = isset($_POST["pwd2"]) ? $_POST["pwd2"] : "";
$orig = isset($_POST["orig"]) ? $_POST["orig"] : "";
$observ = isset($_POST["observ"]) ? $_POST["observ"] : "";

function pintar_formulario_alta($exp, $nom, $user, $email, $f_nac, $orig, $observ, $opciones2){
    
    $formulario1 = <<<FORMULARIO1
            <h1>Datos del alumno</h1>
                
            <form action="form_alta.php" method="post" name="form_insert_alum">
                <p>
                    Expediente: 
                    <input type="text" name="exp" size="5" maxlength="5" value="$exp">
                </p>
            
                <p>
                    Nombre: 
                    <input type="text" name="nom" size="20" maxlength="20" value="$nom">
                </p>
            
                <p>
                    Usuario: 
                    <input type="text" name="user" size="20" maxlength="20" value="$user">
                </p>
            
                <p>
                    Contraseña: 
                    <input type="password" name="pwd" size="15" maxlength="15">
                </p>
            
                <p>
                    Repita su contraseña: 
                    <input type="password" name="pwd2" size="15" maxlength="15">
                </p>
            
                <p>
                    Fecha de nacimiento: 
                    <input type="date" name="f_nac" value="$f_nac">
                </p>
            
                <p>
                    Procedencia:
FORMULARIO1;
    
    print $formulario1;
    
    //foreach ($opciones as $value) {
    foreach ($opciones2 as $key => $value) {
        //echo "<input name='GrOpc_proc' type='radio' value='$value'";
        
        //if($value == $orig){
        if($key == $orig){
            echo "<input name='orig' type='radio' value='$key' checked>$value";
        } else{
            echo "<input name='orig' type='radio' value='$key'>$value";
        }
    }
    
    
    $formulario2 = <<<FORMULARIO2
                </p>
                <p>
                    Email: 
                    <input type="email" name="email" value="$email">
                </p>
            
                <p>
                    Observaciones: 
                    <textarea name="observ" cols="40" rows="10">$observ</textarea>
                </p>
            
                <p>
                    <input type="submit" name="Submit" value="Guardar">
                </p>
            </form>
FORMULARIO2;
    
    print $formulario2;
}


function validar_datos(&$exp, &$nom, &$user, &$email, &$f_nac, &$orig, &$observ, $opciones2, $pwd, $pwd2, &$errores){
    $flag = true;
    
    if(($exp == "") || (!preg_match("/^[0-9]{5}$/", $exp))){
        $exp = "";
        $errores .= " / expediente incorrecto";
        $flag = false;
    }
    
    if($nom == ""){
        $nom = "";
        $errores .= " / nombre incorrecto";
        $flag = false;
    } else {
        $nom = addslashes($nom);
    }
    
    // Funcion preg_match de verificar un mail
    if($email == "" ){
        $email = "";
        $errores .= " / email incorrecto";
        $flag = false;
    }
    
    if($user == ""){
        $user = "";
        $errores .= " / usuario incorrecto";
        $flag = false;
    } else {
        $user = addslashes($user);
    }
    
    if(($pwd == "") || ($pwd2 == "")){
        $errores .= " / No se han introducido las contraseñas";
        $flag = false;
    } else {
        if($pwd != $pwd2){
            $errores .= " / Las contraseñas no coinciden";
            $flag = false;
        } else {
            $pwd = addslashes($pwd);
        }
    }
    
    
    if($f_nac == ""){
        $errores .= " / No se ha introducido la fecha de nacimiento";
        $flag = false;
    } else {
        // aaaa-mm-dd
        $partes = explode('-', $f_nac);
        
        if(count($partes) != 3){
            $errores .= " / Fecha de nacimiento inválida";
            $flag = false;
        } else {
            if(!checkdate($partes[1], $partes[2], $partes[0])){
                $errores .= " / Fecha de nacimiento inválida";
                $flag = false;
            }
        }
    }
    
    //if(!in_array($orig, $opciones)){
    if(!in_array($orig, array_keys($opciones2))){
        $orig = "";
        $errores .= " / Origen inválida";
        $flag = false;
    }
    
    return $flag;
}



if(empty($_POST)){
    pintar_formulario_alta($exp, $nom, $user, $email, $f_nac, $orig, $observ, $opciones2);
} else {
    $errores = "";
    
    if(!validar_datos($exp, $nom, $user, $email, $f_nac, $orig, $observ, $opciones2, $pwd, $pwd2, $errores)){
        print($errores);
        pintar_formulario_alta($exp, $nom, $user, $email, $f_nac, $orig, $observ, $opciones2);
    } else {
        include 'conexion_bd.php';
        
        //$query = "INSERT INTO alumnos (nombre, usuario, clave, f_nac, origen, email, observaciones) "
        //        . "VALUES ('$nom', '$user', '$pwd', '$f_nac', '$orig', '$email', '$observ')";
        
        $query = "INSERT INTO alumnos (expediente, nombre, usuario, clave, f_nac, origen, email, observaciones) "
                . "VALUES ('$exp', '$nom', '$user', '$pwd', '$f_nac', '$orig', '$email', '$observ')";
        
        $res_alum = mysqli_query($conex, $query) or die (mysqli_error($conex));
        
        if($res_alum){
            header("Location: menu_principal.php");
            exit();
        } else {
            echo "Error al guardar en la base de datos";
            pintar_formulario_alta($exp, $nom, $user, $email, $f_nac, $orig, $observ, $opciones2);
        }
    }
}