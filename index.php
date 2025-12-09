<?php
/**
 * Archivo: index.php
 * Propósito: Página de inicio de sesión del sistema de alumnos
 * 
 * Funcionalidades:
 * - Gestión de sesiones PHP
 * - Formulario de login con usuario y contraseña
 * - Validación de credenciales contra base de datos
 * - Redirección al menú principal si login exitoso
 * - Link a registro de nuevos alumnos
 * 
 * Flujo:
 * 1. Si no hay POST: mostrar formulario
 * 2. Si hay POST: validar credenciales
 * 3. Si válido: crear sesión y redirigir
 * 4. Si inválido: mostrar error y repintar formulario
 * 
 * @author Sistema de Gestión Escolar
 * @version 1.0
 */

// Iniciar o continuar sesión PHP
// IMPORTANTE: debe estar antes de cualquier output HTML
session_start();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Login - Sistema de Alumnos</title>
    </head>
    <body>
        <?php
        
        /**
         * Función: pintar_formulario
         * Propósito: Genera y muestra el formulario HTML de login
         * 
         * Características:
         * - Usa sintaxis Heredoc para HTML limpio
         * - Campos: usuario (text), contraseña (password)
         * - Link a form_alta.php para registro
         * 
         * @return void Imprime directamente el formulario
         */
        function pintar_formulario(){
            // Heredoc: permite escribir HTML multilínea sin escapar comillas
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
            
            // Imprimir el formulario HTML
            print $formulario;
        }
        
        // ===== CONTROL DE FLUJO PRINCIPAL =====
        
        // Verificar si es la primera carga (sin datos POST)
        if (empty($_POST)){
            // Primera carga - mostrar formulario de login
            pintar_formulario();
        } else {
            // Formulario enviado - procesar credenciales
            
            // Incluir archivo de conexión a base de datos
            include 'conexion_bd.php';
            
            // Operador ternario para obtener valores POST con seguridad
            // isset() verifica que la variable existe, si no, asigna string vacío
            $user = isset($_POST['user']) ? $_POST['user'] : "";
            $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : "";
            
            // Construir consulta SQL para validar credenciales
            // NOTA: Esta query es vulnerable a SQL injection (no usa prepared statements)
            $query = "SELECT expediente, usuario, clave, nombre, origen FROM alumnos"
                    . " WHERE (usuario = '$user') and (clave = '$pwd')";
            
            // Ejecutar la consulta
            // or die(): si hay error SQL, muestra mensaje y detiene ejecución
            $res_valid = mysqli_query($conex, $query) or die (mysqli_error($conex));
            
            // Validación de credenciales
            // mysqli_num_rows() cuenta las filas del resultado (0 = no encontrado, 1 = encontrado)
            if((mysqli_num_rows($res_valid) == 0) || !$user || !$pwd){
                // Credenciales inválidas o campos vacíos
                echo "Las credenciales introducidad no son válidas";
                pintar_formulario();
            } else {
                // Credenciales válidas - obtener datos del usuario
                // mysqli_fetch_array() obtiene la fila como array asociativo
                $reg_usuario = mysqli_fetch_array($res_valid);
                
                // Guardar datos importantes en la sesión PHP
                // $_SESSION es un array superglobal disponible en todas las páginas
                $_SESSION['nombre'] = $reg_usuario['nombre'];
                $_SESSION['expediente'] = $reg_usuario['expediente'];
                $_SESSION['usuario'] = $reg_usuario['usuario'];
                $_SESSION['origen'] = $reg_usuario['origen'];
                
                // Redirigir al menú principal
                // header() debe ejecutarse antes de cualquier output HTML
                header("location: menu_principal.php");
                exit(); // Detener ejecución después del redirect
            }
        }
        
                
                
        ?>
    </body>
</html>
