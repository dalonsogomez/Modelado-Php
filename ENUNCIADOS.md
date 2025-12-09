# **Recopilación de Enunciados de Examen \- Ingeniería del Software Web**

Este documento contiene los enunciados de tres ejercicios prácticos de desarrollo web (PHP y MySQL).

## **1\. Gestión de un Gimnasio (Título original: Gestión de pólizas en una compañía aseguradora)**

**NOTA:** El archivo del examen será un archivo comprimido que contendrá el fichero .sql con el volcado de la base de datos diseñada, así como el proyecto PHP de Netbeans completo con la implementación en PHP de las funcionalidades que se solicita codificar. El .zip se nombrará con la inicial del nombre más el primer apellido y al final la cadena de caracteres "IWPHPDic24" (Ej: calvarez\_IWPHPDic24.zip)

### **Descripción del Caso**

En el contexto de la automatización de la gestión de un gimnasio, se requiere diseñar una base de datos para almacenar la información relacionada con las actividades, socios, tipos de actividad e inscripciones, y otros detalles pertinentes. Para ello, la base de datos tendrá el siguiente formato:

En cuanto al diseño de la base de datos, se especifican a continuación sus cuatro tablas, con su clave primaria (**negrita**) y sus claves foráneas (\<u\>subrayadas\</u\>).

Adicionalmente, en lo que respecta al formato de los campos de las tablas mencionadas, se establecen las siguientes especificaciones: Todos los campos "ID" son de tipo autonuméricos. El campo "NIF" y "Telefono" son de tipo varchar con una longitud de 9 caracteres. Los campos "Nombre", "Email" y "Descripción" son de tipo varchar y cuentan con una longitud máxima de 50 caracteres cada uno. Los campos "PrecioMensual" son números reales. Los campos de tipo "Fecha" son de tipo date.

* **Actividad**: Almacena datos de las actividades (**actividadID**, nombre, descripción, fechaInicio, fechaFin)  
* **Socio**: Registra los socios del gimnasio (**socioID**, nif, nombre, telefono, email)  
* **Monitor**: Almacena los distintos monitores (**monitorID**, nombre, descripcion)  
* **Inscripciones**: Almacena información sobre las inscripciones (**\<u\>actividadID\</u\>**, **\<u\>socioID\</u\>**, **\<u\>monitorID\</u\>**, fechaInscripcion, precioMensual)

### **Flujo de la Aplicación**

Con el modelo de base de datos descrito, se busca poder inscribir a un socio en una actividad del gimnasio.

**Pantalla 1: Identificación** Se deberá identificar al socio. Para ello, habrá dos opciones:

1. Un formulario donde se permitirá introducir los datos del socio y almacenarlos en la base de datos.  
2. Un desplegable que permita seleccionar a un socio ya registrado. Este desplegable debe mostrar nombre del socio y su NIF entre paréntesis. Ej: Carlos Álvarez (12345678A).

**Pantalla 2: Selección de Actividad** Una vez identificado el socio obtenemos una segunda pantalla. En ella, se deben seleccionar:

* Los datos de la actividad (solamente las actividades activas, en SQL se puede usar CURDATE() para obtener la fecha actual y filtrar en la consulta). Para elegir los datos de la actividad, se mostrará un desplegable con los datos de todas las actividades almacenadas en la base de datos, mostrando el nombre de la actividad y entre paréntesis la fecha de finalización. Ej: nombre\_actividad(fecha finalización).  
* La fecha de inicio de la inscripción (esta debe ser mayor que la fecha actual).  
* El monitor asignado. Para seleccionar el monitor, se mostrará otro desplegable con los datos almacenados en la tabla Monitores, mostrando la descripción. Ej. Monitor de Spinning.

**Procesamiento:** Una vez introducidos todos los datos, se almacenarán en la base de datos. Se debe tener en cuenta que la duración de la inscripción es de un año, que la fecha de inscripción es la fecha actual y que el precio de la inscripción se generará de manera aleatoria entre 100€ y 1000€.

**Pantalla 3: Confirmación** Una vez almacenados los datos se mostrará una pantalla de confirmación en la que aparezcan los datos del socio, la actividad, el monitor y la inscripción realizada. Además, se mostrará un listado con las inscripciones que tenga contratadas el cliente.

### **Se pide:**

1. **Diseño de base de datos con la herramienta PhpMyAdmin**, definiendo todas las tablas con sus campos y claves primarias y foráneas para definir las relaciones entre tablas, según el modelo descrito, así como incluyendo también la introducción de datos para la prueba en la base de datos. **(1 punto)**  
2. **Implementar en PHP el proceso de inscripción** para un determinado socio y actividad según el proceso descrito anteriormente. **(6.5 puntos)**  
3. Implementar también el último proceso de la inscripción que incluye la consulta que muestra confirmación de la inscripción junto al listado de las inscripciones contratadas por ese cliente. Tal y como se describe en el enunciado. **(2.5 puntos)**

