<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Controladores
=============================================*/
require_once "controllers/curl.controller.php";

/*=============================================
Desencriptamos la url
=============================================*/
$_SESSION['empresa'] = "";
$_SESSION['admin'] = "";

$security = explode("~", base64_decode($routesArray[2]));

$value = $security[0];

if ($value == 0) {

    $_SESSION['admin'] = 1;

} else {

    /*=============================================
    Tomamos los datos de la empresa
    =============================================*/
    $url = "empresas?linkTo=id_empresa&equalTo=" . $value . "&select=*";
    $method = "GET";
    $fields = array();
    $token = TemplateController::tokenSet();

    $tenants = CurlController::requestSunat($url, $method, $fields, $token);

    /*=============================================
    Creamos la sesion de la empresa
    =============================================*/
    $_SESSION['empresa'] = $tenants->response->data[0];

}

/*=============================================
Redireccionamos al panel
=============================================*/
echo '<script>
        fncFormatInputs();
        window.location = "/"
    </script>';
