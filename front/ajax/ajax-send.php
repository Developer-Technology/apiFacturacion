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
require_once "../controllers/sales.controller.php";

/*=============================================
Ejecutamos la clase
=============================================*/
//$idSale = $_GET["typeSa"];

$data = array(
    "typeSale" => $_GET["typeSale"],
    "serieSale" => $_GET["serieSale"],
    "numberSale" => $_GET["numberSale"] 
);

$sendSunat = new SalesController();
$sendSunat->sendSunat($data);