## **2\. Gestión Hotelera**

**NOTA:** El archivo del examen será un archivo comprimido que contendrá el fichero .sql con el volcado de la base de datos diseñada, así como el proyecto PHP de Netbeans completo con la implementación en PHP de las funcionalidades que se solicita codificar. El .zip se nombrará con la inicial del nombre más el primer apellido y al final la cadena de caracteres “IWPHPEne25” (Ej: calvarez\_IWPHPEne25.zip)

### **Descripción del Caso**

En el contexto de la automatización de la gestión de un hotel, se requiere diseñar una base de datos para almacenar la información relacionada con las reservas de habitaciones, los clientes y otros detalles pertinentes. Para ello, la base de datos tendrá el siguiente formato:

En cuanto al diseño de la base de datos, se especifican a continuación sus cuatro tablas, con su clave primaria (subrayada y en **negrita**) y sus claves foráneas (subrayadas).

Adicionalmente, en lo que respecta al formato de los campos de las tablas mencionadas, se establecen las siguientes especificaciones: Todos los campos "ID" son de tipo autonuméricos. El campo "NIF" y "Telefono" son de tipo varchar con una longitud de 9 caracteres. Los campos "Nombre", "Dirección", "Email" y "Descripción" (en este caso, la descripción del tipo de habitación) son de tipo varchar y cuentan con una longitud máxima de 50 caracteres cada uno. Los campos "PrecioBase" y cualquier otro precio requerido son números reales. Los campos de tipo "Fecha" son de tipo date.

* **TiposHabitacion**: Almacena los distintos tipos de habitación (**\<u\>tipoHabitacionID\</u\>**, descripcion, precioBase).  
* **Habitaciones**: Registra las habitaciones del hotel (**\<u\>habitacionID\</u\>**, numeroHabitacion, \<u\>tipoHabitacionID\</u\>).  
* **Clientes**: Almacena los datos de los clientes (**\<u\>clienteID\</u\>**, nif, nombre, telefono, email, direccion, tarjetaCredito, contraseña).  
* **Reservas**: Almacena información sobre las reservas (**\<u\>reservaID\</u\>**, \<u\>habitacionID\</u\>, \<u\>clienteID\</u\>, fechaReserva, fechaEntrada, fechaSalida, importeTotal).

### **Flujo de la Aplicación**

Con el modelo de base de datos descrito, se busca poder reservar una habitación en el sistema para un determinado cliente.

**Pantalla 1: Identificación** En una primera pantalla, se deberá identificar al cliente del hotel. Para ello, habrá dos opciones:

* Un formulario donde se permitirá introducir los datos del cliente y almacenarlos en la base de datos (registro).  
* Un formulario de login que permita validar a un cliente ya registrado en la base de datos. (Ej: se introduce el NIF y la contraseña, y se verifica que existe).

**Pantalla 2: Selección de Fechas y Tipo** Una vez identificado el cliente, obtenemos una segunda pantalla. En ella, se deben seleccionar los datos de la habitación. Para ello se introducirán las fechas de entrada y salida de la reserva (la fecha de entrada debe ser igual o mayor que la fecha actual y la fecha de salida debe ser mayor que la fecha de entrada), así como el tipo de habitación. Para seleccionar el tipo de habitación, se mostrará otro desplegable con los datos almacenados en la tabla TiposHabitacion, mostrando la descripción. Ej: “Superior”.

**Pantalla 3: Selección de Habitación Disponible** Una vez introducidos estos datos, se pasará a la siguiente pantalla, donde se mostrará al cliente las habitaciones **disponibles** para esas fechas. En esta pantalla el cliente seleccionará la habitación que desea reservar. Para elegir la habitación, se mostrará un desplegable con las habitaciones disponibles, mostrando el número de la habitación y su tipo entre paréntesis. Ej: “Hab. 101 (Doble)”.

**Procesamiento:** Una vez seleccionada la habitación, se almacenará en la base de datos. Se debe tener en cuenta que la fecha de la reserva (fechaReserva) es la fecha actual y que, tras calcular el precio total (por ejemplo, precioBase x número de noches), se aplicará un descuento aleatorio comprendido entre el 10% y el 30% sobre dicho importe antes de almacenarlo en el campo importeTotal.

