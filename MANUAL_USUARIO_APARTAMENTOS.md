# Manual de Usuario: Gestión de Reservas de Apartamentos Turísticos

## Introducción

Este manual describe el proceso paso a paso para realizar una reserva de un apartamento turístico utilizando el sistema web implementado. El flujo de trabajo está diseñado para ser intuitivo, guiando al usuario desde la selección del cliente hasta la confirmación final de la reserva.

## Requisitos Previos

Para utilizar el sistema, se necesita:
1.  Un navegador web.
2.  Haber configurado la base de datos `apartamentos` utilizando el archivo `apartamentos.sql` proporcionado.
3.  Asegurarse de que el servidor web (por ejemplo, Apache) y PHP están en funcionamiento.
4.  Colocar los archivos PHP (`reservas_paso1.php`, `reservas_paso2.php`, `reservas_paso3.php`, `guardar_comentario.php`, `conexion_apartamentos.php`) en el directorio del servidor web.

## Flujo del Proceso de Reserva

El proceso de reserva se divide en tres pasos principales, seguidos de una valoración opcional.

### Paso 1: Selección del Cliente

1.  **Acceso Inicial**: Abra su navegador y navegue hasta el archivo `reservas_paso1.php`. Por ejemplo: `http://localhost/reservas_paso1.php`.
2.  **Pantalla**: Se le presentará una página titulada "Paso 1: Seleccionar Cliente".
3.  **Acción**: En el menú desplegable, verá una lista de todos los clientes registrados en la base de datos. Seleccione el cliente para el cual desea realizar la reserva.
4.  **Continuar**: Haga clic en el botón **"Siguiente"** para proceder al segundo paso.

*Archivo Involucrado*: `reservas_paso1.php`

### Paso 2: Selección del Inmueble y las Fechas

1.  **Pantalla**: La página mostrará "Paso 2: Seleccionar Inmueble y Fechas", junto con el nombre del cliente que seleccionó en el paso anterior.
2.  **Seleccionar Inmueble**: En el primer menú desplegable, elija uno de los inmuebles disponibles. El ID del inmueble se muestra entre paréntesis junto al nombre para mayor claridad.
3.  **Seleccionar Fechas**:
    *   **Fecha de entrada**: Haga clic en el campo "Fecha de entrada" y seleccione una fecha del calendario. La validación del sistema asegura que no puede seleccionar una fecha anterior a la actual.
    *   **Fecha de salida**: De manera similar, seleccione la fecha de salida. El sistema también valida que esta fecha sea posterior a la fecha de entrada.
4.  **Continuar**: Una vez que haya completado todos los campos, haga clic en el botón **"Realizar Reserva"**.

*Archivo Involucrado*: `reservas_paso2.php`

### Paso 3: Confirmación y Valoración

1.  **Pantalla de Confirmación**: Si los datos introducidos en el paso anterior son válidos, el sistema procesará la reserva y la guardará en la base de datos. A continuación, se le presentará la página "Paso 3: Confirmación de la Reserva".
2.  **Detalles de la Reserva**: Verá un resumen completo de la reserva recién creada, incluyendo:
    *   Nombre del cliente.
    *   Nombre del inmueble.
    *   Fechas de entrada y salida.
    *   Cálculo del número de noches.
    *   El precio total de la estancia.
3.  **Historial de Reservas**: Justo debajo de los detalles, se mostrará una tabla con todas las reservas anteriores realizadas por el mismo cliente, lo que permite una visión rápida de su historial.
4.  **Valorar el Servicio**:
    *   En esta misma página, encontrará un pequeño formulario para valorar la atención recibida durante el proceso.
    *   **Puntuación**: Introduzca una nota numérica entre 0 y 10.
    *   **Comentario**: Opcionalmente, puede dejar un comentario de texto (máximo 200 caracteres).
    *   Haga clic en **"Enviar valoración"** para guardar su opinión.

*Archivos Involucrados*: `reservas_paso3.php`, `guardar_comentario.php`

### Finalización

Al enviar la valoración, el sistema la guardará y mostrará un mensaje de agradecimiento. En este punto, el flujo de reserva ha concluido exitosamente. Puede iniciar una nueva reserva navegando de nuevo a `reservas_paso1.php`.
