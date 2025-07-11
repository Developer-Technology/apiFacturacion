![banner](https://chanamoth.com/wp-content/uploads/2023/01/Developer-technology-new-1.png)

# ADMIN DEL API PARA LA FACTURACIÓN ELECTRÓNICA SUNAT (APIFACT)

No importa el lenguaje de programación que estés usando, puedes usar nuestra API para poder emitir documentos electrónicos desde tu propio sistema, sólo debes enviar un ARCHIVO en JSON y el API se encarga de generar el PDF, XML, el envío a la Sunat, y almacenar la CDR, entre otros procedimientos como consultar el CPE, consultar RUC / DNI y obtener el tipo de cambio por la fecha actual.

## REQUISITOS DEL ENTORNO

1 PHP en su versión 7

## USUARIO DEMOSTRACIÓN

Correo: admin@admin.com  
Clave: password

## TECNOLOGÍAS UTILIZADAS

1 PHP en su versión 7  
2 Ajax  
3 jQuery  
4 JavaScript

## INSTALACIÓN EN LOCALHOST

1 Video de instalación [aquí](https://youtu.be/EFN7mbVbmpE)

## INSTALACIÓN EN HOSTING / VPS

1 Descomprimir el zip dentro del subdominio o directorio  
2 Verificar la versión de PHP (7)  
3 Cambiar la ruta del sistema en "controllers/template.controller.php" en la función "path()"

## CAMBIOS 24/02/23

1 Se cambia la tokenización por empresa   
2 Se conecta con la bd para almacenar la cantidad de consultas por empresa  
3 Se conecta con la bd para almacenar la cantidad de documentos emitidos por empresa  
4 Se ajusta el json con el token  
5 Se mejora interfaz de usuario  
6 Se maneja registro de empresas por usuario  
7 Se implementa módulos como:  
a- Registro con validación de cuenta por correo  
b- Perfil del usuario  
c- Configuración de la empresa  
d- Consultas RUC / DNI / CPE  

## CAMBIOS 27/02/23

1 Se integra la consulta de tipo de cambio por la fecha actual

## CAMBIOS 01/03/23

1 Se autogenera avatar del usuario al registrarse  
2 Se autogenera "claveSecreta" por empresa al registrarse tomando como referencia el número de RUC  
3 Se valida el plan relacionado con la empresa logueada  
4 Se valida cantidad de consultas y documentos (total, emitidos, disponibles) con el plan relacionado a la empresa  
5 Se modifica ajax/ajax-consulta.php con la "claveSecreta" de la empresa logueada

## CAMBIOS 07/03/23

1 Se carga en la interfaz las credenciales para la guía de remisión (client_id, client_secret)

## CAMBIOS AL 08/03/23

1 Se corrige carga de credenciales para la guía de remisión  
2 Se coloca visualmente el modal de las credenciales en todas las vistas  

## CAMBIOS AL 10/03/23

1 Se elimina del repositorio el "sql" que corresponde a la base de datos  
2 Se elimina del repositorio el "Json" que corresponde al postman  
3 Se agrega "htaccess" para producción de ser necesario  
4 Se mantiene la conexión a la base de datos hasta terminar de crear los endpoints para el manejo de la interfaz (el front se manejará en su totalidad con el API)  
5 Se va conectando y modificando de acuerdo a las respuestas del API las partes de la interfaz de usuario  
6 Se crea la tabla "configuraciones" para la personalización del sistema como nombre del sistema, nombre de la empresa, email, y su token para la parte admin  
7 En la tabla "configuraciones" se agregró los campos "id_sunat_configuracion, clave_sunat_configuracion" para el manejo de las peticiones que proporciona el API público de SUNAT como tipo de cambio, consulta de cpe, entre otros, allí colocamos el API para que el token se autogene y se pueda prestar el servicio

## CAMBIOS AL 14/03/23

1 Se soluciona de manera temporal el registro de empresas

## CAMBIOS AL 15/03/23

1 Se soluciona vista perfil de usuario  

## CAMBIOS AL 16/03/23

1 Se almacena el token del usuario en el localstorage para sus próximas validaciones  
2 Se modifica script para validar datos repetidos con Ajax, haciéndolo más dinámico

## CAMBIOS AL 21/03/23

1 Se termina la integración del script con el API REST dinámico para las peticiones al back  
2 Se mejora la consulta de CPE  
3 Se elimina la conexión y los modelos del script, ahora las peticiones se realiza directo con el API  
4 Se elimina las extensiones innecesarias por el momento ya que todo lo realiza el API  
5 Se elimina imagenes de prueba

## CAMBIOS AL 22/03/23

1 Vista planes  
2 Vista empresas  
3 Vista usuarios  
4 Vista configuraciones  
5 Se elimina la carpeta de imagenes y todo se obtiene consultando el API (sirve para futuras integraciones externas)  
6 Vista Dashboard panel admin  
7 Se crea la sesión del admin

## CAMBIOS AL 23/03/23

1 Se modifica HTML para SEO

## CAMBIOS AL 24/03/23

1 Se finaliza la edición del perfil con la carga de foto utilizando el API REST dinámico  
2 Se finaliza la edición de las configuraciones  
3 Se corrige registro de usuario  
4 Se maneja de manera dinámica el estado de los usuarios, empresas  
5 CRUD de planes terminado, antes de eliminar se valida si está asociado a una empresa  
6 Se crea dos ventanas modales para orientación al usuario de como generar las credenciales para el consumo de la API pública de SUNAT y Guía de remisión

## CAMBIOS AL 28/03/23

1 Se obtiene las consultas consumidas de acuerdo al periodo actual y se imprime en el dashboard  
2 Se obtiene los documentos emitidos de acuerdo al periodo actual y se imprime en el dashboard  
3 Se muestra gráfico de líneas en el dashboard de la empresa logueada (Consultas - Documentos) utilizando el json de la base de datos  
4 Se agrega el "js" para generar el gráfico

## CAMBIOS AL 29/03/23

1 Se termina dashboard de la parte admin, con un gráfico lineal con la suma general de los documentos y consultas  
2 Se maqueta tablas de precios con los planes y su contenido antes de registrar una empresa  
3 Se añade el botón de paypal

## CAMBIOS AL 30/03/23

1 Se agrega la configuración de pasarelas de pago (Paypal, Culqi) dentro del módulo de "Configuraciones"  
2 Se convierte al tipo de cambio del día para el pago por Paypal  
3 Se crea un ajax dinámico (ajax-get.php) para las consultas GET al API

## CAMBIOS AL 31/03/23

1 Se obtiene correctamente la venta por PayPal  
2 Se crea un ajax dinámico (ajax-put.php) para las consultas PUT al API  
3 Se incrementa la cantidad de ventas en la tabla "planes" según el ID comprado  
4 Se crea un ajax dinámico (ajax-post.php) para las consultas POST al API  
5 Se inserta correctamente los datos de la venta al ser finalizada con el pago correcto  
6 Se termina carrito de compras con PayPal y se asigna el plan seleccionado a la empresa creada  
7 Se agrega un datatable en el perfil del usuario con las compras realizadas, indicando su estado y si está asignado a una empresa  
8 Se agrega un datatable en la parte admin con las ventas realizadas, indicando su estado y si está asignado a una empresa

## CAMBIOS AL 03/04/23

1 Se actualiza la próxima facturación de la empresa al crear la empresa  
2 Se da la opción de renovar o comprar otro plan a la empresa

## CAMBIOS AL 10/04/24

1 Se actualiza el gráfico del home respetando el año actual

## CAMBIOS AL 10/04/24

1 Se agrega el formulario para el registro de empresa desde el panel del super administrador  
2 Se personaliza el sistema, habilitando o restringiendo el registro o recuperación del password desde el front  
3 Se protege las rutas "/register" y "/forgot" según las configuración del sistema
4 Se agrega el campo "extras_configuracion" de tipo "text" en la tabla "configuraciones" antes del campo "creado_configuracion"  
5 Agregar el siguiente valor al campo creado en la base de datos '[{"reset_pass":"si","register_system":"si","social_login":"no"}]'

## CAMBIOS AL 28/04/24

1 Se agrega la opción de eliminar empresas, siempre y cuando no haya realizo alguna consulta o consumo del plan  
2 Se agrega la opción de editar los datos de las empresas  
3 Se agrega la opción de crear usuarios desde el panel admin, asignando el rol  
4 Se agrega la opción de editar los datos de los usuarios  
5 Se asegura los datos del super admin  
6 Se corrige el panel admin si no hay empresas registradas  
7 Se corrige la parte visual del panel inicial si no hay empresas registradas  
8 Se corrige peticion con el token general del administrador si no hay empresas registradas  
9 Se corrige el carrito de compras si el valor el "0"  
10 Se libera la creación y envío de bajas y resumen de boletas desde el front    

## CAMBIOS AL 28/05/24

1 Se agrega la opción de poder ingresar al panel de la empresa desde el listado del super admin  

## CAMBIOS AL 09/06/24

1 Se agrega configuración para conectar con Supabase  

## CAMBIOS AL 11/06/24

1 Se modifica el registro de empresa generando un ID unico de transaccion  
2 Se inserta la venta y la suscripción al crear la empresa  
3 Se muestra las suscripciones por empresa logueada  
4 Se prepara la vista para cargar el pago de la empresa  

## CAMBIOS AL 16/06/24

1 Se crea el "cron.php" para la insercción de registros en la tabla suscripciones, tomando la fecha de próxima facturación de la empresa y su último registro "pagado" de la tabla suscripciones con una anticipación de 5 días  
2 Se notifica al usuario por correo electrónico 5 días antes de su próxima facturación  

## CAMBIOS AL 17/06/24

1 Se agrega un botón en el listado de las suscripciones de la vista admin para actualizar las suscripciones, en caso no se desee utilizar el cron.php  

## CAMBIOS AL 18/06/24

1 Corrección de errores al utilizar el cron.php en un VPS que no permite datos nulos  

## CAMBIOS AL 20/06/24

1 Se libera la carga del adjunto  
2 Se agrega una opción para habilitar o deshabilitar el envío por correo, para evitar realizar la petición de manera innecesaria si no se tiene configurado el servidor

## CAMBIOS AL 23/06/24

1 Se corrige la creación de avatar al crear un usuario por el panel admin  
2 Se valida si ya existe un archivo adjunto, en el avatar del usuario, logo de la empresa, adjunto en las suscripciones, para eliminarlo previo a la carga del nuevo adjunto  
3 Se actualiza las ventas de planes por cada empresa registrada  
4 Se actualiza las ventas de plnaes al realizar el pago de una suscripción  

## CAMBIOS AL 30/06/24
   
1 Se crea la vista para poder editar los valores por defecto del campo "facturacion_suscripcion"  
2 Se agrega la funcion "billing()" en controllers/settings.controller.php para tomar los datos enviados y modificar únicamente el json  
3 Se valida si la configuración toma como fase "beta" o "producción" para la carga del certificado digital de la empresa que brindará el servicio con el API  
4 Se realiza la firma y el envío de la factura en automático a SUNAT, validando primero que el monto del plan sea mayor a cero  
5 Se realiza el envío del correo con los archivos adjuntos al registrar una empresa

## CAMBIOS AL 06/11/24

1 Se simplifica el cronjob (cron.php) utilizando el endpoint creado en el API  
2 Se simplifica el botón para ejecutar la actualización de las suscripciones utilizando el endpoint creado en el API  
3 Se quita el password de supabase en las configuraciones generales  
4 Se simplifica el proceso para la carga con el pago de una suscripción con el nuevo endpoint creado en el API

## CAMBIOS AL 07/11/24

1 Se simplifica la aprobación o rechazo del pago de una suscripción con le nuevo endpoint desde el API