**Pantalla 4: Confirmación** Una vez almacenados los datos se mostrará una pantalla de confirmación en la que aparezcan los datos del cliente, la habitación y la reserva realizada, con el precio final tras aplicar el descuento. Además, se mostrará un listado con las reservas que tenga contratadas el cliente a partir de la fecha actual.

### **Se pide:**

1. **Diseño de base de datos con la herramienta PhpMyAdmin**, definiendo todas las tablas con sus campos y claves primarias y foráneas para definir las relaciones entre tablas, según el modelo descrito, así como incluyendo también la introducción de datos para la prueba en la base de datos.  
2. **Implementar en PHP el proceso de reserva** de una habitación para un determinado cliente según el proceso descrito anteriormente.  
3. Implementar también el último proceso de la reserva que incluye la consulta que muestra confirmación de la reserva junto al listado de las reservas contratadas por ese cliente. Tal y como se describe en el enunciado.

## **3\. Gestión de Reservas de Apartamentos Turísticos**

### **Descripción del Caso**

Se cuenta con una base de datos para un servicio de alquiler de apartamentos turísticos, que almacena información de usuarios, inmuebles, reservas y comentarios sobre el servicio recibido. La base de datos tiene el siguiente formato:

En cuanto al diseño de la base de datos, se especifican a continuación sus cuatro tablas, con su clave primaria (subrayada y en **negrita**) y sus claves foráneas (subrayadas).

Además, en relación al formato de los campos: los campos id son todos autonuméricos; el NIF es un varchar de 9 caracteres, el teléfono de 9 y el nombre y la dirección varchar de 50; el campo precio y puntuación es número real positivo (entre 0 y 10 en el caso de la puntuación), el campo habitaciones un número entero positivo, los campo fecha serán de tipo date y el campo comentario será un varchar de 200 caracteres.

* **Usuario**: Almacena datos de los usuarios (**\<u\>ID\_Usuario\</u\>**, NIF, nombre, dirección, teléfono).  
* **Inmueble**: Registra los inmuebles disponibles (**\<u\>ID\_Inmueble\</u\>**, nombre, dirección, num\_habitaciones, precio).  
* **Reserva**: Almacena información sobre las reservas (**\<u\>ID\_Reserva\</u\>**, \<u\>ID\_inmueble\</u\>, \<u\>ID\_usuario\</u\>, fecha\_entrada, fecha\_salida).  
* **Comentario**: Nueva tabla para guardar los comentarios sobre el servicio recibido (**\<u\>ID\_reseña\</u\>**, \<u\>ID\_reserva\</u\>, puntuación, comentario).

### **Flujo de la Aplicación**

Con el modelo de base de datos descrito, se busca poder registrar una reserva de alquiler en el sistema para un determinado cliente.

**Pantalla 1: Selección de Cliente** En una primera pantalla, se ha de seleccionar al cliente entre un listado de clientes mostrado a través de una lista desplegable con el nombre de todos ellos.

**Pantalla 2: Selección de Inmueble y Fechas** Una vez seleccionado el cliente obtenemos una segunda pantalla, otra lista desplegable con el nombre de todos los inmuebles disponibles, así como su ID en entre paréntesis. Ej: Caserón en Salamanca (1234). Una vez seleccionado el inmueble, elegimos la fecha de entrada y la fecha de salida. Siempre mayores a la fecha actual y la fecha de salida mayor que la de entrada.

**Pantalla 3: Confirmación y Valoración** Una vez introducidas, se mostrará:

1. La información de la reserva con el precio total calculado.  
2. Una lista de las reservas anteriores del cliente.  
3. Un pequeño formulario para valorar la atención recibida sobre la reserva.

### **Se pide:**

1. **Diseño de base de datos con la herramienta PhpMyAdmin**, definiendo todas las tablas con sus campos y claves primarias y foráneas para definir las relaciones entre tablas, según el modelo descrito, así como incluyendo también la introducción de datos para la prueba en la base de datos. **(1 punto)**  
2. **Implementar en PHP el proceso de reserva** de un inmueble para un determinado cliente, cliente, según proceso descrito. **(5,5 puntos)**  
3. **Implementar también el último proceso de la reserva** que incluye la consulta que muestra confirmación de la sesión contratada para un servicio junto a listado de las reservas anteriores, así como la evaluación de la atención recibida; tal como también se describe en el enunciado. **(3,5 puntos)**

**NOTA GENERAL:** Todos los datos introducidos a través de formularios deben ser validados. Recuerda que no puedes consultar ningún material de tu ordenador. SOLO podrás tener un escritorio abierto en tu ordenador y solo abierto el IDE de programación que utilices, y el navegador para ver los resultados obtenidos. En caso de tener abierto algo más, el ejercicio se considerará SUSPENSO.