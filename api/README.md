![banner](https://developer-technology.net/wp-content/uploads/2023/01/Developer-technology-new-1.png)

# API PARA LA FACTURACIÓN ELECTRÓNICA SUNAT

No importa el lenguaje de programación que estés usando, puedes usar nuestra API para poder emitir documentos electrónicos desde tu propio sistema, sólo debes enviar un ARCHIVO en JSON y el API se encarga de generar el PDF, XML, el envío a la Sunat, y almacenar la CDR, entre otros procedimientos como consultar el CPE, consultar RUC / DNI y obtener el tipo de cambio por la fecha actual.

## REQUISITOS DEL ENTORNO

1 PHP en su versión 7  

## TECNOLOGÍAS UTILIZADAS

1 PHP en su versión 7  
2 spipu/html2pdf para la creación de PDF  
3 phpqrcode para la creación de QR  
4 giansalex/peru-consult para la consulta RUC y DNI  
5 phpmailer/phpmailer para el envío de correo  
6 firebase/php-jwt para el token de sesión del usuario

## INSTALACIÓN EN LOCALHOST

1 Video de instalación [aquí](https://youtu.be/EFN7mbVbmpE)

## INSTALACIÓN EN HOSTING / VPS

1 Descomprimir el zip dentro del dominio, subdominio o directorio  
2 Verificar la versión de PHP (7)  
3 Ejecutar las peticiones con el endpoint  

## CAMBIOS 05/02/23

1 Se muestra el enlace del XML y CDR  
2 Se agrega "claveCertificado" en Json para producción

## CAMBIOS 09/02/23

1 Se muestra el enlace del PDF  
2 Se crea ruta para cargar logo en Json Postman

## CAMBIOS 22/02/23

1 Se ajusta XML para facturas gratuitas, exoneradas, inafectas, icbper   
2 Se tokeniza API y se crea base de datos  
3 Se integra la consulta RUC y DNI  
4 Se ajusta el Json con el token

## CAMBIOS 24/02/23

1 Se cambia la tokenización por empresa   
2 Se conecta con la bd para almacenar la cantidad de consultas por empresa  
3 Se conecta con la bd para almacenar la cantidad de documentos emitidos por empresa  
4 Se ajusta el Json con el token

## CAMBIOS 27/02/23

1 Se integra la consulta de tipo de cambio por la fecha actual

## CAMBIOS 01/03/23

1 Se corrige actualización de consultas y documentos independiente por empresa  
2 Se corrige arreglo de crédito en XML y PDF  
3 Se añade "claveSecreta" en cada empresa para validar su relación con el token  
4 Se simplifica Json con la "claveSecreta" y la data de la empresa lo toma de la base de datos  
5 Se formatea números en PDF  
6 Se modifica xml.controlador.php con la "claveSecreta"

## CAMBIOS 03/03/23

1 Se añade a la base de datos, tabla empresas el client_id y client_secret que se genera en el portal sunat para el envío de guía de remisión  
2 Se crea función para generar el token con el client_id y client_secret y datos de la empresa  
3 Se crea función para el envío de la guía de remisión utilizando el API REST de SUNAT (autogenera el token y ticket)  
4 Se mejora las rutas (endpoints) y se quita funciones redundantes  
5 Se genera XML (Facturas / Boletas) y se firma a la vez (ya no firma y envía)  
6 Al realizar el envío ya no firma el XML, solo se encarga de buscar el XML firmado y lo envía (Facturas / Boletas)  
7 Se elimina archivos zipeados para no cargar las carpetas con la misma información (XML firmado / CDR)  
8 Se almacena XML firmado y XML sin firmar en carpetas distintas según la empresa (documents/{ruc}/signed - documents/{ruc}/unsigned)  
9 Se exporta Json con nuevas carpetas  
10 Se exporta bd con campos añadidos  
11 En el retorno del Json ahora reconoce si la peticion viene con ssl o sin ssl e imprime en la respuesta la ruta con el protocolo detectado

## CAMBIOS 06/03/23

1 Se cambia estructura XML al generar guía de remisión Remitente  
2 Se corrige errores al generar el ticket de la guía de remisión  
3 Se genera XML, ticket y envía con éxito Guía de remisión remitente  
4 Se arma estructura XML de Percepciones (por terminar)  

## CAMBIOS 07/03/23

1 Se arma esctructura XML guía de remisión transportista, se genera ticket y envía con éxito  
2 Se hace el envío automático de las guías de remisión (se deja la opción de consultar ticket)  
3 Se crea función y endpoint para el envío de percepciones y retenciones  
4 Se termina estructura XML de percepciones y envía con éxito  
5 Se arma estructura XML de retenciones y envía con éxito  
6 Se mejora retorno si el success no es TRUE  
7 Se arma Json de una factura de contingencia y envía con éxito  
8 Se arma Json de una factura + percepción y envía con éxito (se modifica Json / XML)  
9 Se arma Json de una factura + retención y envía con éxito (se modifica Json / XML)  
10 Se arma Json de una factura de exportación y envía con éxito (se modifica Json / XML)  
11 Se arma "htaccess" para producción  
12 Se exporta Postman con las nuevas casuísticas

## CAMBIOS AL 08/03/23

1 Se corrige actualización de documentos generados (cantidad)  
2 Se mejora actualización de consultas generadas (cantidad)  
3 Se coloca mensaje de validéz del comprobante si la empresa está en beta o producción  
4 Se autogenera PDF A4 y Ticket al crear un documento  
5 Se mejora retorno al crear un documento, se muestra ruta del xml sin firmar, xml firmado, PDF A4, PDF Ticket y mensaje de éxito  
6 Se agrega la ruta del PDF A4 y Ticket en el retorno del envío de documento  
7 Se adapta PDF guía de remisión transportista  
8 Se crea validación para generar un PDF A4 y Ticket, si se encuentra el XML en el directorio se puede tomar su código QR, caso contrario no permitirá crear el PDF  
9 Se crea validación para enviar un documento a SUNAT, si se encuentra el XML firmado en el directorio se podrá realizar el envío caso contrario no permitirá realizar el envío  
10 Se crea validación para la consulta de ticket (Bajas, Resumen) a SUNAT, si se encuentra el XML firmado en el directorio se podrá realizar la consulta caso contrario no permitirá realizar la consulta  
11 Se corrige "htaccess" para producción  
12 Se exporta Postman actualizado

## CAMBIOS AL 10/03/23

1 Se crea endpoint para el logueo de usuarios (libre sin token)  
2 Se simplifica Json para generar XML por causística, solo se envía los campos dependiendo la causística (ya no es necesario enviar toda la data del Json)  
3 Se crea tabla "configuraciones" en la base de datos para las configuraciones generales del sistema  
4 Se crea endpoint para ver las configuraciones iniciales del sistema (por motivos de seguridad se maneja por un token especial dentro de la tabla "configuraciones" que solo tiene acceso el usuario admin)  
5 Se crea endpoint para ver el listado de empresas (por motivos de seguridad se maneja por un token especial dentro de la tabla "configuraciones" que solo tiene acceso el usuario admin)  
6 Se crea endpoint para ver el listado de planes (por motivos de seguridad se maneja por un token especial dentro de la tabla "configuraciones" que solo tiene acceso el usuario admin)  
7 Se crea endpoint para actualizar los datos de la empresa (se maneja con el token y clave secreta de la empresa)  
8 Se actualiza la base de datos con el logo de la empresa (al cargar el archivo)  
9 Se encripta nombre del logo  
10 Se actualiza la base de datos con el certificado digital y datos correspondiente al mismo  
11 Se encripta nombre del certificado digital

## CAMBIOS AL 13/03/23

1 Se soluciona la sumatoria en las casuísticas al generar el XML

## CAMBIOS AL 14/03/23

1 Se mejora la consulta CPE para cualquier comprobante sin necesidad de estar en el entorno producción

## CAMBIOS AL 15/03/23

1 Se corrige retorno en la consulta de tipo de  cambio y se simplifica función  
2 Se crea función para generar el "token" único de empresa  
3 Se crea función para generar la "claveSecreta" única por empresa  
4 Se crea función para generar avatar del usuario tomando las iniciales en su registro  
5 Se instala PHP Mailer para el envío de correo, tanto de comprobantes como de verificación al registro  
6 Se crea función para el envío dinámico de correos  
7 Se crea función para capitalizar los datos del usuario en su registro  
8 Se crea función para acortar la fecha y mostrarlo en la vista  
9 Se instala "firebase/jwt" para tokenizar la sesión de los usuarios  

## CAMBIOS AL 16/03/23

1 Se crea función para generar el token de autenticación en JWT  
2 Se mejora controlador para la autenticación del usuario actualizando en la base de datos el token y la expiración  
3 En el retorno del logueo se descarta la contraseña del usuario y se añade el token y la expiración  
4 Se modifica la base de datos en la tabla "usuarios" añadiendo el campo "token_exp_usuario" de tipo texto  
5 Se crea un API dinámico para las peticiones del back, esto sirve para facilitar la integración en el front  
6 Se elimina de la tabla "configuraciones" el campo "token_configuracion" y se maneja la autorización (solo de las peticiones al back) con un apikey estático que se puede cambiar desde la clase "Conexion" función "apiKey()", esto para proteger las rutas del API al hacer la peticiones al back  
7 Se añade los estados en las respuesta Json

## CAMBIOS AL 21/03/23

1 Se crea función para generar una clave aleoatoria para el forgot en el front  
2 Se modifica API REST Dinámica para simplificar el forgot password por parte del front  
3 Se corrige generar pdf ticket en notas de crédito y débito  
4 Se hace dinámico la función para enviar correo, tomando los datos de la configuración como el nombre del sistema, nombre de la empresa y web  
5 Se elimina los modelos y controladores, ahora las solicitudes al back se realiza con el API REST dinámico  

## CAMBIOS AL 22/03/23

1 Se crea un controlador dinámico para la carga y eliminación de archivos al servidor  
2 Se autogenera el avatar del usuario y se almacena en el servidor para futuras integraciones externas

## CAMBIOS AL 23/03/23

1 Se agrega el campo "descripcion_configuracion" dentro de la tabla "configuraciones" para el manejo del SEO por el front  
2 Se carga las imagenes y las peticiones se realiza directo al servidor  
3 Se agrega los campos del servidor de correo para personalizarlo desde el admin

## CAMBIOS AL 24/03/23

1 Se mejora el servicio para la carga y eliminación de archivos del servidor utilizando el API REST dinámico  
2 Se mejora controlador para envío de correo con los datos de la configuración con el fin de hacerlo dinámico, también se agrega el logo del sistema o empresa (configuraciones) en el cuerpo del correo  
3 Se crea endpoint para la carga de archivos al servidor  
4 Se crea endpoint para eliminar archivos del servidor  
5 Se mejora respuesta al registro de usuario  
6 Se valida el estado de la empresa para realizar las consultas y solicitudes al API  
7 Se valida las consultas con el plan (totales) y se manda un mensaje si excede las consultas (pendiente hacerlo por mes)  
8 Se valida los documentos con el plan (totales) y se manda un mensaje si excede los documentos (pendiente hacerlo por mes)

## CAMBIOS AL 28/03/23

1 Se corrige variables según casuística al generar PDF A4 de boletas o facturas  
2 Se corrige variables según casuística al generar PDF Ticket de boletas o facturas  
3 Se mejora en un json (base de datos) la cantidad de consultas y documentos por periodo  
4 Al realizar las consultas o documentos primero se valida que corresponda al periodo, si el periodo actual no existe lo actualiza en el json de la bas de datos  
5 Se limita las consultas de acuerdo al plan, iniciando por periodos  
6 Se modifica base de datos, se elimina los campos "consultas_empresa" y "documentos_empresa" de la tabla "empresas"

## CAMBIOS AL 29/03/23

1 Se agrega los campos "paypal_configuracion" y "culqi_configuracion" en la tabla "configuraciones" que trabajan con json para el manejo de compra de planes  
2 Se agrega en el campo "ventas_plan" en la tabla "planes" para obtener una gráfica de los planes más vendidos

## CAMBIOS AL 03/04/23

1 Se valida si la próxima facturación de la empresa es menor a la fecha actual (si ha renovado plan o plan está activo)

## CAMBIOS 26/03/24

1 Se agrega para la generación de PDF en formato A4 y ticket de notas de venta  
2 Se agrega para la generación de PDF en formato A4 y ticket de cotizaciones  
3 Se modifica las rutas para generar cotización y nota de venta  

REEMPLAZAR  
controladores/pdf.controlador.php  
documents/libs/pdf/invoice-a4.php  
documents/libs/pdf/invoice-ticket.php  
rutas/rutas.php

## CAMBIOS 10/04/24

1 Se modifica CORS para acceso a cualquier lenguaje de programación  

REEMPLAZAR  
index.php

## CAMBIOS 14/04/24

1 Se agrega para la generación de PDF en formato A4 y ticket de notas de venta eun una sola URL  
2 Se agrega para la generación de PDF en formato A4 y ticket de cotizaciones en una sola URL  
3 Se modifica las rutas para generar cotización y nota de venta en una sola UR  

REEMPLAZAR  
rutas/rutas.php

## CAMBIOS 14/04/24

1 Se corrige la carga de archivos si la empresa no tiene fecha de expiracion  
2 Se agrega la funcion para convertir el total a texto en la generacion de xml, se actualiza json postman  
3 Se agrega la funcion para convertir el total a texto en la generacion de pdf, se actualiza json postman  
4 Se corrige peticion con el token general del administrador si no hay empresas registradas  

## CAMBIOS 26/05/24

1 Se modifica el formato PDF ticket  
2 Se modifica el formato PDF A4  

## CAMBIOS AL 28/05/24

1 Se corrige la creación de URL para los formatos PDF's de boletas, facturas, notas de crédito y notas de débito  

## CAMBIOS AL 09/06/24

1 Se agrega configuración para conectar con Supabase  

## CAMBIO AL 12/06/24

1 Se crea una nueva ruta para el pago de la suscripcion por empresa  

## CAMBIOS AL 13/06/24

1 Se crea una url /email/send para el consumo de correos utilizando el servidor configurado desde el API

## CAMBIOS AL 20/06/24

1 Corrección de errores en la carga del adjunto al pagar la suscripcion  
2 Corrección de errores para el envío de correo  

## CAMBIOS AL 23/06/24

1 Se corrige el tamaño del logo cargado en los formatos tickets pdf  
2 Se corrige el nombre del archivo adjunto para las suscripciones, tomando un nombre unico  
3 Corrección de la altura en el formato ticket  
4 Se toma una conexion de contingencia si falla la libreria para consultar DNI  

## CAMBIOS AL 30/06/24

1 Se añade el campo "facturacion_configuracion" dentro de la tabla configuraciones con valores iniciales por defecto, esto para la configuración en el envío de facturas al registrar o pagar una suscripción    
Valor por defecto = [{"estado":"activo","factura":{"serie":"F001","correlativo":"0"},"empresa":{"ruc":"11111111111","razonSocial":"EMPRESA DEMOSTRACION SAC","nombreComercial":"DEMO","departamento":"LIMA","provincia":"LIMA","distrito":"LOS OLIVOS","ubigeo":"150117","direccion":"AVENIDA DEMOSTRACION 132","telefono":"999999999","email":"demo@gmail.com"},"sunat":{"modo":"beta","usuarioSol":"MODDATOS","claveSol":"moddatos","claveCertificado":"","expiraCertificado":""}}]    
2 Se libera la ruta "invoice/create" y la ruta "invoice/send", para poder crear la factura con los datos registrados en las configuraciones con sus respectivos archivos PDF  
3 Se modifica la ruta "email/send" para que soporte uno o más archivos adjuntos  

## CAMBIOS AL 06/09/24

1 Se corrige el cambio de fase de producción a beta   
2 Correción carga de certificado

## CAMBIOS AL 06/11/24

1 Se crea el endpoint para que el cron de las suscripciones se ejecute directamente con el API  
2 Se crea el endpoint con el proceso completo para realizar la carga con el pago de una suscripcion  

## CAMBIOS AL 07/11/24

1 Se crea el endpoint para aprobar o rechazar el pago de una suscripción, todo el proceso lo realiza de manera automática  