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
require_once "../controllers/curl.controller.php";
require_once "../controllers/template.controller.php";

class AjaxPost
{

    public $table;
    public $dataPost;

    public function dataSend()
    {

        $url = $this->table . "?token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";

        $method = "POST";
        $fields = $this->dataPost;

        $token = TemplateController::tokenSet();

        $response = CurlController::requestSunat($url, $method, $fields, $token);

        echo json_encode($response->response->status);

    }

}

if (isset($_POST["table"])) {

    $validate = new AjaxPost();
    $validate->table = $_POST["table"];
    $validate->dataPost = $_POST["dataPost"];
    $validate->dataSend();

}