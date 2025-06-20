<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Requerimos los controladores
=============================================*/
require_once "../controllers/curl.controller.php";
require_once "../controllers/template.controller.php";

class StateController
{

    public $state;
    public $id;
    public $table;
    public $suffix;
    public $token;

    public function dataState()
    {

        $url = $this->table . "?id=" . $this->id . "&nameId=id_" . $this->suffix . "&token=" . $this->token . "&table=usuarios&suffix=usuario";
        $method = "PUT";
        $fields = "estado_" . $this->suffix . "=" . $this->state;
        $token = TemplateController::tokenSet();

        $response = CurlController::requestSunat($url, $method, $fields, $token)->response->status;

        echo json_encode($response);

    }

}

if (isset($_POST["state"])) {

    $state = new StateController();
    $state->state = $_POST["state"];
    $state->id = $_POST["id"];
    $state->table = $_POST["table"];
    $state->suffix = $_POST["suffix"];
    $state->token = $_POST["token"];
    $state->dataState();

}