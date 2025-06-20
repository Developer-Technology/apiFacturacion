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

class AjaxPut
{

    public $data;
    public $table;
    public $suffix;
    public $dataUp;

    public function dataPut()
    {

        $url = $this->table . "?id=" . $this->data . "&nameId=" . $this->suffix . "&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";

        $method = "PUT";
        $fields = $this->dataUp;
        $token = TemplateController::tokenSet();

        $response = CurlController::requestSunat($url, $method, $fields, $token);

        echo json_encode($response->response);

    }

}

if (isset($_POST["data"])) {

    $validate = new AjaxPut();
    $validate->data = $_POST["data"];
    $validate->table = $_POST["table"];
    $validate->suffix = $_POST["suffix"];
    $validate->dataUp = $_POST["dataUp"];
    $validate->dataPut();

}