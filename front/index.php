<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Mostrar errores
=============================================*/
ini_set('display_errors', 1);
ini_set("log_errors", 1);
ini_set("error_log", "D:/xampp7/htdocs/scriptApi/php_error_log");

/*=============================================
CORS
=============================================*/
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST');

/*=============================================
Controladores
=============================================*/
require_once "controllers/curl.controller.php";
require_once "controllers/template.controller.php";

$index = new TemplateController();
$index->index();