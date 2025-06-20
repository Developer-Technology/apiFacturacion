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
require_once "../controllers/summaries.controller.php";

/*=============================================
Ejecutamos la clase
=============================================*/
$idSum = $_GET["idSum"];

$sendSunat = new SummariesController();
$sendSunat->sendSunat($idSum);