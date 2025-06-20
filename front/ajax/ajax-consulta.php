<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Iniciamos la sesion
=============================================*/
session_start();

/*=============================================
Requerimos los controladores
=============================================*/
require_once "../controllers/template.controller.php";
require_once "../controllers/curl.controller.php";

/*=============================================
Obtenemos los datos de la empresa
=============================================*/
require_once "../controllers/tenants.controller.php";

/*=============================================
Obtenemos el token de la empresa
=============================================*/
if($_SESSION["empresa"] != '') {

    $value = $_SESSION["empresa"]->id_empresa;

    $dataTenants = TenantsController::dataTenant($value);

    $token = $dataTenants->token_empresa;

    $data = array(
        "claveSecreta" => $dataTenants->clave_secreta_empresa
    );

    $fields = json_encode($data);

    /* Consulta RUC/DNI */
    $urlCon = "consult/" . $_POST['type'] . '/' . $_POST['doc'];

    /* Consulta TC */
    $urlExch = "consult/exchange";

    /* Consulta CPE */
    $url = "consult";

} else {

    /*=============================================
    Si no hay sesion de la empresa se toma el token admin
    =============================================*/
    $token = TemplateController::tokenSet();

    $data = array();

    $fields = $data;

    /* Consulta RUC/DNI */
    $urlCon = $_POST['type'] . '/' . $_POST['doc'];

    /* Consulta TC */
    $urlExch = "exchange/consult";

    /* Consulta CPE */
    $url = "cpe/consult";

}

/*=============================================
Consulta CPE
=============================================*/
if($_POST['type'] == 'cpe') {

    $url = $urlCpe;
    $data = array(
        "comprobante" => array(
            "rucEmisor" => $_POST["rucEmisor"],
            "codComp" => $_POST["typeComp"],
            "serie" => $_POST["serieComp"],
            "numero" => $_POST["numberComp"],
            "fechaEmision" => date('d/m/Y', strtotime($_POST["fechaEmision"])),
            "monto" => $_POST["montoComp"]
        ),
        "claveSecreta" => $dataTenants->clave_secreta_empresa
    );

    $fields = json_encode($data);
    
} else if($_POST['type'] == 'tc') {

    /*=============================================
    Consulta Tipo de cambio
    =============================================*/
    $url = $urlExch;

} else {

    /*=============================================
    Consulta RUC / DNI
    =============================================*/
    $url = $urlCon;

}

/*=============================================
Establecemos el metodo
=============================================*/
$method = "POST";

/*=============================================
Ejecutamos la funcion
=============================================*/
$objConsult = CurlController::requestSunat($url, $method, $fields, $token);

/*=============================================
Mostramos los resultados
=============================================*/
echo json_encode($objConsult);