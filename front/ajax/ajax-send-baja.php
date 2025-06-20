<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Incluimos los controladores
=============================================*/
require_once "../controllers/template.controller.php";
require_once "../controllers/curl.controller.php";
require_once "../controllers/voideds.controller.php";

/*=============================================
Ejecutamos la clase
=============================================*/
$idVoided = $_GET["idVoided"];

$sendSunat = new VoidedsController();
$sendSunat->sendSunat($idVoided);