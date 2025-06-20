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
require_once "../controllers/despatches.controller.php";

/*=============================================
Ejecutamos la clase
=============================================*/
$idDesp = $_GET["idDesp"];

$sendSunat = new DespatchesController();
$sendSunat->sendSunatTrans($idDesp);