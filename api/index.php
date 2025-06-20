<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

/*=============================================
CORS
=============================================*/
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
    header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization');
    header('Allow: GET, POST, OPTIONS, PUT, DELETE');
    exit;
}

header('content-type: application/json; charset=utf-8');

/*=============================================
Mostrar errores
=============================================*/
ini_set('display_errors', 1);
ini_set("log_errors", 1);
ini_set("error_log",  "D:/xampp7/htdocs/api_sunat");

/*=============================================
Requerimos las librerias
=============================================*/
require 'vendor/autoload.php';
require "documents/libs/phpqrcode/qrlib.php";

/*=============================================
Requerimos la conexion a la base de datos
=============================================*/
require_once "modelos/conexion.php";

/*=============================================
Requerimos los controladores
=============================================*/
/* API REST Dinamico */
require_once "controladores/servicios/delete.controlador.php";
require_once "controladores/servicios/files.controlador.php";
require_once "controladores/servicios/get.controlador.php";
require_once "controladores/servicios/post.controlador.php";
require_once "controladores/servicios/put.controlador.php";
/* Peticiones SUNAT */
require_once "controladores/curl.controlador.php";
require_once "controladores/rutas.controlador.php";
require_once "controladores/xml.controlador.php";
require_once "controladores/sunat.controlador.php";
require_once "controladores/signature.controlador.php";
require_once "controladores/pdf.controlador.php";
require_once "controladores/padron.controlador.php";

/*=============================================
Requerimos los modelos
=============================================*/
require_once "modelos/servicios/put.modelo.php";
require_once "modelos/servicios/get.modelo.php";
require_once "modelos/servicios/post.modelo.php";
require_once "modelos/servicios/delete.modelo.php";

/*=============================================
Ejecutamos la ruta
=============================================*/
$rutas = new ControladorRutas();
$rutas -> index();