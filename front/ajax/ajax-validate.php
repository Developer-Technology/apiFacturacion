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

class ValidateController
{

    public $data;
    public $table;
    public $suffix;

    public function dataRepeat()
    {

        $url = $this->table . "?select=" . $this->suffix . "&linkTo=" . $this->suffix . "&equalTo=" . urlencode($this->data);

        $method = "GET";
        $fields = array();
        $token = TemplateController::tokenSet();

        $response = CurlController::requestSunat($url, $method, $fields, $token);

        echo $response->response->status;

    }

}

if (isset($_POST["data"])) {

    $validate = new ValidateController();
    $validate->data = $_POST["data"];
    $validate->table = $_POST["table"];
    $validate->suffix = $_POST["suffix"];
    $validate->dataRepeat();

}