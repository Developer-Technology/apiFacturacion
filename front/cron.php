<?php 

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Requerimos los controladores
=============================================*/
require_once "controllers/curl.controller.php";
require_once "controllers/template.controller.php";

$token = TemplateController::tokenSet();

/*=============================================
Peticion al API
=============================================*/
$url = "cron/suscriptions";
$method = "POST";
$fields = array();
$response = CurlController::requestSunat($url, $method, $fields, $token);

/*=============================================
Imprimir la respuesta de manera ordenada
=============================================*/
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);